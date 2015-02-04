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

namespace Keyword\Action;

use Keyword\Event\KeywordAssociationEvent;
use Keyword\Event\KeywordDeleteEvent;
use Keyword\Event\KeywordEvents;

use Keyword\Event\KeywordToggleVisibilityEvent;
use Keyword\Event\KeywordUpdateEvent;
use Keyword\Event\KeywordUpdateObjectPositionEvent;
use Keyword\Model\Base\ProductAssociatedKeywordQuery;
use Keyword\Model\CategoryAssociatedKeyword;
use Keyword\Model\CategoryAssociatedKeywordQuery;
use Keyword\Model\ContentAssociatedKeyword;
use Keyword\Model\ContentAssociatedKeywordQuery;

use Keyword\Model\FolderAssociatedKeyword;
use Keyword\Model\FolderAssociatedKeywordQuery;

use Keyword\Model\KeywordQuery;
use Keyword\Model\ProductAssociatedKeyword;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\UpdatePositionEvent;

/**
 *
 * keyword class where all actions are managed
 *
 * Class Keyword
 * @package Keyword\Action
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class Keyword implements EventSubscriberInterface
{

    public function updateKeywordFolderAssociation(KeywordAssociationEvent $event)
    {
        // Folder to associate
        $folder = $event->getFolder();

        // Keyword to save to this folder
        $keywordListToSave = $event->getKeywordList();

        // Delete all association to this folder
        FolderAssociatedKeywordQuery::create()
            ->filterByFolderId($folder->getId())
            ->delete();

        if ($keywordListToSave !== null) {
            // Create all associations to this folder
            foreach ($keywordListToSave as $keywordId) {

                $keywordFolderAssociation = new FolderAssociatedKeyword();
                $keywordFolderAssociation
                    ->setFolderId($folder->getId())
                    ->setKeywordId($keywordId)
                    ->save();

            }
        }

    }

    public function updateKeywordContentAssociation(KeywordAssociationEvent $event)
    {
        // Content to associate
        $content = $event->getContent();

        // Keyword to save to this content
        $keywordListToSave = $event->getKeywordList();

        // Delete all association to this content
        ContentAssociatedKeywordQuery::create()
            ->filterByContentId($content->getId())
            ->delete();

        if ($keywordListToSave !== null) {
            // Create all associations to this folder
            foreach ($keywordListToSave as $keywordId) {

                $keywordFolderAssociation = new ContentAssociatedKeyword();
                $keywordFolderAssociation
                    ->setContentId($content->getId())
                    ->setKeywordId($keywordId)
                    ->save();

            }
        }

    }

    public function updateKeywordCategoryAssociation(KeywordAssociationEvent $event)
    {
        // Category to associate
        $category = $event->getCategory();

        // Keyword to save to this category
        $keywordListToSave = $event->getKeywordList();

        // Delete all association to this category
        CategoryAssociatedKeywordQuery::create()
            ->filterByCategoryId($category->getId())
            ->delete();

        if ($keywordListToSave !== null) {
            // Create all associations to this category
            foreach ($keywordListToSave as $keywordId) {

                $keywordCategoryAssociation = new CategoryAssociatedKeyword();
                $keywordCategoryAssociation
                    ->setCategoryId($category->getId())
                    ->setKeywordId($keywordId)
                    ->save();

            }
        }

    }

    public function updateKeywordProductAssociation(KeywordAssociationEvent $event)
    {
        // Product to associate
        $product = $event->getProduct();

        // Keyword to save to this product
        $keywordListToSave = $event->getKeywordList();

        // Delete all association to this product
        ProductAssociatedKeywordQuery::create()
            ->filterByProductId($product->getId())
            ->delete();

        if ($keywordListToSave !== null) {
            // Create all associations to this folder
            foreach ($keywordListToSave as $keywordId) {

                $keywordProductAssociation = new ProductAssociatedKeyword();
                $keywordProductAssociation
                    ->setProductId($product->getId())
                    ->setKeywordId($keywordId)
                    ->save();

            }
        }

    }

    public function updateKeywordPosition(UpdatePositionEvent $event)
    {
        if (null !== $keyword = KeywordQuery::create()->findPk($event->getObjectId())) {

            $keyword->setDispatcher($event->getDispatcher());

            switch ($event->getMode()) {
                case UpdatePositionEvent::POSITION_ABSOLUTE:
                    $keyword->changeAbsolutePosition($event->getPosition());
                    break;
                case UpdatePositionEvent::POSITION_DOWN:
                    $keyword->movePositionDown();
                    break;
                case UpdatePositionEvent::POSITION_UP:
                    $keyword->movePositionUp();
                    break;
            }
        }
    }

    public function createKeyword(KeywordEvents $event)
    {

        $keyword = new \Keyword\Model\Keyword();

        $keyword
            ->setLocale($event->getLocale())
            ->setTitle($event->getTitle())
            ->setCode($event->getCode())
            ->setVisible($event->getVisible())
            ->setKeywordGroupId($event->getKeywordGroupId());

        $keyword->create();

        $event->setKeyword($keyword);
    }

    public function deleteKeyword(KeywordDeleteEvent $event)
    {
        if (null !== $keyword = KeywordQuery::create()->findPk($event->getKeywordId())) {

            $keyword->delete();

            $event->setKeyword($keyword);
        }
    }

    /**
     * process update keyword
     *
     * @param KeywordUpdateEvent $event
     */
    public function updateKeyword(KeywordUpdateEvent $event)
    {
        if (null !== $keyword = KeywordQuery::create()->findPk($event->getKeywordId())) {

            $keyword
                ->setVisible($event->getVisible())
                ->setLocale($event->getLocale())
                ->setTitle($event->getTitle())
                ->setCode($event->getCode())
                ->setDescription($event->getDescription())
                ->setChapo($event->getChapo())
                ->setPostscriptum($event->getPostscriptum())
                ->setKeywordGroupId($event->getKeywordGroupId())
                ->save()
            ;

            $event->setKeyword($keyword);
        }
    }

    public function toggleVisibilityKeyword(KeywordToggleVisibilityEvent $event)
    {
        $keyword = $event->getKeyword();

        $keyword
            ->setVisible(!$keyword->getVisible())
            ->save();

        $event->setKeyword($keyword);

    }

    public function updateKeywordObjectPosition(KeywordUpdateObjectPositionEvent $event)
    {

        $object = null;

        if ($event->getObject() == $event::FOLDER_OBJECT) {
            if (null !== $keywordObjectAssociation = FolderAssociatedKeywordQuery::create()->filterByKeywordId($event->getKeywordId())->filterByFolderId($event->getObjectId())->findOne()) {
                $object = $keywordObjectAssociation;
            }
        } elseif ($event->getObject() == $event::CONTENT_OBJECT) {
            if (null !== $keywordObjectAssociation = ContentAssociatedKeywordQuery::create()->filterByKeywordId($event->getKeywordId())->filterByContentId($event->getObjectId())->findOne()) {
                $object = $keywordObjectAssociation;
            }
        } elseif ($event->getObject() == $event::CATEGORY_OBJECT) {
            if (null !== $keywordObjectAssociation = CategoryAssociatedKeywordQuery::create()->filterByKeywordId($event->getKeywordId())->filterByCategoryId($event->getObjectId())->findOne()) {
                $object = $keywordObjectAssociation;
            }
        } elseif ($event->getObject() == $event::PRODUCT_OBJECT) {
            if (null !== $keywordObjectAssociation = ProductAssociatedKeywordQuery::create()->filterByKeywordId($event->getKeywordId())->filterByProductId($event->getObjectId())->findOne()) {
                $object = $keywordObjectAssociation;
            }
        }

        if ($object !== null) {
            $object->setDispatcher($event->getDispatcher());

            switch ($event->getMode()) {
                case UpdatePositionEvent::POSITION_ABSOLUTE:
                    $object->changeAbsolutePosition($event->getPosition());
                    break;
                case UpdatePositionEvent::POSITION_DOWN:
                    $object->movePositionDown();
                    break;
                case UpdatePositionEvent::POSITION_UP:
                    $object->movePositionUp();
                    break;
            }
        }

    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            KeywordEvents::KEYWORD_UPDATE_FOLDER_ASSOCIATION    => array('updateKeywordFolderAssociation', 128),
            KeywordEvents::KEYWORD_UPDATE_CONTENT_ASSOCIATION   => array('updateKeywordContentAssociation', 128),
            KeywordEvents::KEYWORD_UPDATE_CATEGORY_ASSOCIATION  => array('updateKeywordCategoryAssociation', 128),
            KeywordEvents::KEYWORD_UPDATE_PRODUCT_ASSOCIATION   => array('updateKeywordProductAssociation', 128),
            KeywordEvents::KEYWORD_UPDATE_POSITION              => array('updateKeywordPosition', 128),
            KeywordEvents::KEYWORD_CREATE                       => array('createKeyword', 128),
            KeywordEvents::KEYWORD_UPDATE                       => array('updateKeyword', 128),
            KeywordEvents::KEYWORD_DELETE                       => array('deleteKeyword', 128),
            KeywordEvents::KEYWORD_TOGGLE_VISIBILITY            => array('toggleVisibilityKeyword', 128),
            KeywordEvents::KEYWORD_OBJECT_UPDATE_POSITION       => array('updateKeywordObjectPosition', 128)
        );
    }
}
