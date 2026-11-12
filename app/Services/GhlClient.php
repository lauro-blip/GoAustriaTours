<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/** Thin wrapper over the GoHighLevel API v2 (services.leadconnectorhq.com), authenticated with a Private Integration Token. */
class GhlClient
{
    public function __construct(
        private ?string $token = null,
        private ?string $locationId = null,
    ) {
        $this->token ??= config('goaustria.ghl.token');
        $this->locationId ??= config('goaustria.ghl.location_id');
    }

    public static function configured(): bool
    {
        return filled(config('goaustria.ghl.token')) && filled(config('goaustria.ghl.location_id'));
    }

    public function locationId(): string
    {
        return (string) $this->locationId;
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl(config('goaustria.ghl.base_url'))
            ->withToken((string) $this->token)
            ->withHeaders(['Version' => config('goaustria.ghl.version')])
            ->acceptJson()->asJson()->timeout(20);
    }

    /** Run a request; throws a RuntimeException with the token scrubbed from the message. */
    private function send(callable $call): array
    {
        try {
            return $call($this->http())->throw()->json() ?? [];
        } catch (RequestException $e) {
            $status = $e->response->status();
            $body = mb_substr((string) $e->response->body(), 0, 300);
            throw new GhlException($this->scrub("GHL HTTP {$status}: {$body}"), $status, $e);
        } catch (\Throwable $e) {
            throw new GhlException($this->scrub('GHL request failed: '.$e->getMessage()), 0, $e);
        }
    }

    private function scrub(string $text): string
    {
        return $this->token ? str_replace($this->token, '[redacted]', $text) : $text;
    }

    /** @return array<int, array{id:string,name:string,dataType?:string}> */
    public function customFields(): array
    {
        $res = $this->send(fn ($h) => $h->get("/locations/{$this->locationId}/customFields", ['model' => 'contact']));

        return $res['customFields'] ?? [];
    }

    public function createCustomField(string $name, string $dataType): array
    {
        $res = $this->send(fn ($h) => $h->post("/locations/{$this->locationId}/customFields", [
            'name' => $name, 'dataType' => $dataType, 'model' => 'contact',
        ]));
        Cache::forget($this->cacheKey());

        return $res['customField'] ?? $res;
    }

    /** name => id, cached for an hour. */
    public function fieldIds(bool $fresh = false): array
    {
        if ($fresh) {
            Cache::forget($this->cacheKey());
        }

        return Cache::remember($this->cacheKey(), 3600, fn () => collect($this->customFields())->pluck('id', 'name')->all());
    }

    private function cacheKey(): string
    {
        return 'ghl.field_ids.'.md5((string) $this->locationId);
    }

    /** Find an existing contact by email (null if none). */
    public function findByEmail(string $email): ?array
    {
        try {
            $res = $this->send(fn ($h) => $h->get('/contacts/search/duplicate', ['locationId' => $this->locationId, 'email' => $email]));
        } catch (GhlException $e) {
            if (in_array($e->getCode(), [400, 404, 422], true)) {
                return null;
            }
            throw $e;
        }

        return $res['contact'] ?? null;
    }

    /** @return array{id:string,new:bool} */
    public function upsertContact(array $payload): array
    {
        $res = $this->send(fn ($h) => $h->post('/contacts/upsert', ['locationId' => $this->locationId] + $payload));
        $id = $res['contact']['id'] ?? null;
        if (! $id) {
            throw new GhlException('GHL upsert returned no contact id.');
        }

        return ['id' => $id, 'new' => (bool) ($res['new'] ?? false)];
    }

    /** Adds tags without removing any that staff have set in GHL. */
    public function addTags(string $contactId, array $tags): void
    {
        if ($tags) {
            $this->send(fn ($h) => $h->post("/contacts/{$contactId}/tags", ['tags' => array_values($tags)]));
        }
    }

    public function deleteContact(string $contactId): void
    {
        $this->send(fn ($h) => $h->delete("/contacts/{$contactId}"));
    }
}
