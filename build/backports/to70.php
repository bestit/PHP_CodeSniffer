<?php

declare(strict_types=1);

$folders = [
    realpath(__DIR__ . '/../../src'),
    realpath(__DIR__ . '/../../tests'),
];

foreach ($folders as $baseFolder) {
    $directory = new RecursiveDirectoryIterator($baseFolder);
    $iterator = new RecursiveIteratorIterator($directory);
    $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

    foreach ($regexIterator as $file) {
        $file = current($file);


        $replaces = [
            'private const ' => 'const ',
            'protected const ' => 'const ',
            'public const ' => 'const ',
            ': ?array' => '',
            ': ?int' => '',
            ': ?string' => '',
            ': void;' => ';',
            ': void' => '',
            '?int ' => '',
            '?string ' => ''
        ];

        file_put_contents(
            $file,
            str_replace(
                array_keys($replaces),
                array_values($replaces),
                file_get_contents($file)
            )
        );
    }
}
