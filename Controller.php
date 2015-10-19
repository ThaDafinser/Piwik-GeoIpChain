<?php
namespace Piwik\Plugins\GeoIpChain;

use Exception;
use Piwik\Common;
use Piwik\DataTable\Renderer\Json;
use Piwik\Http;
use Piwik\IP;
use Piwik\Piwik;
use Piwik\Plugins\UserCountry\LocationProvider\GeoIp\ServerBased;
use Piwik\Plugins\UserCountry\LocationProvider\GeoIp;
use Piwik\Plugins\UserCountry\LocationProvider;
use Piwik\Plugins\UserCountry\LocationProvider\DefaultProvider;
use Piwik\Plugins\UserCountry\LocationProvider\GeoIp\Pecl;
use Piwik\View;
use Geocoder\Exception\NoResult;
use Piwik\Plugins\GeoIpChain\Provider\FileAwareProvider;
use Geocoder\Provider\LocaleAwareProvider;
use Geocoder\Exception\UnsupportedOperation;

/**
 */
class Controller extends \Piwik\Plugin\ControllerAdmin
{
    private function getDefaultIp()
    {
        return IP::getIpFromHeader();
    }

    private function getUsedIp()
    {
        return trim(Common::getRequestVar('usedIp', $this->getDefaultIp(), 'string'));
    }

    private function getDefaultAcceptLanguage()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        
        return '';
    }

    public function getUsedAcceptLanguage()
    {
        return Common::getRequestVar('usedAcceptLanguage', $this->getDefaultAcceptLanguage(), 'string');
    }

    public function adminIndex()
    {
        Piwik::checkUserHasSuperUserAccess();
        
        $view = new View('@GeoIpChain/adminIndex');
        
        $providerHandler = new Provider();
        
        $providers = [];
        foreach ($providerHandler->getProviders() as $provider) {
            /* @var $provider \Piwik\Plugins\GeoIpChain\Provider\AbstractProvider */
            $data = [
                'name' => $provider->getName(),
                'isWorking' => $provider->isWorking(),
                'usedFile' => null,
                'doesSupportIpV4' => $provider->doesSupportIpV4(),
                'doesSupportIpV6' => $provider->doesSupportIpV6()
            ];
            
            if ($provider instanceof LocaleAwareProvider) {
                $provider->setLocale(\Locale::acceptFromHttp($this->getUsedAcceptLanguage()));
            }
            
            if ($provider instanceof FileAwareProvider) {
                $data['usedFile'] = $provider->getFile();
            }
            
            $data['result'] = false;
            
            if ($provider->isWorking() === true) {
                try {
                    /* @var $result \Geocoder\Model\AddressCollection */
                    $result = $provider->geocode($this->getUsedIp());
                    
                    /* @var $firstResult \Geocoder\Model\Address */
                    $firstResult = $result->first();
                    
                    $data['result'] = $firstResult;
                } catch (NoResult $ex) {
                } catch (UnsupportedOperation $ex) {
                }
            }
            
            $providers[] = $data;
        }
        
        $view->usedIp = $this->getUsedIp();
        $view->defaultIp = $this->getDefaultIp();
        $view->usedAcceptLanguage = $this->getUsedAcceptLanguage();
        $view->defaultAcceptLanguage = $this->getDefaultAcceptLanguage();
        
        $view->providers = $providers;
        
        $this->setBasicVariablesView($view);
        $this->setBasicVariablesAdminView($view);
        
        return $view->render();
    }
}
