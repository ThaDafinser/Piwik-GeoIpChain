<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

use Geocoder\Provider\LocaleAwareProvider;
use Geocoder\Provider\LocaleTrait;
use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;

class BrowserLocale extends AbstractProvider implements LocaleAwareProvider
{
    use LocaleTrait;

    public function isWorking()
    {
        return true;
    }

    public function doesSupportIpV4()
    {
        return false;
    }

    public function doesSupportIpV6()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'BrowserLocale';
    }

    public function geocode($ipAddress)
    {
        $region = \Locale::getRegion($this->getLocale());
        
        $countryName = \Locale::getDisplayRegion($this->getLocale(), 'en');
        $countryCode = \Locale::getRegion($this->getLocale());
        
        if ($countryCode == '') {
            throw new NoResult(sprintf('No results found for IP address "%s".', $ipAddress));
        }
        
        return $this->returnResults([
            $this->fixEncoding(array_merge($this->getDefaults(), array(
                'country' => $countryName,
                'countryCode' => $countryCode
            )))
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude)
    {
        throw new UnsupportedOperation('The BrowserLocale provider is not able to do reverse geocoding.');
    }
}
