---
title: "Lektion 8.3: Einstellungen mit exp:ini ändern"
audience: trainer, developer
category: training
order: 83
summary: Die Teilnehmenden verwenden die Aktionen von exp:ini (get, set, add, rem, clear, toggle, copy, move), sehen sich jede Änderung vorher mit --dry-run an, finden die Sicherung, lesen Exit-Codes und verschieben einen Block von Einstellungen aus dem globalen Override in ihre Erweiterung. 50 Minuten.
keywords: entwickler, exp:ini, ini.php, get, set, add, rem, clear, toggle, copy, move, move-all, dry-run, probelauf, sicherung, var/backup/ini, exit-codes, json, show-secrets, create-extension, audit-protokoll, einstellungen ändern, developers course, changing settings, backup, exit codes, audit
type: help
---
# Lektion 8.3: Einstellungen mit exp:ini ändern

`exp:ini` liest und ändert Einstellungen in jedem Geltungsbereich, Zeile für Zeile, wobei Kommentare und Reihenfolge
erhalten bleiben, mit einer Sicherung und einem Eintrag im Audit-Protokoll für jeden Schreibvorgang. Es ist der
sichere Ersatz dafür, Einstellungsdateien von Hand oder mit `sed` zu bearbeiten. In dieser Lektion verwenden die
Teilnehmenden seine Aktionen, sehen sich Änderungen vorher an und verschieben Einstellungen, die sich im globalen
Override angesammelt haben, in ihre Erweiterung, wo sie mit dem Code mitwandern.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 8: Einstellungen und Siteaccesses](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Administratoren |
| Voraussetzungen | [Lektion 8.2](siteaccess-einstellungen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `get`, `set`, `add`, `rem`, `clear`, `toggle` und `copy` mit dem richtigen Geltungsbereich verwenden;
- jede Änderung mit `--dry-run` vorab ansehen und die Sicherung einer geschriebenen Datei finden;
- den Exit-Code eines Befehls für Skripte auswerten;
- einen Block von Einstellungen mit `move` in eine Erweiterung verschieben;
- erklären, was `exp:ini` mit Geheimnissen und mit dem INI-Cache macht.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Einstellungen auf der Kommandozeile ändern: exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md) und die
vollständige Referenz [Konsole exp:ini](../../../../bc/6.0/console-exp-ini.md).

1. **Fünf Befehle für den Alltag** (4 Minuten). `set`, `get`, `toggle`, `where`, `rem`. Eine Einstellung wird als
   `<file>/<Block>/<Variable>` angegeben; `.ini` ist optional; Arrays enden auf `[]`, Hash-Einträge lauten
   `Variable[key]`.
2. **Sicherheit** (3 Minuten). Jede geänderte Datei wird zuerst nach `var/backup/ini/<timestamp>/` kopiert (Modus
   0600); Schreibvorgänge sind atomar und werden abgewiesen, wenn sich die Datei inzwischen geändert hat (Exit-Code
   4); jeder Schreibvorgang wird im Audit-Protokoll als `system.setting.write` festgehalten; nach dem Schreiben wird
   der INI-Cache-Tag geleert.
3. **Geheimnisse** (2 Minuten). Variablen mit Namen wie Password, Secret, Token, ApiKey und ähnlichen werden als
   `********` angezeigt; `--show-secrets` zeigt sie an.
4. **Exit-Codes** (2 Minuten). 0 erledigt oder nichts zu ändern, 1 Bedienungsfehler, 2 nicht gefunden, 3
   abgewiesen, 4 Schreiben fehlgeschlagen. Skripte können sich darauf verlassen.
5. **In eine Erweiterung verschieben** (4 Minuten). `exp:ini move <file>/<Block> <from> <to>` schreibt zuerst das
   Ziel und entfernt dann den Block aus der Quelle; `--dry-run` zeigt beide Diffs; `--create-extension` legt für das
   Verschieben eine neue Erweiterung an.

### Häufige Fragen

**Ist `exp:ini` dasselbe wie `php bin/php/ini.php`?** Ja; die Konsole führt dieses Skript aus.

**Warum zeigt sich eine Änderung unter Velocity nicht?** Die Worker von Velocity behalten die Einstellungen aus ihrem
Warm-up; sie sehen die Änderung nach `exp:velocity restart` (Lektion 13.2).

**Kann ich eine eigene Aktion hinzufügen?** Ja: Aktionen und Anbieter von Geltungsbereichen sind Klassen, die in
`settings/ini.ini [IniCommandSettings]` registriert werden.

### Stolperfallen

- **Als root ausführen.** `exp:ini` behält Eigentümer und Gruppe geänderter Dateien bei, aber eine neue Datei, die
  als root angelegt wird, kann die Website lahmlegen. Arbeiten Sie als Benutzer der Website.
- **Den Geltungsbereich vergessen.** `set` braucht einen Geltungsbereich; `get` ohne einen zeigt den wirksamen Wert.
- **In den Standard-Geltungsbereich schreiben.** `settings/<file>.ini` wird ohne `--allow-default` abgewiesen;
  verwenden Sie es nicht.

## Praktische Übung

Auf der Entwicklungsinstallation.

1. Listen Sie die Aktionen auf: `php bin/php/console exp:ini actions`.
2. Setzen Sie den Namen der Website für Ihren öffentlichen Siteaccess und sehen Sie sich die Änderung zuerst an:

   ```bash
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Developer course" siteaccess:<sa> --dry-run
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Developer course" siteaccess:<sa>
   ```

3. Suchen Sie die Sicherung: `ls var/backup/ini/`.
4. Schalten Sie den View-Cache global um und wieder zurück:
   `php bin/php/console exp:ini toggle site.ini/ContentSettings/ViewCaching global` (führen Sie es zweimal aus).
5. Fügen Sie einen Array-Wert hinzu und entfernen Sie ihn:

   ```bash
   php bin/php/console exp:ini add site.ini/DebugSettings/DebugIPList[] 127.0.0.1 global
   php bin/php/console exp:ini add site.ini/DebugSettings/DebugIPList[] 127.0.0.1 global
   php bin/php/console exp:ini rem site.ini/DebugSettings/DebugIPList[] 127.0.0.1 global
   ```

6. Fragen Sie nach einer Einstellung, die niemand setzt, und lesen Sie den Exit-Code:
   `php bin/php/console exp:ini get site.ini/MySettings/Nothing; echo "exit $?"`.
7. Verschieben Sie den Block `[TemplateSettings]` von `site.ini` aus dem globalen Override in Ihre Erweiterung und
   sehen Sie es sich zuerst an:

   ```bash
   php bin/php/console exp:ini move site.ini/TemplateSettings global extension:mysite_hello --dry-run
   ```

   Lesen Sie beide Diffs. Führen Sie den Befehl nur dann ohne `--dry-run` aus, wenn der Block
   Entwicklungseinstellungen enthält, die Sie in der Erweiterung haben möchten; andernfalls hören Sie hier auf.
8. Lesen Sie die JSON-Form eines Befehls: `php bin/php/console exp:ini get site.ini/SiteSettings/SiteName --json`.

## Erwartetes Ergebnis

- Schritt 2: Der Probelauf gibt einen Diff und "nothing written" aus; der echte Lauf nennt die geschriebene Datei.
- Schritt 3: ein Ordner mit Zeitstempel und der Kopie der geänderten Datei.
- Schritt 5: Das zweite `add` meldet "Nothing to change"; `rem` entfernt den Wert.
- Schritt 6: `exit 2`.
- Schritt 7: zwei Diffs, die Zieldatei in der Erweiterung und der globale Override.
- Der Trainer fragt alle Teilnehmenden, was Exit-Code 3 bedeutet.

## Quiz

1. Welche Option zeigt eine Änderung, ohne sie zu schreiben?
2. Welcher Exit-Code bedeutet „abgewiesen“?
   A. 1 B. 2 C. 3 D. 4
3. Wo liegt die Sicherung einer Datei, die `exp:ini` geändert hat?
4. Was schreibt `exp:ini move` zuerst: das Ziel oder die Quelle?
5. Wie zeigen `get`, `list` und `where` Passwörter an?

<details>
<summary>Antworten</summary>

1. `--dry-run`.
2. **C.** 3: zum Beispiel beim Standard-Geltungsbereich, einem unbekannten Siteaccess oder einem Verschieben, das
   einen wirksamen Wert ändern würde.
3. In `var/backup/ini/<timestamp>/<path>`.
4. Das Ziel; danach verlässt der Block die Quelle.
5. Als `********`, sofern nicht `--show-secrets` angegeben ist.

</details>

## Weiterführende Literatur

- [Einstellungen auf der Kommandozeile ändern: exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md).
- [Konsole exp:ini](../../../../bc/6.0/console-exp-ini.md): jede Aktion, Option und jeder Exit-Code, mit Ausgabe.
- [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md): wo Schreibvorgänge in Einstellungen festgehalten werden.

## Verwandte Seiten

- [Modul 8: Einstellungen und Siteaccesses](README.md)
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
