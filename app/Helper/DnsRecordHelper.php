<?php
namespace App\Helper;

class DnsRecordHelper
{
    public static function isValid($sld, $ttl, $type, $data) {
        if (strlen($sld) > 63)
            return false;
        if ($ttl != '' && ($ttl < 300 || $ttl > 604800))
            return false;
        $sld_match = preg_match('/^([A-Za-z0-9]+)|@|\*$/', $sld);
        if (!$sld_match && $type != 'SRV')
            return false;
        switch ($type) {
            case 'A':
                if (filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false)
                    return false;
                break;
            case 'AAAA':
                if (filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
                    return false;
                break;
            case 'SRV':
                $sld_match = preg_match('/^_[A-Za-z0-9]+\._(udp|tcp|tls)(\.[A-Za-z0-9]+)?$/', $sld);
                if (!$sld_match)
                    return false;
                $match = preg_match('/^([0-9]+) ([0-9]+) ([0-9]+) (([A-Za-z0-9-]{1,63}\.){0,2}[A-Za-z0-9]{2,63})\.$/', $data, $matches);
                if ($match) {
                    $priority = $matches[1];
                    $weight = $matches[2];
                    $port = $matches[3];
                    if ($priority >= 0 && $priority <= 65535 && $weight >= 0 && $weight <= 65535 && $port >= 0 && $port <= 65535)
                        return true;
                    else
                        return false;
                } else {
                    return false;
                }
                break;
            case 'TXT':
                $length = strlen($data);
                return $length > 0 && $length <= 255;
                break;
            case 'HTTP_FRAME':
                $length = strlen($data);
                return $length > 0 && $length <= 255;
                break;
            case 'HTTP_REDIRECT':
                $length = strlen($data);
                return $length > 0 && $length <= 255;
                break;
            case 'CNAME':
                if ($sld == '@')
                    return false;
                $match = preg_match('/^([A-Za-z0-9-]{1,63}\.){0,3}[A-Za-z0-9]{2,63}[\.]?$/', $data, $matches);
                if ($match) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'MX':
                $match = preg_match('/^(([0-9]+) )?([A-Za-z0-9-]{1,63}\.){0,4}[A-Za-z0-9]{2,63}[\.]?$/', $data, $matches);
                if ($match) {
                    $priority = $matches[1];
                    if ($priority >= 0 && $priority <= 65535)
                        return true;
                    else
                        return false;
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }

        return true;
    }
}
