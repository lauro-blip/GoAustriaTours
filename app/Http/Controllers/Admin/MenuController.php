<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Menus;
use Illuminate\Http\Request;

/** Admin > Menus: header menu + button, footer columns, footer texts and links (App\Support\Menus). */
class MenuController extends Controller
{
    public function edit()
    {
        return view('admin.menus', [
            'header' => json_decode((string) old('header_json', json_encode(Menus::header())), true) ?: Menus::header(),
            'footer' => json_decode((string) old('footer_json', json_encode(Menus::footer())), true) ?: Menus::footer(),
            'choices' => Menus::linkChoices(),
            'headerLabels' => Menus::HEADER_ITEMS,
            'footerLabels' => Menus::FOOTER_COLUMNS,
            'mega' => Menus::MEGA,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate(['header_json' => ['required', 'string', 'max:50000'], 'footer_json' => ['required', 'string', 'max:50000']]);
        $header = Menus::cleanHeader((array) json_decode($request->input('header_json'), true));
        $footer = Menus::cleanFooter((array) json_decode($request->input('footer_json'), true));
        Setting::put('menu_header', json_encode($header));
        Setting::put('menu_footer', json_encode($footer));

        return redirect()->route('admin.menus.edit')->with('status', 'Menus saved.');
    }

    public function reset()
    {
        Setting::put('menu_header', null);
        Setting::put('menu_footer', null);

        return redirect()->route('admin.menus.edit')->with('status', 'Menus and footer reset to the original.');
    }
}
