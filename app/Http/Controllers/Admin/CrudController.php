<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Small config-driven CRUD base for simple admin lists (tour types, destinations, testimonials, FAQs ...).
 * Subclasses describe the model, the form fields and the validation rules; the generic views do the rest.
 *
 * Field types: text, textarea, richtext, number, select, checkbox, image, lines (one item per line -> JSON array)
 */
abstract class CrudController extends Controller
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** Route name prefix, e.g. "admin.tour-types". */
    abstract protected function routeName(): string;

    abstract protected function title(): string;

    /** @return array<int, array<string, mixed>> */
    abstract protected function fields(): array;

    /** @return array<string, string> table columns [label => attribute] */
    abstract protected function columns(): array;

    protected function rules(?Model $item): array
    {
        return [];
    }

    protected function ordered(): bool
    {
        return Schema::hasColumn((new ($this->model()))->getTable(), 'sort_order');
    }

    protected function query()
    {
        $q = ($this->model())::query();

        return $this->ordered() ? $q->orderBy('sort_order')->orderBy('id') : $q->latest('id');
    }

    public function index()
    {
        return view('admin.crud.index', [
            'items' => $this->query()->get(), 'columns' => $this->columns(), 'routeName' => $this->routeName(),
            'title' => $this->title(), 'ordered' => $this->ordered(), 'extraActions' => $this->extraActions(),
        ]);
    }

    protected function extraActions(): array
    {
        return [];
    }

    public function create()
    {
        return view('admin.crud.form', $this->formData(new ($this->model())));
    }

    public function edit(int $id)
    {
        return view('admin.crud.form', $this->formData(($this->model())::findOrFail($id)));
    }

    private function formData(Model $item): array
    {
        return ['item' => $item, 'fields' => $this->fields(), 'routeName' => $this->routeName(), 'title' => $this->title()];
    }

    public function store(Request $request)
    {
        $item = new ($this->model());
        $this->save($request, $item);

        return redirect()->route($this->routeName().'.index')->with('status', 'Saved.');
    }

    public function update(Request $request, int $id)
    {
        $this->save($request, ($this->model())::findOrFail($id));

        return redirect()->route($this->routeName().'.index')->with('status', 'Saved.');
    }

    protected function save(Request $request, Model $item): void
    {
        $data = $request->validate($this->rules($item->exists ? $item : null));
        $images = app(ImageService::class);

        foreach ($this->fields() as $f) {
            $name = $f['name'];
            switch ($f['type']) {
                case 'checkbox':
                    $data[$name] = $request->boolean($name);
                    break;
                case 'lines':
                    $data[$name] = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $request->input($name)))));
                    break;
                case 'repeater':
                    $keys = array_column($f['sub'], 'name');
                    $data[$name] = collect($request->input($name, []))
                        ->map(fn ($row) => collect($keys)->mapWithKeys(fn ($k) => [$k => trim(strip_tags((string) ($row[$k] ?? '')))])->all())
                        ->filter(fn ($row) => implode('', $row) !== '')->values()->all();
                    break;
                case 'image':
                    unset($data[$name]);
                    if ($request->hasFile($name)) {
                        try {
                            $images->delete($item->{$name});
                            $data[$name] = $images->store($request->file($name))['path'];
                        } catch (InvalidArgumentException $e) {
                            back()->withErrors([$name => $e->getMessage()])->throwResponse();
                        }
                    } elseif ($request->boolean($name.'_remove')) {
                        $images->delete($item->{$name});
                        $data[$name] = null;
                    }
                    break;
                case 'richtext':
                    // Trix output is trusted admin content, but strip scripts and event handlers anyway.
                    $data[$name] = $this->cleanHtml($data[$name] ?? null);
                    break;
            }
        }

        if (array_key_exists('slug', $data) || collect($this->fields())->contains('name', 'slug')) {
            $base = Str::slug($data['slug'] ?? '') ?: Str::slug($data[$this->slugSource()] ?? 'item');
            $data['slug'] = $this->uniqueSlug($base, $item);
        }

        $data = $this->beforeSave($data, $request, $item);
        $item->fill($data)->save();
    }

    protected function slugSource(): string
    {
        return 'name';
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        return $data;
    }

    protected function uniqueSlug(string $base, Model $item): string
    {
        $slug = $base;
        $i = 2;
        while (($this->model())::where('slug', $slug)->when($item->exists, fn ($q) => $q->where('id', '!=', $item->id))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    protected function cleanHtml(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }
        $html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);

        return preg_replace('#(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2#i', '$1=$2#$2', $html);
    }

    public function destroy(int $id)
    {
        $item = ($this->model())::findOrFail($id);
        foreach ($this->fields() as $f) {
            if ($f['type'] === 'image') {
                app(ImageService::class)->delete($item->{$f['name']});
            }
        }
        $item->delete();

        return redirect()->route($this->routeName().'.index')->with('status', 'Deleted.');
    }

    /** Swap sort_order with the neighbour above/below. */
    public function move(int $id, string $dir)
    {
        $ids = $this->query()->pluck('id')->all();
        $i = array_search($id, $ids, true);
        $j = $dir === 'up' ? $i - 1 : $i + 1;
        if ($i !== false && isset($ids[$j])) {
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $n => $key) {
                ($this->model())::whereKey($key)->update(['sort_order' => $n + 1]);
            }
        }

        return back();
    }
}
