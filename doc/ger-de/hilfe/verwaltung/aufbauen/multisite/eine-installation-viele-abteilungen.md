---
title: Eine Installation, viele Abteilungen
audience: government, project-manager, admin
category: public-sector
order: 20
summary: Wie eine Behörde die Sites mehrerer Abteilungen, nachgeordneter Behörden oder Programme aus einer Exponential-Installation betreibt - die Formen einer Multisite, was geteilt und was getrennt ist, und die Abwägungen, die in den Plan gehören.
keywords: Multisite, Abteilungen, Behörden, eine Installation, mehrere Sites, Siteaccess, gemeinsame Inhalte, Abwägungen, zentrales Webteam, Portal, multisite, departments, agencies, one installation, several sites, siteaccess, shared content, trade-offs, central web team, portal
type: help
---
# Eine Installation, viele Abteilungen

Diese Seite richtet sich an Führungskräfte und Administratoren, die mehrere Sites (Abteilungen, nachgeordnete Behörden,
Programme, Kampagnen) auf einer Installation betreiben. Jede Site ist ein oder mehrere **Siteaccesses**: ein Name, seine
Einstellungen in `settings/siteaccess/<name>/` und eine Regel, auf welche Anfragen er antwortet
([Mehrere Sites in einer Installation einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md)).

## Inhalt

1. [Formen](#formen)
2. [Was geteilt und was getrennt ist](#was-geteilt-und-was-getrennt-ist)
3. [Abwägungen](#abwägungen)
4. [Wann getrennte Installationen besser sind](#wann-getrennte-installationen-besser-sind)

## Formen

| Form | Wie |
|---|---|
| Getrennte Inhalte je Abteilung | ein Ordner je Site unter der Wurzel; jeder Siteaccess verwendet seinen Ordner als Wurzel |
| Gemeinsame Inhalte, verschiedene Designs | dieselbe Wurzel, ein anderes `SiteDesign` je Siteaccess |
| Eine Site in mehreren Sprachen | ein Siteaccess je Sprache (siehe den [Bereich Sprachen](../../sprachen/README.md)) |
| Jede Site in einer eigenen Erweiterung | Einstellungen, die nur gelten, während die Siteaccesses der Site laufen |

Das Standard-Sitepaket `sevenx_multisite` ist selbst eine Multisite-Installation.

## Was geteilt und was getrennt ist

| Geteilt | Getrennt je Site |
|---|---|
| Kernel, Erweiterungen, Updates | Einstellungen des Siteaccess, Adresse, `SiteName` |
| die Datenbank und der Inhaltsbaum | der Ordner der Site (Wurzelknoten) |
| Benutzer und Rollen | das Design (`SiteDesign`) und seine Templates |
| Medienbibliothek, Suchindex | Sprachen (`SiteLanguageList[]`) |
| eine Administration (oder mehrere) | Logs und Caches, wenn je Site konfiguriert |

## Abwägungen

| Stärke | Einschränkung |
|---|---|
| ein Kernel und ein Satz Erweiterungen zum Aktualisieren und Absichern | jede Site bewegt sich bei einem Upgrade mit den anderen |
| gemeinsame Inhalte und Medien, ein Suchindex | eine stark belastete Site verlangsamt die anderen auf derselben Maschine |
| eine Sicherung | eine Wiederherstellung stellt alle Sites wieder her |
| ein Ort für Rollen und das Audit-Protokoll | Administratoren einer Abteilung können, wenn Rollen es erlauben, andere sehen |

Schreiben Sie die Abwägung in den Plan
([Multisite- und mehrsprachige Projekte planen](../../../fuehrung/multisite-und-mehrsprachigkeit-planen.md)).

## Wann getrennte Installationen besser sind

- Eine Abteilung muss rechtlich oder nach Richtlinie getrennt sein (eigener Verantwortlicher für die Daten, eigene
  Sicherheitszone).
- Die Release-Zeitpläne müssen sich unterscheiden.
- Die Last einer Site würde den anderen schaden und lässt sich nicht über Server trennen.

## Verwandte Seiten

- [Eine Abteilungs-Site hinzufügen](eine-abteilungs-site-hinzufuegen.md)
- [Mehrere Sites einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
