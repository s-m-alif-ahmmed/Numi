<?php

namespace App\Helpers;

use App\Models\City;
use Illuminate\Support\Str;

class Helper
{
    // Generate a unique slug from the given string
    public static function generateSlug($modal, $title)
    {

        // Generate the initial slug from the title
        $slug = Str::slug($title);

        // Check if the slug already exists in the database
        $existingSlug = $modal::where('slug', $slug)->get();

        // If the slug exists, keep generating a new slug until a unique one is found
        while ($existingSlug) {
            // Append a random string to the slug
            $randomString = Str::random(5);
            $newSlug = Str::slug($title).'-'.$randomString;

            // Check if the new slug exists
            $existingSlug = $modal::where('slug', $newSlug)->first();

            // Set the slug to the new unique slug
            if (! $existingSlug) {
                $slug = $newSlug;
            }
        }

        return $slug;
    }

    // Create City
    public static function getCity($county, $city_name, $city_short_code)
    {
        $data = City::where('city_short_code', $city_short_code)->first();

        if (empty($data)) {
            $data = new City;
            $data->city_name = $city_name;
            $data->city_short_code = $city_short_code;
            $data->slug = Helper::generateSlug(\App\Models\City::class, $city_name);
            $data->country_name = $county;
            $data->save();

            return $data->id;
        } else {
            return $data->id;
        }

    }
}
