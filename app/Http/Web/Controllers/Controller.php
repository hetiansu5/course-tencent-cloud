<?php

namespace App\Http\Web\Controllers;

use App\Caches\NavTreeList as NavTreeListCache;
use App\Caches\Setting as SettingCache;
use App\Services\Auth\Web as WebAuth;
use App\Traits\Response as ResponseTrait;
use App\Traits\Security as SecurityTrait;
use Phalcon\Mvc\Dispatcher;

class Controller extends \Phalcon\Mvc\Controller
{

    protected $siteSettings;
    protected $navList;
    protected $authUser;

    use ResponseTrait, SecurityTrait;

    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        if (!$this->checkRateLimit()) {
            $dispatcher->forward([
                'controller' => 'public',
                'action' => 'throttle',
            ]);
            return false;
        }

        if ($this->isNotSafeRequest()) {
            if (!$this->checkHttpReferer() || !$this->checkCsrfToken()) {
                $dispatcher->forward([
                    'controller' => 'public',
                    'action' => 'robot',
                ]);
                return false;
            }
        }

        $this->siteSettings = $this->getSiteSettings();
        $this->navList = $this->getNavList();
        $this->authUser = $this->getAuthUser();

        return true;
    }

    public function initialize()
    {
        $this->view->setVar('auth_user', $this->authUser);
        $this->view->setVar('site_settings', $this->siteSettings);
        $this->view->setVar('top_nav_list', $this->navList['top']);
        $this->view->setVar('btm_nav_list', $this->navList['bottom']);
    }

    protected function getAuthUser()
    {
        /**
         * @var WebAuth $auth
         */
        $auth = $this->getDI()->get('auth');

        return $auth->getAuthInfo();
    }

    protected function getNavList()
    {
        $treeListCache = new NavTreeListCache();

        return $treeListCache->get();
    }

    protected function getSiteSettings()
    {
        $settingCache = new SettingCache();

        return $settingCache->get('site');
    }

}
