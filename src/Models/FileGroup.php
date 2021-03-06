<?php

namespace App\Models;

use Miladimos\FileManager\Traits\HasUUID;
use Miladimos\FileManager\Models\File;
use Illuminate\Database\Eloquent\Model;
use Miladimos\FileManager\Traits\RouteKeyNameUUID;

class FileGroup extends Model
{
    use HasUUID,
        RouteKeyNameUUID;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'file_groups';


    // protected $fillable = ['title', 'description', 'uuid'];

    protected $guarded = [];


    public function files()
    {
        return $this->belongsToMany(File::class, 'file_group_pivot');
    }
}
