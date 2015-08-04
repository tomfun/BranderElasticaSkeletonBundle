<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service\Annotation;

/**
 * @class  PropertyMetadataInterface
 * @author Tomfun <tomfun1990@gmail.com>
 */
class MethodMetadata extends \Metadata\MethodMetadata implements QueryMetadataInterface
{
    /**
     * @var \Serializable
     */
    protected $filter;
    /**
     * @var \Serializable
     */
    protected $type;

    /**
     * @param string $class
     * @param string $name
     * @param \Serializable $filter
     * @param \Serializable $type
     */
    public function __construct($class, $name, $filter = null, $type = null)
    {
        parent::__construct($class, $name);
        $this->filter = $filter;
        $this->type = $type;
    }

    /**
     * @return \Serializable
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param \Serializable $filter
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @return \Serializable
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \Serializable $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize(
            [
                $this->class,
                $this->name,
                $this->filter,
                $this->type,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function unserialize($str)
    {
        list($this->class, $this->name, $this->filter, $this->type) = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
} 