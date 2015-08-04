<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service\Elastica;

use JMS\Serializer\Annotation as Serializer;

/**
 * @author Tomfun <tomfun1990@gmail.com>
 */
class ElasticaResult
{
    /**
     * @var object[]|null
     */
    protected $rows;
    /** @var int */
    protected $page;
    /** @var int */
    protected $countPage;
    /** @var int */
    protected $countTotal;
    /**
     * @Serializer\Exclude
     * @var array
     */
    protected $extra;

    /**
     * @param object[] $rows Collection of object
     * @param int $page
     * @param int $countPage
     * @param int $countTotal
     * @param array $extra
     */
    public function __construct($rows = [], $page = 1, $countPage = 1, $countTotal = 1, array $extra = [])
    {
        $this->rows = $rows;
        $this->page = $page;
        $this->countPage = $countPage;
        $this->countTotal = $countTotal;
        if (!count($rows)) {
            $this->countTotal = 0;
        }
        $this->extra = $extra;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
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
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountPage()
    {
        return $this->countPage;
    }

    /**
     * @param int $countPage
     * @return $this
     */
    public function setCountPage($countPage)
    {
        $this->countPage = $countPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountTotal()
    {
        return $this->countTotal;
    }

    /**
     * @param int $countTotal
     * @return $this
     */
    public function setCountTotal($countTotal)
    {
        $this->countTotal = $countTotal;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     * @return $this
     */
    public function setExtra(array $extra)
    {
        $this->extra = $extra;
        return $this;
    }
}