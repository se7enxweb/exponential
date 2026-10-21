---
title: Sitzungsunterlagen, Tagesordnungen und Protokolle
audience: government, editor, website-creator
category: public-sector
order: 120
summary: Wie Sie mit Exponential die Sitzungen von Räten, Gremien und Ausschüssen veröffentlichen - eine Sitzungsklasse mit Datum, Gremium, Tagesordnung, Unterlagen und Protokoll, eine Struktur pro Gremium und Jahr, rechtzeitige Veröffentlichung, Berichtigungen und Sprachfassungen.
keywords: Sitzungen, Tagesordnung, Protokoll, Niederschrift, Rat, Gemeinderat, Gremium, Ausschuss, Sitzungsunterlagen, öffentliche Sitzungen, Beschlüsse, Transparenz, Sitzungskalender, meetings, agenda, minutes, council, board, committee, meeting papers, public meetings, decisions, transparency, schedule
type: help
---
# Sitzungsunterlagen, Tagesordnungen und Protokolle

Diese Seite richtet sich an Sitzungsdienste, Redaktionen und Websiteersteller, die die Sitzungen eines Rates, Gremiums
oder Ausschusses veröffentlichen: Ankündigungen, Tagesordnungen, Unterlagen und Protokolle. Viele Rechtsordnungen
legen Fristen fest, Tagesordnungen vor einer Sitzung und Protokolle danach zu veröffentlichen; diese Regeln kommen aus
Ihrem Recht. In Exponential ist jede Sitzung ein Inhaltsobjekt mit ihren Unterlagen darunter.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Eine Struktur pro Gremium und Jahr](#eine-struktur-pro-gremium-und-jahr)
2. [Eine Sitzungsklasse](#eine-sitzungsklasse)
3. [Vor der Sitzung](#vor-der-sitzung)
4. [Nach der Sitzung](#nach-der-sitzung)
5. [Berichtigungen](#berichtigungen)
6. [Sprachen und Barrierefreiheit](#sprachen-und-barrierefreiheit)

## Eine Struktur pro Gremium und Jahr

```
Meetings
  Council
    2026
      2026-03-12 Council meeting
        Agenda (file)
        Report on item 4 (file)
        Minutes (file, after the meeting)
  Planning committee
    2026
```

Alte Aliasse leiten weiter, wenn Ordner umbenannt werden, sodass die Struktur ohne defekte Links wachsen kann
([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)).

## Eine Sitzungsklasse

| Attribut | Datentyp | Hinweis |
|---|---|---|
| Titel | Textzeile | Namensmuster, zum Beispiel „Ratssitzung“ |
| Gremium | Auswahl | Rat, Planungsausschuss ... |
| Datum und Uhrzeit | Datum und Zeit | |
| Ort | Textzeile | oder „online“ mit dem Linkfeld unten |
| Öffentlicher Link (Stream) | URL | wenn Sitzungen gestreamt werden |
| Status | Auswahl | Angesetzt, Tagesordnung veröffentlicht, Stattgefunden, Protokoll genehmigt, Abgesagt |
| Tagesordnung | Formatierter Text | die Punkte, oder die Tagesordnung als Datei darunter |
| Zusammenfassung des Protokolls | Formatierter Text | wird nach der Sitzung ausgefüllt |

Machen Sie die Klasse zu einem Container, damit die Unterlagen Unterobjekte sind. Ein Muster für den Objektnamen wie
`<title>` mit dem Datum im Titel, denn der Name wird zur Adresse.

## Vor der Sitzung

1. Legen Sie das Sitzungsobjekt an, sobald das Datum feststeht; Status *Angesetzt*.
2. Veröffentlichen Sie Tagesordnung und Unterlagen bis zur gesetzlichen Frist; Status *Tagesordnung veröffentlicht*.
   Wenn Unterlagen zu einem genauen Zeitpunkt erscheinen müssen, verwenden Sie das Workflow-Ereignis *Bis Datum
   warten* (siehe „Zeitgesteuertes Veröffentlichen und Sperrfristen“).
3. Nicht öffentliche Unterlagen (ausgenommene Punkte) werden nicht auf die öffentliche Website hochgeladen; führen Sie
   den Punkt mit dem Grund der Ausnahme auf.
4. Ein RSS-Export des Ordners „Sitzungen“ mit Unterknoten informiert Abonnenten über neue Tagesordnungen
   ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).

## Nach der Sitzung

1. Laden Sie das Protokoll im Entwurf oder in der genehmigten Fassung hoch, wie Ihre Regeln es verlangen, und setzen Sie
   den Status.
2. Tragen Sie die Beschlüsse in das Beschlussregister ein und verlinken Sie sie (siehe „Beschlussregister“).
3. Bewahren Sie Aufzeichnungen und Mitschriften nach Ihrer Schriftgutrichtlinie auf.

## Berichtigungen

Wenn das genehmigte Protokoll vom Entwurf abweicht, veröffentlichen Sie die genehmigte Datei als neues Dateiobjekt
(„Protokoll, genehmigt am <Datum>“), statt den Entwurf stillschweigend zu überschreiben, oder ersetzen Sie ihn und fügen
Sie einen datierten Hinweis hinzu. Die Versionsgeschichte behält die frühere Datei für die Mitarbeitenden
([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)).

## Sprachen und Barrierefreiheit

- Auf einer Website mit mehreren Amtssprachen übersetzen Sie das Sitzungsobjekt; Unterlagen in jeder Sprache sind
  getrennte Dateien, oder eine Datei pro Sprache unter der Sitzung
  ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)).
- Stellen Sie Unterlagen als barrierefreie Dokumente oder als Seitentext bereit; gescannte PDFs ohne Text sind nicht
  barrierefrei. Der Stand der Barrierefreiheit der Website ist in den
  [Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md) dokumentiert.

## Verwandte Seiten

- [Beschlussregister](beschlussregister.md)
- [Zeitgesteuertes Veröffentlichen und Sperrfristen](zeitgesteuertes-veroeffentlichen-und-sperrfristen.md)
- [Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
