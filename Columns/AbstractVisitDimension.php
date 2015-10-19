<?php
namespace Piwik\Plugins\GeoIpChain\Columns;

use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Plugins\PrivacyManager\Config as PrivacyManagerConfig;
use Piwik\Plugins\GeoIpChain\LocationProvider;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Network\IPUtils;

abstract class AbstractVisitDimension extends VisitDimension
{

    private $locationProvider;

    public function getRequiredVisitFields()
    {
        return array(
            'location_ip',
            'location_browser_lang'
        );
    }

    /**
     *
     * @return \Piwik\Plugins\GeoIpChain\LocationProvider
     */
    private function getLocationProvider()
    {
        if ($this->locationProvider !== null) {
            return $this->locationProvider;
        }
        
        $locationProvider = new LocationProvider();
        
        $this->locationProvider = $locationProvider;
        
        return $locationProvider;
    }

    protected function getLocationDetail($field, Visitor $visitor, Request $request)
    {
        $ipAddress = $this->getIpAddress($visitor, $request);
        
        $locale = \Locale::acceptFromHttp($request->getBrowserLanguage());
        
        $provider = $this->getLocationProvider();
        $provider->setLocale($locale);
        
        $result = $provider->geocode($ipAddress);
        
        if(array_key_exists($field, $result) && $result[$field] !== null){
            return $result[$field];
        }
        
        return false;
    }

    /**
     *
     * @param Visitor $visitor            
     * @param Request $request            
     *
     * @return string
     */
    private function getIpAddress(Visitor $visitor, Request $request)
    {
        $privacyConfig = new PrivacyManagerConfig();
        
        $ip = $request->getIp();
        
        if ($privacyConfig->useAnonymizedIpForVisitEnrichment) {
            $ip = $visitor->getVisitorColumn('location_ip');
        }
        
        $ipAddress = IPUtils::binaryToStringIP($ip);
        
        return $ipAddress;
    }
}
