<?php

namespace Keyword\Model;

use Keyword\Model\Base\ContentAssociatedKeywordQuery as BaseContentAssociatedKeywordQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'content_associated_keyword' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ContentAssociatedKeywordQuery extends BaseContentAssociatedKeywordQuery
{
    /**
     * Load an existing relation from the database
     * @param $contentId
     * @param $keywordId
     * @return ChildContentAssociatedKeyword
     */
    public static function getContentKeywordAssociation($contentId, $keywordId) {

        return self::create()
            ->filterByKeywordId($keywordId)
            ->filterByContentId($contentId)
            ->findOne();
    }
} // ContentAssociatedKeywordQuery
