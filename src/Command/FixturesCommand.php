<?php

namespace App\Command;

use App\Entity\Ad;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Faker\Provider\Address;

class FixturesCommand extends Command
{
    protected static $defaultName = 'app:fixtures';
    protected $em = null;

    public function __construct(EntityManagerInterface $em, ?string $name = null)
    {
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
        $conn->query('TRUNCATE category');
        // réactive la vérification
        $conn->query('SET FOREIGN_KEY_CHECKS = 1');

        $categories = ["Multimédia",
            "Immobilier",
            "Autmobile",
            "Commerces",
            "Locations",
            "Sports",
            "Littérature"
            ];

        // garder en mémoire
        $allCategories = [];
        foreach ($categories as $label){
            $categorie = new Category();
            $categorie->setLabel($label);
            $this->em->persist($categorie);
            $allCategories[] = $categorie;
        }
        $this->em->flush();

        // générer les annonces
        $allAds = [];
        for($i=0; $i<40; $i++){
            $ad = new Ad();
            // alimenter les propriétées
            $ad->setTitle($faker->realText(30));
            $ad->setDescription($faker->realText(5000));
            $ad->setCity($faker->city);
            $ad->setZip(Address::postcode());
            $ad->setPrice($faker->numberBetween(1, 100000));
            $ad->setDateCreated($faker->dateTimeBetween('- 1 week', 'now'));
            $ad->setCategory($faker->randomElement($allCategories));

            $this->em->persist($ad);

            $allAds[] = $ad;
        }
        $this->em->flush();

        $io->success("Done!");

    }
}
