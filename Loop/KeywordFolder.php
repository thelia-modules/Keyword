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
use Keyword\Model\Map\FolderAssociatedKeywordTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\Collection\ObjectCollection;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Folder;
use Thelia\Core\Template\Element\LoopResult;

use Thelia\Model\Map\FolderTableMap;
use Thelia\Type\TypeCollection;
use Thelia\Type;

/**
 *
 * KeywordFolder loop
 *
 *
 * Class KeywordFolder
 * @package Keyword\Loop
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordFolder extends Folder
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

        $folderIds = array();
        $keywordListId = array();
        $keywordIds = $results->getData();

        foreach ($keywordIds as $keyword) {
            $keywordListId[] = $keyword->getId();
        }

        $keywordListId = implode(',', $keywordListId);

        foreach ($results as $result) {
            foreach ($result->getFolders() as $folder) {
                $folderIds[] = $folder->getId();
            }
        }

        $folderIds = implode(',', $folderIds);

        if ($folderIds) {
            $join = new Join();
            $join->addExplicitCondition(
                FolderTableMap::TABLE_NAME,
                'ID',
                'folder',
                FolderAssociatedKeywordTableMap::TABLE_NAME,
                'folder_id',
                'folder_associated_keyword'
            );
            $join->setJoinType(Criteria::INNER_JOIN);

            $search->addJoinObject($join, 'folder_associated_keyword_join');
            $search->addJoinCondition(
                'folder_associated_keyword_join',
                'folder_associated_keyword.keyword_id IN (' . $keywordListId . ')'
            );
            $search->addJoinCondition(
                'folder_associated_keyword_join',
                'folder_associated_keyword.folder_id IN (' . $folderIds . ')'
            );
            $search->withColumn('folder_associated_keyword.position', 'folder_position');
            $search->distinct();

            $orders = $this->getAssociation_order();

            foreach ($orders as $order) {
                switch ($order) {
                    case "manual":
                        $search->clearOrderByColumns();
                        $search->addAscendingOrderByColumn('folder_position');
                        break;
                    case "manual_reverse":
                        $search->clearOrderByColumns();
                        $search->addDescendingOrderByColumn('folder_position');
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

        foreach ($results->getResultDataCollection() as $folder) {

            $loopResultRow = new LoopResultRow($folder);

            $loopResultRow
                ->set("ID"                  , $folder->getId())
                ->set("IS_TRANSLATED"       , $folder->getVirtualColumn('IS_TRANSLATED'))
                ->set("LOCALE"              , $this->locale)
                ->set("TITLE"               , $folder->getVirtualColumn('i18n_TITLE'))
                ->set("CHAPO"               , $folder->getVirtualColumn('i18n_CHAPO'))
                ->set("DESCRIPTION"         , $folder->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("POSTSCRIPTUM"        , $folder->getVirtualColumn('i18n_POSTSCRIPTUM'))
                ->set("PARENT"              , $folder->getParent())
                ->set("URL"                 , $folder->getUrl($this->locale))
                ->set("META_TITLE"          , $folder->getVirtualColumn('i18n_META_TITLE'))
                ->set("META_DESCRIPTION"    , $folder->getVirtualColumn('i18n_META_DESCRIPTION'))
                ->set("META_KEYWORDS"       , $folder->getVirtualColumn('i18n_META_KEYWORDS'))
                ->set("CHILD_COUNT"         , $folder->countChild())
                ->set("CONTENT_COUNT"       , $folder->countAllContents())
                ->set("VISIBLE"             , $folder->getVisible() ? "1" : "0")
                ->set("POSITION"            , $folder->getPosition())
                ->set("FOLDER_POSITION"     , $folder->getVirtualColumn('folder_position'))
            ;

            $results->addRow($loopResultRow);
        }

        return $results;
    }

}
