<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Miladimos\FileManager\Models\File;

class FileGroup extends Model
{
    protected $fillable = ['title', 'description'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'file_groups';

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
