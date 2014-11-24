<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia                                                                       */
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
/*      along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Keyword\Controller\Admin;

use Keyword\Event\KeywordGroupDeleteEvent;
use Keyword\Event\KeywordGroupEvents;
use Keyword\Event\KeywordGroupToggleVisibilityEvent;
use Keyword\Event\KeywordGroupUpdateEvent;
use Keyword\Form\KeywordGroupCreationForm;
use Keyword\Form\KeywordGroupModificationForm;
use Keyword\Model\KeywordGroupQuery;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Controller\Admin\unknown;
use Thelia\Core\Event\UpdatePositionEvent;

/**
 * Class KeywordGroupController
 * @package Keyword\Controller\Admin
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordGroupController extends AbstractCrudController
{

    public function __construct()
    {
        parent::__construct(
            'keywordGroup',
            'manual',
            'keyword_group_order',

            'admin.keyword.group',

            KeywordGroupEvents::KEYWORD_GROUP_CREATE,
            KeywordGroupEvents::KEYWORD_GROUP_UPDATE,
            KeywordGroupEvents::KEYWORD_GROUP_DELETE,
            KeywordGroupEvents::KEYWORD_GROUP_TOGGLE_VISIBILITY,
            KeywordGroupEvents::KEYWORD_GROUP_UPDATE_POSITION
        );
    }

    public function viewAction()
    {
        if (null !== $this->getExistingObject()) {
            $keywordGroup = $this->getExistingObject();

            return $this->render('keyword-group-view', array('keyword_group_id' => $keywordGroup->getId()));
        }
    }

    /**
     * Return the creation form for this object
     */
    protected function getCreationForm()
    {
        return new KeywordGroupCreationForm($this->getRequest());
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm()
    {
        return new KeywordGroupModificationForm($this->getRequest());
    }

    /**
     * @param $positionChangeMode
     * @param $positionValue
     * @return UpdatePositionEvent|void
     */
    protected function createUpdatePositionEvent($positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
            $this->getRequest()->get('keyword_group_id', null),
            $positionChangeMode,
            $positionValue
        );
    }

    /**
     * @return KeywordToggleVisibilityEvent|void
     */
    protected function createToggleVisibilityEvent()
    {
        return new KeywordGroupToggleVisibilityEvent($this->getExistingObject());
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param  unknown                                    $object
     * @return \Keyword\Form\KeywordGroupModificationForm
     */
    protected function hydrateObjectForm($object)
    {
        // Prepare the data that will hydrate the form
        $data = array(
            'id'           => $object->getId(),
            'locale'       => $object->getLocale(),
            'title'        => $object->getTitle(),
            'code'         => $object->getCode(),
            'chapo'        => $object->getChapo(),
            'description'  => $object->getDescription(),
            'postscriptum' => $object->getPostscriptum(),
            'visible'      => $object->getVisible()
        );

        // Setup the object form
        return new KeywordGroupModificationForm($this->getRequest(), "form", $data);
    }

    /**
     * Creates the creation event with the provided form data
     *
     * @param unknown $formData
     */
    protected function getCreationEvent($formData)
    {

        $keywordGroupCreateEvent = new KeywordGroupEvents(
            $formData['title'],
            $formData['code'],
            $formData['visible'],
            $formData['locale']
        );

        return $keywordGroupCreateEvent;

    }

    /**
     * Creates the update event with the provided form data
     *
     * @param unknown $formData
     */
    protected function getUpdateEvent($formData)
    {
        $keywordGroupUpdateEvent = new KeywordGroupUpdateEvent($formData['id']);

        $keywordGroupUpdateEvent
            ->setLocale($formData['locale'])
            ->setTitle($formData['title'])
            ->setCode($formData['code'])
            ->setChapo($formData['chapo'])
            ->setDescription($formData['description'])
            ->setPostscriptum($formData['postscriptum'])
            ->setVisible($formData['visible']);

        return $keywordGroupUpdateEvent;
    }

    /**
     * Creates the delete event with the provided form data
     */
    protected function getDeleteEvent()
    {
        return new KeywordGroupDeleteEvent($this->getRequest()->get('keyword_group_id'), 0);
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     *
     * @param  \Keyword\Event\KeywordGroupEvents $event
     * @return bool
     */
    protected function eventContainsObject($event)
    {
        return $event->hasKeywordGroup();
    }

    /**
     * Get the created object from an event.
     *
     * @param unknown $event
     */
    protected function getObjectFromEvent($event)
    {
        // TODO: Implement getObjectFromEvent() method.
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject()
    {
        $keywordGroup = KeywordGroupQuery::create()
            ->findOneById($this->getRequest()->get('keyword_group_id', 0));

        if (null !== $keywordGroup) {
            $keywordGroup->setLocale($this->getCurrentEditionLocale());
        }

        return $keywordGroup;

    }

    /**
     * Returns the object label form the object event (name, title, etc.)
     *
     * @param unknown $object
     */
    protected function getObjectLabel($object)
    {
        // TODO: Implement getObjectLabel() method.
    }

    /**
     * Returns the object ID from the object
     *
     * @param unknown $object
     */
    protected function getObjectId($object)
    {
        // TODO: Implement getObjectId() method.
    }

    /**
     * Render the main list template
     *
     * @param unknown $currentKeyword , if any, null otherwise.
     */
    protected function renderListTemplate($currentKeyword)
    {
        return $this->render('module-configure',
            array(
                'module_code' => 'Keyword',
                'code' => 'keyword',
                'keyword_group_order' => $currentKeyword
            ));
    }

    protected function getEditionArguments()
    {
        return array(
            'keyword_group_id' => $this->getRequest()->get('keyword_group_id', 0)
        );
    }

    /**
     * Render the edition template
     */
    protected function renderEditionTemplate()
    {
        return $this->render('keyword-group-edit', $this->getEditionArguments());
    }

    /**
     * Redirect to the edition template
     */
    protected function redirectToEditionTemplate()
    {
        $args = $this->getEditionArguments();

        return $this->generateRedirect('/admin/module/Keyword/group/update?keyword_group_id='.$args['keyword_group_id']);
    }

    /**
     * Redirect to the list template
     */
    protected function redirectToListTemplate()
    {
        return $this->generateRedirect('/admin/module/Keyword');
    }

    protected function performAdditionalUpdateAction($updateEvent)
    {
        if ($this->getRequest()->get('save_mode') != 'stay') {
            return $this->redirectToListTemplate();
        }
    }

}
