---
title: Bestandsaufnahme einer alten Erweiterung
audience: developer, app-creator
category: extension-development
order: 30
summary: Finden Sie heraus, was eine alte Erweiterung enthält und was Arbeit brauchen wird, bevor Sie eine Zeile ändern - Dateien, Einstellungen, PHP-Einstiegspunkte, JavaScript, Inline-Code, Datenbanktabellen, mit den Befehlen, die jedes davon auflisten.
keywords: Bestandsaufnahme, alte Erweiterung, Prüfung, Einstellungen, Module, Operatoren, Datentypen, Inline-Skripte, Datenbanktabellen, inventory, old extension, audit, grep, php -l, settings, modules, operators, datatypes, inline scripts, YUI, jQuery, database tables
type: help
---
# Bestandsaufnahme einer alten Erweiterung

Teil 4, Kapitel 3. Bevor Sie etwas korrigieren, finden Sie heraus, was Sie haben. Dieses Kapitel gibt Ihnen eine Reihe
von Befehlen, von denen jeder eine Frage zu einer Erweiterung beantwortet, und eine Tabelle, in die Sie die Antworten
eintragen. Führen Sie sie im Wurzelverzeichnis Ihrer Entwicklungsinstallation aus, mit der Erweiterung in
`extension/<name>/`. In den Beispielen heißt die Erweiterung `myoldext`; verwenden Sie Ihren eigenen Namen.

## 1. Was ist darin?

```bash
cd extension/myoldext
find . -type f -not -path './.git/*' | sed 's|/[^/]*$||' | sort | uniq -c
```

Das gibt jeden Ordner aus und wie viele Dateien er enthält. Vergleichen Sie die Ordnernamen mit
[Der Verzeichnisaufbau einer Erweiterung](../teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md).
Ordner, die der Kernel nicht kennt (`lib/`, `src/`, `vendor/`), gehören zum eigenen Code der Erweiterung; Ordner wie
`kernel/` oder `lib/ezutils/` innerhalb einer Erweiterung bedeuten meist, dass sie Kernel-Dateien ersetzt, und das
ist die schwierigste Art von Upgrade ([Kernel-Overrides: das letzte Mittel](../teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md)).

## 2. Wer hat sie gemacht, welche Version, welche Lizenz?

```bash
cat ezinfo.php extension.xml composer.json LICENSE* COPYING* 2>/dev/null | head -80
git log --format='%h %ad %an %s' --date=short | tail -5
git log --format='%h %ad %an %s' --date=short | head -5
```

Notieren Sie vor allem anderen die Lizenz und die Autoren. Wenn es keine Lizenzdatei und keine Lizenzzeile gibt,
dürfen Sie Ihre Änderungen nicht veröffentlichen; fragen Sie zuerst den Autor
([Lizenzen lesen, bevor Sie etwas wiederverwenden](../teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)).

## 3. Wo schließt sie sich an den Kernel an?

Jeder Erweiterungspunkt wird durch eine Einstellung registriert. Wenn Sie die Einstellungsdateien und ihre Abschnitte
auflisten, sehen Sie, welche Mechanismen sie verwendet:

```bash
ls settings/
grep -h '^\[' settings/*.ini* | sort | uniq -c
grep -rhn 'ExtensionDirectories\|DesignExtensions\|ModuleList\|ExtensionRepositories\|AvailableDataTypes\|AvailableEventTypes' settings/
```

Dann die Dateien mit festen Namen:

```bash
ls modules/*/module.php autoloads/eztemplateautoload.php cronjobs/*.php bin/php/*.php 2>/dev/null
```

Füllen Sie diese Tabelle aus:

| Mechanismus | Gefunden in | Anzahl |
|---|---|---|
| Module und Views | `modules/*/module.php` | |
| Template-Operatoren | `autoloads/eztemplateautoload.php` | |
| Fetch-Funktionen | `modules/*/function_definition.php` | |
| Datentypen | `settings/content.ini.append.php` `[DataTypeSettings]` | |
| Workflow-Ereignistypen | `settings/workflow.ini.append.php` `[EventSettings]` | |
| Cronjob-Teile | `cronjobs/*.php` und `settings/cronjob.ini.append.php` | |
| CLI-Skripte | `bin/php/*.php` | |
| Designs | `design/*/` und `settings/design.ini.append.php` | |
| Übersetzungen | `translations/*/translation.ts` | |
| Eigene Datenbanktabellen | `sql/*/` oder `share/db_schema.dba` | |

Jede Zeile hat ein Kapitel in [Teil 3](../teil-3-erweiterungsentwickler/README.md),
und Sie prüfen jede davon, wenn sie läuft.

## 4. Lässt sich das PHP parsen?

```bash
find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
```

Keine Ausgabe bedeutet, dass jede Datei mit dem PHP in Ihrem Pfad geparst werden kann. Führen Sie den Befehl mit
jeder PHP-Version, die Sie unterstützen, erneut aus; auf einem Server mit mehreren PHP-Builds hat jedes Binary seinen
eigenen Pfad (zum Beispiel `/usr/bin/php8.0` oder `/opt/<vendor>/php/8.0/bin/php`, je nachdem, wie PHP installiert
wurde).

> **Hinweis**
> `php -l` prüft nur die Syntax. Eine Datei kann sich parsen lassen und trotzdem zur Laufzeit abbrechen, zum Beispiel
> wenn sie eine entfernte Funktion aufruft. Die Schritte 5 und 6 finden solche Stellen.

## 5. Welches alte PHP verwendet sie?

Diese Suchen finden die Muster, die PHP 8 entfernt oder geändert hat. Jeder Treffer ist eine Zeile, die Sie sich
ansehen sollten, nicht immer eine Zeile, die Sie ändern müssen.

```bash
# removed functions
grep -rnE '\b(each|create_function|split|spliti|ereg|eregi|ereg_replace|eregi_replace|mysql_[a-z_]+|money_format|get_magic_quotes_gpc|get_magic_quotes_runtime)\s*\(' --include='*.php' .

# deprecated in 8.1 to 8.5
grep -rnE '\b(utf8_encode|utf8_decode|strftime|gmstrftime|curl_close|imagedestroy|finfo_close|xml_parser_free)\s*\(' --include='*.php' .
grep -rn 'FILTER_SANITIZE_STRING\|\$http_response_header' --include='*.php' .

# parameters typed with a class and defaulting to null, without a question mark
grep -rnE '\(\s*[A-Z][A-Za-z0-9_]+\s+\$[A-Za-z0-9_]+\s*=\s*null|,\s*[A-Z][A-Za-z0-9_]+\s+\$[A-Za-z0-9_]+\s*=\s*null' --include='*.php' .

# ezinfo.php with bare keys
grep -nE '^\s*(return array\(\s*)?(Name|Version|Copyright|License|Info_url|info_url)\s*=>' ezinfo.php
```

Für Konstruktoren alten Stils listen Sie jede Klasse auf und suchen nach einer Methode mit demselben Namen:

```bash
grep -rhoE '^\s*(abstract\s+|final\s+)?class\s+[A-Za-z0-9_]+' --include='*.php' . | awk '{print $NF}' | while read c; do
  grep -rlnE "function\s+$c\s*\(" --include='*.php' . | sed "s|^|$c: |"
done
```

Jede Zeile, die das ausgibt, ist eine Klasse, deren Konstruktor PHP 8 nicht mehr aufruft
([Konstruktoren alten Stils und statische Aufrufe](alte-konstruktoren-und-statische-aufrufe.md)).

## 6. Was tut sie im Browser?

```bash
# inline scripts and event handler attributes in templates
grep -rnE '<script[^>]*>[^<]|\son[a-z]+\s*=|javascript:' --include='*.tpl' design/

# inline styles
grep -rnE '<style|\sstyle\s*=' --include='*.tpl' design/

# old libraries
grep -rnE 'ezjsc::yui|YUI\(|YAHOO\.|jQuery\.browser|\.live\(|\.size\(\)|\.andSelf\(|jQuery\.camelCase' design/ settings/

# copies of libraries shipped with the extension
find design -name '*.js' | xargs -r grep -lE 'jQuery JavaScript Library v|YUI [0-9]' 2>/dev/null
```

Die ersten beiden Suchen finden das meiste von dem, was eine Content Security Policy ohne `'unsafe-inline'`
blockiert; die Browser-Konsole findet den Rest. Notieren Sie die Anzahl pro Template.

## 7. Hat sie eigene Tabellen?

```bash
ls sql/*/ share/*.dba 2>/dev/null
grep -rhoE "'name'\s*=>\s*'[a-z0-9_]+'" classes/ | sort -u | head
```

Die zweite Zeile findet die Tabellennamen in den `definition()`-Methoden von `eZPersistentObject`-Klassen. Alte
Erweiterungen liefern oft nur eine `sql/mysql/schema.sql` mit; Exponential 6 unterstützt mehrere Datenbank-Engines,
planen Sie also eine `.dba`-Datei ein ([Datenbankschema und .dba-Dateien](../teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md)).

## 8. Schreiben Sie es auf

Eine Bestandsaufnahme ist nur nützlich, wenn Sie sie aufbewahren. Kopieren Sie dies in ein Issue Ihres Repositorys:

```text
Extension: myoldext            License: GPL-2.0-or-later      Authors: (from ezinfo.php and git log)
Last release: 1.2 (2012)       PHP files: 41                  Templates: 23

Loads on PHP 8.0: no (2 parse errors)
Old constructors: 4 classes     Removed functions: each() x2, split() x1
Implicitly nullable parameters: 7
Inline scripts: 5 templates     Event attributes: 12           Inline styles: 9
Libraries: YUI 2 (calendar)     Own tables: 2 (MySQL only)
Kernel overrides: none

Estimate: steps 1-4 one day, steps 5-6 two days
```

## Was schiefgehen kann

| Symptom | Ursache | Was zu tun ist |
|---|---|---|
| `grep` findet Hunderte von Treffern in `vendor/` oder einer mitgelieferten Bibliothek | Sie haben Code von Dritten durchsucht | schließen Sie ihn aus; aktualisieren Sie die Bibliothek als Ganzes, statt sie zu patchen |
| `php -l` ist erfolgreich, aber die Administration zeigt eine leere Seite | ein fataler Fehler zur Laufzeit | sehen Sie in `var/log/error.log` und im PHP-Fehlerlog nach |
| Die Konstruktorsuche gibt eine Methode aus, die kein Konstruktor ist | eine Methode trägt zufällig den Namen einer Klasse, aber in einer anderen Klasse | prüfen Sie das von Hand; nur eine Methode in derselben Klasse zählt |

## Verwandte Seiten

- Zurück: [Die Upgrade-Checkliste](die-upgrade-checkliste.md)
- Weiter: [PHP-8-Fehler, die alten Code stoppen](php-8-fehler-die-alten-code-stoppen.md)
- [Eine alte Erweiterung bewerten](../teil-1-vom-benutzer-zum-entwickler/eine-alte-erweiterung-bewerten.md)
- [Den Code lesen, den Sie haben](../teil-1-vom-benutzer-zum-entwickler/den-vorhandenen-code-lesen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
