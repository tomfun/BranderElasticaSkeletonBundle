<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service\Elastica;

use Brander\Bundle\ElasticaSkeletonBundle\Entity\Aggregation;
use Brander\Bundle\ElasticaSkeletonBundle\Entity\AggregationInterface;
use Elastica\Query\BoolQuery;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Tomfun <tomfun1990@gmail.com>
 */
abstract class ElasticaQuery
{
    const DEFAULT_PAGE_GROUP = 10;

    /**
     * @Serializer\Exclude
     * @var int
     */
    protected $pageGroup;
    /**
     * @Serializer\Accessor(getter="getPage", setter="setPage")
     * @Serializer\Type("integer")
     * @var int
     */
    protected $page = 1;
    /**
     * @Serializer\Accessor(getter="getOrderRaw", setter="setOrder")
     * @Serializer\Type("string")
     * @var array
     */
    protected $order = null;

    /**
     * @return array|AggregationInterface[]
     */
    abstract public function getAggregations();

    /**
     * @return array
     */
    abstract public function getFilters();

    /**
     * @param BoolQuery $query
     */
    abstract public function addQueries(BoolQuery $query);

    /**
     * @return void
     */
    abstract public function prettify();

    /**
     * @return int
     */
    public function getPageGroup()
    {
        return $this->pageGroup;
    }

    /**
     * @param int $pageGroup
     * @return $this
     */
    public function setPageGroup($pageGroup)
    {
        $this->pageGroup = (int)$pageGroup;
        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = (int)$page;
        return $this;
    }

    /**
     * @example setOrder('id desc');
     * @param string $order
     * @throws \Exception
     * @return $this
     */
    public function setOrder($order)
    {
        if ($order) {
            $regex = '!^([a-zA-Z0-9_\.]+)(\ (asc|desc))?$!';
            if (!preg_match($regex, $order)) {
                throw new \Exception('Wrong order parameter: ' . $order);
            }
            $this->order = explode(' ', $order);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getOrderRaw()
    {
        return $this->order ? implode(' ', $this->order) : $this->order;
    }
}