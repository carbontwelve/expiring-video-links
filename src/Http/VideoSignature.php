<?php

namespace App\Http;

class VideoSignature
{

    private $expires;

    private $encryptionKey;

    private $iVectorLength;

    /**
     * VideoSignature constructor.
     * @param int $expires
     * @param string $encryptionKey
     * @throws \Exception
     */
    public function __construct(int $expires = 10, string $encryptionKey)
    {
        if (!in_array('aes-256-ctr', openssl_get_cipher_methods(TRUE))) {
            throw new \Exception(__METHOD__ . " Unknown cipher aes-256-ctr");
        }

        if (!in_array('sha256', openssl_get_md_methods(TRUE))) {
            throw new \Exception(__METHOD__ . " Unknown digest sha256");
        }

        $this->expires = $expires;
        $this->encryptionKey = $encryptionKey;
        $this->iVectorLength = openssl_cipher_iv_length('aes-256-ctr');
    }

    /**
     * @param string $str
     * @return string
     * @throws \Exception
     */
    private function encryptString(string $str): string
    {
        $keyHash = openssl_digest($this->encryptionKey, 'sha256', TRUE);
        $iVector = openssl_random_pseudo_bytes($this->iVectorLength);

        $encrypted = openssl_encrypt($str, 'aes-256-ctr', $keyHash, OPENSSL_RAW_DATA, $iVector);
        if ($encrypted === FALSE) {
            throw new \Exception(__METHOD__ . ' Failed: ' . openssl_error_string());
        }

        // Return the digest, IV and encrypted data.
        $payload = base64_encode($iVector . $encrypted);
        $digest  = openssl_digest($this->encryptionKey . $payload, 'sha256');
        return $digest . '|' . $payload;
    }

    /**
     * @param string $str
     * @return string
     * @throws \Exception
     */
    private function decryptString(string $str): string
    {
        $keyHash = openssl_digest($this->encryptionKey, 'sha256', TRUE);

        // Test digest
        $inputs  = explode('|', $str);
        $digest  = $inputs[0];
        $compare = openssl_digest($this->encryptionKey . $inputs[1], 'sha256');
        if ($compare != $digest){
            throw new \Exception(__METHOD__ . ' Authentication digest failure');
        }
        $rawData = base64_decode($inputs[1]);

        // Get IV and encrypted data
        $iVector = substr($rawData, 0, $this->iVectorLength);
        $rawText = substr($rawData, $this->iVectorLength);
        $decrypt = openssl_decrypt($rawText, 'aes-256-ctr', $keyHash, OPENSSL_RAW_DATA, $iVector);

        if ($decrypt === FALSE) {
            throw new \Exception(__METHOD__ . ' Failed: ' . openssl_error_string());
        }

        return $decrypt;
    }

    /**
     * @param array $payload
     * @return string
     * @throws \Exception
     */
    public function getSignedPayloadUri(array $payload = []): string
    {
        return urlencode($this->encryptString(json_encode(['payload' => $payload, 'expiresAfter' => time() + $this->expires])));
    }

    /**
     * @param string $uri
     * @return array
     * @throws \Exception
     */
    public function getPayloadFromSignedUri(string $uri): array
    {
        $data = json_decode($this->decryptString(urldecode($uri)), true);

        if (is_null($data) || ! isset($data['expiresAfter']) || (isset($data['expiresAfter']) && $data['expiresAfter'] < time())) {
            throw new \Exception('Invalid or expired uri.');
        }

        return $data['payload'];
    }
}