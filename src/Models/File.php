<?php

namespace Miladimos\FileManager\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Miladimos\FileManager\Enums\FileStatusEnum;
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function groups()
    {
        return $this->belongsToMany(FileGroup::class, 'file_group_pivot');
    }

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
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

    public function getPathAttribute()
    {
        return $this->base_path . $this->file_name;
    }

    public function getBasenameAttribute(): string
    {
        return $this->filename . '.' . $this->extension;
    }

    /**
     * generate the link for download file
     * this link has expire time
     *
     * @return string
     */
    public function generateLink()
    {

        if (isset($config['secret'])) {
            $secret = $config['secret'];
        }

        if (isset($config['download_link_expire'])) {
            $expireTime = (int)$config['download_link_expire'];
        }

        /** @var int $expireTime */
        $timestamp = Carbon::now()->addMinutes($expireTime)->timestamp;
        $hash = Hash::make($secret . $this->id . request()->ip() . $timestamp);

        return "/api/filemanager/download/$this->id?mac=$hash&t=$timestamp";
    }


    /**
     * download the selected file
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        if (!$this->private) {
            $path = public_path($this->path);
        } else {
            $path = storage_path($this->path);
        }
        return response()->download($path);
    }

}
