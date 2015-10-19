<?php
namespace Piwik\Plugins\GeoIpChain\Columns;

use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Tracker\Action;
use Piwik\Plugins\GeoIpChain\LocationProvider;

class Latitude extends AbstractVisitDimension
{

    protected $columnName = 'location_latitude_new';

    protected $columnType = 'float(10, 6) DEFAULT NULL';

    public function getName()
    {
        return 'Latitude';
    }

    /**
     *
     * @param Request $request            
     * @param Visitor $visitor            
     * @param Action|null $action            
     *
     * @return mixed
     */
    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        $result = $this->getLocationDetail(LocationProvider::LATITUDE_KEY, $visitor, $request);
        
        return $result;
    }

    public function onExistingVisit(Request $request, Visitor $visitor, $action)
    {
        $result = $this->getLocationDetail(LocationProvider::LATITUDE_KEY, $visitor, $request);
        
        return $result;
    }
}
