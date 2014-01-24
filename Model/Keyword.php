<?php

namespace Keyword\Model;

use Keyword\Model\Base\Keyword as BaseKeyword;
use Keyword\Model\Map\KeywordTableMap;
use Propel\Runtime\Propel;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\FolderQuery;

class Keyword extends BaseKeyword
{
    use \Thelia\Model\Tools\ModelEventDispatcherTrait;
    use \Thelia\Model\Tools\PositionManagementTrait;

    public function getContents()
    {
        $contentId = array();

        foreach($this->getContentAssociatedKeywords() as $contentAssociatedKeyword)
        {
            $contentId[] = $contentAssociatedKeyword->getContentId();
        }

        return ContentQuery::create()
            ->filterById($contentId)
            ->find();
    }

    public function getFolders()
    {
        $folderId = array();

        foreach($this->getFolderAssociatedKeywords() as $folderAssociatedKeyword)
        {
            $folderId[] = $folderAssociatedKeyword->getFolderId();
        }

        return FolderQuery::create()
            ->filterById($folderId)
            ->find();
    }

    public function getCategories()
    {
        $categoryId = array();

        foreach($this->getCategoryAssociatedKeywords() as $categoryAssociatedKeyword)
        {
            $categoryId[] = $categoryAssociatedKeyword->getCategoryId();
        }

        return CategoryQuery::create()
            ->filterById($categoryId)
            ->find();
    }

    /**
     * Create a new keyword.
     *
     * Here pre and post insert event are fired
     *
     * @throws \Exception
     */
    public function create()
    {
        $con = Propel::getWriteConnection(KeywordTableMap::DATABASE_NAME);

        $con->beginTransaction();

        try {
            $this->save($con);
            $this->setPosition($this->getNextPosition())->save($con);
            $con->commit();

        } catch(\Exception $ex) {

            $con->rollback();
            throw $ex;
        }

        return $this;
    }

}
