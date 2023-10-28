<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Images;
use App\DataFixtures\ProductsFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($img = 1; $img <= 100; $img++) {
            $image = new Images();
            $image->setName($faker->imageUrl(640, 480, 'animals', true));
            $product = $this->getReference('prod-' . rand(1, 10));
            $image->setProducts($product);
            $manager->persist($image);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductsFixtures::class
        ];
    }
}
