<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Admin;
use App\Entity\Customer;
use App\Entity\Dates;
use App\Entity\ObjectHS;
use App\Entity\Painting;
use App\Entity\Photo;
use App\Entity\Provider;
use App\Entity\Roofing;
use App\Enum\PaintType;
use App\Enum\RoofMaterial;
use App\Enum\StateObject;
use App\Enum\StatusRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

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

        // Create Customers
        $customers = [];
        for ($i = 0; $i < 15; $i++) {
            $customer = new Customer();
            $customer->setName($faker->firstName());
            $customer->setSurname($faker->lastName());
            $customer->setEmail($faker->unique()->safeEmail());
            $customer->setPhone($faker->phoneNumber());
            $customer->setPlainPassword('password');
            $customer->setRoles(['ROLE_USER']);
            $manager->persist($customer);
            $customers[] = $customer;
        }

        // Create Addresses
        $addresses = [];
        foreach ($customers as $customer) {
            for ($j = 0; $j < rand(1, 3); $j++) {
                $address = new Address();
                $address->setAddress($faker->streetAddress());
                $address->setPostalCode($faker->postcode());
                $address->setCity($faker->city());
                $address->setCustomer($customer);
                $manager->persist($address);
                $addresses[] = $address;
            }
        }

        // Create Requests
        foreach ($customers as $customer) {
            for ($k = 0; $k < rand(1, 3); $k++) {
                $baseRequestType = rand(0, 2); // 0 = ObjectHS, 1 = Roofing, 2 = Painting

                if ($baseRequestType === 0) {
                    // Create ObjectHS Request
                    $objectHS = new ObjectHS();
                    $objectHS->setClient($customer);
                    $objectHS->setCreationDate($faker->dateTimeThisYear());
                    $objectHS->setModificationDate($faker->dateTimeThisYear('+1 month'));
                    $objectHS->setStatus($faker->randomElement([
                        StatusRequest::PENDING,
                        StatusRequest::COMPLETED,
                        StatusRequest::CANCELLED
                    ]));
                    $objectHS->setName($faker->word());
                    $objectHS->setState($faker->randomElement(StateObject::cases()));
                    $objectHS->setAge($faker->numberBetween(1, 10));
                    $objectHS->setDetails($faker->text(200));
                    $manager->persist($objectHS);

                    // Create Photo for ObjectHS
                    for ($m = 0; $m < rand(1, 3); $m++) {
                        $photo = new Photo();
                        $photo->setName($faker->word());
                        $photo->setPhotoPath($faker->imageUrl());
                        $photo->setUploadDate($faker->dateTimeThisYear());
                        $photo->setObjectHS($objectHS);
                        $manager->persist($photo);
                    }
                } elseif ($baseRequestType === 1) {
                    // Create Roofing Request
                    $roofing = new Roofing();
                    $roofing->setClient($customer);
                    $roofing->setCreationDate($faker->dateTimeThisYear());
                    $roofing->setModificationDate($faker->dateTimeThisYear('+1 month'));
                    $roofing->setStatus($faker->randomElement([
                        StatusRequest::PENDING,
                        StatusRequest::COMPLETED,
                        StatusRequest::CANCELLED
                    ]));
                    $roofing->setRoofMaterial($faker->randomElement(RoofMaterial::cases()));
                    $roofing->setNeedInsulation($faker->boolean());
                    $roofing->setDescription($faker->text(300));
                    $manager->persist($roofing);

                    // Create Photo for Roofing
                    for ($m = 0; $m < rand(1, 3); $m++) {
                        $photo = new Photo();
                        $photo->setName($faker->word());
                        $photo->setPhotoPath($faker->imageUrl());
                        $photo->setUploadDate($faker->dateTimeThisYear());
                        $photo->setHomeRepair($roofing);
                        $manager->persist($photo);
                    }
                } else {
                    // Create Painting Request
                    $painting = new Painting();
                    $painting->setClient($customer);
                    $painting->setCreationDate($faker->dateTimeThisYear());
                    $painting->setModificationDate($faker->dateTimeThisYear('+1 month'));
                    $painting->setStatus($faker->randomElement([
                        StatusRequest::PENDING,
                        StatusRequest::COMPLETED,
                        StatusRequest::CANCELLED
                    ]));
                    $painting->setSurfaceArea($faker->numberBetween(20, 500));
                    $painting->setPaintType($faker->randomElement(PaintType::cases()));
                    $painting->setDescription($faker->text(300));
                    $manager->persist($painting);

                    // Create Photo for Painting
                    for ($m = 0; $m < rand(1, 3); $m++) {
                        $photo = new Photo();
                        $photo->setName($faker->word());
                        $photo->setPhotoPath($faker->imageUrl());
                        $photo->setUploadDate($faker->dateTimeThisYear());
                        $photo->setHomeRepair($painting);
                        $manager->persist($photo);
                    }
                }

                // Create Dates for each Request
                for ($l = 0; $l < rand(1, 2); $l++) {
                    $date = new Dates();
                    $date->setDate($faker->dateTimeThisMonth());
                    $date->setRequest($objectHS ?? $roofing ?? $painting);
                    $date->setAddress($faker->randomElement($addresses));
                    $manager->persist($date);
                }
            }
        }

        $manager->flush();

    }

}
