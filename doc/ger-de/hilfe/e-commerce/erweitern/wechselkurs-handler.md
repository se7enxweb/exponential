---
title: Wechselkurs-Handler
audience: ecommerce, app-creator, developer
category: e-commerce
order: 66
summary: Woher ein Exponential-Shop seine Wechselkurse bezieht - der Handler eZECB, die Basisklasse eZExchangeRatesUpdateHandler mit ihrer Kursliste und ihren Fehlercodes, einen Handler für eine andere Kursquelle schreiben, die Registrierung und die Planung von Kurs- und Preisaktualisierungen.
keywords: Wechselkurse, Wechselkurs-Handler, eZExchangeRatesUpdateHandler, eZECB, eZECBHandler, requestRates, rateList, baseCurrency, ExchangeRatesUpdateHandler, BaseCurrency, Auto-Umrechnungsfaktoren aktualisieren, Auto-Preis aktualisieren, mehrere Währungen, exchange rates, exchange rate handler, Update autoprices, multi-currency
type: help
---
# Wechselkurs-Handler

Diese Seite ist für Entwickler von Shops, die in mehreren Währungen mit automatischen Preisen verkaufen
([Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md#wie-ezmultiprice-mit-währungen-umgeht)). Die Kurse
werden von einem Handler zur Aktualisierung der Wechselkurse abgerufen, wenn jemand unter **Web-Shop > Währungen** auf
**Auto-Umrechnungsfaktoren aktualisieren** klickt. Der Kernel liefert einen Handler mit, für die Europäische Zentralbank; diese Seite zeigt,
wie Sie eine andere Quelle verwenden.

## Inhalt

1. [Der mitgelieferte Handler](#der-mitgelieferte-handler)
2. [Die Basisklasse](#die-basisklasse)
3. [Einen Handler schreiben](#einen-handler-schreiben)
4. [Ihn registrieren](#ihn-registrieren)
5. [Kurse und Preise aktuell halten](#kurse-und-preise-aktuell-halten)

## Der mitgelieferte Handler

`eZECBHandler` (`kernel/shop/classes/exchangeratehandlers/ezecb/ezecbhandler.php`) liest Server, Port und Adresse
aus `shop.ini [ECBExchangeRatesSettings]` und dort die tägliche Kursdatei. Seine Kurse beziehen sich auf den Euro,
passend zum mitgelieferten `[ExchangeRatesSettings] BaseCurrency=EUR` ([shop.ini durchgegangen](../einstellungen/shop-ini-durchgegangen.md#exchangeratessettings)).

## Die Basisklasse

`eZExchangeRatesUpdateHandler` (`kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php`):

| Element | Bedeutung |
|---|---|
| `initialize( $params )` | Liest Einstellungen; die Basis liest `BaseCurrency` |
| `requestRates()` | Ruft Kurse ab und speichert sie mit `setRateList()`; gibt einen Status zurück |
| `rateList()`, `setRateList()` | Die Kurse, nach Währungscode geschlüsselt |
| `baseCurrency()`, `setBaseCurrency()` | Die Währung, auf die sich die Kurse beziehen |
| `OK`, `CANT_CREATE_HANDLER`, `FAILED`, `EMPTY_RATE_LIST`, `UNKNOWN_BASE_CURRENCY`, `INVALID_BASE_CROSS_RATE` | Statuscodes |

## Einen Handler schreiben

1. Erweitern Sie `eZExchangeRatesUpdateHandler`; lesen Sie die Einstellungen Ihrer Quelle in `initialize()`, nachdem
   Sie die Elternklasse aufgerufen haben.
2. Rufen Sie in `requestRates()` die Kurse ab, bauen Sie ein Array `currency code => rate` relativ zu Ihrer
   Basiswährung, rufen Sie `setRateList()` auf und geben Sie `self::OK` oder einen Fehlercode zurück.
3. Scheitern Sie sicher: Geben Sie bei einem Netzwerkfehler oder einer leeren Antwort einen Fehlercode zurück und
   lassen Sie die alten Kurse stehen, damit Preise nie mit Unsinn umgerechnet werden.

Der RAD-Handler-Assistent hat ein Rezept für Wechselkurs-Handler ([Pfad für Entwickler](../rollen/pfad-fuer-entwickler.md#generatoren-die-zeit-sparen)).

## Ihn registrieren

`eZExchangeRatesUpdateHandler::create()` schreibt den Namen klein und bindet `<directory>/<name>/<name>handler.php`
ein, Klasse `<name>handler`:

```ini
# extension/mystore_shop/settings/shop.ini.append.php
[ExchangeRatesSettings]
ExchangeRatesUpdateHandler=mystorerates
ExtensionDirectories[]=mystore_shop
BaseCurrency=EUR
```

Datei: `extension/mystore_shop/exchangeratehandlers/mystorerates/mystorerateshandler.php`, Klasse `mystoreratesHandler`.

## Kurse und Preise aktuell halten

Zwei getrennte Schritte aktualisieren einen Shop mit mehreren Währungen: **Auto-Umrechnungsfaktoren aktualisieren** ruft die Kurse ab,
**Auto-Preis aktualisieren** berechnet die automatischen Preise neu (`eZShopFunctions::updateAutoRates()`,
`updateAutoprices()`). Keiner der beiden läuft in den mitgelieferten Einstellungen von selbst. Machen Sie sie entweder
zum Teil einer Routine ([Betriebskalender](../betrieb/betriebskalender.md)) oder schreiben Sie einen Cronjob-Teil,
der beide aufruft ([Einen Cronjob-Teil schreiben](../../ersteller/anwendungsentwickler/einen-cronjob-teil-schreiben.md)). Eigene
Preise werden von keinem der beiden Schritte angefasst.

## Verwandte Seiten

- [Den Shop erweitern](README.md)
- [Preise und Währungen (Ihren Shop betreiben)](../../kunden/handel/preise-und-waehrungen.md)
- [Preise und Währungen (Unternehmen)](../../unternehmen/shops/preise-und-waehrungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
