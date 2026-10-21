---
title: Tests für Erweiterungen
audience: developer, app-creator
category: extension-development
order: 340
summary: Tests, die mit einer Erweiterung ausgeliefert werden und keine Datenbank brauchen - phpunit.xml.dist und Bootstrap, Unit- und Dateitests, die Testsuite des Kernels und die übrigen Prüfungen vor jedem Commit.
keywords: Tests, Unit-Test, ohne Datenbank, Template-Test, Styling-Test, Prüfungen vor dem Commit, tests, PHPUnit, phpunit.xml.dist, bootstrap, unit test, DB-less, TestCase, assertSame, template test, styling test, CI, checkclasses, checkcodestyle, php -l
type: help
---
# Tests für Erweiterungen

Teil 3, Kapitel 34. Ein Test ist der günstigste Weg zu wissen, dass Ihre Erweiterung nach einem Kernel-Upgrade, einem
PHP-Upgrade oder Ihrer eigenen nächsten Änderung noch funktioniert. Dieses Kapitel richtet PHPUnit für eine Erweiterung
so ein, wie es `git_manager` und `expui` tun, mit Tests, die keine Datenbank brauchen. Die Konfiguration und der Test
unten wurden mit dem PHPUnit ausgeführt, das das `vendor/` des Kernels bereitstellt (PHPUnit 13 auf der
Referenzinstallation): `OK (3 tests, 3 assertions)`.

## Der Aufbau

```text
extension/mysite_hello/
    phpunit.xml.dist
    tests/
        bootstrap.php
        unit/
            OperatorsTest.php
```

## phpunit.xml.dist

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

Das kommt der eigenen Datei von `git_manager` nahe. `failOnWarning` und `beStrictAboutOutputDuringTests` machen aus
nachlässigen Tests Fehlschläge.

## Der Bootstrap

```php
<?php
/**
 * PHPUnit bootstrap for mysite_hello: loads only the plain classes under test. Nothing of Exponential is booted.
 */
date_default_timezone_set( 'UTC' );
require_once dirname( __DIR__ ) . '/classes/mysitehellooperators.php';
```

Laden Sie nur, was Sie testen. Ein Test, der keine Datenbank, keinen Siteaccess und keine Sitzung braucht, läuft in
Millisekunden, auf jedem Rechner, in jeder CI.

## Ein Test

```php
<?php
/**
 * The template operators of mysite_hello, called directly: no template engine needed.
 */
class OperatorsTest extends PHPUnit\Framework\TestCase
{
    private function runOperator( $name, $value, array $named )
    {
        $operators = new mysiteHelloOperators();
        $operators->modify( null, $name, array(), '', '', $value, $named );
        return $value;
    }

    public function testGreetUsesTheDefaultGreeting()
    {
        $this->assertSame( 'Hello, Ada!', $this->runOperator( 'mysite_greet', null, array( 'name' => 'Ada', 'greeting' => 'Hello' ) ) );
    }

    public function testInitialsOfUtf8Names()
    {
        $this->assertSame( 'ÉZ', $this->runOperator( 'mysite_initials', 'émile zola', array() ) );
    }

    public function testEveryOperatorHasAParameterList()
    {
        $operators = new mysiteHelloOperators();
        $this->assertSame( $operators->operatorList(), array_keys( $operators->namedParameterList() ) );
    }
}
```

Ausführen:

```bash
cd extension/mysite_hello
php ../../vendor/bin/phpunit
```

## Was Sie testen

| Art von Code | Test |
|---|---|
| Operatoren, Hilfsfunktionen, Dienste, Validierung | rufen Sie sie direkt mit Werten auf und prüfen Sie das Ergebnis |
| Templates | lesen Sie die Dateien und prüfen Sie Regeln: kein `<style>`, kein Inline-Skript, jeder Text über `i18n` |
| Einstellungen | die ausgelieferte `.ini.append.php` lässt sich parsen und nennt Klassen, die existieren |
| Stylesheets | nur `--a4-*`-Tokens, nur Ihr Klassenpräfix (der `AdminStylingTest` von `git_manager` tut das) |
| Übersetzungen | jeder Quelltext hat eine Übersetzung in den Sprachen, die Sie ausliefern |
| Code, der die Datenbank braucht | halten Sie ihn schlank; testen Sie die Logik daneben ohne Datenbank |

`tests/unit/AdminStylingTest.php` von `git_manager` ist ein gutes Vorbild für einen Test, der Dateien prüft: Er liest die
Templates und das Stylesheet der Erweiterung und schlägt bei Inline-CSS, Inline-Skripten, fest codierten Farben und
fehlenden Übersetzungen fehl.

## Tests in der Testsuite des Kernels

Das Repository des Kernels testet auch einige Erweiterungen in `tests/tests/extension/<name>/` (zum Beispiel
`git_manager`, `ezjscore`, `ezoe`, `ezupdate`). Die CI des Kernels führt PHPUnit auf PHP 8.1 bis 8.5 ohne Datenbank aus,
dazu Datenbanktests auf MySQL und PostgreSQL ([Continuous Integration](../../../bc/6.0/ci.md)). Eine
Erweiterung, die ihre eigene `phpunit.xml.dist` mitbringt, kann auf dieselbe Weise in der CI ihres eigenen
Repositorys laufen.

## Weitere Prüfungen vor jedem Commit

```bash
find extension/mysite_hello -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
php bin/php/checkclasses.php --quiet-ok
./console exp:checkcodestyle
```

Die erste Prüfung gibt nichts aus, wenn sich jede Datei parsen lässt; die zweite, wenn jede Klasse geladen wird; die
dritte prüft geänderte Dateien gegen die `phpcs.xml.dist` des Kernels.

## Verwandte Seiten

- Zurück: [Admin-Seiten im admin4-Stil](admin-seiten-im-admin4-stil.md)
- Weiter: [Erweiterungen debuggen](erweiterungen-debuggen.md)
- [Von Hand: ein PHPUnit-Test für Ihre Erweiterung](tutorials/von-hand-phpunit-test.md)
- [Vor einem Release testen](../teil-7-veroeffentlichen/vor-einem-release-testen.md)
- [phpunit.de](https://phpunit.de/)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
