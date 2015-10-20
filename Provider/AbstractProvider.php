<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

use Geocoder\Provider\AbstractProvider as GeocoderAbstractProvider;
use Geocoder\Exception\NoResult;

abstract class AbstractProvider extends GeocoderAbstractProvider
{

    const IPV4_EXAMPLE = '46.206.0.37';

    const IPV6_EXAMPLE = '2607:f0d0:1002:0051:0000:0000:0000:0004';

    /**
     *
     * @return boolean
     */
    abstract public function isWorking();

    public function doesSupportIpV4()
    {
        if ($this->isWorking() === true) {
            try {
                $result = $this->geocode(self::IPV4_EXAMPLE);
            } catch (\Exception $ex) {
                if (! $ex instanceof NoResult) {
                    return false;
                }
            }
            
            return true;
        }
        
        return false;
    }

    public function doesSupportIpV6()
    {
        if ($this->isWorking() === true) {
            try {
                $result = $this->geocode(self::IPV6_EXAMPLE);
            } catch (\Exception $ex) {
                if (! $ex instanceof NoResult) {
                    return false;
                }
            }
            
            return true;
        }
        
        return false;
    }

    public function getSupportedFields()
    {
        return [];
    }
}
