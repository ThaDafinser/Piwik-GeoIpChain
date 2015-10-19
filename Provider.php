<?php
namespace Piwik\Plugins\GeoIpChain;

use Piwik\Config;
use Geocoder\Provider\LocaleAwareProvider;
use Piwik\Common;
use Piwik\Plugins\GeoIpChain\Provider\FileAwareProvider;

class Provider
{

    /**
     *
     * @var \GeoIpChain\Provider\AbstractProvider[]
     */
    private $providers;

    /**
     *
     * @return \GeoIpChain\Provider\AbstractProvider[]
     */
    public function getProviders()
    {
        if ($this->providers !== null) {
            return $this->providers;
        }
        
        $config = Config::getInstance()->GeoIpChain;
        
        $providers = [];
        if (isset($config['providers'])) {
            foreach ($config['providers'] as $class) {
                
                $instance = new $class();
                
                if ($instance instanceof LocaleAwareProvider) {
                    $instance->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));
                }
                
                if ($instance instanceof FileAwareProvider) {
                    // $instance->setFile(...);
                }
                
                $providers[] = $instance;
            }
        }
        
        return $providers;
    }

    /**
     *
     * @return \GeoIpChain\Provider\AbstractProvider[]
     */
    public function getWorkingProviders()
    {
        $providers = [];
        foreach ($this->getProviders() as $provider) {
            /* @var $provider \GeoIpChain\Provider\AbstractProvider */
            if ($provider->isWorking() === true) {
                $providers[] = $provider;
            }
        }
        
        return $providers;
    }
}
