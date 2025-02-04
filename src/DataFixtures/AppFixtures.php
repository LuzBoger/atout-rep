<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Admin;
use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Dates;
use App\Entity\ObjectHS;
use App\Entity\Painting;
use App\Entity\Photo;
use App\Entity\Product;
use App\Entity\Provider;
use App\Entity\Roofing;
use App\Entity\Tag;
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

        // 🔹 Définition des Catégories pour les pièces en vrac
        $categoryNames = [
            'Électronique',       // Résistances, condensateurs...
            'Électromécanique',   // Moteurs, relais, bobines...
            'Connectique',        // Câbles, borniers, prises...
            'Pièces Détachées',   // Vis, joints, coques...
            'Outillage'           // Fer à souder, pinces...
        ];
        $categories = [];

        foreach ($categoryNames as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // 🔹 Définition des Tags
        $tagNames = ['Neuf', 'Occasion', 'Reconditionné', 'Déstockage', 'Lot', 'Unité'];
        $tags = [];

        foreach ($tagNames as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // 🔹 Définition des types de produits et leurs photos associées
        $productData = [
            ["Vis en acier inox", "Pièces Détachées", "product_photo_1.png"],   // Vis
            ["Joint en silicone", "Pièces Détachées", "product_photo_2.png"],   // Joint
            ["Câble de cuivre 2mm", "Connectique", "product_photo_3.png"],      // Câble de cuivre
            ["Résistance 1KΩ", "Électronique", "product_photo_4.png"],          // Résistance
            ["Condensateur 470uF", "Électronique", "product_photo_5.png"],      // Condensateur
        ];

        // Création des Produits pour chaque prestataire
        foreach ($providers as $provider) {
            foreach ($productData as [$productName, $categoryName, $photoFile]) {
                for ($k = 0; $k < rand(2, 5); $k++) { // Chaque prestataire a plusieurs produits
                    $product = new Product();
                    $product->setName($productName);
                    $product->setWeight($faker->randomFloat(1, 0.01, 5)); // Poids ajusté
                    $product->setDescription($faker->sentence(10));
                    $product->setLength($faker->randomFloat(2, 1, 10));
                    $product->setWidth($faker->randomFloat(2, 1, 10));
                    $product->setHeight($faker->randomFloat(2, 1, 10));
                    $product->setStock($faker->numberBetween(10, 1000)); // Vendu en vrac => stock élevé
                    $product->setPrice($faker->randomFloat(2, 0.10, 50));
                    $product->setDeleted(false);
                    $product->setProvider($provider);

                    // Assignation d'une catégorie adaptée
                    foreach ($categories as $category) {
                        if ($category->getName() === $categoryName) {
                            $product->addCategory($category);
                            break;
                        }
                    }

                    // Assignation de Tags aléatoires (1 à 2 max)
                    $assignedTags = $faker->randomElements($tags, rand(1, 2));
                    foreach ($assignedTags as $tag) {
                        $product->addTag($tag);
                    }

                    $manager->persist($product);

                    //  Ajout de Photos adaptées au produit (1 à 5 images du même type)
                    for ($m = 1; $m <= rand(1, 5); $m++) {
                        $photo = new Photo();
                        $photo->setName("photo_{$m}");
                        $photo->setPhotoPath($photoFile);
                        $photo->setUploadDate($faker->dateTimeThisYear());
                        $photo->setProduct($product);
                        $manager->persist($photo);
                    }
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
                $request = null;
                $requestType = ''; // Variable pour le type de requête

                if ($baseRequestType === 0) {
                    $request = new ObjectHS();
                    $requestType = 'object_hs'; // Définir le type
                    $request->setName($faker->word());
                    $request->setState($faker->randomElement(StateObject::cases()));
                    $request->setAge($faker->numberBetween(1, 10));
                    $request->setDetails($faker->text(200));
                } elseif ($baseRequestType === 1) {
                    $request = new Roofing();
                    $requestType = 'roofing'; // Définir le type
                    $request->setRoofMaterial($faker->randomElement(RoofMaterial::cases()));
                    $request->setNeedInsulation($faker->boolean());
                    $request->setDescription($faker->text(300));
                } else {
                    $request = new Painting();
                    $requestType = 'painting'; // Définir le type
                    $request->setSurfaceArea($faker->numberBetween(20, 500));
                    $request->setPaintType($faker->randomElement(PaintType::cases()));
                    $request->setDescription($faker->text(300));
                }

                $request->setClient($customer);
                $request->setCreationDate($faker->dateTimeThisYear());
                $request->setModificationDate($faker->dateTimeBetween('-3 months', 'now'));
                $request->setStatus($faker->randomElement([
                    StatusRequest::PENDING,
                    StatusRequest::COMPLETED,
                    StatusRequest::CANCELLED
                ]));

                $manager->persist($request);

                // Create Dates for each Request
                for ($d = 0; $d < rand(1, 5); $d++) {
                    $date = new Dates();
                    $date->setRequest($request);
                    $date->setDate($faker->dateTimeBetween('-1 month', '+6 months'));

                    // Associer une adresse aléatoire au `Dates`
                    if (!empty($addresses)) {
                        $date->setAddress($faker->randomElement($addresses));
                    }

                    $manager->persist($date);
                }

                // Create Photos
                $photoTypes = $baseRequestType === 0 ? ['front', 'side', 'top'] : ['in', 'out'];
                foreach ($photoTypes as $type) {
                    for ($m = 1; $m <= rand(1, 3); $m++) {
                        $photo = new Photo();
                        $photoName = "{$requestType}_{$type}_{$m}.png"; // Nom avec type inclus
                        $photo->setName($photoName);
                        $photo->setPhotoPath("$photoName"); // Chemin avec type inclus
                        $photo->setUploadDate($faker->dateTimeThisYear());

                        if ($baseRequestType === 0) {
                            $photo->setObjectHS($request);
                        } else {
                            $photo->setHomeRepair($request);
                        }

                        $manager->persist($photo);
                    }
                }
            }
        }

        $manager->flush();
    }
}
