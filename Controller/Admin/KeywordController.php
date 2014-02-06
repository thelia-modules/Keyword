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

use Keyword\Event\KeywordToggleVisibilityEvent;
use Keyword\Event\KeywordDeleteEvent;
use Keyword\Event\KeywordAssociationEvent;
use Keyword\Event\KeywordEvents;
use Keyword\Event\KeywordUpdateEvent;
use Keyword\Form\KeywordCategoryModificationForm;
use Keyword\Form\KeywordContentModificationForm;
use Keyword\Form\KeywordCreationForm;
use Keyword\Form\KeywordModificationForm;
use Keyword\Form\KeywordFolderModificationForm;
use Keyword\Form\KeywordProductModificationForm;
use Keyword\Model\KeywordQuery;

use Propel\Runtime\Exception\PropelException;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Controller\Admin\unknown;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\Security\AccessManager;
use Thelia\Model\Base\FolderQuery;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\ProductQuery;

/**
 * Class KeywordController
 * @package Keyword\Controller\Admin
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class KeywordController extends AbstractCrudController
{
    public function __construct()
    {
        parent::__construct(
            'keyword',
            'manual',
            'keyword_order',

            'admin.keyword',

            KeywordEvents::KEYWORD_CREATE,
            KeywordEvents::KEYWORD_UPDATE,
            KeywordEvents::KEYWORD_DELETE,
            KeywordEvents::KEYWORD_TOGGLE_VISIBILITY,
            KeywordEvents::KEYWORD_UPDATE_POSITION
        );
    }

    public function viewAction(){

        if(null !== $this->getExistingObject()){
            $keyword = $this->getExistingObject();
            return $this->render('keyword-view', array('keyword_id' => $keyword->getId()));
        }
    }

    public function updateKeywordFolderAssociation($folder_id)
    {
        if (null !== $response = $this->checkAuth(array(), array('Keyword'), AccessManager::UPDATE)) {
            return $response;
        }

        /** @var KeywordFolderModificationForm $keywordFolderUpdateForm */
        $keywordFolderUpdateForm = new KeywordFolderModificationForm($this->getRequest());

        $message = false;

        try {

            $folder = FolderQuery::create()->findPk($folder_id);

            if (null === $folder) {
                throw new \InvalidArgumentException(sprintf("%d folder id does not exist", $folder_id));
            }

            $form = $this->validateForm($keywordFolderUpdateForm);

            $event = $this->createEventInstance($form->getData());
            $event->setFolder($folder);

            $this->dispatch(KeywordEvents::KEYWORD_UPDATE_FOLDER_ASSOCIATION, $event);

            $this->redirectSuccess($keywordFolderUpdateForm);

        } catch (FormValidationException $e) {
            $message = sprintf("Please check your input: %s", $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf("Sorry, an error occured: %s", $e->getMessage()." ".$e->getFile());
        }

        if ($message !== false) {
            \Thelia\Log\Tlog::getInstance()->error(sprintf("Error during keyword folder association update process : %s.", $message));

            $keywordFolderUpdateForm->setErrorMessage($message);

            $this->getParserContext()
                ->addForm($keywordFolderUpdateForm)
                ->setGeneralError($message)
            ;
        }

        // Redirect to current folder
        $this->redirectToRoute(
            'admin.folders.update',
            array(),
            array('folder_id' => $folder_id, 'current_tab' => 'modules')
        );
    }

    public function updateKeywordContentAssociation($content_id)
    {

        if (null !== $response = $this->checkAuth(array(), array('Keyword'), AccessManager::UPDATE)) {
            return $response;
        }

        /** @var KeywordContentModificationForm $keywordContentUpdateForm */
        $keywordContentUpdateForm = new KeywordContentModificationForm($this->getRequest());

        $message = false;

        try {

            $content = ContentQuery::create()->findPk($content_id);

            if (null === $content) {
                throw new \InvalidArgumentException(sprintf("%d content id does not exist", $content_id));
            }

            $form = $this->validateForm($keywordContentUpdateForm);

            $event = $this->createEventInstance($form->getData());
            $event->setContent($content);

            $this->dispatch(KeywordEvents::KEYWORD_UPDATE_CONTENT_ASSOCIATION, $event);

            $this->redirectSuccess($keywordContentUpdateForm);

        } catch (FormValidationException $e) {
            $message = sprintf("Please check your input: %s", $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf("Sorry, an error occured: %s", $e->getMessage()." ".$e->getFile());
        }

        if ($message !== false) {
            \Thelia\Log\Tlog::getInstance()->error(sprintf("Error during keyword content association update process : %s.", $message));

            $keywordContentUpdateForm->setErrorMessage($message);

            $this->getParserContext()
                ->addForm($keywordContentUpdateForm)
                ->setGeneralError($message)
            ;
        }

        // Redirect to current folder
        $this->redirectToRoute(
            'admin.content.update',
            array(),
            array('content_id' => $content_id, 'current_tab' => 'modules')
        );
    }

    public function updateKeywordCategoryAssociation($category_id)
    {

        if (null !== $response = $this->checkAuth(array(), array('Keyword'), AccessManager::UPDATE)) {
            return $response;
        }

        /** @var KeywordCategoryModificationForm $keywordCategoryUpdateForm */
        $keywordCategoryUpdateForm = new KeywordCategoryModificationForm($this->getRequest());

        $message = false;

        try {

            $category = CategoryQuery::create()->findPk($category_id);

            if (null === $category) {
                throw new \InvalidArgumentException(sprintf("%d category id does not exist", $category_id));
            }

            $form = $this->validateForm($keywordCategoryUpdateForm);

            $event = $this->createEventInstance($form->getData());
            $event->setCategory($category);

            $this->dispatch(KeywordEvents::KEYWORD_UPDATE_CATEGORY_ASSOCIATION, $event);

            $this->redirectSuccess($keywordCategoryUpdateForm);

        } catch (FormValidationException $e) {
            $message = sprintf("Please check your input: %s", $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf("Sorry, an error occured: %s", $e->getMessage()." ".$e->getFile());
        }

        if ($message !== false) {
            \Thelia\Log\Tlog::getInstance()->error(sprintf("Error during keyword category association update process : %s.", $message));

            $keywordCategoryUpdateForm->setErrorMessage($message);

            $this->getParserContext()
                ->addForm($keywordCategoryUpdateForm)
                ->setGeneralError($message)
            ;
        }

        // Redirect to current folder
        $this->redirectToRoute(
            'admin.categories.update',
            array(),
            array('category_id' => $category_id, 'current_tab' => 'modules')
        );
    }

    public function updateKeywordProductAssociation($product_id)
    {

        if (null !== $response = $this->checkAuth(array(), array('Keyword'), AccessManager::UPDATE)) {
            return $response;
        }

        /** @var KeywordProductModificationForm $keywordProductUpdateForm */
        $keywordProductUpdateForm = new KeywordProductModificationForm($this->getRequest());

        $message = false;

        try {

            $product = ProductQuery::create()->findPk($product_id);

            if (null === $product) {
                throw new \InvalidArgumentException(sprintf("%d product id does not exist", $product_id));
            }

            $form = $this->validateForm($keywordProductUpdateForm);

            $event = $this->createEventInstance($form->getData());
            $event->setProduct($product);

            $this->dispatch(KeywordEvents::KEYWORD_UPDATE_PRODUCT_ASSOCIATION, $event);

            $this->redirectSuccess($keywordProductUpdateForm);

        } catch (FormValidationException $e) {
            $message = sprintf("Please check your input: %s", $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf("Sorry, an error occured: %s", $e->getMessage()." ".$e->getFile());
        }

        if ($message !== false) {
            \Thelia\Log\Tlog::getInstance()->error(sprintf("Error during keyword product association update process : %s.", $message));

            $keywordProductUpdateForm->setErrorMessage($message);

            $this->getParserContext()
                ->addForm($keywordProductUpdateForm)
                ->setGeneralError($message)
            ;
        }

        // Redirect to current folder
        $this->redirectToRoute(
            'admin.products.update',
            array(),
            array('product_id' => $product_id, 'current_tab' => 'modules')
        );
    }

    /**
     * @param $positionChangeMode
     * @param $positionValue
     * @return UpdatePositionEvent|void
     */
    protected function createUpdatePositionEvent($positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
            $this->getRequest()->get('keyword_id', null),
            $positionChangeMode,
            $positionValue
        );
    }

    /**
     * @param $data
     * @return \Keyword\Event\KeywordAssociationEvent
     */
    private function createEventInstance($data)
    {

        $keywordAssociationEvent = new KeywordAssociationEvent(
            empty($data["keyword_list"])?null:$data["keyword_list"]
        );

        return $keywordAssociationEvent;
    }

    /**
     * Return the creation form for this object
     */
    protected function getCreationForm()
    {
        return new KeywordCreationForm($this->getRequest());
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm()
    {
        return new KeywordModificationForm($this->getRequest());
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param unknown $object
     * @return \Keyword\Form\KeywordModificationForm
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
        return new KeywordModificationForm($this->getRequest(), "form", $data);
    }

    /**
     * Creates the creation event with the provided form data
     *
     * @param unknown $formData
     * @return \Keyword\Event\KeywordEvents
     */
    protected function getCreationEvent($formData)
    {

        $keywordCreateEvent = new KeywordEvents($formData['title'], $formData['code'], $formData['visible'], $formData['locale']);

        return $keywordCreateEvent;

    }

    /**
     * Creates the update event with the provided form data
     *
     * @param unknown $formData
     */
    protected function getUpdateEvent($formData)
    {
        $keywordUpdateEvent = new KeywordUpdateEvent($formData['id']);

        $keywordUpdateEvent
            ->setLocale($formData['locale'])
            ->setTitle($formData['title'])
            ->setCode($formData['code'])
            ->setChapo($formData['chapo'])
            ->setDescription($formData['description'])
            ->setPostscriptum($formData['postscriptum'])
            ->setVisible($formData['visible']);

        return $keywordUpdateEvent;
    }

    /**
     * @return KeywordToggleVisibilityEvent|void
     */
    protected function createToggleVisibilityEvent()
    {
        return new KeywordToggleVisibilityEvent($this->getExistingObject());
    }

    /**
     * Creates the delete event with the provided form data
     */
    protected function getDeleteEvent()
    {
        return new KeywordDeleteEvent($this->getRequest()->get('keyword_id'), 0);
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     *
     * @param \Keyword\Event\KeywordEvents $event
     * @return bool
     */
    protected function eventContainsObject($event)
    {
        return $event->hasKeyword();
    }

    /**
     * Get the created object from an event.
     *
     * @param unknown $createEvent
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
        $keyword = KeywordQuery::create()
            ->findOneById($this->getRequest()->get('keyword_id', 0));

        if (null !== $keyword) {
            $keyword->setLocale($this->getCurrentEditionLocale());
        }

        return $keyword;

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
     * @param unknown $currentOrder , if any, null otherwise.
     */
    protected function renderListTemplate($currentOrder)
    {
        // TODO: Implement renderListTemplate() method.
    }

    protected function getEditionArguments()
    {
        return array(
            'keyword_id' => $this->getRequest()->get('keyword_id', 0),
            'current_tab' => $this->getRequest()->get('current_tab', 'general')
        );
    }

    /**
     * Render the edition template
     */
    protected function renderEditionTemplate()
    {
        return $this->render('keyword-edit', $this->getEditionArguments());
    }

    /**
     * Redirect to the edition template
     */
    protected function redirectToEditionTemplate()
    {
        $args = $this->getEditionArguments();
        $this->redirect('/admin/module/Keyword/update?keyword_id='.$args['keyword_id'].'&current_tab='.$args['current_tab']);
    }

    /**
     * Redirect to the list template
     */
    protected function redirectToListTemplate()
    {
        $this->redirect('/admin/module/Keyword');
    }

    protected function performAdditionalUpdateAction($updateEvent)
    {
        if ($this->getRequest()->get('save_mode') != 'stay') {
            $this->redirectToListTemplate();
        }
    }
}