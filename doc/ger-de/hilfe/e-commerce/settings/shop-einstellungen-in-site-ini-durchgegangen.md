---
title: Die Shop-Einstellungen in site.ini durchgegangen
audience: ecommerce, website-creator, admin
category: e-commerce
order: 32
summary: Die Shop-Einstellungen, die in site.ini liegen - ClearBasketOnCheckout, ClearBasketOnLogout, RedirectAfterAddToBasket, SendOrderEmail und ProductDatatypeStringList in ShopSettings, die Einstellungen BasketCleanup in Session und die Mail-Einstellungen, die die Bestellmail verwendet -, mit ausgelieferten Werten und wann Sie sie ändern.
keywords: site.ini, ShopSettings, ClearBasketOnCheckout, ClearBasketOnLogout, RedirectAfterAddToBasket, SendOrderEmail, ProductDatatypeStringList, BasketCleanup, BasketCleanupTime, BasketCleanupIdleTime, BasketCleanupFetchLimit, BasketCleanupAverageFrequency, MailSettings, AdminEmail, EmailSender, AllowedRedirectHosts, Warenkorb, Bestellmail, Aufräumen, basket, order mail, cleanup
type: help
---
# Die Shop-Einstellungen in site.ini durchgegangen

Diese Seite ist für Entwickler und Administratoren. Ein Teil des Shop-Verhaltens wird in `settings/site.ini`
festgelegt, nicht in `shop.ini`, und lässt sich deshalb leicht übersehen. Diese Seite listet jede solche Einstellung
mit ihrem ausgelieferten Wert und dem Grund, sie zu ändern.

## Inhalt

1. [ShopSettings](#shopsettings)
2. [Session: Aufräumen von Warenkörben](#session-aufräumen-von-warenkörben)
3. [MailSettings, die die Bestellmail verwendet](#mailsettings-die-die-bestellmail-verwendet)
4. [SiteSettings: Weiterleitungen zu einem Zahlungsanbieter](#sitesettings-weiterleitungen-zu-einem-zahlungsanbieter)
5. [Werte gemeinsam wählen](#werte-gemeinsam-wählen)

## ShopSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `ClearBasketOnCheckout` | `disabled` | Der Warenkorb wird geleert, wenn `shop/checkout` abgeschlossen ist, also nach der Zahlung. Ein Kunde, der die Zahlung abbricht, kehrt zu einem vollen Warenkorb zurück |
| | `enabled` | Der Warenkorb wird geleert, sobald der Kunde auf `shop/confirmorder` bestätigt. Der Kommentar in `site.ini` sagt, dass einige Zahlungssysteme dies brauchen; der Warenkorb ist dann weg, wenn die Zahlung abgebrochen wird |
| `ClearBasketOnLogout` | `disabled` | Abmelden behält den Warenkorb; `enabled` leert ihn |
| `RedirectAfterAddToBasket` | `basket` | Nach **Zum Warenkorb hinzufügen** sieht der Kunde den Warenkorb; `reload` schickt ihn dorthin zurück, wo er war |
| `SendOrderEmail` | `enabled` | Der Handler für die Bestellbestätigung sendet die Bestellmail an den Kunden und an den Shop; `disabled` sendet keine |
| `ProductDatatypeStringList[]` | auskommentiert | Die Datentypen, die Preise enthalten; wenn ungesetzt, `ezprice` und `ezmultiprice` ([Eigene Preis-Datentypen](../erweitern/eigene-preis-datentypen.md)) |

Was diese Einstellungen für den Kunden bedeuten, ist unter [Ablauf von Warenkorb und Kasse](../../unternehmen/shops/warenkorb-und-bestellablauf.md)
beschrieben.

## Session: Aufräumen von Warenkörben

Warenkörbe gehören zu Sitzungen. Der Block `[Session]` entscheidet, wie Warenkörbe abgelaufener Sitzungen entfernt
werden:

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `BasketCleanup` | `cronjob` | Im Cron-Skript `basket_cleanup` aufräumen; `pageload` überlässt es der Sitzungsverwaltung von PHP |
| `BasketCleanupTime` | `120` | Sekunden, die ein Aufräumlauf dauern darf |
| `BasketCleanupIdleTime` | `2` | Sekunden Pause zwischen den Stapeln, um die Last gering zu halten |
| `BasketCleanupFetchLimit` | `3000` | Je Stapel abgerufene Warenkörbe |
| `BasketCleanupAverageFrequency` | `10` | Im Durchschnitt läuft das Aufräumen einmal in zehn Cron-Läufen |

Das Skript `basket_cleanup.php` liegt im Cron-Teil `infrequent` (`settings/cronjob.ini`), dieser Teil muss also
laufen ([Cronjobs](../../../anleitungen/cronjobs.md)). Das Shop-Dashboard warnt, wenn das Aufräumen Warenkörbe löschen
würde, die in PHP-Sitzungen gehalten werden.

## MailSettings, die die Bestellmail verwendet

Der mitgelieferte Handler für die Bestellbestätigung (`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`)
verwendet:

| Einstellung | Verwendung |
|---|---|
| `[MailSettings] EmailSender` | Der Absender der Bestellmail; wenn leer, wird `AdminEmail` verwendet |
| `[MailSettings] AdminEmail` | Die Kopie jeder Bestellmail für den Shop geht an diese Adresse |

Text und Betreff der Mail kommen aus dem Template `shop/orderemail.tpl`. Soll die Kopie für den Shop an eine
Bestellannahme statt an den Administrator der Website gehen, braucht es einen eigenen Handler für die
Bestellbestätigung ([Handler für Warenkorbinformationen und Bestellbestätigung](../erweitern/handler-fuer-warenkorbinformationen-und-bestellbestaetigung.md)).

## SiteSettings: Weiterleitungen zu einem Zahlungsanbieter

Ein Gateway mit Weiterleitung schickt den Kunden zu einem anderen Host. Die Erweiterung `ezpaypal` ergänzt in ihrer
eigenen `site.ini.append` die Zeile `[SiteSettings] AllowedRedirectHosts[]=www.paypal.com`; ein eigenes Gateway
braucht möglicherweise dasselbe für den Host seines Anbieters.

## Werte gemeinsam wählen

| Shop | `ClearBasketOnCheckout` | `RedirectAfterAddToBasket` | `ClearBasketOnLogout` |
|---|---|---|---|
| Wenige Artikel je Bestellung | `disabled` | `basket` | `disabled` |
| Viele Artikel je Bestellung | `disabled` | `reload` | `disabled` |
| Die Dokumentation des Gateways verlangt frühes Leeren | `enabled` | wie oben | wie oben |
| Gemeinsam genutzte oder öffentliche Rechner (Kiosk) | `disabled` | wie oben | `enabled` |

## Verwandte Seiten

- [Die Shop-Einstellungen durchgegangen](README.md)
- [shop.ini durchgegangen](shop-ini-durchgegangen.md)
- [Bestellmails und Belege für Betreiber](../bestellungen/bestellmails-und-belege-fuer-betreiber.md)
- [Vorläufige Bestellungen und Aktivierung](../kasse/temporaere-bestellungen-und-aktivierung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
