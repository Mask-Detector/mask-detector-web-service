<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaskDetection extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'detection_results',
        'total_persons',
        'wearing_mask',
        'not_wearing_mask',
        'confidence_avg',
        'detected_at'
    ];

    protected $casts = [
        'detection_results' => 'array',
        'detected_at' => 'datetime',
        'confidence_avg' => 'decimal:2'
    ];

    // Accessor untuk mendapatkan URL lengkap gambar
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('detected_at', $date);
    }

    // Scope untuk filter berdasarkan compliance (semua pakai masker)
    public function scopeCompliant($query)
    {
        return $query->where('not_wearing_mask', 0);
    }

    // Scope untuk filter berdasarkan non-compliance (ada yang tidak pakai masker)
    public function scopeNonCompliant($query)
    {
        return $query->where('not_wearing_mask', '>', 0);
    }
}
