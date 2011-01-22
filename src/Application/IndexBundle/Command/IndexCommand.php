<?php

namespace Application\IndexBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Application\IndexBundle\Document;

class IndexCommand extends Command{

	protected $dm = null;
	protected $project;
	protected $directory;

    protected function configure()
    {
        parent::configure();

        $this
                ->setName('app:index')
                ->addArgument('project', InputOption::VALUE_REQUIRED, 'Project name', '')
                ->addArgument('directory', InputOption::VALUE_REQUIRED, 'Directory to index', '')



        ;
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return integer 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract class is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
	$this->directory = $input->getArgument('directory');
	$this->project = $input->getArgument('project');

	if(is_dir($this->directory) == false || is_readable($this->directory) == false){
		throw new \Exception("This directory does not exist or is unreadable: " . $this->directory);
	}
	
	$this->dm = $this->container->get('doctrine.odm.mongodb.document_manager');
	$this->scan_directory($this->directory);

    }

	public function scan_directory($dir_name){
		$subdirs = scandir($dir_name);
		foreach($subdirs as $subdir_name){
			if($subdir_name == '.' || $subdir_name == '..' || $subdir_name[0] == '.'){
				continue;
			}
			$subdir_name = $dir_name .  DIRECTORY_SEPARATOR . $subdir_name;
			if(is_dir($subdir_name)){
				$this->scan_directory($subdir_name);
			}else{
				$this->saveScanned($subdir_name);
			}
		}
	}

	public function saveScanned($file_name){
		if(is_readable($file_name)){
	               		$f = new \Application\IndexBundle\Document\File();
        		        $f->source = utf8_encode(file_get_contents($file_name));
				$f->path = $file_name;
				echo substr($f->path, strlen($this->directory)) . "\n";
				$this->dm->persist($f);
				$this->dm->flush();
		}
	}
	
}

