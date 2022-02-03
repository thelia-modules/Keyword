<?php

namespace Keyword\Model;

use Keyword\Model\Base\KeywordGroupQuery as BaseKeywordGroupQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'keyword_group' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class KeywordGroupQuery extends BaseKeywordGroupQuery
{
    public static function getKeywordGroupByCode($code)
    {
        return self::create()->findOneByCode($code);
    }
} // KeywordGroupQuery
