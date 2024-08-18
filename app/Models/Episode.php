<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Character;

class Episode extends Model
{
    use HasFactory;
    
    protected $hidden = ['pivot'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url'
    ];
    
    public function characters()
    {
        return $this->belongsToMany(Character::class);
    }
    
    public static function saveEpisode($episodeUrl)
    {
        $episode = static::where([
            'url' => $episodeUrl,
        ])->first();
        
        if (!$episode) {
            $episode = new static();
            $episode->url = $episodeUrl;
            $episode->save();
        } 
        
        return $episode;
    }
}