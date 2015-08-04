<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service\Annotation;

use Brander\Bundle\ElasticaSkeletonBundle\Annotation\Query;

/**
 * QueryMetadataInterface.
 * @author Tomfun <tomfun1990@gmail.com>
 */
interface QueryMetadataInterface
{
    /**
     * @see ExcludeIf
     * @return string
     */
    public function getFilter();

    /**
     * @return Query
     */
    public function getType();
}