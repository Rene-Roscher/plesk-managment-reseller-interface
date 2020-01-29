<?php

namespace App\Helper;

/**
 * Class FormatHelper
 *
 * @package App\Helper
 */
class FormatHelper
{
    /**
     * @param        $amount
     * @param int    $decimals
     * @param string $dec_point
     * @param string $thousands_sep
     *
     * @return string
     */
    public static function money( $amount, $decimals = 2, $dec_point = ".", $thousands_sep = "" )
    {
        return number_format(round(trim(str_replace([ '.00', '€', ' ', ',' ], [ '', '', '', '.' ], $amount)), $decimals), $decimals, $dec_point, $thousands_sep);
    }

    public static function uptime($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a Tage %h Stunden %i Minuten %s Sekunden');
    }

    public static function byte($bytes) {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2, ',', '.') . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2, ',', '.') . ' kB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Formats Carbon to European Date.
     * @param Carbon $date
     * @param bool $timestamp
     * @return mixed
     */
    public static function date($date, $timestamp = true)
    {
        if($timestamp)
            return $date->format("d.m.Y - H:i:s");
        return $date->format("d.m.Y");
    }

    /**
     * @param $bytes
     * @return string
     */
    public static function bytesPerSecond($bytes) {
        $size = array('Bit/s','kBit/s','MBit/s','GBit/s','TBit/s','PBit/s','EBit/s','ZBit/s','YBit/s');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    /**
     * @param $packages
     * @return string
     */
    public static function packages($packages) {
        $size = array('','k','M','G','T','P','E','Z','Y');
        $factor = floor((strlen($packages) - 1) / 3);
        return sprintf("%.2f",$packages / pow(1000, $factor)) . $size[$factor];
    }

}