<?php 

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use GuzzleHttp\ClientInterface;
use ZipArchive;


class NewCommand extends Command{

	private $clientInterface;

	public function __construct(ClientInterface $clientInterface)
	{
	
		$this->clientInterface = $clientInterface;

		parent::__construct();
	
	}

	public function configure()
	{

		$this->setName('new')
			 ->setDescription('Creates a new laravel project')
			 ->addArgument('name',InputArgument::REQUIRED);

	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("<info> Creating application... </info>");
		// asserts that the folder doesn't already exists
		$directory = getcwd() . "/" . $input->getArgument('name');
		$this->assertApplicationDoesNotExist($directory, $output);


		// download nightly version of laravel and extracts ZIP File
		$this->download($zipFile = $this->makeFileName())
			 ->extract($zipFile, $directory)
			 ->cleanUp($zipFile);


		// alert user

	    $output->writeln("<comment> Application " . $input->getArgument('name') . " ready! </comment>");
	}

	private function assertApplicationDoesNotExist($directory, OutputInterface $output)
	{

		if(is_dir($directory))
		{

			$output->writeln('<error>Application ' . $directory .  ' already exists</error>');
			exit(1);
		}
	}

	private function makeFileName()
	{

		return getcwd() . "/laravel_" . md5(time().uniqid() . ".zip");
	}

	private function download($zipFile)
	{

		$response = $this->clientInterface->get("http://cabinet.laravel.com/latest.zip")->getBody();
		file_put_contents($zipFile, $response);

		return $this;

	}

	private function extract($zipFile,$directory)
	{

		$archive = new ZipArchive;

		$archive->open($zipFile);

		$archive->extractTo($directory);

		$archive->close();

		return $this;
	}

	private function cleanUp($zipFile)
	{

		@chmod($zipFile, 0777); 
		@unlink($zipFile);

		return $this;
	}
}