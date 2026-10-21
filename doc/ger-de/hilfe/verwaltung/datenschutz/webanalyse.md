---
title: Webanalyse und Besucherstatistiken
audience: government, project-manager, admin
category: public-sector
order: 220
summary: Für öffentliche Stellen, die die Nutzung ihrer Website messen - was Exponential selbst aufzeichnet (Suchstatistiken, Aufrufzähler, Anfrageprotokolle), was die Erweiterung bcwebsitestatistics an Google Analytics sendet, was das für Einwilligung und Datenschutzerklärung bedeutet, und weniger eingreifende Alternativen.
keywords: Webanalyse, Besucherstatistiken, Google Analytics, bcwebsitestatistics, Urchin, Tag Manager, Aufrufzähler, Suchstatistiken, Einwilligung, Datenschutzerklärung, selbst betriebene Webanalyse, CSP, öffentlicher Sektor, analytics, visitor statistics, consent, privacy notice
type: help
---
# Webanalyse und Besucherstatistiken

Diese Seite richtet sich an Projektleitungen, Kommunikationsteams und Datenschutzbeauftragte. Öffentliche Stellen
müssen wissen, welche Dienste die Menschen nutzen und was sie nicht finden; zugleich müssen sie die Privatsphäre der
Besucher achten. Exponential bietet einige eigene Statistiken; ein vollständiger Analysedienst ist eine Erweiterung
und ein Drittanbieter ([Besucherstatistiken und Webanalyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was Exponential selbst aufzeichnet](#was-exponential-selbst-aufzeichnet)
2. [bcwebsitestatistics und Google Analytics](#bcwebsitestatistics-und-google-analytics)
3. [Schritte für den Datenschutz](#schritte-für-den-datenschutz)
4. [Verwandte Seiten](#verwandte-seiten)

## Was Exponential selbst aufzeichnet

| Quelle | Was | Personenbezogene Daten |
|---|---|---|
| Suchstatistiken | Suchbegriffe, Anzahl, durchschnittliche Trefferzahl; **standardmäßig aus** (`LogSearchStats`) | Suchbegriffe können Namen enthalten |
| Aufrufzähler | Seitenaufrufe pro Knoten, gezählt vom Cronjob-Teil `updateviewcount` aus dem Zugriffsprotokoll | nichts pro Besucher gespeichert |
| Anfrageprotokolle | „Nicht gefunden“- und Angriffsanfragen, maskiert | maskierte Adressen |
| Prüfprotokoll | Tätigkeiten in der Administration | Daten der Beschäftigten |

Diese Quellen beantworten betriebliche Fragen; sie sind kein Analysedienst.

## bcwebsitestatistics und Google Analytics

Die Erweiterung fügt das Google-Analytics-Skript in die Seiten ein und kann Shop-Bestellungen über den Workflow-Typ
`ezreceipt` melden ([bcwebsitestatistics](../../../../eng-us/features/6.0/extensions/bcwebsitestatistics.md)). Die Einstellungen
stehen in `settings/override/bcwebsitestatistics.ini.append.php`; setzen Sie `Urchin` immer auf Ihre eigene Property.
Laut der Datenschutzerklärung des Projekts hat sie **keine eigene Einwilligungsabfrage**.

## Schritte für den Datenschutz

1. Legen Sie Zweck und Rechtsgrundlage mit Ihrer bzw. Ihrem Datenschutzbeauftragten fest.
2. Laden Sie das Skript erst nach der Einwilligung, wo eine Einwilligung nötig ist (siehe „Cookie-Banner und
   Einwilligung“).
3. Konfigurieren Sie den Dienst für möglichst wenige Daten (mit seinen eigenen Einstellungen, etwa dem Kürzen von
   Adressen, wo er das anbietet).
4. Nennen Sie den Dienst, die Daten und den Empfänger in der Datenschutzerklärung.
5. Erlauben Sie unter einer Content Security Policy die Hosts des Dienstes in `script-src` und `connect-src` für den
   öffentlichen Siteaccess, und verschieben Sie jedes Inline-Snippet in eine Datei.
6. Prüfen Sie jährlich neu, ob die Zahlen tatsächlich genutzt werden.

Alternativen: selbst betriebene Webanalyse, zusammengefasste Serverprotokolle und die eigenen Suchstatistiken von
Exponential (regelmäßig zurückgesetzt).

## Verwandte Seiten

- [Besucherstatistiken und Webanalyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)
- [Erweiterungen von Drittanbietern und Datenschutz](erweiterungen-von-drittanbietern.md)
- [Suchbegriffe](suchbegriffe.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
