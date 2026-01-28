<?php

namespace App\Core;

use DateTimeImmutable;
use RuntimeException;

class Logger
{
    public function __construct(private string $filePath)
    {
        $directory = \dirname($this->filePath);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException('Не удалось создать директорию для логов: ' . $directory);
        }
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        $timestamp = (new DateTimeImmutable())->format(DateTimeImmutable::ATOM);
        $entry = sprintf('[%s] %s: %s', $timestamp, $level, $message);

        if ($context !== []) {
            $entry .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $entry .= PHP_EOL;

        file_put_contents($this->filePath, $entry, FILE_APPEND | LOCK_EX);
    }
}
