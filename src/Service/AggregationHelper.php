<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service;

use Brander\Bundle\ElasticaSkeletonBundle\Entity\AutoAggregation;

/**
 * Class AggregationHelper
 */
class AggregationHelper
{

    const RANGE_GT = 'gt';
    const RANGE_GTE = 'gte';
    const RANGE_LTE = 'lte';
    const RANGE_LT = 'lt';

    /**
     * @param string $name
     * @param string $fieldName
     * @return AutoAggregation
     */
    public static function terms($name, $fieldName = '')
    {
        if (!$fieldName) {
            $fieldName = $name;
        }

        return new AutoAggregation(
            $name,
            'terms',
            $fieldName,
            'setAutoAggregation',
            null,
            'buckets',
            $fieldName,
            null
        );
    }

    /**
     * @param string $name
     * @param int    $interval
     * @param string $fieldName
     * @return AutoAggregation
     */
    public static function histogram($name, $interval, $fieldName = '')
    {
        if (!$fieldName) {
            $fieldName = $name;
        }

        return new AutoAggregation(
            $name,
            'histogram',
            $fieldName,
            'setAutoAggregation',
            [
                $name, // name
                $fieldName, //elastica field name
                $interval,
            ],
            'buckets',
            $name,
            'range_basket'
        );
    }


    /**
     * @example "gt:0;lt:101;" - number range
     * @example "gte:5;lte:15;" - number range
     * @param string        $value
     * @param callable|null $formatter
     * @return array|null
     */
    public static function decodeRange($value, $formatter = null)
    {
        $keywords = [
            self::RANGE_GT,
            self::RANGE_GTE,
            self::RANGE_LT,
            self::RANGE_LTE,
        ];
        $result = [];
        $res = [];
        foreach ($keywords as $keyword) {
            $format = $keyword.':\s*(.+?)\s*;';
            preg_match('/'.$format.'/i', $value, $res);
            if ($res && count($res) > 1) {
                if ($formatter) {
                    $result[$keyword] = call_user_func($formatter, $res[1]);
                } else {
                    $result[$keyword] = $res[1];
                }
            }
        }
        if (count($result)) {
            return $result;
        }

        return null;
    }


    /**
     * Format date for elastic search
     * @param string $value
     * @return string
     */
    public static function dateFormatter($value)
    {
        return (new \DateTime($value))->format(\DateTime::ATOM);
    }
}
