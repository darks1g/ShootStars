<?php

/**
 * TokenManager Class
 * 
 * Manages authentication tokens (e.g., password reset tokens) using a file-based storage system.
 * Useful when database schema modifications are restricted.
 * 
 * Storage Path: backend/data/password_resets.json
 * 
 * @package ShootStars\Auth
 */
class TokenManager {
    private $filePath;

    /**
     * Constructor
     * Initializes storage directory and file if they don't exist.
     */
    public function __construct() {
        $this->filePath = __DIR__ . '/../../backend/data/password_resets.json';
        if (!file_exists(dirname($this->filePath))) {
            mkdir(dirname($this->filePath), 0777, true);
        }
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([]));
        }
    }

    /**
     * Reads tokens from JSON file.
     * @return array Associative array of tokens.
     */
    private function readTokens() {
        $json = file_get_contents($this->filePath);
        return json_decode($json, true) ?? [];
    }

    /**
     * Writes tokens to JSON file.
     * @param array $tokens The tokens array.
     */
    private function writeTokens($tokens) {
        file_put_contents($this->filePath, json_encode($tokens, JSON_PRETTY_PRINT));
    }

    /**
     * Creates a new reset token for an email.
     * Removes any existing tokens for that email.
     * 
     * @param string $email The user's email.
     * @return string The generated secure token.
     */
    public function createToken($email) {
        $tokens = $this->readTokens();
        
        // Remove existing tokens for this email to prevent spamming
        foreach ($tokens as $t => $data) {
            if ($data['email'] === $email) {
                unset($tokens[$t]);
            }
        }

        $token = bin2hex(random_bytes(32));
        $expiry = time() + 3600; // 1 hour

        $tokens[$token] = [
            'email' => $email,
            'expiry' => $expiry
        ];

        $this->writeTokens($tokens);
        return $token;
    }

    /**
     * Validates a token.
     * Checks if it exists and hasn't expired.
     * 
     * @param string $token The token string.
     * @return string|false The email associated with the token if valid, false otherwise.
     */
    public function validateToken($token) {
        $tokens = $this->readTokens();

        if (!isset($tokens[$token])) {
            return false;
        }

        $data = $tokens[$token];
        // Check Expiry
        if (time() > $data['expiry']) {
            unset($tokens[$token]); // Cleanup expired
            $this->writeTokens($tokens);
            return false;
        }

        return $data['email'];
    }

    /**
     * Removes a used token.
     * 
     * @param string $token The token to remove.
     */
    public function removeToken($token) {
        $tokens = $this->readTokens();
        if (isset($tokens[$token])) {
            unset($tokens[$token]);
            $this->writeTokens($tokens);
        }
    }
}
