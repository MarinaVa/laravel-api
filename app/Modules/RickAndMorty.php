<?php

namespace App\Modules;

use Illuminate\Support\Facades\Http;

class RickAndMorty
{
    private $baseUrl = 'https://rickandmortyapi.com/api';
    
    public function getCharacters($params)
    {
        $endPoint = $this->baseUrl . '/character';
        
        $queryParams = [];
        
        if (!empty($params['page'])) {
            $queryParams['page'] = $params['page'];
        }
        
        if (!empty($params['name'])) {
            $queryParams['name'] = $params['name'];
        } 
        
        if (!empty($params['status'])) {
            $queryParams['status'] = $params['status'];
        } 
        
        if (!empty($params['species'])) {
            $queryParams['species'] = $params['species'];
        } 
        
        if (!empty($params['gender'])) {
            $queryParams['gender'] = $params['gender'];
        } 

        $response = Http::get($endPoint, $queryParams);
        
        return $response->json();
    }
    
    public function getCharacterById($id)
    {
        $endPoint = $this->baseUrl . '/character/' . $id;

        $response = Http::get($endPoint);
        
        return $response->json();
    }
}