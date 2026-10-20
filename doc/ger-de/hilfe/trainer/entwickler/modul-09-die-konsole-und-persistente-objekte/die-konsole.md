---
title: "Lektion 9.1: Die Konsole"
audience: trainer, developer
category: training
order: 91
summary: Die Teilnehmenden finden jeden Befehl mit php bin/php/console list, lesen seine Hilfe, verstehen die Namensräume exp, shell, bin, ext und cron und führen Cronjob-Teile über die Konsole aus. 35 Minuten.
keywords: entwickler, konsole, bin/php/console, list, hilfe, namensräume, exp:, shell:, bin:, ext:, cron:, crontab:list, allow-root-user, @description, did you mean, befehle finden, developers course, console, help, namespaces
type: help
---
# Lektion 9.1: Die Konsole

Exponential hat mehr als hundert Kommandozeilenskripte. Die Konsole, `php bin/php/console`, findet sie alle, die des
Kernels und die jeder Erweiterung, beschreibt sie und führt sie über ihren Namen aus. In dieser Lektion finden sich
die Teilnehmenden darin zurecht, damit der eigene Befehl aus der nächsten Lektion neben denen des Kernels in der
Liste erscheint.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 9: Die Konsole und persistente Objekte](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Entwickler und Administratoren |
| Voraussetzungen | [Modul 1](../modul-01-die-entwicklungsinstallation/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- mit `php bin/php/console list` jeden Befehl oder die Befehle eines Namensraums auflisten;
- die Hilfe eines Befehls auf drei Arten lesen;
- die Namensräume `exp:`, `shell:`, `bin:`, `ext:<ext>:` und `cron:` erklären;
- einen Cronjob-Teil mit `cron:<part>` ausführen und die Crontab mit `crontab:list` anzeigen;
- `--allow-root-user` erklären und warum Befehle als Benutzer der Website laufen sollten.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Exponential Console](../../../../../eng-us/bc/6.0/console.md).

1. **`list`** (3 Minuten). `php bin/php/console list` gibt jeden Befehl mit einer einzeiligen Beschreibung aus,
   gruppiert nach Namensraum; `list exp`, `list shell`, `list cron`, `list ext:<ext>` filtern.
2. **Namen** (3 Minuten). Projizieren Sie die Tabelle:

   | Namensraum | Führt aus |
   |---|---|
   | `exp:<name>` | `bin/php/<name>.php` |
   | `shell:<name>` | `bin/shell/<name>.sh` |
   | `bin:<name>` | `bin/<name>.sh` oder `bin/<name>.php` |
   | `ext:<ext>:<name>` | `extension/<ext>/bin/php/<name>.php` oder `extension/<ext>/bin/<name>.sh` |
   | `cron:<part>` | einen Cronjob-Teil über `runcronjobs.php` |

   Ohne Namensraum versucht die Konsole `exp:`, dann `shell:`, dann `bin:`. Viele Befehle haben Aliasse, etwa
   `exp:logs` für `exp:explogs`.
3. **Hilfe** (2 Minuten). `help <command>`, `--help <command>` und `<command> --help` geben die eigene
   Bedienungsanleitung des Skripts aus.
4. **Root** (2 Minuten). Skripte verweigern ohne `--allow-root-user` die Ausführung als root; Dateien, die ein Skript
   als root schreibt, können eine Website lahmlegen, die als anderer Benutzer läuft.

### Häufige Fragen

**Ist `./console` dasselbe?** Ja, wenn Sie den Link `ln -s bin/php/console console` angelegt haben; er wird nicht
mitgeliefert.

**Woher kommen die Beschreibungen?** Aus einem Tag `@description` im Docblock des Skripts (Lektion 9.2).

**Kann ich geplante Aufgaben sehen?** `php bin/php/console crontab:list` gibt die System-Crontab des aktuellen
Benutzers aus; **Setup** > **Cronjobs** zeigt die Teile im Browser.

### Stolperfallen

- **Aus einem anderen Verzeichnis ausführen.** Die Konsole muss aus dem Wurzelverzeichnis der Installation laufen,
  sonst wird `autoload.php` nicht gefunden.
- **Befehle, die löschen.** Manche Befehle entfernen Daten (`exp:trashpurge`, `exp:cleanupversions`). Lesen Sie
  zuerst `--help` und verwenden Sie `--dry-run`, wo es angeboten wird.
- **Tippfehler.** Ein unbekannter Name gibt "Did you mean:" mit dem ähnlichsten Treffer aus.

## Praktische Übung

Auf der Entwicklungsinstallation, aus ihrem Wurzelverzeichnis.

1. Listen Sie jeden Befehl auf und zählen Sie sie: `php bin/php/console list | tail -3`.
2. Listen Sie die Cronjob-Teile auf: `php bin/php/console list cron`.
3. Listen Sie die Shell-Befehle auf: `php bin/php/console list shell`.
4. Lesen Sie die Hilfe des Cache-Befehls auf zwei Arten: `php bin/php/console help exp:ezcache` und
   `php bin/php/console exp:ezcache --help`.
5. Vertippen Sie sich absichtlich bei einem Namen: `php bin/php/console exp:ezcach`. Lesen Sie den Vorschlag.
6. Suchen Sie den Befehl, der die Template-Syntax prüft, und den, der Templates kompiliert (Tipp: `list exp`, suchen
   Sie nach `template`).
7. Führen Sie den Befehl für Systeminformationen aus und lesen Sie die ersten Zeilen:
   `php bin/php/console exp:systeminfo | head -20`.
8. Zeigen Sie die Version an: `php bin/php/console --version`.

## Erwartetes Ergebnis

- Schritt 1 endet mit der Anzahl der verfügbaren Befehle.
- Schritt 2 listet Teile wie `cron:frequent` und `cron:infrequent` mit Beschreibungen auf.
- Schritt 5 gibt `UNKNOWN COMMAND`, `Command "exp:ezcach" was not found.` und `Did you mean:  exp:ezcache` aus.
- Schritt 6: `exp:eztemplatecheck` und `exp:eztc`.
- Der Trainer fragt alle Teilnehmenden, welche Datei `ext:mysite_hello:latest` ausführen würde.

## Quiz

1. Welcher Befehl listet die Befehle einer Erweiterung auf?
2. Was führt `exp:ezcache` aus?
   A. `bin/shell/ezcache.sh` B. `bin/php/ezcache.php` C. `cronjobs/ezcache.php` D. `kernel/ezcache.php`
3. Nennen Sie zwei Arten, die Hilfe eines Befehls zu lesen.
4. Warum verweigert ein Skript die Ausführung als root?
5. Welche Datei führt `ext:mysite_hello:latest` aus?

<details>
<summary>Antworten</summary>

1. `php bin/php/console list ext:<ext>`.
2. **B.** `bin/php/ezcache.php`.
3. Zwei von `help <command>`, `--help <command>`, `<command> --help`.
4. Dateien, die als root geschrieben werden, können eine Website lahmlegen, die als anderer Benutzer läuft;
   `--allow-root-user` bestätigt, dass es Absicht ist.
5. `extension/mysite_hello/bin/php/latest.php`.

</details>

## Weiterführende Literatur

- [Exponential Console](../../../../../eng-us/bc/6.0/console.md): Namen, Optionen, Cronjob-Teile und Befehle von Erweiterungen.
- [Die Cronjob-Konsole](../../../../../eng-us/features/6.0/cronjobs-console.md): die Browseransicht der Cronjob-Teile.

## Verwandte Seiten

- [Modul 9: Die Konsole und persistente Objekte](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
