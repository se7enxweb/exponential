---
title: "Lektion 1.4: Ein Such-Backend wählen"
audience: trainer, admin, hosting
category: training
order: 14
summary: Die Teilnehmenden wägen für eine bestimmte Website die eingebaute Suchmaschine gegen Solr ab, anhand ihrer Größe, ihrer Anforderungen und der Kosten eines zweiten Servers, und schreiben eine kurze Empfehlung. 25 Minuten.
keywords: suchkurs, such-backend, auswählen, eingebaute suche, Solr, ezfind, hosting, kosten, anforderungen, empfehlung, search course, search backend, choose, built-in search, cost, requirements, recommendation
type: help
---
# Lektion 1.4: Ein Such-Backend wählen

Die meisten Websites laufen gut mit der eingebauten Suchmaschine; manche brauchen Solr. Die Entscheidung ist nicht nur
technisch: Solr ist ein zweiter Server, den jemand betreiben, überwachen und sichern muss. Diese Lektion gibt den
Teilnehmenden eine kurze Methode, um zu entscheiden und die Entscheidung einem Website-Eigentümer zu erklären.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 1: Wie die Suche funktioniert](README.md) |
| Dauer | 25 Minuten (10 Präsentation, 15 Übung zu zweit) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Projektleiter, Hosting-Mitarbeitende |
| Voraussetzungen | [Lektion 1.2](die-eingebaute-suchmaschine.md) und [Lektion 1.3](solr-und-ezfind-auf-einen-blick.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Fragen aufzählen, die zwischen der eingebauten Suchmaschine und Solr entscheiden;
- nennen, welche Arbeit der Betrieb von Solr kostet: Installation, Speicher und Festplatte, Überwachung, Sicherungen
  oder Neuaufbau;
- für eine beschriebene Website ein Backend wählen und die Wahl in drei Sätzen begründen;
- die drei Wege nennen, auf denen ein Hoster Solr anbieten kann: gar nicht, als Zusatzleistung auf einem eigenen
  Server oder vom Kunden bereitgestellt.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Such-Backends](../../../hosting/grundlagen/such-backends.md#suche-in-einem-tarif-anbieten) und
[Suche mit ezfind und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md#wann-sich-solr-lohnt).

1. **Fünf Fragen** (5 Minuten). Schreiben Sie sie an die Tafel:
   1. Müssen Besucher Ergebnisse filtern (nach Typ, Tag, Datum), also Facetten? Nur Solr hat sie.
   2. Muss der beste Treffer zuerst kommen, mit Gewichtung von Titeln oder bestimmten Klassen? Solr sortiert nach
      Relevanz; die eingebaute Suchmaschine nicht.
   3. Wie viele Objekte, und wie schnell wächst die Website? Die eingebaute Suchmaschine wird bei sehr großen Mengen
      langsamer.
   4. Wer wird den Solr-Server betreiben, überwachen und seinen Index nach einem Ausfall neu aufbauen?
   5. Wird die Suche überhaupt viel genutzt? Die Suchstatistiken (Modul 3) beantworten das, sobald sie eingeschaltet
      sind.
2. **Die Kostenseite** (3 Minuten). Solr läuft als eigener Java-Dienst; planen Sie seinen Speicher und seine
   Festplatte getrennt von PHP, nehmen Sie `exp:solr status` in die Überwachung auf, und sichern Sie entweder seinen
   Index oder seien Sie in der Lage, ihn neu aufzubauen.
3. **Hosting-Optionen** (2 Minuten). Aus dem Hosting-Leitfaden: nur eingebaute Suche; Solr als Zusatzleistung auf
   einem Server, den der Hoster betreibt (`Url` gesetzt), oder auf dem dedizierten Server des Kunden (`BinaryPath`);
   oder ein vom Kunden bereitgestelltes Solr, das die Installation über `Url` erreicht.

### Häufige Fragen

**Können wir mit der eingebauten Suchmaschine beginnen und später wechseln?** Ja. Ein Wechsel bedeutet, ezfind zu
installieren, Solr zu konfigurieren und neu zu indizieren; die Inhalte ändern sich nicht. Templates, die Funktionen nur
von Solr nutzen (Facetten), sind neue Arbeit.

**Ist Solr schneller?** Für große Indizes und komplexe Abfragen ist es dafür gebaut; auf einer kleinen Website ist der
Unterschied gering und der zusätzliche Server lohnt sich nicht. Messen Sie, bevor Sie entscheiden (Modul 5).

**Empfiehlt der Kurs eines?** Nein. Die Dokumentation sagt, dass die eingebaute Suchmaschine für die meisten Websites
passt; die Entscheidung hängt von den Antworten auf die fünf Fragen ab.

### Stolperfallen

- **Nur nach Funktionslisten entscheiden.** Paare, die jede Solr-Funktion abhaken, vergessen Frage 4. Fragen Sie jedes
  Paar, wer angerufen wird, wenn Solr nachts ausfällt.
- **Zahlen erfinden.** Die Dokumentation nennt keine Objektzahl, ab der die eingebaute Suchmaschine nicht mehr
  ausreicht. Nennen Sie im Kurs keine; sagen Sie den Teilnehmenden, dass sie ihre eigene Website messen sollen.

## Praktische Übung

Arbeiten Sie zu zweit, auf Papier oder in einer Textdatei. Es wird keine Installation gebraucht.

1. Lesen Sie die drei Website-Beschreibungen unten.
   - **A.** Die Website einer Gemeinde mit etwa 2.000 Seiten, einem Redaktionsteam, die Suche wird von Besuchern
     genutzt, um Formulare zu finden.
   - **B.** Ein Online-Magazin mit wachsendem Archiv, Lesern, die nach Thema und Jahr filtern, und einem eigenen
     Betriebsteam.
   - **C.** Die Website eines kleinen Vereins auf Shared Hosting, ohne Shell-Zugang.
2. Beantworten Sie für jede Website die fünf Fragen von der Tafel. Schreiben Sie „unbekannt“, wo die Beschreibung es
   nicht sagt.
3. Wählen Sie für jede Website ein Backend.
4. Schreiben Sie zu jeder Wahl drei Sätze für den Website-Eigentümer: was Sie empfehlen, warum und was der Betrieb
   kostet.
5. Schreiben Sie zu jedem „unbekannt“ die Frage, die Sie dem Eigentümer stellen würden.
6. Stellen Sie der Gruppe eine Website in einer Minute vor.

## Erwartetes Ergebnis

- Jedes Paar hat drei Empfehlungen mit Begründungen und offenen Fragen.
- Ein sinnvolles Ergebnis: A mit der eingebauten Suchmaschine (mit Suchstatistiken für eine spätere Prüfung), B mit
  Solr, wenn das Team es betreiben kann (Facetten nach Thema und Jahr), C mit der eingebauten Suchmaschine (kein Ort,
  um einen Solr-Server zu betreiben). Andere Antworten sind in Ordnung, wenn die Begründungen tragen.
- Der Trainer prüft, dass jede Solr-Empfehlung nennt, wer den Server betreibt.

## Quiz

1. Nennen Sie eine Funktion, die nur Solr bietet und die Besucher direkt bemerken.
2. Welches Werkzeug zeigt Ihnen, wie viel Besucher die Suche tatsächlich nutzen?
3. Nennen Sie drei Aufgaben, die mit dem Betrieb eines Solr-Servers einhergehen.
4. Ein Hoster bietet Solr auf seinem eigenen Server für mehrere Kunden an. Welcher Schlüssel in `solr.ini` verweist
   eine Installation darauf?
   A. `BinaryPath` B. `Url` C. `DataDir` D. `RunDir`
5. Richtig oder falsch: Die Dokumentation nennt eine Objektzahl, ab der Solr erforderlich ist.

<details>
<summary>Antworten</summary>

1. Facetten (Ergebnisse nach Klasse, Tag oder Datum filtern) oder die Hervorhebung der Suchwörter.
2. Die Suchstatistiken (**Setup > Suchstatistiken**, mit eingeschaltetem `LogSearchStats`).
3. Drei beliebige aus: ihn installieren, Speicher und Festplatte planen, mit `exp:solr status` überwachen, den Index
   sichern oder neu aufbauen, ihn aktualisieren.
4. **B.** `Url` verweist auf ein laufendes, anderswo verwaltetes Solr ([Such-Backends](../../../hosting/grundlagen/such-backends.md)).
5. **Falsch.** Sie sagt, dass die eingebaute Suchmaschine für die meisten Websites passt und bei sehr großen
   Objektmengen langsamer wird.

</details>

## Weiterführende Informationen

- [Such-Backends](../../../hosting/grundlagen/such-backends.md): die Optionen und die Checkliste des Hosters.
- [Suche mit ezfind und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md): wann sich Solr
  lohnt.
- [Speicher, CPU und Festplatte bemessen](../../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md): die Serverseite
  planen.

## Verwandte Seiten

- [Modul 1: Wie die Suche funktioniert](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
