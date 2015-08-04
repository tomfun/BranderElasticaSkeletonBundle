<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Annotation;

/**
 * @class Query
 * @author Tomfun <tomfun1990@gmail.com>
 * @Annotation
 * @Target({"METHOD", "PROPERTY"})
 */
abstract class Query implements \Serializable
{
    /**
     * @var string
     */
    public $index = null;
    /**
     * @var bool|null
     */
    public $must = true;
    /**
     * @var string
     */
    public $callMethod = null;

    /**
     * @return array
     */
    public function serialize()
    {
        return serialize([$this->index, $this->must, $this->callMethod]);
    }

    /**
     * @param string $str
     */
    public function unserialize($str)
    {
        list($this->index, $this->must, $this->callMethod) = unserialize($str);
    }
}