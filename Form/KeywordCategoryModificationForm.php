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
namespace Keyword\Form;

use Keyword\Model\KeywordQuery;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Thelia\Form\BaseForm;

class KeywordCategoryModificationForm extends BaseForm
{
    protected function buildForm()
    {

        $keywordList = array();
        foreach (KeywordQuery::create()->find() as $keyword) {
            $keywordList[$keyword->getId()] = $keyword->getId();
        }

        $this->formBuilder
            ->add("keyword_list", ChoiceType::class, array(
                "choices" => $keywordList,
                "multiple" => true
            ))
        ;
    }

    public static function getName()
    {
        return 'keyword_category_modification';
    }
}
