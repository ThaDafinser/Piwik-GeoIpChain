<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Provider\Provider;

class Ip2LocationIpV4 extends AbstractProvider implements Provider, FileAwareProvider
{
    use FileAwareTrait;

    const DEFAULT_PATH = 'data/IP2LOCATION-LITE-DB11.BIN';

    private $adapter;

    public function __construct()
    {
        parent::__construct();
        
        $this->setFile(self::DEFAULT_PATH);
    }

    public function isWorking()
    {
        try {
            $adapter = $this->getAdapter();
        } catch (\Exception $ex) {
            return false;
        }
        
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Ip2LocationIpV4';
    }

    /**
     *
     * @return \Ip2Location\Database
     */
    public function getAdapter()
    {
        if ($this->adapter !== null) {
            return $this->adapter;
        }
        
        $this->adapter = new \Ip2Location\Database($this->getFile(), \Ip2Location\Database::FILE_IO);
        
        return $this->adapter;
    }

    /**
     * {@inheritDoc}
     */
    public function geocode($address)
    {
        if (! filter_var($address, FILTER_VALIDATE_IP)) {
            throw new UnsupportedOperation('The Ip2LocationIpV4 provider does not support street addresses, only IP addresses.');
        }
        
        if ('127.0.0.1' === $address) {
            return $this->returnResults([
                $this->getLocalhostDefaults()
            ]);
        }
        
        try {
            $adapter = $this->getAdapter();
        } catch (\Exception $ex) {
            // @todo better error message -> not working?
            throw new NoResult(sprintf('No results found for IP address "%s".', $address));
        }
        
        $result = $adapter->lookup($address);
        
        if (! isset($result['countryCode']) || $result['countryCode'] === 'Invalid IP address.') {
            throw new UnsupportedOperation(sprintf('The Ip2LocationIpV4 provider does not support IPv6 "%s".', $address));
        }
        
        if (! isset($result['countryCode']) || $result['countryCode'] === '-') {
            throw new NoResult(sprintf('No results found for IP address "%s".', $address));
        }
        
        foreach ($result as $key => $value) {
            if ($value === 'This parameter is unavailable in selected .BIN data file. Please upgrade.') {
                $result[$key] = null;
                continue;
            }
            
            if ($value === '-') {
                $result[$key] = null;
                continue;
            }
        }
        
        return $this->returnResults([
            $this->fixEncoding(array_merge($this->getDefaults(), array(
                'latitude' => (isset($result['latitude']) ? $result['latitude'] : null),
                'longitude' => (isset($result['longitude']) ? $result['longitude'] : null),
                
                'locality' => (isset($result['cityName']) ? $result['cityName'] : null),
                'postalCode' => (isset($result['zipCode']) ? $result['zipCode'] : null),
                
                'subLocality' => (isset($result['regionName']) ? $result['regionName'] : null),
                
                'country' => (isset($result['countryName']) ? $result['countryName'] : null),
                'countryCode' => (isset($result['countryCode']) ? $result['countryCode'] : null)
            )))
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude)
    {
        throw new UnsupportedOperation('The Ip2Location provider is not able to do reverse geocoding.');
    }
}
