---
title: Hinweise zur PHP-8-Migration für Ihren Code
audience: php-developer, developer
category: php-development
order: 70
summary: Was Sie an älterem PHP-Code ändern müssen, damit er unter Exponential 6 sauber unter PHP 7.4 bis 8.5 läuft - dynamische Eigenschaften, count() auf Nicht-Arrays, null an String-Funktionen, SPL-Rückgabetypen, nullbare Parameter, entfernte Funktionen - und wie Sie jedes davon finden.
keywords: PHP-8-Migration, Deprecations, dynamische Eigenschaften, nullbare Typen, Aktualisierung, PHP 8 migration, deprecations, dynamic properties, AllowDynamicProperties, count, is_countable, null to string functions, ReturnTypeWillChange, nullable types, implicit nullable, E_STRICT, utf8_decode, eZPersistentObject, eZDataType, upgrade
type: help
---
# Hinweise zur PHP-8-Migration für Ihren Code

Diese Seite richtet sich an PHP-Entwickler, die älteren Code, oft eine für PHP 5 oder 7 geschriebene Erweiterung, für
Exponential 6 auf PHP 8 bringen. Der Kernel hat dieselbe Arbeit zwischen Dezember 2023 und April 2026 durchlaufen
([Unterstützung für PHP 8](../../bc/6.0/php8.md)); diese Seite macht aus seinen Erfahrungen eine Liste, die Sie
abarbeiten können.

## Inhalt

1. [Wie Sie die Probleme finden](#wie-sie-die-probleme-finden)
2. [Deklarieren Sie die Eigenschaften, die Sie verwenden](#deklarieren-sie-die-eigenschaften-die-sie-verwenden)
3. [Prüfen Sie, bevor Sie zählen](#prüfen-sie-bevor-sie-zählen)
4. [Übergeben Sie kein null an String-Funktionen](#übergeben-sie-kein-null-an-string-funktionen)
5. [Markieren Sie Methoden von SPL-Interfaces](#markieren-sie-methoden-von-spl-interfaces)
6. [Nullbare Parameter](#nullbare-parameter)
7. [Entfernte Funktionen und Konstanten](#entfernte-funktionen-und-konstanten)
8. [Signaturen, die zum Kernel passen müssen](#signaturen-die-zum-kernel-passen-müssen)
9. [Eine Arbeitsreihenfolge](#eine-arbeitsreihenfolge)

## Wie Sie die Probleme finden

- **Debug-Ausgabe auf einer Entwicklungskopie.** Aktivieren Sie `[DebugSettings] DebugOutput=enabled` und benutzen Sie
  die Seiten Ihrer Erweiterung; achten Sie auf Hinweise „Deprecated“, die Ihre Dateien nennen.
- **Die Logs.** `var/log/warning.log` und `var/log/error.log` sammeln, was die Debug-Ausgabe zeigt
  ([Logs, der Log-Viewer und Anfrage-Logs](logs-log-viewer-und-anfrage-logs.md)).
- **Statische Prüfungen.** `.devtools/vendor/bin/phpcs --standard=phpcs.xml.dist extension/<ext>/` meldet Befunde von
  PHPCompatibility (entfernte und veraltete Funktionen), und PHPStan findet undefinierte Methoden und falsche
  Argumentanzahlen ([Statische Analyse mit PHPStan](statische-analyse-mit-phpstan.md)).
- **Syntax unter dem ältesten und dem neuesten PHP.** `php -l` mit 8.0 und mit 8.5.

## Deklarieren Sie die Eigenschaften, die Sie verwenden

PHP 8.2 markiert das Anlegen einer Eigenschaft, die die Klasse nicht deklariert, als veraltet. Alte Erweiterungen tun
das ständig, oft in Klassen, die `eZPersistentObject` oder `eZDataType` erweitern:

```php
class acmeReport extends eZPersistentObject
{
    /** declared, so PHP 8.2 and later do not warn */
    public $ID;
    public $Title;
}
```

Unterklassen von `eZPersistentObject` erzeugen seit 6.0.4 keine Warnungen mehr in der Debug-Ausgabe, aber die
Eigenschaften zu deklarieren ist weiterhin die saubere Lösung, und es dokumentiert die Klasse
([Dynamische Eigenschaften](../erweiterungsentwicklung/teil-4-aktualisieren/dynamische-eigenschaften.md)).

## Prüfen Sie, bevor Sie zählen

`count()` wirft seit PHP 8.0 bei `null` und skalaren Werten einen `TypeError`:

```php
// before
if ( count( $list ) > 0 ) { ... }

// after
if ( is_array( $list ) && count( $list ) > 0 ) { ... }
// or, for arrays and Countable objects
if ( is_countable( $list ) && count( $list ) > 0 ) { ... }
```

Dasselbe gilt für `foreach` über einen Wert, der `null` sein kann: Sichern Sie ihn ab oder geben Sie ihm einen
Standardwert (`$list ?? array()`).

## Übergeben Sie kein null an String-Funktionen

PHP 8.1 markiert `null` für nicht nullbare Parameter interner Funktionen als veraltet (`strlen( null )`, `trim( null )`,
`preg_match( $p, null )`). Werte aus Einstellungen, der Datenbank und `$Params` sind oft `null`:

```php
$title = trim( (string)( $row['title'] ?? '' ) );
```

Der Kernel hat das in 6.0.11 bei Aufrufen von `mysqli_real_escape_string()` und `preg_match()` behoben.

## Markieren Sie Methoden von SPL-Interfaces

Methoden, die `Iterator`, `ArrayAccess`, `Countable` oder `IteratorAggregate` implementieren, müssen die vorläufigen
Rückgabetypen von PHP 8.1 deklarieren oder das Attribut tragen. Der Kernel-Stil hat keine Rückgabetypen, also verwenden
Sie das Attribut:

```php
#[\ReturnTypeWillChange]
public function current()
{
    return $this->items[$this->position];
}
```

PHPStan kann einen fehlenden vorläufigen Rückgabetyp nicht in die Baseline aufnehmen; deshalb schließt
`phpstan.neon.dist` `dfs_filter_iterator.php` aus, bis es behoben ist.

## Nullbare Parameter

PHP 8.4 markiert einen Parameter als veraltet, der einen Klassentyp und einen Standardwert `null` ohne `?` hat
(implizit nullbar). Schreiben Sie das `?` ausdrücklich; nullbare Klassen-Hints erlaubt der Coding-Standard:

```php
// before
public static function reportDirectory( eZINI $siteINI = null )
// after
public static function reportDirectory( ?eZINI $siteINI = null )
```

Siehe [Nullbare Parameter und Typen](../erweiterungsentwicklung/teil-4-aktualisieren/nullable-parameter-und-typen.md).

## Entfernte Funktionen und Konstanten

| Entfernt | Seit | Verwenden Sie |
|---|---|---|
| `each()` | 8.0 | `foreach` oder `key()` und `current()` |
| `create_function()` | 8.0 | eine Closure |
| `split()`, `ereg()`, `eregi()`, `ereg_replace()` | 7.0 | `explode()`, `preg_split()`, `preg_match()`, `preg_replace()` |
| `mysql_*` | 7.0 | `eZDB` |
| `E_STRICT` | veraltet in 8.4 | lassen Sie es aus Fehlermasken weg |
| `utf8_decode()`, `utf8_encode()` | veraltet in 8.2 | `mb_convert_encoding()` |

`phpcs.xml.dist` verbietet `each`, `create_function`, `split`, `ereg`, `eregi`, `ereg_replace`, `mysql_query`,
`mysql_connect`, `sizeof` und `delete` ([Der Coding-Standard](der-coding-standard.md)).

## Signaturen, die zum Kernel passen müssen

Eine Klasse, die eine Kernel-Methode überschreibt, muss deren Parameter beibehalten: dieselbe Anzahl (mehr mit
Standardwerten ist in Ordnung), dieselben Referenzmarkierungen. Unter PHP 8 ist eine Abweichung ein fataler Fehler
**beim Laden der Klasse**, sodass die ganze Seite bricht, nicht nur die Methode. Vergleichen Sie beim Aktualisieren einer
Erweiterung mit der Elternklasse in `kernel/classes/`; der Boot-Smoke-Test findet solche Fälle
([Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md#ihren-code-prüfen)).

## Eine Arbeitsreihenfolge

1. Sorgen Sie dafür, dass jede Datei `php -l` unter 8.0 und 8.5 besteht.
2. Laden Sie jede Klasse (Boot-Smoke-Test oder jede Seite öffnen): Korrigieren Sie Signaturen.
3. Beheben Sie Warnungen zu `count()`, `null` und Eigenschaften aus der Debug-Ausgabe, Seite für Seite.
4. Führen Sie PHPCompatibility und PHPStan auf der Erweiterung aus.
5. Heben Sie die PHP-Einschränkung in `composer.json` auf `^8.0` an und veröffentlichen Sie ein Release ([Die Checkliste für das Upgrade](../erweiterungsentwicklung/teil-4-aktualisieren/die-upgrade-checkliste.md)).

## Verwandte Seiten

- [Deprecations von PHP 8.1 bis 8.5](../erweiterungsentwicklung/teil-4-aktualisieren/php-8-1-bis-8-5-veraltete-funktionen.md)
- [PHP-8-Fehler, die alten Code stoppen](../erweiterungsentwicklung/teil-4-aktualisieren/php-8-fehler-die-alten-code-stoppen.md)
- [Datenbankcode für PHP 8](../erweiterungsentwicklung/teil-4-aktualisieren/datenbankcode-fuer-php-8.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
