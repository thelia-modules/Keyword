<?php

namespace Keyword\Model;

use Keyword\Model\Base\ContentAssociatedKeyword as BaseContentAssociatedKeyword;
use Keyword\Model\Map\ContentAssociatedKeywordTableMap;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Thelia\Model\Tools\PositionManagementTrait;

class ContentAssociatedKeyword extends BaseContentAssociatedKeyword
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
