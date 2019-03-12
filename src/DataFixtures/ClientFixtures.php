<?php

namespace App\DataFixtures;

use App\Ddd\Domain\Client\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ClientFixtures extends Fixture implements OrderedFixtureInterface
{

    public static $testClients = [
        'testClient1',
        'testClient2'
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        foreach (self::$testClients as $testClient) {
            $client = new Client();
            $client->setFirstName($faker->firstName);
            $client->setLastName($faker->lastName);
            $this->addReference($testClient, $client);

            $manager->persist($client);
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
        return 1;
    }

}
