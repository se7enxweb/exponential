---
title: paymentgateways.ini durchgegangen
audience: ecommerce, website-creator, developer
category: e-commerce
order: 35
summary: Wie Exponential Zahlungs-Gateways findet - paymentgateways.ini AvailableGateways und GatewaysDirectories, das automatische Laden einer Gateway-Klasse aus einer aktiven Erweiterung, registerGateway und die Anbieterdateien paypal.ini und ezauthorize.ini.
keywords: paymentgateways.ini, GatewaysSettings, AvailableGateways, GatewaysDirectories, GatewaysDerictories, registerGateway, eZPaymentGatewayType, paypal.ini, ezauthorize.ini, TestMode, Business, PaymentMadeOrderStatusID, Laden von Gateways, Zahlungs-Gateway, gateway loading, payment gateway
type: help
---
# paymentgateways.ini durchgegangen

Diese Seite ist für Entwickler, die ein Zahlungs-Gateway anbinden oder schreiben. Sie erklärt die zwei Wege, auf denen
das Ereignis Zahlungs-Gateway Gateway-Klassen findet, die Einstellungen von `settings/paymentgateways.ini` und die
Anbieterdateien der beiden Zahlungserweiterungen im Repository.

## Inhalt

1. [GatewaysSettings](#gatewayssettings)
2. [Wie Gateways geladen werden](#wie-gateways-geladen-werden)
3. [Die Anbieterdateien](#die-anbieterdateien)
4. [Prüfen, ob ein Gateway verfügbar ist](#prüfen-ob-ein-gateway-verfügbar-ist)

## GatewaysSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `AvailableGateways[]` | leer | Namen der Gateways, die aus `GatewaysDirectories[]` geladen werden |
| `GatewaysDirectories[]` | `kernel/shop/paymentgateways` | Zu durchsuchende Verzeichnisse; dieses Kernel-Verzeichnis wird nicht mitgeliefert, also wird daraus nichts geladen |

Die alte Falschschreibung `GatewaysDerictories` wird weiterhin gelesen, wenn sie vorhanden ist (`eZPaymentGatewayType::loadAndRegisterBuiltInGateways()`).

## Wie Gateways geladen werden

Wenn der Ereignistyp Zahlungs-Gateway erzeugt wird, lädt er Gateways auf zwei Wegen
(`kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php`):

| Weg | Eingebundene Datei | Verwendet von |
|---|---|---|
| Aufgeführte Gateways | `<GatewaysDirectories>/<gateway>/classes/<gateway>gateway.php` für jeden Namen in `AvailableGateways[]` | Gateways, die außerhalb der Konvention für Erweiterungen liegen |
| Aktive Erweiterungen | `extension/<extension>/classes/<extension>gateway.php` für jede aktive Erweiterung | `ezpaypal` (`classes/ezpaypalgateway.php`), `ezauthorize` (`classes/ezauthorizegateway.php`) |

Das Einbinden der Datei genügt nicht: Am Ende seiner Datei ruft jedes Gateway auf

```php
eZPaymentGatewayType::registerGateway( <type>, '<class name>', '<description>' );
```

und nur registrierte Gateways erscheinen im Ereignis Zahlungs-Gateway. Wird derselbe Typ zweimal registriert, wird ein
Fehler protokolliert und der erste behalten. Der einfachste Weg für ein eigenes Gateway ist die Konvention für
Erweiterungen: eine Erweiterung `mypay` mit `classes/mypaygateway.php` ([Entwicklung von Zahlungs-Gateways](../erweitern/entwicklung-von-zahlungs-gateways.md)).

## Die Anbieterdateien

### ezpaypal: paypal.ini

| Block und Einstellung | Ausgeliefert | Hinweis |
|---|---|---|
| `[ServerSettings] ServerName`, `ServerPort`, `RequestURI` | `https://www.paypal.com`, `443`, `/cgi-bin/webscr` | Zeigen Sie beim Testen auf die Sandbox von PayPal |
| `[PaypalSettings] Business` | leer | Ihr PayPal-Geschäftskonto; erforderlich |
| `[PaypalSettings] MaxDescriptionLength` | `127` | Längste gesendete Bestellbeschreibung |
| `[PaypalSettings] NoNote`, `NoteLabel`, `PageStyle`, `BackgroundColor`, `LogoURI` | `0`, leer, leer, `0`, leer | Aussehen der PayPal-Seite |
| `[OrderSettings] PaymentMadeOrderStatusID` | `1000` | Status, der gesetzt wird, wenn die Zahlung bestätigt ist; siehe den Hinweis unter [PayPal-Zahlungen](../../unternehmen/shops/zahlungen-mit-paypal.md) |

Die Erweiterung registriert außerdem die Modul-View `paypal/notify_url` für den Callback von PayPal, nimmt sie von
den Richtlinienprüfungen aus (`[RoleSettings] PolicyOmitList[]=paypal/notify_url`) und erlaubt den
Weiterleitungs-Host (`[SiteSettings] AllowedRedirectHosts[]=www.paypal.com`). Die vollständige Tabelle steht unter
[ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md).

### ezauthorize: ezauthorize.ini

`ezauthorize` ist ein transparentes Gateway: `eZAuthorizeGateway` erweitert `eZCurlGateway`, das `eZPaymentGateway`
erweitert; Kartendaten werden also auf Ihrer Website eingegeben und von Ihrem Server an Authorize.Net gesendet. Seine
`settings/ezauthorize.ini.append.php` enthält unter anderem:

| Einstellung | Ausgeliefert | Hinweis |
|---|---|---|
| `MerchantLogin`, `TransactionKey` | `false` | Ihre Zugangsdaten für Authorize.Net; halten Sie sie in `settings/override/` |
| `TestMode` | `true` | Bis zum Go-live-Test eingeschaltet lassen |
| `SSLVerify` | `true` | Eingeschaltet lassen |
| `CustomerCVV2Check` | `false` | Nach dem Sicherheitscode der Karte fragen |
| `MD5HashVerification`, `MD5HashSecretWord` | `true`, `publish` | Ändern Sie das geheime Wort gegenüber dem ausgelieferten Wert |
| `OrderStatusRefundCode`, `ShowAuthorizeDotNetRefundButton` | `1000`, `true` | Die Erweiterung ergänzt eine Erstattungs-View (Modul `refund`) |
| `ShopAdminEmailAddress` | `info@example.com` | Durch Ihre eigene Adresse ersetzen |

Weil Kartendaten bei diesem Gateway über Ihren Server laufen, lesen Sie
[Grundlagen zu Kartenzahlungen und PCI](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md), bevor Sie es wählen.

## Prüfen, ob ein Gateway verfügbar ist

1. Aktivieren Sie die Erweiterung und leeren Sie den INI-Cache.
2. Öffnen Sie **Setup > Workflows**, legen Sie den Kassen-Workflow an oder bearbeiten Sie ihn und fügen Sie das
   Ereignis **Zahlungs-Gateway** hinzu.
3. Die Beschreibung des Gateways („Paypal“, „Authorize.Net“ oder Ihre) wird zur Auswahl aufgeführt. Fehlt sie, wurde
   die Datei nicht gefunden oder `registerGateway()` nicht aufgerufen.
4. Das Ereignis Zahlungs-Gateway schreibt ein Log nach `var/log/eZPaymentGatewayType.log`; Gateways mit
   Weiterleitung schreiben zusätzlich `var/log/eZRedirectGateway.log` ([Fehlersuche an der Kasse](../betrieb/fehlersuche-an-der-kasse.md)).

## Verwandte Seiten

- [Die Shop-Einstellungen durchgegangen](README.md)
- [Das Ereignis Zahlungs-Gateway](../kasse/das-ereignis-zahlungs-gateway.md)
- [Zahlungs-Gateways (Website-Ersteller)](../../ersteller/website-ersteller/payment-gateways.md)
- [Zahlungs-Gateways (Ihren Shop betreiben)](../../kunden/handel/zahlungsanbieter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
