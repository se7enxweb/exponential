# Eine beschädigte Installation im Browser reparieren

Diese Seite ist für Administratoren, deren Website nicht startet, weil die Composer-Bibliotheken (`vendor/`) fehlen. Bis
6.0.15 sah ein Besucher dann die rohe Ausgabe von PHP. Jetzt zeigt Exponential eine eigene Seite, die erklärt, was nicht
stimmt, und einen Administrator die Installation ohne Shell-Sitzung reparieren lässt. Hinzugefügt am 02.10.2026.
Vollständige Anleitung: [Reparatur](../../../eng-us/bc/6.0/repair.md).

## In drei Minuten reparieren

1. Legen Sie auf dem Server als Benutzer des Webservers einen Reparaturschlüssel an:

   ```bash
   sudo -u <web server user> php bin/php/exprepair.php --create-key
   ```

   Der Schlüssel wird einmal ausgegeben und funktioniert einmal. Nur sein Hash wird gespeichert. Der Befehl läuft ohne
   die Bibliotheken, funktioniert also, während die Website nicht erreichbar ist.
2. Öffnen Sie eine beliebige Seite der Website. Der Cursor steht bereits im Schlüsselfeld: Fügen Sie den Schlüssel ein
   und drücken Sie die Eingabetaste.
3. Beobachten Sie die Statusleiste. Sie zeigt drei Schritte, jeder wartend, laufend, fertig oder fehlgeschlagen, mit
   einem Fortschrittsbalken und dem Ende des Protokolls, alle zwei Sekunden aktualisiert. Wenn alle fertig sind, öffnet
   sich die Seite nach fünf Sekunden neu.

| Schritt | Was läuft |
|---|---|
| Die Composer-Bibliotheken installieren | `composer install --no-dev --no-interaction --no-plugins --no-scripts --no-progress` |
| Die Autoload-Arrays neu erzeugen | `php bin/php/ezpgenerateautoloads.php -e`, dann die Kernel-Autoloads (`ezpgenerateautoloads.php -k` mit einem `--exclude` für das Worktree-Verzeichnis der Installation) |
| Die Caches leeren | `php bin/php/ezcache.php --clear-all` |

Passt `composer.lock` nicht zu `composer.json`, erklärt die Seite das und bietet **Lock-Datei aktualisieren und
installieren** an, 15 Minuten lang autorisiert durch das Token des fehlgeschlagenen Laufs.

Die Reparatur führt `composer install` für Sie aus; Sie müssen Composer nicht selbst ausführen.

## Prüfen und abschalten

```bash
php bin/php/exprepair.php --status    # state of the last repair and the end of its log; safe at any time
php bin/php/exprepair.php --disable   # writes Enabled=false and an empty KeyHash
```

## Warum es sicher ist

- Die Warteschlange läuft als Benutzer des Webservers, nie als root.
- Der Schlüssel wird nur als Hash gespeichert, in `settings/override/exprepair.ini.append.php` (Modus 0640). Committen
  Sie diese Datei nie.
- Der Worker ist abgekoppelt, sodass die Seite, die eine Reparatur startet, sofort antwortet.
- `index.php` beantwortet den Status des Laufs, bevor der Kernel startet, denn der Kernel kann ohne seine Bibliotheken
  nicht starten.
- Reparatureinstellungen werden im [Prüfprotokoll](pruefprotokoll.md) erfasst: ob ein Schlüssel gesetzt ist, nie der
  Schlüssel oder sein Hash.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Enabled` | `true`, sobald ein Schlüssel existiert | Installation |
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `KeyHash` | geschrieben von `--create-key` | Installation |
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Composer` | `composer` im PATH; setzen Sie ihn, wenn das Programm anderswo liegt | Installation |
| `settings/error.ini` | `ErrorSettings` | `StaticErrorPage[dependencies]` | auskommentiert; die Seite, die er nennen würde, ist `design/standard/errors/dependencies.html` | Installation |

## Verwandte Seiten

- [Wartungsmodus](wartungsmodus.md)
- [Dateikonsistenzprüfung](dateikonsistenzpruefung.md)
- [Velocity und der Opcode-Cache](velocity-opcode-cache-und-profil.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../../eng-us/history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
