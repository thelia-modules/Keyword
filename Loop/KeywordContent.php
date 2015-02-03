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
use Keyword\Model\Map\ContentAssociatedKeywordTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\Collection\ObjectCollection;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Content;
use Thelia\Core\Template\Element\LoopResult;

use Thelia\Model\Map\ContentTableMap;
use Thelia\Type\TypeCollection;
use Thelia\Type;

/**
 *
 * Keyword loop
 *
 *
 * Class Keyword
 * @package Keyword\Loop
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordContent extends Content
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

        $contentIds = array();
        $keywordListId = array();
        $keywordIds = $results->getData();

        foreach ($keywordIds as $keyword) {
            $keywordListId[] = $keyword->getId();
        }

        $keywordListId = implode(',', $keywordListId);

        foreach ($results as $result) {
            foreach ($result->getContents() as $content) {
                $contentIds[] = $content->getId();
            }
        }

        $contentIds = implode(',', $contentIds);

        if ($contentIds) {
            $join = new Join();
            $join->addExplicitCondition(
                ContentTableMap::TABLE_NAME,
                'ID',
                'content',
                ContentAssociatedKeywordTableMap::TABLE_NAME,
                'content_id',
                'content_associated_keyword'
            );
            $join->setJoinType(Criteria::INNER_JOIN);

            $search->addJoinObject($join, 'content_associated_keyword_join');
            $search->addJoinCondition(
                'content_associated_keyword_join',
                'content_associated_keyword.keyword_id IN (' . $keywordListId . ')'
            );
            $search->addJoinCondition(
                'content_associated_keyword_join',
                'content_associated_keyword.content_id IN (' . $contentIds . ')'
            );
            $search->withColumn('content_associated_keyword.position', 'content_position');
            $search->distinct();

            $orders = $this->getAssociation_order();

            foreach ($orders as $order) {
                switch ($order) {
                    case "manual":
                        $search->clearOrderByColumns();
                        $search->addAscendingOrderByColumn('content_position');
                        break;
                    case "manual_reverse":
                        $search->clearOrderByColumns();
                        $search->addDescendingOrderByColumn('content_position');
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
        foreach ($results->getResultDataCollection() as $content) {

            $loopResultRow = new LoopResultRow($content);
            $defaultFolderId = $content->getDefaultFolderId();
            $loopResultRow
                ->set("ID"                  , $content->getId())
                ->set("IS_TRANSLATED"       , $content->getVirtualColumn('IS_TRANSLATED'))
                ->set("LOCALE"              , $this->locale)
                ->set("TITLE"               , $content->getVirtualColumn('i18n_TITLE'))
                ->set("CHAPO"               , $content->getVirtualColumn('i18n_CHAPO'))
                ->set("DESCRIPTION"         , $content->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("POSTSCRIPTUM"        , $content->getVirtualColumn('i18n_POSTSCRIPTUM'))
                ->set("URL"                 , $content->getUrl($this->locale))
                ->set("META_TITLE"          , $content->getVirtualColumn('i18n_META_TITLE'))
                ->set("META_DESCRIPTION"    , $content->getVirtualColumn('i18n_META_DESCRIPTION'))
                ->set("META_KEYWORDS"       , $content->getVirtualColumn('i18n_META_KEYWORDS'))
                ->set("POSITION"            , $content->getPosition())
                ->set("DEFAULT_FOLDER"      , $defaultFolderId)
                ->set("VISIBLE"             , $content->getVisible())
                ->set("CONTENT_POSITION"    , $content->getVirtualColumn('content_position'))
            ;

            $results->addRow($loopResultRow);
        }

        return $results;
    }

}
