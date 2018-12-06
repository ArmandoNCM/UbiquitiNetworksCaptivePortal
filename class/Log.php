<?php

class Log{
    public static function print($message, $type = 'message', $file = '-', $line = '-'){
        $title = strtoupper($type);
        $start = "\n\n\t\t========================::UBIQUITI-START-$title::========================\n";
        $ending = "\n\t\t========================::UBIQUITI-ENDING-$title::========================\n\n";

        $messageFinal = $start . $message . $ending . "\n\tAt (File: $file Line: $line)\n\n";
        trigger_error($messageFinal);
    }
}
?>
