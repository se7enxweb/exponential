---
title: workflow.ini für den Shop
audience: ecommerce, website-creator, admin
category: e-commerce
order: 34
summary: Die Teile von settings/workflow.ini, die für einen Shop wichtig sind - die verfügbaren Ereignistypen, die vier Shop-Trigger in AvailableOperationList, Betrag und Beschreibung in SimpleShippingWorkflow und wie Erweiterungen eigene Ereignistypen hinzufügen.
keywords: workflow.ini, EventSettings, AvailableEventTypes, OperationSettings, AvailableOperationList, before_shop_confirmorder, shop_checkout, shop_addtobasket, shop_updatebasket, SimpleShippingWorkflow, ShippingCost, ShippingDescription, ExtensionDirectories, Workflow-Ereignis, Trigger, Versand, workflow event, trigger, shipping
type: help
---
# workflow.ini für den Shop

Diese Seite ist für Entwickler und Administratoren, die Workflows mit dem Shop verbinden. `settings/workflow.ini`
entscheidet, welche Workflow-Ereignistypen existieren, welche Operationen einen Workflow auslösen können und welchen
Betrag das pauschale Versand-Ereignis hat. Die Workflows selbst werden in der Administration angelegt.

## Inhalt

1. [EventSettings](#eventsettings)
2. [OperationSettings](#operationsettings)
3. [SimpleShippingWorkflow](#simpleshippingworkflow)
4. [Ereignistypen aus Erweiterungen](#ereignistypen-aus-erweiterungen)
5. [Das Ergebnis prüfen](#das-ergebnis-prüfen)

## EventSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `RepositoryDirectories[]` | `kernel/classes/workflowtypes` | Wo Ereignistypen des Kernels gefunden werden |
| `ExtensionDirectories[]` | leer | Erweiterungen mit einem Verzeichnis `workflowtypes/event/<name>/` |
| `AvailableEventTypes[]` | `event_ezapprove`, `event_ezwaituntildate`, `event_ezsimpleshipping`, `event_ezmultiplexer`, `event_ezpaymentgateway`, `event_ezfinishuserregister` | Die Ereignistypen, die beim Aufbau eines Workflows angeboten werden |

Davon sind drei für einen Shop wichtig:

| Ereignistyp | Erlaubte Trigger (aus der Klasse) | Verwendung im Shop |
|---|---|---|
| `ezsimpleshipping` | nur `shop` / `confirmorder` / `before` | Fügt eine pauschale Versandzeile hinzu ([Das einfache Versand-Ereignis](../kasse/das-ereignis-einfacher-versand.md)) |
| `ezpaymentgateway` | beliebige | Führt das Zahlungs-Gateway aus; legen Sie es auf `shop_checkout` ([Das Ereignis Zahlungs-Gateway](../kasse/das-ereignis-zahlungs-gateway.md)) |
| `ezmultiplexer` | beliebige | Führt einen anderen Workflow aus, wenn Bedingungen zutreffen ([Der Multiplexer im Shop](../kasse/der-multiplexer-im-shop.md)) |

`ezapprove` und `ezwaituntildate` sind an das Veröffentlichen von Inhalten gebunden und `ezfinishuserregister` an die
Benutzerregistrierung; sie können nicht mit Shop-Triggern verbunden werden.

## OperationSettings

`AvailableOperationList[]` listet die Operationen auf, die einen Workflow starten dürfen. Ein Eintrag ohne Präfix
(`shop_checkout`) bietet sowohl einen Trigger **davor** als auch einen Trigger **danach**; ein Eintrag, der mit
`before_` oder `after_` beginnt, bietet nur diesen einen (die Seite mit der Trigger-Liste wertet die Namen so aus,
`kernel/private/classes/views/trigger/list.php`). Ausgeliefert:

| Eintrag | Operation | Angebotene Trigger | Typisches Ereignis |
|---|---|---|---|
| `content_publish` | `content/publish` | davor, danach | Freigabe |
| `before_shop_confirmorder` | `shop/confirmorder` | davor | Versand |
| `shop_checkout` | `shop/checkout` | davor (Zahlung), danach (sobald die Bestellung aktiviert und per Mail versendet ist) | Zahlung; Berichte |
| `shop_addtobasket` | `shop/addtobasket` | davor, danach | Prüfungen der Artikel |
| `shop_updatebasket` | `shop/updatebasket` | davor, danach | Prüfungen der Mengen |

Die Operation `confirmorder` hat überhaupt keinen Trigger-Punkt danach (`kernel/shop/operation_definition.php`),
weshalb ihr Eintrag das Präfix `before_` trägt. Wie sich jeder Trigger in seine Operation einfügt, steht unter
[Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md).

## SimpleShippingWorkflow

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `ShippingCost` | `12` | Der pauschale Betrag, der jeder Bestellung hinzugefügt wird, in der Währung des Shops; in die Währung der Bestellung umgerechnet |
| `ShippingDescription` | `Shipping` | Der Text der Versandzeile in der Bestellung |

Der Betrag ist für jede Bestellung gleich. Setzen Sie ihn in `settings/override/workflow.ini.append.php`.

## Ereignistypen aus Erweiterungen

Eine Erweiterung fügt einen Ereignistyp hinzu, indem sie ihn in `workflowtypes/event/<name>/<name>type.php` ablegt und
in ihrer `workflow.ini.append.php` ihren eigenen Namen zu `ExtensionDirectories[]` und den Typ zu
`AvailableEventTypes[]` hinzufügt. Die Erweiterung `bcwebsitestatistics` zum Beispiel fügt einen Ereignistyp
`ezreceipt` hinzu, der nach der Kasse läuft und Bestellungen an einen Analysedienst meldet ([Analysen für Shops](../betrieb/analysen-fuer-shops.md)).
Wie Sie einen schreiben, ist unter [Einen Workflow-Ereignistyp schreiben](../../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md)
beschrieben.

## Das Ergebnis prüfen

**Setup > Trigger** listet jeden Trigger aus `AvailableOperationList[]` mit dem verbundenen Workflow auf, und
**Setup > Workflows** zeigt die Ereignisse jedes Workflows ([Workflows](../../../anleitungen/workflows.md#11-die-seite-mit-der-trigger-liste)).
„So funktioniert Ihr Shop“ im Shop-Dashboard nennt die Workflows an den Shop-Triggern und warnt, wenn es an der Kasse
keinen Zahlungsschritt gibt.

## Verwandte Seiten

- [Die Shop-Einstellungen durchgegangen](README.md)
- [Kasse und Workflows](../kasse/README.md)
- [Workflows, Trigger und Ereignisse](../../ersteller/anwendungsentwickler/workflows-und-ereignisse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
