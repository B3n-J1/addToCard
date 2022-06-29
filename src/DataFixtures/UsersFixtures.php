<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UsersFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder, private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail(('admin@admin.com'))
            ->setLastname('Admin')
            ->setFirstname('admin')
            ->setAddress("Rue de l'inconnu")
            ->setZipcode('12345')
            ->setCity('Atlantide')
            ->setPassword(
                $this->passwordEncoder->hashPassword($admin, 'admin')
            )
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        require_once 'vendor/autoload.php';

        $faker = Faker\Factory::create('fr_FR');

        for ($usr=1; $usr <= 5 ; $usr++) { 
            $user = new Users();
            $user->setEmail($faker->email)
                ->setLastname($faker->lastname)
                ->setFirstname($faker->firstname)
                ->setAddress($faker->streetAddress)
                ->setZipcode(str_replace(' ','',$faker->postcode))
                ->setCity($faker->city)
                ->setPassword(
                    $this->passwordEncoder->hashPassword($admin, 'secretPassword')
                );

            $manager->persist($user);
        }



        $manager->flush();
    }
}
