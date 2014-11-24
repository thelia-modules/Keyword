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

namespace Keyword\Loop;

use Keyword\Model\CategoryAssociatedKeywordQuery;
use Keyword\Model\ContentAssociatedKeywordQuery;
use Keyword\Model\FolderAssociatedKeywordQuery;
use Keyword\Model\KeywordQuery;
use Keyword\Model\ProductAssociatedKeywordQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;

use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;

use Thelia\Type\TypeCollection;
use Thelia\Type;
use Thelia\Type\BooleanOrBothType;

/**
 *
 * Keyword loop
 *
 *
 * Class Keyword
 * @package Keyword\Loop
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class Keyword extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createBooleanOrBothTypeArgument('visible', 1),
            new Argument(
                'keyword',
                new TypeCollection(
                    new Type\AlphaNumStringListType()
                )
            ),
            new Argument(
                'folder',
                new TypeCollection(
                    new Type\IntListType()
                )
            ),
            new Argument(
                'content',
                new TypeCollection(
                    new Type\IntListType()
                )
            ),
            new Argument(
                'category',
                new TypeCollection(
                    new Type\IntListType()
                )
            ),
            new Argument(
                'product',
                new TypeCollection(
                    new Type\IntListType()
                )
            ),
            new Argument(
                'order',
                new TypeCollection(
                    new Type\EnumListType(array('alpha', 'alpha-reverse', 'manual', 'manual_reverse', 'random', 'given_id'))
                ),
                'alpha'
            ),
            new Argument(
                'keyword_group',
                new TypeCollection(
                    new Type\IntListType()
                )
            )
        );
    }

    public function buildModelCriteria()
    {

        $search = KeywordQuery::create();

        /* If folder criteria search keyword associated to folder */
        if ($this->getFolder()) {
            $folder = FolderAssociatedKeywordQuery::create()->findByFolderId($this->getFolder());
            $search->filterByFolderAssociatedKeyword($folder);
        }

        /* If content criteria search keyword associated to content */
        if ($this->getContent()) {
            $content = ContentAssociatedKeywordQuery::create()->findByContentId($this->getContent());
            $search->filterByContentAssociatedKeyword($content);
        }

        /* If category criteria search keyword associated to category */
        if ($this->getCategory()) {
            $category = CategoryAssociatedKeywordQuery::create()->findByCategoryId($this->getCategory());
            $search->filterByCategoryAssociatedKeyword($category);
        }

        /* If product criteria search keyword associated to product */
        if ($this->getProduct()) {
            $product = ProductAssociatedKeywordQuery::create()->findByProductId($this->getProduct());
            $search->filterByProductAssociatedKeyword($product);
        }

        /* If keyword group criteria filter by keyword group code */
        if ($this->getKeywordGroup()) {
            $search->filterByKeywordGroupId($this->getKeywordGroup());
        }

        /* If keyword criteria filter by keyword code */
        if ($this->getKeyword()) {
            $search->filterByCode($this->getKeyword());
        }

        /* manage translations */
        $this->configureI18nProcessing($search);

        $id = $this->getId();

        if (!is_null($id)) {
            $search->filterById($id, Criteria::IN);
        }

        $visible = $this->getVisible();

        if ($visible !== BooleanOrBothType::ANY) $search->filterByVisible($visible ? 1 : 0);

        $orders  = $this->getOrder();

        foreach ($orders as $order) {
            switch ($order) {
                case "alpha":
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case "alpha-reverse":
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case "manual":
                    $search->orderByPosition(Criteria::ASC);
                    break;
                case "manual_reverse":
                    $search->orderByPosition(Criteria::DESC);
                    break;
                case "given_id":
                    if(null === $id)
                        throw new \InvalidArgumentException('Given_id order cannot be set without `id` argument');
                    foreach ($id as $singleId) {
                        $givenIdMatched = 'given_id_matched_' . $singleId;
                        $search->withColumn(ContentTableMap::ID . "='$singleId'", $givenIdMatched);
                        $search->orderBy($givenIdMatched, Criteria::DESC);
                    }
                    break;
                case "random":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break(2);
            }
        }

        return $search;

    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $keyword) {

            // Find previous and next category
            $previous = KeywordQuery::create()
                ->filterByPosition($keyword->getPosition(), Criteria::LESS_THAN)
                ->orderByPosition(Criteria::DESC)
                ->findOne()
            ;

            $next = KeywordQuery::create()
                ->filterByPosition($keyword->getPosition(), Criteria::GREATER_THAN)
                ->orderByPosition(Criteria::ASC)
                ->findOne()
            ;

            $loopResultRow = new LoopResultRow($keyword);

            $contentId = array();
            foreach ($keyword->getContents() as $content) {
                $contentId[] = $content->getId();
            }

            $folderId = array();
            foreach ($keyword->getFolders() as $folder) {
                $folderId[] = $folder->getId();
            }

            $categoryId = array();
            foreach ($keyword->getCategories() as $category) {
                $categoryId[] = $category->getId();
            }

            $productId = array();
            foreach ($keyword->getProducts() as $product) {
                $productId[] = $product->getId();
            }

            $loopResultRow->set("ID", $keyword->getId())
                ->set("KEYWORD_GROUP_ID", $keyword->getKeywordGroupId())
                ->set("IS_TRANSLATED",$keyword->getVirtualColumn('IS_TRANSLATED'))
                ->set("LOCALE",$this->locale)
                ->set("TITLE",$keyword->getVirtualColumn('i18n_TITLE'))
                ->set("CODE",$keyword->getCode())
                ->set("CHAPO", $keyword->getVirtualColumn('i18n_CHAPO'))
                ->set("DESCRIPTION", $keyword->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("POSTSCRIPTUM", $keyword->getVirtualColumn('i18n_POSTSCRIPTUM'))
                ->set("POSITION", $keyword->getPosition())
                ->set("VISIBLE", $keyword->getVisible())
                ->set("CONTENTS_ASSOCIATION", $contentId)
                ->set("FOLDERS_ASSOCIATION", $folderId)
                ->set("CATEGORIES_ASSOCIATION", $categoryId)
                ->set("PRODUCTS_ASSOCIATION", $productId)

                ->set("HAS_PREVIOUS", $previous != null ? 1 : 0)
                ->set("HAS_NEXT"    , $next != null ? 1 : 0)

                ->set("PREVIOUS", $previous != null ? $previous->getId() : -1)
                ->set("NEXT"    , $next != null ? $next->getId() : -1)
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;

    }

}
