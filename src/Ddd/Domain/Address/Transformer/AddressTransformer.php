<?php


namespace App\Ddd\Domain\Address\Transformer;


use App\Ddd\Domain\Address\Entity\Address;
use App\Ddd\Domain\Client\Transformer\ClientTransformer;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    /**
     * @param Address $address
     * @return array
     */
    public function transform(Address $address): array
    {
        return [
            'id'         => $address->getId(),
            'street'     => $address->getStreet(),
            'city'       => $address->getCity(),
            'country'    => $address->getCountry(),
            'is_default' => $address->getIsDefault(),
            'client'     => (new ClientTransformer)->transform($address->getClient())
        ];
    }
}