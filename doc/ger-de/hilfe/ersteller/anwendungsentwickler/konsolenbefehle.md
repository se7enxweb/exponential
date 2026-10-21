---
title: Konsolenbefehle
audience: app-creator, developer, admin
category: app-creation
order: 300
summary: Der Einstiegspunkt bin/php/console - Befehlsnamen und Namensräume, automatisch gefundene Befehle von Erweiterungen, die Beschreibung eigener Befehle, Optionen und Exit-Codes und die Befehle, die ein Anwendungsentwickler täglich verwendet.
keywords: Konsole, Befehle, Befehle von Erweiterungen, Beschreibung, Exit-Code, Anwendungsentwickler, console, bin/php/console, commands, exp:, ext:, cron:, shell:, extension commands, description, exit code, exp:velocity, exp:ini, exp:ezcache, app creator
type: help
---
# Konsolenbefehle

Diese Seite ist für Anwendungsentwickler, die Kommandozeilenwerkzeuge ausführen und hinzufügen. `bin/php/console` ist
der eine Einstiegspunkt für jedes Skript von Exponential: Kernel-Skripte, Shell-Skripte, Skripte von Erweiterungen und
Cronjob-Teile ([Exponential Console](../../../bc/6.0/konsole.md)).

## Inhalt

1. [Befehle finden](#befehle-finden)
2. [Befehlsnamen](#befehlsnamen)
3. [Die Befehle Ihrer Erweiterung](#die-befehle-ihrer-erweiterung)
4. [Optionen und Exit-Codes](#optionen-und-exit-codes)
5. [Befehle für den täglichen Gebrauch](#befehle-für-den-täglichen-gebrauch)

## Befehle finden

```bash
php bin/php/console                   # every command
php bin/php/console list ext:expdemo  # one namespace
php bin/php/console --version
```

## Befehlsnamen

| Namensraum | Führt aus | Beispiel |
|---|---|---|
| `exp:<name>` | `bin/php/<name>.php` | `exp:ezcache` |
| `shell:<name>` | `bin/shell/<name>.sh` | |
| `bin:<name>` | `bin/<name>.sh` oder `.php` | |
| `ext:<ext>:<name>` | `extension/<ext>/bin/php/<name>.php` oder `bin/<name>.sh` | `ext:expqueue:status` |
| `ext:<ext>:sh:<name>` | `extension/<ext>/bin/shell/<name>.sh` | |
| `cron:<part>` | einen Cronjob-Teil | `cron:frequent` |

Ohne Namensraum versucht die Konsole `exp:`, dann `shell:`, dann `bin:`.

## Die Befehle Ihrer Erweiterung

Legen Sie das Skript unter `extension/expdemo/bin/php/report.php` ab; die Konsole findet es als `ext:expdemo:report`,
ohne dass es registriert werden muss. Geben Sie ihm im Docblock ein Tag `@description`, damit `list` es beschreibt.
Schreiben Sie die Logik als Klasse ([CLI-Skripte mit eZScript](cli-skripte.md)).

## Optionen und Exit-Codes

- Alles nach dem Befehlsnamen geht unverändert an das Skript; nur `--quiet`, `--version` und `--help` für sich allein
  gehören der Konsole.
- Skripte verweigern die Ausführung als root, sofern nicht `--allow-root-user` angegeben ist; führen Sie sie als
  Benutzer des Webservers aus.
- Der Exit-Code des Skripts wird unverändert zurückgegeben: 0 bei Erfolg, alles andere bei einem Fehlschlag, damit
  Befehle in der CI und in Shell-Pipelines funktionieren. Halten Sie sich bei Ihren Befehlen daran.

## Befehle für den täglichen Gebrauch

| Befehl | Tut |
|---|---|
| `exp:ezpgenerateautoloads -e` | Autoloads der Erweiterungen neu erzeugen |
| `exp:ezcache --clear-tag=ini` | Caches nach Tag oder ID leeren |
| `exp:cache status` | jede Cache-Schicht, PASS oder FAIL |
| `exp:ini get/set/where` | Einstellungen lesen und ändern ([exp:ini](../../../funktionen/6.0/exp-ini-befehl.md)) |
| `exp:velocity deploy` | Autoloads, Caches, PHP-Reload, Neustart von Velocity, in dieser Reihenfolge |
| `exp:velocity status`, `restart`, `cache clear` | der Anwendungsserver |
| `exp:help check`, `exp:help search` | Linkprüfung und Suche der Hilfe |
| `exp:benchmark` | die Website messen |
| `exp:maintenance on/off` | Wartungsmodus |
| `cron:<part>` | einen Cronjob-Teil ausführen |

Die erzeugten Seiten zu jedem Befehl mit seinen Optionen finden Sie in den Beispielen (`doc/eng-us/examples/commands/`).

## Verwandte Seiten

- [CLI-Skripte mit eZScript](cli-skripte.md)
- [Muster Command](../../../standards/muster/befehl.md)
- [Cache-Konsole](../../../bc/6.0/cache-konsole.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
