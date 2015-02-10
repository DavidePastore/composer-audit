# composer-audit
A composer plugin that checks if your application uses dependencies with known security vulnerabilities (it uses [SensioLabs Security Checker][1]).

## Installation
Using the `composer` command:

    $ composer require davidepastore/composer-audit:0.1.0

Manually adding in composer.json:

    {
      "require": {
        "davidepastore/composer-audit": "0.1.0"
      }
    }

## Usage
The checker will be executed when you launch `composer install` or `composer update`.
If you have alerts in your composer.lock, `composer-audit` will print them. An example could be this:

    ALERTS from SensioLabs security advisories.

     *** dompdf/dompdf[v0.6.0] ***

     * dompdf/dompdf/CVE-2014-2383.yaml
    Arbitrary file read in dompdf
    https://www.portcullis-security.com/security-research-and-downloads/security-advisories/cve-2014-2383/
    CVE-2014-2383
    
    
    Please fix these alerts from SensioLabs security advisories.

If no alert is found, you'll get this:

    All good from SensioLabs security advisories.

## Issues

If you have issues, just open one [here][2].

[1]: https://github.com/sensiolabs/security-checker
[2]: https://github.com/DavidePastore/composer-audit/issues
