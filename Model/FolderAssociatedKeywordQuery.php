<?php

namespace Keyword\Model;

use Keyword\Model\Base\FolderAssociatedKeywordQuery as BaseFolderAssociatedKeywordQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'folder_associated_keyword' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class FolderAssociatedKeywordQuery extends BaseFolderAssociatedKeywordQuery
{

    /**
     * Load an existing relation from the database
     * @param $folderId
     * @param $keywordId
     * @return ChildFolderAssociatedKeyword
     */
    public static function getFolderKeywordAssociation($folderId, $keywordId) {

        return self::create()
            ->filterByKeywordId($keywordId)
            ->filterByFolderId($folderId)
            ->findOne();
    }

} // FolderAssociatedKeywordQuery
