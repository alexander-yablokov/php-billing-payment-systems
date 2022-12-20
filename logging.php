<?php
function write_log($str)
    {
        $date = date("d.m.Y H:i:s");
        file_put_contents("debug.log", $date . ' ' . $str."\n", FILE_APPEND);
    }
