<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

/**
 * class  AggregationMetadataInterface
 * @author Tomfun <tomfun1990@gmail.com>
 */
interface AggregationMetadataInterface extends AggregationInterface
{
    /**
     * Field from elastic store
     * @return string
     */
    public function getIndex();

    /**
     * Type of aggregation
     * @return string
     */
    public function getType();

    /**
     * @return mixed|null
     */
    public function getConstructArguments();
}