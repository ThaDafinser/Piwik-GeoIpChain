<?php

namespace Piwik\Plugins\GeoIpChain\Columns;

use Piwik\Plugin\Dimension\VisitDimension;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Tracker\Action;

class Latitude extends VisitDimension
{
    //     protected $columnName = 'location_latitude_new';

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
        //         echo 'HERE';
//         exit();
//         $value = $this->getUrlOverrideValueIfAllowed('long', $request);
//         if ($value !== false) {
//             return $value;
//         }
//         $userInfo = $this->getUserInfo($request, $visitor);
//         $longitude = $this->getLocationDetail($userInfo, LocationProvider::LONGITUDE_KEY);

//         return $longitude;
    }
}
