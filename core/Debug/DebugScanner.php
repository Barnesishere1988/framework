<?php
namespace FW\Debug;

class DebugScanner {

    public static function run() {
        $out = "<h1>Included Files</h1>";

        foreach (get_included_files() as $file) {
            if (str_contains(strtolower($file), "view")) {
                $out .= "<p>$file</p>";
            }
        }

        return $out;
    }
}
