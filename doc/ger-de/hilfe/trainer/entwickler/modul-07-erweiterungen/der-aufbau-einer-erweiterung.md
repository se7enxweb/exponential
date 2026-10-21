---
title: "Lektion 7.1: Der Aufbau einer Erweiterung"
audience: trainer, developer
category: training
order: 71
summary: Die Teilnehmenden ordnen jeden Ordner einer echten Erweiterung dem Erweiterungspunkt zu, dem er dient, vergleichen eine kleine, eine mittlere und eine reine Einstellungserweiterung und sehen sich an, was ihr eigenes mysite_hello nach sechs Modulen enthält. 40 Minuten.
keywords: entwickler, erweiterung, aufbau, ordnerstruktur, settings, classes, autoloads, modules, design, datatypes, eventtypes, cronjobs, bin/php, translations, share, reine einstellungserweiterung, mysite_hello, developers course, extension, folder structure, settings-only extension, anatomy
type: help
---
# Lektion 7.1: Der Aufbau einer Erweiterung

Sechs Module lang haben die Teilnehmenden `mysite_hello` Stück für Stück aufgebaut. Diese Lektion tritt einen
Schritt zurück und betrachtet eine Erweiterung als Ganzes: in welche Ordner der Kernel schaut, welche Einstellung ihn
dazu bringt und wie sich kleine und große Erweiterungen unterscheiden. Sie ist die Landkarte für den Rest von Modul 7.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 7: Erweiterungen](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Modul 6](../modul-06-module-und-views/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Standardordner einer Erweiterung nennen und sagen, was der Kernel in jedem findet;
- die Einstellung nennen, die den Kernel in jeden Ordner schauen lässt;
- eine reine Einstellungserweiterung, eine reine Design-Erweiterung und eine Code-Erweiterung unterscheiden;
- erklären, warum eine Erweiterung auf der Festplatte nichts tut, bis sie eingeschaltet wird.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Was eine Erweiterung ist](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md).

1. **Die Ordner** (6 Minuten). Projizieren Sie die Tabelle und verbinden Sie jede Zeile mit einer Lektion dieses
   Kurses:

   | Ordner oder Datei | Was er enthält | Wie der Kernel ihn findet | Lektion |
   |---|---|---|---|
   | `ezinfo.php`, `extension.xml` | Name, Version, Lizenz | Bei aktiven Erweiterungen immer gelesen | 7.2 |
   | `settings/` | Dateien `.ini.append.php` | Aktive Erweiterung | 3.2, 8.4 |
   | `classes/` | PHP-Klassen | Das Autoload-Array | 7.3 |
   | `autoloads/` | Registrierungen von Template-Operatoren | `[TemplateSettings] ExtensionAutoloadPath[]` | 7.5 |
   | `modules/<name>/` | Module und Views | `module.ini [ModuleSettings] ExtensionRepositories[]` | 5.4, 6.2 |
   | `design/<design>/` | Templates, Stylesheets, Bilder | `design.ini [ExtensionSettings] DesignExtensions[]` | 3.1 |
   | `datatypes/<name>/` | Datentypen | `content.ini [DataTypeSettings] ExtensionDirectories[]` | 4.4 |
   | `eventtypes/event/<name>/` | Workflow-Ereignistypen | `workflow.ini` | 10.2 |
   | `cronjobs/` | Cronjob-Teile | `cronjob.ini` | 10.3 |
   | `bin/php/` | Kommandozeilenskripte | Die Konsole findet sie als `ext:<ext>:<name>` | 9.2 |
   | `translations/<locale>/` | Übersetzungen | Aktive Erweiterung | |
   | `share/`, `sql/` | Datenbankschema und -daten, Pakete | Installationsskripte | |

2. **Von klein bis groß** (4 Minuten). `str_replace` (ein Operator, zwei PHP-Dateien und eine Einstellungsdatei),
   `eztags` (ein Datentyp, Module, Templates, Fetch-Funktionen, Tabellen), `explayouts` (ein Seitenbaukasten). Eine
   reine Einstellungserweiterung ist der sicherste Weg, die Konfiguration einer Website unter Versionskontrolle zu
   halten.
3. **Der Schalter** (3 Minuten). `ActiveExtensions[]` für die ganze Installation, `ActiveAccessExtensions[]` für
   einen Siteaccess. Lektion 7.3 geht tiefer darauf ein.
4. **Was eine Erweiterung nicht ist** (2 Minuten). Keine Änderung in `kernel/`, keine Composer-Bibliothek (auch wenn
   Composer sie mit dem Pakettyp `ezpublish-legacy-extension` installieren kann), kein Inhalt.

### Häufige Fragen

**Kann eine Erweiterung eine Kernel-Klasse ersetzen?** Als letzter Ausweg über den Override-Autoload-Pfad; der
RAD-Assistent **Kernel-Override** schreibt ihn. Ziehen Sie Einstellungen, Ereignisse und Overrides vor.

**Ist der Ordnername wichtig?** Er ist überall der Name der Erweiterung: in Einstellungen, im `extra`-Block von
Composer, in der Liste der Erweiterungen.

**Machen Erweiterungen eine Website langsamer?** Jede aktive Erweiterung fügt Ordner hinzu, die der Kernel nach
Einstellungen und Templates durchsucht; inaktive Erweiterungen auf der Festplatte kosten nichts.

### Stolperfallen

- **Annehmen, eine Erweiterung sei sicher.** Sie läuft mit den Rechten des Kernels. Lesen Sie [Eine alte Erweiterung bewerten](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/eine-alte-erweiterung-bewerten.md),
  bevor Sie eine unbekannte installieren.
- **Ordner, die der Kernel nie liest.** Ein Ordner mit einem selbst erfundenen Namen wird ignoriert, sofern keine
  Einstellung auf ihn verweist.

## Praktische Übung

Auf der Entwicklungsinstallation.

1. Listen Sie Ihre Erweiterung auf: `find extension/mysite_hello -type f | sort`.
2. Schreiben Sie neben jede Datei die Lektion, in der sie entstanden ist, und die Einstellung, mit der der Kernel sie
   verwendet.
3. Vergleichen Sie mit einer kleinen Erweiterung: `find extension/str_replace -type f | sort` (falls installiert).
4. Vergleichen Sie mit einer größeren: `ls extension/eztags/` (falls installiert), und ordnen Sie ihre Ordner der
   Tabelle zu.
5. Zählen Sie die Erweiterungen auf der Festplatte und die aktiven:

   ```bash
   ls extension/ | wc -l
   php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions
   ```

6. Suchen Sie die Erweiterungen, die nur für einen Siteaccess aktiv sind:
   `php bin/php/console exp:ini where site.ini/ExtensionSettings/ActiveAccessExtensions <sa>`.
7. Prüfen Sie im Git-Repository der Erweiterung, dass jede Datei committet ist: `git -C extension/mysite_hello status`.

## Erwartetes Ergebnis

- Schritt 2: eine Tabelle, in der jede Datei von `mysite_hello` einer Lektion und einer Einstellung zugeordnet ist.
- Schritt 5: zwei verschiedene Zahlen; die Differenz sind die installierten, aber inaktiven Erweiterungen.
- Schritt 7: `nothing to commit, working tree clean`.
- Der Trainer prüft die Tabellen aus Schritt 2 von zwei Personen.

## Quiz

1. Welche Einstellung lässt den Kernel in einer Erweiterung nach Modulen suchen?
2. Welcher Ordner enthält die Registrierungen von Template-Operatoren?
   A. `classes/` B. `autoloads/` C. `modules/` D. `settings/`
3. Was ist der Unterschied zwischen `ActiveExtensions[]` und `ActiveAccessExtensions[]`?
4. Nennen Sie einen Vorteil einer reinen Einstellungserweiterung.
5. Richtig oder falsch: Eine Erweiterung auf der Festplatte wird verwendet, sobald es sie gibt.

<details>
<summary>Antworten</summary>

1. `module.ini [ModuleSettings] ExtensionRepositories[]` (mit dem Modul in `ModuleList[]`).
2. **B.** `autoloads/eztemplateautoload.php`.
3. Die erste aktiviert eine Erweiterung für die ganze Installation, die zweite für einen Siteaccess.
4. Die Konfiguration der Website steht unter Versionskontrolle und lässt sich wie Code deployen.
5. **Falsch.** Sie tut nichts, bis sie eingeschaltet wird.

</details>

## Weiterführende Literatur

- [Was eine Erweiterung ist](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md).
- [Die Ordnerstruktur einer Theme-Erweiterung](../../../designer/erweiterungen/ordnerstruktur-einer-theme-erweiterung.md).
- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../../../anleitungen/extensions.md).

## Verwandte Seiten

- [Modul 7: Erweiterungen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
