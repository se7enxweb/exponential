---
title: Die Shop-Einstellungen durchgegangen
audience: ecommerce, website-creator, admin
category: e-commerce
order: 30
summary: Jede Einstellungsdatei, die einen Exponential-Shop prägt, mit dem Ort für Überschreibungen, der Prüfung des tatsächlich wirksamen Werts und einer Seite je Datei, die sie durchgeht - shop.ini, site.ini ShopSettings, shopaccount.ini, workflow.ini und paymentgateways.ini.
keywords: Shop-Einstellungen, shop.ini, site.ini, ShopSettings, shopaccount.ini, workflow.ini, paymentgateways.ini, paypal.ini, ezauthorize.ini, Überschreibung, Siteaccess-Einstellungen, INI-Cache, Einstellungsreferenz, shop settings, override, siteaccess settings, INI cache, settings reference
type: help
---
# Die Shop-Einstellungen durchgegangen

Dieser Teil ist für Entwickler und Administratoren, die einen Exponential-Shop konfigurieren. Der Shop liest sein
Verhalten aus fünf Einstellungsdateien des Kernels und aus den Einstellungen der Zahlungserweiterungen. Jede Seite
dieses Teils geht eine Datei Block für Block durch: den ausgelieferten Wert, was er bewirkt und wann Sie ihn ändern.

## Inhalt

1. [Die Dateien](#die-dateien)
2. [Wohin Shop-Einstellungen gehören](#wohin-shop-einstellungen-gehören)
3. [Den wirksamen Wert prüfen](#den-wirksamen-wert-prüfen)
4. [Nach einer Änderung](#nach-einer-änderung)
5. [Eine minimale Shop-Konfiguration](#eine-minimale-shop-konfiguration)

## Die Dateien

| Datei | Entscheidet | Durchgang |
|---|---|---|
| `settings/shop.ini` | Währung, Rundung, Wechselkurse, Versand, Handler für Warenkorbinformationen und Umsatzsteuer, Name des Warenkorbs, Belege | [shop.ini durchgegangen](shop-ini-durchgegangen.md) |
| `settings/site.ini [ShopSettings]` und `[Session]` | Leeren des Warenkorbs, Weiterleitung nach dem Hinzufügen, Bestellmail, Preis-Datentypen, Aufräumen von Warenkörben | [Die Shop-Einstellungen in site.ini durchgegangen](shop-einstellungen-in-site-ini-durchgegangen.md) |
| `settings/shopaccount.ini` | Das Formular für die Kundendaten und der Handler für die Bestellmail | [shopaccount.ini durchgegangen](shopaccount-ini-durchgegangen.md) |
| `settings/workflow.ini` | Welche Ereignistypen und Shop-Trigger existieren; der pauschale Versandbetrag | [workflow.ini für den Shop](workflow-ini-fuer-den-shop.md) |
| `settings/paymentgateways.ini` | Welche Zahlungs-Gateways geladen werden und woher | [paymentgateways.ini durchgegangen](paymentgateways-ini-durchgegangen.md) |
| `paypal.ini`, `ezauthorize.ini` (Erweiterungen) | Die Einstellungen des Anbieters | [paymentgateways.ini durchgegangen](paymentgateways-ini-durchgegangen.md#die-anbieterdateien) |

## Wohin Shop-Einstellungen gehören

Bearbeiten Sie nie die mitgelieferten Dateien in `settings/`; ein Update überschreibt sie. Legen Sie Ihre Werte an
einem dieser Orte ab, vom allgemeinsten zum spezifischsten:

| Ort | Gilt für | Verwenden Sie ihn für |
|---|---|---|
| Die `settings/<file>.ini.append.php` einer Erweiterung | Jeden Siteaccess, in dem die Erweiterung aktiv ist | Handler und Gateways, die Sie mit einer Erweiterung ausliefern |
| `settings/override/<file>.ini.append.php` | Die ganze Installation | Shopweite Entscheidungen: Währung, USt-Handler, Belege |
| `settings/siteaccess/<siteaccess>/<file>.ini.append.php` | Einen Siteaccess | Unterschiede je Website, etwa eine Währung je Storefront ([Mehrere Shops und Multisite](../betrieb/mehrere-shops-und-multisite.md)) |

Die Endung `.php` mit dem in einen PHP-Kommentar eingeschlossenen Inhalt verhindert, dass die Datei als Text
ausgeliefert wird. Geheimnisse wie der API-Schlüssel eines Gateways oder `[OrderReceiptSettings] Secret` gehören nach
`settings/override/` und nie in ein Repository.

## Den wirksamen Wert prüfen

Überschreibungen stapeln sich, der zählende Wert ist also nicht immer der, den Sie zuletzt bearbeitet haben. Prüfen
Sie ihn, bevor Sie Fehler suchen:

- **Setup > Ini Einstellungen** (`settings/view`) zeigt für jede Einstellung den wirksamen Wert, die Datei, aus der
  er stammt, und jede Datei, die ihn setzt, und kann zwei Siteaccesses vergleichen ([Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)).
- In einer Shell beantworten `./console exp:ini where` und `./console exp:ini get` dieselben Fragen, zum Beispiel
  `./console exp:ini get shop.ini/VATSettings/Handler`.
- Der Abschnitt „So funktioniert Ihr Shop“ im Shop-Dashboard beschreibt die Kasse so, wie diese Installation
  konfiguriert ist, und nennt die Einstellung hinter jedem Schritt ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).

## Nach einer Änderung

```bash
php bin/php/ezcache.php --clear-tag=ini
```

Leeren Sie auch die Template-Caches, wenn eine Einstellung ändert, was Templates anzeigen (zum Beispiel
`OrderLinkView`). Auf Servern, die PHP zwischen Anfragen laufen lassen, laden Sie diese neu, wie Ihre
Hosting-Dokumentation es beschreibt ([Caches](../../../anleitungen/caches.md)).

## Eine minimale Shop-Konfiguration

Ein Shop in einer Währung, mit pauschalem Versand, PayPal und Umsatzsteuer nach Land braucht diese Überschreibungen:

```ini
# settings/override/shop.ini.append.php
[CurrencySettings]
PreferredCurrency=EUR

[VATSettings]
Handler=ezdefault
ProductCategoryAttribute=tax_class
UserCountryAttribute=country

# settings/override/workflow.ini.append.php
[SimpleShippingWorkflow]
ShippingCost=6.50
ShippingDescription=Shipping

# settings/override/paypal.ini.append.php
[PaypalSettings]
Business=<your PayPal business account>
```

Dann die Workflows in der Administration: Einfacher Versand an `before_shop_confirmorder` und das Ereignis
Zahlungs-Gateway an `shop_checkout` ([Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md)).

## Verwandte Seiten

- [Einstellungen und INI-Dateien](../../ersteller/anwendungsentwickler/einstellungen-und-ini-dateien.md)
- [Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md)
- [E-Commerce: hier beginnen](../e-commerce-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
