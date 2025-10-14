<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'date',
        'total_calories',
        'total_protein',
        'total_carbs',
        'total_fat',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Beziehungen
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foods()
    {
        return $this->belongsToMany(Food::class, 'meal_foods')
            ->withPivot('amount_g')
            ->withTimestamps();
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'meal_foods')
            ->withPivot('amount_g')
            ->withTimestamps();
    }

    // Abfragen
    public static function getMealsForUserAndDate($userId, $date)
    {
        return self::where('user_id', $userId)
            ->where('date', $date)
            ->orderBy('type')
            ->get();
    }

    public static function getDailyTotals($userId, $date)
    {
        return self::where('user_id', $userId)
            ->where('date', $date)
            ->selectRaw('SUM(total_calories) as total_calories,
                         SUM(total_protein) as total_protein,
                         SUM(total_carbs) as total_carbs,
                         SUM(total_fat) as total_fat')
            ->first();
    }

    public function updateTotals()
    {
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;

        foreach ($this->foods as $food) {
            $nutrients = $food->calculateNutrientsForAmount($food->pivot->amount_g);
            $totalCalories += $nutrients['calories'];
            $totalProtein += $nutrients['protein'];
            $totalCarbs += $nutrients['carbs'];
            $totalFat += $nutrients['fat'];
        }

        foreach ($this->recipes as $recipe) {
            $nutrients = $recipe->calculateNutrientsForAmount($recipe->pivot->amount_g);
            $totalCalories += $nutrients['calories'];
            $totalProtein += $nutrients['protein'];
            $totalCarbs += $nutrients['carbs'];
            $totalFat += $nutrients['fat'];
        }

        $this->update([
            'total_calories' => $totalCalories,
            'total_protein' => $totalProtein,
            'total_carbs' => $totalCarbs,
            'total_fat' => $totalFat,
        ]);
    }
}
