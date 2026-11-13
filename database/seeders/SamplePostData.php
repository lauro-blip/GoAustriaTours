<?php

namespace Database\Seeders;

/**
 * Starter journal articles: general travel guidance only, no prices, guarantees or company claims.
 * DRAFTS: review/edit in Admin > Journal before launch (see docs/reference/CONTENT_TO_CONFIRM.md).
 */
class SamplePostData
{
    /** @return array<int,array<string,string>> newest first */
    public static function all(): array
    {
        return [
            [
                'slug' => 'self-drive-or-coach-how-to-choose', 'category' => 'Planning',
                'title' => 'Self Drive or Coach? How to Choose Your Austria Tour',
                'excerpt' => 'Three ways to see Austria, each with a different pace. Here is how to decide which one suits you.',
                'image_alt' => 'The Inn river and old town of Innsbruck below the Alps',
                'days_ago' => 3,
                'body' => '<p>Austria is compact, well connected and easy to explore, which is why so many travellers ask whether they should drive themselves, join a guided group or travel by coach. The honest answer is that it depends on how you like to travel.</p>'
                    .'<h2>Self drive: freedom and flexibility</h2><p>Driving gives you the most freedom. You can stop for a lake that catches your eye, linger over lunch in a village or take a scenic detour. It suits confident drivers who enjoy mountain roads and like to set their own pace. Remember that parking in historic centres can be limited, so the best itineraries mix driving days with car-free city stays.</p>'
                    .'<h2>Escorted: a guide for the details</h2><p>With a private or small-group guide, the logistics and the stories are taken care of. It is a good fit if you want the history behind the sights, would rather not navigate, or are travelling as a family or a multigenerational group.</p>'
                    .'<h2>Coach: effortless and sociable</h2><p>Coach tours cover a lot of ground without any driving, so you can watch the scenery rather than the road. They work well for first visits, for travellers who prefer a set plan, and for anyone who enjoys meeting others along the way.</p>'
                    .'<h2>Not sure? Start with your priorities</h2><p>Ask yourself three things: how much do you want to decide day to day, how comfortable are you on Alpine roads, and how much do you want explained as you go? Your answers usually point clearly to one style, and a local expert can help you fine-tune it.</p>',
            ],
            [
                'slug' => 'when-to-visit-austria-season-by-season', 'category' => 'Planning',
                'title' => 'When to Visit Austria: A Season-by-Season Guide',
                'excerpt' => 'Alpine meadows in summer, golden valleys in autumn, Christmas markets in winter. Find your best time to go.',
                'image_alt' => 'The lakeside village of Hallstatt beneath forested mountains',
                'days_ago' => 10,
                'body' => '<p>Austria changes character with the seasons, and there is rarely a bad time to go. What matters is what you want to see and do.</p>'
                    .'<h2>Spring: quieter and greener</h2><p>Valleys turn green, cities come alive in cafe gardens and crowds are lighter. Some high mountain roads and cable cars open later in the season, so check dates if the Alps are a priority.</p>'
                    .'<h2>Summer: lakes, hiking and long days</h2><p>This is the classic season for the Salzkammergut lakes, Alpine walking and scenic drives. Popular villages such as Hallstatt are busiest in the middle of the day, so early mornings and evenings reward you with calm.</p>'
                    .'<h2>Autumn: colour and harvest</h2><p>Forests turn gold, the Danube vineyards in the Wachau are harvested, and the light is superb for photography. It is a favourite for travellers who like mild days and fewer crowds.</p>'
                    .'<h2>Winter: markets and mountains</h2><p>Advent brings Christmas markets to Vienna, Salzburg and Innsbruck, and the Alps are ready for snow sports. Some mountain passes close for the season, which makes winter better suited to city and village stays.</p>'
                    .'<p>Tell us the month you are considering and we will suggest how to get the most from it.</p>',
            ],
            [
                'slug' => 'three-days-in-vienna-first-timers-guide', 'category' => 'Destinations',
                'title' => 'Three Days in Vienna: A First-Timer\'s Guide',
                'excerpt' => 'Palaces, coffee houses and a Ring boulevard made for strolling: a simple plan for your first visit.',
                'image_alt' => 'Schönbrunn Palace seen across its formal gardens',
                'days_ago' => 21,
                'body' => '<p>Vienna rewards unhurried exploring. Three days is enough to see the highlights without rushing, and the compact centre is easy to cover on foot and by tram.</p>'
                    .'<h2>Day one: the imperial centre</h2><p>Start with a walk along the Ringstrasse, the grand boulevard that circles the old town, taking in the opera house, the Hofburg palace and the parks. Finish the afternoon in a traditional coffee house, which is as much a part of Viennese life as any monument.</p>'
                    .'<h2>Day two: Schönbrunn and the gardens</h2><p>Spend the morning at Schönbrunn Palace, the former summer residence of the Habsburgs, and wander the formal gardens. Visit early in the day to avoid the busiest hours. In the evening, consider a concert or an opera performance.</p>'
                    .'<h2>Day three: art, markets and a slower pace</h2><p>Choose a museum that matches your interests, then browse the Naschmarkt for lunch. The Belvedere palace and its gardens make a graceful afternoon before dinner in a traditional wine tavern.</p>'
                    .'<p>Vienna pairs naturally with Salzburg and the Danube valley, which is why many itineraries combine them.</p>',
            ],
            [
                'slug' => 'driving-the-grossglockner-high-alpine-road', 'category' => 'Scenic routes',
                'title' => 'Driving the Grossglockner High Alpine Road',
                'excerpt' => 'Austria\'s most famous mountain road: what to expect, and how to plan the drive.',
                'image_alt' => 'Hairpin bends on the Grossglockner High Alpine Road above the clouds',
                'days_ago' => 35,
                'body' => '<p>The Grossglockner High Alpine Road climbs through the Hohe Tauern National Park, passing meadows, waterfalls and glacier views along a series of hairpin bends. It is one of the great scenic drives of the Alps.</p>'
                    .'<h2>When it is open</h2><p>The road is a seasonal route and is usually open from late spring to autumn, depending on snow. Always check the current opening dates and conditions before you plan around it.</p>'
                    .'<h2>Give it time</h2><p>Although the route can be driven in a couple of hours, most travellers stay far longer. Viewpoints, short walks and cafes along the way are part of the experience, so allow a relaxed half day or more.</p>'
                    .'<h2>Tips for a smooth drive</h2><ul><li>Start early for clearer views and lighter traffic.</li><li>Weather can change quickly at altitude, so bring layers.</li><li>Use lower gears on long descents to spare your brakes.</li><li>Fuel up beforehand.</li></ul>'
                    .'<p>It is a highlight of our Alpine self drive itineraries, and a coach version lets you enjoy the views without driving.</p>',
            ],
        ];
    }
}
