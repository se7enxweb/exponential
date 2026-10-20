---
title: Konsolenbefehle für Entwickler
audience: developer
category: extension-development
order: 310
summary: Die ./console-Befehle, die Erweiterungsentwickler am häufigsten verwenden - Einstellungen, Caches, Autoloads, Klassen- und Template-Prüfungen, Hilfe, Logs, Inhaltsaufträge - und wie die Skripte einer Erweiterung als ext:-Befehle erscheinen.
keywords: Konsole, Befehle, Kommandozeile, Skripte, Entwickler, console, ./console, exp:ini, exp:cache, exp:ezpgenerateautoloads, exp:checkclasses, exp:eztemplatecheck, exp:exphelp, exp:explogs, exp:ezexec, exp:systeminfo, ext: commands, bin/php
type: help
---
# Konsolenbefehle für Entwickler

Teil 8, Referenz 27. `./console` im Wurzelverzeichnis der Installation führt jedes Kommandozeilenskript des Kernels
und der aktiven Erweiterungen unter einem Namen aus. `./console list` zeigt sie alle (etwa 300 auf einer Installation
mit vielen Erweiterungen); `./console help <command>` zeigt die Optionen eines Befehls. Die Skripte sind auch direkt als
`php bin/php/<script>.php` erreichbar. Wie eine Erweiterung eigene Befehle hinzufügt, steht in
[CLI-Skripte und Konsolenbefehle](../teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md).

## Die Befehle für jeden Tag

| Befehl | Tut |
|---|---|
| `exp:ini` | Einstellungen in jedem Geltungsbereich lesen und ändern: `get`, `set`, `add`, `rem`, `clear`, `toggle` ([Der Befehl exp:ini](../teil-2-builder/der-befehl-exp-ini.md)) |
| `exp:cache` | Caches nach Tag, ID oder vollständig leeren und untersuchen ([Cache-Steuerung von der Konsole](../../../../eng-us/bc/6.0/cache-console.md)) |
| `exp:ezcache` | das klassische Cache-Skript: `--clear-tag`, `--clear-id`, `--clear-all`, `--list-tags` |
| `exp:ezpgenerateautoloads` | die Autoload-Arrays neu erzeugen; `-e` für Erweiterungen, nachdem Sie eine Klasse hinzugefügt oder umbenannt haben |
| `exp:checkclasses` | jede deklarierte Klasse laden und die melden, die PHP nicht laden will: findet Signaturfehler sofort |
| `exp:eztemplatecheck` | die Syntax der Templates eines Siteaccess oder eines Design-Verzeichnisses prüfen |
| `exp:ezexec` | eine PHP-Datei mit geladenem Kernel ausführen: `./console exp:ezexec path/to/script.php` |
| `exp:systeminfo` | die Fakten und Zustandsprüfungen der Seite mit den Systeminformationen, als Text oder JSON |

## Hilfe, Logs und Inhalte

| Befehl | Tut |
|---|---|
| `exp:exphelp` | das Hilfesystem: den Index aufbauen, suchen, Links prüfen (`check`) ([Das Hilfesystem für Erweiterungen](hilfesystem-fuer-erweiterungen.md)) |
| `exp:explogs` | die Logdateien auflisten, verfolgen und durchsuchen |
| `exp:audit` | das Audit-Log: verfolgen, ein Ereignis anzeigen, prüfen |
| `exp:expcontentjob` | Inhaltsaufträge ausführen, auflisten, fortsetzen und abbrechen (große Lösch- und Kopiervorgänge in Stapeln) |
| `exp:ezsubtreecopy`, `exp:ezsubtreeremove` | Teilbäume kopieren oder entfernen |
| `exp:updatesearchindex` | alle Inhalte neu indizieren |
| `exp:ezcsvimport`, `exp:ezcsvexport` | Inhalte aus und nach CSV |

## Datenbank und Schema

| Befehl | Tut |
|---|---|
| `exp:ezsqldumpschema` | das Datenbankschema in eine Datei ausgeben (`.dba` oder SQL) |
| `exp:ezsqlinsertschema` | ein Schema und Daten aus einer `.dba`-Datei einspielen |
| `exp:ezsqldiff` | die Unterschiede zwischen zwei Schemas |
| `exp:checkdbfiles` | die Datenbank-Update-Dateien gegen den Upgrade-Pfad prüfen |

Der Schema-Ablauf Ihrer Erweiterung steht in [Datenbankschema und .dba-Dateien](../teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md).

## Qualität

| Befehl | Tut |
|---|---|
| `exp:checkcodestyle` | geänderte PHP-Dateien gegen den Codestil prüfen |
| `exp:checkmanifest` | `share/filelist.md5` gegen die Dateien prüfen |
| `exp:ezcheckphptag` | Zeichen vor `<?php` oder nach `?>` (die die Header zerstören) |

## Die Befehle Ihrer Erweiterung

Jedes Skript im `bin/php/` einer aktiven Erweiterung (und deren Befehlsklassen) erscheint als
`ext:<extension>:<script>`, zum Beispiel `ext:cjw_newsletter:status`. Die erste Zeile des Docblocks des Skripts
(`@description`) ist der Text, den `./console list` anzeigt.

## Befehle ausführen

- Führen Sie sie als Benutzer des Webservers aus, damit die Dateien, die sie schreiben, ihm gehören. Als root
  verweigern sie den Dienst, sofern nicht `--allow-root-user` angegeben ist.
- `-s <siteaccess>` führt einen Befehl mit den Einstellungen eines Siteaccess aus.
- Die meisten Befehle, die Daten ändern, haben `--dry-run`; verwenden Sie es zuerst.

**Demomodus und die Konsole.** Mit `EXP_DEMO_MODE=1` in der Shell gibt jeder Befehl `Demo mode is on (EXP_DEMO_MODE=1) ...` auf der Standardfehlerausgabe aus und protokolliert einmal `demo/mode ... sapi=cli`; `exp:resetuserpassword` verweigert Administratoren, und `exp:install` sowie der Kickstarter behalten ein allgemein bekanntes oder kurzes Administratorpasswort bei. [Demomodus für Entwickler](demomodus-fuer-entwickler.md).

## Verwandte Seiten

- Zurück: [INI-Dateien, denen ein Erweiterungsautor begegnet](ini-dateien-fuer-erweiterungsautoren.md)
- Weiter: [Verzeichnisse unter var/](verzeichnisse-unter-var.md)
- [eZScript und eZCLI](ezscript-und-ezcli.md)
- [Konsole](../../../../eng-us/bc/6.0/console.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
