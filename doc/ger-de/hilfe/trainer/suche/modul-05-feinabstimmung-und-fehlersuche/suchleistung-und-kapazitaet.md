---
title: "Lektion 5.4: Suchleistung und Kapazität"
audience: trainer, admin, developer, hosting
category: training
order: 54
summary: Die Teilnehmenden messen die Geschwindigkeit der Suchseite mit exp:benchmark, vergleichen sie vor und nach einer Änderung der Einstellungen und nennen die Hebel, die die Suche schnell halten, wenn eine Website wächst, von Grenzen und verzögerter Indizierung bis zu Solr. 30 Minuten.
keywords: suchkurs, leistung, kapazität, exp:benchmark, --search, --cold, suchseite, kosten von platzhaltern, DelayedIndexing, MaximumSearchLimit, MaxBooleanClauses, Solr, ausgangsmessung, baseline, search course, performance, capacity, search page, wildcard cost
type: help
---
# Lektion 5.4: Suchleistung und Kapazität

Eine Suchanfrage ist eine der teuersten Seiten, die eine Website ausliefert: Sie kann nicht wie ein Artikel aus dem
Seiten-Cache bedient werden, und jedes Wort bedeutet zusätzliche Arbeit. Diese Lektion bringt den Teilnehmenden bei,
die Suchseite zu messen statt zu raten, ehrlich zu vergleichen und zu wissen, welche Hebel die Suche schnell halten,
wenn die Inhalte wachsen.

> **Hinweis**
> Messen Sie nur Ihre eigene Schulungsinstallation. Lasttests gegen eine Website, die Sie nicht betreiben, können wie
> ein Angriff aussehen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 5: Feinabstimmung und Fehlersuche](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Experten |
| Zielgruppe | Website-Administratoren, Entwickler, Hosting-Mitarbeitende |
| Voraussetzungen | [Lektion 5.1](die-sucheinstellungen-abstimmen.md); die Schulungsinstallation wird ausgeliefert (zum Beispiel mit `exp:velocity start`) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Suchseite mit `exp:benchmark` messen und Median und p90 lesen;
- zwei Läufe fair vergleichen: gleiche Anzahlen, gleiche Seiten, direkt nacheinander, mit gespeicherter
  Ausgangsmessung;
- die Wirkung einer Sucheinstellung (Platzhalter) auf die Suchseite messen;
- die Hebel für Kapazität nennen: Grenzen, verzögerte Indizierung, durchsuchbare Attribute und der Wechsel zu Solr.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Exponential messen](../../../../anleitungen/benchmarking.md),
[Eine gehostete Website messen](../../../hosting/leistung/benchmark-einer-gehosteten-website.md) und
`settings/site.ini [SearchSettings]`.

1. **Die Suchseite in der Messung** (3 Minuten). Ohne `--url` fragt `exp:benchmark` die Startseite, Menüseiten, die
   Suchseite `/content/search?SearchText=<word>` (wenn anonyme Besucher suchen dürfen) und die Anmeldeseite der
   Administration ab, jeweils 100-mal nach 5 Aufwärmanfragen, 4 gleichzeitig. `--search=<words>` wählt die Wörter;
   `--url=/content/search?SearchText=<word>` misst nur die Suchseite.
2. **Fair vergleichen** (3 Minuten). Ein Lauf auf einem ausgelasteten Rechner beweist wenig. Halten Sie `--runs`,
   `--warmup` und `--concurrency` gleich, laufen Sie direkt nacheinander und bewahren Sie Läufe mit `--save=<file>`
   auf; `--baseline=<file>` vergleicht und endet bei einer Verschlechterung mit 1.
3. **Hebel** (4 Minuten).
   - Grenzen: `MaximumSearchLimit`, `MaximumSearchTextWords`, `AllowEmptySearch=disabled` halten einzelne Anfragen
     begrenzt.
   - Platzhalter kosten: Messen Sie, bevor Sie sie einschalten (Übung).
   - Veröffentlichungsgeschwindigkeit: Die verzögerte Indizierung verlagert die Indizierung zu Cron (Modul 2).
   - Indexgröße: nur nützliche Attribute durchsuchbar (Lektion 2.4).
   - Skalierung: Solr ist für sehr große Objektmengen gebaut. Mit ezfind muss `ezfind.ini [SearchHandler]
     MaxBooleanClauses` dem eigenen Limit von Solr entsprechen; jedes Wort ist eine Bedingung pro Abfragefeld, deshalb
     behält der Abfrage-Builder nur so viele Wörter, wie die Felder erlauben.

### Häufige Fragen

**Warum ist die Suchseite langsamer als ein Artikel?** Eine Artikelseite kann aus dem View-Cache ausgeliefert werden;
eine Suche führt für jeden neuen Text die Abfrage der Suchmaschine aus.

**Sollten Suchergebnisse zwischengespeichert werden?** Nicht in diesem Kurs. Messen Sie zuerst; die oben genannten
Hebel sind die dokumentierten.

**Welche Zahlen sind gut?** Die Dokumentation nennt keinen Zielwert. Vergleichen Sie mit Ihrer eigenen
Ausgangsmessung und mit den anderen Seiten derselben Website.

### Stolperfallen

- **Einmal messen.** Ein Lauf sagt wenig. Lassen Sie die Teilnehmenden eine Ausgangsmessung speichern und wiederholen.
- **Ein gemeinsamer Rechner.** Die Messungen anderer Teilnehmender verfälschen Ihre. Führen Sie die Übung reihenweise
  nacheinander aus oder mit weniger Anfragen (`--runs=30`).
- **Vergessen, die Platzhalteränderung rückgängig zu machen.** Schritt 7 stellt die Einstellung wieder her.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert und unter ihrer Adresse erreichbar. Fügen Sie
`--allow-root-user` nur hinzu, wenn Sie als root arbeiten. Führen Sie die Messung aus dem Wurzelverzeichnis der
Installation aus.

1. Führen Sie die Standardmessung einmal aus und suchen Sie die Suchseite in der Tabelle:

   ```bash
   php bin/php/console exp:benchmark --runs=30
   ```

2. Notieren Sie Median und p90 der Suchseite und der Startseite.
3. Messen Sie nur die Suchseite und bewahren Sie den Lauf als Ausgangsmessung auf:

   ```bash
   php bin/php/console exp:benchmark --url="/content/search?SearchText=salad" --runs=50 --save=var/search-baseline.json
   ```

4. Schalten Sie wie in Lektion 5.1 Platzhalter für den öffentlichen Siteaccess ein und leeren Sie den INI-Cache.
5. Messen Sie eine Platzhaltersuche gegen die Ausgangsmessung:

   ```bash
   php bin/php/console exp:benchmark --url="/content/search?SearchText=sal*" --runs=50
   ```

6. Vergleichen Sie die Mediane aus den Schritten 3 und 5. Schreiben Sie einen Satz: Ist der Unterschied größer als die
   Streuung zwischen zwei Läufen von Schritt 3? (Führen Sie Schritt 3 noch einmal ohne `--save` aus, um die Streuung zu
   sehen.)
7. Schalten Sie Platzhalter wieder aus und leeren Sie den INI-Cache.
8. Entfernen Sie die Datei der Ausgangsmessung, die Sie in `var/` angelegt haben, wenn Sie fertig sind, oder behalten
   Sie sie für den Vergleich in der nächsten Lektion.

## Erwartetes Ergebnis

- Schritt 1 gibt eine Tabelle aus, in der die Suchseite unter den gemessenen Seiten steht (sofern anonyme Besucher
  suchen dürfen).
- Die Schritte 3 und 5 geben Median, p90, p99, Minimum, Maximum und Standardabweichung für eine Seite aus; Schritt 3
  schreibt die JSON-Datei.
- Schritt 6 ist ein Satz auf Grundlage von Zahlen, nicht von Eindrücken; auf einer kleinen Schulungs-Website kann der
  Unterschied innerhalb der Streuung liegen.
- Nach Schritt 7 gibt `exp:ini get` für `EnableWildcard` im öffentlichen Siteaccess `false` aus.
- Der Trainer sammelt zwei oder drei Sätze aus Schritt 6 und bespricht, warum die Ergebnisse zwischen den Rechnern
  abweichen.

## Quiz

1. Welche Option legt die Wörter der Suchseite in der Standardmessung fest?
2. Nennen Sie drei Bedingungen für einen fairen Vergleich zweier Läufe.
3. Warum kann eine Suchseite nicht einfach wie ein Artikel aus dem Seiten-Cache ausgeliefert werden?
4. Welche Option vergleicht einen Lauf mit einem gespeicherten und schlägt bei einer Verschlechterung fehl?
   A. `--save` B. `--baseline` C. `--cold` D. `--compare`
5. Nennen Sie drei Hebel, die die Suche schnell halten, wenn eine Website wächst.

<details>
<summary>Antworten</summary>

1. `--search=<words>` ([Exponential messen](../../../../anleitungen/benchmarking.md)).
2. Drei beliebige aus: gleiche `--runs`, `--warmup` und `--concurrency`; dieselben Seiten; direkt nacheinander;
   dieselbe PHP-Konfiguration; ein ruhiger Rechner.
3. Jeder neue Suchtext führt die Abfrage der Suchmaschine aus; die Ergebnisse hängen von den Wörtern und vom Benutzer
   ab.
4. **B.** Es endet bei einer Verschlechterung mit 1.
5. Drei beliebige aus: die Suchgrenzen, Platzhalter aus, sofern nicht gemessen, verzögerte Indizierung, weniger
   durchsuchbare Attribute, der Wechsel zu Solr.

</details>

## Weiterführende Informationen

- [Exponential messen](../../../../anleitungen/benchmarking.md): jeder Modus, jede Option und jede Zahl von
  `exp:benchmark`.
- [Eine gehostete Website messen](../../../hosting/leistung/benchmark-einer-gehosteten-website.md): Messen auf einem Server.
- [Such-Backends](../../../hosting/grundlagen/such-backends.md): was der Betrieb jeder Suchmaschine kostet.

## Verwandte Seiten

- [Modul 5: Feinabstimmung und Fehlersuche](README.md)
- [Suche: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
