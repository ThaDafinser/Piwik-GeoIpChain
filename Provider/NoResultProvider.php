<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

use Geocoder\Provider\LocaleAwareProvider;
use Geocoder\Provider\LocaleTrait;
use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;

class NoResultProvider extends AbstractProvider implements LocaleAwareProvider
{
    use LocaleTrait;

    public function isWorking()
    {
        return true;
    }
    
    public function doesSupportIpV4()
    {
        return true;
    }
    
    public function doesSupportIpV6()
    {
        return true;
    }
    

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'NoResultProvider';
    }

    public function geocode($ipAddress)
    {
        throw new NoResult(sprintf('No results found for IP address "%s".', $ipAddress));
    }

    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude)
    {
        throw new UnsupportedOperation('The NoResultProvider provider is not able to do reverse geocoding.');
    }
}
