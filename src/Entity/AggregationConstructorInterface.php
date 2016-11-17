<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

use Elastica\Aggregation\AbstractAggregation;

/**
 * class  AggregationMetadataInterface
 * @author Tomfun <tomfun1990@gmail.com>
 */
interface AggregationConstructorInterface extends AggregationInterface
{
    /**
     * @return AbstractAggregation
     */
    public function getAggregationForQuery();
}