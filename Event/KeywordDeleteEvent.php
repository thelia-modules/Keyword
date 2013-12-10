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
 * Class KeywordDeleteEvent
 * @package Keyword\Event
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordDeleteEvent extends KeywordEvents
{
    /**
     * @var int keyword id
     */
    protected $keyword_id;

    /**
     * @param int $keyword_id
     */
    public function __construct($keyword_id)
    {
        $this->keyword_id = $keyword_id;
    }

    /**
     * @param int $keyword_id
     */
    public function setKeywordId($keyword_id)
    {
        $this->keyword_id = $keyword_id;
    }

    /**
     * @return int
     */
    public function getKeywordId()
    {
        return $this->keyword_id;
    }

}
