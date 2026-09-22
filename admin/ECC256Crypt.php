<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');

class ECC256Crypt {
    private static $keyFile = __DIR__ . '/../data/ecc_keys.php';

    private static function getKeys() {
        if (file_exists(self::$keyFile)) {
            return require self::$keyFile;
        }

        $config = [
            "private_key_type" => OPENSSL_KEYTYPE_EC,
            "curve_name" => "prime256v1"
        ];

        // Intentar rutas comunes en Windows (XAMPP/Laragon) y Linux (cPanel/Debian/Ubuntu/CentOS)
        $common_paths = [
            'C:/xampp/php/extras/ssl/openssl.cnf',
            'C:/xampp/apache/conf/openssl.cnf',
            'C:/xampp/php/extras/openssl/openssl.cnf',
            'C:/laragon/bin/php/php-8.0/extras/ssl/openssl.cnf',
            '/etc/ssl/openssl.cnf',
            '/usr/lib/ssl/openssl.cnf',
            '/etc/pki/tls/openssl.cnf',
            '/usr/local/ssl/openssl.cnf',
            '/etc/ssl/certs/openssl.cnf'
        ];

        $res = @openssl_pkey_new($config);
        if (!$res) {
            foreach ($common_paths as $path) {
                if (file_exists($path)) {
                    $config['config'] = $path;
                    $res = @openssl_pkey_new($config);
                    if ($res) break;
                }
            }
        }

        if (!$res) {
            $config_rsa = ["private_key_bits" => 2048, "private_key_type" => OPENSSL_KEYTYPE_RSA];
            $res = @openssl_pkey_new($config_rsa);
            if (!$res) {
                foreach ($common_paths as $path) {
                    if (file_exists($path)) {
                        $config_rsa['config'] = $path;
                        $res = @openssl_pkey_new($config_rsa);
                        if ($res) break;
                    }
                }
            }
        }

        $privateKey = '';
        $publicKey = '';

        if ($res) {
            if (isset($config['config'])) {
                @openssl_pkey_export($res, $privateKey, null, ['config' => $config['config']]);
            } else {
                @openssl_pkey_export($res, $privateKey);
            }
            $details = openssl_pkey_get_details($res);
            $publicKey = $details["key"] ?? '';
        }

        // Respaldo de seguridad en caso de fallo en la generación con OpenSSL
        if (empty($privateKey) || empty($publicKey)) {
            $privateKey = bin2hex(random_bytes(32));
            $publicKey = bin2hex(random_bytes(32));
        }

        $keys = [
            'private_key' => $privateKey,
            'public_key' => $publicKey
        ];

        $content = "<?php\ndefined('SECURE_ACCESS') or die('Direct access not permitted');\nreturn " . var_export($keys, true) . ";\n";
        file_put_contents(self::$keyFile, $content);

        return $keys;
    }

    public static function encrypt($data) {
        if (empty($data)) return '';
        
        $keys = self::getKeys();
        
        // Derivar clave simétrica con SHA-256 en formato binario
        $passphrase = hash('sha256', $keys['public_key'] . $keys['private_key'], true);
        
        // Usar AES-256-GCM con IV de 12 bytes y Tag de autenticación
        $iv = openssl_random_pseudo_bytes(12);
        $tag = '';
        
        $encrypted = @openssl_encrypt($data, 'aes-256-gcm', $passphrase, OPENSSL_RAW_DATA, $iv, $tag);
        
        if ($encrypted === false) {
            // Fallback a AES-256-CBC si GCM no se encuentra disponible en la versión PHP
            $passphrase_cbc = hash('sha256', $keys['public_key'] . $keys['private_key']);
            $iv_cbc = openssl_random_pseudo_bytes(16);
            $encrypted_cbc = @openssl_encrypt($data, 'aes-256-cbc', $passphrase_cbc, OPENSSL_RAW_DATA, $iv_cbc);
            if ($encrypted_cbc === false) {
                throw new Exception("Error crítico al encriptar los datos.");
            }
            return base64_encode($iv_cbc . $encrypted_cbc);
        }
        
        // Concatenar IV (12 bytes) + Tag (16 bytes) + Texto Cifrado y codificar en Base64
        return base64_encode($iv . $tag . $encrypted);
    }

    public static function decrypt($data) {
        if (empty($data)) return '';
        
        try {
            $keys = self::getKeys();
            $decoded = base64_decode($data);
            
            if ($decoded === false) {
                return '';
            }

            // Intentar desencriptar con AES-256-GCM
            if (strlen($decoded) >= 28) {
                $passphrase_gcm = hash('sha256', $keys['public_key'] . $keys['private_key'], true);
                $iv_gcm = substr($decoded, 0, 12);
                $tag_gcm = substr($decoded, 12, 16);
                $encrypted_gcm = substr($decoded, 28);
                
                $decrypted_gcm = @openssl_decrypt($encrypted_gcm, 'aes-256-gcm', $passphrase_gcm, OPENSSL_RAW_DATA, $iv_gcm, $tag_gcm);
                if ($decrypted_gcm !== false) {
                    return $decrypted_gcm;
                }
            }

            // Fallback a AES-256-CBC (compatibilidad con datos heredados)
            if (strlen($decoded) >= 16) {
                $passphrase_cbc = hash('sha256', $keys['public_key'] . $keys['private_key']);
                $iv_cbc = substr($decoded, 0, 16);
                $encrypted_cbc = substr($decoded, 16);
                
                $decrypted_cbc = @openssl_decrypt($encrypted_cbc, 'aes-256-cbc', $passphrase_cbc, OPENSSL_RAW_DATA, $iv_cbc);
                if ($decrypted_cbc !== false) {
                    return $decrypted_cbc;
                }
            }
        } catch (Throwable $e) {
            return '';
        }

        return '';
    }
}
