<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in_id', 'user_id', 'store_id', 'items', 'photos', 'comments', 'overall_value',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'photos' => 'array',
            'overall_value' => 'integer',
        ];
    }

    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
