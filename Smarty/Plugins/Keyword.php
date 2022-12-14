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
use Thelia\Core\Template\Smarty\AbstractSmartyPlugin;
use Thelia\Core\Template\Smarty\SmartyPluginDescriptor;

class Keyword extends AbstractSmartyPlugin
{

    /**
     * Check if folder is associated to keyword code
     * @param $params
     * @return bool
     */
    public function folderHasKeyword($params)
    {
        $ret = false;

        if (isset($params['keyword_code']) && isset($params['folder_id'])) {

            $keyword = KeywordQuery::getKeywordByCode($params['keyword_code']);

            if (null !== $keyword) {
                $keywordId = $keyword->getId();
                $keywordFolderAssociation = FolderAssociatedKeywordQuery::getFolderKeywordAssociation($params['folder_id'], $keywordId);

                if (null !== $keywordFolderAssociation) {
                    $ret = true;
                } else {
                    $ret = false;
                }
            } else {
                $ret = false;
            }

        }

        return $ret;

    }

    /**
     * Check if content is associated to keyword code
     * @param $params
     * @return bool
     */
    public function contentHasKeyword($params)
    {
        $ret = false;

        if (isset($params['keyword_code']) && isset($params['content_id'])) {

            $keyword = KeywordQuery::getKeywordByCode($params['keyword_code']);

            if (null !== $keyword) {
                $keywordId = $keyword->getId();
                $keywordContentAssociation = ContentAssociatedKeywordQuery::getContentKeywordAssociation($params['content_id'], $keywordId);

                if (null !== $keywordContentAssociation) {
                    $ret = true;
                } else {
                    $ret = false;
                }
            } else {
                $ret = false;
            }

        }

        return $ret;

    }

    /**
     * Check if category is associated to keyword code
     * @param $params
     * @return bool
     */
    public function categoryHasKeyword($params)
    {
        $ret = false;

        if (isset($params['keyword_code']) && isset($params['category_id'])) {

            $keyword = KeywordQuery::getKeywordByCode($params['keyword_code']);

            if (null !== $keyword) {
                $keywordId = $keyword->getId();
                $keywordCategoryAssociation = CategoryAssociatedKeywordQuery::getCategoryKeywordAssociation($params['category_id'], $keywordId);

                if (null !== $keywordCategoryAssociation) {
                    $ret = true;
                } else {
                    $ret = false;
                }
            } else {
                $ret = false;
            }

        }

        return $ret;

    }

    /**
     * Check if product is associated to keyword code
     * @param $params
     * @return bool
     */
    public function productHasKeyword($params)
    {
        $ret = false;

        if (isset($params['keyword_code']) && isset($params['product_id'])) {

            $keyword = KeywordQuery::getKeywordByCode($params['keyword_code']);

            if (null !== $keyword) {
                $keywordId = $keyword->getId();
                $keywordProductAssociation = ProductAssociatedKeywordQuery::getProductKeywordAssociation($params['product_id'], $keywordId);

                if (null !== $keywordProductAssociation) {
                    $ret = true;
                } else {
                    $ret = false;
                }
            } else {
                $ret = false;
            }

        }

        return $ret;

    }

    /**
     * @return an array of SmartyPluginDescriptor
     */
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
