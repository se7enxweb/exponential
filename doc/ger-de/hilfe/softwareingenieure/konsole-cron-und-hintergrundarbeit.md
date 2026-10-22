---
title: Konsole, Cronjobs und Hintergrundarbeit
audience: software-engineer, developer, admin
category: software-engineering
order: 190
summary: Wie in Exponential Arbeit außerhalb einer Webanfrage läuft - die Konsole und ihre Befehlserkennung, eZScript, Cronjob-Teile, die runcronjobs.php ausführt, Inhaltsjobs für große Inhaltsoperationen, die Job-Warteschlange expqueue für Erweiterungen und die Regeln, mit denen Befehle und Jobs gefahrlos wiederholt werden können.
keywords: Konsole, bin/php/console, Befehle, eZScript, Cronjobs, runcronjobs.php, Cronjob-Teile, Inhaltsjobs, expqueue, Job-Warteschlange, Hintergrundarbeit, idempotent, Exit-Codes, Softwareingenieur, console, commands, cronjob parts, content jobs, job queue, background work, exit codes
type: help
---
# Konsole, Cronjobs und Hintergrundarbeit

Diese Seite richtet sich an Ingenieure, die Arbeit entwerfen, die nicht innerhalb einer Webanfrage laufen soll: Importe,
Bereinigungen, E-Mails, Indexierung, große Inhaltsoperationen. Exponential hat dafür drei Mechanismen, plus die Konsole,
die sie alle ausführt.

## Inhalt

1. [Die Konsole](#die-konsole)
2. [Cronjob-Teile](#cronjob-teile)
3. [Inhaltsjobs](#inhaltsjobs)
4. [Die Job-Warteschlange: expqueue](#die-job-warteschlange-expqueue)
5. [Was Sie verwenden](#was-sie-verwenden)
6. [Regeln](#regeln)

## Die Konsole

`bin/php/console` ist ein Einstiegspunkt für jeden Befehl (Installationen legen meist eine Abkürzung `./console` im
Hauptverzeichnis an). Sie erkennt Befehle zur Laufzeit:

| Quelle | Befehlsname |
|---|---|
| `bin/php/<name>.php` | `exp:<name>` (plus Namen, die mit `@alias` im Docblock deklariert sind, etwa `exp:help`) |
| `bin/shell/<name>.sh` | `shell:<name>` |
| `extension/<ext>/bin/php/<name>` | `ext:<ext>:<name>` |
| ein Cronjob-Teil | `cron:<part>` |

Der Code eines Kernel-Befehls ist eine Klasse `Exponential\Runnable\Command` in `kernel/private/classes/commands/`; die
Datei in `bin/php/` ist ein einzeiliger Einstiegspunkt. Skripte starten über `eZScript`, nicht über `ezpKernelWeb`.
Referenz: [Exponential Console](../../bc/6.0/konsole.md); für Autoren
[Konsolenbefehle](../ersteller/anwendungsentwickler/konsolenbefehle.md).

```bash
php bin/php/console list              # every command
php bin/php/console list cron         # cronjob parts
php bin/php/console help exp:preload  # the help of one command
```

## Cronjob-Teile

`runcronjobs.php` führt die in `settings/cronjob.ini` konfigurierten Teile aus: `[CronjobSettings] Scripts[]` für den
Standardteil und `[CronjobPart-<name>] Scripts[]` für benannte Teile wie `frequent` und `infrequent`. Der Code eines
Teils ist eine Klasse `Exponential\Runnable\CronjobPart` in `kernel/private/classes/cronjobs/`.

```bash
php runcronjobs.php frequent
```

Die Gruppe `frequent` ist auf jeder Installation wichtig: Sie enthält unter anderem den Teil `audit`, der den Audit-Trail
indexiert, ausliefert, Warnregeln auswertet und rotiert ([Der Audit-Trail](../../funktionen/6.0/pruefprotokoll.md)). Cronjobs
lassen sich in Konsole und Administration auflisten, ausführen und pausieren
([Cronjob-Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md), [Cronjobs pausieren](../../funktionen/6.0/cronjobs-pausieren.md)).

## Inhaltsjobs

Große Entfernungs-, Kopier- und Verschiebevorgänge, Paketinstallationen und Audit-Exporte laufen als Inhaltsjobs: Die
Anfrage reiht den Job ein und kehrt zurück, und die Arbeit läuft in Stapeln im Hintergrund weiter
([Inhaltsjobs](../../bc/6.0/inhaltsjobs.md), [Funktionsseite Inhaltsjobs](../../funktionen/6.0/inhaltsauftraege.md)).

## Die Job-Warteschlange: expqueue

`expqueue` ist eine datenbankgestützte Job-Warteschlange für Erweiterungen, ab 6.0.15 von `composer.json` verlangt und
inaktiv, bis sie aktiviert wird. Erzeuger reihen einen Job mit einem Aufruf ein; Worker führen ihn auf der
Kommandozeile, über Cron oder als systemd-Dienst aus, nie innerhalb einer Webanfrage. Sie bietet Prioritätsklassen,
Wiederholungen mit Backoff, eine Dead-Letter-Ablage, Idempotenzschlüssel und Leases mit einem Aufräumer
([expqueue: die Job-Warteschlange](../../funktionen/6.0/erweiterungen/expqueue.md),
[Warteschlangen mit expqueue](../ersteller/anwendungsentwickler/warteschlangen-mit-expqueue.md)).

## Was Sie verwenden

| Bedarf | Verwenden Sie |
|---|---|
| Regelmäßige Wartung (Bereinigung, Sammel-E-Mails, Index-Aktualisierungen) | einen Cronjob-Teil |
| Eine große Inhaltsoperation, die ein Redakteur startet | einen Inhaltsjob |
| Arbeit, die Ihr Code einreiht, mit Wiederholungen und Prioritäten | expqueue |
| Eine Betriebsaufgabe, von Hand oder aus einem Bereitstellungsskript ausgeführt | einen Konsolenbefehl |

## Regeln

- Ein Cronjob-Teil **muss** gefahrlos zweimal laufen und unterbrochen werden können.
- Ein Befehl **muss** seinen `--help`-Text und seine Exit-Codes stabil halten, weil Skripte und Cron davon abhängen, und
  sollte `--dry-run` anbieten, wenn er Daten ändert.
- Gemeinsame Logik gehört in eine Dienstklasse, die Views, Befehle und Teile alle aufrufen
  ([Muster für Hintergrundarbeit](../ersteller/anwendungsentwickler/muster-fuer-hintergrundarbeit.md)).
- Langlaufende Worker sehen eine Klassenänderung erst nach ihrem Neustart, wie die Worker von Velocity.

## Verwandte Seiten

- [Cronjobs](../../anleitungen/cronjobs.md)
- [CLI-Skripte](../ersteller/anwendungsentwickler/cli-skripte.md)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
