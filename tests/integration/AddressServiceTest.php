<?php

namespace App\Tests\Unit;


use App\Ddd\Domain\Address\AddressService;
use App\Ddd\Domain\Address\Entity\Address;
use App\Ddd\Domain\Client\Entity\Client;
use App\Tests\UnitTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Exception\ValidatorException;

class AddressServiceTest extends UnitTest
{

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testAddAddress(): void
    {
        $addressService = self::$container->get('ddd.address.service');

        $client = $this->entityManager->getRepository(Client::class)->findLast();

        $data = $this->fakeData($client);
        $request = new Request([], $data);

        $addressService->create($client, $request->request);

        /** @var Address $address */
        $address = $this->entityManager->getRepository(Address::class)->findLast();

        $this->assertEquals($address->getStreet(),  $data['street']);
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testRemoveDefaultAddress(): void
    {   /** @var AddressService $addressService */
        $addressService = self::$container->get('ddd.address.service');

        /** @var Address $address */
        $address = $this->entityManager->getRepository(Address::class)->findOneDefault();

        try {
            $addressService->delete($address);
            $this->fail('Expected Exception has not been raised.');
        } catch (\Exception $ex) {
            $this->assertEquals($ex->getMessage(), 'You can\'t delete the default address.');
        }
    }

    /**
     * @param Client $client
     * @return array
     */
    private function fakeData(Client $client): array
    {
        $faker = \Faker\Factory::create();
        return [
            'client' => $client->getId(),
            'street' => $faker->streetName,
            'city' => $faker->city,
            'country' => $faker->country,
            'zip_code' => $faker->postcode,
        ];
    }
}
