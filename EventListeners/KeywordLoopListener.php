<?php

declare(strict_types=1);

/*
 * This file is part of the project Anselmi.
 * This project is protected by proprietary license.
 * Do not share this file, unless you have permission.
 */

namespace Keyword\EventListeners;

use Keyword\Model\Map\CategoryAssociatedKeywordTableMap;
use Keyword\Model\Map\ContentAssociatedKeywordTableMap;
use Keyword\Model\Map\FolderAssociatedKeywordTableMap;
use Keyword\Model\Map\KeywordTableMap;
use Keyword\Model\Map\ProductAssociatedKeywordTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsArgDefinitionsEvent;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Model\Map\CategoryTableMap;
use Thelia\Model\Map\ContentTableMap;
use Thelia\Model\Map\FolderTableMap;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Type\EnumType;
use Thelia\Type\TypeCollection;

class KeywordLoopListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_ARG_DEFINITIONS, 'content') => ['addLoopArgDefinition', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_ARG_DEFINITIONS, 'product') => ['addLoopArgDefinition', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_ARG_DEFINITIONS, 'folder') => ['addLoopArgDefinition', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_ARG_DEFINITIONS, 'category') => ['addLoopArgDefinition', 128],

            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'content') => ['contentLoopBuildModelCriteria', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'product') => ['productLoopBuildModelCriteria', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'folder') => ['folderLoopBuildModelCriteria', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'category') => ['categoryLoopBuildModelCriteria', 128],
        ];
    }

    public function addLoopArgDefinition(LoopExtendsArgDefinitionsEvent $event): void
    {
        $argument = $event->getArgumentCollection();
        $argument
            ->addArgument(
                Argument::createAnyListTypeArgument('keyword')
            )
            ->addArgument(
                new Argument(
                    'keyword_match_mode',
                    new TypeCollection(new EnumType(['exact', 'partial'])),
                    'exact'
                )
            )
        ;
    }

    /**
     * @throws PropelException
     */
    public function contentLoopBuildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event): void
    {
        $this->setupLoopBuildModelCriteria(ContentTableMap::COL_ID, 'content', $event);
    }

    /**
     * @throws PropelException
     */
    public function categoryLoopBuildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event): void
    {
        $this->setupLoopBuildModelCriteria(CategoryTableMap::COL_ID, 'category', $event);
    }

    /**
     * @throws PropelException
     */
    public function productLoopBuildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event): void
    {
        $this->setupLoopBuildModelCriteria(ProductTableMap::COL_ID, 'product', $event);
    }

    /**
     * @throws PropelException
     */
    public function folderLoopBuildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event): void
    {
        $this->setupLoopBuildModelCriteria(FolderTableMap::COL_ID, 'folder', $event);
    }

    /**
     * @throws PropelException
     */
    protected function setupLoopBuildModelCriteria($leftTableFieldName, $loopType, LoopExtendsBuildModelCriteriaEvent $event): void
    {
        $this->handleKeywordArgument($leftTableFieldName, $loopType, $event);
    }

    protected function handleKeywordArgument($leftTableFieldName, $loopType, LoopExtendsBuildModelCriteriaEvent $event): void
    {
        $keywords = $event->getLoop()?->getArgumentCollection()->get('keyword')?->getValue();

        if (!empty($keywords)) {
            $search = $event->getModelCriteria();
            [$tableJoin, $keywordJoin] = match ($loopType) {
                'content' => [ContentAssociatedKeywordTableMap::COL_CONTENT_ID, ContentAssociatedKeywordTableMap::COL_KEYWORD_ID],
                'category' => [CategoryAssociatedKeywordTableMap::COL_CATEGORY_ID, CategoryAssociatedKeywordTableMap::COL_KEYWORD_ID],
                'product' => [ProductAssociatedKeywordTableMap::COL_PRODUCT_ID, ProductAssociatedKeywordTableMap::COL_KEYWORD_ID],
                'folder' => [FolderAssociatedKeywordTableMap::COL_FOLDER_ID, FolderAssociatedKeywordTableMap::COL_KEYWORD_ID],
            };
            $search
                ->addJoin($leftTableFieldName, $tableJoin, Criteria::LEFT_JOIN) // Can also be left/right
                ->addJoin($keywordJoin, KeywordTableMap::COL_ID, Criteria::LEFT_JOIN)
            ;

            $matchMode = $event->getLoop()?->getArgumentCollection()->get('keyword_match_mode')?->getValue();

            if ('exact' === $matchMode) {
                $search->add(KeywordTableMap::COL_CODE, $keywords, Criteria::IN);
            } else {
                foreach ($keywords as $keyword) {
                    $search->add(KeywordTableMap::COL_CODE, "%$keyword%", Criteria::LIKE);
                }
            }
        }
    }

}
