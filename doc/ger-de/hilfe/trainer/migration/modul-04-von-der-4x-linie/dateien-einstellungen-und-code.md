---
title: "Lektion 4.3: Dateien, Einstellungen, Designs und die Portierung von Erweiterungen auf PHP 8"
audience: trainer, admin, developer
category: training
order: 43
summary: Die Teilnehmenden übernehmen den Speicher, die Override-Einstellungen und die Designs einer alten Website richtig in Exponential 6, sehen die Einstellungen durch, die sich ändern müssen, und portieren ihre eigenen Erweiterungen mit der Checkliste aus Kapitel 14.9 von PHP 5 auf PHP 8. 60 Minuten.
keywords: migration zu exponential, speicher, override-einstellungen, siteaccess, design-erweiterung, templates, erweiterungen portieren, migrating to exponential, var storage, VarDir, settings override, design extension, eztemplatecheck, PHP 8, porting extensions, exp:checkclasses, autoloads, each, create_function, YUI, jQuery 4
type: help
---
# Lektion 4.3: Dateien, Einstellungen, Designs und die Portierung von Erweiterungen auf PHP 8

Neben der Datenbank laufen zwei weitere Stränge: die Dateien und Einstellungen sowie der Code. Diese Lektion behandelt,
was kopiert wird (und was niemals), welche Einstellungen sich ändern müssen, wohin Designs gehören, und die
Checkliste für die Portierung eigener Erweiterungen von PHP 5 auf PHP 8, die Kapitel 14 als den größten Teil der
meisten Migrationen bezeichnet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 4: Von der 4.x-Linie](README.md) |
| Dauer | 60 Minuten (25 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren (Dateien und Einstellungen) und Entwickler (Code) |
| Voraussetzungen | [Lektion 4.2](das-datenbank-runbook.md); PHP-Kenntnisse für den Codeteil; Shell-Zugang zu einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- nennen, was von der alten Website kopiert wird (Speicher, Overrides, Siteaccess-Einstellungen,
  Konfigurationsdateien) und was niemals (Caches, die Standard-`settings/*.ini`);
- erklären, warum `[FileSettings] VarDir` genau so bleiben muss, wie es war;
- fünf Einstellungen nennen, die sich zwischen 4.x und Exponential 6 ändern;
- ein Design in eine Design-Erweiterung verschieben und Templates mit `eztemplatecheck.php` prüfen;
- die Portierungs-Checkliste auf eine Erweiterung anwenden und drei Brüche durch PHP 8 beheben.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Kapitel 14.6](../../../../installation/14-migration-von-4x.md#146-dateien-speicher-bilder-binärdateien-und-der-cluster),
[14.7](../../../../installation/14-migration-von-4x.md#147-einstellungen-und-siteaccesses),
[14.8](../../../../installation/14-migration-von-4x.md#148-designs-templates-und-javascript) und
[14.9](../../../../installation/14-migration-von-4x.md#149-eigene-extensions-von-php-5-auf-php-8-portieren).

1. **Dateien** (5 Minuten). Kopieren Sie `var/` (mindestens den Speicher); lassen Sie `VarDir` so, wie es war, weil
   jeder gespeicherte Bildpfad damit beginnt; kopieren Sie niemals Caches; Bildvarianten werden bei Bedarf neu
   erzeugt. Folgen Sie nicht dem alten Rat `chmod -R a+rwx`.
2. **Einstellungen** (6 Minuten). Kopieren Sie nur `settings/override/`, `settings/siteaccess/<name>/` und die
   Konfigurationsdateien; niemals die alten `settings/*.ini`. Gehen Sie die Tabelle „Zu ändernde Einstellungen“
   durch: `ezmysql` zu `ezmysqli`, Zeichensatz `utf-8`, `ezdhtml` aus `ActiveExtensions` entfernen, das
   `[DesignSettings]` der Administration aus einem frischen Admin-Siteaccess, Override-Blöcke nach `Priority`
   sortiert, die YUI-Schlüssel gelöscht, `*.ini.append` in `*.ini.append.php` umbenannt. Rewrite-Regeln kommen aus
   [Kapitel 8](../../../../installation/08-die-website-ausliefern.md), nicht von der alten Website.
3. **Designs** (4 Minuten). Verschieben Sie Ihre Designs in eine Design-Erweiterung; bearbeiten Sie niemals
   ausgelieferte Erweiterungen (sie werden bei jedem Update ersetzt). YUI lädt nichts mehr; jQuery ist 4.0.0.
4. **Portierung** (10 Minuten). Die Checkliste mit sieben Schritten aus 14.9: Bestandsaufnahme, jede Datei auf Syntax
   prüfen, Autoloads neu erzeugen und jede Klasse mit `exp:checkclasses` laden, Zeichen um die PHP-Tags prüfen, die
   Brüche durch PHP 8 beheben, dann Deprecations, Templates und Einstellungen, der Erweiterung eine Version geben.
   Zeigen Sie die Tabelle „Aus PHP entfernte Funktionen und Syntax“ (`each()`, `create_function()`, `$string{0}`,
   `split()`, die Familie `mysql_*`, die Argumentreihenfolge von `implode()`).

### Häufige Fragen

**Warum gewinnt ein Template-Override nicht mehr?** Override-Blöcke werden seit Juli 2026 nach `Priority` sortiert,
oder die Reihenfolge der Erweiterungen hat sich geändert (14.13).

**`Class '...' not found`?** Das Autoload-Array wurde nicht neu erzeugt: `php bin/php/ezpgenerateautoloads.php -e`,
dann die Caches leeren (14.13).

**Können unsere TinyMCE-3-Plugins für den Online-Editor bleiben?** Mit der Standard-Engine ja; ezoe verwendet
standardmäßig TinyMCE 3.5.12, und TinyMCE 8 muss ausdrücklich eingeschaltet werden (14.8).

### Stolperfallen

- **Das ganze alte Verzeichnis `settings/` kopieren.** Die neuen Standardwerte sind Teil des Codes; die alten Dateien
  verdecken sie.
- **Ein Parse-Fehler in einer Erweiterung.** Er legt die ganze Website lahm, nicht nur die Erweiterung: Prüfen Sie
  zuerst die Syntax.
- **ezwebin oder ezflow direkt bearbeiten.** Das nächste Update überschreibt es; Overrides gehören in Ihre eigene
  Design-Erweiterung.

## Praktische Übung

Teil 1 läuft auf Ihrer Schulungsinstallation; Teil 2 ist eine Code-Leseübung für Entwickler (Administratoren sehen
stattdessen Einstellungen durch).

1. Prüfen Sie im Wurzelverzeichnis der Installation die Templates Ihres öffentlichen Siteaccess (verwenden Sie dessen
   Namen aus `settings/siteaccess/`):

   ```bash
   ls settings/siteaccess/
   php bin/php/eztemplatecheck.php -s<siteaccess>
   ```

2. Prüfen Sie die Syntax jeder PHP-Datei einer Erweiterung Ihrer Wahl:

   ```bash
   find extension/<extension> -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
   ```

3. Laden Sie jede Klasse und melden Sie die, die PHP ablehnt:

   ```bash
   php bin/php/console exp:checkclasses
   ```

4. Lesen Sie die Einstellung für das Speicherverzeichnis:

   ```bash
   php bin/php/console exp:ini get site.ini/FileSettings/VarDir
   ```

5. **Entwickler**: Schreiben Sie diesen PHP-5-Code mithilfe der Tabellen aus Kapitel 14.9 für PHP 8 um:

   ```php
   while ( list( $key, $value ) = each( $items ) ) { $out[] = $key; }
   $double = create_function( '$a', 'return $a * 2;' );
   $first = $name{0};
   $parts = split( ',', $csv );
   $line = implode( $parts, ';' );
   ```

6. **Administratoren**: Schreiben Sie für eine alte Website mit `DatabaseImplementation=ezmysql`, `ezdhtml` in
   `ActiveExtensions[]`, `ezjscore.ini` mit `PreferredLibrary=yui3` und einer Datei `site.ini.append` auf, was Sie
   ändern und warum, anhand der Tabelle „Zu ändernde Einstellungen“ aus Kapitel 14.7.

## Erwartetes Ergebnis

- Schritt 1 endet mit den ausgelieferten Templates ohne Fehler; Schritt 2 gibt bei einer sauberen Erweiterung nichts
  aus.
- Schritt 3 listet keine Klasse auf, oder die Klassen, die PHP ablehnt, mit dem Grund.
- Schritt 4 gibt das `VarDir` der Installation aus (zum Beispiel `var/site` oder das des Seitenpakets).
- Schritt 5: `foreach ( $items as $key => $value )`, eine Closure `function ( $a ) { return $a * 2; }`, `$name[0]`,
  `explode( ',', $csv )`, `implode( ';', $parts )`.
- Schritt 6: `ezmysqli`, `ezdhtml` entfernen (ezoe hat es ersetzt), die YUI-Schlüssel löschen, in
  `site.ini.append.php` umbenennen mit der ersten Zeile `<?php /* #?ini charset="utf-8"?`.
- Der Trainer prüft bei jeder Person Schritt 5 oder Schritt 6.

## Quiz

1. Warum muss `[FileSettings] VarDir` so bleiben, wie es auf der alten Website war?
2. Was davon kopieren Sie **nicht** von der alten Installation?
   A. `settings/override/` B. `settings/siteaccess/<name>/` C. `var/cache/` D. `var/<VarDir>/storage/`
3. Welcher Befehl meldet jede Klasse, die PHP nicht laden will?
4. Wohin gehören Änderungen, die Sie in einer ausgelieferten Erweiterung wie ezwebin gemacht haben?
5. Was ersetzt `create_function()` in PHP 8?

<details>
<summary>Antworten</summary>

1. Die Pfade aller gespeicherten Bilder und Dateien beginnen damit
   ([Kapitel 14.6](../../../../installation/14-migration-von-4x.md#146-dateien-speicher-bilder-binärdateien-und-der-cluster)).
2. **C.** Caches werden vom neuen Code neu aufgebaut (Kapitel 14.6).
3. `php bin/php/console exp:checkclasses`
   ([Kapitel 14.9](../../../../installation/14-migration-von-4x.md#149-eigene-extensions-von-php-5-auf-php-8-portieren)).
4. In Ihre eigene Design-Erweiterung, als Overrides
   ([Kapitel 14.8](../../../../installation/14-migration-von-4x.md#148-designs-templates-und-javascript)).
5. Eine Closure, `function ( $a ) { ... }` (Kapitel 14.9, „Aus PHP entfernte Funktionen und Syntax“).

</details>

## Weiterführende Informationen

- [Kapitel 14.7: Einstellungen und Siteaccesses](../../../../installation/14-migration-von-4x.md#147-einstellungen-und-siteaccesses):
  die vollständige Tabelle der zu ändernden Einstellungen.
- [Kapitel 14.9: Eigene Erweiterungen portieren](../../../../installation/14-migration-von-4x.md#149-eigene-extensions-von-php-5-auf-php-8-portieren):
  jeder Bruch durch PHP 8 mit Code vorher und nachher.
- [YUI entfernt](../../../../../eng-us/bc/6.0/yui-removal.md): YUI in eigenen Templates ersetzen.

## Verwandte Seiten

- [Modul 4: Von der 4.x-Linie](README.md)
- [Migration zu Exponential: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
