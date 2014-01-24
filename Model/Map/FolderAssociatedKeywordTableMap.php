<?php

namespace Keyword\Model\Map;

use Keyword\Model\FolderAssociatedKeyword;
use Keyword\Model\FolderAssociatedKeywordQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'folder_associated_keyword' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class FolderAssociatedKeywordTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Keyword.Model.Map.FolderAssociatedKeywordTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'folder_associated_keyword';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Keyword\\Model\\FolderAssociatedKeyword';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Keyword.Model.FolderAssociatedKeyword';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 5;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 5;

    /**
     * the column name for the FOLDER_ID field
     */
    const FOLDER_ID = 'folder_associated_keyword.FOLDER_ID';

    /**
     * the column name for the KEYWORD_ID field
     */
    const KEYWORD_ID = 'folder_associated_keyword.KEYWORD_ID';

    /**
     * the column name for the POSITION field
     */
    const POSITION = 'folder_associated_keyword.POSITION';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'folder_associated_keyword.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'folder_associated_keyword.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('FolderId', 'KeywordId', 'Position', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('folderId', 'keywordId', 'position', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(FolderAssociatedKeywordTableMap::FOLDER_ID, FolderAssociatedKeywordTableMap::KEYWORD_ID, FolderAssociatedKeywordTableMap::POSITION, FolderAssociatedKeywordTableMap::CREATED_AT, FolderAssociatedKeywordTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('FOLDER_ID', 'KEYWORD_ID', 'POSITION', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('folder_id', 'keyword_id', 'position', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('FolderId' => 0, 'KeywordId' => 1, 'Position' => 2, 'CreatedAt' => 3, 'UpdatedAt' => 4, ),
        self::TYPE_STUDLYPHPNAME => array('folderId' => 0, 'keywordId' => 1, 'position' => 2, 'createdAt' => 3, 'updatedAt' => 4, ),
        self::TYPE_COLNAME       => array(FolderAssociatedKeywordTableMap::FOLDER_ID => 0, FolderAssociatedKeywordTableMap::KEYWORD_ID => 1, FolderAssociatedKeywordTableMap::POSITION => 2, FolderAssociatedKeywordTableMap::CREATED_AT => 3, FolderAssociatedKeywordTableMap::UPDATED_AT => 4, ),
        self::TYPE_RAW_COLNAME   => array('FOLDER_ID' => 0, 'KEYWORD_ID' => 1, 'POSITION' => 2, 'CREATED_AT' => 3, 'UPDATED_AT' => 4, ),
        self::TYPE_FIELDNAME     => array('folder_id' => 0, 'keyword_id' => 1, 'position' => 2, 'created_at' => 3, 'updated_at' => 4, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('folder_associated_keyword');
        $this->setPhpName('FolderAssociatedKeyword');
        $this->setClassName('\\Keyword\\Model\\FolderAssociatedKeyword');
        $this->setPackage('Keyword.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('FOLDER_ID', 'FolderId', 'INTEGER' , 'folder', 'ID', true, null, null);
        $this->addForeignPrimaryKey('KEYWORD_ID', 'KeywordId', 'INTEGER' , 'keyword', 'ID', true, null, null);
        $this->addColumn('POSITION', 'Position', 'INTEGER', true, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Folder', '\\Thelia\\Model\\Folder', RelationMap::MANY_TO_ONE, array('folder_id' => 'id', ), 'CASCADE', 'RESTRICT');
        $this->addRelation('Keyword', '\\Keyword\\Model\\Keyword', RelationMap::MANY_TO_ONE, array('keyword_id' => 'id', ), 'CASCADE', 'RESTRICT');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()

    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database. In some cases you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by find*()
     * and findPk*() calls.
     *
     * @param \Keyword\Model\FolderAssociatedKeyword $obj A \Keyword\Model\FolderAssociatedKeyword object.
     * @param string $key             (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (null === $key) {
                $key = serialize(array((string) $obj->getFolderId(), (string) $obj->getKeywordId()));
            } // if key === null
            self::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param mixed $value A \Keyword\Model\FolderAssociatedKeyword object or a primary key value.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && null !== $value) {
            if (is_object($value) && $value instanceof \Keyword\Model\FolderAssociatedKeyword) {
                $key = serialize(array((string) $value->getFolderId(), (string) $value->getKeywordId()));

            } elseif (is_array($value) && count($value) === 2) {
                // assume we've been passed a primary key";
                $key = serialize(array((string) $value[0], (string) $value[1]));
            } elseif ($value instanceof Criteria) {
                self::$instances = [];

                return;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or \Keyword\Model\FolderAssociatedKeyword object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value, true)));
                throw $e;
            }

            unset(self::$instances[$key]);
        }
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('FolderId', TableMap::TYPE_PHPNAME, $indexType)] === null && $row[TableMap::TYPE_NUM == $indexType ? 1 + $offset : static::translateFieldName('KeywordId', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return serialize(array((string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('FolderId', TableMap::TYPE_PHPNAME, $indexType)], (string) $row[TableMap::TYPE_NUM == $indexType ? 1 + $offset : static::translateFieldName('KeywordId', TableMap::TYPE_PHPNAME, $indexType)]));
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return $pks;
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? FolderAssociatedKeywordTableMap::CLASS_DEFAULT : FolderAssociatedKeywordTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (FolderAssociatedKeyword object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = FolderAssociatedKeywordTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = FolderAssociatedKeywordTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + FolderAssociatedKeywordTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = FolderAssociatedKeywordTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            FolderAssociatedKeywordTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = FolderAssociatedKeywordTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = FolderAssociatedKeywordTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                FolderAssociatedKeywordTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(FolderAssociatedKeywordTableMap::FOLDER_ID);
            $criteria->addSelectColumn(FolderAssociatedKeywordTableMap::KEYWORD_ID);
            $criteria->addSelectColumn(FolderAssociatedKeywordTableMap::POSITION);
            $criteria->addSelectColumn(FolderAssociatedKeywordTableMap::CREATED_AT);
            $criteria->addSelectColumn(FolderAssociatedKeywordTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.FOLDER_ID');
            $criteria->addSelectColumn($alias . '.KEYWORD_ID');
            $criteria->addSelectColumn($alias . '.POSITION');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(FolderAssociatedKeywordTableMap::DATABASE_NAME)->getTable(FolderAssociatedKeywordTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(FolderAssociatedKeywordTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(FolderAssociatedKeywordTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new FolderAssociatedKeywordTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a FolderAssociatedKeyword or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or FolderAssociatedKeyword object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FolderAssociatedKeywordTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Keyword\Model\FolderAssociatedKeyword) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(FolderAssociatedKeywordTableMap::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            foreach ($values as $value) {
                $criterion = $criteria->getNewCriterion(FolderAssociatedKeywordTableMap::FOLDER_ID, $value[0]);
                $criterion->addAnd($criteria->getNewCriterion(FolderAssociatedKeywordTableMap::KEYWORD_ID, $value[1]));
                $criteria->addOr($criterion);
            }
        }

        $query = FolderAssociatedKeywordQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { FolderAssociatedKeywordTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { FolderAssociatedKeywordTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the folder_associated_keyword table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return FolderAssociatedKeywordQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a FolderAssociatedKeyword or Criteria object.
     *
     * @param mixed               $criteria Criteria or FolderAssociatedKeyword object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FolderAssociatedKeywordTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from FolderAssociatedKeyword object
        }


        // Set the correct dbName
        $query = FolderAssociatedKeywordQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // FolderAssociatedKeywordTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
FolderAssociatedKeywordTableMap::buildTableMap();
