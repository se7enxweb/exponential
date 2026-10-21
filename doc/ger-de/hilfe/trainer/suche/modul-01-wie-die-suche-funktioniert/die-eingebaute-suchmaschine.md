---
title: "Lektion 1.2: Die eingebaute Suchmaschine"
audience: trainer, admin, developer
category: training
order: 12
summary: Die Teilnehmenden erklären, wie die eingebaute Suchmaschine eZSearchEngine Wörter in der Datenbank speichert, wie sie Wörter und Phrasen abgleicht und welche mitgelieferten Grenzen jede Suche prägen. 30 Minuten.
keywords: suchkurs, eZSearchEngine, eingebaute suche, ezsearch_word, ezsearch_object_word_link, phrase, stoppwörter, platzhalter, SearchSettings, MaximumSearchLimit, search course, built-in search, stop words, wildcard
type: help
---
# Lektion 1.2: Die eingebaute Suchmaschine

Jede Installation beginnt mit der eingebauten Suchmaschine `eZSearchEngine`. Sie braucht keinen zusätzlichen Server:
Ihr Index liegt in zwei Tabellen der eigenen Datenbank der Website. Wer weiß, wie sie Wörter speichert und abgleicht,
kann das meiste erklären, was die Teilnehmenden später im Kurs in den Suchergebnissen sehen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 1: Wie die Suche funktioniert](README.md) |
| Dauer | 30 Minuten (15 Präsentation, 15 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren und Entwickler |
| Voraussetzungen | [Lektion 1.1](wo-gesucht-wird.md); ein Terminal auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die beiden Indextabellen `ezsearch_word` und `ezsearch_object_word_link` in je einem Satz beschreiben;
- erklären, dass Wörter normalisiert werden (Kleinbuchstaben, Akzente entfernt), bevor sie indiziert und gesucht
  werden;
- mit doppelten Anführungszeichen nach einem genauen Ausdruck suchen und den Unterschied zu denselben Wörtern ohne
  Anführungszeichen vorhersagen;
- die mitgelieferten Grenzen in `[SearchSettings]` mit `exp:ini` lesen und erklären, wovor jede schützt.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: `settings/site.ini [SearchSettings]`, [Website-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md) und
[10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung).

1. **Index in der Datenbank** (4 Minuten). Wenn ein Objekt veröffentlicht wird, nimmt die Suchmaschine den Text jedes
   Attributs, das in seiner Klasse als **Durchsuchbar** markiert ist, zerlegt ihn in Wörter und speichert jedes Wort
   einmal in `ezsearch_word`. Für jedes Vorkommen fügt sie eine Zeile in `ezsearch_object_word_link` ein (welches
   Objekt, welches Attribut, die Position, die Klasse und die Sektion). Eine Suche schlägt die Wörter nach und führt
   die Verknüpfungen zu den Objekten zurück.
2. **Normalisierung** (3 Minuten). Vor dem Indizieren und vor dem Suchen durchläuft der Text die
   Zeichentransformationsgruppe `search` aus `settings/transform.ini`: Zerlegung, Normalisierung, Entfernen von
   diakritischen Zeichen, Kleinschreibung und eine Bereinigung. Für die Suchmaschine sind `Café`, `cafe` und `CAFE`
   also dasselbe Wort.
3. **Wörter und Phrasen** (3 Minuten). Mehrere Wörter finden Elemente, die alle davon enthalten. Wörter in doppelten
   Anführungszeichen, `"healthy breakfast"`, sind eine Phrase: Die Wörter müssen in dieser Reihenfolge nebeneinander
   stehen. Typografische Anführungszeichen zählen wie gerade.
4. **Grenzen** (5 Minuten). Zeigen Sie die mitgelieferten Werte mit `exp:ini get` (Übung). Erklären Sie jeden:
   `MaximumSearchLimit=30` (die meisten Ergebnisse pro Anfrage), `MaximumSearchTextWords=16` und
   `MaximumSearchTextLength=1024` (längere Texte werden abgeschnitten), `AllowEmptySearch=disabled`,
   `EnableWildcard=false` mit `MinCharacterWildcard=3` und die Stoppwortregel `StopWordThresholdValue=100` mit
   `StopWordThresholdPercent=60`: Auf einer Website mit mehr als 100 Objekten wird ein Wort ignoriert, das in mehr als
   60 Prozent davon vorkommt.

### Häufige Fragen

**Warum findet das Wort „the“ auf einer großen Website nichts?** Es ist nach der Schwellenregel ein Stoppwort: Es
kommt in mehr als 60 Prozent der Objekte vor. Auf einer kleinen Schulungs-Website mit weniger als 100 Objekten greift
die Regel noch nicht.

**Kann sie Wortteile finden?** Nur mit Platzhaltern (`EnableWildcard=true`) und mindestens `MinCharacterWildcard`
Zeichen vor dem `*`. Das ist standardmäßig ausgeschaltet, weil es die Website verlangsamen kann.

**Enthält der Index den ganzen Text?** Er enthält Wörter und ihre Positionen, nicht den Text selbst. Die
Ergebnisliste zeigt die Inhalte aus den Objekten.

**Funktioniert sie auf MongoDB?** Ja; führen Sie auf einer MongoDB-Installation nach der Installation einmal die
Cronjobs aus, um den Index aufzubauen ([Such-Backends](../../../hosting/grundlagen/such-backends.md)).

### Stolperfallen

- **Die INI-Datei direkt lesen.** Die Teilnehmenden öffnen `settings/site.ini` und halten ihre Werte für die
  Wahrheit. Overrides können sie ändern. Vermitteln Sie `exp:ini where`, das jede Datei und den wirksamen Wert zeigt.
- **Eine Relevanzsortierung erwarten.** Die eingebaute Suchmaschine hat kein Relevanzmodell wie Solr. Versprechen Sie
  keine bessere Sortierung, als sie liefert.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und ein Terminal in ihrem Wurzelverzeichnis. Fügen Sie
`--allow-root-user` nur hinzu, wenn Sie als `root` arbeiten.

1. Geben Sie die wirksame Suchmaschine aus:

   ```bash
   php bin/php/console exp:ini get site.ini/SearchSettings/SearchEngine
   ```

2. Zeigen Sie, wo sie gesetzt ist und ob etwas sie überschreibt:

   ```bash
   php bin/php/console exp:ini where site.ini/SearchSettings/SearchEngine
   ```

3. Geben Sie die Grenzen einzeln aus: `MaximumSearchLimit`, `MaximumSearchTextWords`, `AllowEmptySearch`,
   `EnableWildcard`, `StopWordThresholdValue` und `StopWordThresholdPercent` (derselbe Befehl wie in Schritt 1,
   andere Namen).
4. Legen Sie in der Administration unter einem eigenen Ordner einen **Article** mit dem Titel `Morning café routine`
   an und veröffentlichen Sie ihn.
5. Suchen Sie mit dem Feld **Suchen...** nach `cafe` (ohne Akzent). Suchen Sie dann nach `CAFÉ`.
6. Suchen Sie nach `routine morning`, dann nach `"routine morning"` mit doppelten Anführungszeichen, dann nach
   `"morning café"`.
7. Notieren Sie, welche Suchen den Artikel gefunden haben und warum.

## Erwartetes Ergebnis

- Schritt 1 gibt auf einer frischen Installation `eZSearchEngine` aus.
- Schritt 2 listet `settings/site.ini` und jede Override-Datei mit dem wirksamen Wert auf.
- Schritt 5: Beide Suchen finden den Artikel, weil Akzente und Groß-/Kleinschreibung vor dem Abgleich entfernt werden.
- Schritt 6: `routine morning` findet ihn (beide Wörter vorhanden), `"routine morning"` nicht (falsche Reihenfolge als
  Phrase), `"morning café"` schon.
- Der Trainer prüft das, indem er zwei Teilnehmende bittet, Schritt 6 zu erklären.

## Quiz

1. In welchen zwei Tabellen hält die eingebaute Suchmaschine ihren Index?
2. Was bewirkt `StopWordThresholdPercent=60` auf einer Website mit 500 Objekten?
3. Welche Suche findet einen Artikel mit dem Titel „Fresh green salad“?
   A. `"salad green"` B. `"green salad"` C. `salad*` bei ausgeschalteten Platzhaltern D. keine davon
4. Warum ist `AllowEmptySearch` standardmäßig ausgeschaltet?
5. Welcher Befehl zeigt jede Datei, die eine Einstellung setzt, und den wirksamen Wert?

<details>
<summary>Antworten</summary>

1. `ezsearch_word` (jedes Wort einmal) und `ezsearch_object_word_link` (jedes Vorkommen in einem Objekt).
2. Ein Wort, das in mehr als 60 Prozent der Objekte vorkommt, wird bei Suchen ignoriert (der Schwellenwert 100 ist
   überschritten).
3. **B.** Eine Phrase muss die Reihenfolge einhalten; Platzhalter sind standardmäßig ausgeschaltet.
4. Leere Suchen verbrauchen viele Ressourcen (Kommentar in `settings/site.ini`).
5. `php bin/php/console exp:ini where <file>/<block>/<setting>`
   ([Einstellungen über die Kommandozeile ändern](../../../../../eng-us/features/6.0/exp-ini-command.md)).

</details>

## Weiterführende Informationen

- [Website-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md): die Tabelle der Einstellungen der eingebauten
  Suchmaschine.
- [Einstellungen über die Kommandozeile ändern: exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md): Einstellungen
  sicher lesen und schreiben.
- [Such-Backends](../../../hosting/grundlagen/such-backends.md): die eingebaute Suchmaschine aus Sicht eines Hosters.

## Verwandte Seiten

- [Modul 1: Wie die Suche funktioniert](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
