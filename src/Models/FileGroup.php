<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileGroup extends Model
{
    protected $fillable = ['name'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'my_flights';
}
