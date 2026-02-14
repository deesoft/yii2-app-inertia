<?php

function loadEnv($path)
{
    $temp = [];
    $file = $path;
    if (!is_file($file) && is_file(rtrim($path, '/') . '/.env')) {
        $file = rtrim($path, '/') . '/.env';
    }
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) {
        return;
    }
    foreach ($lines as $row) {
        $row = trim($row);
        if (empty($row) || preg_match('/^(#|;)/', $row)) {
            continue;
        }
        $parts = explode('=', $row, 2);
        $key = trim($parts[0]);
        $value = isset($parts[1]) ? trim($parts[1]) : null;
        if ($value === null || strtoupper($value) == 'NULL' || strtoupper($value) == '(NULL)') {
            $temp[$key] = null;
        } elseif (strtoupper($value) == 'TRUE' || strtoupper($value) == '(TRUE)') {
            $temp[$key] = true;
        } elseif (strtoupper($value) == 'FALSE' || strtoupper($value) == '(FALSE)') {
            $temp[$key] = false;
        } else {
            if (preg_match('/^\'(.*)\'$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            }
            $value = preg_replace_callback('/\$\{([^\}]+)\}/', function ($matches) use ($temp) {
                if (array_key_exists($matches[1], $temp)) {
                    return $temp[$matches[1]];
                }
                return $matches[0];
            }, $value);
            $temp[$key] = $value;
        }
        $_ENV[$key] = $temp[$key];
        putenv(sprintf('%s=%s', $key, $temp[$key]));
    }
}

if (!function_exists('env')) {
    function env($name, $default = null)
    {
        return $_ENV[$name] ?? $default;
    }
}