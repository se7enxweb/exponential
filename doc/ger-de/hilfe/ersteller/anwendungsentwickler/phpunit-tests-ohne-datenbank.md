---
title: PHPUnit-Tests ohne Datenbank
audience: app-creator, developer
category: app-creation
order: 500
summary: Schnelle PHPUnit-Tests für Code von Erweiterungen schreiben, die keine Datenbank brauchen - der Bootstrap, Ihre Klassen laden, Dienste, Validatoren, den Hin- und Rückweg der Zeichenkettenform von Datentypen und Template-Operatoren testen und nur die Tests einer Änderung ausführen.
keywords: PHPUnit, Unit-Tests, ohne Datenbank, Bootstrap, Dienste, Validatoren, Mocks, Anwendungsentwickler, unit tests, DB-less, bootstrap, tests/bootstrap.php, test-map.json, services, validators, toString, fromString, mocks, app creator
type: help
---
# PHPUnit-Tests ohne Datenbank

Diese Seite ist für Anwendungsentwickler, die Unit-Tests schreiben, die überall in Sekunden laufen. Die `phpunit.xml`
von Exponential lädt `tests/bootstrap.php`, das den Autoloader von Composer und das Test-Toolkit lädt, den Kernel aber
nicht startet; Tests, die nichts weiter brauchen, laufen ohne Datenbank ([PHPUnit](../../../standards/tests-und-ci.md#phpunit)).

## Inhalt

1. [Was sich ohne Datenbank testen lässt](#was-sich-ohne-datenbank-testen-lässt)
2. [Eine Testklasse](#eine-testklasse)
3. [Ihre Klassen laden](#ihre-klassen-laden)
4. [Ausführen](#ausführen)
5. [Tests den Dateien zuordnen](#tests-den-dateien-zuordnen)

## Was sich ohne Datenbank testen lässt

| Code | Beispiele für Prüfungen |
|---|---|
| Dienste mit reiner Logik | die Summen eines Berichts, das Parsen eines Imports |
| Validatoren | akzeptierte und abgelehnte Eingaben, Fehlertexte |
| Zeichenkettenformen von Datentypen | `fromString( toString( x ) )` ergibt `x` |
| Template-Operatoren | `modify()` setzt den erwarteten Wert |
| Auswertung von Einstellungen | Standardwerte, wenn ein Schlüssel fehlt |
| Sicherheitshelfer | Escaping, Positivlisten, Prüfung von Weiterleitungen |

Halten Sie Datenbankzugriffe aus diesen Klassen heraus oder hinter einer Schnittstelle, die Sie im Test ersetzen können.

## Eine Testklasse

```php
<?php
// extension/expdemo/tests/expDemoColorValidatorTest.php
class expDemoColorValidatorTest extends PHPUnit\Framework\TestCase
{
    public function testAcceptsHexColors()
    {
        $this->assertTrue( expDemoColorValidator::isValid( '#a1b2c3' ) );
    }

    public function testRefusesOtherText()
    {
        $this->assertFalse( expDemoColorValidator::isValid( 'red; background: url(x)' ) );
    }
}
```

## Ihre Klassen laden

Klassen in der Autoload-Map der Erweiterungen werden gefunden, sobald `php bin/php/ezpgenerateautoloads.php -e`
gelaufen ist. Ein Test, der in einem separaten Checkout läuft, kann die Klassendatei per `require_once` laden, wenn sie
nicht über den Autoloader erreichbar ist, so wie es die eigenen Tests der Hilfe im Kernel tun
(`tests/tests/kernel/classes/help/`).

## Ausführen

```bash
vendor/bin/phpunit extension/expdemo/tests/
vendor/bin/phpunit --filter testRefusesOtherText extension/expdemo/tests/
```

Führen Sie die Tests in der CI Ihrer Erweiterung bei jedem Push und Pull Request aus
([Continuous Integration für Erweiterungen](ci-fuer-erweiterungen.md)).

## Tests den Dateien zuordnen

Der Kernel pflegt `tests/test-map.json`, das Quelldateien den Tests zuordnet, die sie abdecken, sodass eine Änderung vor
einem Push nur ihre eigenen Tests ausführt. Machen Sie es in einer großen Erweiterung genauso: eine kleine
Zuordnungsdatei und ein Skript, das die Tests der geänderten Dateien auswählt.

## Verwandte Seiten

- [Anwendungen testen](anwendungen-testen.md)
- [Standard für Tests und CI](../../../standards/tests-und-ci.md)
- [PHPUnit-Versionen](../../../bc/6.0/phpunitv13.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
