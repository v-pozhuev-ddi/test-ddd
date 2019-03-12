<?php

namespace App\Ddd\Domain\Client;

use App\Ddd\Application\FractalService;
use App\Ddd\Domain\Client\Entity\Client;
use App\Ddd\Domain\Client\Transformer\ClientTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ClientService
{
    /**
     * @var EntityManagerInterface
     */
    private $_em;

    /**
     * @var ClientTransformer
     */
    private $clientTransformer;

    /**
     * @var FractalService
     */
    private $fractalService;

    public function __construct( EntityManagerInterface $entityManager, ClientTransformer $clientTransformer, FractalService $fractalService )
    {
        $this->_em = $entityManager;
        $this->clientTransformer = $clientTransformer;
        $this->fractalService    = $fractalService;
    }

    /**
     * @return Collection|Item
     */
    public function getClients()
    {
        $clients = $this->_em->getRepository(Client::class)->findAll();
        $resource = new Collection($clients,$this->clientTransformer, 'clients');

        return $resource;
    }

    /**
     * @param $id
     * @return Item
     * @throws EntityNotFoundException
     */
    public function getClientById($id): Item
    {
        $client = $this->_em->getRepository(Client::class)->find($id);

        if ($client) {
            return new Item($client, $this->clientTransformer, 'clients');
        }

        throw new EntityNotFoundException( 'Client not found.' );
    }
}