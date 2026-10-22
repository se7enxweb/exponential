---
title: Kasse und Workflows
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 40
summary: Die Exponential-Kasse von innen - die vier Shop-Operationen und ihre Triggerpunkte, die temporäre Bestellung und die Workflow-Ereignisse, die Versand hinzufügen, die Zahlung entgegennehmen oder nach Bedingung verzweigen - mit einer Seite für jeden Teil.
keywords: Kasse, Bestellabschluss, Workflow, Trigger, Shop-Operation, confirmorder, shop/checkout, temporäre Bestellung, Ereignis Zahlungs-Gateway, einfacher Versand, Multiplexer, before_shop_confirmorder, shop_checkout, checkout, shop operation, temporary order, payment gateway event, simple shipping
type: help
---
# Kasse und Workflows

Dieser Teil ist für Entwickler und technisch interessierte Händler, die wissen müssen, was zwischen
**Zum Warenkorb hinzufügen** und einer aufgegebenen Bestellung geschieht. Den Weg aus Sicht des Kunden beschreibt
[Ablauf von Warenkorb und Kasse](../../unternehmen/shops/warenkorb-und-bestellablauf.md); diese Seiten erklären die
Mechanik darunter: Operationen, Trigger, Workflow-Prozesse und die Ereignisse, die darin laufen.

## Inhalt

1. [Die Idee in drei Sätzen](#die-idee-in-drei-sätzen)
2. [Die Seiten dieses Teils](#die-seiten-dieses-teils)
3. [Wo Workflows verwaltet werden](#wo-workflows-verwaltet-werden)
4. [Was im Hintergrund läuft](#was-im-hintergrund-läuft)

## Die Idee in drei Sätzen

Jeder Shop-Schritt, der etwas ändert, ist eine **Operation** (`kernel/shop/operation_definition.php`): `addtobasket`,
`updatebasket`, `confirmorder` und `checkout`. Jede Operation hat **Triggerpunkte**; ein Workflow, der mit einem Trigger
verbunden ist, führt dort seine **Ereignisse** aus, und ein Ereignis kann annehmen, ablehnen, eine Seite anzeigen oder
den Kunden anderswohin schicken und warten. Versand und Zahlung sind solche Ereignisse.

## Die Seiten dieses Teils

| Seite | Beantwortet |
|---|---|
| [Die Kassenoperationen Schritt für Schritt](die-kassenoperationen-schritt-fuer-schritt.md) | Was jede Operation tut, in welcher Reihenfolge, vom Warenkorb bis zur aktivierten Bestellung |
| [Temporäre Bestellungen und Aktivierung](temporaere-bestellungen-und-aktivierung.md) | Warum eine Bestellung existiert, bevor sie aufgegeben ist, und was die Aktivierung ändert |
| [Workflows an Shop-Triggern](workflows-an-shop-triggern.md) | Welche Ereignisse zu welchem Trigger passen und wie Sie sie einrichten |
| [Das Ereignis Zahlungs-Gateway](das-ereignis-zahlungs-gateway.md) | Gateways auswählen, die Wahl des Kunden, Weiterleitung und Rückruf |
| [Das Ereignis einfacher Versand](das-ereignis-einfacher-versand.md) | Die pauschale Versandzeile und ihre Grenzen |
| [Der Multiplexer im Shop](der-multiplexer-im-shop.md) | Einen Unter-Workflow nur für manche Kunden oder Produkte ausführen |

## Wo Workflows verwaltet werden

| Bildschirm | Adresse | Verwendung |
|---|---|---|
| Workflows | **Setup > Workflows** | Einen Workflow anlegen und ihm Ereignisse hinzufügen |
| Trigger | **Setup > Trigger** | Einen Workflow mit einem Trigger verbinden |
| Workflow-Prozesse | **Setup > Workflow-Prozesse** | Wartende Prozesse sehen, zum Beispiel eine laufende Zahlung |

Alle drei beschreibt [Workflows](../../../anleitungen/workflows.md). Ein Workflow pro Trigger: Um mehrere Ereignisse an
einem Trigger auszuführen, legen Sie sie in der gewünschten Reihenfolge in einen Workflow.

## Was im Hintergrund läuft

- Das Cron-Skript `workflow.php` (Teil `frequent` in `settings/cronjob.ini`) setzt Prozesse fort, die an Cron
  zurückgestellt wurden ([Workflows](../../../anleitungen/workflows.md#6-der-workflow-cronjob-wie-prozesse-fortgesetzt-werden)).
- Das Skript `basket_cleanup.php` (Teil `infrequent`) entfernt Warenkörbe abgelaufener Sessions.
- Der Rückruf eines Weiterleitungs-Gateways kommt als eigene Anfrage vom Anbieter; er ist kein Cronjob, daher muss die
  Website aus dem Internet erreichbar sein ([Das Ereignis Zahlungs-Gateway](das-ereignis-zahlungs-gateway.md)).

## Verwandte Seiten

- [Warenkorb, Kasse und Bestellungen](../../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md)
- [Workflows, Trigger und Ereignisse](../../ersteller/anwendungsentwickler/workflows-und-ereignisse.md)
- [workflow.ini für den Shop](../settings/workflow-ini-fuer-den-shop.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
