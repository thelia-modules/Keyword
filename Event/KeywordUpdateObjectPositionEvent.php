<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Keyword\Event;

use Thelia\Core\Event\ActionEvent;

class KeywordUpdateObjectPositionEvent extends ActionEvent
{

    const FOLDER_OBJECT = 'folder';
    const CONTENT_OBJECT = 'content';
    const CATEGORY_OBJECT = 'category';
    const PRODUCT_OBJECT = 'product';

    protected $keyword_id;
    protected $object;
    protected $object_id;
    protected $mode;
    protected $position;

    public function __construct($keyword_id, $object, $object_id, $mode, $position)
    {
        $this->keyword_id = $keyword_id;
        $this->object = $object;
        $this->object_id = $object_id;
        $this->mode = $mode;
        $this->position = $position;
    }

    /**
     * @param mixed $keyword_id
     */
    public function setKeywordId($keyword_id)
    {
        $this->keyword_id = $keyword_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getKeywordId()
    {
        return $this->keyword_id;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object_id
     */
    public function setObjectId($object_id)
    {
        $this->object_id = $object_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getObjectId()
    {
        return $this->object_id;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

}
