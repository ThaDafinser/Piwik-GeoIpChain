<?php
namespace Piwik\Plugins\GeoIpChain\Provider;

interface FileAwareProvider
{
    /**
     * Return the locale to be used in locale aware requests.
     *
     * In case there is no locale in use, null is returned.
     *
     * @return string|null
     */
    public function getFile();

    /**
     * Sets the file to be used.
     *
     * @param
     *            string|null
     */
    public function setFile($file);
}
