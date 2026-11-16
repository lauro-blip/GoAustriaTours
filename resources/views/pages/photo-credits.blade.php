@extends('layouts.app', ['metaTitle' => 'Photo credits | '.($site['brand_name'] ?? ''), 'metaDescription' => 'Credits and licences for the photographs used on this website.'])

@php
    $by4 = ['CC BY 4.0', 'https://creativecommons.org/licenses/by/4.0/'];
    $bysa4 = ['CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0/'];
    $cc0 = ['CC0 (public domain)', 'https://creativecommons.org/publicdomain/zero/1.0/'];
    $credits = [
        ['Hallstatt waterfront and churches', 'David Kernan', $by4, 'https://commons.wikimedia.org/wiki/File:View_of_Hallstatt_waterfront_and_churches_from_Hallst%C3%A4tter_See.jpg'],
        ['Salzburg old town panorama', 'Uoaei1', $bysa4, 'https://commons.wikimedia.org/wiki/File:Salzburg_Altstadt_Panorama_20170409_02.jpg'],
        ['Schönbrunn Palace from Neptune Fountain', 'Martin Falbisoner', $bysa4, 'https://commons.wikimedia.org/wiki/File:Schoenbrunn_Palace_as_seen_from_Neptune_Fountain,_September_2016.jpg'],
        ['Melk Abbey, north side', 'Uoaei1', $bysa4, 'https://commons.wikimedia.org/wiki/File:Stift_Melk_Nordseite_01.jpg'],
        ['Innsbruck old town from Universitätsbrücke', 'Luftschiffhafen', $bysa4, 'https://commons.wikimedia.org/wiki/File:Innsbruck_Altstadt_von_Unibruecke.jpg'],
        ['Achensee above the Inn valley', 'Holger Uwe Schmitt', $bysa4, 'https://commons.wikimedia.org/wiki/File:380_m_%C3%BCber_dem_Inntal_liegt_der_Achensee._01.jpg'],
        ['Grossglockner High Alpine Road', 'Dimitry Anikin', $cc0, 'https://commons.wikimedia.org/wiki/File:Gro%C3%9Fglockner-Hochalpenstra%C3%9Fe_1.jpg'],
        ['Grossglockner High Alpine Road (2)', 'Dimitry Anikin', $cc0, 'https://commons.wikimedia.org/wiki/File:Gro%C3%9Fglockner-Hochalpenstra%C3%9Fe_2.jpg'],
        ['Getreidegasse, Salzburg', 'Leonhard Lenz', $cc0, 'https://commons.wikimedia.org/wiki/File:Getreidegasse_Salzburg_2023-09-27_03.jpg'],
    ];
@endphp

@section('content')
<section class="section">
    <div class="container-page max-w-4xl">
        <h1 class="text-5xl">Photo credits</h1>
        <p class="mt-5 max-w-2xl text-muted">Many of the photographs on this site are shared on Wikimedia Commons by their photographers. We have resized, cropped and compressed them for the web. Thank you to everyone listed below.</p>
        <div class="mt-10 overflow-x-auto">
            <table class="w-full min-w-[34rem] text-left text-sm">
                <caption class="sr-only">Photographs, photographers and licences</caption>
                <thead><tr class="border-b border-line text-xs uppercase tracking-wider text-muted"><th scope="col" class="py-3 pr-4 font-semibold">Photograph</th><th scope="col" class="py-3 pr-4 font-semibold">Photographer</th><th scope="col" class="py-3 font-semibold">Licence</th></tr></thead>
                <tbody class="divide-y divide-line">
                    @foreach($credits as [$title, $author, $licence, $url])
                        <tr>
                            <td class="py-3.5 pr-4"><a href="{{ $url }}" class="text-brand underline underline-offset-4" rel="noopener" target="_blank">{{ $title }}</a></td>
                            <td class="py-3.5 pr-4">{{ $author }}</td>
                            <td class="py-3.5"><a href="{{ $licence[1] }}" class="underline underline-offset-4" rel="noopener" target="_blank">{{ $licence[0] }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
