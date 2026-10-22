---
title: Analysen für Shops
audience: ecommerce, businesses, website-creator
category: e-commerce
order: 75
summary: Was ein Exponential-Shop messen kann - das Shop-Dashboard, Bestellstatistiken, die Fetch-Funktionen für Bestseller und „auch gekauft“, Aufrufzahlen, Suchstatistiken und der Audit-Trail - und was die Erweiterung bcwebsitestatistics oder ein anderer Analysedienst hinzufügt, mit Hinweisen zum Datenschutz.
keywords: Analysen, Shop-Analysen, Dashboard, Bestellstatistiken, Aufrufzahlen, Suchstatistiken, Konversion, abgebrochene Warenkörbe, Einwilligung, analytics, store analytics, dashboard, order statistics, best_sell_list, related_purchase, view counts, search statistics, bcwebsitestatistics, ezreceipt, Google Analytics, conversion, abandoned baskets, consent
type: help
---
# Analysen für Shops

Diese Seite richtet sich an Händler und Marketingverantwortliche, die wissen möchten, wie der Shop läuft, und an die
Entwickler, die die Messung einrichten. Exponential misst aus Bestellungen und Inhalten schon selbst eine ganze Menge;
Besucheranalysen wie Traffic-Quellen und Konversionstrichter brauchen einen Analysedienst.

## Inhalt

1. [Was der Shop selbst misst](#was-der-shop-selbst-misst)
2. [Fragen und wo die Antwort steht](#fragen-und-wo-die-antwort-steht)
3. [Einen Analysedienst hinzufügen](#einen-analysedienst-hinzufügen)
4. [Datenschutz](#datenschutz)

## Was der Shop selbst misst

| Quelle | Misst | Wo |
|---|---|---|
| Shop-Dashboard | Bestellungen heute, in 7 und 30 Tagen mit Trends; Umsatz und durchschnittliche Bestellung je Währung; neue und wiederkehrende Kunden; offene und verspätete Bestellungen; laufende und abgebrochene Warenkörbe; Umsatz pro Tag; Aufschlüsselung nach Status; Bestseller | `shop/dashboard` ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)) |
| Bestellstatistiken | Verkaufte Produkte und Summen pro Jahr und Monat | `shop/statistics` |
| `fetch( 'shop', 'best_sell_list', ... )` | Meistverkaufte Produkte unterhalb eines Knotens, optional in einem Zeitfenster | Templates (`kernel/shop/function_definition.php`) |
| `fetch( 'shop', 'related_purchase', ... )` | Produkte, die zusammen mit einem Produkt gekauft wurden | Templates |
| Aufrufzahlen | Seitenaufrufe pro Knoten aus dem Zugriffslog (Cronjob-Teil `updateviewcount`) | `fetch( 'content', 'view_top_list', ... )` |
| Suchstatistiken | Wonach Besucher suchen und wie viele Ergebnisse sie bekommen | **Setup > Suchstatistiken** |
| Audit-Trail, Kanal `commerce` | Angelegte Bestellungen, genehmigte Zahlungen, Statusänderungen, Änderungen an USt, Währungen und Rabatten | Reiter **Audit** ([Audit-Trail für den Handel](audit-trail-fuer-den-handel.md)) |

Den allgemeinen Überblick über die Messung finden Sie unter [Besucherstatistiken und Analysen](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md).

## Fragen und wo die Antwort steht

| Frage | Antwort aus |
|---|---|
| Wie viel haben wir letzten Monat verkauft, pro Produkt? | `shop/statistics` |
| Steigen die Verkäufe? | Trends im Dashboard gegenüber den 30 Tagen davor |
| Welche Bestellungen warten zu lange? | Dashboard, **Zu erledigen** |
| Steigen Kunden an der Kasse aus? | Dashboard: laufende und abgebrochene Warenkörbe, unbestätigte Bestellabschlüsse |
| Wonach suchen Leute und finden es nicht? | Suchstatistiken |
| Welche Produktseiten sind beliebt, werden aber selten gekauft? | Aufrufzahlen neben den Bestellstatistiken |
| Woher kommen die Besucher? | Ein Analysedienst |

## Einen Analysedienst hinzufügen

- **`bcwebsitestatistics`** fügt das Google-Analytics-Skript hinzu und meldet mit `OrderSubmit=enabled` aufgegebene
  Bestellungen über seinen Workflow-Ereignistyp `ezreceipt`, der nur mit dem After-Trigger von `shop_checkout` verbunden
  werden darf ([bcwebsitestatistics](../../../funktionen/6.0/erweiterungen/bcwebsitestatistics.md)).
- **Ein anderer Dienst**: Laden Sie sein Skript als Datei über die Skriptliste Ihres Designs und melden Sie eine
  Bestellung aus dem Template des Belegs oder der Bestellansicht oder aus einem eigenen Ereignis am After-Trigger von
  `shop_checkout` ([Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md)). Unter einer Content Security
  Policy müssen die Hosts des Dienstes erlaubt sein
  ([Besucherstatistiken und Analysen](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md#unter-einer-content-security-policy)).

Melden Sie Bestellsummen, Währung und Produktkennungen; niemals Namen, Adressen oder E-Mail-Adressen.

## Datenschutz

Besucheranalysen brauchen in der Regel die Einwilligung der Besucher; laden Sie Skripte erst nach der Einwilligung und
beschreiben Sie sie auf der Datenschutzseite. Dies ist keine Rechtsberatung. Die eigenen Kennzahlen des Kernels stammen
aus Ihrer Datenbank und Ihren Logs und werden nirgendwohin gesendet. Zu Datenanfragen von Kunden siehe
[DSGVO-Anfragen](../../kunden/handel/dsgvo-anfragen.md).

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Shop-Berichte](../../kunden/handel/shop-berichte.md)
- [Shop-Dashboard und Berichte](../../unternehmen/shops/shop-uebersicht-und-berichte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
