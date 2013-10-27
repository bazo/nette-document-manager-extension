<?php

namespace Bazo\MongoDb\DI;

/**
 * Console service.
 *
 * @author	Martin Bažík
 */
class DoctrineODMCommandsExtension extends \Nette\DI\CompilerExtension
{

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		// console commands - ODM
		$container->addDefinition($this->prefix('consoleCommandODMClearMetadataCache'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand')
				->addTag('console.command');

		$container->addDefinition($this->prefix('consoleCommandODMCreateSchema'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand')
				->addTag('console.command');

		$container->addDefinition($this->prefix('consoleCommandODMDropSchema'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\DropCommand')
				->addTag('console.command');

		$container->addDefinition($this->prefix('consoleCommandODMGenerateDocuments'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateDocumentsCommand')
				->addTag('console.command');

		$container->addDefinition($this->prefix('consoleCommandODMGenerateHydrators'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateHydratorsCommand')
				->addTag('console.command');

		$container->addDefinition($this->prefix('consoleCommandODMGenerateProxies'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand')
				->addTag('console.command');

		$container->addDefinition($this->prefix('consoleCommandODMGenerateRepositories'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateRepositoriesCommand')
				->addTag('console.command');

		$container->addDefinition($this->prefix('consoleCommandODMQuery'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\QueryCommand')
				->addTag('console.command');
	}


}

