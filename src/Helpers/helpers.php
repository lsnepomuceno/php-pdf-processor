<?php

if (!defined("_DEBUG_LEVEL")) {
    define('_DEBUG_LEVEL', 3);
}

if (!defined('STDERR')) {
    define('STDERR', fopen('php://stderr', 'wb'));
}

function var_dump_to_string($var): bool|string
{
    ob_start();
    var_dump($var);
    return ob_get_clean();
}

function debug_var(...$vars)
{
    // If the debug level is less than 3, suppress debug messages
    if (_DEBUG_LEVEL < 3) return;

    $result = [];
    foreach ($vars as $var) {
        $result[] = var_dump_to_string($var);
    }
    return implode("\n", $result);
}

function p_debug_var(...$vars): void
{
    // If the debug level is less than 3, suppress debug messages
    if (_DEBUG_LEVEL < 3) return;

    foreach ($vars as $var) {
        $e = var_dump_to_string($var);
        p_stderr($e, "Debug");
    }
}

function varval($e)
{
    $retval = $e;
    if (is_array($e)) {
        $a = [];
        foreach ($e as $k => $v) {
            $v   = varval($v);
            $a[] = "$k => $v";
        }
        $retval = "[ " . implode(", ", $a) . " ]";
    }
    return $retval;
}

function p_stderr(&$e, $tag = "Error", $level = 1): void
{
    $dinfo = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    $dinfo = $dinfo[$level];
    $e     = sprintf("$tag info at %s:%d: %s", $dinfo['file'], $dinfo['line'], varval($e));
    fwrite(STDERR, "$e\n");
}

function p_debug($e, $retval = false)
{
    // If the debug level is less than 3, suppress debug messages
    if (_DEBUG_LEVEL >= 3) {
        p_stderr($e, "Debug");
    }
    return $retval;
}

function p_warning($e, $retval = false)
{
    // If the debug level is less than 2, suppress warning messages
    if (_DEBUG_LEVEL >= 2) {
        p_stderr($e, "Warning");
    }
    return $retval;
}

function p_error($e, $retval = false)
{
    // If the debug level is less than 1, suppress error messages
    if (_DEBUG_LEVEL >= 1) {
        p_stderr($e, "Error");
    }
    return $retval;
}

/**
 * @throws Exception
 */
function get_random_string($length = 8, $extended = false, $hard = false): string
{
    $token        = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    if ($extended === true) {
        $codeAlphabet .= "!\"#$%&'()*+,-./:;<=>?@[\\]_{}";
    }
    if ($hard === true) {
        $codeAlphabet .= "^`|~";
    }
    $max = strlen($codeAlphabet);
    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }
    return $token;
}

function get_memory_limit(): float|int
{
    $memory_limit = ini_get('memory_limit');
    if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches) === 1) {
        $memory_limit = match ($matches[2]) {
            'K', 'G', 'M' => $memory_limit * 1024,
            default       => 0,
        };
    } else {
        $memory_limit = 0;
    }
    return $memory_limit;
}

function show_bytes($str, $columns = null): string
{
    $result = "";
    if ($columns === null)
        $columns = strlen($str);
    $c = $columns;
    for ($i = 0; $i < strlen($str); $i++) {
        $result .= sprintf("%02x ", ord($str[$i]));
        $c--;
        if ($c === 0) {
            $c      = $columns;
            $result .= "\n";
        }

    }
    return $result;
}

function timestamp_to_pdfdatestring($date = null): string
{
    if ($date === null)
        $date = new DateTime();

    $timestamp = $date->getTimestamp();
    return 'D:' . get_pdf_formatted_date($timestamp);
}

function get_pdf_formatted_date($time): string
{
    return substr_replace(date('YmdHisO', intval($time)), '\'', (0 - 2), 0) . '\'';
}
