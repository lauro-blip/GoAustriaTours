<?php

namespace App\Http\Controllers\Admin;

use App\Models\Destination;
use App\Models\Package;
use App\Models\PackageImage;
use App\Models\TourType;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PackageController extends CrudController
{
    protected function model(): string { return Package::class; }
    protected function routeName(): string { return 'admin.packages'; }
    protected function title(): string { return 'Packages'; }
    protected function slugSource(): string { return 'title'; }

    protected function columns(): array
    {
        return ['Title' => 'title', 'Nights' => 'nights', 'Published' => 'is_published', 'Featured' => 'is_featured'];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
            ['name' => 'slug', 'label' => 'URL slug (leave blank to generate)', 'type' => 'text'],
            ['name' => 'tour_type_id', 'label' => 'Tour type', 'type' => 'select', 'options' => TourType::orderBy('sort_order')->pluck('name', 'id')->all()],
            ['name' => 'destination_id', 'label' => 'Destination', 'type' => 'select', 'blank' => true, 'options' => Destination::orderBy('sort_order')->pluck('name', 'id')->all()],
            ['name' => 'nights', 'label' => 'Nights', 'type' => 'number'],
            ['name' => 'price_from', 'label' => '"From" price per person (leave blank until confirmed)', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'currency', 'label' => 'Currency (3 letters)', 'type' => 'text'],
            ['name' => 'summary', 'label' => 'Short summary', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Full description', 'type' => 'richtext'],
            ['name' => 'highlights', 'label' => 'Highlights (one per line)', 'type' => 'lines'],
            ['name' => 'itinerary', 'label' => 'Day-by-day itinerary', 'type' => 'repeater', 'sub' => [['name' => 'title', 'label' => 'Day title'], ['name' => 'body', 'label' => 'Details', 'area' => true]]],
            ['name' => 'inclusions', 'label' => 'Included (one per line)', 'type' => 'lines'],
            ['name' => 'exclusions', 'label' => 'Not included (one per line)', 'type' => 'lines'],
            ['name' => 'faqs', 'label' => 'FAQs for this package', 'type' => 'repeater', 'sub' => [['name' => 'question', 'label' => 'Question'], ['name' => 'answer', 'label' => 'Answer', 'area' => true]]],
            ['name' => 'hero_image', 'label' => 'Main image', 'type' => 'image'],
            ['name' => 'hero_image_alt', 'label' => 'Main image alt text (required if an image is set)', 'type' => 'text'],
            ['name' => 'seo_title', 'label' => 'SEO title', 'type' => 'text'],
            ['name' => 'meta_description', 'label' => 'Meta description', 'type' => 'textarea'],
            ['name' => 'social_image', 'label' => 'Social sharing image', 'type' => 'image'],
            ['name' => 'is_published', 'label' => 'Published', 'type' => 'checkbox'],
            ['name' => 'is_featured', 'label' => 'Featured on the home page', 'type' => 'checkbox'],
            ['name' => 'noindex', 'label' => 'Hide from search engines (noindex)', 'type' => 'checkbox'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return [
            'title' => ['required', 'string', 'max:190'], 'slug' => ['nullable', 'string', 'max:190'],
            'tour_type_id' => ['required', 'exists:tour_types,id'], 'destination_id' => ['nullable', 'exists:destinations,id'],
            'nights' => ['required', 'integer', 'min:1', 'max:60'], 'price_from' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'], 'summary' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'], 'highlights' => ['nullable'], 'itinerary' => ['nullable', 'array'],
            'inclusions' => ['nullable'], 'exclusions' => ['nullable'], 'faqs' => ['nullable', 'array'],
            'hero_image' => ['nullable', 'image', 'max:8192'],
            'hero_image_alt' => [$item?->hero_image || request()->hasFile('hero_image') ? 'required' : 'nullable', 'string', 'max:190'],
            'seo_title' => ['nullable', 'string', 'max:190'], 'meta_description' => ['nullable', 'string', 'max:320'],
            'social_image' => ['nullable', 'image', 'max:8192'],
            'is_published' => ['boolean'], 'is_featured' => ['boolean'], 'noindex' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        $data['currency'] = strtoupper($data['currency']);
        if (! $item->exists) {
            $data['sort_order'] = Package::max('sort_order') + 1;
        }

        return $data;
    }

    public function edit(int $id)
    {
        $package = Package::with('images')->findOrFail($id);

        return view('admin.packages.edit', ['item' => $package, 'fields' => $this->fields(), 'routeName' => $this->routeName(), 'title' => $this->title()]);
    }

    public function duplicate(int $id)
    {
        $copy = Package::findOrFail($id)->replicate();
        $copy->title .= ' (copy)';
        $copy->slug = $this->uniqueSlug($copy->slug.'-copy', new Package);
        $copy->is_published = false;
        $copy->sort_order = Package::max('sort_order') + 1;
        $copy->save();

        return redirect()->route('admin.packages.edit', $copy->id)->with('status', 'Duplicated as a draft.');
    }

    public function toggle(int $id, string $what)
    {
        abort_unless(in_array($what, ['is_published', 'is_featured'], true), 404);
        $p = Package::findOrFail($id);
        $p->update([$what => ! $p->{$what}]);

        return back();
    }

    // Gallery
    public function addImage(Request $request, int $id, ImageService $images)
    {
        $package = Package::findOrFail($id);
        $request->validate(['image' => ['required', 'image', 'max:8192'], 'alt' => ['required', 'string', 'max:190']], ['alt.required' => 'Alt text is required for every image.']);
        try {
            $path = $images->store($request->file('image'), 'packages')['path'];
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['image' => $e->getMessage()]);
        }
        $package->images()->create(['path' => $path, 'alt' => $request->alt, 'sort_order' => ($package->images()->max('sort_order') ?? 0) + 1]);

        return back()->with('status', 'Image added.');
    }

    public function updateImage(Request $request, PackageImage $image)
    {
        $image->update($request->validate(['alt' => ['required', 'string', 'max:190']]));

        return back()->with('status', 'Alt text saved.');
    }

    public function moveImage(PackageImage $image, string $dir)
    {
        $ids = PackageImage::where('package_id', $image->package_id)->orderBy('sort_order')->orderBy('id')->pluck('id')->all();
        $i = array_search($image->id, $ids, true);
        $j = $dir === 'up' ? $i - 1 : $i + 1;
        if (isset($ids[$j])) {
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $n => $key) {
                PackageImage::whereKey($key)->update(['sort_order' => $n + 1]);
            }
        }

        return back();
    }

    public function deleteImage(PackageImage $image, ImageService $images)
    {
        $images->delete($image->path);
        $image->delete();

        return back()->with('status', 'Image removed.');
    }
}
