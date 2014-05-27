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

use Keyword\Model\KeywordGroup;
use Thelia\Core\Event\ActionEvent;

/**
 *
 * This class contains all Keyword group events identifiers used by Keyword Core
 *
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */

class KeywordGroupEvents extends ActionEvent
{

    const KEYWORD_GROUP_CREATE            = 'keywordGroup.action.create';
    const KEYWORD_GROUP_UPDATE            = 'keywordGroup.action.update';
    const KEYWORD_GROUP_DELETE            = 'keywordGroup.action.delete';
    const KEYWORD_GROUP_TOGGLE_VISIBILITY = 'keywordGroup.action.toggleVisibility';
    const KEYWORD_GROUP_UPDATE_POSITION   = 'keywordGroup.action.updatePosition';

    protected $locale;
    protected $title;
    protected $code;
    protected $visible;
    protected $keywordGroup;

    public function __construct($title, $code, $visible, $locale)
    {
        $this->title = $title;
        $this->code = $code;
        $this->visible = $visible;
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param mixed $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $visible
     *
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param  \Keyword\Model\KeywordGroup $keywordGroup
     * @return $this
     */
    public function setKeywordGroup(KeywordGroup $keywordGroup)
    {
        $this->keywordGroup = $keywordGroup;

        return $this;
    }

    /**
     * @return \Keyword\Model\KeywordGroup
     */
    public function getKeywordGroup()
    {
        return $this->keywordGroup;
    }

    /**
     * check if keyword group exists
     *
     * @return bool
     */
    public function hasKeywordGroup()
    {
        return null !== $this->keywordGroup;
    }
}
