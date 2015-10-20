<?php
namespace Piwik\Plugins\GeoIpChain\Provider\Adapter;

/**
 * Port of https://github.com/zhuzhichao/ip-location-zh/blob/master/src/IpLocationZh.php
 */
use Excpetion;

class IpIp
{

    private $file;

    private $fp;

    private $offset;

    private $index;

    private $cached = [];

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function __destruct()
    {
        if ($this->fp !== null) {
            fclose($this->fp);
        }
    }

    public function getFilePointer()
    {
        if ($this->fp !== null) {
            return $this->fp;
        }
        
        $this->fp = fopen($this->file, 'rb');
        if ($this->fp === false) {
            throw new Exception('Invalid file: "' . $this->file . '"');
        }
        
        $this->offset = unpack('Nlen', fread($this->fp, 4));
        if ($this->offset < 4) {
            throw new Exception('Invalid file: "' . $this->file . '"');
        }
        
        $this->index = fread($this->fp, $this->offset['len'] - 4);
    }

    public function find($ip)
    {
        if (empty($ip) === true) {
            return false;
        }
        
        $nip = gethostbyname($ip);
        $ipdot = explode('.', $nip);
        
        // check if valid ipv4 address
        if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !== 4) {
            return false;
        }
        
        if (isset($this->cached[$nip]) === true) {
            return $this->cached[$nip];
        }
        
        $nip2 = pack('N', ip2long($nip));
        
        $tmp_offset = (int) $ipdot[0] * 4;
        
        $start = unpack('Vlen', $this->index[$tmp_offset] . $this->index[$tmp_offset + 1] . $this->index[$tmp_offset + 2] . $this->index[$tmp_offset + 3]);
        
        $index_offset = null;
        $index_length = null;
        
        $max_comp_len = $this->offset['len'] - 1024 - 4;
        
        for ($start = $start['len'] * 8 + 1024; $start < $max_comp_len; $start += 8) {
            
            if ($this->index{$start} . $this->index{$start + 1} . $this->index{$start + 2} . $this->index{$start + 3} >= $nip2) {
                $index_offset = unpack('Vlen', $this->index{$start + 4} . $this->index{$start + 5} . $this->index{$start + 6} . "\x0");
                $index_length = unpack('Clen', $this->index{$start + 7});
                break;
            }
        }
        
        if ($index_offset === null) {
            return '3';
        }
        
        $fp = $this->getFilePointer();
        
        fseek($fp, $this->offset['len'] + $index_offset['len'] - 1024);
        
        $this->cached[$nip] = explode("\t", fread($fp, $index_length['len']));
        
        return $this->cached[$nip];
    }
}
