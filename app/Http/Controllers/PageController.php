<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureCategory;
use App\Models\InfrastructureService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function products(Request $request)
    {
        $categories = InfrastructureCategory::query()
            ->where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->orderBy('price');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.products', [
            'categories' => $categories,
            'selectedCategory' => null,
        ]);
    }

    public function productCategory(InfrastructureCategory $category)
    {
        abort_unless($category->is_active, 404);

        $categories = InfrastructureCategory::query()
            ->where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->orderBy('price');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.products', [
            'categories' => $categories,
            'selectedCategory' => $category,
        ]);
    }

    public function productService(InfrastructureCategory $category, InfrastructureService $service)
    {
        abort_unless($category->is_active, 404);
        abort_unless($service->is_active, 404);
        abort_unless($service->infrastructure_category_id === $category->id, 404);

        $service->load('category');

        $categories = InfrastructureCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.product', [
            'categories' => $categories,
            'category' => $category,
            'service' => $service,
        ]);
    }

    public function solutions()
    {
        return view('pages.solutions');
    }

    public function pricing()
    {
        $categories = InfrastructureCategory::query()
            ->where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->orderBy('price');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.pricing', [
            'categories' => $categories,
        ]);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contacts()
    {
        return view('pages.contacts');
    }

    public function blog()
    {
        return view('pages.blog');
    }

    public function status()
    {
        return view('pages.status');
    }

    public function knowledgeBase()
    {
        return view('pages.knowledge-base');
    }

    public function apiDocs()
    {
        return view('pages.api-docs');
    }

    public function legal()
    {
        $docs = $this->legalDocs();

        return view('pages.legal', ['docs' => $docs]);
    }

    public function legalDoc(string $doc)
    {
        $docs = $this->legalDocs();
        abort_unless(isset($docs[$doc]), 404);

        return view('legal.doc', [
            'docKey' => $doc,
            'docTitle' => $docs[$doc]['title'],
        ]);
    }

    private function legalDocs(): array
    {
        return [
            'user-agreement' => ['title' => 'Пользовательское соглашение'],
            'service-rules' => ['title' => 'Правила пользования сервисами и услугами'],
            'offer' => ['title' => 'Договор-оферта'],
            'personal-data-consent' => ['title' => 'Согласие на обработку персональных данных'],
            'privacy' => ['title' => 'Обработка и защита персональных данных'],
            'cookies' => ['title' => 'Использование файлов Cookies'],
        ];
    }
}
