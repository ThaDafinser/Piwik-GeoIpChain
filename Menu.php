<?php
namespace Piwik\Plugins\GeoIpChain;

use Piwik\Menu\MenuAdmin;
use Piwik\Menu\MenuReporting;
use Piwik\Piwik;

class Menu extends \Piwik\Plugin\Menu
{

    public function configureAdminMenu(MenuAdmin $menu)
    {
//         if (UserCountry::isGeoLocationAdminEnabled() && Piwik::hasUserSuperUserAccess()) {
            $menu->addSettingsItem('GeoIpChain', $this->urlForAction('adminIndex'), $order = 9999);
//         }
    }
}
