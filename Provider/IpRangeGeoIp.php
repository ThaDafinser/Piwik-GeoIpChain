<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;
use Piwik\Network;
use Geocoder\Provider\Provider;
use Piwik\Plugins\GeoIpChain\LocationProvider;

class IpRangeGeoIp extends AbstractProvider implements Provider, FileAwareProvider
{
    use FileAwareTrait;

    const DEFAULT_PATH = 'data/IpRangeGeoIp.data.php';

    private $data;

    public function __construct()
    {
        parent::__construct();
        
        $this->setFile(self::DEFAULT_PATH);
    }

    public function isWorking()
    {
        if (is_array($this->getData())) {
            return true;
        }
        
        return false;
    }
    
    public function getSupportedFields()
    {
        return [
            LocationProvider::COUNTRY_CODE,
            LocationProvider::COUNTRY_NAME,
    
            LocationProvider::SUB_LOCALITY,
    
            LocationProvider::POSTAL_CODE,
            LocationProvider::LOCALITY,
    
            LocationProvider::LATITUDE,
            LocationProvider::LONGITUDE,
        ];
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
        return 'IpRangeGeoIp';
    }

    private function getData()
    {
        if ($this->data !== null) {
            return $this->data;
        }
        
        $data = false;
        if (file_exists($this->file)) {
            $data = include $this->file;
        }
        
        $this->data = $data;
        
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function geocode($address)
    {
        if (! filter_var($address, FILTER_VALIDATE_IP)) {
            throw new UnsupportedOperation('The IpRangeGeoIp provider does not support street addresses, only IP addresses.');
        }
        
        if ('127.0.0.1' === $address) {
            return $this->returnResults([
                $this->getLocalhostDefaults()
            ]);
        }
        
        $ip = Network\IP::fromStringIP($address);
        
        $result = [];
        foreach ($this->getData() as $row) {
            if (isset($row['networks']) && $ip->isInRanges($row['networks']) === true) {
                if (isset($row['address']) && is_array($row['address'])) {
                    $result = $row['address'];
                }
                
                // match -> stop
                break;
            }
        }
        
        if (count($result) === 0) {
            throw new NoResult(sprintf('No results found for IP address "%s".', $address));
        }
        
        return $this->returnResults([
            $this->fixEncoding(array_merge($this->getDefaults(), [
                'latitude' => (isset($result['latitude']) ? $result['latitude'] : null),
                'longitude' => (isset($result['longitude']) ? $result['longitude'] : null),
                'bounds' => [
                    'south' => (isset($result['bounds']['south']) ? $result['bounds']['south'] : null),
                    'west' => (isset($result['bounds']['west']) ? $result['bounds']['west'] : null),
                    'north' => (isset($result['bounds']['north']) ? $result['bounds']['north'] : null),
                    'east' => (isset($result['bounds']['east']) ? $result['bounds']['east'] : null)
                ],
                'streetNumber' => (isset($result['streetNumber']) ? $result['streetNumber'] : null),
                'streetName' => (isset($result['streetName']) ? $result['streetName'] : null),
                'locality' => (isset($result['locality']) ? $result['locality'] : null),
                'postalCode' => (isset($result['postalCode']) ? $result['postalCode'] : null),
                'subLocality' => (isset($result['subLocality']) ? $result['subLocality'] : null),
                'adminLevels' => [],
                'country' => (isset($result['country']) ? $result['country'] : null),
                'countryCode' => (isset($result['countryCode']) ? $result['countryCode'] : null),
                'timezone' => (isset($result['timezone']) ? $result['timezone'] : null)
            ]))
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude)
    {
        throw new UnsupportedOperation('The IpRangeGeoIp provider is not able to do reverse geocoding.');
    }
}
