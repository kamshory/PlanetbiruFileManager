<?php

/**
 * Class HTPasswd
 *
 * A class for handling password hashing and authentication using MD5 (APR1) and SHA1 encryption.
 */
class HTPasswd
{
	const APRMD5_ALPHABET = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	const BASE64_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	
	/**
     * Hashes a password using the Apache MD5 APR1 hashing algorithm.
     *
     * @param string $mdp The plain text password.
     * @param string|null $salt An optional salt value. If not provided, a new one will be generated.
     *
     * @return string The hashed password in APR1 format.
     */
	public static function hash($mdp, $salt = null) // NOSONAR
	{
		if (is_null($salt)) {
			$salt = self::salt();
		}
		$salt = substr($salt, 0, 8);
		$max = strlen($mdp);
		$context = $mdp . '$apr1$' . $salt; // NOSONAR
		$binary = pack('H32', md5($mdp . $salt . $mdp));
		for ($i = $max; $i > 0; $i -= 16) {
			$context .= substr($binary, 0, min(16, $i));
		}
		for ($i = $max; $i > 0; $i >>= 1) {
			$context .= ($i & 1) ? chr(0) : $mdp[0];
		}
		$binary = pack('H32', md5($context));
		for ($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $mdp : $binary;
			if ($i % 3) {
				$new .= $salt;
			}
			if ($i % 7) {
				$new .= $mdp;
			}
			$new .= ($i & 1) ? $binary : $mdp;
			$binary = pack('H32', md5($new));
		}
		$hash = '';
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) {
				$j = 5;
			}
			$hash = $binary[$i] . $binary[$k] . $binary[$j] . $hash;
		}
		$hash = chr(0) . chr(0) . $binary[11] . $hash;
		$hash = strtr(
			strrev(substr(base64_encode($hash), 2)),
			self::BASE64_ALPHABET,
			self::APRMD5_ALPHABET
		);
		return '$apr1$' . $salt . '$' . $hash;
	}

	/**
     * Generates a random salt using the APR-MD5 alphabet.
     *
     * @return string A randomly generated 8-character salt.
     */
	public static function salt()
	{
		$alphabet = self::APRMD5_ALPHABET;
		$salt = '';
		for ($i = 0; $i < 8; $i++) {
			$offset = hexdec(bin2hex(openssl_random_pseudo_bytes(1))) % 64;
			$salt .= $alphabet[$offset];
		}
		return $salt;
	}

	/**
     * Verifies a plain text password against an APR1 hashed password.
     *
     * @param string $plain The plain text password.
     * @param string $hash The hashed password to compare against.
     *
     * @return bool True if the password matches the hash, false otherwise.
     */
	public static function check($plain, $hash)
	{
		$parts = explode('$', $hash);
		return self::hash($plain, $parts[2]) === $hash;
	}

	/**
     * Hashes a password using the SHA1 algorithm.
     *
     * @param string $password The plain text password.
     *
     * @return string The SHA1 hashed password in the "{SHA}" format.
     */
	public static function crypt_sha1($password)
	{
		return "{SHA}" . base64_encode(hex2bin(sha1($password)));
	}

	/**
     * Authenticates a user by their username and password against stored credentials.
     *
     * @param string $username The username to authenticate.
     * @param string $password The plain text password.
     * @param string $stored The stored credentials in the format of a .htpasswd file.
     *
     * @return bool True if the username and password are valid, false otherwise.
     */
	public static function auth($username, $password, $stored)
	{
		$buff = $stored;
		$buff = str_replace("\n", "\r\n", $buff);
		$buff = str_replace("\r\n\n", "\r\n", $buff);
		$buff = str_replace("\r", "\r\n", $buff);
		$buff = str_replace("\r\r\n", "\r\n", $buff);
		$buffs = explode("\r\n", $buff);
		foreach ($buffs as $line) {
			$line = trim($line, "\r\n");
			$arr = explode(":", $line, 2);
			if ($arr[0] == $username) {
				if (stripos($arr[1], '{SHA}') === 0) {
					return self::crypt_sha1($password) === $arr[1];
				} else if (stripos($arr[1], '$apr1$') === 0) {
					return self::check($password, $arr[1]);
				} else {
					return false;
				}
			}
		}
	}
}
