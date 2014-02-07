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
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Category;
use Thelia\Core\Template\Element\LoopResult;

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

        $argument->addArgument(
            new Argument(
                'keyword',
                new TypeCollection(
                    new Type\AlphaNumStringListType()
                )
            )
        );

        return $argument;

    }

    public function buildModelCriteria()
    {
        $search = parent::buildModelCriteria();

        $keyword = KeywordQuery::create();
        $results = $keyword
            ->findByCode($this->getKeyword())
        ;

        // If Keyword doesn't exist
        if (true === $results->isEmpty()) {
            return null;
        }

        $categoryIds = array();

        foreach ($results as $result) {
            // If any product is associated with keyword
            if (true === $result->getCategories()->isEmpty()) {
                return null;
            }

            foreach ($result->getCategories() as $category) {
                $categoryIds[] = $category->getId();
            }
        }

        if (!empty($categoryIds)) {
            $search->filterById($categoryIds, Criteria::IN);
        }

        return $search;

    }

    public function parseResults(LoopResult $results)
    {
        $results = parent::parseResults($results);

        return $results;
    }

}
