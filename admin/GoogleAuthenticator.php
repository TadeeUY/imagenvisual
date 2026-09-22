<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');

class GoogleAuthenticator {
    private static $_lut = [
        'A' => 0,  'B' => 1,  'C' => 2,  'D' => 3,
        'E' => 4,  'F' => 5,  'G' => 6,  'H' => 7,
        'I' => 8,  'J' => 9,  'K' => 10, 'L' => 11,
        'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15,
        'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
        'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
        'Y' => 24, 'Z' => 25, '2' => 26, '3' => 27,
        '4' => 28, '5' => 29, '6' => 30, '7' => 31
    ];

    public static function createSecret($length = 16) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        // Utilizar random_int para mayor seguridad criptográfica si está disponible
        for ($i = 0; $i < $length; $i++) {
            try {
                $secret .= $chars[random_int(0, 31)];
            } catch (Exception $e) {
                $secret .= $chars[rand(0, 31)];
            }
        }
        return $secret;
    }

    public static function getCode($secret, $timeSlice = null) {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }

        $secretkey = self::_base32Decode($secret);

        // Pack time slice in binary (64-bit integer, big-endian)
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        
        // HMAC-SHA1
        $hm = hash_hmac('sha1', $time, $secretkey, true);
        
        // Offset is the last nibble of the hash
        $offset = ord(substr($hm, -1)) & 0x0F;
        
        // Grab 4 bytes of the hash starting at offset
        $hashpart = substr($hm, $offset, 4);
        
        // Unpack 32-bit integer (big-endian)
        $value = unpack('N', $hashpart);
        $value = $value[1];
        
        // Truncate to signed 32-bit (clear MSB)
        $value = $value & 0x7FFFFFFF;
        
        $modulo = pow(10, 6);
        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }

    public static function verifyCode($secret, $code, $discrepancy = 1, $currentTimeSlice = null) {
        if ($currentTimeSlice === null) {
            $currentTimeSlice = floor(time() / 30);
        }

        if (strlen($code) != 6 || !is_numeric($code)) {
            return false;
        }

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = self::getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    public static function getQRText($username, $secret, $title = 'Imagen Visual') {
        return 'otpauth://totp/' . rawurlencode($title . ':' . $username) . '?secret=' . $secret . '&issuer=' . rawurlencode($title);
    }

    private static function _base32Decode($secret) {
        if (empty($secret)) return '';
        
        $secret = strtoupper($secret);
        $secret = str_replace('=', '', $secret);
        
        $buf = '';
        $val = 0;
        $vBits = 0;
        
        $len = strlen($secret);
        for ($i = 0; $i < $len; $i++) {
            $c = $secret[$i];
            if (!isset(self::$_lut[$c])) {
                continue;
            }
            
            $val = ($val << 5) | self::$_lut[$c];
            $vBits += 5;
            
            if ($vBits >= 8) {
                $vBits -= 8;
                $buf .= chr(($val >> $vBits) & 0xFF);
            }
        }
        return $buf;
    }
}
