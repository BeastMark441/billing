<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureCategory;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function sitemap(Request $request)
    {
        $static = [
            route('products'),
            route('pricing'),
            route('solutions'),
            route('about'),
            route('contacts'),
            route('status'),
            route('knowledge-base'),
            route('api-docs'),
            route('legal'),
            route('legal.doc', 'user-agreement'),
            route('legal.doc', 'service-rules'),
            route('legal.doc', 'offer'),
            route('legal.doc', 'personal-data-consent'),
            route('legal.doc', 'privacy'),
            route('legal.doc', 'cookies'),
        ];

        $categories = InfrastructureCategory::query()
            ->where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        $urls = [];
        foreach ($static as $loc) {
            $urls[] = [
                'loc' => $loc,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('products.category', $category),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];

            foreach ($category->services as $service) {
                $urls[] = [
                    'loc' => route('products.service', [$category, $service]),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }
        }

        $xml = view('seo.sitemap', [
            'urls' => $urls,
            'generatedAt' => now()->toAtomString(),
        ]);

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(Request $request)
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /profile',
            'Disallow: /notifications',
            'Disallow: /payments',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}

