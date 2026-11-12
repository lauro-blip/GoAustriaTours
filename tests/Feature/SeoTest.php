<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_sitemap_lists_public_pages_and_published_packages_only(): void
    {
        Package::where('slug', 'tyrol-private-escorted')->update(['is_published' => false]);

        $res = $this->get('/sitemap.xml')->assertOk();
        $xml = $res->getContent();

        $this->assertStringContainsString('application/xml', $res->headers->get('Content-Type'));
        $this->assertStringContainsString('<urlset', $xml);
        $this->assertStringContainsString(route('home'), $xml);
        $this->assertStringContainsString(route('packages.index'), $xml);
        $this->assertStringContainsString('/packages/imperial-austria-self-drive', $xml);
        $this->assertStringContainsString('/tours/self-drive', $xml);
        $this->assertStringNotContainsString('tyrol-private-escorted', $xml);
        $this->assertStringNotContainsString('thank-you', $xml);
        $this->assertStringNotContainsString('/admin', $xml);
        simplexml_load_string($xml) ?: $this->fail('Sitemap is not valid XML');
    }

    public function test_robots_blocks_admin_and_thank_you(): void
    {
        $body = $this->get('/robots.txt')->assertOk()->getContent();
        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Disallow: /thank-you', $body);
        $this->assertStringContainsString('Sitemap:', $body);
    }

    public function test_pages_have_one_h1_title_description_and_canonical(): void
    {
        foreach (['/', '/packages', '/packages/imperial-austria-self-drive', '/tours/coach', '/about', '/contact', '/terms-and-conditions', '/privacy-policy'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertSame(1, preg_match_all('/<h1[\s>]/', $html), "$url should have exactly one h1");
            $this->assertMatchesRegularExpression('/<title>[^<]+<\/title>/', $html, $url);
            $this->assertStringContainsString('<meta name="description"', $html, $url);
            $this->assertStringContainsString('<link rel="canonical"', $html, $url);
        }
    }

    public function test_thank_you_page_is_noindex_and_404_is_custom(): void
    {
        $this->get('/thank-you')->assertOk()->assertSee('noindex', false);
        $this->get('/no-such-page')->assertNotFound()->assertSee("We can't find that page", false);
    }

    public function test_home_has_faq_structured_data(): void
    {
        $this->get('/')->assertSee('"@type":"FAQPage"', false)->assertSee('"@type":"TravelAgency"', false);
    }

    public function test_site_settings_reach_page_views_not_just_the_layout(): void
    {
        \App\Models\Setting::put('phone', '+1 800 555 0199');
        \App\Models\Setting::put('legal_company_name', 'Acme Alpine Ltd');

        $this->get('/contact')->assertSee('+1 800 555 0199', false);
        $this->get('/')->assertSee('Call +1 800 555 0199', false);
        // The consent text uses the legal company name from settings.
        $this->get('/contact')->assertSee('provided by Acme Alpine Ltd.', false);
    }

    public function test_unpublished_package_is_404(): void
    {
        Package::where('slug', 'tyrol-private-escorted')->update(['is_published' => false]);
        $this->get('/packages/tyrol-private-escorted')->assertNotFound();
    }
}
