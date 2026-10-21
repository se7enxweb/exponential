---
title: Was eine nationale Regierung veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Arten von Inhalten, die Ministerien und zentrale Ressorts typischerweise veröffentlichen, wer sie liest und was jede von einem Content-Management-System verlangt, zugeordnet zu der Funktion von Exponential, die darauf antwortet.
keywords: Website eines Ministeriums, Politikseiten, Pressemitteilung, Publikationen, Konsultation, Reden, Statistiken, Informationen über die Organisation, Inhalte der nationalen Regierung, Umfang, ministry website, policy pages, press release, publications, consultation, speeches, statistics, corporate information, national government content, scope
type: help
---
# Was eine nationale Regierung veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller in einem Ministerium oder zentralen Ressort,
die den Umfang einer Website festlegen. Sie listet die Inhalte auf, die nationale Regierungen typischerweise
veröffentlichen (allgemeine Praxis im öffentlichen Sektor), und für jeden, was er vom Content-Management-System verlangt
und wo Exponential es bietet.

## Inhalt

1. [Die Inhalte](#die-inhalte)
2. [Was das CMS können muss](#was-das-cms-können-muss)
3. [Umfang und Rhythmus](#umfang-und-rhythmus)
4. [Vor dem Bauen zu klärende Fragen](#vor-dem-bauen-zu-klärende-fragen)

## Die Inhalte

| Inhalt | Typische Zielgruppe | Typischer Rhythmus |
|---|---|---|
| Politik- und Themenseiten: was das Ministerium zu einem Thema tut | Bürgerinnen und Bürger, Medien, Interessengruppen | geändert, wenn sich die Politik ändert |
| Nachrichten und Pressemitteilungen | Medien, Bürgerinnen und Bürger | täglich bis wöchentlich, manchmal dringend |
| Reden und Erklärungen von Amtsträgerinnen und Amtsträgern | Medien, Forschende | am selben Tag |
| Publikationen: Berichte, Strategien, Weißbücher, Leitfäden | Fachleute, Forschende | wöchentlich bis monatlich, oft als PDF |
| Konsultationen: ein Vorschlag, die Fragen, das Antwortformular und später das Ergebnis | Interessengruppen, Bürgerinnen und Bürger | einige im Jahr, mit festen Beginn- und Enddaten |
| Dienstleistungen: wie man beantragt, wer berechtigt ist, Links zum Onlinevorgang | Bürgerinnen und Bürger und Unternehmen | stabil, regelmäßig überprüft |
| Informationen über die Organisation: Aufbau, Leitung, Haushalt, Jahresbericht, Kontakt | alle, Aufsichtsstellen | jährlich und bei Änderungen |
| Transparenzmaterial: Offenlegungsverzeichnisse, Ausgaben, Verträge | Medien, Kontrollorganisationen | nach festem Zeitplan |
| Stellen- und Vergabebekanntmachungen | Stellensuchende, Lieferanten | laufend, mit Fristen |
| Veranstaltungen und Kampagnen | Zielgruppen | saisonal |

## Was das CMS können muss

| Bedarf | Funktion von Exponential | Lesen |
|---|---|---|
| Viele Redakteurinnen und Redakteure, jede und jeder auf die eigene Einheit beschränkt | Sektionen und Rollen mit Einschränkungen nach Sektion | [Sektionen](../../../../anleitungen/sektionen.md), [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) |
| Ein zweites Augenpaar vor dem Veröffentlichen | Ein Ereignis *Freigeben* in einem Workflow beim Veröffentlichen | [Workflows](../../../../anleitungen/workflows.md) |
| Verlauf jedes veröffentlichten Textes | Versionen mit Vergleich und neuem Entwurf aus einer alten Version; das Audit-Protokoll | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md), [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md) |
| Publikationen als Dokumente | Die Klasse File (`ezbinaryfile`), Beziehungen von Seiten zu Dateien, PDF-Exporte eines Teilbaums | [PDF-Exporte](../../../../anleitungen/pdf-exporte.md) |
| Mehrere Amtssprachen | Inhaltssprachen und Übersetzungen je Objekt | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| Konsultationen mit Antworten | Der Informationssammler und **Setup > Gesammelte Informationen** | [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md) |
| Nachrichtenfeeds für Medien und andere Websites | RSS-Exporte | [RSS-Feeds](../../../../anleitungen/rss-feeds.md) |
| Benachrichtigungen an Abonnenten | Das Benachrichtigungssystem und die E-Mail-Einstellungen | [Benachrichtigungen](../../../../anleitungen/benachrichtigungen-administrator.md) |
| Themen quer über Einheiten | Tags mit eztags | [Verschlagworten mit eztags](../../../ersteller/website-ersteller/verschlagworten-mit-eztags.md) |
| Suche über eine große Website | Eingebaute Suche oder eZ Find mit Solr | [Suche mit eZ Find und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md) |
| Lastspitzen | Ansichts-Cache, statischer Cache, Antwort-Cache von Velocity | [Leistung und Caching](../../../ersteller/website-ersteller/leistung-und-caching.md) |

## Umfang und Rhythmus

Nationale Websites wachsen meist, ohne dass jemand es entscheidet. Jede Einheit fügt Seiten hinzu; wenige werden
entfernt. Planen Sie:

- **Verantwortung**: Jede Seite hat eine verantwortliche Einheit. Sektionen halten die Einheit fest; auch der Eigentümer
  eines Objekts wird aufgezeichnet ([Eigentümer ändern](../../../../anleitungen/eigentuemer-aendern.md)).
- **Überprüfungsdaten**: Exponential hat keine eingebaute Erinnerung „überprüfen bis“. Ein übliches Muster ist ein
  Datumsattribut (`ezdate`) an der Klasse und eine Liste der Seiten nach ihrem Datum für jede Einheit, erstellt mit einem
  Template-Fetch oder einer gespeicherten Suche und in einer monatlichen Redaktionssitzung geprüft.
- **Archivieren statt löschen**: Ersetzte Publikationen bleiben meist verfügbar, mit einer klaren Kennzeichnung
  „zurückgezogen“ oder „ersetzt“. Objektzustände können diese Kennzeichnung tragen, und Rollen können sie beachten
  ([Objektzustände](../../../../anleitungen/objektzustaende.md)).
- **Dringendes Veröffentlichen**: Eine Erklärung einer Ministerin oder eines Ministers muss vielleicht innerhalb von
  Minuten hinausgehen. Entscheiden Sie im Voraus, wer ohne Freigabe veröffentlichen darf, und geben Sie dieser kleinen
  Gruppe eine Rolle, deren Veröffentlichungen außerhalb des Freigabe-Workflows liegen (das Ereignis *Freigeben* kann
  Benutzergruppen ausschließen).

## Vor dem Bauen zu klärende Fragen

- [ ] Welche Einheiten veröffentlichen, und wer gibt für jede frei?
- [ ] Welche Sprachen sind Amtssprachen, und muss jede Seite in jeder vorliegen, bevor sie veröffentlicht wird?
- [ ] Welche Inhalte müssen aufbewahrt werden, wie lange und von wem (Schriftgutverwaltung)?
- [ ] Welche Dienstleistungen werden anderswo als Vorgang abgewickelt und von der Website nur verlinkt?
- [ ] Wer ist für die Barrierefreiheit von Dokumenten verantwortlich, die Einheiten hochladen, nicht nur von Webseiten?
- [ ] Welcher Verkehr wird an einem normalen Tag und am geschäftigsten Tag der letzten Jahre erwartet?

Die Antworten fließen in das Inhaltsmodell, den Plan für Sektionen und Rollen und die Anforderungen auf den nächsten
Seiten ein.

## Verwandte Seiten

- [Nationale Regierung](README.md)
- [Inhaltsmodell für die nationale Regierung](inhaltsmodell.md)
- [Ihre Website planen](../../../ersteller/website-ersteller/ihre-website-planen.md)
- [Sitemap und Inhaltsinventar](../../../ersteller/website-ersteller/sitemap-und-inhaltsbestandsaufnahme.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
