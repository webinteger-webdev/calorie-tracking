<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'servings',
        'calories_per_serving',
        'protein_per_serving',
        'carbs_per_serving',
        'fat_per_serving',
        'user_id',
    ];

    // Beziehungen
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foods()
    {
        return $this->belongsToMany(Food::class, 'recipe_ingredients')
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

    public function updateNutrients()
    {
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;

        foreach ($this->foods as $food) {
            $factor = $food->pivot->quantity / 100;
            $totalCalories += $food->calories * $factor;
            $totalProtein += $food->protein * $factor;
            $totalCarbs += $food->carbs * $factor;
            $totalFat += $food->fat * $factor;
        }

        $this->update([
            'calories_per_serving' => $totalCalories / $this->servings,
            'protein_per_serving' => $totalProtein / $this->servings,
            'carbs_per_serving' => $totalCarbs / $this->servings,
            'fat_per_serving' => $totalFat / $this->servings,
        ]);
    }

    public function calculateNutrientsForAmount($amountG)
    {
        $factor = $amountG / 100;
        return [
            'calories' => $this->calories_per_serving * $factor,
            'protein' => $this->protein_per_serving * $factor,
            'carbs' => $this->carbs_per_serving * $factor,
            'fat' => $this->fat_per_serving * $factor,
        ];
    }
}
