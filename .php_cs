<?php

use Symfony\CS\Config\Config;
use Symfony\CS\FixerInterface;
use Symfony\CS\Finder\DefaultFinder;

$fixers = [
    'align_double_arrow',
    'array_element_no_space_before_comma',
    'blankline_after_open_tag',
    'braces',
    'class_definition',
    'combine_consecutive_unsets',
    'concat_without_spaces',
    'declare_equal_normalize',
    'double_arrow_multiline_whitespaces',
    'duplicate_semicolon',
    'elseif',
    'encoding',
    'eof_ending',
    'extra_empty_lines',
    'function_call_space',
    'function_declaration',
    'function_typehint_space',
    'hash_to_slash_comment',
    'heredoc_to_nowdoc',
    'include',
    'indentation',
    'join_function',
    'line_after_namespace',
    'linefeed',
    'list_commas',
    'logical_not_operators_with_successor_space',
    'lowercase_constants',
    'lowercase_keywords',
    'method_argument_default_value',
    'method_argument_space',
    'multiline_array_trailing_comma',
    'multiline_spaces_before_semicolon',
    'multiple_use',
    'namespace_no_leading_whitespace',
    'native_function_casing',
    'native_function_casing',
    'new_with_braces',
    'newline_after_open_tag',
    'no_blank_lines_after_class_opening',
    'no_empty_comment',
    'no_empty_lines_after_phpdocs',
    'no_empty_phpdoc',
    'no_empty_statement',
    'no_trailing_whitespace_in_comment',
    'no_useless_else',
    'no_useless_return',
    'object_operator',
    'operators_spaces',
    'ordered_use',
    'parenthesis',
    'php4_constructor',
    'php_closing_tag',
    'phpdoc_indent',
    'phpdoc_inline_tag',
    'phpdoc_no_access',
    'phpdoc_no_empty_return',
    'phpdoc_no_package',
    'phpdoc_order',
    'phpdoc_params',
    'phpdoc_scalar',
    'phpdoc_separation',
    'phpdoc_short_description',
    'phpdoc_to_comment',
    'phpdoc_trim',
    'phpdoc_type_to_var',
    'phpdoc_types',
    'phpdoc_var_without_name',
    'print_to_echo',
    'psr0',
    'remove_leading_slash_use',
    'remove_lines_between_uses',
    'return',
    'self_accessor',
    'short_array_syntax',
    'short_bool_cast',
    'short_echo_tag',
    'short_scalar_cast',
    'short_tag',
    'single_array_no_trailing_comma',
    'single_blank_line_before_namespace',
    'single_line_after_imports',
    'single_quote',
    'spaces_before_semicolon',
    'spaces_cast',
    'standardize_not_equal',
    'switch_case_space',
    'ternary_spaces',
    'trailing_spaces',
    'trim_array_spaces',
    'unalign_equals',
    'unary_operators_spaces',
    'unneeded_control_parentheses',
    'unused_use',
    'visibility',
    'whitespacy_lines'
];

return Config::create()
    ->finder(DefaultFinder::create()->in(__DIR__))
    ->fixers($fixers)
    ->level(FixerInterface::NONE_LEVEL)
    ->setUsingCache(true);
