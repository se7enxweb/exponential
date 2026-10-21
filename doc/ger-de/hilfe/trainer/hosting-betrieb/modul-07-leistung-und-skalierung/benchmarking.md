---
title: "Lektion 7.1: Eine Website messen"
audience: trainer, hosting, admin
category: training
order: 71
summary: Die Teilnehmenden messen eine Website mit exp:benchmark, lesen Median, p90 und die Cache-Spalte, vergleichen warme und kalte Läufe und speichern eine Baseline, die einen Rückschritt nach einer Bereitstellung erkennt. 45 Minuten.
keywords: hosting und betrieb, benchmark, exp:benchmark, median, p90, p99, kalt, warm, baseline, speichern, schwellenwert, min-delta, vergleichen, rückschritt, rauschen, tarifgrenzen, hosting and operations, cold, warm, threshold, compare, regression, noise, plan limits
type: help
---
# Lektion 7.1: Eine Website messen

„Eine langsame Website ist eine Zahl, kein Gefühl.“ Hoster brauchen Zahlen, um zwei Einrichtungen zu vergleichen, um
zu beweisen, dass eine Bereitstellung eine Website nicht verlangsamt hat, und um die Grenzen eines Tarifs festzulegen.
Diese Lektion vermittelt `exp:benchmark`: was es misst und was nicht, wie man seine Ausgabe liest und wie man eine
Baseline führt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 7: Leistung und Skalierung](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Systemadministratoren und Produktmanager |
| Voraussetzungen | [Modul 2](../modul-02-velocity/README.md) und [Lektion 4.1](../modul-04-bereitstellung-und-cron/eine-aenderung-bereitstellen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- sagen, was `exp:benchmark` misst (die Zeit bis zum letzten Byte, pro Seite) und was nicht (Arbeit im Browser,
  maximale Kapazität);
- es sicher ausführen und Median, p90, p99, Standardabweichung, Anfragen pro Sekunde und die Cache-Spalte lesen;
- warme und kalte (`--cold`) Läufe vergleichen und erklären, warum beide ausgewiesen werden;
- eine Baseline speichern und einen späteren Lauf damit vergleichen, mit Lesen des Rückgabewerts;
- Rauschen verringern und erklären, warum Zahlen einer Website keine Eigenschaft des Servers sind.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Eine gehostete Website messen](../../../hosting/leistung/benchmark-einer-gehosteten-website.md),
[Exponential messen](../../../../anleitungen/benchmarking.md) und [Benchmark](../../../../../eng-us/features/6.0/benchmark.md).

1. **Was es misst** (3 Minuten). Die Zeit bis zum letzten Byte jeder Antwort, im HTTP-Modus. Es beantwortet nicht, wie
   viele Besucher ein Rechner verkraftet; das braucht einen Lastgenerator gegen eine Kopie der Website
   (`--tool=ab|wrk|oha` ist der Anfang davon).
2. **Standardmäßig sicher** (2 Minuten). Nur `GET`, keine Cookies, sofern nicht verlangt; große Läufe oder andere
   Hosts brauchen `--force`; `--cold-clear` ist die einzige Option, die etwas leert.
3. **Die Ausgabe lesen** (4 Minuten). Der Median ist die Zahl zum Vergleichen; p90 und p99 zeigen die langsamen
   Ausreißer; eine Standardabweichung so groß wie der Median bedeutet zwei vermischte Arten von Antworten; vergleichen
   Sie nur Zeilen mit derselben Cache-Antwort.
4. **Warm und kalt** (3 Minuten). Warm ist, was ein wiederkehrender Besucher bekommt; `--cold` umgeht jeden
   Antwort-Cache. Die meisten Besucher bekommen die warme Zahl, Redakteure und Suchen die kalte. Eine einzelne Zahl
   „welcher Server ist schneller“ führt in die Irre.
5. **Baselines** (3 Minuten). `--save` und `--baseline`; eine Kennzahl hat sich verschlechtert, wenn sie um mehr als
   `--threshold` Prozent (15) und um mehr als `--min-delta` Millisekunden (1) gewachsen ist. Rückgabewert 0, 1 oder 2.

### Häufige Fragen

**Darf ich die Produktiv-Website eines Kunden messen?** Behalten Sie die Standardwerte bei, oder ein kleines
`--requests` auf einer angeschlagenen Website; ein Benchmark ist Last. Bevorzugen Sie eine Staging-Kopie.

**Warum unterscheiden sich meine zwei Läufe?** Rauschen: andere Last auf dem Rechner, ein zwischen den Läufen
geleerter Cache. Führen Sie sie direkt nacheinander auf einem ruhigen Rechner aus und verwenden Sie auf gemeinsam
genutzten Rechnern einen höheren `--threshold`.

**Wie lege ich daraus Tarifgrenzen fest?** Messen Sie eine repräsentative Website, dargestellt bei steigender
Parallelität, erhöhen Sie die Worker schrittweise und stimmen Sie bis zu dem Punkt ab, an dem p90 zu steigen beginnt
(Lektion 2.3).

### Stolperfallen

- **Angemeldet messen** mit einem Cookie: Angemeldete Seiten umgehen die anonymen Caches. Behalten Sie den Standard bei.
- **Fünfzehn Teilnehmende messen einen gemeinsam genutzten Server.** Jede Person misst ihre eigene Installation; halten
  Sie `--requests` auf gemeinsam genutzter Hardware klein.
- **Eine warme Zeile mit einer kalten Zeile vergleichen.** Prüfen Sie zuerst die Cache-Spalte.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert von Velocity, als Benutzer der Website.

1. Führen Sie den Standard-Benchmark mit weniger Anfragen aus:

   ```bash
   php bin/php/console exp:benchmark --requests=30
   ```

2. Notieren Sie für die Startseite Median, p90 und die Cache-Spalte.
3. Führen Sie ihn dargestellt aus, an jedem Antwort-Cache vorbei: `php bin/php/console exp:benchmark --cold --requests=30`.
4. Vergleichen Sie den warmen und den kalten Median der Startseite und schreiben Sie einen Satz dazu, was jede Zahl für
   Besucher bedeutet.
5. Speichern Sie eine Baseline:

   ```bash
   mkdir -p var/benchmark
   php bin/php/console exp:benchmark --cold --requests=30 --save=var/benchmark/base.json
   ```

6. Leeren Sie den Inhalts-View-Cache (`php bin/php/ezcache.php --clear-id=content`), um die Wirkung einer
   Bereitstellung nachzustellen.
7. Vergleichen Sie mit der Baseline und geben Sie den Rückgabewert aus:

   ```bash
   php bin/php/console exp:benchmark --cold --requests=30 --baseline=var/benchmark/base.json --min-delta=5; echo "exit $?"
   ```

8. Wärmen Sie die Website vor (`php bin/php/warm.php`) und wiederholen Sie Schritt 7.
9. Notieren Sie die Rückgabewerte der Schritte 7 und 8 und erklären Sie den Unterschied.

## Erwartetes Ergebnis

- Schritt 1 gibt pro Seite eine Tabelle mit Median, p90, p99, Minimum, Maximum, sd und der Cache-Spalte aus, dazu die
  Umgebung.
- Schritt 3: Die kalten Mediane sind höher als die warmen.
- Schritt 5 schreibt `var/benchmark/base.json`.
- Schritt 7 meldet direkt nach dem Leeren möglicherweise einen Rückschritt (exit 1); nach dem Vorwärmen endet Schritt 8
  meist mit 0.
- Der Trainer prüft das, indem er nach dem warmen und dem kalten Median der Startseite und nach den beiden
  Rückgabewerten fragt.

## Quiz

1. Was misst `exp:benchmark` im HTTP-Modus?
   A. Darstellung im Browser B. die Zeit bis zum letzten Byte jeder Antwort C. maximale Besucherzahl D. CPU der
   Datenbank
2. Welche Zahl vergleichen Sie zwischen Läufen?
3. Warum werden warme und kalte Läufe ausgewiesen?
4. Wann gilt eine Kennzahl gegenüber einer Baseline als verschlechtert?
5. Was bedeutet Rückgabewert 1 nach einem Lauf mit `--baseline`?

<details>
<summary>Antworten</summary>

1. **B.** ([Eine gehostete Website messen](../../../hosting/leistung/benchmark-einer-gehosteten-website.md#was-es-misst-und-was-nicht))
2. Den Median.
3. Die meisten Besucher bekommen die warme Zahl; Redakteure, Suchen und Cache-Fehltreffer bekommen die kalte.
4. Wenn sie um mehr als `--threshold` Prozent (15) und um mehr als `--min-delta` Millisekunden (1) gewachsen ist.
5. Eine Kennzahl hat sich verschlechtert (0 ist kein Rückschritt, 2 ein Bedienungsfehler oder eine Ablehnung).

</details>

## Weiterführende Informationen

- [Eine gehostete Website messen](../../../hosting/leistung/benchmark-einer-gehosteten-website.md)
- [Exponential messen](../../../../anleitungen/benchmarking.md): jeder Modus und Arbeitsablauf.
- [Caches leeren und vorwärmen](../../../hosting/leistung/caches-leeren-und-vorwaermen.md)
- [Speicher, CPU und Festplatte bemessen](../../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)

## Verwandte Seiten

- [Modul 7: Leistung und Skalierung](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
