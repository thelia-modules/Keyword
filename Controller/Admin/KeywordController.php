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
use Keyword\Model\CategoryAssociatedKeywordQuery;
use Keyword\Model\ContentAssociatedKeywordQuery;
use Keyword\Model\FolderAssociatedKeywordQuery;
use Keyword\Model\KeywordGroupQuery;
use Keyword\Model\KeywordQuery;
use Keyword\Model\ProductAssociatedKeywordQuery;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Thelia\Action\Image as ImageAction;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\BaseForm;
use Thelia\Log\Tlog;
use Thelia\Tools\TokenProvider;
use Thelia\Model\Base\FolderQuery;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\CategoryImageQuery;
use Thelia\Model\CategoryQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ContentImageQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\FolderImageQuery;
use Thelia\Model\ProductImageQuery;
use Thelia\Model\ProductQuery;
use Symfony\Component\Routing\Attribute\Route;


/**
 * Class KeywordController
 * @package Keyword\Controller\Admin
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
#[Route('/admin', name: 'admin.keyword')]
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

    /*
     * The CRUD actions themselves live in AbstractCrudController. PHP attributes are not
     * inherited by Symfony's route loader, so each one is re-declared here as a thin
     * override carrying its route. These names are the module's public route ids, kept
     * identical to the ones the removed Config/routing.xml used to declare.
     */

    #[Route('/module/Keyword/create', name: '.create')]
    public function createAction(
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
    ): RedirectResponse|Response {
        return parent::createAction($eventDispatcher, $translator);
    }

    #[Route('/module/Keyword/update', name: '.update')]
    public function updateAction(ParserContext $parserContext): Response
    {
        return parent::updateAction($parserContext);
    }

    #[Route('/module/Keyword/save', name: '.save')]
    public function processUpdateAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
    ): Response|RedirectResponse {
        return parent::processUpdateAction($request, $eventDispatcher, $translator);
    }

    #[Route('/module/Keyword/delete', name: '.delete')]
    public function deleteAction(
        Request $request,
        TokenProvider $tokenProvider,
        EventDispatcherInterface $eventDispatcher,
        ParserContext $parserContext,
    ): Response|RedirectResponse {
        return parent::deleteAction($request, $tokenProvider, $eventDispatcher, $parserContext);
    }

    #[Route('/module/Keyword/toggle-online', name: '.toggle-online')]
    public function setToggleVisibilityAction(EventDispatcherInterface $eventDispatcher): ?Response
    {
        return parent::setToggleVisibilityAction($eventDispatcher);
    }

    #[Route('/module/Keyword/update-position', name: '.update-position')]
    public function updatePositionAction(Request $request, EventDispatcherInterface $eventDispatcher): mixed
    {
        return parent::updatePositionAction($request, $eventDispatcher);
    }

    #[Route('/module/Keyword/view', name: '.view')]
    public function viewAction(EventDispatcherInterface $dispatcher)
    {
        $keyword = $this->getExistingObject();

        if (null === $keyword) {
            return $this->pageNotFound();
        }

        return $this->render('keyword/keyword-view', $this->getViewData($keyword, $dispatcher));
    }

    #[Route('/folders/update/{folder_id}/keyword', name: '.folders.association.update', requirements: ['folder_id' => '\\d+'])]
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

    #[Route('/content/update/{content_id}/keyword', name: '.contents.association.update', requirements: ['content_id' => '\\d+'])]
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

    #[Route('/categories/update/{category_id}/keyword', name: '.categories.association.update', requirements: ['category_id' => '\\d+'])]
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

    #[Route('/product/update/{product_id}/keyword', name: '.products.association.update', requirements: ['product_id' => '\\d+'])]
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
     */
    #[Route('/module/Keyword/{object}/update-position', name: '.folder.update-position', requirements: ['object' => 'folder|content|category|product'])]
    public function updateObjectPositionAction(EventDispatcherInterface $dispatcher, Request $request)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE))
            return $response;

        try {
            $mode = $request->query->get('mode');

            if ($mode == 'up')
                $mode = UpdatePositionEvent::POSITION_UP;
            elseif ($mode == 'down')
                $mode = UpdatePositionEvent::POSITION_DOWN;
            else
                $mode = UpdatePositionEvent::POSITION_ABSOLUTE;

            $position = $request->query->get('position');
            $object = $request->attributes->get('object');

            $event = $this->createObjectUpdatePositionEvent($request, $mode, $position, $object);

            $dispatcher->dispatch($event, KeywordEvents::KEYWORD_OBJECT_UPDATE_POSITION);

        } catch (\Exception $ex) {
            // Any error
            return $this->errorPage($ex);
        }

        $keywordId = $request->query->get('keyword_id');

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
            $request->query->get('keyword_id'),
            $object,
            $request->query->get($object.'_id'),
            $positionChangeMode,
            $positionValue
        );
    }

    /**
     * @param $positionChangeMode
     * @param $positionValue
     * @return UpdatePositionEvent|void
     */
    protected function createUpdatePositionEvent($positionChangeMode, $positionValue): \Thelia\Core\Event\ActionEvent {
        return new UpdatePositionEvent(
            $this->requestValue('keyword_id'),
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
     * Reads a scalar parameter from the POST body first, then from the query string.
     *
     * The keyword screens are reached both by GET links (?keyword_id=...) and by POST form
     * submits carrying the same field as a hidden input. Request::get(), which used to hide
     * that difference, is deprecated since Symfony 7.4.
     */
    private function requestValue(string $key, mixed $default = null): mixed
    {
        $request = $this->getRequest();

        return $request->request->get($key) ?? $request->query->get($key) ?? $default;
    }

    /**
     * Return the creation form for this object
     */
    protected function getCreationForm(): ?\Thelia\Form\BaseForm {
        return $this->createForm(KeywordCreationForm::getName());
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm(): ?\Thelia\Form\BaseForm {
        return $this->createForm(KeywordModificationForm::getName());
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     */
    protected function hydrateObjectForm(ParserContext $parserContext, $object): \Thelia\Form\BaseForm {

        // Prepare the data that will hydrate the form. The Twig template no longer overrides
        // the displayed field values on top of the form (as the Smarty render_form_field tag
        // did) : the form itself must carry the current object values.
        $data = array(
            'id' => $object->getId(),
            'locale' => $object->getLocale(),
            'title' => $object->getTitle(),
            'code' => $object->getCode(),
            'chapo' => $object->getChapo(),
            'description' => $object->getDescription(),
            'postscriptum' => $object->getPostscriptum(),
            'visible' => (bool) $object->getVisible(),
            'keyword_group_id' => $object->getKeywordGroupId(),
            'success_url' => $this->getRoute('admin.keyword.update', ['keyword_id' => $object->getId()]),
        );

        // Setup the object form
        return $this->createForm(KeywordModificationForm::getName(), FormType::class, $data);
    }

    /**
     * Creates the creation event with the provided form data
     * @return \Keyword\Event\KeywordEvents
     */
    protected function getCreationEvent($formData): \Thelia\Core\Event\ActionEvent|\Propel\Runtime\Event\ActiveRecordEvent|null {
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
    protected function getUpdateEvent($formData): \Thelia\Core\Event\ActionEvent|\Propel\Runtime\Event\ActiveRecordEvent|null {
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
    protected function createToggleVisibilityEvent(): \Thelia\Core\Event\ActionEvent {
        return new KeywordToggleVisibilityEvent($this->getExistingObject());
    }

    /**
     * Creates the delete event with the provided form data
     */
    protected function getDeleteEvent(): \Propel\Runtime\Event\ActiveRecordEvent|\Thelia\Core\Event\ActionEvent|null {
        return new KeywordDeleteEvent($this->requestValue('keyword_id'), 0);
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     *
     * @param \Keyword\Event\KeywordEvents $event
     * @return bool
     */
    protected function eventContainsObject($event): bool {
        return $event->hasKeyword();
    }

    /**
     * Get the created object from an event.
     *
     * @param $createEvent
     */
    protected function getObjectFromEvent($event): mixed {
        // TODO: Implement getObjectFromEvent() method.
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject(): ?\Propel\Runtime\ActiveRecord\ActiveRecordInterface {
        $keyword = KeywordQuery::create()
            ->findOneById($this->requestValue('keyword_id', 0));

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
    protected function getObjectLabel($object): ?string {
        // TODO: Implement getObjectLabel() method.
    }

    /**
     * Returns the object ID from the object
     *
     * @param $object
     */
    protected function getObjectId($object): int {
        // TODO: Implement getObjectId() method.
    }

    /**
     * Render the main list template
     *
     * @param $currentKeyword , if any, null otherwise.
     */
    protected function renderListTemplate($currentKeyword): \Symfony\Component\HttpFoundation\Response {
        $submittedData = $this->getRequest()->request->all(KeywordCreationForm::getName());
        if (isset($submittedData['keyword_group_id'])) {
            // Re-display the keyword group page the creation dialog was opened from.
            // The validation error is kept in session by ParserContext::addForm() and will
            // resurface if the dialog is reopened, mirroring the rest of this back-office's
            // create-dialog error handling (e.g. configuration/state/list.html.twig).
            return $this->generateRedirectFromRoute(
                'admin.keyword.group.view',
                array('keyword_group_id' => $submittedData['keyword_group_id'])
            );
        } else {
            return $this->generateRedirect('/admin/module/Keyword');
        }

    }

    protected function getEditionArguments()
    {
        return array(
            'keyword_id' => $this->requestValue('keyword_id', 0)
        );
    }

    /**
     * Render the edition template
     */
    protected function renderEditionTemplate(): \Symfony\Component\HttpFoundation\Response {
        $keywordId = (int) $this->requestValue('keyword_id', 0);
        $keyword = KeywordQuery::create()->findPk($keywordId);

        if (null === $keyword) {
            return $this->pageNotFound();
        }

        $locale = $this->getCurrentEditionLocale();
        $keyword->setLocale($locale);

        // The form was already hydrated (with the current values or, on a validation
        // error redisplay, with the submitted values and errors) by hydrateObjectForm()
        // or setupFormErrorContext(), both of which store it in the ParserContext.
        $form = $this->getParserContext()->getForm(
            KeywordModificationForm::getName(),
            KeywordModificationForm::class,
            FormType::class
        );

        if (!$form instanceof BaseForm) {
            $form = $this->hydrateObjectForm($this->getParserContext(), $keyword);
        }

        $keywordGroup = KeywordGroupQuery::create()->findPk($keyword->getKeywordGroupId());
        $keywordGroup?->setLocale($locale);

        $groupOptions = [];

        foreach (KeywordGroupQuery::create()->orderByPosition(Criteria::ASC)->find() as $group) {
            $group->setLocale($locale);
            $groupOptions[] = [
                'id' => $group->getId(),
                'title' => $group->getTitle(),
            ];
        }

        [$previousId, $nextId] = $this->findPreviousNextKeywordIds($keyword);

        return $this->render('keyword/keyword-edit', [
            'keyword' => [
                'id' => $keyword->getId(),
                'title' => $keyword->getTitle(),
                'keyword_group_id' => $keyword->getKeywordGroupId(),
                'created_at' => $keyword->getCreatedAt(),
                'updated_at' => $keyword->getUpdatedAt(),
            ],
            'keyword_group_title' => $keywordGroup?->getTitle() ?? '',
            'keyword_group_view_url' => $this->getRoute('admin.keyword.group.view', ['keyword_group_id' => $keyword->getKeywordGroupId()]),
            'keyword_group_options' => $groupOptions,
            'previous_url' => $previousId ? $this->getRoute('admin.keyword.update', ['keyword_id' => $previousId]) : null,
            'next_url' => $nextId ? $this->getRoute('admin.keyword.update', ['keyword_id' => $nextId]) : null,
            'form' => $form->getForm()->createView(),
            'form_action' => $this->getRoute('admin.keyword.save'),
        ]);
    }

    /**
     * Redirect to the edition template
     */
    protected function redirectToEditionTemplate(): \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse {
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

        $keywordGroupId = $this->requestValue('keyword_group_id');

        return $keywordGroupId != null ? $keywordGroupId : 0;
    }

    /**
     * Redirect to the list template
     */
    protected function redirectToListTemplate(): \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse {
        // Redirect to parent keyword group list. Thelia 3 has no per-module router
        // ("router.keyword" never existed here): the module routes are served by the
        // admin router, which is the default one for an admin controller.
        return $this->generateRedirectFromRoute(
            'admin.keyword.group.view',
            array('keyword_group_id' => $this->getKeywordGroupId())
        );

    }

    protected function performAdditionalUpdateAction(EventDispatcherInterface $eventDispatcher, $updateEvent): ?\Symfony\Component\HttpFoundation\Response {
        if ($this->requestValue('save_mode') != 'stay') {
            return $this->redirectToListTemplate();
        }
    }

    /**
     * Build every piece of data displayed on the keyword view page (breadcrumb, prev/next,
     * rights and the 4 association tables), replacing the {loop} tags of the former Smarty
     * template. No query happens in the Twig template itself.
     */
    private function getViewData($keyword, EventDispatcherInterface $dispatcher): array
    {
        $locale = $this->getCurrentEditionLocale();
        $keywordId = $keyword->getId();

        $keywordGroup = KeywordGroupQuery::create()->findPk($keyword->getKeywordGroupId());
        $keywordGroup?->setLocale($locale);

        [$previousId, $nextId] = $this->findPreviousNextKeywordIds($keyword);

        $canEditPosition = $this->getSecurityContext()->isGranted(['ADMIN'], ['admin.keyword'], [], [AccessManager::UPDATE]);

        return [
            'keyword' => [
                'id' => $keywordId,
                'title' => $keyword->getTitle(),
                'code' => $keyword->getCode(),
                'keyword_group_id' => $keyword->getKeywordGroupId(),
            ],
            'keyword_group_title' => $keywordGroup?->getTitle() ?? '',
            'keyword_group_view_url' => $this->getRoute('admin.keyword.group.view', ['keyword_group_id' => $keyword->getKeywordGroupId()]),
            'previous_url' => $previousId ? $this->getRoute('admin.keyword.view', ['keyword_id' => $previousId]) : null,
            'next_url' => $nextId ? $this->getRoute('admin.keyword.view', ['keyword_id' => $nextId]) : null,
            'active_tab' => (string) $this->requestValue('tab', 'folder'),
            'can_edit_position' => $canEditPosition,
            'folders' => $this->buildFolderAssociationRows($dispatcher, $keywordId, $locale),
            'contents' => $this->buildContentAssociationRows($dispatcher, $keywordId, $locale),
            'categories' => $this->buildCategoryAssociationRows($dispatcher, $keywordId, $locale),
            'products' => $this->buildProductAssociationRows($dispatcher, $keywordId, $locale),
        ];
    }

    /**
     * Reproduces the previous/next lookup done by Keyword\Loop\Keyword::parseResults():
     * plain position ordering, with no keyword_group filtering.
     *
     * @return array{0: int|null, 1: int|null}
     */
    private function findPreviousNextKeywordIds($keyword): array
    {
        $previous = KeywordQuery::create()
            ->filterByPosition($keyword->getPosition(), Criteria::LESS_THAN)
            ->orderByPosition(Criteria::DESC)
            ->findOne();

        $next = KeywordQuery::create()
            ->filterByPosition($keyword->getPosition(), Criteria::GREATER_THAN)
            ->orderByPosition(Criteria::ASC)
            ->findOne();

        return [$previous?->getId(), $next?->getId()];
    }

    private function buildFolderAssociationRows(EventDispatcherInterface $dispatcher, int $keywordId, string $locale): array
    {
        $canEdit = $this->getSecurityContext()->isGranted(['ADMIN'], ['admin.folder'], [], [AccessManager::UPDATE]);
        $rows = [];

        foreach (FolderAssociatedKeywordQuery::create()->filterByKeywordId($keywordId)->orderByPosition(Criteria::ASC)->find() as $assoc) {
            $folder = FolderQuery::create()->findPk($assoc->getFolderId());

            if (null === $folder) {
                continue;
            }

            $folder->setLocale($locale);
            $folderId = $folder->getId();

            $rows[] = [
                'id' => $folderId,
                'title' => $folder->getTitle(),
                'position' => $assoc->getPosition(),
                'image_url' => $this->getThumbnailUrl($dispatcher, 'folder', $folderId, $locale),
                'browse_url' => $this->getRoute('admin.folders.default', ['parent' => $folderId]),
                'edit_url' => $this->getRoute('admin.folders.update', ['folder_id' => $folderId]),
                'can_edit' => $canEdit,
                'position_up_url' => $this->getRoute('admin.keyword.folder.update-position', ['object' => 'folder', 'folder_id' => $folderId, 'keyword_id' => $keywordId, 'mode' => 'up']),
                'position_down_url' => $this->getRoute('admin.keyword.folder.update-position', ['object' => 'folder', 'folder_id' => $folderId, 'keyword_id' => $keywordId, 'mode' => 'down']),
            ];
        }

        return $rows;
    }

    private function buildContentAssociationRows(EventDispatcherInterface $dispatcher, int $keywordId, string $locale): array
    {
        $canEdit = $this->getSecurityContext()->isGranted(['ADMIN'], ['admin.content'], [], [AccessManager::UPDATE]);
        $rows = [];

        foreach (ContentAssociatedKeywordQuery::create()->filterByKeywordId($keywordId)->orderByPosition(Criteria::ASC)->find() as $assoc) {
            $content = ContentQuery::create()->findPk($assoc->getContentId());

            if (null === $content) {
                continue;
            }

            $content->setLocale($locale);
            $contentId = $content->getId();

            $rows[] = [
                'id' => $contentId,
                'title' => $content->getTitle(),
                'position' => $assoc->getPosition(),
                'image_url' => $this->getThumbnailUrl($dispatcher, 'content', $contentId, $locale),
                'browse_url' => null,
                'edit_url' => $this->getRoute('admin.content.update', ['content_id' => $contentId]),
                'can_edit' => $canEdit,
                'position_up_url' => $this->getRoute('admin.keyword.folder.update-position', ['object' => 'content', 'content_id' => $contentId, 'keyword_id' => $keywordId, 'mode' => 'up']),
                'position_down_url' => $this->getRoute('admin.keyword.folder.update-position', ['object' => 'content', 'content_id' => $contentId, 'keyword_id' => $keywordId, 'mode' => 'down']),
            ];
        }

        return $rows;
    }

    private function buildCategoryAssociationRows(EventDispatcherInterface $dispatcher, int $keywordId, string $locale): array
    {
        $canEdit = $this->getSecurityContext()->isGranted(['ADMIN'], ['admin.category'], [], [AccessManager::UPDATE]);
        $rows = [];

        foreach (CategoryAssociatedKeywordQuery::create()->filterByKeywordId($keywordId)->orderByPosition(Criteria::ASC)->find() as $assoc) {
            $category = CategoryQuery::create()->findPk($assoc->getCategoryId());

            if (null === $category) {
                continue;
            }

            $category->setLocale($locale);
            $categoryId = $category->getId();

            $rows[] = [
                'id' => $categoryId,
                'title' => $category->getTitle(),
                'position' => $assoc->getPosition(),
                'image_url' => $this->getThumbnailUrl($dispatcher, 'category', $categoryId, $locale),
                // /admin/catalog no longer exists in Thelia 3: admin.categories.default replaces it.
                'browse_url' => $this->getRoute('admin.categories.default', ['category_id' => $categoryId]),
                'edit_url' => $this->getRoute('admin.categories.update', ['category_id' => $categoryId]),
                'can_edit' => $canEdit,
                'position_up_url' => $this->getRoute('admin.keyword.folder.update-position', ['object' => 'category', 'category_id' => $categoryId, 'keyword_id' => $keywordId, 'mode' => 'up']),
                'position_down_url' => $this->getRoute('admin.keyword.folder.update-position', ['object' => 'category', 'category_id' => $categoryId, 'keyword_id' => $keywordId, 'mode' => 'down']),
            ];
        }

        return $rows;
    }

    private function buildProductAssociationRows(EventDispatcherInterface $dispatcher, int $keywordId, string $locale): array
    {
        $canEdit = $this->getSecurityContext()->isGranted(['ADMIN'], ['admin.product'], [], [AccessManager::UPDATE]);
        $rows = [];

        foreach (ProductAssociatedKeywordQuery::create()->filterByKeywordId($keywordId)->orderByPosition(Criteria::ASC)->find() as $assoc) {
            $product = ProductQuery::create()->findPk($assoc->getProductId());

            if (null === $product) {
                continue;
            }

            $product->setLocale($locale);
            $productId = $product->getId();

            $rows[] = [
                'id' => $productId,
                'title' => $product->getTitle(),
                'position' => $assoc->getPosition(),
                'image_url' => $this->getThumbnailUrl($dispatcher, 'product', $productId, $locale),
                'browse_url' => null,
                'edit_url' => $this->getRoute('admin.products.update', ['product_id' => $productId]),
                'can_edit' => $canEdit,
                'position_up_url' => $this->getRoute('admin.keyword.folder.update-position', ['object' => 'product', 'product_id' => $productId, 'keyword_id' => $keywordId, 'mode' => 'up']),
                'position_down_url' => $this->getRoute('admin.keyword.folder.update-position', ['object' => 'product', 'product_id' => $productId, 'keyword_id' => $keywordId, 'mode' => 'down']),
            ];
        }

        return $rows;
    }

    /**
     * Resolve the URL of a 50x50 cropped thumbnail for the first image of a folder/content/
     * category/product, reproducing the core `image` loop processing (Thelia\Core\Template\
     * Loop\Image::parseResults()) without going through a loop.
     */
    private function getThumbnailUrl(EventDispatcherInterface $dispatcher, string $type, int $objectId, string $locale): string
    {
        $queryClass = match ($type) {
            'folder' => FolderImageQuery::class,
            'content' => ContentImageQuery::class,
            'category' => CategoryImageQuery::class,
            'product' => ProductImageQuery::class,
        };
        $filterMethod = 'filterBy'.ucfirst($type).'Id';

        $image = $queryClass::create()->{$filterMethod}($objectId)->orderByPosition(Criteria::ASC)->findOne();

        if (null === $image) {
            return '';
        }

        $baseSourceFilePath = ConfigQuery::read('images_library_path');
        $baseSourceFilePath = null === $baseSourceFilePath
            ? THELIA_LOCAL_DIR.'media'.DS.'images'
            : THELIA_ROOT.$baseSourceFilePath;

        $sourceFilePath = sprintf('%s/%s/%s', $baseSourceFilePath, $type, $image->setLocale($locale)->getFile());

        if (!file_exists($sourceFilePath)) {
            return '';
        }

        $event = (new ImageEvent())
            ->setWidth(50)
            ->setHeight(50)
            ->setResizeMode((string) ImageAction::EXACT_RATIO_WITH_CROP)
            ->setCacheSubdirectory($type)
            ->setSourceFilepath($sourceFilePath);

        try {
            $dispatcher->dispatch($event, TheliaEvents::IMAGE_PROCESS);

            return $event->getFileUrl() ?? '';
        } catch (\Exception) {
            return '';
        }
    }
}
