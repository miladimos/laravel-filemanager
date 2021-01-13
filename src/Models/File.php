<?php

namespace Miladimos\FileManager\Models;

use Carbon\Carbon;
use App\Traits\hasUUID;
use App\Models\FileGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class File extends Model
{
    use hasUUID;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'files';

    // protected $fillable = ['imageable_id', 'imageable_type', 'url'];

    protected $guarded = [];

    protected $casts = [
        'size' => 'int',
    ];

    public function groups()
    {
        return $this->belongsToMany(FileGroup::class, 'file_group_pivot');
    }

    /**
     * Retrieve all associated models of given class.
     * @param  string $class FQCN
     * @return MorphToMany
     */
    public function models(string $class): MorphToMany
    {
        return $this
            ->morphedByMany(
                $class,
                'mediable',
                config('mediable.mediables_table', 'mediables')
            )
            ->withPivot('tag', 'order');
    }

    protected $uploads = '/images/';

    public function getUrlAttribute($image)
    {
        return $this->uploads . $image;
    }

    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Retrieve the file extension.
     * @return string
     */
    public function getBasenameAttribute(): string
    {
        return $this->filename . '.' . $this->extension;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
    //    public function getPublicUrl($key = null)
    //    {
    //        $storageDisk =  Storage::disk(config('upload.disk'));
    //        $url =  $storageDisk->url('uploads/' . $this->uuid . '/' . $this->file_name);
    //        if (config('upload.files.' . $key)) {
    //            list($key, $resize, $size) = explode('.', $key);
    //            $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
    //            $name = str_replace('.'.$extension,'', $this->file_name);
    //            $url = $storageDisk->url('cache/' . $this->uuid . '/' . $name . '-' . $size . '.' . $extension);
    //        }
    //
    //        return $url;
    //    }
    //
    //    public function getPrivateUrl()
    //    {
    //
    //    }
    //
    //    public function getAllPublicUrl($key = null)
    //    {
    //        $urls = [];
    //        foreach (config('upload.files.'.$key.'.resize', []) as $keys => $value) {
    //            if (array_get($value,'create_on_upload')) {
    //                $urls[$keys] = $this->getPublicUrl($key.'.resize.'.$keys);
    //            }
    //        }
    //
    //        return $urls;
    //    }


    //    public static function getUserQuotaUsed($userid) {
    //        $user = User::find($userid);
    //
    //        $userQuota = $user->leftJoin('files', 'files.user_id' ,'=', 'users.id')
    //            ->groupBy('disk_quota')
    //            ->selectRaw('ifnull((sum(files.file_size) / 1024 / 1024), 0) disk_usage, disk_quota')
    //            ->where('users.id', $userid)
    //            ->first();
    //
    //        return $userQuota;
    //    }


    //    public function user() {
    //        return $this->belongsTo('App\User');
    //    }

    //    public function getUserDiskQuota(Request $request) {
    //        $id = $request->input('userId');
    //
    //        $userQuota = QuotaHelper::getUserQuotaUsed($id);
    //
    //        return $userQuota->toJson();
    //    }


}
