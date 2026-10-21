---
title: Checkliste für Bibliothekswebsites
audience: businesses, project-manager, website-creator, editor
category: business-solutions
order: 80
summary: Eine Checkliste für die Website einer Bibliothek, eines Archivs oder eines Katalogs auf Exponential - Umfang und Planung, das Modell der Einträge, Schlagwörter, Suche, Dateien und Lizenzen, Exporte und Feeds, Barrierefreiheit, Start und die monatliche Routine - jeder Punkt mit der Seite verlinkt, die ihn erklärt.
keywords: Checkliste für Bibliotheken, Start des Katalogs, Archivwebsite, Go-live, Modell der Einträge, Schlagwörter, Suche, digitale Sammlung, Feeds, Barrierefreiheit, monatliche Routine, library checklist, catalog launch, archive website, record model, subject headings, search, digital collection, feeds, accessibility, monthly routine
type: help
---
# Checkliste für Bibliothekswebsites

Diese Seite ist für die Projektleitung einer Website für eine Bibliothek, ein Archiv oder einen Katalog. Sie sammelt die
Prüfungen dieses Bereichs in der Reihenfolge eines Projekts, sodass Sie sie in Ihr Werkzeug zur Aufgabenverfolgung
kopieren und abhaken können. Jeder Punkt verlinkt die Seite, die ihn erklärt; hier ist nichts neu.

## Inhalt

1. [Planung](#planung)
2. [Modell der Einträge](#modell-der-einträge)
3. [Schlagwörter](#schlagwörter)
4. [Einträge hereinholen](#einträge-hereinholen)
5. [Suche](#suche)
6. [Dateien und digitale Sammlungen](#dateien-und-digitale-sammlungen)
7. [Exporte und Feeds](#exporte-und-feeds)
8. [Barrierefreiheit und Leser](#barrierefreiheit-und-leser)
9. [Start](#start)
10. [Jeden Monat](#jeden-monat)
11. [Jedes Jahr](#jedes-jahr)

## Planung

Aus [Katalog planen](katalog-planen.md):

- [ ] Umfang schriftlich festgehalten: was auf der Website steht und welches System für Bestände und Ausleihe führend
      bleibt.
- [ ] Es ist klar, dass es keine Unterstützung für MARC, Z39.50 oder Ausleihe gibt
      ([Bibliotheken und Kataloge](README.md#was-exponential-nicht-ist)).
- [ ] Erwartete Zahl der Einträge jetzt und in drei Jahren.
- [ ] Arten von Einträgen aufgelistet; Zielgruppen und ihre Aufgaben aufgelistet.
- [ ] Wer katalogisiert, wer freigibt, wer das Vokabular verantwortet.
- [ ] Sprachen der Beschreibung entschieden.
- [ ] Kennzahlen für die monatliche Prüfung gewählt.

## Modell der Einträge

Aus [Katalogeinträge modellieren](katalogeintraege-modellieren.md):

- [ ] Eine Klasse pro Art von Eintrag; Container für Sammlungen und Reihen.
- [ ] Felder mit Wert-Datentypen für alles, was sortiert, gefiltert oder durchstöbert wird (`ezdate`, `ezselection`,
      Relationen, Tags).
- [ ] Kennungen wie die ISBN in eigenen Feldern, nicht übersetzbar.
- [ ] Urheber als Person- oder Organization-Objekte, mit `ezobjectrelationlist` verknüpft.
- [ ] Pflicht, durchsuchbar und übersetzbar bewusst gesetzt.
- [ ] Muster für Objektname und URL-Alias ergeben lesbare Namen.
- [ ] Klassen auf einer Entwicklungskopie gebaut und als Paket übertragen.

## Schlagwörter

Aus [Tags und Schlagwörter](tags-und-schlagwoerter.md):

- [ ] Tag-Baum gebaut: Oberbegriffe, Unterbegriffe, Synonyme; wo möglich flach.
- [ ] Entschieden, wie verwandte Begriffe behandelt werden (sie sind keine eingebaute Tag-Relation).
- [ ] Feld für Themen geschlossen; ein eigenes offenes Feld für Schlüsselwörter nur, wenn gewünscht.
- [ ] Besucher haben `tags/read` und `tags/view`; Katalogisierende `add` nur unterhalb ihrer Tags.
- [ ] Themenseiten haben eine Anmerkung zum Umfang.
- [ ] Tags für jede Sprache der Beschreibung übersetzt.

## Einträge hereinholen

- [ ] Weg entschieden: Eingabe, Import per Skript, REST, RSS-Import oder Writer-Import
      ([Katalog planen](katalog-planen.md#wie-einträge-hereinkommen)).
- [ ] Importe verwenden Remote-IDs, damit sie ohne Duplikate erneut laufen können
      ([Inhalte im Code anlegen und aktualisieren](../../../../eng-us/help/creators/app-creators/creating-content-in-code.md)).
- [ ] Eine Stichprobe echter Einträge vor dem vollständigen Import von Katalogisierenden geprüft.
- [ ] Alte Sammlungen veröffentlicht, bevor Feeds angekündigt werden.

## Suche

Aus [Katalogsuche](katalogsuche.md):

- [ ] Durchsuchbare Felder geprüft; Index nach jedem großen Import neu aufgebaut.
- [ ] Das Ergebnis-Template zeigt Zahlen, Blättern und Vorschläge, wenn nichts gefunden wird.
- [ ] Erweiterte Suche angeboten, wenn Leser Filter nach Klasse oder Datum brauchen.
- [ ] Programme zur Textextraktion installiert, wenn Dateiinhalte gefunden werden müssen; Scans vor dem Upload
      durchsuchbar gemacht.
- [ ] Suchstatistiken mit der Ansprechperson für den Datenschutz entschieden.
- [ ] Eine datierte Entscheidung zu Solr (`ezfind`).

## Dateien und digitale Sammlungen

Aus [Dateien und digitale Sammlungen](dateien-und-digitale-sammlungen.md):

- [ ] Material für alle, für Mitglieder und für Mitarbeiter durch Sektionen getrennt; Downloads abgemeldet getestet.
- [ ] Keine direkten Links in `var/.../storage/`.
- [ ] Download-Links zeigen Format und Größe.
- [ ] Standardlizenz für die Installation und pro Eintragsklasse gesetzt; Lizenzen pro Objekt, wo sie abweichen.
- [ ] Upload-Grenzen und Speicherplatz mit dem Hoster geplant.
- [ ] Upload-Formulare für Besucher verwenden `enhancedezbinaryfile` mit einem vernünftigen `MaxFiles` und Spamschutz.

## Exporte und Feeds

Aus [PDF- und Druckexporte](pdf-und-druckexporte.md) und [Feeds für Neuerwerbungen](feeds-fuer-neuerwerbungen.md):

- [ ] Gespeicherte PDF-Exporte enthalten nur öffentliches Material; eingeschränkte Listen werden bei Bedarf erzeugt.
- [ ] Eine TrueType-Schrift eingerichtet, wenn Titel nichtlateinische Schriften verwenden.
- [ ] Eine benannte Person erzeugt gespeicherte Exporte neu.
- [ ] Feeds für neue Einträge aktiv, eine Quelle pro Eintragsklasse, Links auf die öffentliche Website.
- [ ] Autodiscovery-Links im Pagelayout; `/rss/feed/` in `robots.txt` nicht gesperrt.
- [ ] Ein wiederkehrender Newsletter der Neuerwerbungen, falls geplant, mit Einwilligung eingerichtet
      ([Newsletter](../newsletter/README.md)).

## Barrierefreiheit und Leser

- [ ] Templates folgen [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md):
      Landmarks, ein `h1`, Beschriftungen, Kontrast, Zoom auf 200 %.
- [ ] Cover und Scans haben Alternativtexte.
- [ ] Getestet mit [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md).
- [ ] Leserkonten und Bereiche nur für Mitglieder geprüft ([Mitglieder](../mitglieder/README.md)).

## Start

- [ ] Die [Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md) des Website-Erstellers
      bestanden.
- [ ] Backups von Datenbank und Speicher laufen, und eine Wiederherstellung ist getestet
      ([Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md)).
- [ ] Sitemaps erzeugt und angemeldet ([Sitemaps](../../../../eng-us/help/creators/website-creators/sitemaps.md)).
- [ ] Katalogisierende geschult; eine kurze Anleitung für Redakteure geschrieben
      ([Die Website an Redakteure übergeben](../../../../eng-us/help/creators/website-creators/handing-over-to-editors.md)).

## Jeden Monat

- [ ] Suchbegriffe ohne Ergebnisse geprüft; Synonyme oder Einträge ergänzt.
- [ ] Inhaltsprüfungen ausgeführt: nicht verknüpfte Dateien, ungültige Relationen, fehlende Dateien
      ([Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)).
- [ ] Feeds in einem Feedreader geöffnet; gespeicherte PDFs bei Bedarf neu erzeugt.
- [ ] Rollen für Mitarbeiter und Ehrenamtliche geprüft, die gewechselt haben
      ([Zugriffsprüfungen](../../../../eng-us/help/businesses/security/access-reviews.md)).

## Jedes Jahr

- [ ] Vokabular geprüft: Schlagwörter ohne Einträge, Schlagwörter mit sehr vielen.
- [ ] Eintragsklassen mit dem abgeglichen, was Katalogisierende tatsächlich ausfüllen.
- [ ] Die Entscheidung zu Solr mit der Größe des Katalogs abgeglichen.
- [ ] Lizenzen digitaler Objekte mit den Rechteinhabern geprüft.

## Verwandte Seiten

- [Bibliotheken und Kataloge](README.md)
- [Checkliste Go-live](../vorlagen/checkliste-go-live.md)
- [Checkliste Jahresrückblick](../vorlagen/checkliste-jahresrueckblick.md)
- [Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
