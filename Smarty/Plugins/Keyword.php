<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*	    email : info@thelia.net                                                      */
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

namespace Keyword\Smarty\Plugins;

use Keyword\Model\CategoryAssociatedKeywordQuery;
use Keyword\Model\ContentAssociatedKeywordQuery;
use Keyword\Model\FolderAssociatedKeywordQuery;
use Keyword\Model\KeywordQuery;
use Keyword\Model\ProductAssociatedKeywordQuery;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class Keyword extends AbstractSmartyPlugin
{
    protected function genericHasKeyWord(array $params, string $paramName, string $queryFunction): bool
    {
        if (isset($params['keyword_code'], $params[$paramName])) {
            $keyword = KeywordQuery::getKeywordByCode($params['keyword_code']);

            return null !== $queryFunction($params[$paramName], $keyword?->getId());
        }

        return false;
    }

    /**
     * Check if folder is associated to keyword code
     * @param $params
     * @return bool
     */
    public function folderHasKeyword($params)
    {
        return $this->genericHasKeyWord(
            $params,
            'folder_id',
            'Keyword\Model\FolderAssociatedKeywordQuery::getFolderKeywordAssociation');
    }

    /**
     * Check if content is associated to keyword code
     * @param $params
     * @return bool
     */
    public function contentHasKeyword($params)
    {
        return $this->genericHasKeyWord(
            $params,
            'content_id',
            'Keyword\Model\ContentAssociatedKeywordQuery::getContentKeywordAssociation');
    }

    /**
     * Check if category is associated to keyword code
     * @param $params
     * @return bool
     */
    public function categoryHasKeyword($params)
    {
        return $this->genericHasKeyWord(
            $params,
            'category_id',
            'Keyword\Model\CategoryAssociatedKeywordQuery::getCategoryKeywordAssociation');
    }

    /**
     * Check if product is associated to keyword code
     * @param $params
     * @return bool
     */
    public function productHasKeyword($params)
    {
        return $this->genericHasKeyWord(
            $params,
            'product_id',
            'Keyword\Model\ProductAssociatedKeywordQuery::getProductKeywordAssociation');
    }

    public function getPluginDescriptors()
    {
        return array(
            new SmartyPluginDescriptor("function", "folder_has_keyword", $this, "folderHasKeyword"),
            new SmartyPluginDescriptor("function", "content_has_keyword", $this, "contentHasKeyword"),
            new SmartyPluginDescriptor("function", "category_has_keyword", $this, "categoryHasKeyword"),
            new SmartyPluginDescriptor("function", "product_has_keyword", $this, "productHasKeyword")
        );
    }
}
