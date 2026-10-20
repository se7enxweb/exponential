---
title: Was eine Kommune veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die Städte, Gemeinden und Räte typischerweise veröffentlichen - Dienstleistungsseiten, Ratsangelegenheiten, Bekanntmachungen, Nachrichten, örtliche Informationen, Veranstaltungen - und was jede Art von Exponential braucht.
keywords: Website einer Kommune, Website des Rats, Dienstleistungen, Abfallabfuhr, Genehmigungen, Ratssitzungen, öffentliche Bekanntmachungen, örtliche Nachrichten, Veranstaltungen, Umfang, municipal website, council website, services, waste collection, permits, council meetings, public notices, local news, events, scope
type: help
---
# Was eine Kommune veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller einer Stadt, Gemeinde oder eines Rats, die den
Umfang einer Website festlegen. Sie listet auf, was die Kommunalverwaltung typischerweise veröffentlicht (allgemeine
Praxis im öffentlichen Sektor), und was jede Art von Inhalt braucht.

## Inhalt

1. [Dienstleistungen](#dienstleistungen)
2. [Ratsangelegenheiten](#ratsangelegenheiten)
3. [Bekanntmachungen, Nachrichten und Warnungen](#bekanntmachungen-nachrichten-und-warnungen)
4. [Örtliche Informationen und Veranstaltungen](#örtliche-informationen-und-veranstaltungen)
5. [Was das CMS können muss](#was-das-cms-können-muss)
6. [Zu klärende Fragen](#zu-klärende-fragen)

## Dienstleistungen

Die meisten Besuche einer kommunalen Website betreffen eine Dienstleistung. Eine typische Liste von Leistungsbereichen:
Abfall und Recycling, Straßen und Parken, Wohnen, Planungs- und Baugenehmigungen, örtliche Steuern und Zahlungen,
Personenstandswesen (Geburten, Eheschließungen, Sterbefälle), soziale Hilfen, Schulen und Kinderbetreuung,
Bibliotheken, Sport und Freizeit, Friedhöfe, Genehmigungen für Gewerbe und Veranstaltungen.

Jede Dienstleistungsseite beantwortet dieselben Fragen: was es ist, wer es nutzen kann, was es kostet, wie man es
beantragt, wie lange es dauert, was danach geschieht, an wen man sich wendet. Eine Klasse Dienstleistung mit diesen
Feldern hält die Seiten einheitlich (siehe [Inhaltsmodell](inhaltsmodell.md)). Der eigentliche Vorgang (beantragen,
bezahlen, buchen) geschieht meist in einem eigenen System, auf das die Seite verlinkt.

## Ratsangelegenheiten

| Inhalt | Rhythmus |
|---|---|
| Rats- und Ausschussmitglieder (ihre öffentliche Rolle) | nach jeder Wahl |
| Sitzungskalender | je Jahr |
| Tagesordnungen und Sitzungsvorlagen | Tage vor jeder Sitzung, Fristen oft gesetzlich festgelegt |
| Niederschriften | nach der Genehmigung in der nächsten Sitzung |
| Beschlüsse | nach jeder Sitzung |
| Petitionen, Fragen aus der Öffentlichkeit | je Sitzung |

Die Einzelheiten stehen unter [Ratssitzungen und örtliche Dienstleistungen](ratssitzungen-und-oertliche-dienstleistungen.md).

## Bekanntmachungen, Nachrichten und Warnungen

| Inhalt | Lebensdauer |
|---|---|
| Öffentliche Bekanntmachungen (Bauanträge, Straßensperrungen, amtliche Bekanntmachungen) | ein fester Zeitraum, dann verborgen oder archiviert |
| Nachrichten | Wochen |
| Dringende Warnungen (Unwetter, Wasserversorgung, Störungen von Dienstleistungen) | Stunden bis Tage |
| Änderungen von Abfuhrtagen, Öffnungszeiten | bis zur nächsten Änderung |

Exponential hat kein eigenes Warnbanner. Eine Warnung ist meist ein Objekt in einem Ordner, den das Template der
Startseite auflistet, wenn er nicht leer ist, oder ein Block im Layout-Editor, den die Redaktion füllt und leert
([Der Layout-Editor](../../../layout-editor.md)). Der Cronjob `hide.php` kann Bekanntmachungen verbergen, deren
Enddatum vorbei ist ([Cronjobs](../../../../anleitungen/cronjobs.md)).

## Örtliche Informationen und Veranstaltungen

Öffnungszeiten von Ämtern, Parks, Einrichtungen, Ortsgeschichte, Karten, Informationen zu Wahlen,
Veranstaltungskalender. Veranstaltungen decken die mitgelieferten Klassen Event und Event calendar ab, deren
Kalenderansicht mit dem Design ezwebin kommt (`event_view_calendar.tpl`, `event_view_program.tpl`); eine Website mit
einem anderen Design braucht eigene Templates.

## Was das CMS können muss

| Bedarf | Exponential | Lesen |
|---|---|---|
| Einheitliche Dienstleistungsseiten | eine Klasse Dienstleistung mit Pflichtfeldern | [Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) |
| Fachbereiche bearbeiten nur ihre Seiten | Sektionen und Rollen | [Sektionen](../../../../anleitungen/sektionen.md) |
| Formulare für Meldungen und Anträge | der Informationssammler, mit CSRF-Formular-Tokens und optionalem CAPTCHA | [Formulare mit dem Informationssammler](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md) |
| Bekanntmachungen, die ablaufen | Cronjobs `hide.php` / `unpublish.php` | [Cronjobs](../../../../anleitungen/cronjobs.md) |
| Die Einwohnerschaft verfolgt Nachrichten | RSS-Exporte, Benachrichtigungen, Newsletter | [RSS-Feeds](../../../../anleitungen/rss-feeds.md), [Newsletter mit CJW Newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md) |
| Eine Dienstleistung schnell finden | Suche auf der Website und gute Titel | [Suche auf der Website](../../../../../eng-us/help/creators/website-creators/site-search.md) |

## Zu klärende Fragen

- [ ] Welche zehn Dienstleistungen bringen die meisten Besuche? Bauen Sie diese Seiten zuerst und am besten.
- [ ] Woher kommen die Ratsdokumente (ein Ratsinformationssystem?), und wer lädt sie hoch?
- [ ] Wer darf nachts oder am Wochenende eine dringende Warnung veröffentlichen?
- [ ] Welche Sprachen braucht die Einwohnerschaft über die Amtssprache hinaus?

## Verwandte Seiten

- [Kommunen](README.md)
- [Inhaltsmodell für Kommunen](inhaltsmodell.md)
- [Ihre Website planen](../../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
