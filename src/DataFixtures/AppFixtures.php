<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Customer;
use App\Entity\Provider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Create 10 Providers
        for ($i = 0; $i < 10; $i++) {
            $provider = new Provider();
            $provider->setName($faker->firstName());
            $provider->setSurname($faker->lastName());
            $provider->setEmail($faker->unique()->safeEmail());
            $provider->setPhone($faker->phoneNumber());
            $provider->setPlainPassword('password');
            $provider->setRoles(['ROLE_PRESTA']);
            $provider->setPriceFDP($faker->randomFloat(2, 10, 100));
            $manager->persist($provider);
        }

        // Create 15 Customers
        for ($i = 0; $i < 15; $i++) {
            $customer = new Customer();
            $customer->setName($faker->firstName());
            $customer->setSurname($faker->lastName());
            $customer->setEmail($faker->unique()->safeEmail());
            $customer->setPhone($faker->phoneNumber());
            $customer->setPlainPassword('password');
            $customer->setRoles(['ROLE_USER']);
            $customer->setCity($faker->city());
            $manager->persist($customer);
        }

        // Create 3 Admins
        for ($i = 0; $i < 3; $i++) {
            $admin = new Admin();
            $admin->setName($faker->firstName());
            $admin->setSurname($faker->lastName());
            $admin->setEmail($faker->unique()->safeEmail());
            $admin->setPhone($faker->phoneNumber());
            $admin->setPlainPassword('adminpassword');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setAccountCreationDate($faker->dateTimeBetween('-1 year', 'now'));
            $manager->persist($admin);
        }

        $manager->flush();
    }
}
