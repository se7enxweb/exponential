---
title: Shop-Konto-Handler
audience: ecommerce, app-creator, developer
category: e-commerce
order: 64
summary: Einen Shop-Konto-Handler für einen Exponential-Shop schreiben, um an der Kasse andere Kundendaten zu erfassen - die fünf Methoden, wie die Daten mit der Bestellung gespeichert werden, die Formular-View und die Templates, die Registrierung in shopaccount.ini und Beispiele wie Felder für Firma und USt-IdNr.
keywords: Shop-Konto-Handler, eZShopAccountHandler, verifyAccountInformation, fetchAccountInformation, email, accountName, accountInformation, shopaccount.ini, shopaccounthandlers, Kundendaten, Kassenformular, Firmenname, USt-IdNr., Lieferadresse, shop account handler, customer data, checkout form, company name, VAT number, delivery address
type: help
---
# Shop-Konto-Handler

Diese Seite ist für Entwickler, die an der Kasse andere Kundendaten abfragen müssen: einen Firmennamen, eine
USt-IdNr., eine separate Lieferadresse, eine Telefonnummer. Der Shop-Konto-Handler entscheidet, welches Formular der
Kunde ausfüllt und wie die Kundendaten der Bestellung zurückgelesen werden. Die mitgelieferten Handler sind unter
[shopaccount.ini durchgegangen](../einstellungen/shopaccount-ini-durchgegangen.md) beschrieben.

## Inhalt

1. [Die fünf Methoden](#die-fünf-methoden)
2. [Wo die Daten liegen](#wo-die-daten-liegen)
3. [Einen schreiben](#einen-schreiben)
4. [Ihn registrieren](#ihn-registrieren)
5. [Checkliste](#checkliste)

## Die fünf Methoden

Die mitgelieferten Handler in `kernel/classes/shopaccounthandlers/` implementieren alle:

| Methode | Aufgerufen | Gibt zurück |
|---|---|---|
| `verifyAccountInformation()` | Bei **Kasse** im Warenkorb | `true`, wenn die Kasse ohne Formular weitergehen kann |
| `fetchAccountInformation( &$module )` | Wenn die Prüfung fehlgeschlagen ist | Leitet zum Formular (oder zur Anmeldung) weiter |
| `email( $order )` | Bestellmail, Bestellliste, Kundenliste | Die E-Mail-Adresse des Kunden |
| `accountName( $order )` | Bestellliste, Kundenliste | Den Namen des Kunden |
| `accountInformation( $order )` | Bestellansicht, Beleg, Mail-Templates | Ein Array mit den Daten des Kunden |

`eZShopAccountHandler::instance()` (`kernel/classes/ezshopaccounthandler.php`) lädt den in `[AccountSettings]
Handler` genannten Handler als Klasse `<name>ShopAccountHandler` und fällt auf `eZDefaultShopAccountHandler` zurück.

## Wo die Daten liegen

Die Handler `ezuser`, `ezsimple` und `bcusauser` speichern die Daten des Formulars in der Bestellung selbst, als XML
in `ezorder.data_text_1`, und parsen sie in `email()`, `accountName()` und `accountInformation()` zurück. Weil die
Daten in der Bestellung liegen, bleiben sie so, wie sie eingegeben wurden, auch wenn sich das Konto des Kunden später
ändert. `bcusauser` zeigt, wie Sie mit fast keinem Code Felder zu einem bestehenden Handler hinzufügen: Er erweitert
`eZUserShopAccountHandler`.

## Einen schreiben

1. Kopieren Sie die Formular-View, von der Sie ausgehen: `shop/userregister` legt die vorläufige Bestellung an und
   speichert das XML (`kernel/private/classes/views/shop/userregister.php`). Legen Sie Ihre eigene View in das Modul
   Ihrer Erweiterung, mit den zusätzlichen Feldern, der Validierung und derselben Anlage der Bestellung.
2. Schreiben Sie den Handler: `fetchAccountInformation()` leitet auf Ihre View weiter; `email()`, `accountName()` und
   `accountInformation()` lesen Ihr XML, einschließlich der neuen Felder.
3. Templates: Die Bestellansicht und die Belege binden je Handler ein Konto-Template aus
   `design/standard/templates/shop/accounthandlers/` ein (`html/` und `ascii/`); fügen Sie Ihres in Ihrem Design hinzu.
4. Wenn die Umsatzsteuer vom Land abhängt, geben Sie dem Formular ein Länderfeld und legen Sie es dort ab, wo
   `eZVATManager` es findet ([Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md#das-land-des-kunden)).

Schützen Sie das Formular wie jedes Formular ([Formulare, CSRF und HTTP-Eingaben](../../ersteller/anwendungsentwickler/formulare-csrf-und-http-eingaben.md))
und speichern Sie nur die Daten, die Sie brauchen ([DSGVO-Anfragen](../../kunden/handel/dsgvo-anfragen.md)).

## Ihn registrieren

```ini
# extension/mystore_shop/settings/shopaccount.ini.append.php
[HandlerSettings]
ExtensionRepositories[]=mystore_shop

[AccountSettings]
Handler=mystore
```

mit der Klasse in `extension/mystore_shop/shopaccounthandlers/mystoreshopaccounthandler.php`. Die Erweiterung
`ezauthorize` liefert ein Beispiel für diesen Aufbau mit (`shopaccounthandlers/ezcustomshopaccounthandler.php`).

## Checkliste

- [ ] Kasse als Gast und mit Anmeldung erreichen beide die Bestätigungsseite.
- [ ] Jedes neue Feld erscheint in Bestellansicht, Beleg, Bestellmail und Kundenliste.
- [ ] Alte Bestellungen, die mit dem vorherigen Handler aufgegeben wurden, werden weiterhin angezeigt.
- [ ] Ungültige Eingaben werden mit einer klaren, barrierefreien Meldung abgelehnt ([Barrierefreiheit im Shop](../betrieb/barrierefreiheit-im-shop.md)).

## Verwandte Seiten

- [Den Shop erweitern](README.md)
- [Kundenkonten und Kasse als Gast](../../unternehmen/shops/kundenkonten-und-gastbestellung.md)
- [Kundenkonten (Ihren Shop betreiben)](../../kunden/handel/kundenkonten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
