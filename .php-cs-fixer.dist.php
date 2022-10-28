<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PHP81Migration'           => true,
        '@PHP80Migration:risky'     => true,
        '@PHPUnit84Migration:risky' => true,
        '@PhpCsFixer'               => true,
        '@PhpCsFixer:risky'         => true,
        'binary_operator_spaces'    => [
            'default'   => 'align_single_space_minimal',
            'operators' => ['||' => null, '&&' => null, '|' => 'no_space'],
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'default',
                'exit',
                'goto',
                'include',
                'include_once',
                'phpdoc',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'yield',
                'yield_from',
            ],
        ],
        'class_attributes_separation' => [
            'elements' => [
                'case'         => 'none',
                'const'        => 'only_if_meta',
                'method'       => 'one',
                'property'     => 'only_if_meta',
                'trait_import' => 'none',
            ],
        ],
        'class_definition' => [
            'inline_constructor_arguments'        => false,
            'multi_line_extends_each_single_line' => true,
            'single_item_single_line'             => true,
            'space_before_parenthesis'            => true,
        ],
        'concat_space'                            => ['spacing' => 'one'],
        'control_structure_braces'                => true,
        'control_structure_continuation_position' => true,
        'curly_braces_position'                   => true,
        'date_time_create_from_format_call'       => true,
        'date_time_immutable'                     => true,
        'declare_parentheses'                     => true,
        'final_class'                             => true,
        'final_public_method_for_abstract_class'  => true,
        'general_phpdoc_annotation_remove'        => [
            'annotations' => ['author', 'category', 'filesource', 'source'],
        ],
        'global_namespace_import'                          => true,
        'mb_str_functions'                                 => true,
        'multiline_whitespace_before_semicolons'           => ['strategy' => 'no_multi_line'],
        'no_multiple_statements_per_line'                  => true,
        'no_unset_on_property'                             => false,
        'nullable_type_declaration_for_default_null_value' => true,
        'operator_linebreak'                               => ['only_booleans' => false],
        'ordered_class_elements'                           => [
            'order' => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'method_public_static',
                'method_protected_static',
                'method_private_static',
                'phpunit',
                'method_public',
                'method_protected',
                'method_private',
            ]
        ],
        'ordered_interfaces' => true,
        'ordered_imports'    => [
            'imports_order' => ['class', 'function', 'const'],
        ],
        'phpdoc_line_span'                       => ['const' => 'single', 'property' => 'single'],
        'phpdoc_tag_casing'                      => true,
        'phpdoc_to_param_type'                   => true,
        'phpdoc_to_property_type'                => true,
        'phpdoc_to_return_type'                  => true,
        'phpdoc_types_order'                     => ['null_adjustment' => 'always_last'],
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
        'php_unit_test_class_requires_covers'    => false,
        'psr_autoloading'                        => ['dir' => 'src'],
        'regular_callable_call'                  => true,
        'self_static_accessor'                   => true,
        'simplified_if_return'                   => true,
        'simplified_null_return'                 => true,
        'static_lambda'                          => true,
        'trailing_comma_in_multiline'            => [
            'elements' => ['arrays', 'arguments', 'parameters', 'match'],
        ],
        'yoda_style' => ['equal' => false, 'identical' => false],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
