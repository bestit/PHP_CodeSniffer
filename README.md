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

| Sniff | Description | suppressable |
| ----- | ----------- | ------------ |
| BestIt.Commenting.ClassDoc.TagFormatContentInvalid | Authors MUST commit to their classes and add an [author phpDoc-Tag](http://docs.phpdoc.org/references/phpdoc/tags/author.html). |
| BestIt.Comment.TagSorting.MissingNewlineBetweenTags | You SHOULD separate tag groups and the final return with a newline. | Yes (By Sniff-Name) |
| BestIt.Comment.TagSorting.WrongTagSorting | You SHOULD sort the tags by their occurrence and then alphabetically, but @return SHOULD be the last. | Yes (By Sniff-Name) |
| SlevomatCodingStandard.Classes.ClassConstantVisibility.MissingConstantVisibility | Constants MUST be marked with a visibility. |
| SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName | No class may be used via its FQCN. You MUST import every class! |
| BestIt.Functions.FluentSetter | Every setter function MUST return $this if nothing else is returned.  | Yes |
| BestIt.Formatting.SpaceAfterDeclare | There MUST be one empty line after declare-statement. |
| BestIt.TypeHints.DeclareStrictTypes | Every file MUST have "declare(strict_types=1);" two line breaks after the opening tag. There MUST be no spaces aroung the equal-sign. |
| BestIt.TypeHints.TypeHintDeclaration | Every function or method MUST have a type hint if the return annotation is valid. |
| Squiz.Strings.DoubleQuoteUsage | Every String MUST be wrapped with single quotes. |
| Generic.Formatting.SpaceAfterCast | There MUST be a space after cast. |
| Generic.Arrays.DisallowLongArraySyntax | Every array syntax MUST be in short array syntax. |
| BestIt.Formatting.OpenTagSniff | After the open tag there MUST be an empty line. |
| BestIt.Commenting.AbstractDocSniff.NoImmediateDocFound¹|  There MUST be a doc block before the listened token |
| BestIt.Commenting.AbstractDocSniff.NoSummary¹|  There MUST be a summary |
| BestIt.Commenting.AbstractDocSniff.SummaryNotFirst¹|  The summary MUST be the first statement in a doc block |
| BestIt.Commenting.AbstractDocSniff.SummaryTooLong¹|  The summary length MUST be maximum 120 characters |
| BestIt.Commenting.AbstractDocSniff.CommentNotMultiLine¹|  Every doc block must be multi line |
| BestIt.Commenting.AbstractDocSniff.NoLineAfterSummary¹|  There MUST be an empty line after the summary |
| BestIt.Commenting.AbstractDocSniff.LineAfterSummaryNotEmpty¹|  The line after the summary MUST be empty |
| BestIt.Commenting.AbstractDocSniff.DescriptionNotFound¹|  There MUST be doc block long description |
| BestIt.Commenting.AbstractDocSniff.NoLineAfterDescription¹|  There MUST be an empty line after the long description |
| BestIt.Commenting.AbstractDocSniff.MuchLinesAfterDescription¹|  There MUST be an empty line after the long description  |
| BestIt.Commenting.AbstractDocSniff.DescriptionTooLong¹|  Every line of the long description MUST be not longer than 120 characters |
| BestIt.Commenting.AbstractDocSniff.TagNotAllowed¹|  The given tag MUST NOT be used |
| BestIt.Commenting.AbstractDocSniff.TagOccurrenceMin¹|  The given tag MUST occur min x times |
| BestIt.Commenting.AbstractDocSniff.TagOccurrenceMax¹|  The given tag MUST occur max x times |
| BestIt.Commenting.AbstractDocSniff.TagWrongPosition¹|  The given tag MUST be at the correct position |
| BestIt.Commenting.AbstractDocSniff.SummaryUcFirst¹|  The summary first letter MUST be a capital letter |
| BestIt.Commenting.AbstractDocSniff.DescriptionUcFirst¹|  The long description first letter MUST be a capital letter |
| BestIt.Commenting.AbstractDocSniff.NoLineAfterTag¹|  There MUST be an empty line after the given tag |
| BestIt.Commenting.AbstractDocSniff.MuchLinesAfterTag¹|  There MUST be a single empty line after the given tag |
| BestIt.Commenting.AbstractDocSniff.TagFormatContentInvalid¹|  The tag content MUST match the given pattern |

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
/(?P<code>[\w]+)(\(\w*\))?\.(?P<errorLines>[\d\,]+)(?P<fixedSuffix>\.fixed)?\.php/
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
/(?P<code>[\w]+)(\(\w*\))?\.(?P<errorLines>[\d\,]+)(?P<fixedSuffix>\.fixed)?\.php/
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