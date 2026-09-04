<?php
/**
 * python-bin.php
 *
 * Resolves the Python interpreter used by the meter "Test connection" endpoints
 * (config/tcp.php, config/rs485.php) when they shell_exec() a connector/*.py script.
 *
 * Reads PYTHON_BIN from .env (loaded via config.php). Falls back to "py" — the
 * Windows launcher, which lives in C:\Windows and is therefore on the system PATH
 * that Apache inherits, unlike a bare "python" from a per-user install.
 *
 * Usage:
 *   require_once __DIR__ . '/python-bin.php';
 *   $cmd = sprintf('%s %s ... 2>&1', ems_python_bin(), escapeshellarg($script), ...);
 */

require_once __DIR__ . '/config.php';   // loads .env into $_ENV

if (!function_exists('ems_python_bin')) {
    function ems_python_bin(): string
    {
        $bin = trim($_ENV['PYTHON_BIN'] ?? '');
        if ($bin === '') {
            $bin = 'py';
        }
        // Quote a full path that contains spaces (e.g. "C:\Program Files\Python\python.exe")
        if (strpbrk($bin, " \t") !== false && $bin[0] !== '"') {
            $bin = '"' . $bin . '"';
        }
        return $bin;
    }
}
