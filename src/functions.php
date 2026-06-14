<?php

namespace Covaleski\LaravelRoa;

/**
 * Get all classes declared within the supplied PHP code.
 *
 * @see https://stackoverflow.com/a/27440555 Modified from this answer.
 * @param string $code PHP code.
 * @return array<int, class-string>
 */
function file_get_classes(string $code): array
{
    $tokens = token_get_all($code);
    $namespace = '';
    $classes = [];
    for ($i = 0; isset($tokens[$i]); $i++) {
        if (!is_array($tokens[$i])) {
            continue;
        }
        if (
            T_NAMESPACE === $tokens[$i][0]
            && T_WHITESPACE === $tokens[$i + 1][0]
            && in_array($tokens[$i + 2][0], [T_STRING, T_NAME_QUALIFIED], true)
        ) {
            $namespace = $tokens[$i + 2][1] . '\\';
            $i += 2;
        }
        if (
            T_CLASS === $tokens[$i][0]
            && T_WHITESPACE === $tokens[$i + 1][0]
            && T_STRING === $tokens[$i + 2][0]
        ) {
            $classes[] = $namespace . $tokens[$i + 2][1];
            $i += 2;
        }
    }
    return $classes;
}

/**
 * Get a human-readable representation for the specified file size.
 *
 * @see https://phpshare.org/ Modified from the Format Size Units snippet.
 * @param int $size File size in bytes.
 * @return string Human-readable representation in B/KB/MB/GB.
 */
function format_size_units(int $size): string
{
    if ($size >= 1073741824) {
        return number_format($size / 1073741824, 2) . ' GB';
    } elseif ($size >= 1048576) {
        return number_format($size / 1048576, 2) . ' MB';
    } elseif ($size >= 1024) {
        return number_format($size / 1024, 2) . ' KB';
    } else {
        return $size . ' bytes';
    }
}
