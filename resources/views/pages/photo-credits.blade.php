@php
    $bTitle = 'Photo credits';
    $bEyebrow = 'Thank you';
    $bImage = asset('images/winter/hero-salzburg-1280.webp');
    $bCrumbs = [['Photo credits', null]];
    $by4 = ['CC BY 4.0', 'https://creativecommons.org/licenses/by/4.0/'];
    $bysa4 = ['CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0/'];
    $cc0 = ['CC0 (public domain)', 'https://creativecommons.org/publicdomain/zero/1.0/'];
    $by2 = ['CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0/'];
    $bysa2 = ['CC BY-SA 2.0', 'https://creativecommons.org/licenses/by-sa/2.0/'];
    $bysa3 = ['CC BY-SA 3.0', 'https://creativecommons.org/licenses/by-sa/3.0/'];
    $bysa3de = ['CC BY-SA 3.0 DE', 'https://creativecommons.org/licenses/by-sa/3.0/de/'];
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
        ['Hallstatt in winter', 'Thomas Fabian', $bysa2, 'https://commons.wikimedia.org/wiki/File:Hallstatt_winter.jpg'],
        ['Schönbrunn Palace, winter 2023', 'Michellethewise', $cc0, 'https://commons.wikimedia.org/wiki/File:01_Schloss_Sch%C3%B6nbrunn,_Winter_2023.jpg'],
        ['First snow in Salzburg', 'Wald1siedel', $bysa3, 'https://commons.wikimedia.org/wiki/File:First_snow_in_Salzburg.JPG'],
        ['Zell am See in winter', 'Wald1siedel', $bysa4, 'https://commons.wikimedia.org/wiki/File:Zell_am_See_im_Winter_01.JPG'],
        ['Winter snow in Kitzbühel', 'Valerii Tkachenko', $by2, 'https://commons.wikimedia.org/wiki/File:Winter_snow_in_Kitzb%C3%BChel.jpg'],
        ['Tauplitz with Grimming', 'Bernd Thaller', $by2, 'https://commons.wikimedia.org/wiki/File:Tauplitz_with_Grimming_(24025523011).jpg'],
        ['Christmas market, Salzburg', 'Salzburger Nockerl', $bysa4, 'https://commons.wikimedia.org/wiki/File:Christmas_Market_Salzburg_Austria_.jpg'],
        ['Untersberg in winter', 'Renek78', $cc0, 'https://commons.wikimedia.org/wiki/File:Untersberg_in_winter_of_2023-25.jpg'],
        ['Zeller See and Steinernes Meer', 'Wald1siedel', $bysa3, 'https://commons.wikimedia.org/wiki/File:Zeller_See_Zell_am_See_Steinernes_Meer.JPG'],
        ['Wagrain in winter', 'Arne Müseler', $bysa3de, 'https://commons.wikimedia.org/wiki/File:Wagrain_salzburg.jpg'],
        ['Panorama over Kapitelplatz, Salzburg', 'Strubbl', $bysa4, 'https://commons.wikimedia.org/wiki/File:Panorama_%C3%BCber_Kapitelplatz_in_Salzburg_2022-02-05.jpg'],
        ['Beautiful place in Montafon', 'Yesiamsebi', $by2, 'https://commons.wikimedia.org/wiki/File:Beautiful_place_in_montafon_(25265594591).jpg'],
    ];
@endphp
@extends('layouts.site', ['metaTitle' => 'Photo credits | '.($site['brand_name'] ?? ''), 'metaDescription' => 'Credits and licences for the photographs used on this website.'])

@section('content')
@include('pages.partials.banner', ['bClass' => 'slim'])
<section class="sec">
    <div class="wrap narrow">
        <p class="prose-lead">Many of the photographs on this site are shared on Wikimedia Commons by their photographers. We have resized, cropped and compressed them for the web. Thank you to everyone listed below. The three images in the "Your Journey. Your Way." boxes on the home page are AI-generated illustrations, not photographs.</p>
        <div class="tablewrap">
            <table class="credits">
                <caption class="sr-only">Photographs, photographers and licences</caption>
                <thead><tr><th scope="col">Photograph</th><th scope="col">Photographer</th><th scope="col">Licence</th></tr></thead>
                <tbody>
                    @foreach($credits as [$title, $author, $licence, $url])
                        <tr>
                            <td><a href="{{ $url }}" rel="noopener" target="_blank">{{ $title }}</a></td>
                            <td>{{ $author }}</td>
                            <td><a href="{{ $licence[1] }}" rel="noopener" target="_blank">{{ $licence[0] }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
