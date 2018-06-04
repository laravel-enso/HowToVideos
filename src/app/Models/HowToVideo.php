<?php

namespace LaravelEnso\HowToVideos\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\HowToVideos\app\Classes\Storer;
use LaravelEnso\HowToVideos\app\Classes\Destroyer;
use LaravelEnso\HowToVideos\app\Classes\Presenter;

class HowToVideo extends Model
{
    protected $fillable = [
        'name', 'description', 'video_original_name', 'video_saved_name',
        'poster_original_name', 'poster_saved_name'
    ];

    protected $appends = ['tagList'];

    public function tags()
    {
        return $this->belongsToMany(HowToTag::class);
    }

    public static function store(array $file, array $attributes)
    {
        return (new Storer($file, $attributes))
            ->video()
            ->run();
    }

    public function addPoster(array $file)
    {
        return (new Storer($file, $this->toArray()))
            ->poster()
            ->run();
    }

    public function video()
    {
        return (new Presenter($this))
            ->video()
            ->inline();
    }

    public function poster()
    {
        return (new Presenter($this))
            ->poster()
            ->inline();
    }

    public function removePoster()
    {
        (new Destroyer($this))
            ->poster()
            ->run();

        $this->update([
            'poster_saved_name' => null,
            'poster_original_name' => null,
        ]);
    }

    public function updateWithTags($request)
    {
        \DB::transaction(function () use ($request) {
            $this->update([
                'name' => $request['name'],
                'description' => $request['description'],
            ]);

            $this->tags()->sync($request['tagList']);
        });
    }

    public function getTagListAttribute()
    {
        return $this->tags()->pluck('id');
    }

    public function delete()
    {
        if ($this->poster_saved_name) {
            $this->removePoster();
        }

        (new Destroyer($this))
            ->video()
            ->run();

        parent::delete();
    }
}
