<?php

namespace App\Command;

use App\Entity\Ad;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Faker\Provider\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FixturesCommand extends Command
{
    protected static $defaultName = 'app:fixtures';
    protected $em = null;
    protected $encoder = null;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, ?string $name = null)
    {
        $this->encoder = $encoder;
        $this->em = $em;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Load random data in the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->text("Now loading fixtures...");

        $faker = \Faker\Factory::create('fr_FR');

        $conn = $this->em->getConnection();
        //désactive la vérification des clefs étrangère
        $conn->query('SET FOREIGN_KEY_CHECKS = 0');
        $conn->query('TRUNCATE ad');
        $conn->query('TRUNCATE user');
        $conn->query('TRUNCATE category');

        // réactive la vérification
        $conn->query('SET FOREIGN_KEY_CHECKS = 1');

        $io->text("Tables vidées ...");

        $categories = ["Multimédia",
            "Immobilier",
            "Automobile",
            "Commerces",
            "Locations",
            "Sports",
            "Littérature"
            ];

        //ajout catégories
        $io->text("Ajout des catégories ...");

        $allCategories = [];
        foreach ($categories as $label){
            $categorie = new Category();
            $categorie->setLabel($label);
            $this->em->persist($categorie);
            $allCategories[] = $categorie;
        }
        $this->em->flush();

        // générer les users
        // génération de mon user
        $io->text("Création des utilisateurs");
        $io->progressStart(11);

        $allUsers = [];

        $myUser = new User();
        $myUser->setUsername("myutilisateur");
        $myUser->setEmail("myuser@gmail.com");
        $hash = $this->encoder->encodePassword($myUser, "mdpmdpmdp");
        $myUser->setPassword($hash);
        $this->em->persist($myUser);
        $allUsers[] = $myUser;
        $this->em->flush();

        for($i=0; $i<10; $i++){
            $user = new User();
            $user->setUsername($faker->unique()->userName);
            $user->setEmail($faker->unique()->email);
            $password = $user->getUsername();
            $hash = $this->encoder->encodePassword($user, $password);
            $user->setPassword($hash);
            $this->em->persist($user);

            $allUsers[] = $user;
        }
        $this->em->flush();
        $io->progressFinish();


        // générer les annonces
        $io->text("Création des annonces ...");
        $io->progressStart(100);

        for($i=0; $i<100; $i++){
            // progressement de la barre
            $io->progressAdvance(1);

            $ad = new Ad();
            // alimenter les propriétées
            $ad->setTitle($faker->realText(30));
            $ad->setDescription($faker->realText(1500));
            $ad->setCity($faker->city);
            $ad->setZip(Address::postcode());
            $ad->setPrice($faker->numberBetween(1, 100000));
            $ad->setDateCreated($faker->dateTimeBetween('- 1 week', 'now'));
            $ad->setCategory($faker->randomElement($allCategories));
            $ad->setUser($faker->randomElement($allUsers));

            $this->em->persist($ad);

        }
        //arrêt barre de progression
        $io->progressFinish();

        $this->em->flush();

        $io->success("Done!");
    }
}
