<?php

namespace PhpCsFixer;

$finder = Finder::create()
    ->name('*.php')
    ->in(__DIR__.DIRECTORY_SEPARATOR.'src')
    ->in(__DIR__.DIRECTORY_SEPARATOR.'test')
;

return Config::create()
    ->setUsingCache(false)
    ->setRules([
        '@PSR2' => true,                                    // Use PSR-2 formatting by default.
        // 'not_operator_with_successor_space' => true,        // Logical NOT operators (!) should have one trailing whitespace.
        'trailing_comma_in_multiline_array' => true,        // PHP multi-line arrays should have a trailing comma.
        'ordered_imports' => true,                          // Ordering use statements (alphabetically)
        // 'ordered_class_elements' => true,                   // Order class elements
        'blank_line_before_return' => true,                 // An empty line feed should precede a return statement
        'array_syntax' => ['syntax' => 'short'],            // PHP arrays should use the PHP 5.4 short-syntax.
        'short_scalar_cast' => true,                        // Cast "(boolean)" and "(integer)" should be written as "(bool)" and "(int)". "(double)" and "(real)" as "(float)".
        'single_blank_line_before_namespace' => true,       // An empty line feed should precede the namespace.
        'blank_line_after_opening_tag' => true,             // An empty line feed should follow a PHP open tag.
        'no_unused_imports' => true,                        // Unused use statements must be removed.
        'trim_array_spaces' => true,                        // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
        'no_trailing_comma_in_singleline_array' => true,    // PHP single-line arrays should not have a trailing comma.
        'phpdoc_order' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag' =>  true,
        'no_extra_blank_lines' => ['extra'],
        'ternary_operator_spaces' => true,                  // Standardize spaces around ternary operator.
    ])
    ->setFinder($finder)
    ;
