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
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints;

class KeywordCreationForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add('title', 'text', array(
                    'constraints' => array(
                        new NotBlank()
                    ),
                    'label' => Translator::getInstance()->trans('Title'),
                    'label_attr' => array(
                        'for' => 'keyword_title'
                    )
                ))
            ->add('code', 'text', array(
                    'constraints' => array(
                        new NotBlank(),
                        new Callback(array(
                            "methods" => array(
                                array($this, "verifyExistingCode")
                            )
                        ))
                    ),
                    'label' => Translator::getInstance()->trans('Unique identifier', array(), 'keyword'),
                    'label_attr' => array(
                        'for' => 'keyword_code'
                    )
                ))
            ->add("keyword_group_id", "integer", array(
                    "constraints" => array(
                        new GreaterThan(array(
                            'value' => 0
                        ))
                    )
                ))
            ->add('visible', 'integer', array(
                    'label' => Translator::getInstance()->trans('Visible ?'),
                    'label_attr' => array(
                        'for' => 'keyword_visible'
                    )
                ))
            ->add("locale", "text", array(
                    "constraints" => array(
                        new NotBlank()
                    )
                ))

        ;
    }

    public function verifyExistingCode($value, ExecutionContextInterface $context)
    {
        $keyword = KeywordQuery::getKeywordByCode($value);
        if ($keyword) {
            $context->addViolation("This keyword identifier already exist.");
        }
    }

    public function getName()
    {
        return 'admin_keyword_creation';
    }
}
