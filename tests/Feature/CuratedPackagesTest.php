<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The sample data has 15 published packages: page 1 = 6, page 2 = 6, page 3 = 3. */
class CuratedPackagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_there_are_fifteen_sample_packages_and_only_the_original_six_are_featured(): void
    {
        $this->assertSame(15, Package::where('is_published', true)->count());
        $this->assertSame(6, Package::where('is_featured', true)->count());
    }

    public function test_every_sample_package_has_a_card_image_file(): void
    {
        foreach (Package::pluck('slug') as $slug) {
            $this->assertFileExists(public_path("images/packages/{$slug}.webp"), $slug);
        }
    }

    public function test_new_package_pages_render(): void
    {
        foreach (['musical-austria-private-escorted', 'danube-valley-by-coach', 'tyrol-and-vorarlberg-by-coach'] as $slug) {
            $this->get("/packages/{$slug}")->assertOk()->assertSee('Days 1-');
        }
    }
}
