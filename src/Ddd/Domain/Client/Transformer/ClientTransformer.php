<?php


namespace App\Ddd\Domain\Client\Transformer;


use App\Ddd\Domain\Client\Entity\Client;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
    /**
     * @param Client $client
     * @return array
     */
    public function transform(Client $client): array
    {
        return [
            'id'         => $client->getId(),
            'first_name' => $client->getFirstName(),
            'last_name'  => $client->getLastName(),
        ];
    }
}