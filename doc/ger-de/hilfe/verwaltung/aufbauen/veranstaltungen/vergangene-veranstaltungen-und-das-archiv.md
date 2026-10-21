---
title: Vergangene Veranstaltungen und das Archiv
audience: government, website-creator, admin, editor
category: public-sector
order: 90
summary: Was mit Veranstaltungen nach ihrem Termin auf einer Exponential-Site geschieht - sie in einem Jahresarchiv behalten, sie mit hide.php über die To Time automatisch verstecken, Anmeldedaten entfernen und Ergebnisse wie Aufzeichnungen und Berichte veröffentlichen.
keywords: vergangene Veranstaltungen, Archiv, Archiv nach Jahr, Aufzeichnungen, Ergebnisse, Anmeldedaten entfernen, Aufräumen, past events, archive, hide.php, HideSettings, to_time, event archive by year, recordings, outcomes, sign-up data removal, cleanup
type: help
---
# Vergangene Veranstaltungen und das Archiv

Diese Seite richtet sich an Site-Ersteller, Administratoren und Redakteure, die entscheiden, was mit Veranstaltungen
nach ihrem Termin geschieht. Kalender füllen sich; vergangene Veranstaltungen werden entweder zu einem nützlichen
Nachweis oder zu Ballast. Die genannten Funktionen sind in [Cronjobs](../../../../anleitungen/cronjobs.md) und
[Archiv und Ablauf](../nachrichten-und-warnmeldungen/archiv-und-ablauf.md) dokumentiert.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Behalten, archivieren oder verstecken](#behalten-archivieren-oder-verstecken)
2. [Vergangene Veranstaltungen automatisch verstecken](#vergangene-veranstaltungen-automatisch-verstecken)
3. [Ein Jahresarchiv](#ein-jahresarchiv)
4. [Nach der Veranstaltung](#nach-der-veranstaltung)

## Behalten, archivieren oder verstecken

| Veranstaltung | Üblicherweise |
|---|---|
| Öffentliche Sitzungen (Rat, Beteiligungstermine) | behalten: sie gehören zum Schriftgut; Ergebnisse ergänzen |
| Veranstaltungen für die Gemeinschaft und Kultur | nach dem Termin verstecken, oder ein Jahr im Archiv behalten |
| Schulungen, Tage der offenen Tür | nach dem Termin verstecken |
| Abgesagte Veranstaltungen | nach dem Termin verstecken |

Die Kalenderansichten und Listen „kommender“ Veranstaltungen lassen vergangene Veranstaltungen bereits aus dem
aktuellen Monat und aus nach Beginn gefilterten Listen heraus
([Kommende Veranstaltungen auflisten](kommende-veranstaltungen-auflisten.md)); das Verstecken nimmt sie auch aus Suche und Menüs.

## Vergangene Veranstaltungen automatisch verstecken

Der Cronjob `hide.php` versteckt Knoten, deren Datumsattribut vorbei ist. Verwenden Sie für Veranstaltungen das Ende:

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the Events calendar>
HideDateAttributeList[event]=to_time
```

Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`); das Skript gehört zum globalen Teil der
Cronjobs. Veranstaltungen ohne To Time werden nicht versteckt (der Filter braucht einen Wert). Verstecken Sie
stattdessen nach `from_time`, verschwindet eine Veranstaltung bei ihrem Beginn, was zu früh ist für Veranstaltungen,
die Menschen an diesem Tag noch finden wollen.

Nehmen Sie Veranstaltungen, die Sie behalten möchten, aus, indem Sie sie in einen Kalender legen, der nicht in
`RootNodeList[]` steht, zum Beispiel einen eigenen Kalender „Öffentliche Sitzungen“.

## Ein Jahresarchiv

| Schritt | Wie |
|---|---|
| Legen Sie unter Events einen Ordner „Archiv <Jahr>“ an | zu Jahresbeginn |
| Verschieben Sie die Veranstaltungen des Vorjahres dorthin | markieren Sie sie in der Liste **Unterelemente**, **Weitere Aktionen** > **Ausgewähltes verschieben**; eine große Verschiebung kann als Content-Job laufen ([Inhaltsmodell und Bearbeitung, 8](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#8-große-operationen-als-inhaltsjobs)) |
| Alte Adressen funktionieren weiter | das Verschieben behält Weiterleitungen von den alten Aliasen ([Weiterleitungen und verschobene Inhalte](../../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)) |
| Kennzeichnen Sie sie | ein Hinweis „archiviert“ im Template für Veranstaltungen in Archivordnern |

## Nach der Veranstaltung

| Aufgabe | Wo |
|---|---|
| Ergebnisse ergänzen: eine Zusammenfassung, Präsentationen, eine Aufzeichnung mit Untertiteln | bearbeiten Sie die Veranstaltung, oder verknüpfen Sie einen Bericht |
| Anmeldedaten entfernen | **Setup > Gesammelte Informationen**, die Sammlungen der Veranstaltung, **Ausgewählte entfernen** ([Aufbewahrung gesammelter Informationen](../formulare/aufbewahrung-gesammelter-informationen.md)) |
| Mailkopien der Anmeldungen beim Veranstalter entfernen | das Postfach |
| Fotos von Teilnehmenden | nur mit Einwilligung veröffentlichen, wo Ihre Regeln es verlangen |

## Verwandte Seiten

- [Veranstaltungen](README.md)
- [Absagen und Änderungen](absagen-und-aenderungen.md)
- [Archiv und Ablauf](../nachrichten-und-warnmeldungen/archiv-und-ablauf.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
