<?php
namespace Brander\Bundle\ElasticaSkeletonBundle\Service\Elastica;

use Brander\Bundle\ElasticaSkeletonBundle\Annotation\ExcludeIf;
use Brander\Bundle\ElasticaSkeletonBundle\Annotation\QueryString;
use Brander\Bundle\ElasticaSkeletonBundle\Annotation\Raw;
use Brander\Bundle\ElasticaSkeletonBundle\Service\Annotation\ElasticaMetadataFactory;
use Elastica\Aggregation\AbstractSimpleAggregation;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use FOS\ElasticaBundle\Paginator\RawPaginatorAdapter;
use Pagerfanta\Pagerfanta;
use ReflectionClass;

/**
 * @class  ElasticaList
 * @author Tomfun <tomfun1990@gmail.com>
 */
abstract class ElasticaList
{
    /** @var PaginatedFinderInterface */
    protected $finder;
    /** @var  ElasticaMetadataFactory */
    protected $metadataFactory;

    /**
     * @param PaginatedFinderInterface $finder
     */
    public function __construct(PaginatedFinderInterface $finder = null)
    {
        $this->finder = $finder;
    }

    /**
     * @param ElasticaMetadataFactory $factory
     */
    public function setMetadataFactory(ElasticaMetadataFactory $factory)
    {
        $this->metadataFactory = $factory;
    }

    /**
     * @param mixed $value
     * @param string $filter
     * @return bool
     * @throws \Exception
     */
    protected function filterQuery($value, $filter)
    {
        if ($filter === null) {
            return true;
        }
        switch ($filter) {
            case ExcludeIf::IS_EMPTY:
                if (!$value || empty($value)) {
                    return false;
                }
                break;
            case ExcludeIf::IS_NULL:
                if (is_null($value)) {
                    return false;
                }
                break;
            case ExcludeIf::IS_NOT_NULL:
                if (!is_null($value)) {
                    return false;
                }
                break;
            case ExcludeIf::IS_FALSE:
                if ($value === false) {
                    return false;
                }
                break;
            case ExcludeIf::IS_TRUE:
                if ($value === true) {
                    return false;
                }
                break;
            case ExcludeIf::IS_NOT_TRUE:
                if ($value !== true) {
                    return false;
                }
                break;
            case ExcludeIf::IS_NOT_FALSE:
                if ($value !== false) {
                    return false;
                }
                break;
            default:
                throw new \Exception('Wrong filter type for ElasticaList');
        }
        return true;
    }

    /**
     * @param ElasticaQuery $query
     * @return Query\Bool|null
     * @throws \Exception
     */
    protected function createQuery($query)
    {
        $properties = $this->metadataFactory->getQueryProperties($query);
        $methods = $this->metadataFactory->getQueryMethods($query);
        $prefix = 'Brander\\Bundle\\ElasticaSkeletonBundle\\Annotation\\';

        $qry = new \Elastica\Query\Bool();

        foreach ($properties as $property) {
            $value = $property->getValue($query);
            $type = $property->getType();
            if (!$this->filterQuery($value, $property->getFilter())) {
                continue;
            }
            if ($callMethod = $type->callMethod) {
                $value = $value->$callMethod();
            }
            $subQuery = null;
            switch (get_class($type)) {
                case $prefix . 'Term':
                    $subQuery = new \Elastica\Query\Term(
                        [
                            $type->index => $value,
                        ]
                    );
                    break;
                case $prefix . 'Terms':
                    $subQuery = new \Elastica\Query\Terms($type->index, $value);
                    break;
                case $prefix . 'QueryString':
                    /** @var $type QueryString */
                    $type = $property->getType();
                    if ($type->withoutIndex) {
                        $subQuery = new \Elastica\Query\QueryString($value);
                    } else {
                        $subQuery = new \Elastica\Query\QueryString(
                            [
                                $type->index => $value,
                            ]
                        );
                    }
                    break;
                case get_class():
                    throw new \Exception(
                        'You forgot set @Elastica\Term() or else type in '
                        . get_class($query)
                        . '->' . $property->name
                    );
                    break;
                default:
                    throw new \Exception(
                        'Not support query type for ElasticaList, maybe you forgot set @Elastica\Term()'
                    );
            }
            if (!$subQuery) {
                throw new \Exception('Something goes wrong with this query type for ElasticaList');
            }
            if ($type->must) {
                $qry->addMust($subQuery);
            } else {
                $qry->addMustNot($subQuery);
            }
        }

        foreach ($methods as $method) {
            $methodName = $method->name;
            $value = $query->$methodName();
            $type = $method->getType();
            if (!$this->filterQuery($value, $method->getFilter())) {
                continue;
            }
            if ($callMethod = $type->callMethod) {
                $value = $value->$callMethod();
            }
            $subQuery = null;
            switch (get_class($type)) {
                case $prefix . 'Term':
                    $subQuery = new \Elastica\Query\Term(
                        [
                            $type->index => $value,
                        ]
                    );
                    break;
                case $prefix . 'Terms':
                    $subQuery = new \Elastica\Query\Terms(
                        $type->index, $value
                    );
                    break;
                case $prefix . 'QueryString':
                    /** @var $type QueryString */
                    if ($type->withoutIndex) {
                        $subQuery = new \Elastica\Query\QueryString($value);
                    } else {
                        $subQuery = new \Elastica\Query\QueryString(
                            [
                                $type->index => $value,
                            ]
                        );
                    }
                    break;
                case $prefix . 'Raw':
                    /** @var $type Raw */
                    $subQuery = $value;
                    if (!($subQuery instanceof \Elastica\Query\AbstractQuery || is_array($subQuery))) {
                        throw new \Exception('Raw query has wrong type (in ElasticaList)');
                    }
                    break;
                case get_class():
                    throw new \Exception(
                        'You forgot set @Elastica\Term() or else type in '
                        . get_class($query)
                        . '->' . $methodName
                        . '()'
                    );
                default:
                    throw new \Exception(
                        'Not support query type for ElasticaList, maybe you forgot set @Elastica\Term()'
                    );
            }
            if (!$subQuery) {
                throw new \Exception('Something goes wrong with this query type for ElasticaList');
            }
            if ($type->must) {
                $qry->addMust($subQuery);
            } else {
                $qry->addMustNot($subQuery);
            }
        }

        $query->addQueries($qry);

        $check = $qry->toArray()['bool'];
        if ($check instanceof \stdClass) {
            $check = (array) $check;
        }
        if (!count($check)) {
            $qry = null;
        }
        return $qry;
    }

    /**
     * @param ElasticaQuery $query
     * @return \Elastica\Filter\AbstractMulti
     */
    protected function createFilters($query)
    {
        $filterCommon = new \Elastica\Filter\BoolAnd();
        foreach ($query->getFilters() as $filter) {
            $filterCommon->addFilter($filter);
        }

        return $filterCommon;
    }

    /**
     * @param Query $elastica
     * @param ElasticaQuery $query
     */
    protected function addAggregations(\Elastica\Query $elastica, $query)
    {
        $map = $query->getAggregations();
        if (!$map || empty($map)) {
            return;
        }
        foreach ($map as $aggregationMetaData) {
            $index = $aggregationMetaData->getIndex();
            $type = $aggregationMetaData->getType();
            $name = $aggregationMetaData->getName();
            $type = '\\Elastica\\Aggregation\\' . ucfirst($type);
            /** @var AbstractSimpleAggregation $aggregation */
            if (isset($aggregationMetaData->getExtra()['constructArguments'])) {
                $reflect = new ReflectionClass($type);
                $aggregation = $reflect->newInstanceArgs($aggregationMetaData->getExtra()['constructArguments']);
            } else {
                $aggregation = new $type($name);
            }
            $aggregation->setField($index);
            $elastica->addAggregation($aggregation);
        }
    }

    /**
     * @param \Elastica\Query $elastica
     * @param \Elastica\Filter\AbstractMulti $filterCommon
     */
    protected function addFilters(Query $elastica, \Elastica\Filter\AbstractMulti $filterCommon)
    {
        if (count($filterCommon->getFilters())) {
            $elastica->setParam('filter', $filterCommon->toArray());
        }
    }

    /**
     * @param \Elastica\Query $elastica
     * @param ElasticaQuery $query
     */
    protected function addOrder(Query $elastica, $query)
    {
        if (($order = $query->getOrder()) && is_array($order) && count($order)) {
            $elastica->addSort(
                [
                    $order[0] => [
                        'order' => isset($order[1]) ? $order[1] : 'asc',
                    ],
                ]
            );
        }
    }

    /**
     * @param Pagerfanta $data
     * @param ElasticaResult $result
     * @param ElasticaQuery $query
     * @throws \Exception
     */
    protected function addAggregationToResult(Pagerfanta $data, ElasticaResult $result, ElasticaQuery $query)
    {
        $map = $query->getAggregations();
        if (!$map || empty($map)) {
            return;
        }
        $adapter = $data->getAdapter();
        if ($adapter instanceof RawPaginatorAdapter || $adapter instanceof FantaPaginatorAdapter) {
            $aggregations = $adapter->getAggregations();
            foreach ($map as $aggregationMetadata) {
                $name = $aggregationMetadata->getName();
                if (!isset($aggregations[$name])) {
                    throw new \Exception(
                        'Specified aggregation ('
                        . $name
                        . ') not found in result, can\'t get Aggregations'
                    );
                }
                $setter = $aggregationMetadata->getSetter();
                if (!method_exists($result, $setter)) {
                    throw new \Exception(
                        'Specified aggregation ('
                        . $name
                        . ') has no setter for data: '
                        . $setter
                        . ', in class: '
                        . get_class($result)
                        . ', can\'t get Aggregations'
                    );
                }
                if (isset($aggregationMetadata->getExtra()['extractValueField'])) {
                    $value = $aggregations[$name][$aggregationMetadata->getExtra()['extractValueField']];
                } else {
                    $value = $aggregations[$name];
                }

                $result->$setter($value, $aggregationMetadata);
            }
        } else {
            throw new \Exception('Wrong adapter type, can\'t get Aggregations');
        }
    }

    /**
     * @example return new ElasticaResult($rows, $page, $countPage, $countTotal);
     * @param Pagerfanta $data
     * @param            $rows
     * @param            $page
     * @param            $countPage
     * @param            $countTotal
     * @return ElasticaResult
     */
    abstract protected function createResult(Pagerfanta $data, $rows, $page, $countPage, $countTotal);

    /**
     * @param ElasticaQuery $query
     * @param array $orderMap
     * @return ElasticaResult
     * @throws \Exception
     */
    public function result($query, array $orderMap = null)
    {
        if ($query instanceof ElasticaQuery) {
            $query->prettify();
            $qry = $this->createQuery($query);
            $filterCommon = $this->createFilters($query);

            $elastica = new \Elastica\Query($qry);

            $this->addFilters($elastica, $filterCommon);
            $this->addAggregations($elastica, $query);
            $this->addOrder($elastica, $query);

            $data = $this->finder->findPaginated($elastica);
            $data->setCurrentPage($query->getPage());
            $data->setMaxPerPage($query->getPageGroup());

            $rows = (array)$data->getIterator();
            $result = $this->createResult(
                $data,
                $rows,
                $query->getPage(),
                max($data->getNbPages(), 1),
                $data->getNbResults()
            );
            $this->addAggregationToResult($data, $result, $query);

            return $result;
        }

        throw new \Exception('Wrong query');
    }

    /**
     * @param ElasticaQuery $query
     * @return ElasticaResult
     * @throws \Exception
     */
    public function resultSearch($query)
    {
        if ($query instanceof ElasticaQuery) {
            $query->prettify();
            $qry = $this->createQuery($query);

            $elastica = new \Elastica\Query($qry);

            $data = $this->finder->findPaginated($elastica);
            $data->setCurrentPage($query->getPage());
            $data->setMaxPerPage($query->getPageGroup());

            $rows = (array)$data->getIterator();
            $result = $this->createResult(
                $data,
                $rows,
                $query->getPage(),
                max($data->getNbPages(), 1),
                $data->getNbResults()
            );

            return $result;
        }

        throw new \Exception('Wrong query');
    }
}