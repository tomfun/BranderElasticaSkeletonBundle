<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service\Annotation;

use Brander\Bundle\ElasticaSkeletonBundle\Annotation;
use Brander\Bundle\ElasticaSkeletonBundle\Annotation\ExcludeIf;
use Doctrine\Common\Annotations\CachedReader;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;

/**
 * @class  AnnotationDriver
 * @author Tomfun <tomfun1990@gmail.com>
 */
class AnnotationDriver implements DriverInterface
{
    const FILTER_CLASS = 'Brander\\Bundle\\ElasticaSkeletonBundle\\Annotation\\ExcludeIf';

    /** @var CachedReader */
    private $reader;

    /**
     * @param CachedReader $reader
     */
    public function __construct(CachedReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return array
     */
    protected function getPropertyClasses()
    {
        $prefix = 'Brander\\Bundle\\ElasticaSkeletonBundle\\Annotation\\';
        $result = [];
        $classes = [
            'Term',
            'Terms',
            'QueryString',
        ];
        foreach ($classes as $class) {
            $result[] = $prefix . $class;
        }
        return $result;
    }

    /**
     * @return array
     */
    protected function getMethodClasses()
    {
        $prefix = 'Brander\\Bundle\\ElasticaSkeletonBundle\\Annotation\\';
        $result = [];
        $classes = [
            'Term',
            'Terms',
            'QueryString',
            'Raw',
        ];
        foreach ($classes as $class) {
            $result[] = $prefix . $class;
        }
        return $result;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());

        foreach ($class->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation(
                $property,
                self::FILTER_CLASS
            );
            $metadata = new PropertyMetadata(
                $class->getName(),
                $property->getName()
            );
            $foundSome = false;
            if ($annotation && $annotation instanceof ExcludeIf) {
                $metadata->setFilter($annotation->value);
                $foundSome = true;
            }
            foreach ($this->getPropertyClasses() as $annotationClass) {
                $annotation = $this->reader->getPropertyAnnotation(
                    $property,
                    $annotationClass
                );
                if ($annotation instanceof $annotationClass) {
                    $metadata->setType($annotation);
                    if ($annotation instanceof Annotation\Query && !$annotation->index) {
                        $annotation->index = $property->name;
                    }
                    $foundSome = true;
                }
            }
            if ($foundSome) {
                $classMetadata->addPropertyMetadata($metadata);
            }
        }

        foreach ($class->getMethods() as $method) {
            $annotation = $this->reader->getMethodAnnotation(
                $method,
                self::FILTER_CLASS
            );
            $metadata = new MethodMetadata(
                $class->getName(),
                $method->getName()
            );
            $foundSome = false;
            if ($annotation && $annotation instanceof ExcludeIf) {
                $metadata->setFilter($annotation->value);
                $foundSome = true;
            }
            foreach ($this->getMethodClasses() as $annotationClass) {
                $annotation = $this->reader->getMethodAnnotation(
                    $method,
                    $annotationClass
                );
                if ($annotation instanceof $annotationClass) {
                    if ($annotation instanceof Annotation\Query && !$annotation->index) {
                        $index = $method->name;
                        if (substr($index, 0, 3) === 'get') {
                            $index = substr($index, 3);
                            $index = lcfirst($index);
                        }
                        $annotation->index = $index;
                    }
                    $metadata->setType($annotation);
                    $foundSome = true;
                }
            }
            if ($foundSome) {
                $classMetadata->addMethodMetadata($metadata);
            }
        }

        return $classMetadata;
    }
}