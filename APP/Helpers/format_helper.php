<?php declare(strict_types=1);

/**
 * Send headers to prevent caching.
 */
function header_nocache(): void
{
    header('Expires: Mon, 18 Jul 1988 01:08:08 GMT');              // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // Always modified
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0'); // HTTP/1.1
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');                                    // HTTP/1.0
}


/**
 * Current datetime (Y-m-d H:i:s).
 */
function now(): string
{
    return (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
}

/**
 * Current time (h:i A).
 */
function current_time(): string
{
    return (new DateTimeImmutable('now'))->format('h:i A');
}

/**
 * Current date (Y-m-d).
 */
function today(): string
{
    return (new DateTimeImmutable('today'))->format('Y-m-d');
}

/**
 * Sanitize input string.
 */
function validate_data(string $value): string
{
    return htmlspecialchars(strip_tags(stripslashes(trim($value))), ENT_QUOTES, 'UTF-8');
}


/**
 * Internal helper for safe number formatting.
 */
function format_number(
    float|int|string $n,
    int $decimals = 2,
    string $decimalSeparator = '.',
    string $thousandsSeparator = ','
): string {
    $number = is_numeric($n) ? (float)$n : 0.0;
    return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
}

/**
 * Format a number.
 */
function fnumber(
    float|int|string $n,
    int $decimals = 2,
    string $decimalSeparator = '.',
    string $thousandsSeparator = ','
): string {
    return format_number($n, $decimals, $decimalSeparator, $thousandsSeparator);
}

/**
 * Format as currency.
 */
function fcurrency(
    float|int|string $n,
    string $symbol = 'Rs.',
    int $decimals = 2,
    string $decimalSeparator = '.',
    string $thousandsSeparator = ','
): string {
    return $symbol . ' ' . format_number($n, $decimals, $decimalSeparator, $thousandsSeparator);
}

/**
 * Format as percentage.
 */
function fpercent(
    float|int|string $n,
    int $decimals = 2,
    string $decimalSeparator = '.',
    string $thousandsSeparator = ','
): string {
    return format_number($n, $decimals, $decimalSeparator, $thousandsSeparator) . '%';
}


