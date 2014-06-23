<?php

namespace Keyword\Model;

use Keyword\Model\Base\CategoryAssociatedKeywordQuery as BaseCategoryAssociatedKeywordQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'category_associated_keyword' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class CategoryAssociatedKeywordQuery extends BaseCategoryAssociatedKeywordQuery
{
    /**
     * Load an existing relation from the database
     * @param $categoryId
     * @param $keywordId
     * @return ChildCategoryAssociatedKeyword
     */
    public static function getCategoryKeywordAssociation($categoryId, $keywordId) {

        return self::create()
            ->filterByKeywordId($keywordId)
            ->filterByCategoryId($categoryId)
            ->findOne();
    }
} // CategoryAssociatedKeywordQuery
