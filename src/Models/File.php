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
    use HasUUID,
        RouteKeyNameUUID;

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

    public function getFullUrl(string $conversionName = ''): string
    {
        return url($this->getUrl($conversionName));
    }

    public function getExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function getHumanReadableSizeAttribute(): string
    {
        return File::getHumanReadableSize($this->size);
    }

    public function getDiskDriverName(): string
    {
        return strtolower(config("filesystems.disks.{$this->disk}.driver"));
    }

    public function getConversionsDiskDriverName(): string
    {
        $diskName = $this->conversions_disk ?? $this->disk;

        return strtolower(config("filesystems.disks.{$diskName}.driver"));
    }

    public function hasCustomProperty(string $propertyName): bool
    {
        return Arr::has($this->custom_properties, $propertyName);
    }

    /**
     * Get the value of custom property with the given name.
     *
     * @param string $propertyName
     * @param mixed $default
     *
     * @return mixed
     */
    public function getCustomProperty(string $propertyName, $default = null)
    {
        return Arr::get($this->custom_properties, $propertyName, $default);
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setCustomProperty(string $name, $value): self
    {
        $customProperties = $this->custom_properties;

        Arr::set($customProperties, $name, $value);

        $this->custom_properties = $customProperties;

        return $this;
    }

    public function forgetCustomProperty(string $name): self
    {
        $customProperties = $this->custom_properties;

        Arr::forget($customProperties, $name);

        $this->custom_properties = $customProperties;

        return $this;
    }

    public function getMediaConversionNames(): array
    {
        $conversions = ConversionCollection::createForMedia($this);

        return $conversions->map(fn (Conversion $conversion) => $conversion->getName())->toArray();
    }

    public function getGeneratedConversions(): Collection
    {
        return collect($this->generated_conversions ?? []);
    }


    public function markAsConversionGenerated(string $conversionName): self
    {
        $generatedConversions = $this->generated_conversions;

        Arr::set($generatedConversions, $conversionName, true);

        $this->generated_conversions = $generatedConversions;

        $this->save();

        return $this;
    }

    public function markAsConversionNotGenerated(string $conversionName): self
    {
        $generatedConversions = $this->generated_conversions;

        Arr::set($generatedConversions, $conversionName, false);

        $this->generated_conversions = $generatedConversions;

        $this->save();

        return $this;
    }

    public function hasGeneratedConversion(string $conversionName): bool
    {
        $generatedConversions = $this->getGeneratedConversions();

        return $generatedConversions[$conversionName] ?? false;
    }

    public function toResponse($request)
    {
        return $this->buildResponse($request, 'attachment');
    }

    public function toInlineResponse($request)
    {
        return $this->buildResponse($request, 'inline');
    }

    private function buildResponse($request, string $contentDispositionType)
    {
        $downloadHeaders = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => $this->mime_type,
            'Content-Length' => $this->size,
            'Content-Disposition' => $contentDispositionType . '; filename="' . $this->file_name . '"',
            'Pragma' => 'public',
        ];

        return response()->stream(function () {
            $stream = $this->stream();

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $downloadHeaders);
    }

    public function getResponsiveImageUrls(string $conversionName = ''): array
    {
        return $this->responsiveImages($conversionName)->getUrls();
    }

    public function hasResponsiveImages(string $conversionName = ''): bool
    {
        return count($this->getResponsiveImageUrls($conversionName)) > 0;
    }

    public function getSrcset(string $conversionName = ''): string
    {
        return $this->responsiveImages($conversionName)->getSrcset();
    }

    public function getPreviewUrlAttribute()
    {
        return $this->hasGeneratedConversion('preview') ? $this->getUrl('preview') : '';
    }

    public function getOriginalUrlAttribute()
    {
        return $this->getUrl();
    }

    public function move(HasMedia $model, $collectionName = 'default', string $diskName = '', string $fileName = ''): self
    {
        $newMedia = $this->copy($model, $collectionName, $diskName, $fileName);

        $this->delete();

        return $newMedia;
    }

    public function copy(HasMedia $model, $collectionName = 'default', string $diskName = '', string $fileName = ''): self
    {
        $temporaryDirectory = TemporaryDirectory::create();

        $temporaryFile = $temporaryDirectory->path('/') . DIRECTORY_SEPARATOR . $this->file_name;

        /** @var \Spatie\MediaLibrary\MediaCollections\Filesystem $filesystem */
        $filesystem = app(Filesystem::class);

        $filesystem->copyFromMediaLibrary($this, $temporaryFile);

        $fileAdder = $model
            ->addMedia($temporaryFile)
            ->usingName($this->name)
            ->setOrder($this->order_column)
            ->withCustomProperties($this->custom_properties);

        if ($fileName !== '') {
            $fileAdder->usingFileName($fileName);
        }

        $newMedia = $fileAdder
            ->toMediaCollection($collectionName, $diskName);

        $temporaryDirectory->delete();

        return $newMedia;
    }

    public function responsiveImages(string $conversionName = ''): RegisteredResponsiveImages
    {
        return new RegisteredResponsiveImages($this, $conversionName);
    }

    public function stream()
    {
        /** @var \Spatie\MediaLibrary\MediaCollections\Filesystem $filesystem */
        $filesystem = app(Filesystem::class);

        return $filesystem->getStream($this);
    }

    public function toHtml()
    {
        return $this->img()->toHtml();
    }

    public function img(string $conversionName = '', $extraAttributes = []): HtmlableMedia
    {
        return (new HtmlableMedia($this))
            ->conversion($conversionName)
            ->attributes($extraAttributes);
    }

    public function __invoke(...$arguments): HtmlableMedia
    {
        return $this->img(...$arguments);
    }

    public function temporaryUpload(): BelongsTo
    {
        MediaLibraryPro::ensureInstalled();

        return $this->belongsTo(TemporaryUpload::class);
    }

    public static function findWithTemporaryUploadInCurrentSession(array $uuids)
    {
        MediaLibraryPro::ensureInstalled();

        return static::query()
            ->whereIn('uuid', $uuids)
            ->whereHasMorph(
                'model',
                [TemporaryUpload::class],
                fn (Builder $builder) => $builder->where('session_id', session()->getId())
            )
            ->get();
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

    public function __toString()
    {
        return "name: {$this->name}, size: {$this->size}, mime: {$this->mimeType}";
    }
}
