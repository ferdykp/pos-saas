<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class PrivateMysqlClient
{
    /** Credentials never enter shell arguments, logs or process output. */
    public function restore(array $config, string $database, string $path): void
    {
        if (! preg_match('/^growpos_verify_[a-f0-9]{24}$/', $database)) {
            throw new \InvalidArgumentException('Restore target must be an isolated verification database.');
        }
        $directory = storage_path('app/private');
        if (! is_dir($directory)) {
            mkdir($directory, 0700, true);
        }
        $credentials = tempnam($directory, '.mysql-');
        chmod($credentials, 0600);
        $escape = fn ($value) => '"'.str_replace(['\\', '"', "\n", "\r"], ['\\\\', '\\"', '\\n', '\\r'], (string) $value).'"';
        $options = ['user' => $config['username'], 'password' => $config['password'], 'host' => $config['host'], 'port' => $config['port']];
        if (! empty($config['unix_socket'])) {
            $options['socket'] = $config['unix_socket'];
        }
        $content = "[client]\n";
        foreach ($options as $key => $value) {
            $content .= $key.'='.$escape($value)."\n";
        }
        file_put_contents($credentials, $content);
        $input = fopen($path, 'rb');
        try {
            $process = new Process(['mysql', '--defaults-extra-file='.$credentials, '--binary-mode', $database]);
            $process->setInput($input)->setTimeout(600)->run();
            if (! $process->isSuccessful()) {
                throw new \RuntimeException('Isolated restore failed; inspect database compatibility and privileges.');
            }
        } finally {
            fclose($input);
            unlink($credentials);
        }
    }
}
