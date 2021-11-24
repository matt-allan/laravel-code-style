<?php

declare(strict_types=1);

namespace MattAllan\LaravelCodeStyle\Dev;

use Brick\VarExporter\VarExporter;
use Illuminate\Support\Collection;
use StyleCI\SDK\Client as StyleCIClient;

/**
 * A utility for converting StyleCI rules to PHPCS Rules.
 *
 * @internal
 */
class GenerateRules
{
    /**
     * The rules used by StyleCI that are not available in a stable
     * released version of PHP-CS-Fixer.
     */
    const UNRELEASED_RULES = [
        'phpdoc_singular_inheritdoc',
        // See https://github.com/FriendsOfPHP/PHP-CS-Fixer/commit/89106bccfb33fdac02397f231966f37d14bf4e07
        'integer_literal_case',

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
        'class_definition' => [
            // TODO: re-enable once it doesn't break anonymous classes
            'class_definition' => false,
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
        'laravel_braces' => [
            // TODO: enable once braces fixers are split
            // See https://github.com/matt-allan/laravel-code-style/issues/47
            'braces' => false,
            // 'braces' => [
            //     'allow_single_line_anonymous_class_with_empty_body' => true,
            // ],
        ],
        'laravel_phpdoc_alignment' => [
            // TODO: use 2 spaces after w/ vertical alignment
            // Need to write a new fixer?
            // See https://docs.styleci.io/fixers#laravel_phpdoc_alignment
            // 'phpdoc_align' => false,
        ],
        'laravel_phpdoc_order' => [
            // TODO: enable once other phpdoc rules work
            // @param, then @return, then @throws
            // See https://docs.styleci.io/fixers#laravel_phpdoc_order
            // 'phpdoc_order' => true,
        ],
        'laravel_phpdoc_separation' => [
            // TODO: separate but group @param and @return
            // Need to fork https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/07430f5aca4874476bf175317cfb6284cd80421a/src/DocBlock/TagComparator.php
            // See https://docs.styleci.io/fixers#laravel_phpdoc_separation
            // 'phpdoc_separation' => false,
        ],
        'lowercase_constants' => [
            'constant_case' => [
                'case' => 'lower',
            ],
        ],
        'method_argument_space' => [
            'method_argument_space' => [
                'on_multiline' => 'ignore',
            ],
        ],
        'method_separation' => [
            'class_attributes_separation' => [
                'elements' => [
                    'method' => 'one',
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
                'replacements' => [
                    'type' => 'var',
                ],
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
        'psr4' => [
            'psr_autoloading' => true,
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
        'trailing_comma_in_multiline_array' => [
            'trailing_comma_in_multiline' => [
                'elements' => ['arrays'],
            ],
        ],
        'unalign_equals' => [
            'binary_operator_spaces' => [
                'operators' => [
                    '=' => 'single_space',
                ],
            ],
        ],
        'union_type_without_spaces' => [
            'types_spaces' => [
                'space' => 'none',
            ],
        ],
        'unix_line_endings' => [
            'line_ending' => true,
        ],
    ];

    /**
     * @var \StyleCI\SDK\Client|null
     */
    private $styleCIClient;

    public function __construct(?StyleCIClient $styleCIClient = null)
    {
        $this->styleCIClient = $styleCIClient ?? new StyleCIClient();
        $this->registerMacros();
    }

    public function __invoke(): void
    {
        $path = __DIR__.'/../Config.php';

        $replaced = preg_replace(
            '/(?<=const RULE_DEFINITIONS = )([^;]+)(?=;)/',
            static::exportRules(),
            file_get_contents($path)
        );

        file_put_contents($path, $replaced);
    }

    private function exportRules(): string
    {
        $rules = VarExporter::export(static::rules()->toArray());

        return static::indent($rules);
    }

    private function indent(string $rules): string
    {
        return Collection::make(explode("\n", $rules))
            ->map(function (string $line, int $index) {
                return $index === 0 ? $line : "    $line";
            })->implode("\n");
    }

    private function rules(): Collection
    {
        return collect($this->styleCIClient->presets())
            ->realize()
            ->firstWhere('name', 'laravel')
            ->get('fixers')
            ->reject(function (string $rule) {
                return in_array($rule, self::UNRELEASED_RULES);
            })
            ->pipe(function (Collection $rules) {
                $fixers = collect($this->styleCIClient->fixers())->realize();

                [$risky, $notRisky] = $rules->partition(function ($rule) use ($fixers) {
                    return $fixers->firstWhere('name', $rule)->get('risky');
                });

                return collect([
                    '@Laravel' => $notRisky,
                    '@Laravel:risky' => $risky,
                ]);
            })
            ->tap(function (Collection $rules) {
                // the @PSR2 preset isn't listed in the StyleCI preset but is used by Laravel.
                // @see https://laravel.com/docs/8.x/contributions#coding-style
                // @todo: see if this is redundant
                $rules->get('@Laravel')->prepend('@PSR2');
            })
            ->map(function (Collection $rules) {
                return $rules->reduce(function (Collection $carry, string $rule) {
                    return $carry->mergeRecursive(
                        static::STYLECI_TO_PHPCS_MAP[$rule] ?? [$rule => true]
                    );
                }, collect());
            });
    }

    private function registerMacros()
    {
        if (! Collection::hasMacro('realize')) {
            // "Realize" the collection by recursively converting all
            // nested arrays to collections.
            Collection::macro('realize', function () {
                return $this->map(function ($value) {
                    return is_array($value) ? (new static($value))->realize() : $value;
                });
            });
        }
    }
}
