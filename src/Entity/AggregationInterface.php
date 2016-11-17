<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

use Brander\Bundle\ElasticaSkeletonBundle\Service\Elastica\ElasticaResult;

/**
 * class  Aggregation
 * @author Tomfun <tomfun1990@gmail.com>
 */
interface AggregationInterface
{
    /**
     * @param ElasticaResult $result
     * @param mixed          $aggregations
     * @throws \Exception
     */
    public function transformToResult(ElasticaResult $result, $aggregations);

    /**
     * Get Aggregation Name
     * @return string
     */
    public function getName();
}