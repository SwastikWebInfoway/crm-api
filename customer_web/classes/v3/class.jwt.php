<?php
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class JwtHandler extends Helper{

    private $secretKey;
    private $algorithm;
    private $issuer;
    private $expireAfter; 

    public function __construct($respType = null){

        parent::__construct($respType);
        $dotenv = Dotenv::createImmutable('../');
        $dotenv->load();
        $dotenv->required([
            'JWT_SECRET_KEY', 
            'JWT_ALGORITHM',
            'JWT_ISSUER',
            'JWT_EXPIRE_AFTER'
        ]);

        $this->secretKey = $_ENV['JWT_SECRET_KEY'];
        $this->algorithm = $_ENV['JWT_ALGORITHM'];
        $this->issuer = $_ENV['JWT_ISSUER'];
        $this->expireAfter = (int)$_ENV['JWT_EXPIRE_AFTER'];
    }

    /**
     * Create JWT Token
     * @param array $payloadData Additional data to include in the token
     * @return string Encoded JWT token
     */
    public function createToken(array $payloadData = []): string {
        $issuedAt = time();
        $expire = $issuedAt + $this->expireAfter;

        $payload = array_merge([
            'iss' => $this->issuer,       // Issuer
            'iat' => $issuedAt,           // Issued at
            'exp' => $expire,            // Expiration time
        ], $payloadData);

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Verify JWT Token
     * @param string $token JWT token to verify
     * @return object|false Decoded token data or false if verification fails
     */
    public function verifyToken(string $token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return $decoded;
        } catch (Exception $e) {
            // Token is invalid
            return false;
        }
    }

    /**
     * Extract value from JWT Token
     * @param string $token JWT token
     * @param string $key Key to extract from payload
     * @return mixed|null The value or null if not found
     */
    public function getValueFromToken(string $token) {
        $decoded = $this->verifyToken($token);
        
        if ($decoded) {
            return $decoded;
        }
        
        return null;
    }
}