<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Provider\Provider;
use Piwik\Plugins\GeoIpChain\Provider\Adapter\IpIp as IpIpAdapter;

class IpIp extends AbstractProvider implements Provider, FileAwareProvider
{
    use FileAwareTrait;

    const DEFAULT_PATH = 'data/17monipdb.dat';

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
            $adapter->getFilePointer();
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
        return 'IpIp';
    }

    /**
     *
     * @return IpIpAdapter
     */
    public function getAdapter()
    {
        if ($this->adapter !== null) {
            return $this->adapter;
        }
        
        $this->adapter = new IpIpAdapter($this->getFile());
        
        return $this->adapter;
    }

    /**
     * {@inheritDoc}
     */
    public function geocode($address)
    {
        if (! filter_var($address, FILTER_VALIDATE_IP)) {
            throw new UnsupportedOperation('The IpIp provider does not support street addresses, only IP addresses.');
        }
        
        if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new UnsupportedOperation(sprintf('The IpIp provider does not support IPv6 "%s".', $address));
        }
        
        if ('127.0.0.1' === $address) {
            return $this->returnResults([
                $this->getLocalhostDefaults()
            ]);
        }
        
        try {
            $adapter = $this->getAdapter();
            $result = $adapter->find($address);
        } catch (\Exception $ex) {
            // @todo better error message -> not working?
            throw new NoResult(sprintf('No results found for IP address "%s".', $address));
        }
        
        if (! isset($result[0]) || $result[0] == '') {
            throw new NoResult(sprintf('No results found for IP address "%s".', $address));
        }
        
        return $this->returnResults([
            $this->fixEncoding(array_merge($this->getDefaults(), $this->getRealResult($result)))
        ]);
    }

    private function getRealResult(array $result)
    {
        $countryName = null;
        if (isset($result[0]) && $result[0] != '') {
            $countryName = $result[0];
        }
        
        $subLocality = null;
        if (isset($result[1]) && $result[1] != '' && $result[0] != $result[1]) {
            $subLocality = $result[1];
        }
        
        return array(
            'subLocality' => $subLocality,
            
            'country' => $countryName
        );
    }

    /**
     * {@inheritDoc}
     */
    public function reverse($latitude, $longitude)
    {
        throw new UnsupportedOperation('The Ip2Location provider is not able to do reverse geocoding.');
    }
}
