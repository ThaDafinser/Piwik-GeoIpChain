<?php

namespace Piwik\Plugins\GeoIpChain\Columns;

class Longitude
{
    //     protected $columnName = 'location_longitude_new';

//     protected $columnType = 'float(10, 6) DEFAULT NULL';

    /**
     * @param Request     $request
     * @param Visitor     $visitor
     * @param Action|null $action
     *
     * @return mixed
     */
    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
    }
}
