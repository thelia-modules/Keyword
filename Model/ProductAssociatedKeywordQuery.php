<?php

namespace Keyword\Model;

use Keyword\Model\Base\ProductAssociatedKeywordQuery as BaseProductAssociatedKeywordQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'product_associated_keyword' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ProductAssociatedKeywordQuery extends BaseProductAssociatedKeywordQuery
{
    /**
     * Load an existing relation from the database
     * @param $productId
     * @param $keywordId
     * @return ChildProductAssociatedKeyword
     */
    public static function getProductKeywordAssociation($productId, $keywordId) {

        return self::create()
            ->filterByKeywordId($keywordId)
            ->filterByProductId($productId)
            ->findOne();
    }
} // ProductAssociatedKeywordQuery
