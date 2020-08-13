<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$directory = new RecursiveDirectoryIterator($baseFolder = './src/Standards/BestIt/Sniffs');
$iterator = new RecursiveIteratorIterator($directory);
$regexIterator = new RegexIterator($iterator, '/^.+Sniff\.php$/i', RecursiveRegexIterator::GET_MATCH);

outputCodesTable(handleFiles($regexIterator, $baseFolder));

/**
 * Returns the constants of the class.
 *
 * @param string $fullQualifiedClassName
 *
 * @return array
 */
function getConstants(string $fullQualifiedClassName): array
{
    $reflection = new ReflectionClass($fullQualifiedClassName);
    $constants = $reflection->getConstants();

    return !$reflection->isAbstract() ? $constants : [];
}

/**
 * Returns the description for the code constant by parsing the doc block.
 *
 * @throws DomainException If there is no valid doc block.
 *
 * @param string $fullQualifiedClassName
 * @param string $constant
 *
 * @return string
 */
function getCodeDesc(string $fullQualifiedClassName, string $constant): string
{
    $constReflection = new ReflectionClassConstant($fullQualifiedClassName, $constant);

    // $re = ;
    if (!$docComment = $constReflection->getDocComment()) {
        throw new DomainException(
            sprintf('There should be a doc block for %s:%s', $fullQualifiedClassName, $constant),
        );
    }

    if (!preg_match('~/\*\*\s(.*)\s~m', $docComment, $matches)) {
        throw new DomainException(
            sprintf(
                'There should be a doc block with summary for %s:%s',
                $fullQualifiedClassName,
                $constant,
            ),
        );
    }

    return trim($matches[1], '* ');
}

/**
 * Iterates thru the base folder and parses the sniff files for the documentation.
 *
 * @param Iterator $regexIterator
 * @param string $baseFolder
 *
 * @return array
 */
function handleFiles(Iterator $regexIterator, string $baseFolder): array
{
    $codes = [];

    foreach ($regexIterator as $file) {
        [$file] = $file;

        $simpleClassName = str_replace([$baseFolder . DIRECTORY_SEPARATOR, '.php', 'Sniff'], '', $file);
        $fullQualifiedClassName = 'BestIt\\Sniffs\\' . str_replace('/', '\\', $simpleClassName) . 'Sniff';

        $hasSuppresses = (bool) preg_match_all(
            '/->isSniffSuppressed\((?P<code>\s*.*\s*)\)/mU',
            file_get_contents($file),
            $suppresses,
        );

        try {
            $constants = getConstants($fullQualifiedClassName);

            foreach ($constants as $constant => $constantValue) {
                if (substr($constant, 0, 5) === 'CODE_') {
                    $sniffDesc = getCodeDesc($fullQualifiedClassName, $constant);

                    $sniffRule = sprintf(
                        'BestIt.%s.%s',
                        str_replace(DIRECTORY_SEPARATOR, '.', $simpleClassName),
                        $constantValue,
                    );

                    $codes[$sniffRule] = [$sniffDesc, $hasSuppresses];

                    if ($hasSuppresses) {
                        if (!array_filter($suppresses['code'])) {
                            $codes[$sniffRule][1] = 'yes by class';
                        } else {
                            $codeHasMatchingSuppress = false;

                            foreach ($suppresses['code'] as $foundSuppress) {
                                $foundSuppressValue = str_replace(['self::', 'static::'], '', $foundSuppress);


                                if (
                                    $codeHasMatchingSuppress = in_array(
                                        $foundSuppressValue,
                                        [$constant, $constantValue],
                                        true,
                                    )
                                ) {
                                    $codes[$sniffRule][1] = 'yes';

                                    break;
                                }
                            }

                            if (!$codeHasMatchingSuppress) {
                                $codes[$sniffRule][1] = false;
                            }
                        }
                    }
                }
            }
        } catch (ReflectionException $e) {
            echo $e;
        }
    }
    return $codes;
}

/**
 * Saves the table in the table.md and outputs it.
 *
 * @param array $codes
 *
 * @return void
 */
function outputCodesTable(array $codes): void
{
    ksort($codes);

    file_put_contents(
        $tmpFile = __DIR__ . DIRECTORY_SEPARATOR . 'table.md',
        <<<EOD
| Sniff | Description | suppressable |
| ----- | ----------- | ------------ |
EOD,
    );

    foreach ($codes as $code => $codeRule) {
        [$description, $hasSuppresses] = $codeRule;

        file_put_contents(
            $tmpFile,
            sprintf(
                "\n| %s | %s | %s |",
                $code,
                $description,
                $hasSuppresses ?: 'no',
            ),
            FILE_APPEND,
        );
    }

    echo file_get_contents($tmpFile);
}
