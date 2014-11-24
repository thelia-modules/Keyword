<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Keyword\Event;

/**
 * Class KeywordUpdateEvent
 * @package Keyword\Event
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordUpdateEvent extends KeywordEvents
{
    protected $keyword_id;
    protected $keyword_group_id;

    protected $chapo;
    protected $description;
    protected $postscriptum;

    public function __construct($keyword_id)
    {
        $this->keyword_id = $keyword_id;
    }

    /**
     * @param mixed $chapo
     *
     * @return $this
     */
    public function setChapo($chapo)
    {
        $this->chapo = $chapo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * @param mixed $keyword_id
     *
     * @return $this
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
     * @param mixed $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $postscriptum
     *
     * @return $this
     */
    public function setPostscriptum($postscriptum)
    {
        $this->postscriptum = $postscriptum;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostscriptum()
    {
        return $this->postscriptum;
    }

    /**
     * @return mixed
     */
    public function getKeywordGroupId()
    {
        return $this->keyword_group_id;
    }

    /**
     * @param mixed $keyword_group_id
     */
    public function setKeywordGroupId($keyword_group_id)
    {
        $this->keyword_group_id = $keyword_group_id;

        return $this;
    }

}
