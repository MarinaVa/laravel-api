<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Modules\RickAndMorty;
use App\Models\Character;
use App\Models\Episode;

class CharactersController extends Controller
{
    public function getAll(Request $request)
    {
        $rickAndMorty = new RickAndMorty();
        $characters = $rickAndMorty->getCharacters($request->all());
        
        return response($characters, Response::HTTP_OK);
    }
    
    public function getFavorites(Request $request)
    {
        $myCharacters = Character::getCurrentUserList($request->all());
        
        return response($myCharacters, Response::HTTP_OK);
    }
    
    public function saveCharacter(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        
        if (Character::getFavoriteByParam('rickandmorty_id', $request->id)) {
            return response(['error' => 'The user has already added this character'], Response::HTTP_BAD_REQUEST);
        }
        
        $rickAndMorty = new RickAndMorty();
        $rickAndMortyCharacter = $rickAndMorty->getCharacterById($request->id);
        
        if (isset($rickAndMortyCharacter['error'])) {
            return response(['error' => $rickAndMortyCharacter['error']], Response::HTTP_BAD_REQUEST);
        }
        
        $character = Character::saveCharacter($rickAndMortyCharacter);
        
        foreach ($rickAndMortyCharacter['episode'] as $url) {
            $episode = Episode::saveEpisode($url); 
            $character->episodes()->syncWithoutDetaching($episode->id);
        }       
        
        $character->users()->syncWithoutDetaching(auth()->user()->id);
        
        return response($character, Response::HTTP_OK);
    }
    
    public function deleteCharacter($id)
    {
        $character = Character::getFavoriteByParam('id', $id);
        
        if (!$character) {
            return response(['error' => 'The character is absent in the favorite list'], Response::HTTP_NOT_FOUND);
        }
        
        $character->users()->detach(auth()->user()->id);
        return response($character, Response::HTTP_OK);
    }
}
