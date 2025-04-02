<?php
class Dotenv {
    private $path;
    private $variables = [];

    public function __construct($path) {
        $this->path = $path;
    }

    public function load() {
        $envFile = $this->path . '/.env';
        if (!file_exists($envFile)) {
            error_log("Dotenv: .env file not found at: " . $envFile);
            return;
        }

        error_log("Dotenv: Loading .env file from: " . $envFile);
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            error_log("Dotenv: Failed to read .env file");
            return;
        }

        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^(["\']).*\1$/', $value)) {
                    $value = substr($value, 1, -1);
                }
                
                $this->variables[$key] = $value;
                putenv("$key=$value");
                error_log("Dotenv: Loaded variable: $key");
            }
        }
    }

    public function required($variables) {
        foreach ($variables as $variable) {
            if (!isset($this->variables[$variable])) {
                error_log("Dotenv: Required variable '$variable' is not set");
                throw new Exception("Required environment variable '$variable' is not set.");
            }
        }
    }
} 