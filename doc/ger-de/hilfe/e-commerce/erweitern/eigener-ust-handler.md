---
title: Eigener USt-Handler
audience: ecommerce, app-creator, developer
category: e-commerce
order: 62
summary: Die Entscheidung über die Umsatzsteuer in einem Exponential-Shop durch einen eigenen Handler ersetzen - der Vertrag von getVatPercent, die Erweiterung von eZDefaultVATHandler, die Registrierung in shop.ini, RequireUserCountry und Fälle wie Geschäftskunden mit USt-IdNr. oder digitale Dienstleistungen.
keywords: USt-Handler, Umsatzsteuer, getVatPercent, eZDefaultVATHandler, eZVATManager, VATSettings, Handler, vathandlers, dynamische Umsatzsteuer, RequireUserCountry, Geschäftskunden, Reverse Charge, digitale Dienstleistungen, eigene Steuer, VAT handler, dynamic VAT, business customers, reverse charge, digital services, custom tax
type: help
---
# Eigener USt-Handler

Diese Seite ist für Entwickler, deren Umsatzsteuerregeln sich nicht mit USt-Regeln nach Land und Produktkategorie
ausdrücken lassen. Der Shop fragt einen USt-Handler nach dem Prozentsatz jedes Produkts mit dynamischer
Umsatzsteuer; Sie können Ihren eigenen Handler schreiben und alles andere behalten.

## Inhalt

1. [Wann Sie einen brauchen](#wann-sie-einen-brauchen)
2. [Der Vertrag](#der-vertrag)
3. [Ihn registrieren](#ihn-registrieren)
4. [Auf dem Standard-Handler aufbauen](#auf-dem-standard-handler-aufbauen)
5. [Testen](#testen)

## Wann Sie einen brauchen

| Situation | Reicht der Standard-Handler? |
|---|---|
| Steuersatz nach Land des Kunden und Produktkategorie | Ja ([Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md)) |
| Ein anderer Satz für Geschäftskunden mit gültiger USt-IdNr. | Nein |
| Der Satz hängt von der Lieferadresse statt vom Land des Kontos ab | Nein |
| Die Sätze kommen von einem externen Steuerdienst | Nein |

Die Steuerregeln selbst sind Sache Ihres Steuerberaters; der Handler setzt nur um, was Sie entscheiden.

## Der Vertrag

`eZVATManager::getVAT( $object, $country )` (`kernel/classes/ezvatmanager.php`) lädt den Handler und ruft auf:

```php
function getVatPercent( $object, $country )
```

| Argument | Bedeutung |
|---|---|
| `$object` | Das Inhaltsobjekt des Produkts |
| `$country` | Der Ländercode des Kunden oder `false`, wenn unbekannt |

Geben Sie den Prozentsatz als Zahl zurück. Die Rückgabe von `null` kennzeichnet die Umsatzsteuer als unbekannt. Der
Handler wird nur für Produkte gefragt, deren Preis den dynamischen USt-Typ verwendet; Produkte mit einem festen
USt-Typ behalten diesen.

## Ihn registrieren

```ini
# extension/mystore_shop/settings/shop.ini.append.php
[VATSettings]
Handler=mystore
ExtensionDirectories[]=mystore_shop
RequireUserCountry=false
```

Die Klasse `mystoreVATHandler` gehört in `extension/mystore_shop/vathandlers/mystorevathandler.php`. Setzen Sie
`RequireUserCountry=false` nur, wenn Ihr Handler das Land nicht braucht; der mitgelieferte Kommentar sagt das ebenfalls
([shop.ini durchgegangen](../einstellungen/shop-ini-durchgegangen.md#vatsettings)).

## Auf dem Standard-Handler aufbauen

Wenn Sie `eZDefaultVATHandler` (`kernel/classes/vathandlers/ezdefaultvathandler.php`) erweitern, bleibt die
regelbasierte Entscheidung erhalten, und Sie können zuerst eine Ausnahme behandeln:

```php
class mystoreVATHandler extends eZDefaultVATHandler
{
    function getVatPercent( $object, $country )
    {
        if ( $this->isBusinessCustomerWithValidNumber() )
        {
            return 0;
        }
        return parent::getVatPercent( $object, $country );
    }
}
```

`isBusinessCustomerWithValidNumber()` schreiben Sie selbst; woher die USt-IdNr. kommt (ein Kontofeld, das
Kassenformular Ihres Shop-Konto-Handlers), ist Ihr Entwurf. Der RAD-Handler-Assistent hat ein Rezept für
USt-Handler, das dieses Gerüst schreibt ([Pfad für Entwickler](../rollen/pfad-fuer-entwickler.md#generatoren-die-zeit-sparen)).

## Testen

- [ ] Unit-Tests von `getVatPercent()` für jeden Fall, den Sie umsetzen.
- [ ] Eine Testbestellung je Fall; Bestätigungsseite, Bestellung und Mail stimmen überein.
- [ ] Unbekanntes Land: Das Ergebnis ist das, was Sie beabsichtigen, keine versehentliche 0.
- [ ] Das Shop-Dashboard zeigt keine unerwartete Warnung zu 0 Prozent.

## Verwandte Seiten

- [Den Shop erweitern](README.md)
- [USt-Regeln und USt-Typen](../../unternehmen/shops/ust-regeln-und-ust-typen.md)
- [Checkliste zur Umsatzsteuer](../../kunden/handel/mehrwertsteuer-checkliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
