<?php

namespace App\Ddd\Application\Controller;

use App\Ddd\Application\FractalService;
use App\Ddd\Domain\Client\ClientService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/clients", name="api_clients_")
 */
class ApiClientController extends AbstractApiController
{
    /**
     * @var ClientService
     */
    private $clientService;

    public function __construct ( FractalService $fractalService, ClientService $clientService )
    {
        parent::__construct ( $fractalService );
        $this->clientService = $clientService;
    }

    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list ()
    {
        $clients = $this->clientService->getClients ();
        return new JsonResponse( $this->fractalService->transform ( $clients ) );
    }

    /**
     * @Route("/{id}", name="item", methods={"GET"})
     * @param $id
     *
     * @return JsonResponse
     */
    public function item ( $id )
    {
        try {
            $client = $this->clientService->getClientById ( $id );
            return new JsonResponse( $this->fractalService->transform ( $client ) );
        } catch (\Exception $e) {
            $errorMessage = $this->fractalService->transform ( $e->getMessage (), false );
            $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR;

            return new JsonResponse( $errorMessage, $errorCode );
        }
    }
}