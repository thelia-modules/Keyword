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
use Keyword\Model\Map\ProductAssociatedKeywordTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\Collection\ObjectCollection;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Product;
use Thelia\Core\Template\Element\LoopResult;

use Thelia\Exception\TaxEngineException;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\ProductQuery;
use Thelia\Type\TypeCollection;
use Thelia\Type;

/**
 *
 * KeywordProduct loop
 *
 *
 * Class KeywordProduct
 * @package Keyword\Loop
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordProduct extends Product
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

        $productIds = array();
        $keywordListId = array();
        $keywordIds = $results->getData();

        foreach ($keywordIds as $keyword) {
            $keywordListId[] = $keyword->getId();
        }

        $keywordListId = implode(',', $keywordListId);

        foreach ($results as $result) {
            // If any content is associated with keyword
            if (true === $result->getProducts()->isEmpty()) {
                return null;
            }

            foreach ($result->getProducts() as $product) {
                $productIds[] = $product->getId();
            }
        }

        $productIds = implode(',', $productIds);

        $join = new Join();
        $join->addExplicitCondition(ProductTableMap::TABLE_NAME, 'ID', 'product', ProductAssociatedKeywordTableMap::TABLE_NAME, 'product_id', 'product_associated_keyword');
        $join->setJoinType(Criteria::INNER_JOIN);

        $search->addJoinObject($join, 'product_associated_keyword_join');
        $search->addJoinCondition('category_associated_keyword_join','category_associated_keyword.keyword_id IN ('.$keywordListId.')');
        $search->addJoinCondition('product_associated_keyword_join','product_associated_keyword.product_id IN ('.$productIds.')');
        $search->withColumn('product_associated_keyword.position', 'product_position');

        $orders = $this->getAssociation_order();

        foreach ($orders as $order) {
            switch ($order) {
                case "manual":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('product_position');
                    break;
                case "manual_reverse":
                    $search->clearOrderByColumns();
                    $search->addDescendingOrderByColumn('product_position');
                    break;
                case "random":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break(2);
            }
        }

        return $search;

    }

    public function parseResults(LoopResult $results)
    {
        $complex = $this->getComplex();
        if (true === $complex) {
            return $this->parseComplex($results);
        }

        $taxCountry = $this->container->get('thelia.taxEngine')->getDeliveryCountry();
        /** @var \Thelia\Core\Security\SecurityContext $securityContext */
        $securityContext = $this->container->get('thelia.securityContext');

        foreach ($results->getResultDataCollection() as $product) {

            $loopResultRow = new LoopResultRow($product);

            $price = $product->getVirtualColumn('price');

            if ($securityContext->hasCustomerUser() && $securityContext->getCustomerUser()->getDiscount() > 0) {
                $price = $price * (1-($securityContext->getCustomerUser()->getDiscount()/100));
            }

            try {
                $taxedPrice = $product->getTaxedPrice(
                    $taxCountry,
                    $price
                );
            } catch (TaxEngineException $e) {
                $taxedPrice = null;
            }
            $promoPrice = $product->getVirtualColumn('promo_price');

            if ($securityContext->hasCustomerUser() && $securityContext->getCustomerUser()->getDiscount() > 0) {
                $promoPrice = $promoPrice * (1-($securityContext->getCustomerUser()->getDiscount()/100));
            }
            try {
                $taxedPromoPrice = $product->getTaxedPromoPrice(
                    $taxCountry,
                    $promoPrice
                );
            } catch (TaxEngineException $e) {
                $taxedPromoPrice = null;
            }

            // Find previous and next product, in the default category.
            $default_category_id = $product->getDefaultCategoryId();

            $loopResultRow
                ->set("WEIGHT"                  , $product->getVirtualColumn('weight'))
                ->set("QUANTITY"                , $product->getVirtualColumn('quantity'))
                ->set("EAN_CODE"                , $product->getVirtualColumn('ean_code'))
                ->set("BEST_PRICE"              , $product->getVirtualColumn('is_promo') ? $promoPrice : $price)
                ->set("BEST_PRICE_TAX"          , $taxedPrice - $product->getVirtualColumn('is_promo') ? $taxedPromoPrice - $promoPrice : $taxedPrice - $price)
                ->set("BEST_TAXED_PRICE"        , $product->getVirtualColumn('is_promo') ? $taxedPromoPrice : $taxedPrice)
                ->set("PRICE"                   , $price)
                ->set("PRICE_TAX"               , $taxedPrice - $price)
                ->set("TAXED_PRICE"             , $taxedPrice)
                ->set("PROMO_PRICE"             , $promoPrice)
                ->set("PROMO_PRICE_TAX"         , $taxedPromoPrice - $promoPrice)
                ->set("TAXED_PROMO_PRICE"       , $taxedPromoPrice)
                ->set("IS_PROMO"                , $product->getVirtualColumn('is_promo'))
                ->set("IS_NEW"                  , $product->getVirtualColumn('is_new'))
                ->set("PRODUCT_POSITION"        , $product->getVirtualColumn('product_position'))
            ;

            $results->addRow($this->associateValues($loopResultRow, $product, $default_category_id));
        }

        return $results;
    }

    private function associateValues($loopResultRow, $product, $default_category_id)
    {
        $loopResultRow
            ->set("ID"                      , $product->getId())
            ->set("REF"                     , $product->getRef())
            ->set("IS_TRANSLATED"           , $product->getVirtualColumn('IS_TRANSLATED'))
            ->set("LOCALE"                  , $this->locale)
            ->set("TITLE"                   , $product->getVirtualColumn('i18n_TITLE'))
            ->set("CHAPO"                   , $product->getVirtualColumn('i18n_CHAPO'))
            ->set("DESCRIPTION"             , $product->getVirtualColumn('i18n_DESCRIPTION'))
            ->set("POSTSCRIPTUM"            , $product->getVirtualColumn('i18n_POSTSCRIPTUM'))
            ->set("URL"                     , $product->getUrl($this->locale))
            ->set("META_TITLE"              , $product->getVirtualColumn('i18n_META_TITLE'))
            ->set("META_DESCRIPTION"        , $product->getVirtualColumn('i18n_META_DESCRIPTION'))
            ->set("META_KEYWORDS"            , $product->getVirtualColumn('i18n_META_KEYWORDS'))
            ->set("PRODUCT_SALE_ELEMENT"    , $product->getVirtualColumn('pse_id'))
            ->set("POSITION"                , $product->getPosition())
            ->set("VISIBLE"                 , $product->getVisible() ? "1" : "0")
            ->set("TEMPLATE"                , $product->getTemplateId())
            ->set("DEFAULT_CATEGORY"        , $default_category_id)
            ->set("TAX_RULE_ID"             , $product->getTaxRuleId())

        ;


        if ($this->getBackend_context() || $this->getWithPrevNextInfo()) {
            // Find previous and next category
            $previous = ProductQuery::create()
                ->joinProductCategory()
                ->where('ProductCategory.category_id = ?', $default_category_id)
                ->filterByPosition($product->getPosition(), Criteria::LESS_THAN)
                ->orderByPosition(Criteria::DESC)
                ->findOne()
            ;

            $next = ProductQuery::create()
                ->joinProductCategory()
                ->where('ProductCategory.category_id = ?', $default_category_id)
                ->filterByPosition($product->getPosition(), Criteria::GREATER_THAN)
                ->orderByPosition(Criteria::ASC)
                ->findOne()
            ;

            $loopResultRow
                ->set("HAS_PREVIOUS"     , $previous != null ? 1 : 0)
                ->set("HAS_NEXT"         , $next != null ? 1 : 0)
                ->set("PREVIOUS"         , $previous != null ? $previous->getId() : -1)
                ->set("NEXT"             , $next != null ? $next->getId() : -1)
            ;
        }

        return $loopResultRow;
    }

}
