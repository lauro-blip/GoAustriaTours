<?php

namespace App\Http\Controllers\Admin;

/**
 * Admin > Trust & tracking: everything that builds buyer confidence (contact details, the named planner,
 * memberships, review proof) plus the Google Tag Manager ID. Same save logic as Settings; only the fields differ.
 * Values still holding a "[CONFIRM" placeholder, or left blank, are simply not shown on the site.
 */
class TrustController extends SettingController
{
    /** group => [key => [label, type]] */
    public const TRUST_GROUPS = [
        'Reach us (top bar, footer, contact page, trust strip)' => [
            'phone' => ['Phone, as displayed (e.g. +1 800 555 0100)', 'text'],
            'phone_tel' => ['Phone for tap-to-call (digits and +, no spaces, e.g. +18005550100)', 'text'],
            'email' => ['Public email', 'text'],
        ],
        'Who you are (home page trust strip and "Meet your planner" section)' => [
            'established_line' => ['Established line (true facts only, e.g. "Planning Austria journeys since 2012")', 'text'],
            'planner_name' => ['Planner name (the "Meet your planner" section appears only when name and bio are filled in)', 'text'],
            'planner_role' => ['Role / title (e.g. Founder and Austria specialist)', 'text'],
            'planner_bio' => ['Short bio (true facts only; blank line = new paragraph)', 'textarea'],
            'planner_photo' => ['Photo (portrait, 4:5 works best)', 'image'],
        ],
        'Memberships and protection (shown under the hero; leave unused slots blank)' => [
            'trust_mark_1_label' => ['Mark 1: name (e.g. ABTA Member, ATOL Protected, Fully Bonded)', 'text'],
            'trust_mark_1_logo' => ['Mark 1: logo (optional; the name shows if there is no logo)', 'image'],
            'trust_mark_1_url' => ['Mark 1: link to your membership page (optional)', 'text'],
            'trust_mark_2_label' => ['Mark 2: name', 'text'],
            'trust_mark_2_logo' => ['Mark 2: logo (optional)', 'image'],
            'trust_mark_2_url' => ['Mark 2: link (optional)', 'text'],
            'trust_mark_3_label' => ['Mark 3: name', 'text'],
            'trust_mark_3_logo' => ['Mark 3: logo (optional)', 'image'],
            'trust_mark_3_url' => ['Mark 3: link (optional)', 'text'],
            'trust_mark_4_label' => ['Mark 4: name', 'text'],
            'trust_mark_4_logo' => ['Mark 4: logo (optional)', 'image'],
            'trust_mark_4_url' => ['Mark 4: link (optional)', 'text'],
        ],
        'Review proof (shown under the hero; only enter ratings that are real and current)' => [
            'review_summary' => ['Summary (e.g. "Rated 4.9 from 120 Google reviews")', 'text'],
            'review_url' => ['Link to the reviews page (optional)', 'text'],
        ],
        'Response promise (shown by the quiz and on the thank-you page; only promise what you will do)' => [
            'response_promise' => ['Response promise (e.g. "A personal reply within 1 business day")', 'text'],
        ],
        'Tracking' => [
            'gtm_id' => ['Google Tag Manager ID (GTM-XXXXXXX). Leave blank to keep tracking OFF; add it when your Google Ads conversions are ready.', 'text'],
            'clarity_id' => ['Microsoft Clarity project ID (free heatmaps and session recordings; letters/numbers from clarity.microsoft.com). Leave blank for OFF.', 'text'],
            'google_ads_id' => ['Google Ads tag ID (AW-123456789) for conversion tracking WITHOUT Tag Manager. Leave blank if you use GTM, to avoid counting twice.', 'text'],
            'google_ads_conversion_label' => ['Google Ads conversion label (the part after the slash in "AW-123456789/AbC-D_efG"). Fires on the thank-you page.', 'text'],
            'meta_pixel_id' => ['Meta (Facebook/Instagram) Pixel ID, for remarketing and the Lead event on the thank-you page. Leave blank for OFF.', 'text'],
        ],
    ];

    protected function groups(): array
    {
        return self::TRUST_GROUPS;
    }

    protected function page(): array
    {
        return ['title' => 'Trust & tracking', 'heading' => 'Trust & tracking', 'action' => route('admin.trust.update'), 'ghl' => false];
    }
}
