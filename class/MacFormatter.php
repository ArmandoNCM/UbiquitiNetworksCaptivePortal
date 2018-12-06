<?php

class MacFormatter {

    public static function formatMac($mac){

        $bytes = array();

        $i = 0;
        
        while ($i < 12){

            $bytes[] = strtoupper(substr($mac, $i, 2));
            $i = $i + 2;
        }

        return implode(':', $bytes);
    }

}

?>