<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VacationsMenuTest extends TestCase
{
    use RefreshDatabase;

    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function main(string $url): string
    {
        $html = $this->get($url)->assertOk()->getContent();

        return substr($html, (int) strpos($html, '<main'));
    }

    public function test_mega_menu_has_the_three_sections_on_every_page(): void
    {
        foreach (['/packages', '/journal', '/contact'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('id="mega-vacations"', false)
                ->assertSee('Vacations &amp; Experiences', false)
                ->assertSee('Signature Collections')->assertSee('By Traveler')->assertSee('Featured Packages')
                ->assertSee('The Imperial Collection')->assertSee('Couples &amp; Honeymoons', false)
                ->assertSee('/packages?collection=alpine', false);
        }
    }

    public function test_every_collection_package_exists_so_no_collection_link_is_empty_or_partly_dead(): void
    {
        foreach (config('goaustria.collections') as $slug => $col) {
            foreach ($col['packages'] as $pkg) {
                $this->assertTrue(Package::where('slug', $pkg)->where('is_published', true)->exists(), "$slug: $pkg");
            }
            $main = $this->main('/packages?collection='.$slug);
            $this->assertStringContainsString($col['name'], str_replace('&amp;', '&', $main));
            $this->assertStringContainsString($col['packages'][0], $main);
        }
    }

    public function test_collection_filter_shows_only_its_packages(): void
    {
        $main = $this->main('/packages?collection=lakes');
        $this->assertStringContainsString('Lakes and Mountains by Coach', $main);
        $this->assertStringNotContainsString('Classic Austria by Coach', $main);
        $this->assertStringNotContainsString('Showing', $this->main('/packages?collection=bogus'));
    }

    public function test_featured_packages_column_lists_featured_packages_only(): void
    {
        Package::where('slug', 'danube-valley-by-coach')->update(['is_featured' => true]);   // featured but sort order 14: beyond the first four
        $html = $this->get('/journal')->assertOk()->getContent();
        $menu = substr($html, strpos($html, 'id="mega-vacations"'), strpos($html, 'id="mega-tours"') - strpos($html, 'id="mega-vacations"'));

        $this->assertStringContainsString('Imperial Austria Self Drive', $menu);
        $this->assertStringNotContainsString('Musical Austria Private Escorted', $menu);   // not featured
    }

    public function test_by_traveler_items_open_the_modal_with_who_is_travelling(): void
    {
        $this->get('/journal')->assertOk()->assertSee("traveler: 'couples'", false)->assertSee('name="traveler"', false);
    }

    public function test_traveler_choice_is_saved_with_the_enquiry_and_unknown_values_are_rejected(): void
    {
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);
        $payload = [
            'form_name' => 'header-modal', 'page_url' => 'http://localhost/', 'first_name' => 'Ada', 'last_name' => 'L', 'email' => 'ada@example.com',
            'country' => 'US', 'phone' => '', 'consent' => '1', 'g-recaptcha-response' => 'token', 'special_request' => 'Window seat please',
        ];

        $this->post('/enquiry', $payload + ['traveler' => 'couples'])->assertSessionHasNoErrors();
        $e = Enquiry::latest('id')->first();
        $this->assertStringContainsString('Travelling as: Couples & Honeymoons', $e->special_request);
        $this->assertStringContainsString('Window seat please', $e->special_request);

        $this->post('/enquiry', $payload + ['traveler' => 'hacker'])->assertSessionHasErrors('traveler');
    }
}
