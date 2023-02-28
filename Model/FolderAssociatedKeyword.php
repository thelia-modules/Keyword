<?php

namespace Keyword\Model;

use Keyword\Model\Base\FolderAssociatedKeyword as BaseFolderAssociatedKeyword;
use Keyword\Model\Map\FolderAssociatedKeywordTableMap;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Thelia\Model\Tools\PositionManagementTrait;

class FolderAssociatedKeyword extends BaseFolderAssociatedKeyword
{
    use PositionManagementTrait;

    public function preInsert(ConnectionInterface $con = null)
    {
        $this->setPosition($this->getNextPosition());

        return parent::preInsert($con);
    }

    protected function addCriteriaToPositionQuery($query)
    {
        $query->filterByKeywordId($this->getKeywordId());
    }
}
