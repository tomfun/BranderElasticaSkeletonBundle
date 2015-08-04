<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Annotation;

/**
 * @class QueryString
 * @author Tomfun <tomfun1990@gmail.com>
 * @Annotation
 * @Target({"METHOD", "PROPERTY"})
 */
final class QueryString extends Query
{
    public $withoutIndex = true;

    /**
     * @return array
     */
    public function serialize()
    {
        return serialize([$this->index, $this->must, $this->callMethod, $this->withoutIndex]);
    }

    /**
     * @param string $str
     */
    public function unserialize($str)
    {
        list($this->index, $this->must, $this->callMethod, $this->withoutIndex) = unserialize($str);
    }
}