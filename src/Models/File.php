<?php

namespace Miladimos\FileManager\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Miladimos\FileManager\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Miladimos\FileManager\Traits\RouteKeyNameUUID;

class File extends Model
{
    use HasUUID, RouteKeyNameUUID;

    protected $table = 'files';

    // protected $fillable = ['imageable_id', 'imageable_type', 'url'];

    protected $guarded = [];

    protected $casts = [
        'size' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(config('filemanager.database.user_model'), 'user_id');
    }

    public function groups()
    {
        return $this->belongsToMany(FileGroup::class, 'file_group_pivot');
    }

    public function fileable()
    {
        return $this->morphTo();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = now() . '-' . $value;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    /**
     * generate the link for download file
     * this link has expire time
     *
     * @return string
     */
    public function generateDownloadLink()
    {
        $secret = env('APP_KEY');

        $expireTime = (int)config('filemanager.download_link_expire');

        $timestamp = Carbon::now()->addMinutes($expireTime)->timestamp;
        $hash = Hash::make($secret . $this->uuid . getUserIP() . $timestamp);

//        return "/api/filemanager/download/$this->uuid?mac=$hash&t=$timestamp";
        return route('filemanager.download', [$this, $hash, $timestamp]);
    }

    public function getPublicUrl($key = null)
    {
        $storageDisk = Storage::disk(config('filemanager.disk'));
        $url = $storageDisk->url('uploads/' . $this->uuid . '/' . $this->file_name);
        if (config('filemanager.files.' . $key)) {
            list($key, $resize, $size) = explode('.', $key);
            $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
            $name = str_replace('.' . $extension, '', $this->file_name);
            $url = $storageDisk->url('cache/' . $this->uuid . '/' . $name . '-' . $size . '.' . $extension);
        }

        return $url;
    }

    public function getIsPrivateAttribute()
    {
        return $this->is_private ? true : false;
    }

    public function getIsPublicAttribute()
    {
        return $this->is_private ? false : true;
    }

    public function getBasenameAttribute(): string
    {
        return $this->name . '.' . $this->extension;
    }

}
