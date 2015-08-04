<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Entity;

use Brander\Bundle\ElasticaSkeletonBundle\Annotation as Elastica;
use Brander\Bundle\ElasticaSkeletonBundle\Service\Elastica\ElasticaQuery;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Tomfun <tomfun1990@gmail.com>
 */
abstract class RandomElasticaQuery extends ElasticaQuery
{
    const RANDOM_PAGE_GROUP = 5;

    /**
     * @Elastica\ExcludeIf(Elastica\ExcludeIf::IS_EMPTY)
     * @Elastica\QueryString()
     * @Serializer\Type("string")
     * @var string|null
     */
    protected $search = null;

    /**
     * @Serializer\Type("boolean")
     * @var bool|null
     */
    protected $isRandomSort = null;

    /**
     * @Serializer\Type("integer")
     * @var string|null
     */
    protected $randomPattern = null;

    /**
     * @return bool|null
     */
    public function getIsRandomSort()
    {
        return $this->isRandomSort;
    }

    /**
     * @param bool|null $isRandomSort
     * @return $this
     */
    public function setIsRandomSort($isRandomSort)
    {
        $this->isRandomSort = $isRandomSort;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRandomPattern()
    {
        return $this->randomPattern;
    }

    /**
     * @param null|string $randomPattern
     * @return $this
     */
    public function setRandomPattern($randomPattern)
    {
        $this->randomPattern = $randomPattern;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param null|string $search
     * @return $this
     */
    public function setSearch($search)
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function prettify()
    {
        if ($this->isRandomSort) {
            $this->setPageGroup(static::RANDOM_PAGE_GROUP);
        } elseif (!$this->pageGroup) {
            $this->pageGroup = static::DEFAULT_PAGE_GROUP;
        }
    }
}
