<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

use Brander\Bundle\ElasticaSkeletonBundle\Service\Elastica\ElasticaResult;

/**
 * class  AggregationMetadata
 * @author Tomfun <tomfun1990@gmail.com>
 */
class AggregationMetadata implements AggregationInterface, AggregationMetadataInterface
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $type;
    /** @var string|null */
    protected $setter = null;
    /** @var string|null */
    protected $index = null;
    /** @var mixed|null */
    protected $constructArguments = null;
    /** @var string|null */
    protected $extractValueField = null;


    /**
     * @param string      $name
     * @param string      $type
     * @param string|null $index
     * @param string|null $setter
     * @param mixed|null  $constructArguments
     * @param string|null $extractValueField
     */
    public function __construct($name, $type, $index = null, $setter = null, $constructArguments = null, $extractValueField = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->index = $index;
        $this->setter = $setter;
        $this->constructArguments = $constructArguments;
        $this->extractValueField = $extractValueField;
    }

    /**
     * @return string
     */
    public function getSetter()
    {
        if ($this->setter) {
            return $this->setter;
        }
        $r = 'set';
        foreach (explode('_', $this->name) as $s) {
            $r .= ucfirst($s);
        }

        return $r;
    }

    /**
     * @param string $setter
     * @return $this
     */
    public function setSetter($setter)
    {
        $this->setter = $setter;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        if ($this->index) {
            return $this->index;
        }

        return $this->name;
    }

    /**
     * @param string $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getConstructArguments()
    {
        return $this->constructArguments;
    }

    /**
     * @param mixed|null $constructArguments
     *
     * @return $this
     */
    public function setConstructArguments($constructArguments)
    {
        $this->constructArguments = $constructArguments;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExtractValueField()
    {
        return $this->extractValueField;
    }

    /**
     * @param null|string $extractValueField
     *
     * @return $this
     */
    public function setExtractValueField($extractValueField)
    {
        $this->extractValueField = $extractValueField;

        return $this;
    }

    /**
     * @param ElasticaResult $result
     * @param mixed          $aggregations
     * @throws \Exception
     */
    public function transformToResult(ElasticaResult $result, $aggregations)
    {
        $setter = $this->getSetter();
        if (!method_exists($result, $setter)) {
            throw new \Exception(
                'Specified aggregation ('.$this->getName().') has no setter for data: '.$setter.', in class: '.get_class($result).', can\'t get Aggregations'
            );
        }
        if ($this->getExtractValueField()) {
            $value = $aggregations[$this->getName()][$this->getExtractValueField()];
        } else {
            $value = $aggregations[$this->getName()];
        }

        $result->$setter($value, $this);
    }
}