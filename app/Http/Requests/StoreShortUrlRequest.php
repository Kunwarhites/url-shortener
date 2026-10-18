<?php

namespace App\Http\Requests;

use App\Models\ShortUrl;
use Illuminate\Foundation\Http\FormRequest;

class StoreShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ShortUrl::class);
    }

    public function rules(): array
    {
        return [
            'long_url' => ['required', 'url:http,https', 'max:2048'],
        ];
    }
}
