# best it PHP_CodeSniffer
This package contains a default rule set and custom rules which are used in all best it projects.

## Installation
Our PHP_CodeSniffer package can be installed with composer with the following command:
```bash
composer require bestit/phpcodesniffer --dev
```

## Usage
Create a PHP_CodeSniffer configuration (phpcs.xml.dist / phpcs.xml) like this:
```xml
<?xml version="1.0"?>
<ruleset name="PROJECT-X">
    <description>The coding standard for project x.</description>
    
    <!-- Path to best it ruleset. -->
    <rule ref="./vendor/bestit/codesniffer/src/Standards/BestIt/ruleset.xml" />
    
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
| Sniff        | Description         |
| ------------ | ------------------- |
|              |                     |

## Contributing
See [CONTRIBUTING.md](CONTRIBUTING.md).