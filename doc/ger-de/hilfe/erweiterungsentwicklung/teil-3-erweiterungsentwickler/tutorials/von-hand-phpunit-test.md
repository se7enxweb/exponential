---
title: "Von Hand: ein PHPUnit-Test für Ihre Erweiterung"
audience: developer, app-creator
category: extension-development
order: 230
summary: Tests, die ohne Datenbank laufen - phpunit.xml.dist, ein Bootstrap, Operator-Tests und ein Dateitest gegen Inline-CSS und -Skripte, grün mit dem PHPUnit des Kernels.
keywords: Tutorial, von Hand, Test, ohne Datenbank, Template-Test, Inline-CSS, Inline-Skript, tutorial, by hand, PHPUnit, test, phpunit.xml.dist, bootstrap, TestCase, assertSame, DB-less, template test, inline CSS, inline script
type: help
---
# Von Hand: ein PHPUnit-Test für Ihre Erweiterung

Tutorial 22. Sie fügen `mysite_hello` eine Testsuite hinzu, die weder Datenbank noch Request braucht und daher überall
in einer Sekunde läuft. Die Konfiguration, der Bootstrap und die Operator-Tests unten liefen mit dem PHPUnit im
`vendor/` des Kernels (PHPUnit 13 auf der Referenzinstallation) und meldeten `OK (3 tests, 3 assertions)`.

## 1. phpunit.xml.dist

`extension/mysite_hello/phpunit.xml.dist`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
  The unit tests of mysite_hello: plain PHP, no database, no request.
  Run from extension/mysite_hello: php ../../vendor/bin/phpunit
-->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         bootstrap="tests/bootstrap.php"
         colors="false"
         cacheDirectory="tests/.phpunit.cache"
         failOnWarning="true"
         failOnRisky="true"
         beStrictAboutOutputDuringTests="true">
    <testsuites>
        <testsuite name="unit">
            <directory suffix="Test.php">tests/unit</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

Fügen Sie `tests/.phpunit.cache/` zur `.gitignore` der Erweiterung hinzu.

## 2. Der Bootstrap

`extension/mysite_hello/tests/bootstrap.php`:

```php
<?php
/**
 * PHPUnit bootstrap for mysite_hello: loads only the plain classes under test. Nothing of Exponential is booted.
 */
date_default_timezone_set( 'UTC' );
require_once dirname( __DIR__ ) . '/classes/mysitehellooperators.php';
```

## 3. Die Operator-Tests

`extension/mysite_hello/tests/unit/OperatorsTest.php`: die drei Tests aus
[Tests für Erweiterungen](../tests-fuer-erweiterungen.md#ein-test) (Standardgruß, UTF-8-Initialen, jeder Operator
hat eine Parameterliste).

## 4. Ein Dateitest für Templates

`extension/mysite_hello/tests/unit/TemplatesTest.php`:

```php
<?php
/**
 * The templates of mysite_hello follow the administration's rules: no <style>, no style="", no inline script or
 * event handler. One test over every template, so it runs the same on every PHPUnit version the kernel supports.
 */
class TemplatesTest extends PHPUnit\Framework\TestCase
{
    public function testNoInlineCssOrScript()
    {
        $checked = 0;
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( dirname( __DIR__, 2 ) . '/design' ) );
        foreach ( $it as $file )
        {
            if ( !$file->isFile() || substr( $file->getFilename(), -4 ) !== '.tpl' )
                continue;
            $text = file_get_contents( $file->getPathname() );
            $name = $file->getPathname();
            $this->assertDoesNotMatchRegularExpression( '/<style\b/i', $text, "$name: a <style> block" );
            $this->assertDoesNotMatchRegularExpression( '/\sstyle\s*=/i', $text, "$name: a style attribute" );
            $this->assertDoesNotMatchRegularExpression( '/<script\b[^>]*>\s*\S/i', $text, "$name: an inline script" );
            $this->assertDoesNotMatchRegularExpression( '/\son[a-z]+\s*=/i', $text, "$name: an event handler attribute" );
            $checked++;
        }
        $this->assertGreaterThan( 0, $checked, 'no template was found' );
    }
}
```

Mit den Templates aus den früheren Tutorials meldete die ganze Suite `OK (4 tests, 24 assertions)`.

> **Hinweis**
> Eine erste Fassung dieses Tests verwendete eine `@dataProvider`-Annotation. PHPUnit 13 liest keine Annotationen mehr
> und rief den Test ohne Argumente auf (`ArgumentCountError`). Ein einziger Test, der über die Dateien läuft,
> funktioniert mit jeder PHPUnit-Version, die die `composer.json` des Kernels zulässt (9.6 bis 13); wenn Sie nur neue
> Versionen unterstützen, verwenden Sie stattdessen das Attribut `#[DataProvider('templates')]`.

## 5. Ausführen

```bash
cd extension/mysite_hello
php ../../vendor/bin/phpunit
```

## 6. In der CI

Nehmen Sie denselben Befehl in die CI des Repositorys der Erweiterung auf (zum Beispiel ein GitHub-Actions-Workflow,
der die Erweiterung auscheckt, PHP und PHPUnit mit Composer installiert und ihn ausführt). Die eigene CI des Kernels
führt PHPUnit auf PHP 8.1 bis 8.5 aus ([Continuous Integration](../../../../bc/6.0/ci.md)); tun Sie dasselbe
für die Versionen, die Ihre `composer.json` angibt.

## Verwandte Seiten

- Zurück: [Von Hand: Hilfeseiten für Ihre Erweiterung](von-hand-hilfeseiten-fuer-ihre-erweiterung.md)
- Weiter: [Abschlussprojekt: eine vollständige Erweiterung von der Idee bis zum Release](abschlussprojekt-eine-vollstaendige-erweiterung.md)
- [Tests für Erweiterungen](../tests-fuer-erweiterungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
