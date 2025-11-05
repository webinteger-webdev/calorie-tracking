<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'name',
        'brand',
        'category_id',
        'calories',
        'protein',
        'carbs',
        'fat',
        'fiber',
        'serving_unit',
        'source',
        'openfoodfacts_id',
        'created_by',
    ];

    // Beziehungen
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'meal_foods')
            ->withPivot('amount_g')
            ->withTimestamps();
    }

    // Abfragen
    public static function search($query, $categoryId = null, $brand = null)
    {
        $foods = self::where('name', 'like', "%{$query}%");
        if ($categoryId) {
            $foods->where('category_id', $categoryId);
        }
        if ($brand) {
            $foods->where('brand', 'like', "%{$brand}%");
        }
        return $foods->orderBy('name')->limit(10)->get();
    }

    public function calculateNutrientsForAmount($amountG)
    {
        $factor = $amountG / 100;
        return [
            'calories' => $this->calories * $factor,
            'protein' => $this->protein * $factor,
            'carbs' => $this->carbs * $factor,
            'fat' => $this->fat * $factor,
            'fiber' => $this->fiber ? $this->fiber * $factor : null,
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function fetchFromOpenFoodFacts($openfoodfactsId, $name = null)
    {
        // dd('fetchFromOpenFoodFacts is called with', $openfoodfactsId, $name);
        $client = new Client();
        $response = $client->get("https://world.openfoodfacts.org/api/v0/product/{$openfoodfactsId}.json");
        $data = json_decode($response->getBody(), true);

        if (isset($data['product'])) {
            $product = $data['product'];

            return self::updateOrCreate(
                ['openfoodfacts_id' => $openfoodfactsId],
                [
                    'name' => $product['product_name'] ?? $name,
                    'brand' => $product['brands'] ?? '',
                    'calories' => $product['nutriments']['energy-kcal_100g'] ?? 0,
                    'protein' => $product['nutriments']['proteins_100g'] ?? 0,
                    'carbs' => $product['nutriments']['carbohydrates_100g'] ?? 0,
                    'fat' => $product['nutriments']['fat_100g'] ?? 0,
                    'fiber' => $product['nutriments']['fiber_100g'] ?? null,
                    'serving_unit' => $product['serving_size'] ?? '100g',
                    'source' => 'Open Food Facts',
                    'openfoodfacts_id' => $openfoodfactsId,
                ]
            );
        }
        return null;
    }
}
