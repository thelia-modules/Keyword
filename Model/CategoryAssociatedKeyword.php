<?php

namespace Keyword\Model;

use Keyword\Model\Base\CategoryAssociatedKeyword as BaseCategoryAssociatedKeyword;

use Propel\Runtime\Connection\ConnectionInterface;

use Thelia\Model\Tools\PositionManagementTrait;

class CategoryAssociatedKeyword extends BaseCategoryAssociatedKeyword
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
