---
title: Versand und Bearbeitung
audience: businesses, admin, website-creator
category: business-solutions
order: 70
summary: Welche Versandmöglichkeiten ein Exponential-Shop von Haus aus hat - den pauschalen Workflow für einfachen Versand und eigene Versand-Handler - und wie ein Unternehmen sein Versandmodell wählt und ausbaut.
keywords: Versand, Versandkosten, Lieferung, Bearbeitungsgebühr, einfacher Versand, ezsimpleshipping, Versand-Handler, kostenloser Versand, Abholung, Paket, Versandetiketten, shipping, shipping cost, delivery, handling fee, simple shipping, shipping handler, free shipping, pickup, parcel, shipping labels
type: help
---
# Versand und Bearbeitung

Diese Seite richtet sich an Geschäftsinhaber, die entscheiden, wie Kunden für die Lieferung bezahlen. Sie beschreibt
die Versandwerkzeuge, die mit Exponential ausgeliefert werden, was jedes kann und was nicht, und wie Sie von einer
pauschalen Versandgebühr zu Regeln nach Zone oder Gewicht wachsen. Die Einrichtung ist unter
[Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md) beschrieben.

## Inhalt

1. [Zwei Versandwerkzeuge](#zwei-versandwerkzeuge)
2. [Der pauschale Versand-Workflow](#der-pauschale-versand-workflow)
3. [Ein eigener Versand-Handler](#ein-eigener-versand-handler)
4. [Übliche Versandmodelle](#übliche-versandmodelle)
5. [Abholung und lokale Lieferung](#abholung-und-lokale-lieferung)
6. [Was nicht enthalten ist](#was-nicht-enthalten-ist)
7. [Checkliste](#checkliste)

## Zwei Versandwerkzeuge

| Werkzeug | Was es tut | Aufwand |
|---|---|---|
| Workflow-Ereignis für einfachen Versand (`ezsimpleshipping`) | Fügt jeder Bestellung eine feste Versandposition hinzu | Nur Konfiguration |
| Versand-Handler | Berechnet den Versand mit Ihren eigenen Regeln, angezeigt im Warenkorb und bei der Bestätigung | Entwicklerarbeit |

Beide werden unter [Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md#setting-up-a-shop-in-order)
genannt: „a shipping handler or the simple shipping workflow in `workflow.ini`“.

## Der pauschale Versand-Workflow

Der Ereignistyp für einfachen Versand liegt in `kernel/classes/workflowtypes/event/ezsimpleshipping/`. Was sein Code
tut:

- Er liest die Kosten und die Beschreibung der Position aus `settings/workflow.ini`, `[SimpleShippingWorkflow]`. Die
  ausgelieferten Werte sind `ShippingCost=12` und `ShippingDescription=Shipping`; wer Ihren Shop baut, setzt Ihre
  eigenen in einem Override.
- Er fügt die Kosten einmal pro Bestellung hinzu, als zusätzliche Bestellposition des Typs `ezsimpleshipping`, und
  fügt sie nicht zweimal hinzu, wenn der Schritt erneut läuft.
- Er rechnet den Betrag in die Währung der Bestellung um.

Er läuft als Workflow am Auslöser `before_shop_confirmorder`, sodass der Kunde die Versandkosten auf der
Bestätigungsseite sieht, bevor er bezahlt ([Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md#workflow-triggers),
[Workflows: Freigaben, Trigger und die Prozesse, die warten](../../../anleitungen/workflows.md)).

> **Hinweis**
> Der pauschale Workflow berechnet für einen kleinen Artikel und für einen vollen Warenkorb denselben Betrag, und das
> für jedes Land. Für viele kleine Shops mit ähnlichen Paketen ist das in Ordnung. Wenn Ihre Pakete stark variieren,
> planen Sie einen Versand-Handler.

## Ein eigener Versand-Handler

`settings/shop.ini` hat einen Block `[ShippingSettings]` mit einer Einstellung `Handler` (in der ausgelieferten Datei
auskommentiert) und Verzeichnissen, in denen Handler gesucht werden, darunter `extension/<your_extension>/shippinghandlers`.
Die Kommentare in dieser Datei beschreiben die Benennung: Ein Handler namens `ezcustom` ist die Klasse
`eZCustomShippingHandler`. Der `eZShippingManager` des Kernels (`kernel/classes/ezshippingmanager.php`) fragt den
Handler nach den Versandinformationen eines Warenkorbs, aktualisiert sie, wenn sich der Warenkorb ändert, und
entfernt sie, wenn der Warenkorb geleert wird.

Mit dem Kernel wird kein Versand-Handler ausgeliefert: Das in `shop.ini` genannte Standardverzeichnis
`kernel/classes/shippinghandlers` existiert in diesem Release nicht. Ein Handler wird von einem Entwickler oder Partner
geschrieben, meist als Teil einer Erweiterung ([Als Erweiterung paketieren](../../../../eng-us/help/creators/app-creators/packaging-as-an-extension.md)).

Worauf ein Handler seine Kosten stützt, entscheiden Sie: Gewichtsfelder an Produkten, das Land des Kunden, die Summe
des Warenkorbs oder eine Abfrage beim Tarifdienst eines Versanddienstleisters.

## Übliche Versandmodelle

| Modell | Mit den mitgelieferten Werkzeugen | Braucht einen Handler |
|---|---|---|
| Eine Pauschale pro Bestellung | ja, einfacher Versand | nein |
| Gar keine Versandkosten (Dienstleistungen, Dateien) | ja, den Versand-Workflow weglassen | nein |
| Kostenloser Versand ab einem Bestellwert | nein | ja |
| Kosten nach Zone oder Land | nein | ja |
| Kosten nach Gewicht oder Anzahl der Artikel | nein | ja, dazu ein Gewichtsfeld an den Produkten |
| Live-Tarife des Versanddienstleisters | nein | ja, dazu der Dienst des Versanddienstleisters |
| Kunde wählt Standard oder Express | nein | ja, dazu Template-Arbeit |

## Abholung und lokale Lieferung

Zu den mitgelieferten Bestellstatus gehören **Ready for pickup** (Status 11) und **Shipped** (Status 10)
([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md#order-statuses-for-the-whole-lifecycle)).
Ein Shop, der Abholung anbietet, kann damit Mitarbeitern und Kunden sagen, wo eine Bestellung steht, auch mit
pauschalen oder gar keinen Versandkosten. Ready for pickup zählt als „wartet auf den Kunden“, sodass das Dashboard es
nach der kürzeren Wartefrist hervorhebt.

## Was nicht enthalten ist

Der technische Überblick nennt Versandetiketten unter den Dingen, die als Erweiterungen oder Integrationen zu planen
sind ([Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md#what-the-shop-does-not-do)).
Sendungsnummern, Etikettendruck und die Buchung beim Versanddienstleister geschehen außerhalb von Exponential, es sei
denn, Sie fügen sie hinzu. Viele kleine Unternehmen beginnen damit, Etiketten im eigenen Portal des
Versanddienstleisters zu erstellen und die Sendungsnummer an der Bestellung zu notieren.

## Checkliste

- [ ] Versandmodell gewählt: keins, pauschal oder Regeln (Handler).
- [ ] Mit dem pauschalen Workflow: Kosten und Beschreibung in einem Override von `[SimpleShippingWorkflow]` gesetzt.
- [ ] Der Workflow ist mit `before_shop_confirmorder` verbunden.
- [ ] Versandkosten und ihre umsatzsteuerliche Behandlung mit Ihrem Steuerberater geklärt.
- [ ] Eine Testbestellung zeigt die Versandposition auf der Bestätigungsseite, in jeder aktiven Währung.
- [ ] Kunden können Ihre Versandbedingungen vor der Kasse lesen.

## Verwandte Seiten

- [Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md)
- [Warenkorb und Bestellablauf](warenkorb-und-bestellablauf.md)
- [Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
