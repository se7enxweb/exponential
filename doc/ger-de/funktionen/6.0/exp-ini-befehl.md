# Einstellungen auf der Kommandozeile ändern: exp:ini

Diese Seite ist für Administratoren und Entwickler, die INI-Einstellungen ändern. Ein Befehl liest und ändert
Einstellungen in jedem Geltungsbereich: der globalen Überschreibung, einem Siteaccess, einer Extension, einem
Extension-Siteaccess. Er bearbeitet Dateien Zeile für Zeile, sodass Kommentare, Leerzeilen und die Reihenfolge bleiben,
wie Sie sie geschrieben haben (anders als `eZINI::save()`, das die ganze Datei neu schreibt). Hinzugefügt am
02.10.2026. Vollständige Referenz mit der Ausgabe jeder Aktion: [Konsole exp:ini](../../../eng-us/bc/6.0/console-exp-ini.md).

## Fünf Befehle, die den größten Teil eines Tages abdecken

```bash
./console exp:ini set site.ini/SiteSettings/SiteName "My site" global --allow-root-user
./console exp:ini get site.ini/SiteSettings/SiteName --allow-root-user
./console exp:ini toggle site.ini/ContentSettings/ViewCaching global --allow-root-user
./console exp:ini where cronjob.ini/CronjobPart-publishing/Scripts --allow-root-user
./console exp:ini rem site.ini/SiteSettings/SiteName siteaccess:admin --allow-root-user
```

- `php bin/php/ini.php ...` ist derselbe Befehl.
- `exp:ini --help` listet die Aktionen auf; `exp:ini help <action>` erklärt eine.
- Fügen Sie `--dry-run` hinzu, um zu sehen, was geschehen würde, ohne zu schreiben.

## Geltungsbereiche

| Geltungsbereich | Geschriebene Datei |
|---|---|
| `global` (oder `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>` oder `<sa>` | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`; abgelehnt außer mit `--allow-default` |

## Aktionen

| Aktion | Was sie tut |
|---|---|
| `get` | Einen Wert ausgeben |
| `set` | Einen Wert schreiben |
| `add` | Zu einem Array hinzufügen, ohne Duplikate |
| `rem` | Einen Wert entfernen |
| `clear` | Die Zurücksetzzeile `Variable[]` schreiben |
| `toggle` | enabled/disabled, true/false, yes/no, on/off, 1/0 umschalten und dabei Ihre Schreibweise beibehalten |
| `copy`, `move`, `move-all` | Einstellungen kopieren oder verschieben, zum Beispiel in eine Extension |
| `where` | Jede Datei, die eine Variable setzt, in Ladereihenfolge, und der geltende Wert; führt auch die Dateien inaktiver Extensions auf |
| `list`, `scopes`, `actions` | Einstellungen, Geltungsbereiche und Aktionen auflisten |

## Was er für die Sicherheit tut

- Eine Sicherung jeder geänderten Datei geht nach `var/backup/ini/<timestamp>/`.
- Das Speichern als Benutzer der Website behält die Gruppe der Datei bei (er schreibt an Ort und Stelle neu, wenn sich
  die Gruppe nicht setzen lässt).
- Schreibvorgänge an Einstellungen werden im [Prüfprotokoll](pruefprotokoll.md) als `system.setting.write` erfasst;
  Geheimnisse werden als `[secret]` erfasst.

## Erweitern

Aktionen und Anbieter von Geltungsbereichen sind Klassen, die in `settings/ini.ini` `[IniCommandSettings]` registriert
sind. **Setup > RAD** unter `/setup/radsurvey/(show)/inicommand` listet jede mit ihrem Eigentümer auf. Dieselbe
Editorklasse (`expIniEditor`) steht hinter dem Reiter für Einstellungen der [Debug-Leiste](exp-debug-leiste.md).

## Verwandte Seiten

- [Konsole](../../../eng-us/bc/6.0/console.md)
- [Platzierungen von INI-Überschreibungen](../../spezifikationen/6.0/ini-override-platzierungen.md)
- [Audit-Ereignismodell](../../spezifikationen/6.0/audit-ereignismodell.md)
- [eZINI erhält Kommentare beim Speichern](../../../eng-us/bc/6.0/eZINI_PRESERVES_COMMENTS.md)
- [INI-Überschreibungen für mehrere Websites](ini-ueberschreibungen-fuer-mehrere-websites.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md), [Oktober 2026](../../../eng-us/history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
