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
use Keyword\Event\KeywordUpdateObjectPositionEvent;
use Keyword\Form\KeywordCategoryModificationForm;
use Keyword\Form\KeywordContentModificationForm;
use Keyword\Form\KeywordCreationForm;
use Keyword\Form\KeywordModificationForm;
use Keyword\Form\KeywordFolderModificationForm;
use Keyword\Form\KeywordProductModificationForm;
use Keyword\Model\KeywordQuery;

use Propel\Runtime\Exception\PropelException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Log\Tlog;
use Thelia\Model\Base\FolderQuery;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\ProductQuery;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class KeywordController
 * @package Keyword\Controller\Admin
 * @Route("/admin", name="keyword")
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

    /**
     * @Route("/module/Keyword/view", name="view")
     */
    public function viewAction()
    {
        if (null !== $this->getExistingObject()) {
            $keyword = $this->getExistingObject();

            return $this->render('keyword-view', array('keyword_id' => $keyword->getId()));
        }

        return $this->pageNotFound();
    }

    /**
     * @Route("/folders/update/{folder_id}/keyword", name="update_keyword_folder_association")
     */
    public function updateKeywordFolderAssociation(
        EventDispatcherInterface $dispatcher,
        ParserContext            $parserContext,
                                 $folder_id
    )
    {
        if (null !== $response = $this->checkAuth(array(), array('Keyword'), AccessManager::UPDATE)) {
            return $response;
        }

        /** @var KeywordFolderModificationForm $keywordFolderUpdateForm */
        $keywordFolderUpdateForm = $this->createForm(KeywordFolderModificationForm::getName());

        try {

            $folder = FolderQuery::create()->findPk($folder_id);

            if (null === $folder) {
                throw new \InvalidArgumentException(sprintf("%d folder id does not exist", $folder_id));
            }

            $form = $this->validateForm($keywordFolderUpdateForm);

            $event = $this->createEventInstance($form->getData());
            $event->setFolder($folder);

            $dispatcher->dispatch($event, KeywordEvents::KEYWORD_UPDATE_FOLDER_ASSOCIATION);

            return $this->generateSuccessRedirect($keywordFolderUpdateForm);

        } catch (FormValidationException $e) {
            $message = sprintf("Please check your input: %s", $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
        }

        if ($message !== false) {
            Tlog::getInstance()->error(
                sprintf("Error during keyword folder association update process : %s.", $message)
            );

            $keywordFolderUpdateForm->setErrorMessage($message);

            $parserContext
                ->addForm($keywordFolderUpdateForm)
                ->setGeneralError($message);
        }

        return $this->generateErrorRedirect($keywordFolderUpdateForm);
    }

    /**
     * @Route("/content/update/{content_id}/keyword", name="update_keyword_content_association")
     */
    public function updateKeywordContentAssociation(
        EventDispatcherInterface $dispatcher,
        ParserContext            $parserContext,
                                 $content_id
    )
    {

        if (null !== $response = $this->checkAuth(array(), array('Keyword'), AccessManager::UPDATE)) {
            return $response;
        }

        /** @var KeywordContentModificationForm $keywordContentUpdateForm */
        $keywordContentUpdateForm = $this->createForm(KeywordContentModificationForm::getName());

        try {

            $content = ContentQuery::create()->findPk($content_id);

            if (null === $content) {
                throw new \InvalidArgumentException(sprintf("%d content id does not exist", $content_id));
            }

            $form = $this->validateForm($keywordContentUpdateForm);

            $event = $this->createEventInstance($form->getData());
            $event->setContent($content);

            $dispatcher->dispatch($event, KeywordEvents::KEYWORD_UPDATE_CONTENT_ASSOCIATION);

            return $this->generateSuccessRedirect($keywordContentUpdateForm);

        } catch (FormValidationException $e) {
            $message = sprintf("Please check your input: %s", $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
        }

        if ($message !== false) {
            \Thelia\Log\Tlog::getInstance()->error(
                sprintf("Error during keyword content association update process : %s.", $message)
            );

            $keywordContentUpdateForm->setErrorMessage($message);

            $parserContext
                ->addForm($keywordContentUpdateForm)
                ->setGeneralError($message);
        }

        // Redirect to current folder
        return $this->generateErrorRedirect($keywordContentUpdateForm);
    }

    /**
     * @Route("/categories/update/{category_id}/keyword", name="update_keyword_category_association") /
     */
    public function updateKeywordCategoryAssociation(
        EventDispatcherInterface $dispatcher,
        ParserContext            $parserContext,
                                 $category_id)
    {

        if (null !== $response = $this->checkAuth(array(), array('Keyword'), AccessManager::UPDATE)) {
            return $response;
        }

        /** @var KeywordCategoryModificationForm $keywordCategoryUpdateForm */
        $keywordCategoryUpdateForm = $this->createForm(KeywordCategoryModificationForm::getName());

        try {

            $category = CategoryQuery::create()->findPk($category_id);

            if (null === $category) {
                throw new \InvalidArgumentException(sprintf("%d category id does not exist", $category_id));
            }

            $form = $this->validateForm($keywordCategoryUpdateForm);

            $event = $this->createEventInstance($form->getData());
            $event->setCategory($category);

            $dispatcher->dispatch($event, KeywordEvents::KEYWORD_UPDATE_CATEGORY_ASSOCIATION);

            return $this->generateSuccessRedirect($keywordCategoryUpdateForm);

        } catch (FormValidationException $e) {
            $message = sprintf("Please check your input: %s", $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
        }

        if ($message !== false) {
            \Thelia\Log\Tlog::getInstance()->error(
                sprintf("Error during keyword category association update process : %s.", $message)
            );

            $keywordCategoryUpdateForm->setErrorMessage($message);

            $parserContext
                ->addForm($keywordCategoryUpdateForm)
                ->setGeneralError($message);
        }

        return $this->generateErrorRedirect($keywordCategoryUpdateForm);
    }

    /**
     * @Route("/product/update/{product_id}/keyword", name="update_keyword_product_association") /
     */
    public function updateKeywordProductAssociation(
        EventDispatcherInterface $dispatcher,
        ParserContext            $parserContext,
                                 $product_id
    )
    {
        if (null !== $response = $this->checkAuth(array(), array('Keyword'), AccessManager::UPDATE)) {
            return $response;
        }

        /** @var KeywordProductModificationForm $keywordProductUpdateForm */
        $keywordProductUpdateForm = $this->createForm(KeywordProductModificationForm::getName());

        try {

            $product = ProductQuery::create()->findPk($product_id);

            if (null === $product) {
                throw new \InvalidArgumentException(sprintf("%d product id does not exist", $product_id));
            }

            $form = $this->validateForm($keywordProductUpdateForm);

            $event = $this->createEventInstance($form->getData());
            $event->setProduct($product);

            $dispatcher->dispatch($event, KeywordEvents::KEYWORD_UPDATE_PRODUCT_ASSOCIATION);

            return $this->generateSuccessRedirect($keywordProductUpdateForm);

        } catch (FormValidationException $e) {
            $message = sprintf("Please check your input: %s", $e->getMessage());
        } catch (PropelException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = sprintf("Sorry, an error occured: %s", $e->getMessage() . " " . $e->getFile());
        }

        if ($message !== false) {
            \Thelia\Log\Tlog::getInstance()->error(
                sprintf("Error during keyword product association update process : %s.", $message)
            );

            $keywordProductUpdateForm->setErrorMessage($message);

            $parserContext
                ->addForm($keywordProductUpdateForm)
                ->setGeneralError($message);
        }

        return $this->generateErrorRedirect($keywordProductUpdateForm);
    }

    /**
     * Update keyword object position
     * @Route("/module/Keyword/{object}/update-position", name="update_object_position_action") /
     */
    public function updateObjectPositionAction(EventDispatcherInterface $dispatcher, Request $request)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE))
            return $response;

        try {
            $mode = $request->get('mode', null);

            if ($mode == 'up')
                $mode = UpdatePositionEvent::POSITION_UP;
            elseif ($mode == 'down')
                $mode = UpdatePositionEvent::POSITION_DOWN;
            else
                $mode = UpdatePositionEvent::POSITION_ABSOLUTE;

            $position = $request->get('position', null);
            $object = $request->get('object');

            $event = $this->createObjectUpdatePositionEvent($request, $mode, $position, $object);

            $dispatcher->dispatch($event, KeywordEvents::KEYWORD_OBJECT_UPDATE_POSITION);

        } catch (\Exception $ex) {
            // Any error
            return $this->errorPage($ex);
        }

        $keywordId = $request->get('keyword_id');

        return $this->generateRedirect('/module/Keyword/view?keyword_id=' . $keywordId);
    }

    /**
     * @param $positionChangeMode
     * @param $positionValue
     * @param $object
     */
    protected function createObjectUpdatePositionEvent(Request $request, $positionChangeMode, $positionValue, $object)
    {
        return new KeywordUpdateObjectPositionEvent(
            $request->get('keyword_id', null),
            $object,
            $request->get("$object" . '_id', null),
            $positionChangeMode,
            $positionValue
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
            empty($data["keyword_list"]) ? null : $data["keyword_list"]
        );

        return $keywordAssociationEvent;
    }

    /**
     * Return the creation form for this object
     */
    protected function getCreationForm()
    {
        return $this->createForm(KeywordCreationForm::getName());
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm()
    {
        return $this->createForm(KeywordModificationForm::getName());
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     */
    protected function hydrateObjectForm(ParserContext $parserContext, $object)
    {

        // Prepare the data that will hydrate the form
        $data = array(
            'id' => $object->getId(),
            'locale' => $object->getLocale(),
            'title' => $object->getTitle(),
            'code' => $object->getCode(),
            'chapo' => $object->getChapo(),
            'description' => $object->getDescription(),
            'postscriptum' => $object->getPostscriptum(),
            'visible' => $object->getVisible()
        );

        // Setup the object form
        return $this->createForm(KeywordModificationForm::getName());
    }

    /**
     * Creates the creation event with the provided form data
     * @return \Keyword\Event\KeywordEvents
     */
    protected function getCreationEvent($formData)
    {
        $keywordCreateEvent = new KeywordEvents(
            $formData['title'],
            $formData['code'],
            $formData['visible'],
            $formData['locale'],
            $formData['keyword_group_id']
        );

        return $keywordCreateEvent;
    }

    /**
     * Creates the update event with the provided form data
     *
     * @param $formData
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
            ->setVisible($formData['visible'])
            ->setKeywordGroupId($formData['keyword_group_id']);

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
     * @param $createEvent
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
     * @param $object
     */
    protected function getObjectLabel($object)
    {
        // TODO: Implement getObjectLabel() method.
    }

    /**
     * Returns the object ID from the object
     *
     * @param $object
     */
    protected function getObjectId($object)
    {
        // TODO: Implement getObjectId() method.
    }

    /**
     * Render the main list template
     *
     * @param $currentKeyword , if any, null otherwise.
     */
    protected function renderListTemplate($currentKeyword)
    {
        $request = $this->getRequest()->get('admin_keyword_creation');
        if (isset($request['keyword_group_id'])) {
            $keywordGroupId = $request['keyword_group_id'];

            return $this->render(
                'keyword-group-view',
                array(
                    'keyword_group_id' => $keywordGroupId
                )
            );
        } else {
            return $this->generateRedirect('/admin/module/Keyword');
        }

    }

    protected function getEditionArguments()
    {
        return array(
            'keyword_id' => $this->getRequest()->get('keyword_id', 0)
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

        return $this->generateRedirect('/admin/module/Keyword/update?keyword_id=' . $args['keyword_id']);
    }

    /**
     * Get the keyword group id from request
     * @return int|mixed
     *
     */
    protected function getKeywordGroupId()
    {

        $keywordGroupId = $this->getRequest()->get('keyword_group_id', null);

        return $keywordGroupId != null ? $keywordGroupId : 0;
    }

    /**
     * Redirect to the list template
     */
    protected function redirectToListTemplate()
    {
        // Set the module router to use module routes
        $this->setCurrentRouter("router.keyword");

        // Redirect to parent keyword group list
        return $this->generateRedirectFromRoute(
            'admin.keyword.group.view',
            array('keyword_group_id' => $this->getKeywordGroupId())
        );

    }

    protected function performAdditionalUpdateAction(EventDispatcherInterface $eventDispatcher, $updateEvent)
    {
        if ($this->getRequest()->get('save_mode') != 'stay') {
            return $this->redirectToListTemplate();
        }
    }
}
