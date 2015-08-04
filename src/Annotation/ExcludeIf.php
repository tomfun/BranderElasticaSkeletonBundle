<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Annotation;

/**
 * @class Test
 * @author Tomfun <tomfun1990@gmail.com>
 * @Annotation
 * @Target({"METHOD", "PROPERTY"})
 */
final class ExcludeIf
{
    const IS_NULL = "null";
    const IS_NOT_NULL = "notNull";
    const IS_TRUE = "true";
    const IS_FALSE = "false";
    const IS_EMPTY = "empty";//for arrays, string etc.

    const IS_NOT_TRUE = "notTrue";
    const IS_NOT_FALSE = "notFalse";
    /**
     * @Enum({"null", "true", "false", "notNull", "empty", "notTrue", "notFalse"})
     */
    public $value;
}