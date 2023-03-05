<?php

// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Bundle to manage file and directories
        $finder = new Finder();
        $finder->in(__DIR__.'/sql');
        $finder->name('*.sql');
        $finder->files();
        $finder->sortByName();

        $connection = $manager->getConnection();

        foreach ($finder as $file) {
            echo "Importing: {$file->getBasename()} ".PHP_EOL;

            $sql = $file->getContents();
            $result = $connection->executeQuery($sql);  // Execute native SQL
            //var_dump($result);

            $manager->flush();
        }
    }
}
