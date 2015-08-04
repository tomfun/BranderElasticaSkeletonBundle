<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 *
 * TODO
 *
 *
 *
 * @author Tomfun <tomfun1990@gmail.com>
 */
class RangeTime
{
    /**
     * @Serializer\Type("integer")
     * @var int
     */
    protected $max;

    /**
     * @Serializer\Type("integer")
     * @var int
     */
    protected $min;

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     * @return $this
     */
    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param string $index
     * @param float $min
     * @param float $max
     * @return \Elastica\Filter\Range|null
     */
    public static function constructElasticaRange($index, $min, $max)
    {
        if ($min !== false || $max !== false) {
            $range = [];
            if ($min) {
                $range['from'] = (float)$min;
            }
            if ($max) {
                $range['to'] = (float)$max;
            }
            return new \Elastica\Filter\Range($index, $range);
        }
        return null;
    }

    /**
     * @param $index
     * @return \Elastica\Filter\Range|null
     */
    public function toElasticaRange($index)
    {
        return self::constructElasticaRange($index, $this->getMin(), $this->getMax());
    }

}