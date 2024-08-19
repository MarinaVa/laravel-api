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
    /**
     * Lista de Rick&Morty
     * 
     * Este metodo devuelve el listado de personajes de Rick&Morty API
     * 
     * @unauthenticated
     * 
     * @queryParam page integer Number of page. Example: 1
     * @queryParam name string Character's name. Example: Cult Leader Morty
     * @queryParam status string Character's status. Example: Alive
     * @queryParam species string Character's species. Example: Human
     * @queryParam gender string Character's gender. Example: Male
     */
    public function getAll(Request $request)
    {
        $rickAndMorty = new RickAndMorty();
        $characters = $rickAndMorty->getCharacters($request->all());
        
        return response($characters, Response::HTTP_OK);
    }
    
    /**
     * Lista de favoritos
     * 
     * Este metodo devuelve el listado de los favoritos personajes del usuario
     * 
     * @queryParam page integer Number of page. Example: 1
     * @queryParam name string Character's name. Example: Cult Leader Morty
     * @queryParam status string Character's status. Example: Alive
     * @queryParam species string Character's species. Example: Human
     * @queryParam gender string Character's gender. Example: Male
     */
    public function getFavorites(Request $request)
    {
        $myCharacters = Character::getCurrentUserList($request->all());
        
        return response($myCharacters, Response::HTTP_OK);
    }
    
    /**
     * Guardar personaje
     * 
     * Este metodo guarda el personaje en el listado de favoritos
     * 
     * @bodyParam id integer required Rick&Morty character's id. Example: 1
     */
    public function saveCharacter(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
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
        $character->episodes = $character->episodes()->get();
        
        return response($character, Response::HTTP_OK);
    }
    
    /**
     * Quitar personaje
     * 
     * Este metodo quita el personaje del listado de favoritos
     * 
     * @urlParam id integer required Character's ID. Example: 1
     */
    public function deleteCharacter($id)
    {
        if (!is_numeric($id)) {
            return response(['error' => 'The id must be a number'], Response::HTTP_BAD_REQUEST);
        }
        
        $character = Character::getFavoriteByParam('id', (int)$id);
        
        if (!$character) {
            return response(['error' => 'The character is absent in the favorite list'], Response::HTTP_NOT_FOUND);
        }
        
        $character->users()->detach(auth()->user()->id);
        return response($character, Response::HTTP_OK);
    }
}
