<?php

namespace App\Console\Commands;

use App\Jobs\SyncEnquiryToSheet;
use App\Models\Enquiry;
use Illuminate\Console\Command;

class SheetsSync extends Command
{
    protected $signature = 'sheets:sync {--all : Send every enquiry again, not only those not yet in the sheet}';

    protected $description = 'Send enquiries that are not in the Google Sheet yet (safe to re-run: rows already in a tab are skipped by the sheet script)';

    public function handle(): int
    {
        if (! SyncEnquiryToSheet::enabled()) {
            $this->error('The Google Sheet web app URL and secret must be saved in Admin > Google Sheet first.');

            return self::FAILURE;
        }

        $query = Enquiry::orderBy('id');
        if (! $this->option('all')) {
            $query->where('sheet_status', '!=', 'synced');
        }

        $sent = 0;
        foreach ($query->cursor() as $enquiry) {
            SyncEnquiryToSheet::dispatchSync($enquiry->id);
            $enquiry->refresh();
            $this->line("#{$enquiry->id} {$enquiry->form_name}: {$enquiry->sheet_status}".($enquiry->sheet_error ? " ({$enquiry->sheet_error})" : ''));
            $sent++;
        }
        $this->info("Processed {$sent} enquiries.");

        return self::SUCCESS;
    }
}
