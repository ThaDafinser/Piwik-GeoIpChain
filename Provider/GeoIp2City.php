<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Provider\Provider;

class GeoIp2City extends AbstractProvider implements Provider, FileAwareProvider
{
    use FileAwareTrait;

    const DEFAULT_PATH = 'data/GeoLite2-City.mmdb';

    private $adapter;

    public function __construct()
    {
        parent::__construct();
        
        $this->setFile(self::DEFAULT_PATH);
    }

    public function isWorking()
    {
        try {
            $this->getProvider();
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
        return 'GeoIp2City';
    }

    /**
     *
     * @return \Geocoder\Provider\GeoIP2
     */
    public function getProvider()
    {
        if ($this->adapter !== null) {
            return $this->adapter;
        }
        
        // Maxmind GeoIP2 Provider: e.g. the database reader
        $reader = new \GeoIp2\Database\Reader($this->getFile());
        
        $adapter = new \Geocoder\Adapter\GeoIP2Adapter($reader);
        $geocoder = new \Geocoder\Provider\GeoIP2($adapter);
        
        return $geocoder;
    }

    /**
     * {@inheritDoc}
     */
    public function geocode($address)
    {
        if ($this->isWorking() !== true) {
            throw new NoResult(sprintf('No results found for IP address "%s".', $address));
        }
        
        $provider = $this->getProvider();
        
        return $provider->geocode($address);
    }

    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude)
    {
        throw new UnsupportedOperation('The Ip2Location provider is not able to do reverse geocoding.');
    }
}
