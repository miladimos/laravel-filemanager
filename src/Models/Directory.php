<?php

namespace App\Models;

use App\Traits\hasUUID;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use hasUUID;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'directories';

    // protected $fillable = ['name'];

    protected $guarded = [];


    public function parent()
    {
        return $this->belongsTo(Directory::class, 'parent_id', 'id')
    }



    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
