<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

use Elastica\Aggregation\AbstractAggregation;
use ReflectionClass;

/**
 * Class AutoAggregation
 */
class AutoAggregation extends AggregationMetadata implements AutoAggregationInterface
{
    /**
     * @var null|string
     */
    private $serializeName;
    /**
     * @var null|string
     */
    private $serializeType;

    /**
     * @param string      $name
     * @param string      $type
     * @param string|null $index
     * @param string|null $setter
     * @param mixed|null  $constructArguments
     * @param string|null $extractValueField
     * @param string|null $serializeName
     * @param string|null $serializeType
     */
    public function __construct(
        $name,
        $type,
        $index = null,
        $setter = 'setAutoAggregation',
        $constructArguments = null,
        $extractValueField = null,
        $serializeName = null,
        $serializeType = null
    ) {
        parent::__construct($name, $type, $index, $setter ? $setter : 'setAutoAggregation', $constructArguments, $extractValueField);
        $this->serializeName = $serializeName ? $serializeName : $name;
        $this->serializeType = $serializeType ? $serializeType : $type;
    }

    /**
     * @return AbstractAggregation
     */
    public function getAggregationForQuery()
    {
        $index = $this->getIndex();
        $type = $this->getType();
        $name = $this->getName();
        $type = '\\Elastica\\Aggregation\\'.ucfirst($type);
        /** @var AbstractAggregation $aggregation */
        if ($this->getConstructArguments()) {
            $reflect = new ReflectionClass($type);
            $aggregation = $reflect->newInstanceArgs($this->getConstructArguments());
        } else {
            $aggregation = new $type($name);
        }
        $aggregation->setField($index);

        return $aggregation;
    }

    /**
     * @return null|string
     */
    public function getSerializeName()
    {
        return $this->serializeName;
    }

    /**
     * @param null|string $serializeName
     */
    public function setSerializeName($serializeName)
    {
        $this->serializeName = $serializeName;
    }

    /**
     * @return null|string
     */
    public function getSerializeType()
    {
        return $this->serializeType;
    }

    /**
     * @param null|string $serializeType
     */
    public function setSerializeType($serializeType)
    {
        $this->serializeType = $serializeType;
    }
}
