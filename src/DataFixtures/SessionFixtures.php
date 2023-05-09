<?php

namespace App\DataFixtures;

use App\Factory\SessionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;

class SessionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

		SessionFactory::createMany(10);
        $manager->flush();
    }
}
