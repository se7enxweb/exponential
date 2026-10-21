---
title: Nachrichten und Warnmeldungen
audience: government, website-creator, project-manager, admin
category: public-sector
order: 10
summary: Index der Anleitungen zum Veröffentlichen von Nachrichten, Pressemitteilungen und dringenden Bekanntmachungen auf einer Exponential-Site des öffentlichen Sektors und zum Erreichen von Menschen mit RSS, Benachrichtigungen und Newslettern, zum Archivieren alter Elemente und zum Beschreiben von Seiten für das Teilen.
keywords: Nachrichten, Warnmeldungen, dringende Bekanntmachungen, Pressemitteilungen, RSS, Benachrichtigungen, Newsletter, Archiv, Ablauf, Metadaten, Teilen in sozialen Netzwerken, news, alerts, urgent notices, press releases, notifications, newsletters, archive, expiry, metadata, Open Graph, social sharing
type: help
---
# Nachrichten und Warnmeldungen

Dieser Ordner richtet sich an Site-Ersteller, Projektleiter und Administratoren, die den Nachrichtenteil einer Site des
öffentlichen Sektors einrichten: Nachrichtenartikel, Pressemitteilungen, dringende Bekanntmachungen und die Kanäle, die
sie zu den Menschen bringen (RSS, Benachrichtigungsmails, Newsletter), dazu, was mit Elementen geschieht, wenn sie alt
sind. Exponential hat kein eigenes Warnbanner und kein Sendesystem; dringende Bekanntmachungen werden aus Inhalten,
Templates und Caches gebaut, wie die Seiten erklären.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Kanäle wählen](#kanäle-wählen)

## Die Seiten

| Seite | Die Antwort in einer Zeile |
|---|---|
| [Nachrichtenartikel](nachrichtenartikel.md) | Die Klasse Article, ein Nachrichtenordner je Jahr, Listen auf der Startseite, Daten und Bilder |
| [Das Muster für dringende Bekanntmachungen](das-muster-fuer-dringende-bekanntmachungen.md) | Ein Ordner für Bekanntmachungen, den die Startseite zeigt, wenn er nicht leer ist, mit Endzeit und Cache-Erneuerung |
| [RSS-Feeds](rss-feeds.md) | RSS-Exporte von Nachrichten und Bekanntmachungen unter **Setup > RSS**, Formate, Caching, Importe |
| [Benachrichtigungen und Abonnements](benachrichtigungen-und-abonnements.md) | Menschen mit Konto folgen Ordnern mit **Benachrichtigen**; sofort oder als Zusammenfassung |
| [Newsletter](newsletter.md) | cjw_newsletter für eine regelmäßige Nachrichtenzusammenfassung, mit Double-Opt-in und Abmeldung mit einem Klick |
| [Pressemitteilungen](pressemitteilungen.md) | Eine Klasse für Pressemitteilungen, Sperrfrist mit einem Ereignis **Bis Datum warten**, Pressekontakte als Rollen |
| [Archiv und Ablauf](archiv-und-ablauf.md) | `hide.php` und `unpublish.php`, Archive nach Jahr, was zu behalten ist |
| [Metadaten und Teilen in sozialen Netzwerken](metadaten-und-teilen-in-sozialen-netzwerken.md) | Titel, Beschreibungen und Open-Graph-Bilder mit xrowmetadata, ohne Tracker von Drittanbietern |

## Lesereihenfolge

1. **Nachrichtenartikel** und **Das Muster für dringende Bekanntmachungen**.
2. **RSS-Feeds**, **Benachrichtigungen und Abonnements** und **Newsletter**.
3. **Pressemitteilungen**, wenn Sie eine Pressestelle haben.
4. **Archiv und Ablauf** und **Metadaten und Teilen in sozialen Netzwerken**.

Nach oben: [Eine Site für den öffentlichen Sektor aufbauen](../README.md), der Index aller Anleitungen zum Aufbauen,
und [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md), der Anfang des Bereichs für Behörden. Warnmeldungen für
bestimmte Arten von Organisationen stehen in
[Warnmeldungen in vielen Sprachen und in einfacher Sprache](../../../../../eng-us/help/government/levels/public-health/alerts-multilingual-and-plain-language.md)
und
[Warnmeldungen, Verfügbarkeit, Wartungsmodus und statischer Cache](../../../../../eng-us/help/government/levels/emergency-services/alerts-availability-maintenance-and-static-cache.md).

## Kanäle wählen

| Kanal | Wer ihn erhält | Braucht | Geschwindigkeit |
|---|---|---|---|
| Die Website | alle, die nachsehen | nichts | bei Veröffentlichung (durch die Caches) |
| RSS | Feedreader, andere Sites, Medien | einen RSS-Export | wenn der Reader abfragt |
| Benachrichtigungsmails | Kontoinhaber, die einem Ordner folgen | Konten, den Benachrichtigungs-Cronjob | beim nächsten Benachrichtigungslauf, oder als Zusammenfassung |
| Newsletter | Abonnenten einer Liste | cjw_newsletter und seine Cronjobs | wenn eine Ausgabe gesendet wird |
| Soziale Medien, Warnsysteme | deren Zielgruppen | außerhalb von Exponential | ihre eigene |

Nichts davon ist ein System zur Warnung der Bevölkerung im Notfall. Für Warnungen an alle verwenden Sie die Warnsysteme
Ihres Landes.

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](../leistungskatalog/README.md)
- [Anleitung zu RSS-Feeds](../../../../anleitungen/rss-feeds.md)
- [Benachrichtigungen für Administratoren](../../../../anleitungen/benachrichtigungen-administrator.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
