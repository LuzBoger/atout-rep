<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Admin;
use App\Entity\Customer;
use App\Entity\Dates;
use App\Entity\ObjectHS;
use App\Entity\Painting;
use App\Entity\Photo;
use App\Entity\Product;
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

        // Create Providers
        $providers = [];
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
            $providers[] = $provider;
        }

        // Create Products
        foreach ($providers as $provider) {
            for ($k = 0; $k < rand(5, 15); $k++) {
                $product = new Product();
                $product->setName($faker->words(3, true));
                $product->setWeight($faker->randomFloat(1, 0.1, 10));
                $product->setDescription($faker->text(200));
                $product->setLength($faker->randomFloat(2, 10, 100));
                $product->setWidth($faker->randomFloat(2, 10, 100));
                $product->setHeight($faker->randomFloat(2, 10, 100));
                $product->setStock($faker->numberBetween(0, 100));
                $product->setPrice($faker->randomFloat(2, 5, 500));
                $product->setDeleted(false);
                $product->setProvider($provider);
                $manager->persist($product);

                // Add Photos for Products
                for ($m = 0; $m < rand(1, 3); $m++) {
                    $photo = new Photo();
                    $photo->setName($faker->word());
                    $photo->setPhotoPath("https://picsum.photos/1920/1080?random=" . rand(1, 10000));
                    $photo->setUploadDate($faker->dateTimeThisYear());
                    $photo->setProduct($product); // Association avec le produit
                    $manager->persist($photo);
                }
            }
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

                    // Create Photos for ObjectHS
                    for ($m = 0; $m < rand(1, 3); $m++) {
                        $photo = new Photo();
                        $photo->setName($faker->word());
                        $photo->setPhotoPath("https://picsum.photos/1920/1080?random=" . rand(1, 10000));
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

                    // Create Photos for Roofing
                    for ($m = 0; $m < rand(1, 3); $m++) {
                        $photo = new Photo();
                        $photo->setName($faker->word());
                        $photo->setPhotoPath("https://picsum.photos/1920/1080?random=" . rand(1, 10000));
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

                    // Create Photos for Painting
                    for ($m = 0; $m < rand(1, 3); $m++) {
                        $photo = new Photo();
                        $photo->setName($faker->word());
                        $photo->setPhotoPath("https://picsum.photos/1920/1080?random=" . rand(1, 10000));
                        $photo->setUploadDate($faker->dateTimeThisYear());
                        $photo->setHomeRepair($painting);
                        $manager->persist($photo);
                    }
                }
            }
        }


        $manager->flush();

    }

}
