<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Design;
use Illuminate\Http\Request;

/** Admin > Design: colours, fonts, buttons and text style of the public site (App\Support\Design). Preview before saving; reset to the brand. */
class DesignController extends Controller
{
    public function edit()
    {
        // Coming back from a preview: load the previewed values into the form (not saved yet) and stop previewing on the site.
        $preview = session()->pull('design_preview');
        if (is_array($preview)) {
            session()->now('status', 'Your previewed design is loaded below. Save it to publish, or change it further.');
        }

        return view('admin.design', [
            'design' => is_array($preview) ? Design::clean($preview) : Design::current(),
            'defaults' => Design::DEFAULTS,
            'presets' => Design::PRESETS,
        ]);
    }

    public function update(Request $request)
    {
        $d = Design::clean($this->input($request));
        foreach ($d as $k => $v) {
            // Store only what differs from the brand, so later brand updates still reach untouched values.
            Setting::put('design_'.$k, $v === Design::DEFAULTS[$k] ? null : $v);
        }
        session()->forget('design_preview');

        return redirect()->route('admin.design.edit')->with('status', Design::isDefault($d) ? 'Design saved (brand defaults).' : 'Design saved. The site now uses it.');
    }

    /** Shows the unsaved design on the site to this admin only (session), then they come back here to save or discard. */
    public function preview(Request $request)
    {
        session(['design_preview' => Design::clean($this->input($request))]);

        return redirect()->route('home');
    }

    public function reset()
    {
        foreach (array_keys(Design::DEFAULTS) as $k) {
            Setting::put('design_'.$k, null);
        }
        session()->forget('design_preview');

        return redirect()->route('admin.design.edit')->with('status', 'Design reset to the GoAustriaTours brand.');
    }

    private function input(Request $request): array
    {
        $request->validate(collect(Design::COLORS)->mapWithKeys(fn ($v, $k) => [$k => ['required', 'regex:/^#[0-9a-fA-F]{6}$/']])->all(), [
            'regex' => 'Colours must be a hex value like #0f3b59.',
        ]);

        return $request->only(array_keys(Design::DEFAULTS));
    }
}
