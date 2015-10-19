<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

trait FileAwareTrait
{
    private $file;

    /**
     * Return the locale to be used in locale aware requests.
     *
     * In case there is no locale in use, null is returned.
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the file to be used.
     *
     * @param
     *            string|null
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}
