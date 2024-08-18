<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Episode;
use App\Models\User;

class Character extends Model
{
    use HasFactory;
    
    protected $hidden = ['pivot'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rickandmorty_id',
        'name',
        'status',
        'species',
        'type',
        'gender',
        'origin_name',
        'origin_url',
        'location_name',
        'location_url',
        'image',
        'url'
    ];
    
    public function episodes()
    {
        return $this->belongsToMany(Episode::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    
    public static function saveCharacter($data)
    {
        $character = static::where([
            'rickandmorty_id' => $data['id']
        ])->first();
        
        if (!$character) {
            $character = new static();
            $character->rickandmorty_id = $data['id'];
            $character->name = $data['name'];
            $character->status = $data['status'];
            $character->species = $data['species'];
            $character->type = $data['type'];
            $character->gender = $data['gender'];    
            $character->origin_name = $data['origin']['name'];
            $character->origin_url = $data['origin']['url'];
            $character->location_name = $data['location']['name'];
            $character->location_url = $data['location']['url'];
            $character->image = $data['image'];
            $character->url = $data['url'];
            $character->save();
        }
        
        return $character;
    }
    
    public static function getCurrentUserList($params)
    {
        $userList = static::with('episodes')
            ->whereHas('users', function ($query) {
                $query->where('user_id', auth()->user()->id);
            });
        
        if (!empty($params['name'])) {
            $userList->where(['name' => $params['name']]);
        } 
        
        if (!empty($params['status'])) {
            $userList->where(['status' => $params['status']]);
        } 
        
        if (!empty($params['species'])) {
            $userList->where(['species' => $params['species']]);
        } 
        
        if (!empty($params['gender'])) {
            $userList->where(['gender' => $params['gender']]);
        } 
            
        return $userList->paginate(10);
    }
    
    public static function getFavoriteByParam($paramName, $value)
    {
        $userCharacter = static::where([$paramName => $value])
            ->whereHas('users', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->first();
        
        return $userCharacter;
    }
}
