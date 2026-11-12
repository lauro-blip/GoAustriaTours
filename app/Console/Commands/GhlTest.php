<?php

namespace App\Console\Commands;

use App\Services\GhlClient;
use App\Services\GhlException;
use App\Support\GhlFields;
use Illuminate\Console\Command;

class GhlTest extends Command
{
    protected $signature = 'ghl:test {--keep : Do not delete the test contact afterwards}';

    protected $description = 'Check the GoHighLevel token and location, and create/update a clearly labelled test contact';

    public function handle(): int
    {
        if (! GhlClient::configured()) {
            $this->error('GHL is OFF: GHL_API_TOKEN and/or GHL_LOCATION_ID are empty in .env.');

            return self::FAILURE;
        }

        $client = new GhlClient;

        try {
            $fields = $client->fieldIds(fresh: true);
            $this->info('Token and location OK. '.count($fields).' custom fields found.');

            $missing = array_values(array_diff(array_keys(GhlFields::all()), array_keys($fields)));
            if ($missing) {
                $this->warn(count($missing).' expected fields are missing. Run: php artisan ghl:setup');
            }

            $result = $client->upsertContact([
                'firstName' => 'ZZ TEST', 'lastName' => 'Website Integration (safe to delete)',
                'email' => 'ghl-test+'.now()->format('YmdHis').'@example.com',
                'source' => 'ghl:test command',
            ]);
            $client->addTags($result['id'], ['ghl-test']);
            $this->info('Test contact '.($result['new'] ? 'created' : 'updated').': '.$result['id']);

            if (! $this->option('keep')) {
                $client->deleteContact($result['id']);
                $this->info('Test contact deleted.');
            }
        } catch (GhlException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
