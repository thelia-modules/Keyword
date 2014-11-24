<?php

namespace Keyword\Model\Base;

use \Exception;
use \PDO;
use Keyword\Model\Keyword as ChildKeyword;
use Keyword\Model\KeywordI18nQuery as ChildKeywordI18nQuery;
use Keyword\Model\KeywordQuery as ChildKeywordQuery;
use Keyword\Model\Map\KeywordTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'keyword' table.
 *
 *
 *
 * @method     ChildKeywordQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildKeywordQuery orderByKeywordGroupId($order = Criteria::ASC) Order by the keyword_group_id column
 * @method     ChildKeywordQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     ChildKeywordQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildKeywordQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     ChildKeywordQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildKeywordQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildKeywordQuery groupById() Group by the id column
 * @method     ChildKeywordQuery groupByKeywordGroupId() Group by the keyword_group_id column
 * @method     ChildKeywordQuery groupByVisible() Group by the visible column
 * @method     ChildKeywordQuery groupByPosition() Group by the position column
 * @method     ChildKeywordQuery groupByCode() Group by the code column
 * @method     ChildKeywordQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildKeywordQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildKeywordQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildKeywordQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildKeywordQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildKeywordQuery leftJoinKeywordGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the KeywordGroup relation
 * @method     ChildKeywordQuery rightJoinKeywordGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the KeywordGroup relation
 * @method     ChildKeywordQuery innerJoinKeywordGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the KeywordGroup relation
 *
 * @method     ChildKeywordQuery leftJoinContentAssociatedKeyword($relationAlias = null) Adds a LEFT JOIN clause to the query using the ContentAssociatedKeyword relation
 * @method     ChildKeywordQuery rightJoinContentAssociatedKeyword($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ContentAssociatedKeyword relation
 * @method     ChildKeywordQuery innerJoinContentAssociatedKeyword($relationAlias = null) Adds a INNER JOIN clause to the query using the ContentAssociatedKeyword relation
 *
 * @method     ChildKeywordQuery leftJoinFolderAssociatedKeyword($relationAlias = null) Adds a LEFT JOIN clause to the query using the FolderAssociatedKeyword relation
 * @method     ChildKeywordQuery rightJoinFolderAssociatedKeyword($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FolderAssociatedKeyword relation
 * @method     ChildKeywordQuery innerJoinFolderAssociatedKeyword($relationAlias = null) Adds a INNER JOIN clause to the query using the FolderAssociatedKeyword relation
 *
 * @method     ChildKeywordQuery leftJoinCategoryAssociatedKeyword($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategoryAssociatedKeyword relation
 * @method     ChildKeywordQuery rightJoinCategoryAssociatedKeyword($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategoryAssociatedKeyword relation
 * @method     ChildKeywordQuery innerJoinCategoryAssociatedKeyword($relationAlias = null) Adds a INNER JOIN clause to the query using the CategoryAssociatedKeyword relation
 *
 * @method     ChildKeywordQuery leftJoinProductAssociatedKeyword($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductAssociatedKeyword relation
 * @method     ChildKeywordQuery rightJoinProductAssociatedKeyword($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductAssociatedKeyword relation
 * @method     ChildKeywordQuery innerJoinProductAssociatedKeyword($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductAssociatedKeyword relation
 *
 * @method     ChildKeywordQuery leftJoinKeywordI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the KeywordI18n relation
 * @method     ChildKeywordQuery rightJoinKeywordI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the KeywordI18n relation
 * @method     ChildKeywordQuery innerJoinKeywordI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the KeywordI18n relation
 *
 * @method     ChildKeyword findOne(ConnectionInterface $con = null) Return the first ChildKeyword matching the query
 * @method     ChildKeyword findOneOrCreate(ConnectionInterface $con = null) Return the first ChildKeyword matching the query, or a new ChildKeyword object populated from the query conditions when no match is found
 *
 * @method     ChildKeyword findOneById(int $id) Return the first ChildKeyword filtered by the id column
 * @method     ChildKeyword findOneByKeywordGroupId(int $keyword_group_id) Return the first ChildKeyword filtered by the keyword_group_id column
 * @method     ChildKeyword findOneByVisible(int $visible) Return the first ChildKeyword filtered by the visible column
 * @method     ChildKeyword findOneByPosition(int $position) Return the first ChildKeyword filtered by the position column
 * @method     ChildKeyword findOneByCode(string $code) Return the first ChildKeyword filtered by the code column
 * @method     ChildKeyword findOneByCreatedAt(string $created_at) Return the first ChildKeyword filtered by the created_at column
 * @method     ChildKeyword findOneByUpdatedAt(string $updated_at) Return the first ChildKeyword filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildKeyword objects filtered by the id column
 * @method     array findByKeywordGroupId(int $keyword_group_id) Return ChildKeyword objects filtered by the keyword_group_id column
 * @method     array findByVisible(int $visible) Return ChildKeyword objects filtered by the visible column
 * @method     array findByPosition(int $position) Return ChildKeyword objects filtered by the position column
 * @method     array findByCode(string $code) Return ChildKeyword objects filtered by the code column
 * @method     array findByCreatedAt(string $created_at) Return ChildKeyword objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildKeyword objects filtered by the updated_at column
 *
 */
abstract class KeywordQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Keyword\Model\Base\KeywordQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Keyword\\Model\\Keyword', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildKeywordQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildKeywordQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Keyword\Model\KeywordQuery) {
            return $criteria;
        }
        $query = new \Keyword\Model\KeywordQuery();
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
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildKeyword|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = KeywordTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(KeywordTableMap::DATABASE_NAME);
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
     * @return   ChildKeyword A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, KEYWORD_GROUP_ID, VISIBLE, POSITION, CODE, CREATED_AT, UPDATED_AT FROM keyword WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildKeyword();
            $obj->hydrate($row);
            KeywordTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildKeyword|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(12, 56, 832), $con);
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
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(KeywordTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(KeywordTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(KeywordTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(KeywordTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KeywordTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the keyword_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByKeywordGroupId(1234); // WHERE keyword_group_id = 1234
     * $query->filterByKeywordGroupId(array(12, 34)); // WHERE keyword_group_id IN (12, 34)
     * $query->filterByKeywordGroupId(array('min' => 12)); // WHERE keyword_group_id > 12
     * </code>
     *
     * @see       filterByKeywordGroup()
     *
     * @param     mixed $keywordGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByKeywordGroupId($keywordGroupId = null, $comparison = null)
    {
        if (is_array($keywordGroupId)) {
            $useMinMax = false;
            if (isset($keywordGroupId['min'])) {
                $this->addUsingAlias(KeywordTableMap::KEYWORD_GROUP_ID, $keywordGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($keywordGroupId['max'])) {
                $this->addUsingAlias(KeywordTableMap::KEYWORD_GROUP_ID, $keywordGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KeywordTableMap::KEYWORD_GROUP_ID, $keywordGroupId, $comparison);
    }

    /**
     * Filter the query on the visible column
     *
     * Example usage:
     * <code>
     * $query->filterByVisible(1234); // WHERE visible = 1234
     * $query->filterByVisible(array(12, 34)); // WHERE visible IN (12, 34)
     * $query->filterByVisible(array('min' => 12)); // WHERE visible > 12
     * </code>
     *
     * @param     mixed $visible The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(KeywordTableMap::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(KeywordTableMap::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KeywordTableMap::VISIBLE, $visible, $comparison);
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
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(KeywordTableMap::POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(KeywordTableMap::POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KeywordTableMap::POSITION, $position, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(KeywordTableMap::CODE, $code, $comparison);
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
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(KeywordTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(KeywordTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KeywordTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(KeywordTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(KeywordTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KeywordTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Keyword\Model\KeywordGroup object
     *
     * @param \Keyword\Model\KeywordGroup|ObjectCollection $keywordGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByKeywordGroup($keywordGroup, $comparison = null)
    {
        if ($keywordGroup instanceof \Keyword\Model\KeywordGroup) {
            return $this
                ->addUsingAlias(KeywordTableMap::KEYWORD_GROUP_ID, $keywordGroup->getId(), $comparison);
        } elseif ($keywordGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(KeywordTableMap::KEYWORD_GROUP_ID, $keywordGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByKeywordGroup() only accepts arguments of type \Keyword\Model\KeywordGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the KeywordGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function joinKeywordGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('KeywordGroup');

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
            $this->addJoinObject($join, 'KeywordGroup');
        }

        return $this;
    }

    /**
     * Use the KeywordGroup relation KeywordGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Keyword\Model\KeywordGroupQuery A secondary query class using the current class as primary query
     */
    public function useKeywordGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinKeywordGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'KeywordGroup', '\Keyword\Model\KeywordGroupQuery');
    }

    /**
     * Filter the query by a related \Keyword\Model\ContentAssociatedKeyword object
     *
     * @param \Keyword\Model\ContentAssociatedKeyword|ObjectCollection $contentAssociatedKeyword  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByContentAssociatedKeyword($contentAssociatedKeyword, $comparison = null)
    {
        if ($contentAssociatedKeyword instanceof \Keyword\Model\ContentAssociatedKeyword) {
            return $this
                ->addUsingAlias(KeywordTableMap::ID, $contentAssociatedKeyword->getKeywordId(), $comparison);
        } elseif ($contentAssociatedKeyword instanceof ObjectCollection) {
            return $this
                ->useContentAssociatedKeywordQuery()
                ->filterByPrimaryKeys($contentAssociatedKeyword->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByContentAssociatedKeyword() only accepts arguments of type \Keyword\Model\ContentAssociatedKeyword or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ContentAssociatedKeyword relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function joinContentAssociatedKeyword($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ContentAssociatedKeyword');

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
            $this->addJoinObject($join, 'ContentAssociatedKeyword');
        }

        return $this;
    }

    /**
     * Use the ContentAssociatedKeyword relation ContentAssociatedKeyword object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Keyword\Model\ContentAssociatedKeywordQuery A secondary query class using the current class as primary query
     */
    public function useContentAssociatedKeywordQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinContentAssociatedKeyword($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ContentAssociatedKeyword', '\Keyword\Model\ContentAssociatedKeywordQuery');
    }

    /**
     * Filter the query by a related \Keyword\Model\FolderAssociatedKeyword object
     *
     * @param \Keyword\Model\FolderAssociatedKeyword|ObjectCollection $folderAssociatedKeyword  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByFolderAssociatedKeyword($folderAssociatedKeyword, $comparison = null)
    {
        if ($folderAssociatedKeyword instanceof \Keyword\Model\FolderAssociatedKeyword) {
            return $this
                ->addUsingAlias(KeywordTableMap::ID, $folderAssociatedKeyword->getKeywordId(), $comparison);
        } elseif ($folderAssociatedKeyword instanceof ObjectCollection) {
            return $this
                ->useFolderAssociatedKeywordQuery()
                ->filterByPrimaryKeys($folderAssociatedKeyword->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByFolderAssociatedKeyword() only accepts arguments of type \Keyword\Model\FolderAssociatedKeyword or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FolderAssociatedKeyword relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function joinFolderAssociatedKeyword($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FolderAssociatedKeyword');

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
            $this->addJoinObject($join, 'FolderAssociatedKeyword');
        }

        return $this;
    }

    /**
     * Use the FolderAssociatedKeyword relation FolderAssociatedKeyword object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Keyword\Model\FolderAssociatedKeywordQuery A secondary query class using the current class as primary query
     */
    public function useFolderAssociatedKeywordQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFolderAssociatedKeyword($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FolderAssociatedKeyword', '\Keyword\Model\FolderAssociatedKeywordQuery');
    }

    /**
     * Filter the query by a related \Keyword\Model\CategoryAssociatedKeyword object
     *
     * @param \Keyword\Model\CategoryAssociatedKeyword|ObjectCollection $categoryAssociatedKeyword  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByCategoryAssociatedKeyword($categoryAssociatedKeyword, $comparison = null)
    {
        if ($categoryAssociatedKeyword instanceof \Keyword\Model\CategoryAssociatedKeyword) {
            return $this
                ->addUsingAlias(KeywordTableMap::ID, $categoryAssociatedKeyword->getKeywordId(), $comparison);
        } elseif ($categoryAssociatedKeyword instanceof ObjectCollection) {
            return $this
                ->useCategoryAssociatedKeywordQuery()
                ->filterByPrimaryKeys($categoryAssociatedKeyword->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategoryAssociatedKeyword() only accepts arguments of type \Keyword\Model\CategoryAssociatedKeyword or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CategoryAssociatedKeyword relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function joinCategoryAssociatedKeyword($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CategoryAssociatedKeyword');

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
            $this->addJoinObject($join, 'CategoryAssociatedKeyword');
        }

        return $this;
    }

    /**
     * Use the CategoryAssociatedKeyword relation CategoryAssociatedKeyword object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Keyword\Model\CategoryAssociatedKeywordQuery A secondary query class using the current class as primary query
     */
    public function useCategoryAssociatedKeywordQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategoryAssociatedKeyword($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CategoryAssociatedKeyword', '\Keyword\Model\CategoryAssociatedKeywordQuery');
    }

    /**
     * Filter the query by a related \Keyword\Model\ProductAssociatedKeyword object
     *
     * @param \Keyword\Model\ProductAssociatedKeyword|ObjectCollection $productAssociatedKeyword  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByProductAssociatedKeyword($productAssociatedKeyword, $comparison = null)
    {
        if ($productAssociatedKeyword instanceof \Keyword\Model\ProductAssociatedKeyword) {
            return $this
                ->addUsingAlias(KeywordTableMap::ID, $productAssociatedKeyword->getKeywordId(), $comparison);
        } elseif ($productAssociatedKeyword instanceof ObjectCollection) {
            return $this
                ->useProductAssociatedKeywordQuery()
                ->filterByPrimaryKeys($productAssociatedKeyword->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductAssociatedKeyword() only accepts arguments of type \Keyword\Model\ProductAssociatedKeyword or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductAssociatedKeyword relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function joinProductAssociatedKeyword($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductAssociatedKeyword');

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
            $this->addJoinObject($join, 'ProductAssociatedKeyword');
        }

        return $this;
    }

    /**
     * Use the ProductAssociatedKeyword relation ProductAssociatedKeyword object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Keyword\Model\ProductAssociatedKeywordQuery A secondary query class using the current class as primary query
     */
    public function useProductAssociatedKeywordQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductAssociatedKeyword($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductAssociatedKeyword', '\Keyword\Model\ProductAssociatedKeywordQuery');
    }

    /**
     * Filter the query by a related \Keyword\Model\KeywordI18n object
     *
     * @param \Keyword\Model\KeywordI18n|ObjectCollection $keywordI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function filterByKeywordI18n($keywordI18n, $comparison = null)
    {
        if ($keywordI18n instanceof \Keyword\Model\KeywordI18n) {
            return $this
                ->addUsingAlias(KeywordTableMap::ID, $keywordI18n->getId(), $comparison);
        } elseif ($keywordI18n instanceof ObjectCollection) {
            return $this
                ->useKeywordI18nQuery()
                ->filterByPrimaryKeys($keywordI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByKeywordI18n() only accepts arguments of type \Keyword\Model\KeywordI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the KeywordI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function joinKeywordI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('KeywordI18n');

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
            $this->addJoinObject($join, 'KeywordI18n');
        }

        return $this;
    }

    /**
     * Use the KeywordI18n relation KeywordI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Keyword\Model\KeywordI18nQuery A secondary query class using the current class as primary query
     */
    public function useKeywordI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinKeywordI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'KeywordI18n', '\Keyword\Model\KeywordI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildKeyword $keyword Object to remove from the list of results
     *
     * @return ChildKeywordQuery The current query, for fluid interface
     */
    public function prune($keyword = null)
    {
        if ($keyword) {
            $this->addUsingAlias(KeywordTableMap::ID, $keyword->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the keyword table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(KeywordTableMap::DATABASE_NAME);
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
            KeywordTableMap::clearInstancePool();
            KeywordTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildKeyword or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildKeyword object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(KeywordTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(KeywordTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        KeywordTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            KeywordTableMap::clearRelatedInstancePool();
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
     * @return     ChildKeywordQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(KeywordTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildKeywordQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(KeywordTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildKeywordQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(KeywordTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildKeywordQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(KeywordTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildKeywordQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(KeywordTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildKeywordQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(KeywordTableMap::CREATED_AT);
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildKeywordQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'KeywordI18n';

        return $this
            ->joinKeywordI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildKeywordQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('KeywordI18n');
        $this->with['KeywordI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildKeywordI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'KeywordI18n', '\Keyword\Model\KeywordI18nQuery');
    }

} // KeywordQuery
