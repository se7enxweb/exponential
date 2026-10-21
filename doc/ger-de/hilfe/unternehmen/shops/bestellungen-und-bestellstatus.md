---
title: Bestellungen und Bestellstatus
audience: businesses, admin, editor
category: business-solutions
order: 100
summary: Wie Bestellungen in einem Exponential-Shop bearbeitet werden - die Bestellliste und die Bestellansicht, die siebzehn mitgelieferten Status von Pending bis Refunded, eigene Status, wer sie ändern darf, Archivieren und Kunden.
keywords: Bestellungen, Bestellstatus, Bestellliste, Bestellansicht, ausstehend, bezahlt, versandt, abgeschlossen, storniert, erstattet, Retouren, Archiv, eigener Status, setstatus, orders, order status, order list, order view, pending, paid, shipped, completed, canceled, cancelled, refunded, returns, archive, custom status
type: help
---
# Bestellungen und Bestellstatus

Diese Seite richtet sich an Shop-Verantwortliche und an die Personen, die Bestellungen bearbeiten. Sie erklärt, wo
Bestellungen erscheinen, was jeder mitgelieferte Bestellstatus bedeutet, wie Sie damit Ihren eigenen Ablauf abbilden
und wie Sie einschränken, wer einen Status ändern darf. Die Referenz ist
[Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md).

## Inhalt

1. [Wo Bestellungen zu finden sind](#wo-bestellungen-zu-finden-sind)
2. [Die mitgelieferten Status](#die-mitgelieferten-status)
3. [Was „abgeschlossen“ und „kein Umsatz“ bedeuten](#was-abgeschlossen-und-kein-umsatz-bedeuten)
4. [Ihren Ablauf abbilden](#ihren-ablauf-abbilden)
5. [Eigene Status](#eigene-status)
6. [Wer einen Status ändern darf](#wer-einen-status-ändern-darf)
7. [Bestellungen archivieren und entfernen](#bestellungen-archivieren-und-entfernen)
8. [Kunden](#kunden)

## Wo Bestellungen zu finden sind

| Bildschirm | Adresse | Braucht |
|---|---|---|
| Shop-Dashboard | `shop/dashboard` | `shop/administrate` |
| Bestellliste | `shop/orderlist` | `shop/administrate` |
| Bestellansicht | `shop/orderview/<id>` | `shop/buy` für die eigene Bestellung des Kunden; Mitarbeiter öffnen sie aus der Liste |
| Archiv | `shop/archivelist` | `shop/administrate` |
| Kunden | `shop/customerlist`, `shop/customerorderview` | `shop/administrate` |
| Statistik | `shop/statistics` | `shop/administrate` |

Quelle: `kernel/shop/module.php`. Die Bestellliste lässt sich nach jeder Spalte sortieren (ID, Kunde, beide Summen,
Zeit, Status), und die Sortierung bleibt in der Adresse erhalten, sodass ein Link wie „größte Bestellungen zuerst“ als
Lesezeichen gespeichert werden kann ([Sortierung der Bestellliste](../../../funktionen/6.0/sortierung-der-bestellliste.md)).

## Die mitgelieferten Status

Seit 6.0.15 hat jede neue Installation und jede Installation nach dem Datenbank-Update auf 6.0.15 siebzehn Status
([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md#bestellstatus-für-den-ganzen-lebenszyklus)).
Die Administration zeigt sie unter ihren englischen Namen:

| Phase | Status (ID) |
|---|---|
| Neu | Pending (1, ausstehend), Processing (2, in Bearbeitung) |
| Zahlung | Awaiting payment (4, Zahlung erwartet), Paid (5, bezahlt), Payment failed (6, Zahlung fehlgeschlagen) |
| Abwicklung | On hold (7, zurückgestellt), Backordered (8, nachbestellt), Packed (9, verpackt), Shipped (10, versandt), Ready for pickup (11, abholbereit) |
| Ende | Delivered (3, zugestellt), Completed (12, abgeschlossen), Cancelled (13, storniert) |
| Nach dem Ende | Return requested (14, Rücksendung angefragt), Returned (15, zurückgesandt), Partially refunded (16, teilweise erstattet), Refunded (17, erstattet) |

Vor diesem Release wurden nur Pending, Processing und Delivered mitgeliefert, und Shops erfanden ihre eigenen.

## Was „abgeschlossen“ und „kein Umsatz“ bedeuten

Jeder Status trägt drei Bedeutungen, die das Dashboard verwendet (dieselbe Seite):

| Bedeutung | Status | Wirkung |
|---|---|---|
| Abgeschlossen | Delivered, Completed, Cancelled, Partially refunded, Refunded | Die Bestellung ist nicht mehr „offen“ |
| Kein Umsatz | Payment failed, Cancelled, Refunded | Wird als Bestellung gezählt, aber nicht als Umsatz |
| Wartet auf den Kunden | Pending, Awaiting payment, Payment failed, Ready for pickup | Wird nach einer kürzeren Wartefrist hervorgehoben |

So bleiben die Summen auf dem Dashboard ehrlich: Eine erstattete Bestellung bläht den Umsatz nicht auf, und eine
stornierte Bestellung wartet nicht in der Aufgabenliste.

## Ihren Ablauf abbilden

Wählen Sie die Status aus, die Sie brauchen, und stimmen Sie sie mit dem Team ab. Beispiele, von einfach bis
ausführlich:

| Art des Shops | Statusweg |
|---|---|
| Dienstleistungen, kein Versand | Pending, Paid, Completed |
| Kleiner Paketversand | Pending, Paid, Packed, Shipped, Completed |
| Shop mit Abholung | Paid, Packed, Ready for pickup, Completed |
| Shop mit Retouren | ... Shipped, Return requested, Returned, Refunded |

Die PayPal-Erweiterung setzt einen Status, wenn eine Zahlung bestätigt ist, `paypal.ini [OrderSettings] PaymentMadeOrderStatusID`;
unter [Zahlungen mit PayPal](zahlungen-mit-paypal.md) steht, wie Sie ihn auf diese Status abstimmen.

> **Tipp**
> Schreiben Sie Ihren Statusweg auf eine Seite und hängen Sie sie neben der Person auf, die die Pakete packt. Ein
> Status nützt nur, wenn alle ihn auf dieselbe Weise setzen.

## Eigene Status

Status mit einer ID unter 1000 sind intern und können nicht versehentlich entfernt werden; eigene Status beginnen bei
1000. Status werden unter `shop/status` verwaltet (Richtlinie `shop/edit_status`). Wenn Sie vor 6.0.15 eine der Nummern
4 bis 17 für einen eigenen Status verwendet haben, prüfen Sie nach dem Update die Statusliste
([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md#einen-bestehenden-shop-aktualisieren)).

## Wer einen Status ändern darf

Einen Status zu ändern erfordert `shop/setstatus`, das zwei Beschränkungen hat, `FromStatus` und `ToStatus`
(`kernel/shop/module.php`). Damit können Sie Rollen wie diese bauen:

- **Packen**: darf Bestellungen von Paid auf Packed und von Packed auf Shipped setzen;
- **Kundenservice**: darf Bestellungen auf Return requested und Returned setzen;
- **Inhaber**: darf jeden Status setzen, auch Refunded.

Jede Änderung wird im Statusverlauf der Bestellung festgehalten (`kernel/classes/ezorderstatushistory.php`), und das
Prüfprotokoll zeichnet Handelsereignisse auf ([Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)). Rollen werden wie unter
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) beschrieben eingerichtet.

> **Hinweis**
> Einen Status in Exponential zu setzen bewegt kein Geld. Eine Erstattung erfolgt im Konto bei Ihrem Zahlungsanbieter;
> der Status hält fest, dass sie erfolgt ist.

## Bestellungen archivieren und entfernen

Abgeschlossene Bestellungen lassen sich archivieren (`shop/archiveorder`), wodurch sie aus der aktiven Liste
verschwinden, ohne gelöscht zu werden, und wieder zurückholen (`shop/unarchiveorder`). Bestellungen können auch
entfernt werden (`shop/removeorder`). Denken Sie daran, dass Ihre Buchhaltungs- und Steuervorschriften verlangen
können, Bestellunterlagen jahrelang aufzubewahren; archivieren Sie lieber, statt zu entfernen, und lesen Sie
[Aufbewahrung und Löschung von Daten](../sicherheit/aufbewahrung-und-loeschung-von-daten.md).

## Kunden

Die Kundenliste zeigt, wer gekauft hat und wie viel; die Bestellansicht eines Kunden listet die Bestellungen eines
Kunden auf. Beide brauchen `shop/administrate`. Das Dashboard zählt neue, wiederkehrende und alle bisherigen Kunden
([Shop-Dashboard und Berichte](shop-uebersicht-und-berichte.md)).

## Verwandte Seiten

- [Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)
- [Bestellbelege und Bestätigungsmails](bestellbelege-und-bestaetigungsmails.md)
- [Einen Shop im Alltag betreiben](einen-shop-im-alltag-betreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
