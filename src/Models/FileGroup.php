<?php

namespace App\Models;

use App\Traits\HasUUID;
use Miladimos\FileManager\Models\File;
use Illuminate\Database\Eloquent\Model;

class FileGroup extends Model
{
    use HasUUID;

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

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
