<?php

use App\Constants\DiskNames;
use Illuminate\Support\Facades\Log;

if (!function_exists('getConstantNameByValue')) {
    /**
     * Generate public URL for Supabase Storage file
     *
     * @param string $value
     * @param string $class
     * @return string
     */

    function getConstantNameByValue(string $value, string $class): ?string
    {
        $reflection = new \ReflectionClass($class);
        $constants = $reflection->getConstants();

        foreach ($constants as $name => $val) {
            if ($val === $value) {
                return $name;
            }
        }
        return null; // Value not found
    }
}

if (!function_exists(('fireAndForgetRequest'))) {
    function fireAndForgetRequest(string $url, array $options = [])
    {
        try {
            $parsedUrl = parse_url($url);
            $host = $parsedUrl['host'];
            $port = $parsedUrl['port'] ?? ($parsedUrl['scheme'] === 'https' ? 443 : 80);
            $path = $parsedUrl['path'] ?? '/';
            $query = $parsedUrl['query'] ?? '';

            if ($query) {
                $path .= '?' . $query;
            }

            $fp = fsockopen(
                ($parsedUrl['scheme'] === 'https' ? 'ssl://' : '') . $host,
                $port,
                $errno,
                $errstr,
                1 // Short timeout for connection only
            );

            if (!$fp) {
                throw new Exception("Socket error: $errstr ($errno)");
            }

            $out = "POST $path HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";

            foreach ($options['headers'] as $name => $value) {
                $out .= "$name: $value\r\n";
            }

            $body = $options['body'] ?? '';
            $out .= "Content-Length: " . strlen($body) . "\r\n";
            $out .= "Connection: close\r\n\r\n";
            $out .= $body;

            fwrite($fp, $out);
            usleep(10000); // 10ms - ensure data starts sending
            fclose($fp); // Close immediately without waiting for response

        } catch (Exception $e) {
            Log::error('Fire-and-forget failed: ' . $e->getMessage());
        }
    }
}
