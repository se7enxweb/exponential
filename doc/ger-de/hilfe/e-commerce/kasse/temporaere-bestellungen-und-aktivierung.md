---
title: Temporäre Bestellungen und Aktivierung
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 42
summary: Warum eine Exponential-Bestellung existiert, bevor der Kunde bezahlt hat - die an der Kasse angelegte temporäre Bestellung, wie sie die Produktsammlung des Warenkorbs teilt, was die Aktivierung ändert (Bestellnummer, Statusverlauf, Mail, Prüfpfad) und warum temporäre Bestellungen nie in Listen oder Statistiken erscheinen.
keywords: temporäre Bestellung, is_temporary, Aktivierung, activate-order, Bestellnummer, order_nr, ezorder_nr_incr, Produktsammlung, MyTemporaryOrderID, Statusverlauf, Pending, unbestätigter Bestellabschluss, temporary order, activation, order number, product collection, status history, unconfirmed checkout
type: help
---
# Temporäre Bestellungen und Aktivierung

Diese Seite ist für Entwickler und Shop-Manager, die sich fragen, warum eine Bestellung ohne Bestellnummer existieren
kann oder warum ein Zahlungsanbieter eine Zahlung zeigt, die die Bestellliste nicht kennt. Sie erklärt die temporäre
Bestellung: den Datensatz, mit dem die Kasse arbeitet, bis die Zahlung abgeschlossen ist.

## Inhalt

1. [Was eine temporäre Bestellung ist](#was-eine-temporäre-bestellung-ist)
2. [Ihr Lebenslauf](#ihr-lebenslauf)
3. [Was die Aktivierung ändert](#was-die-aktivierung-ändert)
4. [Wo temporäre Bestellungen erscheinen und wo nicht](#wo-temporäre-bestellungen-erscheinen-und-wo-nicht)
5. [Bestell-ID und Bestellnummer](#bestell-id-und-bestellnummer)
6. [Was das für Sie bedeutet](#was-das-für-sie-bedeutet)

## Was eine temporäre Bestellung ist

Wenn der Kunde zur Kasse geht, legt `eZBasket::createOrder()` (`kernel/classes/ezbasket.php`) eine Zeile in `ezorder` an,
mit `is_temporary = 1`, dem Status **Pending**, dem aktuellen Benutzer und der Produktsammlung des Warenkorbs. Der
Warenkorb merkt sich die Bestellung (`order_id`), und die Session merkt sie sich als `MyTemporaryOrderID`. Warenkorb und
temporäre Bestellung teilen sich eine Produktsammlung, Posten werden also nicht kopiert.

## Ihr Lebenslauf

| Moment | Zustand |
|---|---|
| Schaltfläche Kasse, Kundendaten angegeben | Temporäre Bestellung angelegt, `order_nr = 0` |
| Bestätigungsseite | Versandzeilen als Bestellposten hinzugefügt; noch temporär |
| Abbrechen auf der Bestätigungsseite | Der Warenkorb kommt mit seinen Posten zurück |
| Zahlung begonnen (Weiterleitungs-Gateway) | Für den Workflow-Prozess wird ein Zahlungsobjekt gespeichert; noch temporär |
| Zahlung bestätigt, Workflow läuft weiter | `activate-order` läuft: Die Bestellung ist aufgegeben |
| Zahlung nie bestätigt | Bleibt temporär; wird nie als Bestellung angezeigt |

## Was die Aktivierung ändert

`eZOrder::activate()` (`kernel/classes/ezorder.php`), ausgeführt von der Methode `activate-order` der Kassenoperation:

1. `is_temporary` wird `0`.
2. Die Bestellung erhält ihre **Bestellnummer** aus der eigenen Zählertabelle `ezorder_nr_incr`.
3. Für den aktuellen Status wird ein erster Eintrag im Statusverlauf angelegt.
4. Ist der Prüfpfad aktiv, wird das Ereignis `commerce.order.create` mit Bestellnummer, Summe und Währung aufgezeichnet,
   nie mit der Adresse des Kunden ([Prüfpfad für den Handel](../betrieb/audit-trail-fuer-den-handel.md)).

Direkt nach der Aktivierung versendet die Kassenoperation die Bestellmail (`send-order-email`) und führt den
Nachher-Trigger von `shop_checkout` aus. Der Warenkorb wird geleert, wenn die Kasse abgeschlossen ist, es sei denn,
`ClearBasketOnCheckout=enabled` hat ihn früher geleert
([Die Shop-Einstellungen in site.ini durchgegangen](../settings/shop-einstellungen-in-site-ini-durchgegangen.md)).

## Wo temporäre Bestellungen erscheinen und wo nicht

Bestellliste, Archiv, Kundenliste, Bestellstatistik und Zählungen filtern alle auf `is_temporary = 0`
(`kernel/classes/ezorder.php`). Temporäre Bestellungen zählen daher nicht als Bestellungen oder Umsatz. Das
Shop-Dashboard listet „unconfirmed checkouts“ unter **Zu erledigen** auf; dort finden Sie Bestellabschlüsse, die auf
halbem Weg stehen geblieben sind ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).

Die Bereinigung der Warenkörbe entfernt abgelaufene Warenkörbe und ihre Produktsammlungen (`eZBasket::cleanupExpired()`);
im geprüften Code löscht sie keine Zeilen aus `ezorder`.

## Bestell-ID und Bestellnummer

| | Bestell-ID (`id`) | Bestellnummer (`order_nr`) |
|---|---|---|
| Vergeben | Wenn die temporäre Bestellung angelegt wird | Wenn die Bestellung aktiviert wird |
| Lücken | Ja: Jeder abgebrochene Bestellabschluss verbraucht eine ID | Nur aufgegebene Bestellungen erhalten eine |
| Sichtbar für | Interne Adressen wie `shop/orderview/<id>` | Kunden, in Bestellansicht, Mail und Beleg |

Bitten Sie Kunden und Mitarbeitende, die Bestellnummer anzugeben. Entfernte Dienste können eine Bestellung über die
Nummer finden (`exporder::byNumber`, [Headless Commerce mit expservices](../betrieb/headless-commerce-mit-expservices.md)).

## Was das für Sie bedeutet

- Eine Zahlung beim Anbieter ohne Bestellung in der Liste bedeutet, dass die Aktivierung nie lief: Der Rückruf kam
  nicht an, oder der Workflow lief nicht weiter ([Fehlersuche an der Kasse](../betrieb/fehlersuche-an-der-kasse.md)).
- Ereignisse, die die Bestellnummer brauchen oder einmal pro aufgegebener Bestellung laufen müssen, gehören an den
  Nachher-Trigger von `shop_checkout`, nicht an den Vorher-Trigger ([Workflows an Shop-Triggern](workflows-an-shop-triggern.md)).
- Berichte auf Basis der Tabelle `ezorder` müssen wie der Kernel auf `is_temporary = 0` filtern.

## Verwandte Seiten

- [Die Kassenoperationen Schritt für Schritt](die-kassenoperationen-schritt-fuer-schritt.md)
- [Der Lebenszyklus einer Bestellung im Betrieb](../bestellungen/der-lebenszyklus-einer-bestellung-im-betrieb.md)
- [Bestellungen und Bestellstatus](../../unternehmen/shops/bestellungen-und-bestellstatus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
