<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service\Elastica;

use Brander\Bundle\ElasticaSkeletonBundle\Entity\RandomElasticaQuery;

/**
 * @class  ElasticaList
 * @author Tomfun <tomfun1990@gmail.com>
 */
abstract class RandomizeElasticaList extends ElasticaList
{
    /**
     * @param \Elastica\Query $elastica
     * @param RandomElasticaQuery $query
     * @throws \Exception
     */
    protected function addOrder(\Elastica\Query $elastica, $query)
    {
        if (!($query instanceof RandomElasticaQuery)) {
            throw new \Exception('Wrong query type');
        }
        if ($query->getIsRandomSort()) {
            if ($query->getRandomPattern()) {
                $sortScript = [
                    'script' => "org.elasticsearch.cluster.routing.operation.hash.djb.DjbHashFunction.DJB_HASH(doc[\"id\"].value + pattern)",
                    'params' => ['pattern' => md5($query->getRandomPattern())],
                ];
            } else {
                $sortScript = [
                    'script' => "Math.random()",
                ];
            }
            $sortScript = array_merge(
                $sortScript,
                [
                    'order' => 'asc',
                    'type'  => 'number',
                    'lang'  => "mvel",
                ]
            );
            $elastica->addSort(
                [
                    '_script' => $sortScript,
                ]
            );
        } else {
            parent::addOrder($elastica, $query);
        }
    }
}