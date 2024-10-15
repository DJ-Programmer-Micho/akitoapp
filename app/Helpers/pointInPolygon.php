<?php

use Illuminate\Support\Facades\Log;

function pointInPolygon($latitude, $longitude, $polygon)
{
    // Log::info('Checking point in polygon:', [
    //     'latitude' => $latitude,
    //     'longitude' => $longitude,
    //     'polygon' => $polygon
    // ]);

    $inside = false;

    for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
        $xi = $polygon[$i]['lat'];
        $yi = $polygon[$i]['lng'];
        $xj = $polygon[$j]['lat'];
        $yj = $polygon[$j]['lng'];

        $intersects = (($yi > $longitude) != ($yj > $longitude)) &&
            ($latitude < ($xj - $xi) * ($longitude - $yi) / ($yj - $yi) + $xi);
        if ($intersects) {
            $inside = !$inside;
        }
    }

    return $inside;
}
