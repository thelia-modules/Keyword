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
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\StandardDescriptionFieldsTrait;

/**
 * Class KeywordModificationForm
 * @package Thelia\Form
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordModificationForm extends KeywordCreationForm
{
    use StandardDescriptionFieldsTrait;

    protected function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add("id", "hidden", array("constraints" => array(new GreaterThan(array('value' => 0)))))
            ->add("keyword_group_id", "integer", array(
                    "constraints" => array(
                        new GreaterThan(
                            array('value' => 0)
                        )
                    ),
                    'label' => Translator::getInstance()->trans('Change keyword group', array(), 'keyword'),
                    'label_attr' => array(
                        'for' => 'keyword_group_id'
                    )
                )
            )
        ;

        // Add standard description fields, excluding title and locale, which a re defined in parent class
        $this->addStandardDescFields(array('title', 'locale'));
    }

    public function verifyExistingCode($value, ExecutionContextInterface $context)
    {
        $keywordId = $this->getRequest()->get('keyword_id');
        $keywordUpdated = KeywordQuery::create()->findPk($keywordId);

        // If the sent code isn't identical to the keyword code being updated
        if ($keywordUpdated->getCode() !== $value) {

            // Check if code keyword with this code exist
            parent::verifyExistingCode($value, $context);
        }
    }

    public function getName()
    {
        return "admin_keyword_modification";
    }
}
