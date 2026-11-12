<?php

namespace App\Support;

/** Builders for JSON-LD structured data. */
class Schema
{
    public static function organization(array $site): array
    {
        $same = array_values(array_filter([$site['social_facebook'] ?? null, $site['social_instagram'] ?? null, $site['social_youtube'] ?? null]));

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            'name' => $site['brand_name'] ?? config('app.name'),
            'legalName' => $site['legal_company_name'] ?? null,
            'url' => url('/'),
            'telephone' => ($site['phone_tel'] ?? '') ?: null,
            'email' => str_contains($site['email'] ?? '', '@') ? $site['email'] : null,
            'sameAs' => $same ?: null,
        ]);
    }

    public static function faq($faqs): ?array
    {
        $items = collect($faqs)->filter(fn ($f) => ! empty($f['question']))->map(fn ($f) => [
            '@type' => 'Question',
            'name' => $f['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f['answer'])],
        ])->values()->all();

        return $items ? ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items] : null;
    }

    /** @param array<int, array{0:string,1:string}> $crumbs [name, url] */
    public static function breadcrumbs(array $crumbs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($crumbs)->values()->map(fn ($c, $i) => [
                '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1],
            ])->all(),
        ];
    }

    public static function trip($package): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $package->title,
            'description' => strip_tags((string) $package->summary),
            'url' => route('packages.show', $package),
            'touristType' => $package->tourType?->name,
            'offers' => $package->price_from ? [
                '@type' => 'Offer', 'priceCurrency' => $package->currency, 'price' => (string) $package->price_from,
                'availability' => 'https://schema.org/InStock', 'url' => route('packages.show', $package),
            ] : null,
        ]);
    }
}
