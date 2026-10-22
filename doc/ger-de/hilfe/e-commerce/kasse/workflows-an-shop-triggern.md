---
title: Workflows an Shop-Triggern
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 43
summary: Welche Workflow-Ereignisse in Exponential an welchen Shop-Trigger gehören, wie Sie die Workflows für Versand und Zahlung Schritt für Schritt einrichten, was ein Ereignis an jedem Trigger kann und was nicht, und die Fehler, die eine Kasse kaputt machen.
keywords: Workflow, Trigger, shop_checkout, before_shop_confirmorder, shop_addtobasket, shop_updatebasket, vorher, nachher, Zahlungs-Workflow, Versand-Workflow, Reihenfolge der Ereignisse, Workflow-Prozess, Setup Trigger, workflow, trigger, before, after, payment workflow, shipping workflow, event order, workflow process
type: help
---
# Workflows an Shop-Triggern

Diese Seite ist für Entwickler und Administratoren, die Workflows mit dem Shop verbinden. Sie ordnet jedem Shop-Trigger
die Ereignisse zu, die dort passen, führt durch die Einrichtung der beiden üblichen Workflows und listet die Fehler auf,
die eine Kasse kaputt machen.

## Inhalt

1. [Die Trigger-Übersicht](#die-trigger-übersicht)
2. [Den Versand-Workflow einrichten](#den-versand-workflow-einrichten)
3. [Den Zahlungs-Workflow einrichten](#den-zahlungs-workflow-einrichten)
4. [Mehrere Ereignisse an einem Trigger](#mehrere-ereignisse-an-einem-trigger)
5. [Eigene Ereignisse](#eigene-ereignisse)
6. [Fehler, die eine Kasse kaputt machen](#fehler-die-eine-kasse-kaputt-machen)

## Die Trigger-Übersicht

| Trigger unter Setup > Trigger | Operationspunkt | Was in diesem Moment existiert | Passt für |
|---|---|---|---|
| `shop` `addtobasket` vorher | `pre_addtobasket` | Das Produkt (`object_id`), gewählte Optionen, Menge | Einen Posten ablehnen (zum Beispiel ein Produkt, das an diesen Kunden nicht verkauft werden darf) |
| `shop` `addtobasket` nachher | `post_addtobasket` | Der Posten liegt im Warenkorb | Protokollierung, Empfehlungen |
| `shop` `updatebasket` vorher / nachher | `pre_updatebasket`, `post_updatebasket` | Posten-IDs und Mengen | Mengenbegrenzungen |
| `shop` `confirmorder` vorher | `pre_confirmorder` | Die temporäre Bestellung | Versand (`ezsimpleshipping` oder ein eigenes Ereignis), Zuschläge als Bestellposten |
| `shop` `checkout` vorher | `pre_checkout` | Die temporäre Bestellung mit ihrer endgültigen Summe | Zahlung (`ezpaymentgateway`) |
| `shop` `checkout` nachher | `post_checkout` | Die aufgegebene Bestellung mit ihrer Nummer; die Mail wurde versendet | Berichte, Exporte in andere Systeme, Analyse |

Welche Trigger überhaupt erscheinen, entscheidet `workflow.ini [OperationSettings]`
([workflow.ini für den Shop](../einstellungen/workflow-ini-fuer-den-shop.md)). Welche Ereignistypen an einen Trigger gelegt
werden dürfen, entscheidet jeder Ereignistyp selbst: `ezsimpleshipping` erlaubt nur `shop` `confirmorder` vorher.

## Den Versand-Workflow einrichten

1. Legen Sie den Betrag fest: `ShippingCost` und `ShippingDescription` in `settings/override/workflow.ini.append.php`.
2. **Setup > Workflows**, eine Gruppe öffnen, **Neuer Workflow**, Name „Shipping“.
3. Fügen Sie das Ereignis **Simple shipping** hinzu, speichern Sie.
4. **Setup > Trigger**: Wählen Sie für `shop` `confirmorder` vorher „Shipping“, speichern Sie.
5. Geben Sie eine Testbestellung auf: Die Bestätigungsseite zeigt die Versandzeile.

Einzelheiten und Grenzen: [Das Ereignis einfacher Versand](das-ereignis-einfacher-versand.md).

## Den Zahlungs-Workflow einrichten

1. Aktivieren Sie die Gateway-Erweiterung und stellen Sie ihre Anbieterdatei auf den Testmodus
   ([paymentgateways.ini durchgegangen](../einstellungen/paymentgateways-ini-durchgegangen.md)).
2. **Setup > Workflows**, **Neuer Workflow**, Name „Payment“.
3. Fügen Sie das Ereignis **Payment Gateway** hinzu; wählen Sie im Ereignis die Gateways aus, die Kunden verwenden
   dürfen; speichern Sie.
4. **Setup > Trigger**: Wählen Sie für `shop` `checkout` vorher „Payment“, speichern Sie.
5. Geben Sie eine Testbestellung auf und schließen Sie die Zahlung im Testmodus des Anbieters ab.

Einzelheiten: [Das Ereignis Zahlungs-Gateway](das-ereignis-zahlungs-gateway.md).

## Mehrere Ereignisse an einem Trigger

Ein Trigger hat einen Workflow. Um an einem Punkt mehrere Ereignisse auszuführen, fügen Sie sie diesem Workflow in der
richtigen Reihenfolge hinzu. Ereignisse laufen nacheinander; das nächste beginnt erst, wenn das vorige angenommen hat.
Setzen Sie an `pre_checkout` Prüfungen, die eine Bestellung ablehnen können, vor das Zahlungsereignis, damit ein Kunde
nie für eine Bestellung belastet wird, die ein späteres Ereignis ablehnt.

Um ein Ereignis nur für manche Kunden oder Produkte auszuführen, verpacken Sie einen zweiten Workflow in ein
Multiplexer-Ereignis ([Der Multiplexer im Shop](der-multiplexer-im-shop.md)).

## Eigene Ereignisse

Ein Ereignistyp ist eine Klasse, die `eZWorkflowEventType` erweitert und eine Methode `execute()` hat, die einen Status
zurückgibt: angenommen, abgelehnt, ein Template anzeigen, weiterleiten oder an Cron zurückstellen. Erzeugen Sie einen mit
dem Assistenten für Workflow-Ereignisse unter **Setup > RAD** (`setup/workflowevent`) und lesen Sie
[Einen Workflow-Ereignistyp schreiben](../../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md). Beschränken Sie ihn
mit `setTriggerTypes()` auf die Trigger, die er versteht, wie es `eZSimpleShippingType` tut.

Für ein Ereignis an `post_checkout` enthalten die Prozessparameter die `order_id`; laden Sie die Bestellung mit
`eZOrder::fetch()` und lesen Sie ihre Nummer, Posten und Summen. Lesen Sie nie Kartendaten: Es gibt keine zu lesen, und
es darf keine geben.

## Fehler, die eine Kasse kaputt machen

| Fehler | Symptom |
|---|---|
| Zahlungsereignis an `checkout` nachher statt vorher | Bestellungen werden aufgegeben und gemailt, bevor jemand bezahlt |
| Versandereignis an `checkout` vorher | Kein Versand auf der Bestätigungsseite; die Summe ändert sich nach dem Bestätigen |
| Ereignis für einfachen Versand und ein Versand-Handler gleichzeitig aktiv | Zwei unabhängige Versandkosten können erscheinen; verwenden Sie das eine oder das andere |
| Workflow bearbeitet, aber nicht gespeichert | Das alte Verhalten bleibt; prüfen Sie **Setup > Trigger** |
| Cron-Teil `workflow.php` läuft nicht | Zurückgestellte Prozesse werden nie fortgesetzt ([Workflows](../../../anleitungen/workflows.md#6-der-workflow-cronjob-wie-prozesse-fortgesetzt-werden)) |
| Gar kein Zahlungs-Workflow | Jede Bestellung wird unbezahlt aufgegeben; „Nächste Schritte“ im Dashboard warnt davor |

## Verwandte Seiten

- [Kasse und Workflows](README.md)
- [Workflows](../../../anleitungen/workflows.md)
- [Workflows, Trigger und Ereignisse](../../ersteller/anwendungsentwickler/workflows-und-ereignisse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
