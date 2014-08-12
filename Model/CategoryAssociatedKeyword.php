<?php

namespace Keyword\Model;

use Keyword\Model\Base\CategoryAssociatedKeyword as BaseCategoryAssociatedKeyword;
use Keyword\Model\Map\CategoryAssociatedKeywordTableMap;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;

class CategoryAssociatedKeyword extends BaseCategoryAssociatedKeyword
{
    use \Thelia\Model\Tools\ModelEventDispatcherTrait;
    use \Thelia\Model\Tools\PositionManagementTrait;

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
