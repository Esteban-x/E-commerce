<?php

namespace App\DataFixtures;

use Faker;

use App\Entity\Products;
use Doctrine\Persistence\ObjectManager;
;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($prod = 1; $prod <= 200; $prod++) {
            $product = new Products();
            $product->setName($faker->text(5));
            $product->setDescription($faker->text());
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setPrice($faker->numberBetween(900, 150000));
            $product->setStock($faker->numberBetween(900, 1000));
            $manager->persist($product);
            $this->addReference('prod-' . $prod, $product);
            $category = $this->getReference('cat-' . rand(1, 8));
            $product->setCategories($category);
        }

        $manager->flush();
    }
}
