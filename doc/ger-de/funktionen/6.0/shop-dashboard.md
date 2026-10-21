# Shop-Dashboard und Bestellstatus

Diese Seite ist für Shop-Betreiber und Administratoren, die den eingebauten Shop betreiben. Der Reiter **Shop** der
Administrationsoberfläche öffnet jetzt ein Dashboard (`shop/dashboard`), das auf einer Seite zeigt, wofür man früher eine
Anleitung brauchte, um es zusammenzusetzen: wie es dem Shop geht, welche Bestellungen Aufmerksamkeit brauchen und wie der
Bestellvorgang konfiguriert ist. Dasselbe Release fügt für jede Phase im Leben einer Bestellung einen Bestellstatus
hinzu.

## Öffnen

1. Melden Sie sich in der Administrationsoberfläche an.
2. Klicken Sie auf **Shop** (der Reiter hieß vor dem 30. September 2026 Web-Shop).
3. Das Dashboard ist der erste Eintrag des Shop-Menüs. Die Bestellliste bleibt im selben Menü einen Klick entfernt und
   ist vom Dashboard überall dort verlinkt, wo Bestellungen gezeigt werden.

Es braucht die Richtlinie `shop / administrate`, wie die Bestellliste und die Statistik. Titel und Texte sind übersetzt
(Deutsch eingeschlossen).

## Was auf der Seite steht

| Abschnitt | Was er Ihnen sagt |
|---|---|
| Auf einen Blick | Bestellungen heute, in 7 und 30 Tagen mit dem Trend gegenüber den 30 Tagen davor; Umsatz und durchschnittliche Bestellung pro Währung; Kunden (neu, wiederkehrend, insgesamt); offene Bestellungen und wie viele zu lange warten; laufende und abgebrochene Warenkörbe; Produkte und Produkte ohne Preis. |
| Diagramme und Ranglisten | Umsatz pro Tag, Verteilung der Bestellstatus, meistverkaufte Produkte. |
| Zu erledigen | Bestellungen, die auf den Shop warten, die längsten zuerst; die neuesten Bestellungen, Warenkörbe und unbestätigten Bestellvorgänge; die Produktklassen mit ihrem Preisattribut. |
| Wie Ihr Shop arbeitet | Der Bestellvorgang Schritt für Schritt, wie diese Installation ihn konfiguriert: in den Warenkorb legen, Warenkorb, der Handler für Shop-Konten, Bestätigungsseite mit Versand und MwSt., Zahlung (die Workflows an den Shop-Auslösern und die verfügbaren Gateways), die Bestell-E-Mails und der Beleg sowie die Bearbeitung über die Status. Jeder Schritt nennt die Einstellung oder Admin-Seite, die ihn ändert. Es folgen Bestellstatus, MwSt.-Typen, Regeln und die Länder, aus denen Bestellungen kamen, Währungen und Rabattgruppen. |
| Nächste Schritte | Vorschläge, die aus Ihren Daten und Einstellungen ermittelt werden, die dringendsten zuerst: ausstehende Bestellungen, die mehr als zwei Tage warten, kein Zahlungsschritt im Bestellvorgang, ein MwSt.-Typ mit 0 Prozent in Verwendung, ein Aufräumen von Warenkörben, das in PHP-Sitzungen gehaltene Warenkörbe löschen würde, und so weiter. |

Jede Zahl kommt aus einer festen Menge von etwa zwanzig Aggregatabfragen, gleich, wie groß der Shop ist, geschrieben ohne
`LIMIT` oder Engine-spezifische Funktionen, sodass sie auf Oracle, MySQL, PostgreSQL und SQLite gleich läuft. Summen
verwenden die Arithmetik der Bestellliste, sodass die Zahlen mit ihr übereinstimmen.

## Bestellstatus für den ganzen Lebenszyklus

Eine Installation brachte früher drei Status mit (Pending, Processing, Delivered), sodass ein Shop, der Zahlungen annimmt,
Pakete versendet oder Rücksendungen bearbeitet, eigene erfinden musste. Vierzehn weitere kommen jetzt mit jeder neuen
Installation und mit der Datenbankaktualisierung auf 6.0.15. Es sind interne Status (Bezeichner unter 1000), sodass sie
nicht versehentlich entfernt werden können. Eigene Status beginnen bei 1000.

| ID | Konstante in `eZOrderStatus` | Name | Abgeschlossen | Kein Umsatz | Wartet auf den Kunden |
|---|---|---|---|---|---|
| 1 | `PENDING` | Pending | | | ja |
| 2 | `PROCESSING` | Processing | | | |
| 3 | `DELIVERED` | Delivered | ja | | |
| 4 | `AWAITING_PAYMENT` | Awaiting payment | | | ja |
| 5 | `PAID` | Paid | | | |
| 6 | `PAYMENT_FAILED` | Payment failed | | ja | ja |
| 7 | `ON_HOLD` | On hold | | | |
| 8 | `BACKORDERED` | Backordered | | | |
| 9 | `PACKED` | Packed | | | |
| 10 | `SHIPPED` | Shipped | | | |
| 11 | `READY_FOR_PICKUP` | Ready for pickup | | | ja |
| 12 | `COMPLETED` | Completed | ja | | |
| 13 | `CANCELLED` | Cancelled | ja | ja | |
| 14 | `RETURN_REQUESTED` | Return requested | | | |
| 15 | `RETURNED` | Returned | | | |
| 16 | `PARTIALLY_REFUNDED` | Partially refunded | ja | | |
| 17 | `REFUNDED` | Refunded | ja | ja | |

Nach Phasen gruppiert: Zahlung (Awaiting payment, Paid, Payment failed), Erfüllung (On hold, Backordered, Packed,
Shipped, Ready for pickup), das Ende einer Bestellung (Completed, Cancelled) und danach (Return requested, Returned,
Partially refunded, Refunded). Die Namen sind die gespeicherten Namen der Status, wie die Installation sie anlegt.

Die drei Hilfsmethoden sagen Code und Dashboard, was jeder Status bedeutet:

```php
eZOrderStatus::finishedStatusIDs();          // no longer an open order
eZOrderStatus::noRevenueStatusIDs();         // brought in no money
eZOrderStatus::waitingForCustomerStatusIDs();// the shorter "waiting too long" limit applies
```

Auf dem Dashboard ist eine Bestellung **offen**, bis ihr Status abgeschlossen ist (nicht nur bis Delivered), sodass eine
stornierte oder erstattete Bestellung nicht mehr in der Liste der zu bearbeitenden Bestellungen wartet. Fehlgeschlagene,
stornierte und erstattete Bestellungen zählen als Bestellungen, aber nicht als Umsatz in den Summen, dem Durchschnitt,
dem Tagesdiagramm und den Top-Produkten. Bestellungen, die auf den Kunden warten, werden nach der kürzeren Grenze
hervorgehoben. Die Statustabelle führt die Status in der Reihenfolge des Lebenszyklus auf, sagt, was jeder bedeutet, und
färbt jeden nach dem, wofür er steht: neu, wartet auf den Kunden, in Bearbeitung, erledigt, angehalten oder eigener
Status.

### Einen bestehenden Shop aktualisieren

Die Startdaten jeder Engine tragen die neuen Status (MySQL, PostgreSQL, saubere Daten für SQLite und
`share/db_data.dba`). Die Skripte der Datenbankaktualisierung auf 6.0.15
(`update/database/<mysql|postgresql|sqlite>/6.0/dbupdate-6.0.0-6.0.15.sql`) fügen jeden Status nur hinzu, wenn noch kein
Status diese Nummer hat, und überlassen die Zeilen-ID der Datenbank, sodass ein von Ihnen angelegter eigener Status seine
Zeile behält. Wenn Sie eine der Nummern 4 bis 17 bereits für einen eigenen Status verwendet haben, prüfen Sie nach der
Aktualisierung die Liste der Bestellstatus des Shops.

## Verwandte Seiten

- [Bestellbelege](bestellbelege.md), [Sortierung der Bestellliste](sortierung-der-bestellliste.md), [Name der Warenkorbansicht im Shop](../../bc/6.0/view-name-des-shop-warenkorbs.md)
- [Navigation der Administrationsoberfläche: der oberste Knoten und die Reiter](oberster-knoten-des-inhaltsbaums-im-admin.md) (der Reiter Shop)
- [Installer-Protokolle und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
