<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public static function search($query)
    {
        return self::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get();
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
}
