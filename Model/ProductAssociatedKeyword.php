<?php

namespace Keyword\Model;

use Keyword\Model\Base\ProductAssociatedKeyword as BaseProductAssociatedKeyword;
use Keyword\Model\Map\ProductAssociatedKeywordTableMap;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;

class ProductAssociatedKeyword extends BaseProductAssociatedKeyword
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
