---
title: Audit-Trail für den Handel
audience: ecommerce, admin, businesses
category: e-commerce
order: 79
summary: Was der Audit-Trail von Exponential über einen Shop aufzeichnet - die Handelsereignisse zu Bestellungen, Zahlungen, USt, Währungen und Rabatten, was nie aufgezeichnet wird, wer den Kanal commerce lesen darf, Aufbewahrung, Prüfung und Exporte und wie Sie damit häufige Fragen beantworten.
keywords: Audit-Trail, Kanal commerce, Aufbewahrung, Nachweis, Bestellungen, Zahlungen, USt, audit trail, commerce channel, commerce.order.create, commerce.order.status, commerce.payment.approve, commerce.vat.change, commerce.currency.change, commerce.discount.change, commerce.order.delete, commerce.order.purge, audit/read, Channel limitation, retention, exp:audit verify
type: help
---
# Audit-Trail für den Handel

Diese Seite richtet sich an Händler, Administratoren und Prüfer, die wissen müssen, wer in einem Shop was geändert hat.
Exponential 6.0.15 zeichnet sicherheitsrelevante Ereignisse in einem manipulationssicheren Log auf, standardmäßig
eingeschaltet; der Shop schreibt seine Ereignisse in den Kanal `commerce` ([Der Audit-Trail](../../../funktionen/6.0/pruefprotokoll.md)).

## Inhalt

1. [Die Handelsereignisse](#die-handelsereignisse)
2. [Was nie aufgezeichnet wird](#was-nie-aufgezeichnet-wird)
3. [Wer ihn lesen darf](#wer-ihn-lesen-darf)
4. [Fragen beantworten](#fragen-beantworten)
5. [Aufbewahrung und Nachweis](#aufbewahrung-und-nachweis)

## Die Handelsereignisse

Gefunden im Code des Shops (die Aufrufe von `expAuditHook::emit()`):

| Ereignis | Wann |
|---|---|
| `commerce.basket.checkout` | Ein Bestellabschluss beginnt: Warenkorb, Bestellung, Anzahl der Artikel und Summe |
| `commerce.order.create` | Eine Bestellung wird aktiviert: Bestellnummer, Summe, Währung |
| `commerce.order.status` | Der Status einer Bestellung ändert sich |
| `commerce.order.archive`, `commerce.order.unarchive` | Eine Bestellung wird archiviert oder zurückgeholt |
| `commerce.order.item.remove` | Ein Artikel wird aus einer Bestellung entfernt |
| `commerce.order.delete` | Eine Bestellung wird entfernt (`shop/removeorder`) |
| `commerce.order.purge` | Alle Bestellungen werden entfernt (das Aufräumskript des Shops) |
| `commerce.payment.approve` | Ein Zahlungsobjekt wird durch den Callback eines Gateways genehmigt |
| `commerce.vat.change` | USt-Typen oder -Regeln ändern sich |
| `commerce.currency.change` | Währungen oder Kurse ändern sich |
| `commerce.discount.change` | Rabattgruppen oder -regeln ändern sich |

CSV- und Paketimporte und -exporte (`data.*`) gehen in denselben Kanal. Änderungen am Preis eines Produkts sind
Inhaltsänderungen: Sie stehen im Kanal `content` als Veröffentlichungsereignisse, und die Versionshistorie des Produkts
zeigt die alten und neuen Werte ([Inhaltshistorie](../../../anleitungen/inhaltsverlauf.md)).

## Was nie aufgezeichnet wird

Kartendaten, Kundenadressen bei Bestellereignissen, Passwörter und Geheimnisse. Bestellereignisse tragen IDs, Nummern,
Summen, Währung und Status, also das, was ein Prüfer braucht, und nichts, was ein Leck des Logs preisgeben würde
([Leitfaden zum Audit](../../../bc/6.0/audit.md)).

## Wer ihn lesen darf

Der Reiter **Audit** braucht `audit/read`. Seine Einschränkung `Channel` erlaubt es, einem Shop-Prüfer nur den Kanal
`commerce` zu geben, ohne Zugriff auf Anmeldungen oder Inhalte. Exporte brauchen `audit/export`.

## Fragen beantworten

| Frage | Filter |
|---|---|
| Wer hat letzten Monat die USt-Regeln geändert? | `commerce.vat.change`, der Monat |
| Wer hat Bestellung 1042 auf Refunded gesetzt? | `commerce.order.status`, Objekt Bestellung 1042 |
| Wurde die Zahlung einer Bestellung vom Anbieter bestätigt? | `commerce.payment.approve` für die Bestellung |
| Hat jemand Bestellungen gelöscht? | `commerce.order.delete`, `commerce.order.purge` |
| Wie viele Bestellabschlüsse wurden begonnen, aber nicht aufgegeben? | `commerce.basket.checkout` gegenüber `commerce.order.create` |

Dieselben Fragen in einer Shell: `./console exp:audit tail --channel=commerce`.

## Aufbewahrung und Nachweis

Die mitgelieferte Aufbewahrung für den Kanal `commerce` beträgt 90 Tage live und 730 Tage als Archive
(`[AuditChannel_commerce]` `LiveDays`, `ArchiveDays`). Wenn Sie Nachweise länger aufbewahren müssen, erhöhen Sie
`ArchiveDays` oder exportieren und speichern Sie die Einträge. `./console exp:audit verify --channel=commerce` prüft,
dass die Kette der Einträge intakt ist. Der Cronjob-Teil `frequent` muss für Indizierung, Rotation und Archive laufen.

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Sicherheits-Checkliste für den Shop](../../unternehmen/shops/sicherheitscheckliste-fuer-shops.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
