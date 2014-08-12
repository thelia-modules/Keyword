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

use Keyword\Model\Base\Keyword;
use Thelia\Core\Event\ActionEvent;

/**
 *
 * This class contains all Keyword events identifiers used by Keyword Core
 *
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */

class KeywordEvents extends ActionEvent
{

    const KEYWORD_CREATE            = 'keyword.action.create';
    const KEYWORD_UPDATE            = 'keyword.action.update';
    const KEYWORD_DELETE            = 'keyword.action.delete';
    const KEYWORD_TOGGLE_VISIBILITY = 'keyword.action.toggleVisibility';
    const KEYWORD_UPDATE_POSITION   = 'keyword.action.updatePosition';
    const KEYWORD_OBJECT_UPDATE_POSITION = 'keyword.action.updateObjectPosition';

    const BEFORE_KEYWORD_ASSOCIATE_FOLDER                     = 'keyword.action.beforeKeywordAssociateFolder';
    const AFTER_KEYWORD_ASSOCIATE_FOLDER                      = 'keyword.action.afterKeywordAssociateFolder';

    /**
     * sent on keyword folder association update
     */
    const KEYWORD_UPDATE_FOLDER_ASSOCIATION = "keyword.action.updateFolderAssociation";

    const BEFORE_KEYWORD_ASSOCIATE_CONTENT                     = 'keyword.action.beforeKeywordAssociateContent';
    const AFTER_KEYWORD_ASSOCIATE_CONTENT                      = 'keyword.action.afterKeywordAssociateContent';

    /**
     * sent on keyword content association update
     */
    const KEYWORD_UPDATE_CONTENT_ASSOCIATION = "keyword.action.updateContentAssociation";

    const BEFORE_KEYWORD_ASSOCIATE_CATEGORY                     = 'keyword.action.beforeKeywordAssociateCategory';
    const AFTER_KEYWORD_ASSOCIATE_CATEGORY                      = 'keyword.action.afterKeywordAssociateCategory';

    /**
     * sent on keyword category association update
     */
    const KEYWORD_UPDATE_CATEGORY_ASSOCIATION = "keyword.action.updateCategoryAssociation";

    const BEFORE_KEYWORD_ASSOCIATE_PRODUCT                     = 'keyword.action.beforeKeywordAssociateProduct';
    const AFTER_KEYWORD_ASSOCIATE_PRODUCT                      = 'keyword.action.afterKeywordAssociateProduct';

    /**
     * sent on keyword product association update
     */
    const KEYWORD_UPDATE_PRODUCT_ASSOCIATION = "keyword.action.updateProductAssociation";

    protected $locale;
    protected $title;
    protected $code;
    protected $visible;
    protected $keyword;
    protected $keywordGroupId;

    public function __construct($title, $code, $visible, $locale, $keywordGroupId)
    {
        $this->title = $title;
        $this->code = $code;
        $this->visible = $visible;
        $this->locale = $locale;
        $this->keywordGroupId = $keywordGroupId;
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
     * @param mixed $keywordGroupId
     */
    public function setKeywordGroupId($keywordGroupId)
    {
        $this->keywordGroupId = $keywordGroupId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getKeywordGroupId()
    {
        return $this->keywordGroupId;
    }

    /**
     * @param  \Keyword\Model\Keyword $keyword
     * @return $this
     */
    public function setKeyword(Keyword $keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * @return \Keyword\Model\Keyword
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * check if keyword exists
     *
     * @return bool
     */
    public function hasKeyword()
    {
        return null !== $this->keyword;
    }
}
