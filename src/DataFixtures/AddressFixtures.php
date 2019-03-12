<?php

namespace App\DataFixtures;

use App\Ddd\Domain\Address\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AddressFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        foreach (ClientFixtures::$testClients as $testClient) {
            for ($i = 1; $i <= 3; $i++) {
                $address = new Address();

                $address->setClient($this->getReference($testClient));
                $address->setStreet($faker->streetName);
                $address->setCity($faker->city);
                $address->setCountry($faker->country);
                $address->setZipcode($faker->postcode);

                if ($i === 1) {
                    $address->setIsDefault(true);
                }

                $manager->persist($address);
            }
        }
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder(): int
    {
        return 2;
    }
}
