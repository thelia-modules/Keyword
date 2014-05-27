<?php

namespace Keyword\Model;

use Keyword\Model\Base\KeywordGroup as BaseKeywordGroup;
use Keyword\Model\Map\KeywordGroupTableMap;
use Propel\Runtime\Propel;

class KeywordGroup extends BaseKeywordGroup
{
    use \Thelia\Model\Tools\ModelEventDispatcherTrait;
    use \Thelia\Model\Tools\PositionManagementTrait;

    /**
     * Create a new keyword group.
     *
     * Here pre and post insert event are fired
     *
     * @throws \Exception
     */
    public function create()
    {
        $con = Propel::getWriteConnection(KeywordGroupTableMap::DATABASE_NAME);

        $con->beginTransaction();

        try {
            $this->save($con);
            $this->setPosition($this->getNextPosition())->save($con);
            $con->commit();

        } catch (\Exception $ex) {

            $con->rollback();
            throw $ex;
        }

        return $this;
    }
}
