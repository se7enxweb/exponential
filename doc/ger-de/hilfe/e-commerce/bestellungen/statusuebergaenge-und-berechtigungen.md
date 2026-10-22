---
title: Statusübergänge und Berechtigungen
audience: ecommerce, customer, admin
category: e-commerce
order: 52
summary: Wie Exponential entscheidet, wer den Status einer Bestellung ändern darf - die Richtlinie shop/setstatus mit ihren Einschränkungen FromStatus und ToStatus, wie mehrere Richtlinien zusammenwirken, Rollenentwürfe für Versand, Kundenservice und Manager und wie Sie sie testen.
keywords: setstatus, FromStatus, ToStatus, Berechtigungen für Bestellstatus, canModifyStatus, statusModificationList, Einschränkung, Rollenentwurf, Versand, Kundenservice, shop/edit_status, order status permissions, limitation, role design, packing staff, customer service
type: help
---
# Statusübergänge und Berechtigungen

Diese Seite ist für Händler und Administratoren, die entscheiden, wer Bestellungen von einem Status in einen anderen
bewegen darf. Der Shop hat genau dafür eine Richtlinienfunktion, `shop/setstatus`, mit zwei Einschränkungen. Richtig
eingesetzt, macht sie aus Ihrem Statusablauf erzwungene Schritte.

## Inhalt

1. [Die Richtlinie und ihre Einschränkungen](#die-richtlinie-und-ihre-einschränkungen)
2. [Wie die Prüfung funktioniert](#wie-die-prüfung-funktioniert)
3. [Rollenentwürfe](#rollenentwürfe)
4. [Die Statusliste selbst bearbeiten](#die-statusliste-selbst-bearbeiten)
5. [Die Rollen testen](#die-rollen-testen)

## Die Richtlinie und ihre Einschränkungen

`kernel/shop/module.php` definiert `setstatus` mit zwei Einschränkungen, beides Listen von Bestellstatus:

| Einschränkung | Bedeutung |
|---|---|
| `FromStatus` | Der aktuelle Status der Bestellung muss in dieser Liste stehen |
| `ToStatus` | Der neue Status muss in dieser Liste stehen |

Ohne Einschränkungen erlaubt `shop/setstatus` jede Änderung. Die in der Bestellansicht angebotenen Status sind die, die
der aktuelle Benutzer für diese Bestellung setzen darf (`eZOrder::statusModificationList()`).

## Wie die Prüfung funktioniert

`eZOrder::canModifyStatus()` (`kernel/classes/ezorder.php`) liest den Zugriff des Benutzers auf `shop/setstatus`:

- Zugriff „ja“ (eine Richtlinie ohne Einschränkungen): Jede Änderung ist erlaubt.
- Zugriff „eingeschränkt“: Jede Richtlinie wird für sich geprüft. Eine Richtlinie erlaubt die Änderung, wenn der aktuelle
  Status in ihrem `FromStatus` steht (falls gesetzt) und der neue Status in ihrem `ToStatus` (falls gesetzt). Erlaubt
  **eine einzige** Richtlinie sie, ist die Änderung erlaubt.

Einschränkungen wirken innerhalb einer Richtlinie also zusammen (beide müssen gelten), und Richtlinien wirken mit „oder“
zusammen. Um jemandem zwei getrennte Schritte zu geben, geben Sie ihm zwei Richtlinien, nicht eine Richtlinie mit beiden
Listen zusammengeführt: Das Zusammenführen würde auch die Kreuzkombinationen erlauben.

## Rollenentwürfe

Beispiel mit einem Paketablauf (Pending, Paid, Packed, Shipped, Completed, On hold, Cancelled, Refunded):

| Rolle | Richtlinien für `shop/setstatus` | Wirkung |
|---|---|---|
| Versand | 1: Von Paid, nach Packed oder On hold. 2: Von Packed oder On hold, nach Shipped oder Packed | Kann nur bezahlte Bestellungen durch Verpacken und Versand bewegen |
| Kundenservice | 1: Von Pending oder Awaiting payment, nach Cancelled. 2: Von Shipped oder Delivered, nach Return requested | Kann unbezahlte Bestellungen stornieren und Rücksendungen eröffnen |
| Shop-Manager | Keine Einschränkung | Jede Änderung, einschließlich Erstattungen |
| Inhaber | Keine Einschränkung, dazu `shop/edit_status` und `shop/setup` | Bearbeitet außerdem die Statusliste, die USt. und die Preise |

Alle Rollen für Shop-Mitarbeitende brauchen außerdem `shop/administrate`, um Bestellliste und Bestellansicht im Reiter
Web-Shop zu sehen ([Pfade nach Rolle](../rollen/README.md#die-shop-richtlinienfunktionen)). Legen Sie die Rollen unter
**Setup > Rollen und Richtlinien** an ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

## Die Statusliste selbst bearbeiten

`shop/status` (Richtlinie `shop/edit_status`) bearbeitet die Liste der Status: eigene Status hinzufügen (IDs ab 1000),
sie umbenennen und festlegen, ob sie aktiv sind. Überlassen Sie das dem Inhaber: Ein umbenannter Status ändert, was die
Einschränkungen jeder Rolle bedeuten.

## Die Rollen testen

- [ ] Melden Sie sich als Testbenutzer jeder Rolle an, nicht als Administrator.
- [ ] Öffnen Sie eine Bestellung in jedem Status und prüfen Sie die angebotenen Auswahlen gegen die Tabelle, die Sie
  entworfen haben.
- [ ] Versuchen Sie eine Änderung, die abgelehnt werden sollte; sie darf nicht angeboten werden.
- [ ] Prüfen Sie, dass der Statusverlauf der Bestellung den Testbenutzer und den Zeitpunkt zeigt.
- [ ] Prüfen Sie bei eingeschaltetem Prüfpfad, dass `commerce.order.status` die Änderungen aufzeichnet ([Prüfpfad für den Handel](../betrieb/audit-trail-fuer-den-handel.md)).

## Verwandte Seiten

- [Bestellbetrieb](README.md)
- [Der Lebenszyklus einer Bestellung im Betrieb](der-lebenszyklus-einer-bestellung-im-betrieb.md)
- [Bestellungen im Alltag](../../kunden/handel/bestellungen-im-alltag.md)
- [Richtlinienfunktionen und Einschränkungen](../../ersteller/anwendungsentwickler/richtlinienfunktionen-und-einschraenkungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
