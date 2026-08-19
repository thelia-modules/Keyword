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
use Keyword\Form\KeywordCreationForm;
use Keyword\Form\KeywordGroupCreationForm;
use Keyword\Form\KeywordGroupModificationForm;
use Keyword\Model\KeywordGroupQuery;
use Keyword\Model\KeywordQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\BaseForm;
use Symfony\Component\Routing\Attribute\Route;


/**
 * Class KeywordGroupController
 * @package Keyword\Controller\Admin
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
#[Route('/admin/module/Keyword/group', name: 'keyword_group')]
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

    #[Route('/view', name: 'view')]
    public function viewAction()
    {
        $keywordGroup = $this->getExistingObject();

        if (null === $keywordGroup) {
            return $this->pageNotFound();
        }

        return $this->render('keyword/keyword-group-view', $this->getViewData($keywordGroup));
    }

    /**
     * Return the creation form for this object
     */
    protected function getCreationForm(): ?\Thelia\Form\BaseForm {
        return $this->createForm(KeywordGroupCreationForm::getName());
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm(): ?\Thelia\Form\BaseForm {
        return $this->createForm(KeywordGroupModificationForm::getName());
    }

    /**
     * @param $positionChangeMode
     * @param $positionValue
     * @return UpdatePositionEvent|void
     */
    protected function createUpdatePositionEvent($positionChangeMode, $positionValue): \Thelia\Core\Event\ActionEvent {
        return new UpdatePositionEvent(
            $this->getRequest()->get('keyword_group_id', null),
            $positionChangeMode,
            $positionValue
        );
    }

    protected function createToggleVisibilityEvent(): \Thelia\Core\Event\ActionEvent {
        return new KeywordGroupToggleVisibilityEvent($this->getExistingObject());
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param  $object
     */
    protected function hydrateObjectForm(ParserContext $parserContext, $object): \Thelia\Form\BaseForm {
        // Prepare the data that will hydrate the form. The Twig template no longer overrides
        // the displayed field values on top of the form (as the Smarty render_form_field tag
        // did) : the form itself must carry the current object values.
        $data = array(
            'id'           => $object->getId(),
            'locale'       => $object->getLocale(),
            'title'        => $object->getTitle(),
            'code'         => $object->getCode(),
            'chapo'        => $object->getChapo(),
            'description'  => $object->getDescription(),
            'postscriptum' => $object->getPostscriptum(),
            'visible'      => (bool) $object->getVisible(),
            'success_url'  => $this->getRoute('admin.keyword.group.update', ['keyword_group_id' => $object->getId()]),
        );

        // Setup the object form
        return $this->createForm(KeywordGroupModificationForm::getName(), FormType::class, $data);
    }

    /**
     * Creates the creation event with the provided form data
     *
     * @param $formData
     */
    protected function getCreationEvent($formData): \Thelia\Core\Event\ActionEvent|\Propel\Runtime\Event\ActiveRecordEvent|null {

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
     * @param $formData
     */
    protected function getUpdateEvent($formData): \Thelia\Core\Event\ActionEvent|\Propel\Runtime\Event\ActiveRecordEvent|null {
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
    protected function getDeleteEvent(): \Propel\Runtime\Event\ActiveRecordEvent|\Thelia\Core\Event\ActionEvent|null {
        return new KeywordGroupDeleteEvent($this->getRequest()->get('keyword_group_id'), 0);
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     *
     * @param  \Keyword\Event\KeywordGroupEvents $event
     * @return bool
     */
    protected function eventContainsObject($event): bool {
        return $event->hasKeywordGroup();
    }

    /**
     * Get the created object from an event.
     *
     * @param $event
     */
    protected function getObjectFromEvent($event): mixed {
        // TODO: Implement getObjectFromEvent() method.
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject(): ?\Propel\Runtime\ActiveRecord\ActiveRecordInterface {
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
    protected function renderEditionTemplate(): \Symfony\Component\HttpFoundation\Response {
        $keywordGroupId = (int) $this->getRequest()->get('keyword_group_id', 0);
        $keywordGroup = KeywordGroupQuery::create()->findPk($keywordGroupId);

        if (null === $keywordGroup) {
            return $this->pageNotFound();
        }

        $locale = $this->getCurrentEditionLocale();
        $keywordGroup->setLocale($locale);

        // The form was already hydrated (with the current values or, on a validation
        // error redisplay, with the submitted values and errors) by hydrateObjectForm()
        // or setupFormErrorContext(), both of which store it in the ParserContext.
        $form = $this->getParserContext()->getForm(
            KeywordGroupModificationForm::getName(),
            KeywordGroupModificationForm::class,
            FormType::class
        );

        if (!$form instanceof BaseForm) {
            $form = $this->hydrateObjectForm($this->getParserContext(), $keywordGroup);
        }

        $previous = KeywordGroupQuery::create()
            ->filterByPosition($keywordGroup->getPosition(), Criteria::LESS_THAN)
            ->orderByPosition(Criteria::DESC)
            ->findOne();

        $next = KeywordGroupQuery::create()
            ->filterByPosition($keywordGroup->getPosition(), Criteria::GREATER_THAN)
            ->orderByPosition(Criteria::ASC)
            ->findOne();

        return $this->render('keyword/keyword-group-edit', [
            'keyword_group' => [
                'id' => $keywordGroup->getId(),
                'title' => $keywordGroup->getTitle(),
                'created_at' => $keywordGroup->getCreatedAt(),
                'updated_at' => $keywordGroup->getUpdatedAt(),
            ],
            'previous_url' => $previous ? $this->getRoute('admin.keyword.group.update', ['keyword_group_id' => $previous->getId()]) : null,
            'next_url' => $next ? $this->getRoute('admin.keyword.group.update', ['keyword_group_id' => $next->getId()]) : null,
            'form' => $form->getForm()->createView(),
            'form_action' => $this->getRoute('admin.keyword.group.save'),
        ]);
    }

    /**
     * Redirect to the edition template
     */
    protected function redirectToEditionTemplate(): \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse {
        $args = $this->getEditionArguments();

        return $this->generateRedirect('/admin/module/Keyword/group/update?keyword_group_id='.$args['keyword_group_id']);
    }

    /**
     * Redirect to the list template
     */
    protected function redirectToListTemplate(): \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse {
        return $this->generateRedirect('/admin/module/Keyword');
    }

    protected function performAdditionalUpdateAction(EventDispatcherInterface $eventDispatcher, $updateEvent): ?\Symfony\Component\HttpFoundation\Response {
        if ($this->getRequest()->get('save_mode') != 'stay') {
            return $this->redirectToListTemplate();
        }

        return null;
    }

    /**
     * Build every piece of data displayed on the keyword group view page (breadcrumb,
     * prev/next, rights, the keyword table and the keyword creation/delete dialogs),
     * replacing the {loop} tags of the former Smarty template.
     */
    private function getViewData($keywordGroup): array
    {
        $locale = $this->getCurrentEditionLocale();
        $keywordGroupId = $keywordGroup->getId();

        $previous = KeywordGroupQuery::create()
            ->filterByPosition($keywordGroup->getPosition(), Criteria::LESS_THAN)
            ->orderByPosition(Criteria::DESC)
            ->findOne();

        $next = KeywordGroupQuery::create()
            ->filterByPosition($keywordGroup->getPosition(), Criteria::GREATER_THAN)
            ->orderByPosition(Criteria::ASC)
            ->findOne();

        $security = $this->getSecurityContext();
        $canCreate = $security->isGranted(['ADMIN'], ['admin.keyword'], [], [AccessManager::CREATE]);
        $canChange = $security->isGranted(['ADMIN'], ['admin.keyword'], [], [AccessManager::UPDATE]);
        $canDelete = $security->isGranted(['ADMIN'], ['admin.keyword'], [], [AccessManager::DELETE]);

        $keywords = [];

        foreach (KeywordQuery::create()->filterByKeywordGroupId($keywordGroupId)->orderByPosition(Criteria::ASC)->find() as $keyword) {
            $keyword->setLocale($locale);
            $keywordId = $keyword->getId();

            $keywords[] = [
                'id' => $keywordId,
                'title' => $keyword->getTitle(),
                'code' => $keyword->getCode(),
                'visible' => (bool) $keyword->getVisible(),
                'position' => $keyword->getPosition(),
                'view_url' => $this->getRoute('admin.keyword.view', ['keyword_id' => $keywordId]),
                'edit_url' => $this->getRoute('admin.keyword.update', ['keyword_id' => $keywordId]),
                'toggle_url' => $this->getRoute('admin.keyword.toggle-online', ['keyword_id' => $keywordId, 'keyword_group_id' => $keywordGroupId]),
                'position_up_url' => $this->getRoute('admin.keyword.update-position', ['keyword_id' => $keywordId, 'keyword_group_id' => $keywordGroupId, 'mode' => 'up']),
                'position_down_url' => $this->getRoute('admin.keyword.update-position', ['keyword_id' => $keywordId, 'keyword_group_id' => $keywordGroupId, 'mode' => 'down']),
            ];
        }

        $createForm = null;

        if ($canCreate) {
            $createForm = $this->createForm(KeywordCreationForm::getName(), FormType::class, [
                'locale' => $locale,
                'visible' => true,
                'keyword_group_id' => $keywordGroupId,
                'success_url' => $this->getRoute('admin.keyword.group.view', ['keyword_group_id' => $keywordGroupId]),
            ])->getForm()->createView();
        }

        return [
            'keyword_group' => [
                'id' => $keywordGroupId,
                'title' => $keywordGroup->getTitle(),
            ],
            'previous_url' => $previous ? $this->getRoute('admin.keyword.group.view', ['keyword_group_id' => $previous->getId()]) : null,
            'next_url' => $next ? $this->getRoute('admin.keyword.group.view', ['keyword_group_id' => $next->getId()]) : null,
            'can_create' => $canCreate,
            'can_change' => $canChange,
            'can_delete' => $canDelete,
            'keywords' => $keywords,
            'create_form' => $createForm,
            'create_form_action' => $this->getRoute('admin.keyword.create'),
            'delete_form_action' => $this->getRoute('admin.keyword.delete'),
        ];
    }
}
