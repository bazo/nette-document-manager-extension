<?php

namespace Bazo\MongoDb\DI;

use Nette\Config\Configurator;
use Nette\DI\ContainerBuilder;

/**
 * Console service.
 *
 * @author	Martin Bažík
 */
class DoctrineODMCommandsExtension extends \Nette\DI\CompilerExtension
{

	/** @var bool */
	private $skipInitDefaultParameters;

	/**
	 * @param bool
	 */
	public function __construct($skipInitDefaultParameters = FALSE)
	{
		$this->skipInitDefaultParameters = $skipInitDefaultParameters;
	}

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		if (!$this->skipInitDefaultParameters) {
			$this->initDefaultParameters($container);
		}

		// console commands - ODM
		$container->addDefinition($this->prefix('consoleCommandODMClearMetadataCache'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand')
				->addTag('consoleCommand');

		$container->addDefinition($this->prefix('consoleCommandODMCreateSchema'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand')
				->addTag('consoleCommand');

		$container->addDefinition($this->prefix('consoleCommandODMDropSchema'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\DropCommand')
				->addTag('consoleCommand');

		$container->addDefinition($this->prefix('consoleCommandODMGenerateDocuments'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateDocumentsCommand')
				->addTag('consoleCommand');

		$container->addDefinition($this->prefix('consoleCommandODMGenerateHydrators'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateHydratorsCommand')
				->addTag('consoleCommand');

		$container->addDefinition($this->prefix('consoleCommandODMGenerateProxies'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand')
				->addTag('consoleCommand');

		$container->addDefinition($this->prefix('consoleCommandODMGenerateRepositories'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateRepositoriesCommand')
				->addTag('consoleCommand');

		$container->addDefinition($this->prefix('consoleCommandODMQuery'))
				->setClass('Doctrine\ODM\MongoDB\Tools\Console\Command\QueryCommand')
				->addTag('consoleCommand');
	}

	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$class = get_called_class();
		$configurator->onCompile[] = function(Configurator $configurator, \Nette\Config\Compiler $compiler) use($class) {
			$compiler->addExtension('doctrineODMCommands', new $class);
		};
	}

}