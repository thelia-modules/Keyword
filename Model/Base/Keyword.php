<?php

namespace Keyword\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Keyword\Model\CategoryAssociatedKeyword as ChildCategoryAssociatedKeyword;
use Keyword\Model\CategoryAssociatedKeywordQuery as ChildCategoryAssociatedKeywordQuery;
use Keyword\Model\ContentAssociatedKeyword as ChildContentAssociatedKeyword;
use Keyword\Model\ContentAssociatedKeywordQuery as ChildContentAssociatedKeywordQuery;
use Keyword\Model\FolderAssociatedKeyword as ChildFolderAssociatedKeyword;
use Keyword\Model\FolderAssociatedKeywordQuery as ChildFolderAssociatedKeywordQuery;
use Keyword\Model\Keyword as ChildKeyword;
use Keyword\Model\KeywordGroup as ChildKeywordGroup;
use Keyword\Model\KeywordGroupQuery as ChildKeywordGroupQuery;
use Keyword\Model\KeywordI18n as ChildKeywordI18n;
use Keyword\Model\KeywordI18nQuery as ChildKeywordI18nQuery;
use Keyword\Model\KeywordQuery as ChildKeywordQuery;
use Keyword\Model\ProductAssociatedKeyword as ChildProductAssociatedKeyword;
use Keyword\Model\ProductAssociatedKeywordQuery as ChildProductAssociatedKeywordQuery;
use Keyword\Model\Map\KeywordTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class Keyword implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Keyword\\Model\\Map\\KeywordTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the keyword_group_id field.
     * @var        int
     */
    protected $keyword_group_id;

    /**
     * The value for the visible field.
     * @var        int
     */
    protected $visible;

    /**
     * The value for the position field.
     * @var        int
     */
    protected $position;

    /**
     * The value for the code field.
     * @var        string
     */
    protected $code;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        KeywordGroup
     */
    protected $aKeywordGroup;

    /**
     * @var        ObjectCollection|ChildContentAssociatedKeyword[] Collection to store aggregation of ChildContentAssociatedKeyword objects.
     */
    protected $collContentAssociatedKeywords;
    protected $collContentAssociatedKeywordsPartial;

    /**
     * @var        ObjectCollection|ChildFolderAssociatedKeyword[] Collection to store aggregation of ChildFolderAssociatedKeyword objects.
     */
    protected $collFolderAssociatedKeywords;
    protected $collFolderAssociatedKeywordsPartial;

    /**
     * @var        ObjectCollection|ChildCategoryAssociatedKeyword[] Collection to store aggregation of ChildCategoryAssociatedKeyword objects.
     */
    protected $collCategoryAssociatedKeywords;
    protected $collCategoryAssociatedKeywordsPartial;

    /**
     * @var        ObjectCollection|ChildProductAssociatedKeyword[] Collection to store aggregation of ChildProductAssociatedKeyword objects.
     */
    protected $collProductAssociatedKeywords;
    protected $collProductAssociatedKeywordsPartial;

    /**
     * @var        ObjectCollection|ChildKeywordI18n[] Collection to store aggregation of ChildKeywordI18n objects.
     */
    protected $collKeywordI18ns;
    protected $collKeywordI18nsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior

    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'en_US';

    /**
     * Current translation objects
     * @var        array[ChildKeywordI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $contentAssociatedKeywordsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $folderAssociatedKeywordsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $categoryAssociatedKeywordsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productAssociatedKeywordsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $keywordI18nsScheduledForDeletion = null;

    /**
     * Initializes internal state of Keyword\Model\Base\Keyword object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Keyword</code> instance.  If
     * <code>obj</code> is an instance of <code>Keyword</code>, delegates to
     * <code>equals(Keyword)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return Keyword The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return Keyword The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [keyword_group_id] column value.
     *
     * @return   int
     */
    public function getKeywordGroupId()
    {

        return $this->keyword_group_id;
    }

    /**
     * Get the [visible] column value.
     *
     * @return   int
     */
    public function getVisible()
    {

        return $this->visible;
    }

    /**
     * Get the [position] column value.
     *
     * @return   int
     */
    public function getPosition()
    {

        return $this->position;
    }

    /**
     * Get the [code] column value.
     *
     * @return   string
     */
    public function getCode()
    {

        return $this->code;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[KeywordTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [keyword_group_id] column.
     *
     * @param      int $v new value
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function setKeywordGroupId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->keyword_group_id !== $v) {
            $this->keyword_group_id = $v;
            $this->modifiedColumns[KeywordTableMap::KEYWORD_GROUP_ID] = true;
        }

        if ($this->aKeywordGroup !== null && $this->aKeywordGroup->getId() !== $v) {
            $this->aKeywordGroup = null;
        }


        return $this;
    } // setKeywordGroupId()

    /**
     * Set the value of [visible] column.
     *
     * @param      int $v new value
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function setVisible($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->visible !== $v) {
            $this->visible = $v;
            $this->modifiedColumns[KeywordTableMap::VISIBLE] = true;
        }


        return $this;
    } // setVisible()

    /**
     * Set the value of [position] column.
     *
     * @param      int $v new value
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function setPosition($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->position !== $v) {
            $this->position = $v;
            $this->modifiedColumns[KeywordTableMap::POSITION] = true;
        }


        return $this;
    } // setPosition()

    /**
     * Set the value of [code] column.
     *
     * @param      string $v new value
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function setCode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->code !== $v) {
            $this->code = $v;
            $this->modifiedColumns[KeywordTableMap::CODE] = true;
        }


        return $this;
    } // setCode()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[KeywordTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[KeywordTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : KeywordTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : KeywordTableMap::translateFieldName('KeywordGroupId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->keyword_group_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : KeywordTableMap::translateFieldName('Visible', TableMap::TYPE_PHPNAME, $indexType)];
            $this->visible = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : KeywordTableMap::translateFieldName('Position', TableMap::TYPE_PHPNAME, $indexType)];
            $this->position = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : KeywordTableMap::translateFieldName('Code', TableMap::TYPE_PHPNAME, $indexType)];
            $this->code = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : KeywordTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : KeywordTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = KeywordTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Keyword\Model\Keyword object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aKeywordGroup !== null && $this->keyword_group_id !== $this->aKeywordGroup->getId()) {
            $this->aKeywordGroup = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(KeywordTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildKeywordQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aKeywordGroup = null;
            $this->collContentAssociatedKeywords = null;

            $this->collFolderAssociatedKeywords = null;

            $this->collCategoryAssociatedKeywords = null;

            $this->collProductAssociatedKeywords = null;

            $this->collKeywordI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Keyword::setDeleted()
     * @see Keyword::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(KeywordTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildKeywordQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(KeywordTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(KeywordTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(KeywordTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(KeywordTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                KeywordTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aKeywordGroup !== null) {
                if ($this->aKeywordGroup->isModified() || $this->aKeywordGroup->isNew()) {
                    $affectedRows += $this->aKeywordGroup->save($con);
                }
                $this->setKeywordGroup($this->aKeywordGroup);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->contentAssociatedKeywordsScheduledForDeletion !== null) {
                if (!$this->contentAssociatedKeywordsScheduledForDeletion->isEmpty()) {
                    \Keyword\Model\ContentAssociatedKeywordQuery::create()
                        ->filterByPrimaryKeys($this->contentAssociatedKeywordsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->contentAssociatedKeywordsScheduledForDeletion = null;
                }
            }

                if ($this->collContentAssociatedKeywords !== null) {
            foreach ($this->collContentAssociatedKeywords as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->folderAssociatedKeywordsScheduledForDeletion !== null) {
                if (!$this->folderAssociatedKeywordsScheduledForDeletion->isEmpty()) {
                    \Keyword\Model\FolderAssociatedKeywordQuery::create()
                        ->filterByPrimaryKeys($this->folderAssociatedKeywordsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->folderAssociatedKeywordsScheduledForDeletion = null;
                }
            }

                if ($this->collFolderAssociatedKeywords !== null) {
            foreach ($this->collFolderAssociatedKeywords as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->categoryAssociatedKeywordsScheduledForDeletion !== null) {
                if (!$this->categoryAssociatedKeywordsScheduledForDeletion->isEmpty()) {
                    \Keyword\Model\CategoryAssociatedKeywordQuery::create()
                        ->filterByPrimaryKeys($this->categoryAssociatedKeywordsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->categoryAssociatedKeywordsScheduledForDeletion = null;
                }
            }

                if ($this->collCategoryAssociatedKeywords !== null) {
            foreach ($this->collCategoryAssociatedKeywords as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productAssociatedKeywordsScheduledForDeletion !== null) {
                if (!$this->productAssociatedKeywordsScheduledForDeletion->isEmpty()) {
                    \Keyword\Model\ProductAssociatedKeywordQuery::create()
                        ->filterByPrimaryKeys($this->productAssociatedKeywordsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productAssociatedKeywordsScheduledForDeletion = null;
                }
            }

                if ($this->collProductAssociatedKeywords !== null) {
            foreach ($this->collProductAssociatedKeywords as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->keywordI18nsScheduledForDeletion !== null) {
                if (!$this->keywordI18nsScheduledForDeletion->isEmpty()) {
                    \Keyword\Model\KeywordI18nQuery::create()
                        ->filterByPrimaryKeys($this->keywordI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->keywordI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collKeywordI18ns !== null) {
            foreach ($this->collKeywordI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[KeywordTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . KeywordTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(KeywordTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(KeywordTableMap::KEYWORD_GROUP_ID)) {
            $modifiedColumns[':p' . $index++]  = 'KEYWORD_GROUP_ID';
        }
        if ($this->isColumnModified(KeywordTableMap::VISIBLE)) {
            $modifiedColumns[':p' . $index++]  = 'VISIBLE';
        }
        if ($this->isColumnModified(KeywordTableMap::POSITION)) {
            $modifiedColumns[':p' . $index++]  = 'POSITION';
        }
        if ($this->isColumnModified(KeywordTableMap::CODE)) {
            $modifiedColumns[':p' . $index++]  = 'CODE';
        }
        if ($this->isColumnModified(KeywordTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(KeywordTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO keyword (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'KEYWORD_GROUP_ID':
                        $stmt->bindValue($identifier, $this->keyword_group_id, PDO::PARAM_INT);
                        break;
                    case 'VISIBLE':
                        $stmt->bindValue($identifier, $this->visible, PDO::PARAM_INT);
                        break;
                    case 'POSITION':
                        $stmt->bindValue($identifier, $this->position, PDO::PARAM_INT);
                        break;
                    case 'CODE':
                        $stmt->bindValue($identifier, $this->code, PDO::PARAM_STR);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = KeywordTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getKeywordGroupId();
                break;
            case 2:
                return $this->getVisible();
                break;
            case 3:
                return $this->getPosition();
                break;
            case 4:
                return $this->getCode();
                break;
            case 5:
                return $this->getCreatedAt();
                break;
            case 6:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Keyword'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Keyword'][$this->getPrimaryKey()] = true;
        $keys = KeywordTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getKeywordGroupId(),
            $keys[2] => $this->getVisible(),
            $keys[3] => $this->getPosition(),
            $keys[4] => $this->getCode(),
            $keys[5] => $this->getCreatedAt(),
            $keys[6] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aKeywordGroup) {
                $result['KeywordGroup'] = $this->aKeywordGroup->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collContentAssociatedKeywords) {
                $result['ContentAssociatedKeywords'] = $this->collContentAssociatedKeywords->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFolderAssociatedKeywords) {
                $result['FolderAssociatedKeywords'] = $this->collFolderAssociatedKeywords->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCategoryAssociatedKeywords) {
                $result['CategoryAssociatedKeywords'] = $this->collCategoryAssociatedKeywords->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductAssociatedKeywords) {
                $result['ProductAssociatedKeywords'] = $this->collProductAssociatedKeywords->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collKeywordI18ns) {
                $result['KeywordI18ns'] = $this->collKeywordI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = KeywordTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setKeywordGroupId($value);
                break;
            case 2:
                $this->setVisible($value);
                break;
            case 3:
                $this->setPosition($value);
                break;
            case 4:
                $this->setCode($value);
                break;
            case 5:
                $this->setCreatedAt($value);
                break;
            case 6:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = KeywordTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setKeywordGroupId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setVisible($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPosition($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setCode($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCreatedAt($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setUpdatedAt($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(KeywordTableMap::DATABASE_NAME);

        if ($this->isColumnModified(KeywordTableMap::ID)) $criteria->add(KeywordTableMap::ID, $this->id);
        if ($this->isColumnModified(KeywordTableMap::KEYWORD_GROUP_ID)) $criteria->add(KeywordTableMap::KEYWORD_GROUP_ID, $this->keyword_group_id);
        if ($this->isColumnModified(KeywordTableMap::VISIBLE)) $criteria->add(KeywordTableMap::VISIBLE, $this->visible);
        if ($this->isColumnModified(KeywordTableMap::POSITION)) $criteria->add(KeywordTableMap::POSITION, $this->position);
        if ($this->isColumnModified(KeywordTableMap::CODE)) $criteria->add(KeywordTableMap::CODE, $this->code);
        if ($this->isColumnModified(KeywordTableMap::CREATED_AT)) $criteria->add(KeywordTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(KeywordTableMap::UPDATED_AT)) $criteria->add(KeywordTableMap::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(KeywordTableMap::DATABASE_NAME);
        $criteria->add(KeywordTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Keyword\Model\Keyword (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setKeywordGroupId($this->getKeywordGroupId());
        $copyObj->setVisible($this->getVisible());
        $copyObj->setPosition($this->getPosition());
        $copyObj->setCode($this->getCode());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getContentAssociatedKeywords() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addContentAssociatedKeyword($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getFolderAssociatedKeywords() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFolderAssociatedKeyword($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCategoryAssociatedKeywords() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategoryAssociatedKeyword($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductAssociatedKeywords() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductAssociatedKeyword($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getKeywordI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addKeywordI18n($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \Keyword\Model\Keyword Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildKeywordGroup object.
     *
     * @param                  ChildKeywordGroup $v
     * @return                 \Keyword\Model\Keyword The current object (for fluent API support)
     * @throws PropelException
     */
    public function setKeywordGroup(ChildKeywordGroup $v = null)
    {
        if ($v === null) {
            $this->setKeywordGroupId(NULL);
        } else {
            $this->setKeywordGroupId($v->getId());
        }

        $this->aKeywordGroup = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildKeywordGroup object, it will not be re-added.
        if ($v !== null) {
            $v->addKeyword($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildKeywordGroup object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildKeywordGroup The associated ChildKeywordGroup object.
     * @throws PropelException
     */
    public function getKeywordGroup(ConnectionInterface $con = null)
    {
        if ($this->aKeywordGroup === null && ($this->keyword_group_id !== null)) {
            $this->aKeywordGroup = ChildKeywordGroupQuery::create()->findPk($this->keyword_group_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aKeywordGroup->addKeywords($this);
             */
        }

        return $this->aKeywordGroup;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('ContentAssociatedKeyword' == $relationName) {
            return $this->initContentAssociatedKeywords();
        }
        if ('FolderAssociatedKeyword' == $relationName) {
            return $this->initFolderAssociatedKeywords();
        }
        if ('CategoryAssociatedKeyword' == $relationName) {
            return $this->initCategoryAssociatedKeywords();
        }
        if ('ProductAssociatedKeyword' == $relationName) {
            return $this->initProductAssociatedKeywords();
        }
        if ('KeywordI18n' == $relationName) {
            return $this->initKeywordI18ns();
        }
    }

    /**
     * Clears out the collContentAssociatedKeywords collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addContentAssociatedKeywords()
     */
    public function clearContentAssociatedKeywords()
    {
        $this->collContentAssociatedKeywords = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collContentAssociatedKeywords collection loaded partially.
     */
    public function resetPartialContentAssociatedKeywords($v = true)
    {
        $this->collContentAssociatedKeywordsPartial = $v;
    }

    /**
     * Initializes the collContentAssociatedKeywords collection.
     *
     * By default this just sets the collContentAssociatedKeywords collection to an empty array (like clearcollContentAssociatedKeywords());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initContentAssociatedKeywords($overrideExisting = true)
    {
        if (null !== $this->collContentAssociatedKeywords && !$overrideExisting) {
            return;
        }
        $this->collContentAssociatedKeywords = new ObjectCollection();
        $this->collContentAssociatedKeywords->setModel('\Keyword\Model\ContentAssociatedKeyword');
    }

    /**
     * Gets an array of ChildContentAssociatedKeyword objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildKeyword is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildContentAssociatedKeyword[] List of ChildContentAssociatedKeyword objects
     * @throws PropelException
     */
    public function getContentAssociatedKeywords($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collContentAssociatedKeywordsPartial && !$this->isNew();
        if (null === $this->collContentAssociatedKeywords || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collContentAssociatedKeywords) {
                // return empty collection
                $this->initContentAssociatedKeywords();
            } else {
                $collContentAssociatedKeywords = ChildContentAssociatedKeywordQuery::create(null, $criteria)
                    ->filterByKeyword($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collContentAssociatedKeywordsPartial && count($collContentAssociatedKeywords)) {
                        $this->initContentAssociatedKeywords(false);

                        foreach ($collContentAssociatedKeywords as $obj) {
                            if (false == $this->collContentAssociatedKeywords->contains($obj)) {
                                $this->collContentAssociatedKeywords->append($obj);
                            }
                        }

                        $this->collContentAssociatedKeywordsPartial = true;
                    }

                    reset($collContentAssociatedKeywords);

                    return $collContentAssociatedKeywords;
                }

                if ($partial && $this->collContentAssociatedKeywords) {
                    foreach ($this->collContentAssociatedKeywords as $obj) {
                        if ($obj->isNew()) {
                            $collContentAssociatedKeywords[] = $obj;
                        }
                    }
                }

                $this->collContentAssociatedKeywords = $collContentAssociatedKeywords;
                $this->collContentAssociatedKeywordsPartial = false;
            }
        }

        return $this->collContentAssociatedKeywords;
    }

    /**
     * Sets a collection of ContentAssociatedKeyword objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $contentAssociatedKeywords A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildKeyword The current object (for fluent API support)
     */
    public function setContentAssociatedKeywords(Collection $contentAssociatedKeywords, ConnectionInterface $con = null)
    {
        $contentAssociatedKeywordsToDelete = $this->getContentAssociatedKeywords(new Criteria(), $con)->diff($contentAssociatedKeywords);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->contentAssociatedKeywordsScheduledForDeletion = clone $contentAssociatedKeywordsToDelete;

        foreach ($contentAssociatedKeywordsToDelete as $contentAssociatedKeywordRemoved) {
            $contentAssociatedKeywordRemoved->setKeyword(null);
        }

        $this->collContentAssociatedKeywords = null;
        foreach ($contentAssociatedKeywords as $contentAssociatedKeyword) {
            $this->addContentAssociatedKeyword($contentAssociatedKeyword);
        }

        $this->collContentAssociatedKeywords = $contentAssociatedKeywords;
        $this->collContentAssociatedKeywordsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ContentAssociatedKeyword objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ContentAssociatedKeyword objects.
     * @throws PropelException
     */
    public function countContentAssociatedKeywords(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collContentAssociatedKeywordsPartial && !$this->isNew();
        if (null === $this->collContentAssociatedKeywords || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collContentAssociatedKeywords) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getContentAssociatedKeywords());
            }

            $query = ChildContentAssociatedKeywordQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByKeyword($this)
                ->count($con);
        }

        return count($this->collContentAssociatedKeywords);
    }

    /**
     * Method called to associate a ChildContentAssociatedKeyword object to this object
     * through the ChildContentAssociatedKeyword foreign key attribute.
     *
     * @param    ChildContentAssociatedKeyword $l ChildContentAssociatedKeyword
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function addContentAssociatedKeyword(ChildContentAssociatedKeyword $l)
    {
        if ($this->collContentAssociatedKeywords === null) {
            $this->initContentAssociatedKeywords();
            $this->collContentAssociatedKeywordsPartial = true;
        }

        if (!in_array($l, $this->collContentAssociatedKeywords->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddContentAssociatedKeyword($l);
        }

        return $this;
    }

    /**
     * @param ContentAssociatedKeyword $contentAssociatedKeyword The contentAssociatedKeyword object to add.
     */
    protected function doAddContentAssociatedKeyword($contentAssociatedKeyword)
    {
        $this->collContentAssociatedKeywords[]= $contentAssociatedKeyword;
        $contentAssociatedKeyword->setKeyword($this);
    }

    /**
     * @param  ContentAssociatedKeyword $contentAssociatedKeyword The contentAssociatedKeyword object to remove.
     * @return ChildKeyword The current object (for fluent API support)
     */
    public function removeContentAssociatedKeyword($contentAssociatedKeyword)
    {
        if ($this->getContentAssociatedKeywords()->contains($contentAssociatedKeyword)) {
            $this->collContentAssociatedKeywords->remove($this->collContentAssociatedKeywords->search($contentAssociatedKeyword));
            if (null === $this->contentAssociatedKeywordsScheduledForDeletion) {
                $this->contentAssociatedKeywordsScheduledForDeletion = clone $this->collContentAssociatedKeywords;
                $this->contentAssociatedKeywordsScheduledForDeletion->clear();
            }
            $this->contentAssociatedKeywordsScheduledForDeletion[]= clone $contentAssociatedKeyword;
            $contentAssociatedKeyword->setKeyword(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Keyword is new, it will return
     * an empty collection; or if this Keyword has previously
     * been saved, it will retrieve related ContentAssociatedKeywords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Keyword.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildContentAssociatedKeyword[] List of ChildContentAssociatedKeyword objects
     */
    public function getContentAssociatedKeywordsJoinContent($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildContentAssociatedKeywordQuery::create(null, $criteria);
        $query->joinWith('Content', $joinBehavior);

        return $this->getContentAssociatedKeywords($query, $con);
    }

    /**
     * Clears out the collFolderAssociatedKeywords collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addFolderAssociatedKeywords()
     */
    public function clearFolderAssociatedKeywords()
    {
        $this->collFolderAssociatedKeywords = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collFolderAssociatedKeywords collection loaded partially.
     */
    public function resetPartialFolderAssociatedKeywords($v = true)
    {
        $this->collFolderAssociatedKeywordsPartial = $v;
    }

    /**
     * Initializes the collFolderAssociatedKeywords collection.
     *
     * By default this just sets the collFolderAssociatedKeywords collection to an empty array (like clearcollFolderAssociatedKeywords());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFolderAssociatedKeywords($overrideExisting = true)
    {
        if (null !== $this->collFolderAssociatedKeywords && !$overrideExisting) {
            return;
        }
        $this->collFolderAssociatedKeywords = new ObjectCollection();
        $this->collFolderAssociatedKeywords->setModel('\Keyword\Model\FolderAssociatedKeyword');
    }

    /**
     * Gets an array of ChildFolderAssociatedKeyword objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildKeyword is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildFolderAssociatedKeyword[] List of ChildFolderAssociatedKeyword objects
     * @throws PropelException
     */
    public function getFolderAssociatedKeywords($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collFolderAssociatedKeywordsPartial && !$this->isNew();
        if (null === $this->collFolderAssociatedKeywords || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFolderAssociatedKeywords) {
                // return empty collection
                $this->initFolderAssociatedKeywords();
            } else {
                $collFolderAssociatedKeywords = ChildFolderAssociatedKeywordQuery::create(null, $criteria)
                    ->filterByKeyword($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collFolderAssociatedKeywordsPartial && count($collFolderAssociatedKeywords)) {
                        $this->initFolderAssociatedKeywords(false);

                        foreach ($collFolderAssociatedKeywords as $obj) {
                            if (false == $this->collFolderAssociatedKeywords->contains($obj)) {
                                $this->collFolderAssociatedKeywords->append($obj);
                            }
                        }

                        $this->collFolderAssociatedKeywordsPartial = true;
                    }

                    reset($collFolderAssociatedKeywords);

                    return $collFolderAssociatedKeywords;
                }

                if ($partial && $this->collFolderAssociatedKeywords) {
                    foreach ($this->collFolderAssociatedKeywords as $obj) {
                        if ($obj->isNew()) {
                            $collFolderAssociatedKeywords[] = $obj;
                        }
                    }
                }

                $this->collFolderAssociatedKeywords = $collFolderAssociatedKeywords;
                $this->collFolderAssociatedKeywordsPartial = false;
            }
        }

        return $this->collFolderAssociatedKeywords;
    }

    /**
     * Sets a collection of FolderAssociatedKeyword objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $folderAssociatedKeywords A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildKeyword The current object (for fluent API support)
     */
    public function setFolderAssociatedKeywords(Collection $folderAssociatedKeywords, ConnectionInterface $con = null)
    {
        $folderAssociatedKeywordsToDelete = $this->getFolderAssociatedKeywords(new Criteria(), $con)->diff($folderAssociatedKeywords);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->folderAssociatedKeywordsScheduledForDeletion = clone $folderAssociatedKeywordsToDelete;

        foreach ($folderAssociatedKeywordsToDelete as $folderAssociatedKeywordRemoved) {
            $folderAssociatedKeywordRemoved->setKeyword(null);
        }

        $this->collFolderAssociatedKeywords = null;
        foreach ($folderAssociatedKeywords as $folderAssociatedKeyword) {
            $this->addFolderAssociatedKeyword($folderAssociatedKeyword);
        }

        $this->collFolderAssociatedKeywords = $folderAssociatedKeywords;
        $this->collFolderAssociatedKeywordsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FolderAssociatedKeyword objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related FolderAssociatedKeyword objects.
     * @throws PropelException
     */
    public function countFolderAssociatedKeywords(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collFolderAssociatedKeywordsPartial && !$this->isNew();
        if (null === $this->collFolderAssociatedKeywords || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFolderAssociatedKeywords) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getFolderAssociatedKeywords());
            }

            $query = ChildFolderAssociatedKeywordQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByKeyword($this)
                ->count($con);
        }

        return count($this->collFolderAssociatedKeywords);
    }

    /**
     * Method called to associate a ChildFolderAssociatedKeyword object to this object
     * through the ChildFolderAssociatedKeyword foreign key attribute.
     *
     * @param    ChildFolderAssociatedKeyword $l ChildFolderAssociatedKeyword
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function addFolderAssociatedKeyword(ChildFolderAssociatedKeyword $l)
    {
        if ($this->collFolderAssociatedKeywords === null) {
            $this->initFolderAssociatedKeywords();
            $this->collFolderAssociatedKeywordsPartial = true;
        }

        if (!in_array($l, $this->collFolderAssociatedKeywords->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddFolderAssociatedKeyword($l);
        }

        return $this;
    }

    /**
     * @param FolderAssociatedKeyword $folderAssociatedKeyword The folderAssociatedKeyword object to add.
     */
    protected function doAddFolderAssociatedKeyword($folderAssociatedKeyword)
    {
        $this->collFolderAssociatedKeywords[]= $folderAssociatedKeyword;
        $folderAssociatedKeyword->setKeyword($this);
    }

    /**
     * @param  FolderAssociatedKeyword $folderAssociatedKeyword The folderAssociatedKeyword object to remove.
     * @return ChildKeyword The current object (for fluent API support)
     */
    public function removeFolderAssociatedKeyword($folderAssociatedKeyword)
    {
        if ($this->getFolderAssociatedKeywords()->contains($folderAssociatedKeyword)) {
            $this->collFolderAssociatedKeywords->remove($this->collFolderAssociatedKeywords->search($folderAssociatedKeyword));
            if (null === $this->folderAssociatedKeywordsScheduledForDeletion) {
                $this->folderAssociatedKeywordsScheduledForDeletion = clone $this->collFolderAssociatedKeywords;
                $this->folderAssociatedKeywordsScheduledForDeletion->clear();
            }
            $this->folderAssociatedKeywordsScheduledForDeletion[]= clone $folderAssociatedKeyword;
            $folderAssociatedKeyword->setKeyword(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Keyword is new, it will return
     * an empty collection; or if this Keyword has previously
     * been saved, it will retrieve related FolderAssociatedKeywords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Keyword.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildFolderAssociatedKeyword[] List of ChildFolderAssociatedKeyword objects
     */
    public function getFolderAssociatedKeywordsJoinFolder($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildFolderAssociatedKeywordQuery::create(null, $criteria);
        $query->joinWith('Folder', $joinBehavior);

        return $this->getFolderAssociatedKeywords($query, $con);
    }

    /**
     * Clears out the collCategoryAssociatedKeywords collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCategoryAssociatedKeywords()
     */
    public function clearCategoryAssociatedKeywords()
    {
        $this->collCategoryAssociatedKeywords = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCategoryAssociatedKeywords collection loaded partially.
     */
    public function resetPartialCategoryAssociatedKeywords($v = true)
    {
        $this->collCategoryAssociatedKeywordsPartial = $v;
    }

    /**
     * Initializes the collCategoryAssociatedKeywords collection.
     *
     * By default this just sets the collCategoryAssociatedKeywords collection to an empty array (like clearcollCategoryAssociatedKeywords());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategoryAssociatedKeywords($overrideExisting = true)
    {
        if (null !== $this->collCategoryAssociatedKeywords && !$overrideExisting) {
            return;
        }
        $this->collCategoryAssociatedKeywords = new ObjectCollection();
        $this->collCategoryAssociatedKeywords->setModel('\Keyword\Model\CategoryAssociatedKeyword');
    }

    /**
     * Gets an array of ChildCategoryAssociatedKeyword objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildKeyword is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCategoryAssociatedKeyword[] List of ChildCategoryAssociatedKeyword objects
     * @throws PropelException
     */
    public function getCategoryAssociatedKeywords($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryAssociatedKeywordsPartial && !$this->isNew();
        if (null === $this->collCategoryAssociatedKeywords || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategoryAssociatedKeywords) {
                // return empty collection
                $this->initCategoryAssociatedKeywords();
            } else {
                $collCategoryAssociatedKeywords = ChildCategoryAssociatedKeywordQuery::create(null, $criteria)
                    ->filterByKeyword($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCategoryAssociatedKeywordsPartial && count($collCategoryAssociatedKeywords)) {
                        $this->initCategoryAssociatedKeywords(false);

                        foreach ($collCategoryAssociatedKeywords as $obj) {
                            if (false == $this->collCategoryAssociatedKeywords->contains($obj)) {
                                $this->collCategoryAssociatedKeywords->append($obj);
                            }
                        }

                        $this->collCategoryAssociatedKeywordsPartial = true;
                    }

                    reset($collCategoryAssociatedKeywords);

                    return $collCategoryAssociatedKeywords;
                }

                if ($partial && $this->collCategoryAssociatedKeywords) {
                    foreach ($this->collCategoryAssociatedKeywords as $obj) {
                        if ($obj->isNew()) {
                            $collCategoryAssociatedKeywords[] = $obj;
                        }
                    }
                }

                $this->collCategoryAssociatedKeywords = $collCategoryAssociatedKeywords;
                $this->collCategoryAssociatedKeywordsPartial = false;
            }
        }

        return $this->collCategoryAssociatedKeywords;
    }

    /**
     * Sets a collection of CategoryAssociatedKeyword objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $categoryAssociatedKeywords A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildKeyword The current object (for fluent API support)
     */
    public function setCategoryAssociatedKeywords(Collection $categoryAssociatedKeywords, ConnectionInterface $con = null)
    {
        $categoryAssociatedKeywordsToDelete = $this->getCategoryAssociatedKeywords(new Criteria(), $con)->diff($categoryAssociatedKeywords);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->categoryAssociatedKeywordsScheduledForDeletion = clone $categoryAssociatedKeywordsToDelete;

        foreach ($categoryAssociatedKeywordsToDelete as $categoryAssociatedKeywordRemoved) {
            $categoryAssociatedKeywordRemoved->setKeyword(null);
        }

        $this->collCategoryAssociatedKeywords = null;
        foreach ($categoryAssociatedKeywords as $categoryAssociatedKeyword) {
            $this->addCategoryAssociatedKeyword($categoryAssociatedKeyword);
        }

        $this->collCategoryAssociatedKeywords = $categoryAssociatedKeywords;
        $this->collCategoryAssociatedKeywordsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CategoryAssociatedKeyword objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CategoryAssociatedKeyword objects.
     * @throws PropelException
     */
    public function countCategoryAssociatedKeywords(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryAssociatedKeywordsPartial && !$this->isNew();
        if (null === $this->collCategoryAssociatedKeywords || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategoryAssociatedKeywords) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCategoryAssociatedKeywords());
            }

            $query = ChildCategoryAssociatedKeywordQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByKeyword($this)
                ->count($con);
        }

        return count($this->collCategoryAssociatedKeywords);
    }

    /**
     * Method called to associate a ChildCategoryAssociatedKeyword object to this object
     * through the ChildCategoryAssociatedKeyword foreign key attribute.
     *
     * @param    ChildCategoryAssociatedKeyword $l ChildCategoryAssociatedKeyword
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function addCategoryAssociatedKeyword(ChildCategoryAssociatedKeyword $l)
    {
        if ($this->collCategoryAssociatedKeywords === null) {
            $this->initCategoryAssociatedKeywords();
            $this->collCategoryAssociatedKeywordsPartial = true;
        }

        if (!in_array($l, $this->collCategoryAssociatedKeywords->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategoryAssociatedKeyword($l);
        }

        return $this;
    }

    /**
     * @param CategoryAssociatedKeyword $categoryAssociatedKeyword The categoryAssociatedKeyword object to add.
     */
    protected function doAddCategoryAssociatedKeyword($categoryAssociatedKeyword)
    {
        $this->collCategoryAssociatedKeywords[]= $categoryAssociatedKeyword;
        $categoryAssociatedKeyword->setKeyword($this);
    }

    /**
     * @param  CategoryAssociatedKeyword $categoryAssociatedKeyword The categoryAssociatedKeyword object to remove.
     * @return ChildKeyword The current object (for fluent API support)
     */
    public function removeCategoryAssociatedKeyword($categoryAssociatedKeyword)
    {
        if ($this->getCategoryAssociatedKeywords()->contains($categoryAssociatedKeyword)) {
            $this->collCategoryAssociatedKeywords->remove($this->collCategoryAssociatedKeywords->search($categoryAssociatedKeyword));
            if (null === $this->categoryAssociatedKeywordsScheduledForDeletion) {
                $this->categoryAssociatedKeywordsScheduledForDeletion = clone $this->collCategoryAssociatedKeywords;
                $this->categoryAssociatedKeywordsScheduledForDeletion->clear();
            }
            $this->categoryAssociatedKeywordsScheduledForDeletion[]= clone $categoryAssociatedKeyword;
            $categoryAssociatedKeyword->setKeyword(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Keyword is new, it will return
     * an empty collection; or if this Keyword has previously
     * been saved, it will retrieve related CategoryAssociatedKeywords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Keyword.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategoryAssociatedKeyword[] List of ChildCategoryAssociatedKeyword objects
     */
    public function getCategoryAssociatedKeywordsJoinCategory($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCategoryAssociatedKeywordQuery::create(null, $criteria);
        $query->joinWith('Category', $joinBehavior);

        return $this->getCategoryAssociatedKeywords($query, $con);
    }

    /**
     * Clears out the collProductAssociatedKeywords collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductAssociatedKeywords()
     */
    public function clearProductAssociatedKeywords()
    {
        $this->collProductAssociatedKeywords = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductAssociatedKeywords collection loaded partially.
     */
    public function resetPartialProductAssociatedKeywords($v = true)
    {
        $this->collProductAssociatedKeywordsPartial = $v;
    }

    /**
     * Initializes the collProductAssociatedKeywords collection.
     *
     * By default this just sets the collProductAssociatedKeywords collection to an empty array (like clearcollProductAssociatedKeywords());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductAssociatedKeywords($overrideExisting = true)
    {
        if (null !== $this->collProductAssociatedKeywords && !$overrideExisting) {
            return;
        }
        $this->collProductAssociatedKeywords = new ObjectCollection();
        $this->collProductAssociatedKeywords->setModel('\Keyword\Model\ProductAssociatedKeyword');
    }

    /**
     * Gets an array of ChildProductAssociatedKeyword objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildKeyword is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductAssociatedKeyword[] List of ChildProductAssociatedKeyword objects
     * @throws PropelException
     */
    public function getProductAssociatedKeywords($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductAssociatedKeywordsPartial && !$this->isNew();
        if (null === $this->collProductAssociatedKeywords || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductAssociatedKeywords) {
                // return empty collection
                $this->initProductAssociatedKeywords();
            } else {
                $collProductAssociatedKeywords = ChildProductAssociatedKeywordQuery::create(null, $criteria)
                    ->filterByKeyword($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductAssociatedKeywordsPartial && count($collProductAssociatedKeywords)) {
                        $this->initProductAssociatedKeywords(false);

                        foreach ($collProductAssociatedKeywords as $obj) {
                            if (false == $this->collProductAssociatedKeywords->contains($obj)) {
                                $this->collProductAssociatedKeywords->append($obj);
                            }
                        }

                        $this->collProductAssociatedKeywordsPartial = true;
                    }

                    reset($collProductAssociatedKeywords);

                    return $collProductAssociatedKeywords;
                }

                if ($partial && $this->collProductAssociatedKeywords) {
                    foreach ($this->collProductAssociatedKeywords as $obj) {
                        if ($obj->isNew()) {
                            $collProductAssociatedKeywords[] = $obj;
                        }
                    }
                }

                $this->collProductAssociatedKeywords = $collProductAssociatedKeywords;
                $this->collProductAssociatedKeywordsPartial = false;
            }
        }

        return $this->collProductAssociatedKeywords;
    }

    /**
     * Sets a collection of ProductAssociatedKeyword objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productAssociatedKeywords A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildKeyword The current object (for fluent API support)
     */
    public function setProductAssociatedKeywords(Collection $productAssociatedKeywords, ConnectionInterface $con = null)
    {
        $productAssociatedKeywordsToDelete = $this->getProductAssociatedKeywords(new Criteria(), $con)->diff($productAssociatedKeywords);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->productAssociatedKeywordsScheduledForDeletion = clone $productAssociatedKeywordsToDelete;

        foreach ($productAssociatedKeywordsToDelete as $productAssociatedKeywordRemoved) {
            $productAssociatedKeywordRemoved->setKeyword(null);
        }

        $this->collProductAssociatedKeywords = null;
        foreach ($productAssociatedKeywords as $productAssociatedKeyword) {
            $this->addProductAssociatedKeyword($productAssociatedKeyword);
        }

        $this->collProductAssociatedKeywords = $productAssociatedKeywords;
        $this->collProductAssociatedKeywordsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductAssociatedKeyword objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductAssociatedKeyword objects.
     * @throws PropelException
     */
    public function countProductAssociatedKeywords(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductAssociatedKeywordsPartial && !$this->isNew();
        if (null === $this->collProductAssociatedKeywords || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductAssociatedKeywords) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductAssociatedKeywords());
            }

            $query = ChildProductAssociatedKeywordQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByKeyword($this)
                ->count($con);
        }

        return count($this->collProductAssociatedKeywords);
    }

    /**
     * Method called to associate a ChildProductAssociatedKeyword object to this object
     * through the ChildProductAssociatedKeyword foreign key attribute.
     *
     * @param    ChildProductAssociatedKeyword $l ChildProductAssociatedKeyword
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function addProductAssociatedKeyword(ChildProductAssociatedKeyword $l)
    {
        if ($this->collProductAssociatedKeywords === null) {
            $this->initProductAssociatedKeywords();
            $this->collProductAssociatedKeywordsPartial = true;
        }

        if (!in_array($l, $this->collProductAssociatedKeywords->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductAssociatedKeyword($l);
        }

        return $this;
    }

    /**
     * @param ProductAssociatedKeyword $productAssociatedKeyword The productAssociatedKeyword object to add.
     */
    protected function doAddProductAssociatedKeyword($productAssociatedKeyword)
    {
        $this->collProductAssociatedKeywords[]= $productAssociatedKeyword;
        $productAssociatedKeyword->setKeyword($this);
    }

    /**
     * @param  ProductAssociatedKeyword $productAssociatedKeyword The productAssociatedKeyword object to remove.
     * @return ChildKeyword The current object (for fluent API support)
     */
    public function removeProductAssociatedKeyword($productAssociatedKeyword)
    {
        if ($this->getProductAssociatedKeywords()->contains($productAssociatedKeyword)) {
            $this->collProductAssociatedKeywords->remove($this->collProductAssociatedKeywords->search($productAssociatedKeyword));
            if (null === $this->productAssociatedKeywordsScheduledForDeletion) {
                $this->productAssociatedKeywordsScheduledForDeletion = clone $this->collProductAssociatedKeywords;
                $this->productAssociatedKeywordsScheduledForDeletion->clear();
            }
            $this->productAssociatedKeywordsScheduledForDeletion[]= clone $productAssociatedKeyword;
            $productAssociatedKeyword->setKeyword(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Keyword is new, it will return
     * an empty collection; or if this Keyword has previously
     * been saved, it will retrieve related ProductAssociatedKeywords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Keyword.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAssociatedKeyword[] List of ChildProductAssociatedKeyword objects
     */
    public function getProductAssociatedKeywordsJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProductAssociatedKeywordQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getProductAssociatedKeywords($query, $con);
    }

    /**
     * Clears out the collKeywordI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addKeywordI18ns()
     */
    public function clearKeywordI18ns()
    {
        $this->collKeywordI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collKeywordI18ns collection loaded partially.
     */
    public function resetPartialKeywordI18ns($v = true)
    {
        $this->collKeywordI18nsPartial = $v;
    }

    /**
     * Initializes the collKeywordI18ns collection.
     *
     * By default this just sets the collKeywordI18ns collection to an empty array (like clearcollKeywordI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initKeywordI18ns($overrideExisting = true)
    {
        if (null !== $this->collKeywordI18ns && !$overrideExisting) {
            return;
        }
        $this->collKeywordI18ns = new ObjectCollection();
        $this->collKeywordI18ns->setModel('\Keyword\Model\KeywordI18n');
    }

    /**
     * Gets an array of ChildKeywordI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildKeyword is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildKeywordI18n[] List of ChildKeywordI18n objects
     * @throws PropelException
     */
    public function getKeywordI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collKeywordI18nsPartial && !$this->isNew();
        if (null === $this->collKeywordI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collKeywordI18ns) {
                // return empty collection
                $this->initKeywordI18ns();
            } else {
                $collKeywordI18ns = ChildKeywordI18nQuery::create(null, $criteria)
                    ->filterByKeyword($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collKeywordI18nsPartial && count($collKeywordI18ns)) {
                        $this->initKeywordI18ns(false);

                        foreach ($collKeywordI18ns as $obj) {
                            if (false == $this->collKeywordI18ns->contains($obj)) {
                                $this->collKeywordI18ns->append($obj);
                            }
                        }

                        $this->collKeywordI18nsPartial = true;
                    }

                    reset($collKeywordI18ns);

                    return $collKeywordI18ns;
                }

                if ($partial && $this->collKeywordI18ns) {
                    foreach ($this->collKeywordI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collKeywordI18ns[] = $obj;
                        }
                    }
                }

                $this->collKeywordI18ns = $collKeywordI18ns;
                $this->collKeywordI18nsPartial = false;
            }
        }

        return $this->collKeywordI18ns;
    }

    /**
     * Sets a collection of KeywordI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $keywordI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildKeyword The current object (for fluent API support)
     */
    public function setKeywordI18ns(Collection $keywordI18ns, ConnectionInterface $con = null)
    {
        $keywordI18nsToDelete = $this->getKeywordI18ns(new Criteria(), $con)->diff($keywordI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->keywordI18nsScheduledForDeletion = clone $keywordI18nsToDelete;

        foreach ($keywordI18nsToDelete as $keywordI18nRemoved) {
            $keywordI18nRemoved->setKeyword(null);
        }

        $this->collKeywordI18ns = null;
        foreach ($keywordI18ns as $keywordI18n) {
            $this->addKeywordI18n($keywordI18n);
        }

        $this->collKeywordI18ns = $keywordI18ns;
        $this->collKeywordI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related KeywordI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related KeywordI18n objects.
     * @throws PropelException
     */
    public function countKeywordI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collKeywordI18nsPartial && !$this->isNew();
        if (null === $this->collKeywordI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collKeywordI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getKeywordI18ns());
            }

            $query = ChildKeywordI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByKeyword($this)
                ->count($con);
        }

        return count($this->collKeywordI18ns);
    }

    /**
     * Method called to associate a ChildKeywordI18n object to this object
     * through the ChildKeywordI18n foreign key attribute.
     *
     * @param    ChildKeywordI18n $l ChildKeywordI18n
     * @return   \Keyword\Model\Keyword The current object (for fluent API support)
     */
    public function addKeywordI18n(ChildKeywordI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collKeywordI18ns === null) {
            $this->initKeywordI18ns();
            $this->collKeywordI18nsPartial = true;
        }

        if (!in_array($l, $this->collKeywordI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddKeywordI18n($l);
        }

        return $this;
    }

    /**
     * @param KeywordI18n $keywordI18n The keywordI18n object to add.
     */
    protected function doAddKeywordI18n($keywordI18n)
    {
        $this->collKeywordI18ns[]= $keywordI18n;
        $keywordI18n->setKeyword($this);
    }

    /**
     * @param  KeywordI18n $keywordI18n The keywordI18n object to remove.
     * @return ChildKeyword The current object (for fluent API support)
     */
    public function removeKeywordI18n($keywordI18n)
    {
        if ($this->getKeywordI18ns()->contains($keywordI18n)) {
            $this->collKeywordI18ns->remove($this->collKeywordI18ns->search($keywordI18n));
            if (null === $this->keywordI18nsScheduledForDeletion) {
                $this->keywordI18nsScheduledForDeletion = clone $this->collKeywordI18ns;
                $this->keywordI18nsScheduledForDeletion->clear();
            }
            $this->keywordI18nsScheduledForDeletion[]= clone $keywordI18n;
            $keywordI18n->setKeyword(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->keyword_group_id = null;
        $this->visible = null;
        $this->position = null;
        $this->code = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collContentAssociatedKeywords) {
                foreach ($this->collContentAssociatedKeywords as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFolderAssociatedKeywords) {
                foreach ($this->collFolderAssociatedKeywords as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategoryAssociatedKeywords) {
                foreach ($this->collCategoryAssociatedKeywords as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductAssociatedKeywords) {
                foreach ($this->collProductAssociatedKeywords as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collKeywordI18ns) {
                foreach ($this->collKeywordI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collContentAssociatedKeywords = null;
        $this->collFolderAssociatedKeywords = null;
        $this->collCategoryAssociatedKeywords = null;
        $this->collProductAssociatedKeywords = null;
        $this->collKeywordI18ns = null;
        $this->aKeywordGroup = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(KeywordTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildKeyword The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[KeywordTableMap::UPDATED_AT] = true;

        return $this;
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildKeyword The current object (for fluent API support)
     */
    public function setLocale($locale = 'en_US')
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildKeywordI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collKeywordI18ns) {
                foreach ($this->collKeywordI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildKeywordI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildKeywordI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addKeywordI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildKeyword The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildKeywordI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collKeywordI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collKeywordI18ns[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildKeywordI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [title] column value.
         *
         * @return   string
         */
        public function getTitle()
        {
        return $this->getCurrentTranslation()->getTitle();
    }


        /**
         * Set the value of [title] column.
         *
         * @param      string $v new value
         * @return   \Keyword\Model\KeywordI18n The current object (for fluent API support)
         */
        public function setTitle($v)
        {    $this->getCurrentTranslation()->setTitle($v);

        return $this;
    }


        /**
         * Get the [description] column value.
         *
         * @return   string
         */
        public function getDescription()
        {
        return $this->getCurrentTranslation()->getDescription();
    }


        /**
         * Set the value of [description] column.
         *
         * @param      string $v new value
         * @return   \Keyword\Model\KeywordI18n The current object (for fluent API support)
         */
        public function setDescription($v)
        {    $this->getCurrentTranslation()->setDescription($v);

        return $this;
    }


        /**
         * Get the [chapo] column value.
         *
         * @return   string
         */
        public function getChapo()
        {
        return $this->getCurrentTranslation()->getChapo();
    }


        /**
         * Set the value of [chapo] column.
         *
         * @param      string $v new value
         * @return   \Keyword\Model\KeywordI18n The current object (for fluent API support)
         */
        public function setChapo($v)
        {    $this->getCurrentTranslation()->setChapo($v);

        return $this;
    }


        /**
         * Get the [postscriptum] column value.
         *
         * @return   string
         */
        public function getPostscriptum()
        {
        return $this->getCurrentTranslation()->getPostscriptum();
    }


        /**
         * Set the value of [postscriptum] column.
         *
         * @param      string $v new value
         * @return   \Keyword\Model\KeywordI18n The current object (for fluent API support)
         */
        public function setPostscriptum($v)
        {    $this->getCurrentTranslation()->setPostscriptum($v);

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
