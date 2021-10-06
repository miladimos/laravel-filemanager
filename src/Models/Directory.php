<?php

namespace Miladimos\FileManager\Models;

use Miladimos\FileManager\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Miladimos\FileManager\Traits\RouteKeyNameUUID;

class Directory extends Model
{
    use HasUUID,
        RouteKeyNameUUID;

    protected $table = 'directories';

    // protected $fillable = ['name', 'uuid'];

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Directory::class, 'parent_id', 'id');
    }

    // who created ?
    public function user()
    {
        return $this->belongsTo(config('filemanager.database.user_model'), 'user_id');
    }
}
