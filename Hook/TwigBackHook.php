<?php

declare(strict_types=1);

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

namespace Keyword\Hook;

use Keyword\Form\KeywordCategoryModificationForm;
use Keyword\Form\KeywordContentModificationForm;
use Keyword\Form\KeywordFolderModificationForm;
use Keyword\Form\KeywordGroupCreationForm;
use Keyword\Form\KeywordProductModificationForm;
use Keyword\Model\CategoryAssociatedKeywordQuery;
use Keyword\Model\ContentAssociatedKeywordQuery;
use Keyword\Model\FolderAssociatedKeywordQuery;
use Keyword\Model\KeywordGroupQuery;
use Keyword\Model\KeywordQuery;
use Keyword\Model\ProductAssociatedKeywordQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormView;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\BaseForm;
use Thelia\Tools\URL;

/**
 * Renders the Twig back-office fragments of the Keyword module.
 *
 * Historically the same hooks were also declared in Config/config.xml on the
 * deprecated BackHookManager class, instantiated by the legacy XML hook loader
 * without autowiring: its container was never initialized, any render() threw, and
 * hook isolation silently swallowed every fragment. Both the XML declaration and
 * BackHookManager have been removed; this autowired class (registered through
 * Keyword::configureServices() and getSubscribedHooks()) is the single hook owner.
 * On databases that carry old module_hook rows pointing at `keyword.back.hook`,
 * those rows no longer match any service and are ignored at container compile.
 */
class TwigBackHook extends BaseHook
{
    public function __construct(
        private readonly SecurityContext $securityContext,
        private readonly TheliaFormFactory $theliaFormFactory,
        private readonly ParserContext $parserContext,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => ['type' => 'back', 'method' => 'renderModuleConfiguration'],
            'module.config-js' => ['type' => 'back', 'method' => 'renderModuleConfigJs'],
            'category.tab-content' => ['type' => 'back', 'method' => 'renderCategoryTab'],
            'content.tab-content' => ['type' => 'back', 'method' => 'renderContentTab'],
            'folder.tab-content' => ['type' => 'back', 'method' => 'renderFolderTab'],
            'product.tab-content' => ['type' => 'back', 'method' => 'renderProductTab'],
        ];
    }

    public function renderModuleConfiguration(HookRenderEvent $event): void
    {
        $event->add(
            $this->render('keyword/hook/module-configuration.html.twig', $this->getModuleConfigurationData())
        );
    }

    public function renderModuleConfigJs(HookRenderEvent $event): void
    {
        $event->add(
            $this->render('keyword/hook/module-config-js.html.twig')
        );
    }

    public function renderCategoryTab(HookRenderEvent $event): void
    {
        $categoryId = (int) ($event->getArgument('category') ?? $event->getArgument('category_id'));

        $event->add(
            $this->render('keyword/hook/category-edit.html.twig', $this->getCategoryAssociationData($categoryId))
        );
    }

    public function renderContentTab(HookRenderEvent $event): void
    {
        $contentId = (int) ($event->getArgument('content') ?? $event->getArgument('content_id'));

        $event->add(
            $this->render('keyword/hook/content-edit.html.twig', $this->getContentAssociationData($contentId))
        );
    }

    public function renderFolderTab(HookRenderEvent $event): void
    {
        $folderId = (int) ($event->getArgument('folder') ?? $event->getArgument('folder_id'));

        $event->add(
            $this->render('keyword/hook/folder-edit.html.twig', $this->getFolderAssociationData($folderId))
        );
    }

    public function renderProductTab(HookRenderEvent $event): void
    {
        $productId = (int) ($event->getArgument('product') ?? $event->getArgument('product_id'));

        $event->add(
            $this->render('keyword/hook/product-edit.html.twig', $this->getProductAssociationData($productId))
        );
    }

    /**
     * Build every piece of data displayed on the "Keyword groups" list of the
     * module.configuration hook (/admin/module/Keyword) : rows, rights and the
     * keyword group creation FormView.
     */
    private function getModuleConfigurationData(): array
    {
        $locale = $this->getLang()->getLocale();

        $rows = [];

        foreach (KeywordGroupQuery::create()->orderByPosition(Criteria::ASC)->find() as $group) {
            $group->setLocale($locale);

            $rows[] = [
                'id' => $group->getId(),
                'title' => $group->getTitle(),
                'code' => $group->getCode(),
                'visible' => (bool) $group->getVisible(),
                'position' => $group->getPosition(),
                'view_url' => URL::getInstance()->absoluteUrl('/admin/module/Keyword/group/view', ['keyword_group_id' => $group->getId()]),
                'edit_url' => URL::getInstance()->absoluteUrl('/admin/module/Keyword/group/update', ['keyword_group_id' => $group->getId()]),
                'toggle_url' => URL::getInstance()->absoluteUrl('/admin/module/Keyword/group/toggle-online', ['keyword_group_id' => $group->getId()]),
                'position_up_url' => URL::getInstance()->absoluteUrl('/admin/module/Keyword/group/update-position', ['keyword_group_id' => $group->getId(), 'mode' => 'up']),
                'position_down_url' => URL::getInstance()->absoluteUrl('/admin/module/Keyword/group/update-position', ['keyword_group_id' => $group->getId(), 'mode' => 'down']),
            ];
        }

        $createForm = null;

        if ($this->securityContext->isGranted(['ADMIN'], ['admin.keyword.group'], [], [AccessManager::CREATE])) {
            $createForm = $this->createFormView(KeywordGroupCreationForm::class, KeywordGroupCreationForm::getName(), [
                'locale' => $locale,
                'visible' => true,
                'success_url' => URL::getInstance()->absoluteUrl('/admin/module/Keyword'),
            ]);
        }

        return [
            'groups' => $rows,
            'create_form' => $createForm,
            'create_form_action' => URL::getInstance()->absoluteUrl('/admin/module/Keyword/group/create'),
            'delete_form_action' => URL::getInstance()->absoluteUrl('/admin/module/Keyword/group/delete'),
        ];
    }

    private function getFolderAssociationData(int $folderId): array
    {
        $associatedIds = array_map(
            static fn ($assoc) => $assoc->getKeywordId(),
            iterator_to_array(FolderAssociatedKeywordQuery::create()->filterByFolderId($folderId)->find())
        );

        $returnUrl = URL::getInstance()->absoluteUrl('/admin/folders/update/'.$folderId, ['current_tab' => 'modules']);

        return [
            'form' => $this->createFormView(KeywordFolderModificationForm::class, KeywordFolderModificationForm::getName(), [
                'success_url' => $returnUrl,
                'error_url' => $returnUrl,
            ]),
            'form_action' => URL::getInstance()->absoluteUrl('/admin/folders/update/'.$folderId.'/keyword'),
            'return_url' => $returnUrl,
            'keyword_groups' => $this->getKeywordGroupsCheckboxData($associatedIds),
        ];
    }

    private function getContentAssociationData(int $contentId): array
    {
        $associatedIds = array_map(
            static fn ($assoc) => $assoc->getKeywordId(),
            iterator_to_array(ContentAssociatedKeywordQuery::create()->filterByContentId($contentId)->find())
        );

        $returnUrl = URL::getInstance()->absoluteUrl('/admin/content/update/'.$contentId, ['current_tab' => 'modules']);

        return [
            'form' => $this->createFormView(KeywordContentModificationForm::class, KeywordContentModificationForm::getName(), [
                'success_url' => $returnUrl,
                'error_url' => $returnUrl,
            ]),
            'form_action' => URL::getInstance()->absoluteUrl('/admin/content/update/'.$contentId.'/keyword'),
            'return_url' => $returnUrl,
            'keyword_groups' => $this->getKeywordGroupsCheckboxData($associatedIds),
        ];
    }

    private function getCategoryAssociationData(int $categoryId): array
    {
        $associatedIds = array_map(
            static fn ($assoc) => $assoc->getKeywordId(),
            iterator_to_array(CategoryAssociatedKeywordQuery::create()->filterByCategoryId($categoryId)->find())
        );

        $returnUrl = URL::getInstance()->absoluteUrl('/admin/categories/update', ['category_id' => $categoryId, 'current_tab' => 'modules']);

        return [
            'form' => $this->createFormView(KeywordCategoryModificationForm::class, KeywordCategoryModificationForm::getName(), [
                'success_url' => $returnUrl,
                'error_url' => $returnUrl,
            ]),
            'form_action' => URL::getInstance()->absoluteUrl('/admin/categories/update/'.$categoryId.'/keyword'),
            'return_url' => $returnUrl,
            'keyword_groups' => $this->getKeywordGroupsCheckboxData($associatedIds),
        ];
    }

    private function getProductAssociationData(int $productId): array
    {
        $associatedIds = array_map(
            static fn ($assoc) => $assoc->getKeywordId(),
            iterator_to_array(ProductAssociatedKeywordQuery::create()->filterByProductId($productId)->find())
        );

        $returnUrl = URL::getInstance()->absoluteUrl('/admin/products/update', ['product_id' => $productId, 'current_tab' => 'modules']);

        return [
            'form' => $this->createFormView(KeywordProductModificationForm::class, KeywordProductModificationForm::getName(), [
                'success_url' => $returnUrl,
                'error_url' => $returnUrl,
            ]),
            'form_action' => URL::getInstance()->absoluteUrl('/admin/product/update/'.$productId.'/keyword'),
            'return_url' => $returnUrl,
            'keyword_groups' => $this->getKeywordGroupsCheckboxData($associatedIds),
        ];
    }

    /**
     * Build the "keyword group > keyword checkboxes" tree used by the generic
     * association fragment, mirroring the keyword_group/keyword loops default
     * (visible only) filtering.
     *
     * @param int[] $associatedKeywordIds keyword ids already linked to the edited object
     */
    private function getKeywordGroupsCheckboxData(array $associatedKeywordIds): array
    {
        $locale = $this->getLang()->getLocale();
        $groups = [];

        foreach (KeywordGroupQuery::create()->filterByVisible(1)->orderByPosition(Criteria::ASC)->find() as $group) {
            $group->setLocale($locale);

            $keywords = [];

            foreach (KeywordQuery::create()->filterByKeywordGroupId($group->getId())->filterByVisible(1)->orderByPosition(Criteria::ASC)->find() as $keyword) {
                $keyword->setLocale($locale);

                $keywords[] = [
                    'id' => $keyword->getId(),
                    'title' => $keyword->getTitle(),
                    'code' => $keyword->getCode(),
                    'checked' => \in_array($keyword->getId(), $associatedKeywordIds, true),
                ];
            }

            // Mirror the Smarty {ifloop rel="keywords"}: skip empty groups.
            if ([] === $keywords) {
                continue;
            }

            $groups[] = [
                'id' => $group->getId(),
                'code' => $group->getCode(),
                'title' => $group->getTitle(),
                'keywords' => $keywords,
            ];
        }

        return $groups;
    }

    /**
     * Build a FormView for a module form, re-using the errored form kept in the
     * ParserContext (after a failed validation redirect) when there is one.
     */
    private function createFormView(string $formClass, string $formName, array $data = []): FormView
    {
        $form = $this->parserContext->getForm($formName, $formClass, FormType::class);

        if (!$form instanceof BaseForm) {
            $form = $this->theliaFormFactory->createForm($formName, FormType::class, $data);
        }

        return $form->getForm()->createView();
    }
}
