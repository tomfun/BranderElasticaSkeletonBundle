<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

use Brander\Bundle\ElasticaSkeletonBundle\Service\Elastica\ElasticaResult;
use Elastica\Aggregation\AbstractAggregation;

/**
 * Class ManualAutoAggregation for create your own project specified aggregations
 */
class ManualAutoAggregation implements AutoAggregationInterface
{
    const SETTER = 'setAutoAggregation';
    /**
     * @var string
     */
    private $serializeType;
    /**
     * @var string
     */
    private $serializeName;
    /**
     * @var AbstractAggregation
     */
    private $aggregation;
    /**
     * @var callable
     */
    private $extractValue;

    /**
     * AutoAggregationAbstract constructor.
     * @param AbstractAggregation $aggregation
     * @param callable            $extractValue  Get usefull data from elastic aggregation result
     * @param string              $serializeType
     * @param string              $serializeName
     */
    public function __construct(AbstractAggregation $aggregation, $extractValue, $serializeType, $serializeName = '')
    {
        $this->serializeType = $serializeType;
        $this->serializeName = $serializeName ? $serializeName : $aggregation->getName();
        $this->aggregation = $aggregation;
        $this->extractValue = $extractValue;
    }

    /**
     * @param ElasticaResult $result
     * @param mixed          $aggregations
     * @throws \Exception
     */
    public function transformToResult(ElasticaResult $result, $aggregations)
    {
        $setter = static::SETTER;
        $value = call_user_func($this->extractValue, $aggregations[$this->getName()], $aggregations);

        $result->$setter($value, $this);
    }

    /**
     * Get Aggregation Name
     * @return string
     */
    public function getName()
    {
        return $this->aggregation->getName();
    }

    /**
     * @return string
     */
    public function getSerializeName()
    {
        return $this->serializeName;
    }

    /**
     * @return string
     */
    public function getSerializeType()
    {
        return $this->serializeType;
    }

    /**
     * @return AbstractAggregation
     */
    public function getAggregationForQuery()
    {
        return $this->aggregation;
    }
}
