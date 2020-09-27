<?php

declare(strict_types=1);

namespace MattAllan\LaravelCodeStyle\Dev;

use Brick\VarExporter\VarExporter;
use Illuminate\Support\Collection;

/**
 * A utility for converting StyleCI rules to PHPCS Rules.
 *
 * @internal
 */
class GenerateRules
{
    /**
     * @see https://docs.styleci.io/presets#laravel
     */
    const STYLECI_PRESET = [
        '@Laravel' => [
            // the @PSR2 preset isn't listed in the StyleCI preset but is used by Laravel.
            // @see https://laravel.com/docs/8.x/contributions#coding-style
            '@PSR2',
            'align_phpdoc',
            'alpha_ordered_imports',
            'array_indentation',
            'binary_operator_spaces',
            'blank_line_after_namespace',
            'blank_line_after_opening_tag',
            'blank_line_before_return',
            'cast_spaces',
            'class_definition',
            'compact_nullable_typehint',
            'concat_without_spaces',
            'declare_equal_normalize',
            // 'die_to_exit', todo: unreleased
            'elseif',
            'encoding',
            'full_opening_tag',
            'function_declaration',
            'function_typehint_space',
            'hash_to_slash_comment',
            'heredoc_to_nowdoc',
            'include',
            'indentation',
            'lowercase_cast',
            'lowercase_constants',
            'lowercase_keywords',
            'lowercase_static_reference',
            'magic_constant_casing',
            'magic_method_casing',
            'method_argument_space',
            'method_separation',
            'method_visibility_required',
            'native_function_casing',
            'native_function_type_declaration_casing',
            'no_alternative_syntax',
            'no_binary_string',
            'no_blank_lines_after_class_opening',
            'no_blank_lines_after_phpdoc',
            'no_blank_lines_after_throw',
            'no_blank_lines_between_imports',
            'no_blank_lines_between_traits',
            'no_closing_tag',
            'no_empty_phpdoc',
            'no_empty_statement',
            'no_extra_consecutive_blank_lines',
            'no_leading_import_slash',
            'no_leading_namespace_whitespace',
            'no_multiline_whitespace_around_double_arrow',
            'no_multiline_whitespace_before_semicolons',
            'no_short_bool_cast',
            'no_singleline_whitespace_before_semicolons',
            'no_spaces_after_function_name',
            'no_spaces_inside_offset',
            'no_spaces_inside_parenthesis',
            'no_trailing_comma_in_list_call',
            'no_trailing_comma_in_singleline_array',
            'no_trailing_whitespace',
            'no_trailing_whitespace_in_comment',
            'no_unneeded_control_parentheses',
            'no_unneeded_curly_braces',
            'no_unset_cast',
            'no_unused_imports',
            // 'no_unused_lambda_imports', todo: unreleased
            'no_useless_return',
            'no_whitespace_before_comma_in_array',
            'no_whitespace_in_blank_line',
            'normalize_index_brace',
            'not_operator_with_successor_space',
            'object_operator_without_whitespace',
            'phpdoc_indent',
            'phpdoc_inline_tag',
            'phpdoc_no_access',
            'phpdoc_no_package',
            'phpdoc_no_useless_inheritdoc',
            'phpdoc_return_self_reference',
            'phpdoc_scalar',
            'phpdoc_single_line_var_spacing',
            'phpdoc_summary',
            'phpdoc_trim',
            'phpdoc_type_to_var',
            'phpdoc_types',
            'phpdoc_var_without_name',
            'post_increment',
            'print_to_echo',
            'property_visibility_required',
            'psr12_braces',
            'return_type_declaration',
            'short_array_syntax',
            'short_list_syntax',
            'short_scalar_cast',
            'single_blank_line_at_eof',
            'single_blank_line_before_namespace',
            'single_class_element_per_statement',
            'single_import_per_statement',
            'single_line_after_imports',
            'single_quote',
            'space_after_semicolon',
            'standardize_not_equals',
            'switch_case_semicolon_to_colon',
            'switch_case_space',
            // 'switch_continue_to_break', todo: unreleased
            'ternary_operator_spaces',
            'trailing_comma_in_multiline_array',
            'trim_array_spaces',
            'unalign_equals',
            'unary_operator_spaces',
            'unix_line_endings',
            'whitespace_after_comma_in_array',
        ],
        '@Laravel:risky' => [
            'no_alias_functions',
            'no_unreachable_default_argument_value',
            'psr4',
            'self_accessor',
        ],
    ];

    /**
     * Maps a styleCI rule name to a single key value pair of PHPCS [rule => config].
     *
     * Multiple StyleCI rules may map to the same PHPCS rule. When that happens
     * the rules are merged recursively into a single definition. If a rule is
     * not defined in this map it default to [rule => true].
     */
    const STYLECI_TO_PHPCS_MAP = [
        'align_phpdoc' => [
            'align_multiline_comment' => [
                'comment_type' => 'phpdocs_like',
            ],
        ],
        'alpha_ordered_imports' => [
            'ordered_imports' => [
                'sort_algorithm' => 'alpha',
            ],
        ],
        'binary_operator_spaces' => [
            'binary_operator_spaces' => [
                'operators' => [
                    // equivalent to not having the align_double_arrow
                    // or unalign_double_arrow rule enabled
                    '=>' => null,
                ],
            ],
        ],
        'blank_line_before_return' => [
            'blank_line_before_statement' => [
                'statements' => [
                    'return',
                ],
            ],
        ],
        'concat_without_spaces' => [
            'concat_space' => [
                'spacing' => 'none',
            ],
        ],
        'die_to_exit' => [
            'no_alias_language_construct_call' => true,
        ],
        'hash_to_slash_comment' => [
            'single_line_comment_style' => [
                'comment_types' => ['hash'],
            ],
        ],
        'indentation' => [
            'indentation_type' => true,
        ],
        'method_separation' => [
            'class_attributes_separation' => [
                'elements' => [
                    'method',
                ],
            ],
        ],
        'method_visibility_required' => [
            'visibility_required' => [
                'elements' => ['method'],
            ],
        ],
        'no_blank_lines_after_throw' => [
            'no_extra_blank_lines' => [
                'tokens' => [
                    'throw',
                ],
            ],
        ],
        'no_blank_lines_between_imports' => [
            'no_extra_blank_lines' => [
                'tokens' => [
                    'use',
                ],
            ],
        ],
        'no_blank_lines_between_traits' => [
            'no_extra_blank_lines' => [
                'tokens' => [
                    'use_trait',
                ],
            ],
        ],
        'no_extra_consecutive_blank_lines' => [
            'no_extra_blank_lines' => [
                'tokens' => [
                    'extra',
                ],
            ],
        ],
        'no_multiline_whitespace_before_semicolons' => [
            'multiline_whitespace_before_semicolons' => true,
        ],
        'no_spaces_inside_offset' => [
            'no_spaces_around_offset' => [
                'positions' => [
                    'inside',
                ],
            ],

        ],
        'no_unused_lambda_imports' => [
            'lambda_not_used_import' => true,
        ],
        'phpdoc_type_to_var' => [
            'phpdoc_no_alias_tag' => [
                'type' => 'var',
            ],
        ],
        'post_increment' => [
            'increment_style' => [
                'style' => 'post',
            ],
        ],
        'print_to_echo' => [
            'no_mixed_echo_print' => [
                'use' => 'echo',
            ],
        ],
        'property_visibility_required' => [
            'visibility_required' => [
                'elements' => ['property'],
            ],
        ],
        // @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/4943
        'psr12_braces' => [
            // todo: add ['allow_single_line_anonymous_class_with_empty_body' => true] once released
            'braces' => true,
        ],
        'return_type_declaration' => [
            'return_type_declaration' => [
                'space_before' => 'none',
            ],
        ],
        'short_array_syntax' => [
            'array_syntax' => [
                'syntax' => 'short',
            ],
        ],
        'short_list_syntax' => [
            'list_syntax' => [
                'syntax' => 'short',
            ],
        ],
        'unalign_equals' => [
            'binary_operator_spaces' => [
                'operators' => [
                    '=' => 'single_space',
                ],
            ],
        ],
        'unix_line_endings' => [
            'line_ending' => true,
        ],
    ];

    public static function generate(): void
    {
        $code = file_get_contents(__DIR__.'/../Config.php');

        $rules = VarExporter::export(static::rules()->toArray());

        $rules = Collection::make(explode("\n", $rules))
            ->map(function (string $line, int $index) {
                return $index === 0 ? $line : "    $line";
            })->implode("\n");

        $replaced = preg_replace(
            '/(?<=const RULE_DEFINITIONS = )([^;]+)(?=;)/',
            $rules,
            $code
        );

        file_put_contents(__DIR__.'/../Config.php', $replaced);
    }

    private static function rules(): Collection
    {
        return Collection::make(static::STYLECI_PRESET)
            ->map(function (array $rules) {
                return Collection::make($rules)->reduce(function (Collection $carry, string $rule) {
                    return $carry->mergeRecursive(
                        static::STYLECI_TO_PHPCS_MAP[$rule] ?? [$rule => true]
                    );
                }, Collection::make());
            });
    }
}
