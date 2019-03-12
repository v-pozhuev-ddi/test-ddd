<?php

namespace App\Ddd\Domain\Address;

use App\Ddd\Domain\Address\Entity\Address;
use App\Ddd\Domain\Address\Transformer\AddressTransformer;
use App\Ddd\Domain\Client\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddressService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AddressTransformer
     */
    private $addressTransformer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public const MIN_COUNT = 1;

    public const MAX_COUNT = 3;

    /**
     * AddressService constructor.
     * @param EntityManagerInterface $entityManager
     * @param AddressTransformer $addressTransformer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AddressTransformer $addressTransformer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->addressTransformer = $addressTransformer;
        $this->validator = $validator;
    }

    /**
     * @param Client $client
     * @return Collection|Item
     */
    public function getClientAddresses(Client $client)
    {
        $addresses = $this->entityManager->getRepository(Address::class)->findBy(['client' => $client]);
        $resource = new Collection($addresses, $this->addressTransformer, 'addresses');

        return $resource;
    }

    /**
     * @param $id
     * @return Item
     * @throws EntityNotFoundException
     */
    public function getAddressById($id): Item
    {
        $address = $this->entityManager->getRepository(Address::class)->find($id);

        if ($address) {
            return new Item($address, $this->addressTransformer, 'addresses');
        }

        throw new EntityNotFoundException('Address not found');
    }

    /**
     * @param Client $client
     * @param ParameterBag $parameters
     */
    public function create(Client $client, ParameterBag $parameters): void
    {
        $this->validateMaxCount($client);

        $address = $this->fillAddress(new Address(), $parameters);
        $address->setClient($client);

        $defaultAddress = $this->entityManager->getRepository(Address::class)->getDefaultByClient($client);
        if (!$defaultAddress) {
            $address->setIsDefault(true);
        }

        $errors = $this->validator->validate($address);

        if (count($errors) > 0) {
            throw new ValidatorException('Address has not added. Validation error.');
        }

        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }

    /**
     * @param Address $address
     * @param ParameterBag $parameters
     */
    public function update(Address $address, ParameterBag $parameters): void
    {
        $address = $this->fillAddress($address, $parameters);

        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }

    /**
     * @param Address $address
     */
    public function delete(Address $address): void
    {
        $this->validateMinCount($address->getClient());

        if ($address->getIsDefault()) {
            throw new ValidatorException('You can\'t delete the default address.');
        }
        $this->entityManager->remove($address);
        $this->entityManager->flush();
    }

    /**
     * @param Address $address
     */
    public function setDefaultState(Address $address): void
    {
        $client = $address->getClient();
        $defaultAddress = $this->entityManager->getRepository(Address::class)->getDefaultByClient($client);
        if ($defaultAddress) {
            $defaultAddress->setIsDefault(false);
            $this->entityManager->persist($defaultAddress);
        }

        $address->setIsDefault(true);
        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }

    /**
     * @param Address $address
     * @param ParameterBag $parameters
     * @return Address
     */
    private function fillAddress(Address $address, ParameterBag $parameters): Address
    {
        $address->setStreet($parameters->get('street', $address->getStreet()));
        $address->setCity($parameters->get('city', $address->getCity()));
        $address->setCountry($parameters->get('country', $address->getCountry()));
        $address->setZipcode($parameters->get('zip_code', $address->getZipcode()));
        $address->setIsDefault($parameters->getBoolean('is_default', $address->getIsDefault()));

        return $address;
    }

    private function validateMaxCount(Client $client):void
    {
        $count = $this->entityManager->getRepository(Address::class)->countByClient($client);

        if ($count >= self::MAX_COUNT) {
            throw new ValidatorException('Maximum address limit exceeded');
        }
    }

    private function validateMinCount(Client $client):void
    {
        $count = $this->entityManager->getRepository(Address::class)->countByClient($client);

        if ($count <= self::MIN_COUNT) {
            throw new ValidatorException('You cannot delete the last address.');
        }
    }
}