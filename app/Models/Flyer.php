<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flyer extends Model
{
    use HasFactory;

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->isActiveFlyer = $this->checkActiveFlyer($this->start_date, $this->end_date);
    }

    protected $fillable = [
        'id', 'title', 'start_date', 'end_date', 'is_published', 'retailer', 'category'
    ];

    protected $hidden = [
        'isActiveFlyer'
    ];

    private function checkActiveFlyer($startDate, $endDate) 
    {
        $isActive = false;
        $currentDate = date("Y-m-d");
        if ($startDate <= $currentDate && $endDate >= $currentDate) {
            $isActive = true;
        }
        return $isActive;
    }


}
