<?php
namespace Piwik\Plugins\GeoIpChain;

use Piwik\Config;
use Geocoder\Provider\LocaleAwareProvider;
use Piwik\Common;
use Piwik\Plugins\GeoIpChain\Provider\FileAwareProvider;
use Geocoder\Provider\Chain;

class LocationProvider
{

    const LATITUDE = 'latitude';

    const LONGITUDE = 'longitude';

    const STREET_NUMBER = 'streetNumber';

    const STREET_NAME = 'streetName';

    const LOCALITY = 'locality';

    const POSTAL_CODE = 'postalCode';

    const SUB_LOCALITY = 'subLocality';

    const COUNTRY_CODE = 'countryCode';

    const COUNTRY_NAME = 'country';

    const TIMEZONE = 'timezone';

    private $locale;

    /**
     *
     * @var \GeoIpChain\Provider\AbstractProvider[]
     */
    private $providers;

    private $workingProviders;

    public function setLocale($locale)
    {
        $this->locale = $locale;
        
        // set locale on change!
        foreach ($this->getProviders() as $provider) {
            if ($instance instanceof LocaleAwareProvider) {
                $instance->setLocale($locale);
            }
        }
    }

    public function getLocale()
    {
        if ($this->locale === null) {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $this->locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            }
        }
        
        return $this->locale;
    }

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
                    $instance->setLocale($this->getLocale());
                }
                
                if ($instance instanceof FileAwareProvider) {}
                
                $providers[] = $instance;
            }
        }
        
        $this->providers = $providers;
        
        return $providers;
    }

    /**
     *
     * @return \GeoIpChain\Provider\AbstractProvider[]
     */
    public function getWorkingProviders()
    {
        if ($this->workingProviders !== null) {
            return $this->workingProviders;
        }
        
        $providers = [];
        foreach ($this->getProviders() as $provider) {
            /* @var $provider \GeoIpChain\Provider\AbstractProvider */
            if ($provider->isWorking() === true) {
                $providers[] = $provider;
            }
        }
        
        $this->workingProviders = $providers;
        
        return $providers;
    }

    /**
     *
     * @param unknown $ipAddress            
     */
    public function geocode($ipAddress)
    {
        // simple array memory cache
        if (isset($this->results[$ipAddress])) {
            return $this->results[$ipAddress];
        }
        
        $chain = new Chain($this->getWorkingProviders());
        
        try {
            $result = $chain->geocode($ipAddress);
        } catch (\Exception $ex) {
            var_dump($ex);
            exit();
        }
        
        $firstResult = $result->first();
        
        return [
            self::LATITUDE => $firstResult->getLatitude(),
            self::LONGITUDE => $firstResult->getLongitude()
        ];
    }
}
