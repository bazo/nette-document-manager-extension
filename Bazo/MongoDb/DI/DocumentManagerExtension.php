<?php

namespace Bazo\MongoDb\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

/**
 * Description of DocumentManager
 *
 * @author Martin Bažík
 */
class DocumentManagerExtension extends \Nette\DI\CompilerExtension
{

	/** @var array */
	public $defaults = array(
		'documentsDir' => '%appDir%/models/documents',
		'proxyDir' => '%appDir%/models/proxies',
		'hydratorDir' => '%appDir%/models/hydrators',
		'dbname' => 'app',
		'uri' => 'mongodb://localhost/app',
		'cachePrefix' => 'app',
		'metaDataCacheClass' => '\Doctrine\Common\Cache\ArrayCache',
		'autoGenerateHydratorClasses' => false,
		'autoGenerateProxyClasses' => false,
		'hydratorNamespace' => 'Hydrators',
		'proxyNamespace' => 'Proxies',
		'cacheAnnotations' => true,
		'mongoOptions' => array('connect' => true),
		'eventManager' => null,
		'debug' => false,
		'indexAnnotations' => true,
		'metaDataCache' => null
	);

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults, true);

		$container->addDefinition($this->prefix('documentManager'))
				->setClass('\Doctrine\ODM\MongoDB\DocumentManager')
				->setFactory('\Bazo\MongoDb\DI\DocumentManagerExtension::createDocumentManager', array($config, '@container'))
				->setAutowired(FALSE);

		$container->addDefinition('documentManager')
				->setClass('\Doctrine\ODM\MongoDB\DocumentManager')
				->setFactory('@container::getService', array($this->prefix('documentManager')));
	}

	/**
	 * 
	 * @param array $config
	 * @return \Doctrine\ODM\MongoDB\DocumentManager
	 */
	public static function createDocumentManager($config, \Nette\DI\Container $container)
	{
		$configuration = new Configuration();

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

		\Doctrine\Common\Annotations\AnnotationRegistry::registerFile(VENDORS_DIR . '/doctrine/mongodb-odm/lib/Doctrine/ODM/MongoDB/Mapping/Annotations/DoctrineAnnotations.php');

		$reader = new AnnotationReader;

		if ($config['cacheAnnotations'] == true) {
			$reader = new \Doctrine\Common\Annotations\CachedReader(
					$reader, $metadataCache, $config['debug']
			);
		}

		if ($config['indexAnnotations'] == true) {
			$reader = new \Doctrine\Common\Annotations\IndexedReader($reader);
		}

		$driverImpl = new AnnotationDriver($reader, $config['documentsDir']);

		$configuration->setMetadataDriverImpl($driverImpl);

		$configuration->setDefaultDB($config['dbname']);

		$mongo = new \Mongo($config['uri'], $config['mongoOptions']);
		$connection = new Connection($mongo);
		$dm = \Doctrine\ODM\MongoDB\DocumentManager::create($connection, $configuration, $config['eventManager']);

		return $dm;
	}

}