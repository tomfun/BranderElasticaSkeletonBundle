<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Annotation;

/**
 * Query to find something in array. Something can be also array. Look at links bellow.
 * @class Terms
 * @author Tomfun <tomfun1990@gmail.com>
 * @Annotation
 * @Target({"METHOD", "PROPERTY"})
 * @link http://stackoverflow.com/questions/21933787/elasticsearch-not-returning-results-for-terms-query-against-string-property
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html
 */
final class Terms extends Query
{

}