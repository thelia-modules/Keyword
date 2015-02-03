<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*	email : info@thelia.net                                                      */
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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.     */
/*                                                                                   */
/*************************************************************************************/

namespace Keyword\Loop;

use Keyword\Model\KeywordQuery;
use Keyword\Model\Map\CategoryAssociatedKeywordTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\Collection\ObjectCollection;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Category;
use Thelia\Core\Template\Element\LoopResult;

use Thelia\Model\CategoryQuery;
use Thelia\Model\Map\CategoryTableMap;
use Thelia\Type\TypeCollection;
use Thelia\Type;

/**
 *
 * KeywordCategory loop
 *
 *
 * Class KeywordCategory
 * @package Keyword\Loop
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordCategory extends Category
{
    protected function getArgDefinitions()
    {
        $argument = parent::getArgDefinitions();

        $argument
            ->addArgument(
                new Argument(
                    'keyword',
                    new TypeCollection(
                        new Type\AlphaNumStringListType()
                    )
                )

            )
            ->addArgument(
                new Argument(
                    'association_order',
                    new TypeCollection(
                        new Type\EnumListType(array('manual', 'manual_reverse', 'random'))
                    ),
                    'manual'
                )
            );

        return $argument;

    }

    public function buildModelCriteria()
    {
        $search = parent::buildModelCriteria();

        $keyword = KeywordQuery::create();

        /** @var ObjectCollection $results */
        $results = $keyword
            ->findByCode($this->getKeyword())
        ;

        // If Keyword doesn't exist
        if (true === $results->isEmpty()) {
            return null;
        }

        $categoryIds = array();
        $keywordListId = array();
        $keywordIds = $results->getData();

        foreach ($keywordIds as $keyword) {
            $keywordListId[] = $keyword->getId();
        }

        $keywordListId = implode(',', $keywordListId);

        foreach ($results as $result) {
            foreach ($result->getCategories() as $category) {
                $categoryIds[] = $category->getId();
            }
        }

        $categoryIds = implode(',', $categoryIds);

        if ($categoryIds) {
            $join = new Join();
            $join->addExplicitCondition(
                CategoryTableMap::TABLE_NAME,
                'ID',
                'category',
                CategoryAssociatedKeywordTableMap::TABLE_NAME,
                'category_id',
                'category_associated_keyword'
            );
            $join->setJoinType(Criteria::INNER_JOIN);

            $search->addJoinObject($join, 'category_associated_keyword_join');
            $search->addJoinCondition(
                'category_associated_keyword_join',
                'category_associated_keyword.keyword_id IN (' . $keywordListId . ')'
            );
            $search->addJoinCondition(
                'category_associated_keyword_join',
                'category_associated_keyword.category_id IN (' . $categoryIds . ')'
            );
            $search->withColumn('category_associated_keyword.position', 'category_position');
            $search->distinct();

            $orders = $this->getAssociation_order();

            foreach ($orders as $order) {
                switch ($order) {
                    case "manual":
                        $search->clearOrderByColumns();
                        $search->addAscendingOrderByColumn('category_position');
                        break;
                    case "manual_reverse":
                        $search->clearOrderByColumns();
                        $search->addDescendingOrderByColumn('category_position');
                        break;
                    case "random":
                        $search->clearOrderByColumns();
                        $search->addAscendingOrderByColumn('RAND()');
                        break(2);
                }
            }
        } else {
            return null;
        }

        return $search;

    }

    public function parseResults(LoopResult $results)
    {
        foreach ($results->getResultDataCollection() as $category) {

            $loopResultRow = new LoopResultRow($category);

            $loopResultRow
                ->set("ID"                      , $category->getId())
                ->set("IS_TRANSLATED"           , $category->getVirtualColumn('IS_TRANSLATED'))
                ->set("LOCALE"                  , $this->locale)
                ->set("TITLE"                   , $category->getVirtualColumn('i18n_TITLE'))
                ->set("CHAPO"                   , $category->getVirtualColumn('i18n_CHAPO'))
                ->set("DESCRIPTION"             , $category->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("POSTSCRIPTUM"            , $category->getVirtualColumn('i18n_POSTSCRIPTUM'))
                ->set("PARENT"                  , $category->getParent())
                ->set("URL"                     , $category->getUrl($this->locale))
                ->set("META_TITLE"              , $category->getVirtualColumn('i18n_META_TITLE'))
                ->set("META_DESCRIPTION"        , $category->getVirtualColumn('i18n_META_DESCRIPTION'))
                ->set("META_KEYWORDS"           , $category->getVirtualColumn('i18n_META_KEYWORDS'))
                ->set("VISIBLE"                 , $category->getVisible() ? "1" : "0")
                ->set("POSITION"                , $category->getPosition())
                ->set("CATEGORY_POSITION"       , $category->getVirtualColumn('category_position'))

            ;

            if ($this->getNeedCountChild()) {
                $loopResultRow->set("CHILD_COUNT", $category->countChild());
            }

            if ($this->getNeedProductCount()) {
                $loopResultRow->set("PRODUCT_COUNT", $category->countAllProducts());
            }

            if ($this->getBackend_context() || $this->getWithPrevNextInfo()) {
                // Find previous and next category
                $previous = CategoryQuery::create()
                    ->filterByParent($category->getParent())
                    ->filterByPosition($category->getPosition(), Criteria::LESS_THAN)
                    ->orderByPosition(Criteria::DESC)
                    ->findOne()
                ;

                $next = CategoryQuery::create()
                    ->filterByParent($category->getParent())
                    ->filterByPosition($category->getPosition(), Criteria::GREATER_THAN)
                    ->orderByPosition(Criteria::ASC)
                    ->findOne()
                ;

                $loopResultRow
                    ->set("HAS_PREVIOUS"            , $previous != null ? 1 : 0)
                    ->set("HAS_NEXT"                , $next != null ? 1 : 0)

                    ->set("PREVIOUS"                , $previous != null ? $previous->getId() : -1)
                    ->set("NEXT"                    , $next != null ? $next->getId() : -1)
                ;
            }

            $results->addRow($loopResultRow);
        }

        return $results;
    }

}
