<?php

// STEP 12 — SearchRecipeRequest.
//
// A Laravel Form Request is a specialised Request subclass that Laravel
// validates *before* the controller method is called. If validation
// fails on a JSON request, Laravel returns a 422 with the error bag
// automatically — we never need to write `if (!$validator->passes())`.
//
// Moving the `q` check here lets the controller stop worrying about
// input shape; it just reads `$request->validated('q')` knowing the
// value is non-empty, a string, within length bounds, and trimmed.

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRecipeRequest extends FormRequest
{
    // No auth in this app — every request is allowed through validation.
    // Returning true is explicit; leaving it off would 403 every call.
    public function authorize(): bool
    {
        return true;
    }

    // Validation rules. TheMealDB's `s` param is a substring match; very
    // short queries return thousands of unrelated meals, so we enforce
    // at least 2 chars. 50 is a defensive upper bound.
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:2', 'max:50'],
        ];
    }

    // Friendlier messages than Laravel's defaults ("The q field is required").
    public function messages(): array
    {
        return [
            'q.required' => 'Pass a search term via ?q=, e.g. /api/recipes/search?q=chicken',
            'q.min'      => 'Search term must be at least 2 characters.',
            'q.max'      => 'Search term must be 50 characters or fewer.',
        ];
    }

    // Trim whitespace *before* validation so " chicken " passes min:2
    // cleanly and the service receives a normalised query.
    protected function prepareForValidation(): void
    {
        if ($this->has('q')) {
            $this->merge(['q' => trim((string) $this->input('q'))]);
        }
    }
}
