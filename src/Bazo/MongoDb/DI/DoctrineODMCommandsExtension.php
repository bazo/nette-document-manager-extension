<?php

namespace Bazo\MongoDb\DI;

use Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateHydratorsCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GeneratePersistentCollectionsCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\QueryCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\DropCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\ShardCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\UpdateCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\ValidateCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use Nette\DI\CompilerExtension;

/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class DoctrineODMCommandsExtension extends CompilerExtension
{

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$container
				->addDefinition($this->prefix('consoleCommandODMClearMetadataCache'))
				->setClass(MetadataCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMCreateSchema'))
				->setClass(CreateCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMDropSchema'))
				->setClass(DropCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMUpdateSchema'))
				->setClass(UpdateCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMShardSchema'))
				->setClass(ShardCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMValidateSchema'))
				->setClass(ValidateCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMGenerateHydrators'))
				->setClass(GenerateHydratorsCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMGeneratePersistentCollections'))
				->setClass(GeneratePersistentCollectionsCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMGenerateProxies'))
				->setClass(GenerateProxiesCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;

		$container
				->addDefinition($this->prefix('consoleCommandODMQuery'))
				->setClass(QueryCommand::class)
				->addTag('console.command')
				->addTag('kdyby.console.command')
		;
		
		$container
				->addDefinition($this->prefix('consoleHelperODMDocumentManager'))
				->setClass(DocumentManagerHelper::class)
				->addTag('console.helper')
				->addTag('kdyby.console.helper')
		;
	}

}
