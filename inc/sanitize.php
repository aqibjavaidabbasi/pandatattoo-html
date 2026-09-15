<?php
/**
 * The WordPress sanitiser subset the ported code relies on.
 * Behaviour matches WP closely enough that field values reaching Contentful are identical.
 */
declare(strict_types=1);

/** sanitize_text_field(): strip tags, drop control chars and line breaks, collapse whitespace. */
function wp_sanitize_text_field(?string $str): string {
    $s = strip_tags((string) $str);
    $s = preg_replace('/[\r\n\t ]+/', ' ', $s) ?? '';
    $s = preg_replace('/[\x00-\x1F\x7F]/u', '', $s) ?? '';
    return trim($s);
}

/** sanitize_key(): lowercase; keep a-z, 0-9, underscore and dash only. */
function wp_sanitize_key(?string $key): string {
    return preg_replace('/[^a-z0-9_\-]/', '', strtolower((string) $key)) ?? '';
}

/** sanitize_file_name(): strip path separators and characters WP considers unsafe. */
function wp_sanitize_file_name(?string $name): string {
    $n = str_replace(['?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"', '&',
                      '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}', '%', '+',
                      chr(0)], '', (string) $name);
    $n = preg_replace('/[\s-]+/', '-', $n) ?? '';
    return trim($n, '.-_');
}
