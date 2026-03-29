<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\DynamicField;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

class DynamicFieldAdminController extends Controller
{
    public function index(Request $request): View
    {
        $fields = DynamicField::query()
            ->with(['category', 'subcategory'])
            ->orderBy('sort_order')
            ->latest()
            ->paginate((int) $request->query('per_page', 12))
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name_ar')
            ->get();

        $subcategories = Subcategory::query()
            ->with('category')
            ->orderBy('name_ar')
            ->get();

        $selectedField = null;

        $fieldItems = collect($fields->items());

        if ($request->filled('selected')) {
            $selectedField = $fieldItems->firstWhere('id', (int) $request->query('selected'));
        }

        if (! $selectedField) {
            $selectedField = $fieldItems->first();
        }

        $types = ['text', 'textarea', 'number', 'select', 'boolean', 'date'];

        return view('admin.dynamic-fields.index', compact(
            'fields',
            'categories',
            'subcategories',
            'selectedField',
            'types'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'label_ar' => ['required', 'string', 'max:255'],
            'label_en' => ['nullable', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'unique:dynamic_fields,key'],
            'type' => ['required', 'in:text,textarea,number,select,boolean,date'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'options_text' => ['nullable', 'string'],
        ]);

        if (empty($data['category_id']) && empty($data['subcategory_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'category_id' => 'يجب اختيار تصنيف رئيسي أو تصنيف فرعي على الأقل.',
                ]);
        }

        $options = null;

        if ($data['type'] === 'select') {
            $options = collect(explode(',', (string) ($data['options_text'] ?? '')))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        $field = DynamicField::query()->create([
            'category_id' => $data['category_id'] ?? null,
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'label_ar' => $data['label_ar'],
            'label_en' => $data['label_en'] ?? null,
            'key' => $data['key'],
            'type' => $data['type'],
            'is_required' => (bool) ($data['is_required'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
            'sort_order' => $data['sort_order'] ?? 0,
            'options' => $options,
        ]);

        return redirect()
            ->route('admin.dynamic-fields.index', ['selected' => $field->id])
            ->with('success', __('messages.created_successfully'));
    }

    public function update(Request $request, DynamicField $dynamicField): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'label_ar' => ['required', 'string', 'max:255'],
            'label_en' => ['nullable', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'unique:dynamic_fields,key,' . $dynamicField->id],
            'type' => ['required', 'in:text,textarea,number,select,boolean,date'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'options_text' => ['nullable', 'string'],
        ]);

        if (empty($data['category_id']) && empty($data['subcategory_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'category_id' => 'يجب اختيار تصنيف رئيسي أو تصنيف فرعي على الأقل.',
                ]);
        }

        $options = null;

        if ($data['type'] === 'select') {
            $options = collect(explode(',', (string) ($data['options_text'] ?? '')))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        $dynamicField->update([
            'category_id' => $data['category_id'] ?? null,
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'label_ar' => $data['label_ar'],
            'label_en' => $data['label_en'] ?? null,
            'key' => $data['key'],
            'type' => $data['type'],
            'is_required' => (bool) ($data['is_required'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
            'sort_order' => $data['sort_order'] ?? 0,
            'options' => $options,
        ]);

        return redirect()
            ->route('admin.dynamic-fields.index', ['selected' => $dynamicField->id])
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(DynamicField $dynamicField): RedirectResponse
    {
        $dynamicField->delete();

        return redirect()
            ->route('admin.dynamic-fields.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}