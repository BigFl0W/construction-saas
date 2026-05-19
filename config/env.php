<?php
/**
 * Lightweight environment loader.
 *
 * Loads values from `.env` and optional `.env.local` at the project root
 * without requiring a third-party dependency.
 */

if (!function_exists('tpv_project_root')) {
    function tpv_project_root() {
        return dirname(__DIR__);
    }
}

if (!function_exists('tpv_load_env_file')) {
    function tpv_load_env_file($filePath) {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            $value = str_replace(['\n', '\r', '\t'], ["\n", "\r", "\t"], $value);

            if (getenv($key) === false) {
                putenv($key . '=' . $value);
            }

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }

            if (!array_key_exists($key, $_SERVER)) {
                $_SERVER[$key] = $value;
            }
        }
    }
}

if (!function_exists('tpv_load_env')) {
    function tpv_load_env() {
        static $loaded = false;

        if ($loaded) {
            return;
        }

        $root = tpv_project_root();
        tpv_load_env_file($root . '/.env');
        tpv_load_env_file($root . '/.env.local');

        $loaded = true;
    }
}

if (!function_exists('tpv_env')) {
    function tpv_env($key, $default = null) {
        tpv_load_env();

        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('tpv_env_bool')) {
    function tpv_env_bool($key, $default = false) {
        $value = tpv_env($key, null);
        if ($value === null) {
            return $default;
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }
}

tpv_load_env();
