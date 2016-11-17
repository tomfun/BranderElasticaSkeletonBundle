<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

/**
 * Interface AutoAggregationInterface
 */
interface AutoAggregationInterface extends AggregationConstructorInterface
{
    /**
     * @return string
     */
    public function getSerializeName();

    /**
     * @return string
     */
    public function getSerializeType();
}
