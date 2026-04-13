<?php

namespace Covaleski\LaravelRoa;

/**
 * Get all classes declared within the supplied PHP code.
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
