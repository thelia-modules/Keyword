<?php
/*************************************************************************************/
/*      Copyright (c) Open Studio                                                    */
/*      web : https://open.studio                                                    */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

/**
 * Created by Franck Allimant, OpenStudio <fallimant@openstudio.fr>
 * Date: 17/01/2025 11:04
 */
namespace Keyword\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class BackHookManager extends BaseHook
{
    /**
     * @param HookRenderEvent $event
     */
    public function onModuleConfigure(HookRenderEvent $event)
    {
        $event->add(
            $this->render('hook/module-configuration.html')
        );
    }

    public function onModuleConfigJs(HookRenderEvent $event)
    {
        $event->add(
            $this->render('hook/module-config-js.html')
        );
    }
    public function onCategoryTabContent(HookRenderEvent $event)
    {
        $event->add(
            $this->render('hook/category-edit.html')
        );
    }

    public function onContentTabContent(HookRenderEvent $event)
    {
        $event->add(
            $this->render('hook/content-edit.html')
        );
    }

    public function onFolderTabContent(HookRenderEvent $event)
    {
        $event->add(
            $this->render('hook/folder-edit.html')
        );
    }
    public function onProductTabContent(HookRenderEvent $event)
    {
        $event->add(
            $this->render('hook/product-edit.html')
        );
    }
}
