# Spezifikation: die Sicherheits- und Stabilitätshärtung von 6.0.13

Diese Seite ist die Übersicht über den Härtungsdurchgang, den die Linie 6.0.13 am 21. Februar 2026 erhielt: sieben
Sicherheitsbefunde, eine größere Zahl von Absicherungen gegen Null- und undefinierte Werte, die PHP 8 in fatale Fehler
verwandeln würde, Korrekturen für Deprecations von PHP 8.4 und eine Testsuite, die sie nachweist. Lesen Sie sie, wenn Sie
6.0.12 oder älter betreiben oder Extensions pflegen, die dieselben Codemuster verwenden. Die vollständigen Befunde, Diffs,
Testergebnisse und CVSS-Werte stehen in den [Release-Notes zur Sicherheitshärtung](../../bc/6.0/haertung.md); die
Test-Werkzeugkette wird in [PHPUnit 10](../../bc/6.0/phpunitv10.md) erklärt.

## Was Sie tun müssen

1. Aktualisieren Sie auf 6.0.13 oder neuer. Alle Korrekturen sind abwärtskompatibel. Die einzige sichtbare Auswirkung: Ein
   Sortierspaltenname mit anderen Zeichen als `[a-zA-Z0-9_.]` wird mit einer Debug-Warnung übersprungen (SEC-02).
2. Wenn Sie eigene Extensions haben, lesen Sie [Für Extension-Entwickler](#für-extension-entwickler). Dieselben Muster
   können in Ihrem Code stecken.
3. Führen Sie die Sicherheitstests aus (siehe [Prüfen](#prüfen)).

## Die sieben Sicherheitsbefunde

| ID | Klasse | CWE | Basiswert | Datei | Korrektur |
|---|---|---|---|---|---|
| SEC-01 | SQL-Injection | 89 | 7.2 | `kernel/classes/ezrole.php` | Rollen-IDs werden nach `(int)` umgewandelt, bevor sie SQL erreichen |
| SEC-02 | SQL-Injection | 89 | 8.8 | `kernel/classes/ezcontentobjecttreenode.php` (`createSortingSQLStrings`) | Eigene Sortierspalten werden nur akzeptiert, wenn sie `[a-zA-Z0-9_.]` entsprechen |
| SEC-03 | SQL-Injection | 89 | 7.5 | dieselbe Datei (`createPermissionCheckingSQL`) | Teilbaumpfade werden mit `escapeString()` maskiert |
| SEC-04 | SQL-Injection | 89 | 6.5 | dieselbe Datei (`hideSubTree`, `unhideSubTree`) | Knoten-IDs werden umgewandelt, Pfade maskiert |
| SEC-05 | Einschleusen von Betriebssystembefehlen | 78 | 9.8 | `lib/ezutils/classes/ezsendmailtransport.php` | Die an `sendmail -f` übergebene Absenderadresse läuft durch `escapeshellarg()` |
| SEC-06 | Einschleusen von Betriebssystembefehlen | 78 | 8.1 | `lib/ezfile/classes/ezgzipshellcompressionhandler.php` | Gzip-Dateinamen laufen durch `escapeshellarg()` |
| SEC-07 | Reflektiertes XSS | 79 | 6.1 | `kernel/content/search.php` | Der Suchtext im Seitentitel wird HTML-kodiert |

Commits: SEC-01 bis SEC-06 in `221caccf31`; SEC-07 ist Teil von `5a4c83fb30`.

Bei HEAD ist `kernel/content/search.php` ein Einstiegspunkt mit einem einzigen Aufruf. Der Code, einschließlich des Titels mit
`htmlspecialchars( $searchText ... )`, liegt in `kernel/private/classes/views/content/search.php`
(siehe [Befehle, Cronjob-Teile und Modul-Views als Klassen](ausfuehrbare-befehle-cronjobs-ansichten.md)).

## Die Stabilitätskorrekturen

| Gruppe | Bedeutung | Beispiele | Commit |
|---|---|---|---|
| UND | Undefinierter Offset oder undefinierte Variable (in PHP 7 ein Hinweis, in PHP 8 ein Fehler) | `ezhttpheader.php`, `eznavigationpart.php`, `ezkeyword.php` | `dbb64b1692` |
| LOG | Rückgabewert von `preg_match_all` ohne Prüfung verwendet | `eznamepatternresolver.php` | `dbb64b1692` |
| NUL | Objekt oder Array kann null sein (`TypeError` in PHP 8) | `ezcontentobjecttrashnode.php`, `ezcontentupload.php`, `ezpackagehandler.php`, `ezimagealiashandler.php` | `dbb64b1692` |
| PRG | Ergebnis von `preg_replace()` kann null sein | `ezdifftextengine.php` | `dbb64b1692` |
| PHP | Deprecations von PHP 8.4 | `eztimetype.php` (kurze Zeitangaben wie `10:30`), `ezorder.php` (`fetchList()`: Pflichtparameter nach optionalen, nullable Typ) | `2b98050cc4` |
| IMP | Platzhalterklassen, die Daten stillschweigend verwarfen | `ezsoapparameter.php` (`setValue()` speicherte nichts), `ezsoapheader.php` (`addHeader()` speicherte nichts): SOAP-Aufrufe sendeten leere Parameter und keine Header | `5a4c83fb30` |
| SET | Setup-Skripte mit nicht initialisierten Variablen | `kernel/setup/cachetoolbar.php`, `datatype.php`, `extensions.php`, `session.php` und zwei Schritte des Assistenten | `5a4c83fb30` |
| KNT | Views des Content-Moduls ohne Null- oder Eingabeprüfungen | `kernel/content/node_edit.php`, `removenode.php`, `restore.php`, `upload.php`, `view.php` und weitere (12 Dateien) | `5a4c83fb30` |

Bei HEAD sind die KNT-Views Einstiegspunkte, die Klassen unter `kernel/private/classes/views/content/` aufrufen.

Die SOAP-Korrektur ist wichtig, wenn eine Extension über `lib/ezsoap` mit einem Zahlungsanbieter, einem entfernten Feed
oder einem Authentifizierungsanbieter kommuniziert: Aufrufe, die vorher erfolgreich aussahen, haben möglicherweise leere
Werte gesendet.

## Was sich nicht geändert hat

Der Patch-Satz hebt die PHP-Mindestversion nicht an. Die Analyse der PHP-Versionen je gepatchter Datei steht in den
Härtungs-Notes, Abschnitt „PHP Version Compatibility“.

## Für Extension-Entwickler

- Wandeln Sie IDs vor SQL in Integer um: `(int)$id` und `array_map( 'intval', $ids )` für Listen, die Sie per implode in
  `IN ( ... )` einsetzen.
- Maskieren Sie Strings mit `eZDB::instance()->escapeString( $text )`, bevor Sie sie einsetzen.
- Setzen Sie niemals einen Spalten- oder Tabellennamen aus der Anfrage ein; prüfen Sie ihn gegen eine Whitelist.
- Umschließen Sie jedes Shell-Argument mit `escapeshellarg()`.
- Kodieren Sie Benutzertext für den Ort, an dem er landet: `htmlspecialchars()` für HTML, den Operator `wash` in Templates.
- Sichern Sie gegen `null` ab: `$value ?? ''`, `$array ?? array()`, `is_array()` vor `implode()` oder `foreach`.

## Prüfen

Die Tests sind PHPUnit-10-Tests in `tests/tests/kernel/classes/security/`: `eZSecurityHardeningTest.php`
(14 Testmethoden bei HEAD) und `eZRSSSecurityTest.php`. Sie sind in `phpunit.xml` als Suite `security` registriert.
Führen Sie sie auf einer Entwicklungsinstallation aus, niemals gegen eine Datenbank im Produktivbetrieb:

```bash
php vendor/bin/phpunit --testsuite security
```

Als die Änderung vorgenommen wurde, bestanden nach dem Patch 14 von 14 Sicherheitstests und vorher keiner.

## Verwandte Seiten

- [Release-Notes zur Sicherheitshärtung](../../bc/6.0/haertung.md), [PHP-8-Unterstützung](../../bc/6.0/php8.md), [PHPUnit 10](../../bc/6.0/phpunitv10.md), [PHPUnit 13](../../bc/6.0/phpunitv13.md), [RAD-Werkzeuge – Sicherheit](../../bc/6.0/rad-sicherheit.md)
- [Spezifikation: die Sicherheitspatches vom August 2026](sicherheitshaertung-2026-08.md), [Sicherheitsvorgaben vom September 2026](sicherheitsvorgaben-2026-09.md), [Härtung der Datentypen und Eingaben](datentyp-eingabehaertung.md)
- [Changelog 6.0.13](../../../changelogs/6.0/6.0.13.md), [Chronik: Februar 2026](../../geschichte/2026/2026-02.md)
