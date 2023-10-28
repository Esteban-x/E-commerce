<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;;

class UsersFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder, private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('admin@example.com');
        $admin->setLastName('Example');
        $admin->setFirstName('Okay');
        $admin->setAddress('12 rue du port');
        $admin->setZipCode('95330');
        $admin->setCity('Paris');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr <= 5; $usr++) {

            $user = new Users();
            $user->setEmail($faker->email);
            $user->setLastName($faker->lastName);
            $user->setFirstName($faker->firstName);
            $user->setAddress($faker->streetAddress);
            $user->setZipCode(str_replace(' ', '', $faker->postcode));
            $user->setCity($faker->city);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );

            $manager->persist($user);
        }

        $manager->flush();
    }
}
