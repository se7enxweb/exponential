---
title: Was ein Parlament veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die Parlamente und gesetzgebende Versammlungen typischerweise veröffentlichen - Mitglieder, Tagesordnungen, Gesetzentwürfe, Abstimmungen, Protokolle, Ausschüsse, Petitionen, Informationen für Besucher und zur politischen Bildung - und was jede Art von Exponential braucht.
keywords: Website eines Parlaments, Abgeordnete, Tagesordnung, Gesetzentwürfe, Stadien der Gesetzgebung, Abstimmungen, Plenarprotokolle, Ausschüsse, Petitionen, Umfang, parliament website, members, order paper, agenda, bills, legislation stages, votes, divisions, transcripts, hansard, committees, petitions, scope
type: help
---
# Was ein Parlament veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller eines Parlaments oder einer gesetzgebenden
Versammlung, die den Umfang einer Website festlegen. Sie listet auf, was Parlamente typischerweise veröffentlichen
(allgemeine Praxis im öffentlichen Sektor; die Verfahren unterscheiden sich), und was jede Art von Inhalt braucht.

## Inhalt

1. [Die Inhalte](#die-inhalte)
2. [Was das CMS können muss](#was-das-cms-können-muss)
3. [Woher die Daten kommen](#woher-die-daten-kommen)
4. [Zu klärende Fragen](#zu-klärende-fragen)

## Die Inhalte

| Inhalt | Zielgruppe | Rhythmus |
|---|---|---|
| Mitglieder: öffentliche Rolle, Partei oder Fraktion, Wahlkreis, Ausschüsse, Kontakt | Bürgerinnen und Bürger, Presse | mit Wahlen und Ernennungen |
| Tagesordnungen für Sitzungen des Plenums und der Ausschüsse | Presse, Mitglieder, Öffentlichkeit | täglich während der Sitzungswochen |
| Gesetzentwürfe: Text jeder Fassung, Stadium, Änderungsanträge, Begründungen | Juristinnen und Juristen, Presse, Interessengruppen | wie die Entwürfe voranschreiten |
| Abstimmungen: Ergebnis je Abstimmung und, wo aufgezeichnet, wie jedes Mitglied abgestimmt hat | Presse, Forschende, Bürgerinnen und Bürger | binnen Stunden nach einer Abstimmung |
| Protokolle der Debatten | Forschende, Presse | am Tag danach oder später |
| Ausschüsse: Mitglieder, Untersuchungen, Anhörungen, Berichte | Interessengruppen | laufend |
| Petitionen, Anfragen, schriftliche Antworten | Bürgerinnen und Bürger | laufend |
| Live- und aufgezeichnetes Video der Sitzungen | Öffentlichkeit | während der Sitzungen |
| Besuche, politische Bildung, wie das Parlament funktioniert | Schulen, Öffentlichkeit | stabil |
| Verwaltung: Haushalt, Aufwendungen der Mitglieder, soweit vorgeschrieben, Stellen | Aufsicht, Presse | jährlich |

## Was das CMS können muss

| Bedarf | Exponential | Lesen |
|---|---|---|
| Gesetzentwürfe mit ihren Fassungen, Abstimmungen und Ausschüssen verknüpft | Beziehungen zwischen Objekten | [Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md) |
| Seiten der Mitglieder überall wiederverwendet, wo ein Mitglied erscheint | eine Klasse Mitglied, verknüpft aus Abstimmungen und Ausschüssen | [Inhaltsmodell für Parlamente](inhaltsmodell.md) |
| Amtliche Dokumente nach der Veröffentlichung unverändert | Versionen, das Audit-Protokoll, eine eingeschränkte Rolle für Berichtigungen | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) |
| Listen nach Datum (Tagesordnungen, Abstimmungen) | Datumsattribute und Fetches; RSS-Exporte | [RSS-Feeds](../../../../anleitungen/rss-feeds.md) |
| Last bei wichtigen Abstimmungen | Caches und Velocity | [Leistung und Caching](../../../../../eng-us/help/creators/website-creators/performance-and-caching.md) |
| Daten zur Weiterverwendung | Datendateien als Objekte der Klasse File; REST-API | [REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md) |
| Mehrere Amtssprachen | Übersetzungen je Objekt | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |

## Woher die Daten kommen

| Inhalt | Übliche Quelle | Der Anteil der Website |
|---|---|---|
| Gesetzentwürfe und ihre Texte | ein System für Gesetzgebung oder Vorgangsverfolgung | die Dokumente veröffentlichen; auf den eigenen Datensatz des Systems verlinken |
| Abstimmungen | ein elektronisches Abstimmungssystem oder die Aufzeichnung der Parlamentsverwaltung | das Ergebnis und die Aufzeichnung als Dokument veröffentlichen; die Stimmen je Mitglied als Tabelle oder Datendatei |
| Protokolle | der Stenografische Dienst | von ihm erzeugte Dokumente oder HTML veröffentlichen |
| Video | eine Streaming-Plattform | verlinken oder einbetten; Untertitel vom Anbieter |

Exponential importiert Inhalte über seine REST-API, über Pakete oder Skripte; ein parlamentarisches System anzubinden ist
eine Entwicklungsaufgabe. Hat ein Quellsystem eine eigene öffentliche Schnittstelle, verlinken Sie darauf, statt alles zu
kopieren.

## Zu klärende Fragen

- [ ] Welches System ist für jede Art von Aufzeichnung maßgeblich, und ist die Website eine Kopie oder die amtliche
  Veröffentlichung?
- [ ] Wie bald nach einer Abstimmung muss das Ergebnis online sein?
- [ ] Welche Daten werden zur Weiterverwendung veröffentlicht, in welchen Formaten, unter welcher Lizenz?
- [ ] Wie werden die Seiten der Mitglieder nach einer Wahl aktualisiert?

## Verwandte Seiten

- [Parlamente](README.md)
- [Tagesordnungen, Gesetzentwürfe und Abstimmungsergebnisse](tagesordnungen-gesetzentwuerfe-und-abstimmungsergebnisse.md)
- [Ihre Website planen](../../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
