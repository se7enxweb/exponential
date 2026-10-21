---
title: Besucherstatistik und Analyse
audience: website-creator, admin
category: website-creation
order: 680
summary: Messen, wie eine Website genutzt wird - was Exponential selbst aufzeichnet (Suchstatistiken, Aufrufzahlen, Anfrage-Logs, Shop-Statistiken), einen Analysedienst mit bcwebsitestatistics oder einem eigenen Skript ergänzen, und Hinweise zu Datenschutz und CSP.
keywords: Statistik, Analyse, Webanalyse, Suchstatistiken, Aufrufzahlen, Anfrage-Logs, Shop-Statistiken, Datenschutz, Einwilligung, Website-Ersteller, statistics, analytics, Google Analytics, bcwebsitestatistics, search statistics, view count, request logs, shop statistics, privacy, consent, CSP, website creator
type: help
---
# Besucherstatistik und Analyse

Diese Seite richtet sich an Website-Ersteller, die wissen wollen, wie eine Website genutzt wird. Exponential zeichnet
einiges selbst auf; für eine Besucheranalyse ergänzen Sie einen Dienst.

## Inhalt

1. [Was Exponential aufzeichnet](#was-exponential-aufzeichnet)
2. [Einen Analysedienst ergänzen](#einen-analysedienst-ergänzen)
3. [Datenschutz und Einwilligung](#datenschutz-und-einwilligung)
4. [Unter einer Content Security Policy](#unter-einer-content-security-policy)

## Was Exponential aufzeichnet

| Quelle | Was | Wo |
|---|---|---|
| Suchstatistiken | gesuchte Begriffe, Anzahl, durchschnittliche Ergebnisse (standardmäßig aus) | **Setup > Suchstatistiken** ([URLs und Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)) |
| Aufrufzahlen | Seitenaufrufe je Knoten, gezählt vom Cronjob-Teil `updateviewcount` aus dem Zugriffslog | `fetch( 'content', 'view_top_list', ... )` |
| Anfrage-Logs | Anfragen mit Zeitmessung, für den Betrieb | [Anfrage-Logs](../../../../eng-us/features/6.0/request-logs.md) |
| Shop-Statistiken | Bestellungen und Umsatz | `shop/statistics`, das Shop-Dashboard |
| Änderungsprotokoll | wer in der Administration was geändert hat | [Änderungsprotokoll](../../../../eng-us/features/6.0/audit-trail.md) |

Diese Quellen beantworten betriebliche Fragen ("was suchen Besucher und finden es nicht", "welche Seiten sind beliebt").
Sie ersetzen keinen Analysedienst.

## Einen Analysedienst ergänzen

- **`bcwebsitestatistics`** fügt den Seiten das Google-Analytics-Skript hinzu und kann Shop-Bestellungen über den
  Workflow-Typ `ezreceipt` melden; setzen Sie `Urchin` in
  `settings/override/bcwebsitestatistics.ini.append.php` auf Ihre eigene Property
  ([bcwebsitestatistics](../../../../eng-us/features/6.0/extensions/bcwebsitestatistics.md)).
- **Ein anderer Dienst**: Fügen Sie sein Skript als Datei über die Skriptliste Ihres Designs hinzu
  ([Stylesheets und Skripte](stylesheets-und-skripte.md)), mit seinen Einstellungen in einem JSON-Datenblock.

## Datenschutz und Einwilligung

Analyse braucht in vielen Rechtsordnungen die Einwilligung des Besuchers. Laden Sie das Skript erst nach der
Einwilligung, beschreiben Sie es auf der Datenschutzseite, und bevorzugen Sie Dienste, die Sie selbst hosten können oder
die keine Cookies setzen, wenn Ihr Publikum das erwartet. Dies ist keine Rechtsberatung; fragen Sie die Person, die für
den Datenschutz verantwortlich ist.

## Unter einer Content Security Policy

Ein Analyseskript von einem anderen Host muss erlaubt werden: `AdditionalSources[]=script-src https://<host>` und
`connect-src` für das Ziel, an das es Daten sendet, in `[ContentSecurityPolicy]` der Website
([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)). Inline-Schnipsel, die der Dienst
vorschlägt, müssen in eine Datei verschoben werden.

## Verwandte Seiten

- [SEO-Grundlagen](seo-grundlagen.md)
- [Ein Projekt messen: Kennzahlen](../../fuehrung/kennzahlen-aus-logs-und-audit.md)
- [Suche auf der Website](suche-auf-der-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
