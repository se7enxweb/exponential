---
title: Code schreiben, der unter PHP 8.0 läuft
audience: php-developer, developer
category: php-development
order: 60
summary: Die Regeln für PHP-Code, der unter PHP 8.0, der ältesten unterstützten Version, laufen muss - die Syntax, die Sie nicht verwenden dürfen, wie lib/phpcompat.php und Absicherungen mit function_exists() funktionieren, in späteren Versionen hinzugekommene Argumente, die Flags für das HTML-Maskieren und wie Sie Ihren Code mit PHP 8.0 und PHPCompatibility prüfen.
keywords: PHP 8.0, Kompatibilität, Absicherung, Syntax, HTML-Maskierung, compatibility, phpcompat.php, function_exists, PHP_VERSION_ID, array_is_list, fputcsv, fsync, enums, readonly, never, first-class callable, htmlspecialchars flags, PHPCompatibility, testVersion, lint80
type: help
---
# Code schreiben, der unter PHP 8.0 läuft

Diese Seite richtet sich an PHP-Entwickler, die Kernel-Code oder Erweiterungen schreiben, die mit Exponential
ausgeliefert werden. Jede solche Datei muss unter PHP 8.0, der ältesten unterstützten Version, und unter 8.5, der
neuesten, laufen. Die Regeln sind kurz, und die Werkzeuge prüfen sie, aber die CI führt PHPUnit erst ab 8.1 aus: Unter
8.0 wird Ihr Code nur durch Syntax und Boot geprüft, also machen Sie es gleich beim Schreiben richtig.

## Inhalt

1. [Syntax, die Sie nicht verwenden dürfen](#syntax-die-sie-nicht-verwenden-dürfen)
2. [Funktionen, die neuer als PHP 8.0 sind](#funktionen-die-neuer-als-php-80-sind)
3. [Argumente, die spätere Versionen hinzugefügt haben](#argumente-die-spätere-versionen-hinzugefügt-haben)
4. [Geänderte Standardwerte: HTML-Maskierung](#geänderte-standardwerte-html-maskierung)
5. [Ihren Code prüfen](#ihren-code-prüfen)
6. [Beispiele aus dem Kernel](#beispiele-aus-dem-kernel)

## Syntax, die Sie nicht verwenden dürfen

PHP 8.0 kann Folgendes nicht parsen, sodass ein einziges Vorkommen die ganze Datei am Laden hindert:

| Syntax | Seit | Schreiben Sie stattdessen |
|---|---|---|
| `enum Status { ... }` | 8.1 | Klassenkonstanten |
| `readonly`-Eigenschaften und -Klassen | 8.1, 8.2 | eine private Eigenschaft mit einem Getter |
| Rückgabetyp `never` | 8.1 | keinen Rückgabetyp (der Kernel-Stil hat ohnehin keinen) |
| `new` in Initialisierern (`function f( $x = new Foo() )`) | 8.1 | `$x = null`, dann `$x = $x ?? new Foo()` |
| First-Class-Callables (`strlen(...)`) | 8.1 | `'strlen'` oder `array( $object, 'method' )` oder eine Closure |
| Schnittmengentypen (`A&B`) | 8.1 | keinen Typ (siehe [Warum der Code keine Typdeklarationen hat](warum-keine-typdeklarationen.md)) |
| typisierte Klassenkonstanten | 8.3 | eine untypisierte Konstante |

Attribute wie `#[\ReturnTypeWillChange]` und `#[\Override]` sind unter 8.0 harmlos: PHP 8.0 liest sie als Attribute und
ignoriert unbekannte.

## Funktionen, die neuer als PHP 8.0 sind

Es gibt zwei Wege, und der Kernel nutzt beide:

1. **Eine Funktion, die sich in PHP mit demselben Ergebnis schreiben lässt, kommt in `lib/phpcompat.php`**, innerhalb von
   `if ( !function_exists( '...' ) )`. `autoload.php` lädt die Datei nur, wenn `PHP_VERSION_ID < 80100`, sodass sie ab
   8.1 nicht einmal geöffnet wird und PHPs eigene Funktion läuft. Heute definiert sie `array_is_list()`.

   ```php
   if ( !function_exists( 'array_is_list' ) )
   {
       function array_is_list( array $array )
       {
           $i = 0;
           foreach ( $array as $key => $unused )
           {
               if ( $key !== $i++ )
                   return false;
           }
           return true;
       }
   }
   ```

2. **Eine Funktion, die sich nicht in PHP schreiben lässt, wird hinter `function_exists()` aufgerufen**, mit einem
   Ersatz, der unter 8.0 korrekt ist:

   ```php
   fflush( $handle );
   if ( function_exists( 'fsync' ) )
       fsync( $handle );
   ```

   Der Kernel tut das für `fsync()` (8.1), `memory_reset_peak_usage()` (8.2), `str_increment()` und
   `str_decrement()` (8.3) sowie Bildfunktionen wie `imageavif()`.

## Argumente, die spätere Versionen hinzugefügt haben

Eine Funktion, die es unter 8.0 gibt, die aber später einen Parameter bekommen hat, wirft unter 8.0 einen
`ArgumentCountError`, wenn Sie ihn übergeben. Verzweigen Sie nach `PHP_VERSION_ID`:

```php
if ( PHP_VERSION_ID >= 80100 )
    fputcsv( $handle, $row, ',', '"', '\\', "\r\n" );
else
{
    // PHP 8.0: no $eol parameter; write the line and fix its ending afterwards
}
```

So schreibt `kernel/classes/subitems/expsubitemscsvexport.php` CSV unter jeder Version.

## Geänderte Standardwerte: HTML-Maskierung

PHP 8.1 hat die Standard-Flags von `htmlspecialchars()`, `htmlentities()`, `html_entity_decode()` und
`htmlspecialchars_decode()` auf `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401` geändert. Der Standard von PHP 8.0 lässt
`'` unmaskiert, sodass ein Wert in einem HTML-Attribut mit einfachen Anführungszeichen unter 8.0 nicht sicher wäre.
**Übergeben Sie die Flags immer ausdrücklich:**

```php
$html = htmlspecialchars( $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
```

Der Template-Operator `wash` und 27 weitere Kernel- und Erweiterungsdateien übergeben sie seit 6.0.15, sodass jede
Version dieselbe Ausgabe liefert ([Ausgaben aus PHP maskieren](ausgaben-aus-php-maskieren.md)).

## Ihren Code prüfen

| Prüfung | Befehl | Findet |
|---|---|---|
| PHP-8.0-Syntax geänderter Dateien | `make lint80` (findet die Binärdatei als `EXP_PHP80`, `php8.0`, `php80` oder `PHP80=/path`) | Parse-Fehler durch neuere Syntax |
| PHP-8.0-Syntax jeder Datei | `make lint80-all PHP80=/path/to/php8.0` | dasselbe überall, wie der CI-Job `php80` |
| PHPCompatibility | `make phpcs` (`testVersion` `8.0-` in `phpcs.xml.dist`) | neuere Funktionen und Argumente, entfernte und veraltete Funktionen |
| Boot unter 8.0 | `php bin/php/ezexec.php tests/bin/bootsmoke.php` mit PHP 8.0 | eine Klasse, die nicht geladen werden kann |
| Installation unter 8.0 | `composer install --no-dev --prefer-dist` mit PHP 8.0, in einem sauberen Checkout | eine Abhängigkeit, deren Minimum zu hoch ist |

`bootsmoke.php` lädt die Klassen aus `autoload/ezp_kernel.php` und der Erweiterungs-Map (ohne die Befehls-, View- und
Cronjob-Klassen und die Tests, deren Syntax stattdessen geprüft wird), sodass ein fataler Fehler beim Laden einer Klasse,
etwa eine Methodensignatur, die nicht mehr zu ihrer Elternklasse passt, auch ohne einen Test sichtbar wird, der die
Klasse verwendet.

## Beispiele aus dem Kernel

Die Arbeit an 6.0.15 hat Folgendes gefunden, und dieselben Fehler sind in Erweiterungen häufig
([Unterstützung für PHP 8.0](../../bc/6.0/php-8.0-unterstuetzung.md)):

| Fehler | Wirkung unter PHP 8 | Korrektur |
|---|---|---|
| `implode( $pieces, $glue )` | `TypeError` seit 8.0 | `implode( $glue, $pieces )` |
| `crypt()` ohne Salt | `ArgumentCountError` seit 8.0 | ein Zufallswert aus `random_bytes()` |
| ein Referenzparameter, wo der der Elternklasse ein Wertparameter ist | fataler Fehler, wenn die Klasse geladen wird | die Signatur der Elternklasse übernehmen |
| eine Methode einer Unterklasse mit weniger Parametern als die der Elternklasse | fataler Fehler, wenn die Klasse geladen wird | die Parameter hinzufügen |
| `each()`, `split()`, `create_function()` | entfernt | `foreach`, `explode()`, eine Closure (`phpcs.xml.dist` verbietet sie) |

## Verwandte Seiten

- [Unterstützte PHP-Versionen: 8.0 bis 8.5](unterstuetzte-php-versionen.md)
- [Hinweise zur PHP-8-Migration für Ihren Code](hinweise-zur-php-8-migration-fuer-ihren-code.md)
- [Entfernte PHP-Funktionen](../erweiterungsentwicklung/teil-4-aktualisieren/entfernte-php-funktionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
