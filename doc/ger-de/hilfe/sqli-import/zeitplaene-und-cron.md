---
title: Zeitpläne und Cron in 7x SQLI Import
audience: admin, integrator, developer
category: integration
order: 30
summary: Wie 7x SQLI Import Importe von selbst startet - die Arten von Zeitplänen von einmalig bis zu Cron-Ausdrücken, Zeitzonen und Sommerzeit, verpasste Läufe, E-Mails bei Fehlern und die Cron-Teile sqliimport und expqueue, die jede Minute laufen müssen.
keywords: sqliimport, Zeitplan, Cron, Cron-Ausdruck, Intervall, stündlich, täglich, wöchentlich, monatlich, Zeitzone, Sommerzeit, Nachholen, Benachrichtigung, runcronjobs, sqliimport_run, sqliimport_cleanup, tick, schedule, cron expression, interval, time zone, daylight saving time, catch up, notify
type: help
---
# Zeitpläne und Cron in 7x SQLI Import

## Inhalt

1. [Arten von Zeitplänen](#arten-von-zeitplänen)
2. [Zeitzonen](#zeitzonen)
3. [Verpasste Läufe und E-Mails](#verpasste-läufe-und-e-mails)
4. [Die Cron-Teile](#die-cron-teile)

## Arten von Zeitplänen

Ein Import kann mehrere Zeitpläne haben:

| Art | Läuft |
|---|---|
| Einmal | Zu einem Datum und einer Uhrzeit |
| Intervall | Alle N Minuten, mindestens 5 |
| Stündlich | Jede Stunde zu einer Minute |
| Täglich | Jeden Tag zu einer Uhrzeit |
| Wöchentlich | Jede Woche an einem Wochentag zu einer Uhrzeit |
| Monatlich | Jeden Monat an einem Tag zu einer Uhrzeit (in einem kürzeren Monat am letzten Tag) |
| Cron | Ein Cron-Ausdruck mit fünf Feldern, etwa `30 2 * * *` oder `0 6 * * mon-fri` |

Cron-Ausdrücke unterstützen Listen, Bereiche, Schritte, die Namen der Monate und Tage sowie die Kurzformen
`@hourly`, `@daily`, `@weekly`, `@monthly` und `@yearly`.

In 2.0.0-alpha.1 entstehen Zeitpläne durch die Migration geplanter Importe von 1.x oder in PHP. Der Zeitplan-Editor
kommt mit dem Import-Editor in 2.0.0-beta.1.

## Zeitzonen

Jeder Zeitplan hat eine Zeitzone (Vorgabe `UTC`). „Täglich um 02:30“ in `Europe/Berlin` bleibt das ganze Jahr um
02:30 Ortszeit. Am Tag, an dem die Uhr vorgestellt wird, wird eine Uhrzeit, die es nicht gibt, ausgelassen; am Tag,
an dem sie zurückgestellt wird, läuft eine Uhrzeit, die es zweimal gibt, einmal.

## Verpasste Läufe und E-Mails

- Lief cron eine Weile nicht, holt ein Zeitplan einmal nach (die Vorgabe) oder wartet auf seinen nächsten Termin,
  wenn er verpasste Läufe auslassen soll.
- Läuft der Import noch, wenn sein Zeitplan fällig ist, wird dieser Lauf nicht gestartet; der Zeitplan wartet auf
  seinen nächsten Termin.
- Ein Zeitplan kann E-Mail-Adressen nennen, die benachrichtigt werden, wenn einer seiner Läufe fehlschlägt oder mit
  fehlgeschlagenen Datensätzen endet.

## Die Cron-Teile

Zwei Cron-Teile müssen jede Minute laufen:

```bash
* * * * * cd /pfad/zu/exponential && php runcronjobs.php -q expqueue
* * * * * cd /pfad/zu/exponential && php runcronjobs.php -q sqliimport
```

- `sqliimport` stellt die Läufe fälliger Zeitpläne und offener Importaufträge von 1.x in die Warteschlange, beendet
  Läufe, deren Jobs beendet sind, und entfernt alte Läufe. Er stellt nur ein; die Importarbeit selbst leistet er
  nicht.
- `expqueue` startet die Worker, die die Läufe verarbeiten. expqueue kann stattdessen auch als systemd-Dienst
  laufen.

`./console ext:sqliimport:tick` führt eine Runde des Teils `sqliimport` von Hand aus und gibt aus, was sie getan
hat.

Die Cron-Teile `sqliimport_run` und `sqliimport_cleanup` von 1.x funktionieren noch, sind aber veraltet. Ist die
Job-Warteschlange installiert, übergibt `sqliimport_run` die Importe von 1.x an die Engine von 2.x. Ersetzen Sie
sie durch `sqliimport`.

Weiter: [Aktualisierung von 1.x](aktualisierung-von-1x.md).
