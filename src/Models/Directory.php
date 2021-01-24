<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use HasUUID;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'directories';

    // protected $fillable = ['name', 'uuid'];

    protected $guarded = [];


    public function parent()
    {
        return $this->belongsTo(Directory::class, 'parent_id', 'id');
    }


    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
