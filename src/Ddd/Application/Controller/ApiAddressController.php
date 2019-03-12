<?php

namespace App\Ddd\Application\Controller;

use App\Ddd\Application\FractalService;
use App\Ddd\Domain\Address\AddressService;
use App\Ddd\Domain\Address\Entity\Address;
use App\Ddd\Domain\Client\Entity\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/api/clients/{client}/addresses", name="api_client_address_")
 * @ParamConverter("client", class="App\Ddd\Domain\Client\Entity\Client", options={"mapping": {"client": "id"}})
 */
class ApiAddressController extends AbstractApiController
{
    /**
     * @var AddressService
     */
    private $addressService;

    public function __construct(FractalService $fractalService, AddressService $addressService)
    {
        parent::__construct($fractalService);
        $this->addressService = $addressService;
    }

    /**
     * @Route("/", name="list", methods={"GET"})
     * @param Client $client
     * @return JsonResponse
     */
    public function list(Client $client): JsonResponse
    {
        $addresses = $this->addressService->getClientAddresses($client);
        return new JsonResponse($this->fractalService->transform($addresses, "/clients/{$client->getId()}"));
    }

    /**
     * @Route("/{id}", name="item", methods={"GET"})
     * @param Client $client
     * @param $id
     *
     * @return JsonResponse
     */
    public function item(Client $client, $id): ?JsonResponse
    {
        try {
            $address = $this->addressService->getAddressById($id);
            return new JsonResponse($this->fractalService->transform($address, "/clients/{$client->getId()}"));
        } catch (\Exception $e) {
            $errorMessage = $this->fractalService->transform($e->getMessage(), false);
            $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR;

            return new JsonResponse($errorMessage, $errorCode);
        }
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     * @param Client $client
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Client $client, Request $request): JsonResponse
    {
        try {
            $this->addressService->create($client, $request->request);
            return new JsonResponse($this->fractalService->transform('Address has been added'), Response::HTTP_OK);
        } catch (\Exception $exception) {
            return new JsonResponse($this->fractalService->transform($exception->getMessage(), false),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{address}", name="update", methods={"POST"})
     * @ParamConverter("address", class="App\Ddd\Domain\Address\Entity\Address", options={"mapping": {"address": "id"}})
     * @param Address $address
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Address $address, Request $request): JsonResponse
    {
        try {
            $this->addressService->update($address, $request->request);
            return new JsonResponse($this->fractalService->transform('Address has been updated.'), Response::HTTP_OK);
        } catch (\Exception $exception) {
            return new JsonResponse($this->fractalService->transform($exception->getMessage(), false),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{address}", name="delete", methods={"DELETE"})
     * @ParamConverter("address", class="App\Ddd\Domain\Address\Entity\Address", options={"mapping": {"address": "id"}})
     * @param Address $address
     * @return JsonResponse
     */
    public function delete(Address $address): JsonResponse
    {
        try {
            $this->addressService->delete($address);
            return new JsonResponse($this->fractalService->transform('Address has been removed successfully.'),
                Response::HTTP_OK);
        } catch (\Exception $exception) {
            return new JsonResponse($this->fractalService->transform($exception->getMessage(), false),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Route("/{address}/default", name="default_state", methods={"POST"})
     * @ParamConverter("address", class="App\Ddd\Domain\Address\Entity\Address", options={"mapping": {"address": "id"}})
     * @param Address $address
     * @return JsonResponse
     */
    public function setDefaultState(Address $address): JsonResponse
    {
        try {
            $this->addressService->setDefaultState($address);
            return new JsonResponse($this->fractalService->transform('Default state is set successfully.'),
                Response::HTTP_OK);
        } catch (\Exception $exception) {
            return new JsonResponse($this->fractalService->transform($exception->getMessage(), false),
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}