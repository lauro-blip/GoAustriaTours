<h2>New enquiry #{{ $enquiry->id }}</h2>
<p><strong>{{ $enquiry->full_name }}</strong><br>
Email: {{ $enquiry->email }}<br>
Phone: {{ $enquiry->phone_e164 }}</p>
<p>Form: {{ $enquiry->form_name }}@if($enquiry->package_name) &mdash; Package: {{ $enquiry->package_name }}@endif<br>
Travel date: {{ $enquiry->travel_date ?: 'n/a' }}<br>
Second quote: {{ $enquiry->second_quote ?: 'n/a' }}</p>
@if($enquiry->special_request)<p>Special request:<br>{{ $enquiry->special_request }}</p>@endif
@if($enquiry->quiz_answers)
<h3>Answers</h3>
<ul>@foreach($enquiry->quiz_answers as $k => $v)<li>{{ $k }}: {{ $v }}</li>@endforeach</ul>
@endif
<p>Source: {{ $enquiry->tracking_last['utm_source'] ?? ($enquiry->tracking_first['utm_source'] ?? 'direct') }} / {{ $enquiry->tracking_last['utm_campaign'] ?? '-' }}</p>
<p>Open in admin: {{ url('/admin/enquiries/'.$enquiry->id) }}</p>
