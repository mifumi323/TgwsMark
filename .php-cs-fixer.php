<?php

return (new \PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'phpdoc_summary' => false,
        'phpdoc_separation' => false,
        'yoda_style' => false,
        'increment_style' => [
            'style' => 'post',
        ],
        'nullable_type_declaration_for_default_null_value' => [
            'use_nullable_type_declaration' => true,
        ],
        'phpdoc_to_comment' => [
            'ignored_tags' => [
                'var',
            ],
        ],
    ])
;
