# best it PHP_CodeSniffer

[![Build Status](https://travis-ci.org/bestit/PHP_CodeSniffer.svg?branch=master)](https://travis-ci.org/bestit/php_codesniffer) [![Build Status](https://scrutinizer-ci.com/g/bestit/PHP_CodeSniffer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bestit/PHP_CodeSniffer/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bestit/PHP_CodeSniffer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bestit/PHP_CodeSniffer/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/bestit/PHP_CodeSniffer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bestit/PHP_CodeSniffer/?branch=master)

This package contains a default rule set and custom rules which are used in all best it projects.

## Installation

Our PHP_CodeSniffer package can be installed with composer with the following command:
```bash
composer require bestit/php_codesniffer --dev
```

## Usage

Create a PHP_CodeSniffer configuration (phpcs.xml.dist / phpcs.xml) like this:
```xml
<?xml version="1.0"?>
<ruleset name="PROJECT-X">
    <description>The coding standard for project x.</description>

    <!-- Path to best it ruleset. -->
    <rule ref="./vendor/bestit/php_codesniffer/src/Standards/BestIt/ruleset.xml" />

    <!-- Path to directory which are checked. -->
    <file>src/</file>
    <file>tests/</file>
</ruleset>
```

Execute the PHP_CodeSniffer (path can vary on your composer configuration):

```bash
./vendor/bin/phpcs
```
## Used sniffs

The base for the BestIt Standard is [PSR-12](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md).

| Sniff | Standard | Description |
| ----- | -------- | ----------- |
| BestIt.Commenting.ClassDoc.TagFormatContentInvalid | BestIt | Authors MUST commit to their classes and add an [author phpDoc-Tag](http://docs.phpdoc.org/references/phpdoc/tags/author.html). |
| Generic.PHP.DisallowShortOpenTag.EchoFound | PSR-1 | PHP code MUST use the long <?php ?> tags or the short-echo <?= ?> tags; it MUST NOT use the other tag variations. |
| Generic.Files.ByteOrderMark | PSR-1 | PHP code MUST use only UTF-8 without BOM. |
| SlevomatCodingStandard.Classes.ClassConstantVisibility.MissingConstantVisibility | BestIt | Constants MUST be marked with a visibility. |
| Squiz.Classes.ValidClassName | PSR-1 | Class names MUST be declared in StudlyCaps. |
| Generic.NamingConventions.UpperCaseConstantName | PSR-1 | Class constants MUST be declared in all upper case with underscore separators. |
| Generic.Files.LineEndings | PSR-2 | All PHP files MUST use the Unix LF (linefeed) line ending. |
| Squiz.WhiteSpace.SuperfluousWhitespace" | PSR-2 | There MUST NOT be trailing whitespace at the end of non-blank lines. |
| Squiz.WhiteSpace.SuperfluousWhitespace.StartFile | PSR-2 | There MUST NOT be trailing whitespace at the end of non-blank lines. |
| Squiz.WhiteSpace.SuperfluousWhitespace.EndFile | PSR-2 | There MUST NOT be trailing whitespace at the end of non-blank lines. |
| Squiz.WhiteSpace.SuperfluousWhitespace.EmptyLines | PSR-2 | There MUST NOT be trailing whitespace at the end of non-blank lines. |
| Generic.Formatting.DisallowMultipleStatements | PSR-2 | There MUST NOT be more than one statement per line. |
| Generic.WhiteSpace.ScopeIndent | PSR-2 | Code MUST use an indent of 4 spaces. |
| Generic.WhiteSpace.DisallowTabIndent | PSR-2 | Code MUST NOT use tabs for indenting. |
| Generic.PHP.LowerCaseKeyword | PSR-2 | The PHP constants true, false, and null MUST be in lower case. |
| BestIt.Functions.FluentSetter | BestIt | Every setter function MUST return $this if nothing else is returned.  |
| BestIt.Formatting.SpaceAfterDeclare | BestIt | There MUST be one empty line after declare-statement. |
| BestIt.TypeHints.DeclareStrictTypes | BestIt | Every file MUST have "declare(strict_types=1);" two line breaks after the opening tag. There MUST be no spaces aroung the equal-sign. |
| BestIt.TypeHints.TypeHintDeclaration | BestIt | Every function or method MUST have a type hint if the return annotation is valid. |
| Squiz.Strings.DoubleQuoteUsage | Squiz | Every String MUST be wrapped with single quotes. |
| Generic.Formatting.SpaceAfterCast | BestIt | There MUST be a space after cast. |
| Generic.Arrays.DisallowLongArraySyntax | BestIt | Every array syntax MUST be in short array syntax. |
| BestIt.Formatting.OpenTagSniff | BestIt | After the open tag there MUST be an empty line. |
| BestIt.Commenting.AbstractDocSniff.NoImmediateDocFound¹| BestIt | There MUST be a doc block before the listened token |
| BestIt.Commenting.AbstractDocSniff.NoSummary¹| BestIt | There MUST be a summary |
| BestIt.Commenting.AbstractDocSniff.SummaryNotFirst¹| BestIt | The summary MUST be the first statement in a doc block |
| BestIt.Commenting.AbstractDocSniff.SummaryTooLong¹| BestIt | The summary length MUST be maximum 120 characters |
| BestIt.Commenting.AbstractDocSniff.CommentNotMultiLine¹| BestIt | Every doc block must be multi line |
| BestIt.Commenting.AbstractDocSniff.NoLineAfterSummary¹| BestIt | There MUST be an empty line after the summary |
| BestIt.Commenting.AbstractDocSniff.LineAfterSummaryNotEmpty¹| BestIt | The line after the summary MUST be empty |
| BestIt.Commenting.AbstractDocSniff.DescriptionNotFound¹| BestIt | There MUST be doc block long description |
| BestIt.Commenting.AbstractDocSniff.NoLineAfterDescription¹| BestIt | There MUST be an empty line after the long description |
| BestIt.Commenting.AbstractDocSniff.MuchLinesAfterDescription¹| BestIt | There MUST be an empty line after the long description  |
| BestIt.Commenting.AbstractDocSniff.DescriptionTooLong¹| BestIt | Every line of the long description MUST be not longer than 120 characters |
| BestIt.Commenting.AbstractDocSniff.TagNotAllowed¹| BestIt | The given tag MUST NOT be used |
| BestIt.Commenting.AbstractDocSniff.TagOccurrenceMin¹| BestIt | The given tag MUST occur min x times |
| BestIt.Commenting.AbstractDocSniff.TagOccurrenceMax¹| BestIt | The given tag MUST occur max x times |
| BestIt.Commenting.AbstractDocSniff.TagWrongPosition¹| BestIt | The given tag MUST be at the correct position |
| BestIt.Commenting.AbstractDocSniff.SummaryUcFirst¹| BestIt | The summary first letter MUST be a capital letter |
| BestIt.Commenting.AbstractDocSniff.DescriptionUcFirst¹| BestIt | The long description first letter MUST be a capital letter |
| BestIt.Commenting.AbstractDocSniff.NoLineAfterTag¹| BestIt | There MUST be an empty line after the given tag |
| BestIt.Commenting.AbstractDocSniff.MuchLinesAfterTag¹| BestIt | There MUST be a single empty line after the given tag |
| BestIt.Commenting.AbstractDocSniff.TagFormatContentInvalid¹| BestIt | The tag content MUST match the given pattern |

¹ AbstractDocSniff means ClassDocSniff, MethodDocSniff, ConstantDocSniff and PropertyDocSniff  

## Testing

### Requirements

**To be able to test our written sniffs ensure that composer is installed with the option `--prefer-source`.
This is needed because we use the TestCase of the SlevomatCodingStandard.**

### Error code as public constant   

In additional to readable/clean clode our test base requires you to provide your error codes as a public constant prefixed
with **CODE_** in your sniff class.

### No "Test"-Namespace

Our test base expects you to provide everything in the "normal" namespace: _BestIt_.

### Helping test traits

#### Token Registration

The trait _BestIt\Sniffs\TestTokenRegistrationTrait_ helps you with testing of the registered tokens.

#### Public error codes

The trait _BestIt\Sniffs\TestRequiredConstantsTrait_ helps you to test the errors codes. We suggest that you test the 
values as well, because they could be part of a "foreign ruleset" out of your control from your "customer." So we 
enforce that the constant values stay api-stable!

#### Default Integration Tests

The trait _BestIt\Sniffs\DefaultSniffIntegrationTestTrait_ provides you with three tests to test the usually use cases of
a sniff based on sniff-individual test files:

1. testCorrect
2. testErrors
3. testWarnings

##### Requirements

* Your test-file should be called exactly like your sniff (including the namespace) but suffixed with _**Test**_
* Provide a folder **_Fixtures_** as a sibling to your test file.
* Put a folder into your "Fixtures" directory, which is called exactly like the short name of the sniff.

##### testCorrect

Create a **_correct_** folder into your fixtures directory. Every php file in this folder will be checked against your 
sniff. The sniff may not populate errors and warning for a successful test!

##### testErrors

Create a **_with_errors_** folder into your fixtures directory. Every php file in this folder should trigger an error in your sniff. 
You must provide the error structure through the file name. The file name must match the following pregex:

```regex
/(?P<code>[\w]*)(\(\d\))?\.(?P<errorLines>[\d\,]*)(?P<fixedSuffix>\.fixed)?\.php/
```

The file name gives information about which errors in which line should occur.
Example files would be _ErrorCode.1.php, ErrorCode.1,2,3.php, ErrorCode.1,2,3.fixed.php_, ErrorCode(2).1,2,3.php, 
ErrorCode(2).1,2,3.fixed.php_. The error code must be the
original code value from your sniff, the numbers after the first dot are the erroneous lines.

If you provide an additional file which is suffixed with "fixed" then this is the correct formatted file for its
erroneous sibling.

##### testWarnings

Create a **_with_warnings_** folder into your fixtures directory. Every php file in this folder should trigger a warning in your sniff. 
You must provide the warning structure through the file name. The file name must match the following pregex:

```regex
/(?P<code>[\w]*)(\(\d\))?\.(?P<errorLines>[\d\,]*)(?P<fixedSuffix>\.fixed)?\.php/
```

The file name gives information about which warning in which line should occur.
Example files would be _WarningCode.1.php, WarningCode.1,2,3.php, WarningCode.1,2,3.fixed.php_, 
WarningCode(2).1,2,3.php, WarningCode(2).1,2,3.fixed.php_. The warning code must be the
original code value from your sniff, the numbers after the first dot are the lines with warnings.

If you provide an additional file which is suffixed with "fixed" then this is the correct formatted file for its
erroneous sibling.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## TODO 

* Remove further slevomat dependencies for internal apis.