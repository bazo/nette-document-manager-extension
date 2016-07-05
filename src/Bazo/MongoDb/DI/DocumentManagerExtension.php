<?php

namespace Bazo\MongoDb\DI;


use Bazo\MongoDb\Logger;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use MongoClient;
use Nette\DI\CompilerExtension;
use Nette\DI\Container;

/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class DocumentManagerExtension extends CompilerExtension
{

	/** @var array */
	public $defaults = [
		'documentsDir'					 => '%appDir%/model/documents',
		'proxyDir'						 => '%tempDir%/proxies',
		'hydratorDir'					 => '%tempDir%/hydrators',
		'dbname'						 => 'app',
		'uri'							 => 'mongodb://localhost/app',
		'cachePrefix'					 => 'app',
		'metaDataCacheClass'			 => '\Doctrine\Common\Cache\ArrayCache',
		'autoGenerateHydratorClasses'	 => FALSE,
		'autoGenerateProxyClasses'		 => FALSE,
		'hydratorNamespace'				 => 'Hydrators',
		'proxyNamespace'				 => 'Proxies',
		'cacheAnnotations'				 => TRUE,
		'mongoOptions'					 => ['connect' => TRUE],
		'eventManager'					 => NULL,
		'debug'							 => FALSE,
		'indexAnnotations'				 => TRUE,
		'metaDataCache'					 => NULL,
		'listeners'						 => [],
		'logger'						 => NULL,
		'loggerPrefix'					 => 'MongoDB query: ',
		'filters'						 => [
			'soft-deleteable' => FALSE
		]
	];

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults, TRUE);

		$container->addDefinition($this->prefix('documentManager'))
				->setClass(DocumentManager::class)
				->setFactory('\Bazo\MongoDb\DI\DocumentManagerExtension::createDocumentManager', [
					$config,
					'@container'
				])
		;
	}


	/**
	 *
	 * @param array $config
	 * @return DocumentManager
	 */
	public static function createDocumentManager($config, Container $container)
	{
		$configuration = new Configuration();

		if (is_null($config['eventManager'])) {
			$evm = new EventManager;
		} else {
			$evm = $config['eventManager'];
		}

		$configuration->setProxyDir($config['proxyDir']);
		$configuration->setProxyNamespace($config['proxyNamespace']);

		$configuration->setHydratorDir($config['hydratorDir']);
		$configuration->setHydratorNamespace($config['hydratorNamespace']);

		$configuration->setAutoGenerateHydratorClasses($config['autoGenerateHydratorClasses']);
		$configuration->setAutoGenerateProxyClasses($config['autoGenerateProxyClasses']);

		if (isset($config['metaDataCache'])) {
			$metadataCache = $config['metaDataCache'];
		} else {
			$metadataCache = new $config['metaDataCacheClass'];
			$metadataCache->setNamespace($config['cachePrefix']);
		}

		$configuration->setMetadataCacheImpl($metadataCache);

		AnnotationDriver::registerAnnotationClasses();

		$reader = new AnnotationReader;

		if ($config['cacheAnnotations'] == TRUE) {
			$reader = new CachedReader(
					$reader, $metadataCache, $config['debug']
			);
		}

		if ($config['indexAnnotations'] == TRUE) {
			$reader = new IndexedReader($reader);
		}

		if (class_exists(\Gedmo\DoctrineExtensions::class)) {
			\Gedmo\DoctrineExtensions::registerAnnotations();

			$configuration->addFilter('soft-deleteable', \Gedmo\SoftDeleteable\Filter\ODM\SoftDeleteableFilter::class);

			foreach ($config['listeners'] as $listenerName => $enabled) {
				if ($enabled) {
					$listener = self::configureListener($listenerName, $reader);
					$evm->addEventSubscriber($listener);
				}
			}
		}

		$driverImpl = new AnnotationDriver($reader, $config['documentsDir']);

		$configuration->setMetadataDriverImpl($driverImpl);

		$configuration->setDefaultDB($config['dbname']);

		$logger = new Logger($config['logger'], $config['loggerPrefix']);
		$configuration->setLoggerCallable([$logger, 'logQuery']);

		$mongo		 = new MongoClient($config['uri'], $config['mongoOptions']);
		$connection	 = new Connection($mongo);
		$dm			 = DocumentManager::create($connection, $configuration, $evm);

		foreach ($config['filters'] as $filter => $enabled) {
			if ($enabled) {
				$dm->getFilterCollection()->enable($filter);
			}
		}

		return $dm;
	}


	private static function configureListener($listener, Reader $reader)
	{
		switch ($listener) {
			case 'timestampable':
				$listener = new \Gedmo\Timestampable\TimestampableListener;
				$listener->setAnnotationReader($reader);

				return $listener;
			case 'soft-deleteable':
				$listener = new \Gedmo\SoftDeleteable\SoftDeleteableListener;
				$listener->setAnnotationReader($reader);

				return $listener;
		}
	}


}
