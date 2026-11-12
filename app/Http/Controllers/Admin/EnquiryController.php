<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncEnquiryToGhl;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnquiryController extends Controller
{
    private function filtered(Request $request)
    {
        return Enquiry::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->q.'%';
                $q->where(fn ($w) => $w->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term)->orWhere('phone_e164', 'like', $term)->orWhere('package_name', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('source'), fn ($q) => $q->where(fn ($w) => $w
                ->where('tracking_first->utm_source', $request->source)->orWhere('tracking_last->utm_source', $request->source)))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest('id');
    }

    public function index(Request $request)
    {
        return view('admin.enquiries.index', ['enquiries' => $this->filtered($request)->paginate(25)->withQueryString()]);
    }

    public function show(Enquiry $enquiry)
    {
        return view('admin.enquiries.show', ['enquiry' => $enquiry]);
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $enquiry->update($request->validate([
            'status' => ['required', Rule::in(Enquiry::STATUSES)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]));

        return back()->with('status', 'Saved.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')->with('status', 'Enquiry deleted.');
    }

    public function retryGhl(Enquiry $enquiry)
    {
        if (! SyncEnquiryToGhl::enabled()) {
            return back()->withErrors(['ghl' => 'GoHighLevel sync is off. Add GHL_API_TOKEN and GHL_LOCATION_ID to .env first.']);
        }
        $enquiry->update(['ghl_status' => 'pending', 'ghl_error' => null]);
        SyncEnquiryToGhl::dispatch($enquiry->id);

        return back()->with('status', 'GoHighLevel sync queued.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filtered($request);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Date', 'Form', 'Package', 'First name', 'Last name', 'Email', 'Phone', 'Travel date', 'Second quote', 'Status', 'Source', 'Medium', 'Campaign', 'GCLID', 'Quiz answers', 'Consent', 'Terms version', 'IP', 'GHL status']);
            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $e) {
                    $t = $e->tracking_last ?? $e->tracking_first ?? [];
                    fputcsv($out, array_map([$this, 'safe'], [
                        $e->id, $e->created_at, $e->form_name, $e->package_name, $e->first_name, $e->last_name, $e->email, $e->phone_e164,
                        $e->travel_date, $e->second_quote, $e->status, $t['utm_source'] ?? '', $t['utm_medium'] ?? '', $t['utm_campaign'] ?? '',
                        $t['gclid'] ?? '', json_encode($e->quiz_answers), $e->consent_given ? 'yes' : 'no', $e->terms_version, $e->ip_address, $e->ghl_status,
                    ]));
                }
            });
            fclose($out);
        }, 'enquiries-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    /** Neutralise spreadsheet formula injection. */
    public function safe(mixed $v): string
    {
        $v = (string) $v;

        return $v !== '' && str_contains('=+-@', $v[0]) ? "'".$v : $v;
    }
}
