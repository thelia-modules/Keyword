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

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Folder;
use Thelia\Model\Content;

/**
 * Class KeywordAssocationEvent
 * @package Keyword\Event
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordAssociationEvent extends ActionEvent
{
    //base parameters for creating new customer
    protected $keyword_list;

    /**
     * @var \Thelia\Model\Folder
     */
    protected $folder;

    /**
     * @var \Thelia\Model\Content
     */
    protected $content;

    /**
     * @param array  $keyword_list     the list of keywords
     */
    public function __construct($keyword_list)
    {
        $this->keyword_list = $keyword_list;
    }

    /**
     * @param Folder $folder
     */
    public function setFolder(Folder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    public function getKeywordList(){
        return $this->keyword_list;
    }

    public function getFolder(){
        return $this->folder;
    }

    public function getContent(){
        return $this->content;
    }

}
