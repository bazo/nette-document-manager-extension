<?php
namespace Bazo\MongoDb;

use Psr\Log\LoggerInterface;
/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class Logger
{

	private $logger;
	private $prefix;
	private $batchInsertTreshold;

	public function __construct(LoggerInterface $logger = NULL, $prefix = 'MongoDB query: ')
	{
		$this->logger	 = $logger;
		$this->prefix	 = $prefix;
	}


	public function setBatchInsertThreshold($batchInsertTreshold)
	{
		$this->batchInsertTreshold = $batchInsertTreshold;
	}


	public function logQuery(array $query)
	{
		dump($query);exit;
		if ($this->logger === NULL) {
			return;
		}
		if (isset($query['batchInsert']) && NULL !== $this->batchInsertTreshold && $this->batchInsertTreshold <= $query['num']) {
			$query['data'] = '**' . $query['num'] . ' item(s)**';
		}
		array_walk_recursive($query, function(&$value, $key) {
			if ($value instanceof \MongoBinData) {
				$value = base64_encode($value->bin);
				return;
			}
			if (is_float($value) && is_infinite($value)) {
				$value = ($value < 0 ? '-' : '') . 'Infinity';
				return;
			}
			if (is_float($value) && is_nan($value)) {
				$value = 'NaN';
				return;
			}
		});
		$this->logger->debug($this->prefix . json_encode($query));
	}


}
