<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Client;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        
        $client       = new Client();
        // $page représente la page affiché par le tableau
        $page = isset($_GET["page"]) ? $_GET["page"] : 0;
        // $nbParPage représente le nombre de pokemon affiché par page
        $nbParPage    = 3;
        // $start représente l'id du 1er pokemon de la page
        $start        = 1 + ($page * $nbParPage);
        //$fin représente l'id du dernier pokemon de la page
        $fin          = $start + $nbParPage;
    	//Boucle permettant de recuperer tout les pokemons via l'api
        for ($i = $start; $i < $fin; $i++) {
            $client->setUri('https://pokeapi.co/api/v2/pokemon/' . $i);
            $response             = $client->send();
            $responseContent      = $response->getBody();
            $responseJson         = json_decode($responseContent);

            if($response->getStatusCode() != '200'){
            	throw new \Exception("Error : unable to reach the pokeapi");
            }

            $pokemons[$i]['name'] = $responseJson->name;
            foreach ($responseJson->types as $type) {
                $pokemons[$i]['types'][] = $type->type->name;
            }
            $pokemons[$i]['image'] = $responseJson->sprites->front_default;

        }
        return new ViewModel(array(
            'pokemons' => $pokemons,
            'page' => $page
        ));
    }
}