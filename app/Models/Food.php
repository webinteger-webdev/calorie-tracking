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

    public static function fetchFromOpenFoodFacts($openfoodfactsId)
    {
        $client = new Client();
        $response = $client->get("https://world.openfoodfacts.org/api/v0/product/{$openfoodfactsId}.json");
        $data = json_decode($response->getBody(), true);

        if (isset($data['product'])) {
            return self::updateOrCreate(
                ['openfoodfacts_id' => $openfoodfactsId],
                [
                    'name' => $data['product']['product_name'] ?? 'Unknown',
                    'calories' => $data['product']['nutriments']['energy-kcal_100g'] ?? 0,
                    'protein' => $data['product']['nutriments']['proteins_100g'] ?? 0,
                    'carbs' => $data['product']['nutriments']['carbohydrates_100g'] ?? 0,
                    'fat' => $data['product']['nutriments']['fat_100g'] ?? 0,
                    'fiber' => $data['product']['nutriments']['fiber_100g'] ?? null,
                    'serving_unit' => 'g',
                    'source' => 'Open Food Facts',
                ]
            );
        }
        return null;
    }
}
