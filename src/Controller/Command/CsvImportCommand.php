<?php

namespace App\Command;

use App\Entity\Product;
use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CsvImportCommand extends Command
{
	/*
	 * @var EntityManagerInterface
	 */
	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		parent::__construct();
		$this->em = $em;
	}
	protected function configure()
	{
		$this
			->setName('csv:import')
			->setDescription('Imports a csv file');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
		$io->title('Preparation for import...');
		$reader = Reader::createFromPath('%kernel.root_dir%/../public/pasta.csv');
		$reader->setDelimiter(';');
		$results = $reader->fetchAssoc();

		foreach ($results as $row) {
			$product = (new Product())
						->setName($row['name'])
						->setRegion($row['region'])
						->setPrice($row['price'])
						->setBasePrice($row['basePrice'])
						->setManufacturer($row['manufacturer'])
						->setProperties($row['properties']);
			$this->em->persist($product);
		}

		$this->em->flush();
		$io->success('Import was successful');

		return iterator_count($results);
	}
}