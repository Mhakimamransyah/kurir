<?php 

namespace App\Http\Helper;

class Distance{
    
    //Helper untuk mengukur jarak
    public function __construct()
    {
        //
    }

    public static function haversine_circle_distance($sumber,$tujuan){
        // [latitude, longitude]
        $radius_bumi        =  6371; // dalam satuan KM

        $latitude_asal      = deg2rad($sumber[0]);
        $latitude_tujuan    = deg2rad($tujuan[0]);
        $longitude_asal     = deg2rad($sumber[1]);
        $longitude_tujuan   = deg2rad($tujuan[1]);

        $delta_latitude  = $latitude_tujuan - $latitude_asal;
        $delta_longitude = $longitude_tujuan - $longitude_asal;

        $angle = 2 * asin(sqrt(pow(sin($delta_latitude / 2), 2)+ cos($latitude_asal) * cos($latitude_tujuan) * pow(sin($delta_longitude / 2), 2)));

        return (double)number_format((float)$angle * $radius_bumi, 2, '.', ''); 
    }

    public static function vincenty_circle_distance($sumber,$tujuan){
        // [latitude, longitude]
        $radius_bumi        =  6371; // dalam satuan KM

        $latitude_asal      = deg2rad($sumber[0]);
        $latitude_tujuan    = deg2rad($tujuan[0]);
        $longitude_asal     = deg2rad($sumber[1]);
        $longitude_tujuan   = deg2rad($tujuan[1]);

        $delta_longitude = $longitude_tujuan - $longitude_asal;
        $a = pow(cos($latitude_tujuan) * sin($delta_longitude), 2) + pow(cos($latitude_asal) * sin($latitude_tujuan) - sin($latitude_asal) * cos($latitude_tujuan) * cos($delta_longitude), 2);
        $b = sin($latitude_asal) * sin($latitude_tujuan) + cos($latitude_asal) * cos($latitude_tujuan) * cos($delta_longitude);

        $angle = atan2(sqrt($a), $b);
        return round($angle * $radius_bumi);
    }

}

?>

