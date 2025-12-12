<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtamMagangDnPerush extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etam_magang_dn_perush';

    protected $fillable = [
        'magang_id',
        'user_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Relationship dengan Job Fair
     */
    public function magang()
    {
        return $this->belongsTo(EtamMagangDn::class, 'magang_id');
    }

    /**
     * Relationship dengan User (Perusahaan)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship dengan User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan User yang mengupdate
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship dengan User yang menghapus
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 0:
                return 'Pending';
            case 1:
                return 'Approved';
            case 2:
                return 'Rejected';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 0:
                return '<span class="badge bg-warning">Pending</span>';
            case 1:
                return '<span class="badge bg-success">Approved</span>';
            case 2:
                return '<span class="badge bg-danger">Rejected</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }
}
