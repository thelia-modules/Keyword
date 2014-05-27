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

use Keyword\Event\KeywordGroupDeleteEvent;
use Keyword\Event\KeywordGroupEvents;

use Keyword\Event\KeywordGroupToggleVisibilityEvent;
use Keyword\Event\KeywordGroupUpdateEvent;
use Keyword\Model\KeywordGroupQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\UpdatePositionEvent;

/**
 *
 * keyword group class where all actions are managed
 *
 * Class KeywordGroup
 * @package Keyword\Action
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordGroup implements EventSubscriberInterface
{

    public function createKeywordGroup(KeywordGroupEvents $event)
    {
        $keywordGroup = new \Keyword\Model\KeywordGroup();

        $keywordGroup->setLocale($event->getLocale())
            ->setTitle($event->getTitle())
            ->setCode($event->getCode())
            ->setVisible($event->getVisible())
            ->create();

        $event->setKeywordGroup($keywordGroup);
    }

    /**
     * process update keyword group
     *
     * @param KeywordGroupUpdateEvent $event
     */
    public function updateKeywordGroup(KeywordGroupUpdateEvent $event)
    {
        if (null !== $keywordGroup = KeywordGroupQuery::create()->findPk($event->getKeywordGroupId())) {

            $keywordGroup
                ->setVisible($event->getVisible())
                ->setLocale($event->getLocale())
                ->setTitle($event->getTitle())
                ->setCode($event->getCode())
                ->setDescription($event->getDescription())
                ->setChapo($event->getChapo())
                ->setPostscriptum($event->getPostscriptum())
                ->save()
            ;

            $event->setKeywordGroup($keywordGroup);
        }
    }

    public function deleteKeywordGroup(KeywordGroupDeleteEvent $event)
    {
        if (null !== $keywordGroup = KeywordGroupQuery::create()->findPk($event->getKeywordGroupId())) {

            $keywordGroup->delete();

            $event->setKeywordGroup($keywordGroup);
        }
    }

    public function updateKeywordGroupPosition(UpdatePositionEvent $event)
    {
        if (null !== $keywordGroup = KeywordGroupQuery::create()->findPk($event->getObjectId())) {

            $keywordGroup->setDispatcher($event->getDispatcher());

            switch ($event->getMode()) {
                case UpdatePositionEvent::POSITION_ABSOLUTE:
                    $keywordGroup->changeAbsolutePosition($event->getPosition());
                    break;
                case UpdatePositionEvent::POSITION_DOWN:
                    $keywordGroup->movePositionDown();
                    break;
                case UpdatePositionEvent::POSITION_UP:
                    $keywordGroup->movePositionUp();
                    break;
            }
        }
    }

    public function toggleVisibilityKeywordGroup(KeywordGroupToggleVisibilityEvent $event)
    {
        $keywordGroup = $event->getKeywordGroup();

        $keywordGroup
            ->setVisible(!$keywordGroup->getVisible())
            ->save();

        $event->setKeywordGroup($keywordGroup);

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
            KeywordGroupEvents::KEYWORD_GROUP_CREATE                => array('createKeywordGroup', 128),
            KeywordGroupEvents::KEYWORD_GROUP_UPDATE                => array('updateKeywordGroup', 128),
            KeywordGroupEvents::KEYWORD_GROUP_DELETE                => array('deleteKeywordGroup', 128),
            KeywordGroupEvents::KEYWORD_GROUP_UPDATE_POSITION       => array('updateKeywordGroupPosition', 128),
            KeywordGroupEvents::KEYWORD_GROUP_TOGGLE_VISIBILITY     => array('toggleVisibilityKeywordGroup', 128)
        );
    }
}
