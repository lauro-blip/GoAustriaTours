<?php

namespace App\Console\Commands;

use App\Services\GhlClient;
use App\Services\GhlException;
use App\Support\GhlFields;
use Illuminate\Console\Command;

class GhlSetup extends Command
{
    protected $signature = 'ghl:setup {--dry-run : List what would be created without creating it}';

    protected $description = 'Create any missing GoHighLevel custom fields (safe to re-run; matches by exact name)';

    public function handle(): int
    {
        if (! GhlClient::configured()) {
            $this->error('GHL_API_TOKEN and GHL_LOCATION_ID must be set in .env first.');

            return self::FAILURE;
        }

        $client = new GhlClient;

        try {
            $existing = collect($client->customFields())->pluck('id', 'name');
        } catch (GhlException $e) {
            $this->error($e->getMessage());
            $this->line('Check the token has the custom field scopes (see docs/reference/GHL_INTEGRATION.md).');

            return self::FAILURE;
        }

        $created = [];
        $skipped = 0;
        foreach (GhlFields::all() as $name => $type) {
            if ($existing->has($name)) {
                $skipped++;
                continue;
            }
            if ($this->option('dry-run')) {
                $this->line("Would create: {$name} ({$type})");
                continue;
            }
            try {
                $client->createCustomField($name, $type);
                $created[] = $name;
                $this->info("Created: {$name} ({$type})");
            } catch (GhlException $e) {
                $this->error("Failed: {$name}: ".$e->getMessage());
            }
        }

        $client->fieldIds(fresh: true);
        $this->newLine();
        $this->line('Created '.count($created).', already existed '.$skipped.'.');
        $this->line('Tip: move the new fields into a folder in GHL by hand if you like, but do NOT rename them.');

        return self::SUCCESS;
    }
}
