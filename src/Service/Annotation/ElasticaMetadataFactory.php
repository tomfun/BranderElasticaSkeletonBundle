<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service\Annotation;

use Metadata\MergeableClassMetadata;
use Metadata\MetadataFactory;

/**
 * @class ElasticaMetadataFactory
 * @author Tomfun <tomfun1990@gmail.com>
 */
class ElasticaMetadataFactory extends MetadataFactory
{
    /**
     * @param $class
     * @return QueryMetadataInterface[]|PropertyMetadata[]
     */
    public function getQueryProperties($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        /** @var MergeableClassMetadata $metadata */
        $metadata = $this->getMetadataForClass($class);
        return $metadata->propertyMetadata;
    }

    /**
     * @param $class
     * @return QueryMetadataInterface[]|MethodMetadata[]
     */
    public function getQueryMethods($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        /** @var MergeableClassMetadata $metadata */
        $metadata = $this->getMetadataForClass($class);
        return $metadata->methodMetadata;
    }
}