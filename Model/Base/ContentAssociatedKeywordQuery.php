<?php

namespace Keyword\Model\Base;

use \Exception;
use \PDO;
use Keyword\Model\ContentAssociatedKeyword as ChildContentAssociatedKeyword;
use Keyword\Model\ContentAssociatedKeywordQuery as ChildContentAssociatedKeywordQuery;
use Keyword\Model\Map\ContentAssociatedKeywordTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Content;

/**
 * Base class that represents a query for the 'content_associated_keyword' table.
 *
 *
 *
 * @method     ChildContentAssociatedKeywordQuery orderByContentId($order = Criteria::ASC) Order by the content_id column
 * @method     ChildContentAssociatedKeywordQuery orderByKeywordId($order = Criteria::ASC) Order by the keyword_id column
 * @method     ChildContentAssociatedKeywordQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildContentAssociatedKeywordQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildContentAssociatedKeywordQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildContentAssociatedKeywordQuery groupByContentId() Group by the content_id column
 * @method     ChildContentAssociatedKeywordQuery groupByKeywordId() Group by the keyword_id column
 * @method     ChildContentAssociatedKeywordQuery groupByPosition() Group by the position column
 * @method     ChildContentAssociatedKeywordQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildContentAssociatedKeywordQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildContentAssociatedKeywordQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildContentAssociatedKeywordQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildContentAssociatedKeywordQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildContentAssociatedKeywordQuery leftJoinContent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Content relation
 * @method     ChildContentAssociatedKeywordQuery rightJoinContent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Content relation
 * @method     ChildContentAssociatedKeywordQuery innerJoinContent($relationAlias = null) Adds a INNER JOIN clause to the query using the Content relation
 *
 * @method     ChildContentAssociatedKeywordQuery leftJoinKeyword($relationAlias = null) Adds a LEFT JOIN clause to the query using the Keyword relation
 * @method     ChildContentAssociatedKeywordQuery rightJoinKeyword($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Keyword relation
 * @method     ChildContentAssociatedKeywordQuery innerJoinKeyword($relationAlias = null) Adds a INNER JOIN clause to the query using the Keyword relation
 *
 * @method     ChildContentAssociatedKeyword findOne(ConnectionInterface $con = null) Return the first ChildContentAssociatedKeyword matching the query
 * @method     ChildContentAssociatedKeyword findOneOrCreate(ConnectionInterface $con = null) Return the first ChildContentAssociatedKeyword matching the query, or a new ChildContentAssociatedKeyword object populated from the query conditions when no match is found
 *
 * @method     ChildContentAssociatedKeyword findOneByContentId(int $content_id) Return the first ChildContentAssociatedKeyword filtered by the content_id column
 * @method     ChildContentAssociatedKeyword findOneByKeywordId(int $keyword_id) Return the first ChildContentAssociatedKeyword filtered by the keyword_id column
 * @method     ChildContentAssociatedKeyword findOneByPosition(int $position) Return the first ChildContentAssociatedKeyword filtered by the position column
 * @method     ChildContentAssociatedKeyword findOneByCreatedAt(string $created_at) Return the first ChildContentAssociatedKeyword filtered by the created_at column
 * @method     ChildContentAssociatedKeyword findOneByUpdatedAt(string $updated_at) Return the first ChildContentAssociatedKeyword filtered by the updated_at column
 *
 * @method     array findByContentId(int $content_id) Return ChildContentAssociatedKeyword objects filtered by the content_id column
 * @method     array findByKeywordId(int $keyword_id) Return ChildContentAssociatedKeyword objects filtered by the keyword_id column
 * @method     array findByPosition(int $position) Return ChildContentAssociatedKeyword objects filtered by the position column
 * @method     array findByCreatedAt(string $created_at) Return ChildContentAssociatedKeyword objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildContentAssociatedKeyword objects filtered by the updated_at column
 *
 */
abstract class ContentAssociatedKeywordQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Keyword\Model\Base\ContentAssociatedKeywordQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Keyword\\Model\\ContentAssociatedKeyword', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildContentAssociatedKeywordQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildContentAssociatedKeywordQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Keyword\Model\ContentAssociatedKeywordQuery) {
            return $criteria;
        }
        $query = new \Keyword\Model\ContentAssociatedKeywordQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$content_id, $keyword_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildContentAssociatedKeyword|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ContentAssociatedKeywordTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ContentAssociatedKeywordTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildContentAssociatedKeyword A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT CONTENT_ID, KEYWORD_ID, POSITION, CREATED_AT, UPDATED_AT FROM content_associated_keyword WHERE CONTENT_ID = :p0 AND KEYWORD_ID = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildContentAssociatedKeyword();
            $obj->hydrate($row);
            ContentAssociatedKeywordTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildContentAssociatedKeyword|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ContentAssociatedKeywordTableMap::CONTENT_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ContentAssociatedKeywordTableMap::KEYWORD_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ContentAssociatedKeywordTableMap::CONTENT_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ContentAssociatedKeywordTableMap::KEYWORD_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the content_id column
     *
     * Example usage:
     * <code>
     * $query->filterByContentId(1234); // WHERE content_id = 1234
     * $query->filterByContentId(array(12, 34)); // WHERE content_id IN (12, 34)
     * $query->filterByContentId(array('min' => 12)); // WHERE content_id > 12
     * </code>
     *
     * @see       filterByContent()
     *
     * @param     mixed $contentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByContentId($contentId = null, $comparison = null)
    {
        if (is_array($contentId)) {
            $useMinMax = false;
            if (isset($contentId['min'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::CONTENT_ID, $contentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($contentId['max'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::CONTENT_ID, $contentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentAssociatedKeywordTableMap::CONTENT_ID, $contentId, $comparison);
    }

    /**
     * Filter the query on the keyword_id column
     *
     * Example usage:
     * <code>
     * $query->filterByKeywordId(1234); // WHERE keyword_id = 1234
     * $query->filterByKeywordId(array(12, 34)); // WHERE keyword_id IN (12, 34)
     * $query->filterByKeywordId(array('min' => 12)); // WHERE keyword_id > 12
     * </code>
     *
     * @see       filterByKeyword()
     *
     * @param     mixed $keywordId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByKeywordId($keywordId = null, $comparison = null)
    {
        if (is_array($keywordId)) {
            $useMinMax = false;
            if (isset($keywordId['min'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::KEYWORD_ID, $keywordId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($keywordId['max'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::KEYWORD_ID, $keywordId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentAssociatedKeywordTableMap::KEYWORD_ID, $keywordId, $comparison);
    }

    /**
     * Filter the query on the position column
     *
     * Example usage:
     * <code>
     * $query->filterByPosition(1234); // WHERE position = 1234
     * $query->filterByPosition(array(12, 34)); // WHERE position IN (12, 34)
     * $query->filterByPosition(array('min' => 12)); // WHERE position > 12
     * </code>
     *
     * @param     mixed $position The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentAssociatedKeywordTableMap::POSITION, $position, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentAssociatedKeywordTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(ContentAssociatedKeywordTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentAssociatedKeywordTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Thelia\Model\Content object
     *
     * @param \Thelia\Model\Content|ObjectCollection $content The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByContent($content, $comparison = null)
    {
        if ($content instanceof \Thelia\Model\Content) {
            return $this
                ->addUsingAlias(ContentAssociatedKeywordTableMap::CONTENT_ID, $content->getId(), $comparison);
        } elseif ($content instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ContentAssociatedKeywordTableMap::CONTENT_ID, $content->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByContent() only accepts arguments of type \Thelia\Model\Content or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Content relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function joinContent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Content');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Content');
        }

        return $this;
    }

    /**
     * Use the Content relation Content object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\ContentQuery A secondary query class using the current class as primary query
     */
    public function useContentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinContent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Content', '\Thelia\Model\ContentQuery');
    }

    /**
     * Filter the query by a related \Keyword\Model\Keyword object
     *
     * @param \Keyword\Model\Keyword|ObjectCollection $keyword The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function filterByKeyword($keyword, $comparison = null)
    {
        if ($keyword instanceof \Keyword\Model\Keyword) {
            return $this
                ->addUsingAlias(ContentAssociatedKeywordTableMap::KEYWORD_ID, $keyword->getId(), $comparison);
        } elseif ($keyword instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ContentAssociatedKeywordTableMap::KEYWORD_ID, $keyword->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByKeyword() only accepts arguments of type \Keyword\Model\Keyword or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Keyword relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function joinKeyword($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Keyword');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Keyword');
        }

        return $this;
    }

    /**
     * Use the Keyword relation Keyword object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Keyword\Model\KeywordQuery A secondary query class using the current class as primary query
     */
    public function useKeywordQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinKeyword($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Keyword', '\Keyword\Model\KeywordQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildContentAssociatedKeyword $contentAssociatedKeyword Object to remove from the list of results
     *
     * @return ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function prune($contentAssociatedKeyword = null)
    {
        if ($contentAssociatedKeyword) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ContentAssociatedKeywordTableMap::CONTENT_ID), $contentAssociatedKeyword->getContentId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ContentAssociatedKeywordTableMap::KEYWORD_ID), $contentAssociatedKeyword->getKeywordId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the content_associated_keyword table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ContentAssociatedKeywordTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ContentAssociatedKeywordTableMap::clearInstancePool();
            ContentAssociatedKeywordTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildContentAssociatedKeyword or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildContentAssociatedKeyword object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ContentAssociatedKeywordTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ContentAssociatedKeywordTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        ContentAssociatedKeywordTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ContentAssociatedKeywordTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(ContentAssociatedKeywordTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(ContentAssociatedKeywordTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(ContentAssociatedKeywordTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(ContentAssociatedKeywordTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(ContentAssociatedKeywordTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildContentAssociatedKeywordQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(ContentAssociatedKeywordTableMap::CREATED_AT);
    }

} // ContentAssociatedKeywordQuery
