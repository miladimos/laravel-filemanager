<?php

namespace Miladimos\FileManager\Models;

use Miladimos\FileManager\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Miladimos\FileManager\Traits\RouteKeyNameUUID;

class Directory extends Model
{
    use HasUUID, RouteKeyNameUUID;

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
}
