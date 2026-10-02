<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tourPackage = $this->route('tourPackage');

        $image = [ 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048' ];

        return [

        
            'category_id' => [ 'nullable', 'integer', 'exists:categories,id' ],

            'hero_image' => $image,

            'hero_image_title' => [ 'nullable', 'array' ],
            'hero_image_title.en' => [ 'nullable', 'string', 'max:255' ],
            'hero_image_title.bn' => [ 'nullable', 'string', 'max:255' ],

            'hero_image_btn' => [ 'nullable', 'array' ],
            'hero_image_btn.en' => [ 'nullable', 'string', 'max:255' ],
            'hero_image_btn.bn' => [ 'nullable', 'string','max:255' ],

            'header' => ['nullable', 'array' ],
            'header.en' => [ 'nullable', 'string', 'max:1000' ],
            'header.bn' => [ 'nullable', 'string', 'max:1000' ],

            'sub_header' => [ 'nullable', 'array' ],
            'sub_header.en' => ['nullable', 'string', 'max:1000' ],
            'sub_header.bn' => ['nullable', 'string', 'max:1000' ],

            'package_name' => [ 'required', 'array' ],
            'package_name.en' => [ 'required', 'string', 'max:255' ],
            'package_name.bn' => [ 'nullable', 'string', 'max:255' ],

       
            'slug' => ['nullable', 'string', 'max:255',
                Rule::unique('tour_packages', 'slug')
                    ->ignore(
                        $tourPackage?->id
                    ),
            ],

            'package_image' => [ $this->isMethod('post') ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'package_price' => [ 'nullable', 'array' ],
            'package_price.en' => [ 'nullable', 'string', 'max:255' ],
            'package_price.bn' => [ 'nullable', 'string', 'max:255' ],

            'package_duration' => ['nullable','array' ],
            'package_duration.en' => [ 'nullable', 'string','max:255' ],
            'package_duration.bn' => [ 'nullable', 'string', 'max:255' ],

            'package_image_title' => [ 'nullable', 'array'],
            'package_image_title.en' => [ 'nullable', 'string', 'max:255' ],
            'package_image_title.bn' => [ 'nullable', 'string', 'max:255' ],

            'package_map_image' => [ 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048' ],

            'package_destination' => [ 'nullable', 'array' ],
            'package_destination.en' => [ 'nullable', 'string', 'max:1000' ],
            'package_destination.bn' => [ 'nullable', 'string', 'max:1000' ],

  
            'order' => [ 'nullable', 'integer', 'min:0' ],

            'is_active' => [ 'nullable', 'boolean' ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' =>
                'The selected country does not exist.',

            'package_name.required' =>
                'The package name is required.',

            'package_name.en.required' =>
                'The English package name is required.',

            'slug.unique' =>
                'This package slug already exists.',

            'package_image.image' =>
                'The package image must be an image.',

            'package_image.mimes' =>
                'The package image must be jpg, jpeg, png, or webp.',

            'hero_image.image' =>
                'The hero image must be an image.',

            'package_map_image.image' =>
                'The map image must be an image.',
        ];
    }
}
