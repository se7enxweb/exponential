---
title: Entwicklung von Zahlungs-Gateways
audience: ecommerce, app-creator, developer
category: e-commerce
order: 63
summary: Ein Zahlungs-Gateway für einen Anbieter bauen, den Exponential noch nicht unterstützt - Weiterleitung oder transparent, die Dateien der Erweiterung, die Gateway-Klasse, das Zahlungsobjekt, die Callback-View mit ihren Prüfungen, Registrierung, Protokollierung, Sicherheitsregeln und ein Testplan, nach dem Vorbild der Erweiterung ezpaypal.
keywords: Entwicklung von Zahlungs-Gateways, eigenes Gateway, eZRedirectGateway, eZPaymentGateway, eZPaymentObject, eZPaymentCallbackChecker, registerGateway, createPaymentObject, createRedirectionUrl, notify_url, PolicyOmitList, AllowedRedirectHosts, Webhook, Callback, Anbieterintegration, payment gateway development, custom gateway, webhook, callback, provider integration
type: help
---
# Entwicklung von Zahlungs-Gateways

Diese Seite ist für Entwickler, die einen Exponential-Shop an einen Zahlungsanbieter anbinden, für den es keine
Erweiterung gibt. Das Repository liefert die Basisklassen und zwei vollständige Beispiele mit, `ezpaypal`
(Weiterleitung) und `ezauthorize` (transparent). Diese Seite beschreibt, wie sie aufgebaut sind, damit Sie das nächste
bauen können.

## Inhalt

1. [Weiterleitung oder transparent](#weiterleitung-oder-transparent)
2. [Die Dateien der Erweiterung](#die-dateien-der-erweiterung)
3. [Die Gateway-Klasse](#die-gateway-klasse)
4. [Der Callback](#der-callback)
5. [Einstellungen, die die Erweiterung ergänzt](#einstellungen-die-die-erweiterung-ergänzt)
6. [Sicherheitsregeln](#sicherheitsregeln)
7. [Testplan](#testplan)

## Weiterleitung oder transparent

| | Gateway mit Weiterleitung | Transparentes Gateway |
|---|---|---|
| Basisklasse | `eZRedirectGateway` | `eZPaymentGateway` |
| Kartendaten | Beim Anbieter eingegeben | Auf Ihrer Website eingegeben oder vom Skript des Anbieters in ein Token umgewandelt |
| Besuche | Zwei: hinaus zum Anbieter, zurück über den Callback | Eine Anfrage |
| Zustand zwischen den Besuchen | `eZPaymentObject` | keiner nötig |
| Beispiel | `ezpaypal` | `ezauthorize` (über `eZCurlGateway`) |
| Pflichten zur Kartensicherheit | Geringer | Höher ([Grundlagen zu Kartenzahlungen und PCI](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md)) |

Bevorzugen Sie eine Weiterleitung oder eine vom Anbieter gehostete Zahlungsseite, wenn der Anbieter eine anbietet.

## Die Dateien der Erweiterung

Nach dem Vorbild von `extension/ezpaypal/`:

```text
extension/mypay/
  classes/mypaygateway.php        the gateway; loaded automatically for an active extension
  classes/mypaychecker.php        the callback checker (extends eZPaymentCallbackChecker)
  modules/mypay/module.php        a module with one view for the provider's callback
  modules/mypay/notify.php
  settings/module.ini.append      ModuleList[]=mypay, ExtensionRepositories[]=mypay
  settings/site.ini.append        AllowedRedirectHosts[], PolicyOmitList[]=mypay/notify
  settings/mypay.ini              provider settings without secrets
```

Der Dateiname des Gateways muss `classes/<extension>gateway.php` sein, damit es automatisch geladen wird
([paymentgateways.ini durchgegangen](../settings/paymentgateways-ini-durchgegangen.md#wie-gateways-geladen-werden)).

## Die Gateway-Klasse

Für ein Gateway mit Weiterleitung erweitern Sie `eZRedirectGateway` (`kernel/shop/classes/ezredirectgateway.php`)
und implementieren:

| Methode | Tut |
|---|---|
| `createPaymentObject( $processID, $orderID )` | Gibt ein `eZPaymentObject::createNew( $processID, $orderID, '<type>' )` zurück; es übersteht den Weg zum Anbieter |
| `createRedirectionUrl( $process )` | Baut die Adresse des Anbieters mit Betrag, Währung, Bestellreferenz und Ihren Rücksprung- und Callback-Adressen |

Den Rest erledigt die Basisklasse: Beim ersten Durchlauf speichert sie das Zahlungsobjekt und leitet weiter; beim
zweiten akzeptiert sie, wenn das Zahlungsobjekt genehmigt ist, und lehnt sonst ab; `cleanup()` entfernt
abgebrochene Zahlungsobjekte. Am Ende der Datei:

```php
eZPaymentGatewayType::registerGateway( 'mypay', 'mypaygateway', 'My provider' );
```

Für ein transparentes Gateway erweitern Sie `eZPaymentGateway` und implementieren `execute( $process, $event )`, das
`eZWorkflowType::STATUS_ACCEPTED` oder `STATUS_REJECTED` zurückgibt oder ein Template, um ein Formular anzuzeigen
(`STATUS_FETCH_TEMPLATE_REPEAT`). `createShortDescription( $order, $maxDescLen )` der Basisklasse liefert eine auf
die Grenze des Anbieters gekürzte Bestellbeschreibung.

Der RAD-Handler-Assistent hat Rezepte für beide Arten und schreibt ein Gerüst mit Kommentaren zu jeder Methode
([Pfad für Entwickler](../rollen/pfad-fuer-entwickler.md#generatoren-die-zeit-sparen)); vergleichen Sie seine Ausgabe mit den
oben genannten Ladepfaden, bevor Sie sich darauf verlassen.

## Der Callback

Die Callback-View empfängt die Nachricht des Anbieters und sollte mit einem Prüfer, der
`eZPaymentCallbackChecker` (`kernel/shop/classes/ezpaymentcallbackchecker.php`) erweitert:

1. Die Nachricht lesen (`createDataFromPOST()` oder `createDataFromGET()`).
2. Sie beim Anbieter validieren (`requestValidation()`) und, wo der Anbieter seine Adressen dokumentiert,
   `checkServerIP()`.
3. Bestellung und Zahlungsobjekt laden (`setupOrderAndPaymentObject()`).
4. Betrag und Währung mit der Bestellung abgleichen (`checkAmount()`, `checkCurrency()`).
5. Genehmigen und den Workflow fortsetzen (`approvePayment()`), was den Rest der Kasse ausführt: Aktivierung und die
   Bestellmail.
6. Optional einen Bestellstatus setzen, wie es `ezpaypal` mit `PaymentMadeOrderStatusID` tut.

Antworten Sie dem Anbieter schnell; der Browser des Kunden wartet währenddessen auf `shop/checkout`, das bis zu 25
Sekunden lang abfragt ([Die Kassenoperationen Schritt für Schritt](../kasse/die-kassenoperationen-schritt-fuer-schritt.md#schritt-6-zurück-vom-anbieter)).

## Einstellungen, die die Erweiterung ergänzt

| Einstellung | Warum |
|---|---|
| `site.ini [SiteSettings] AllowedRedirectHosts[]` | Der Host des Anbieters, damit die Weiterleitung erlaubt ist |
| `site.ini [RoleSettings] PolicyOmitList[]` | Die Callback-View, damit der Anbieter sie ohne Anmeldung aufrufen kann |
| `module.ini [ModuleSettings] ModuleList[]`, `ExtensionRepositories[]` | Das Callback-Modul |
| Ihre Anbieterdatei | Schalter für den Testmodus, Endpunkt, Händler-ID; Zugangsdaten nur in `settings/override/` |

## Sicherheitsregeln

- Vertrauen Sie nie der Rückkehr des Kunden auf Ihre Website als Zahlungsnachweis; nur der validierte Callback
  genehmigt.
- Prüfen Sie Betrag und Währung bei jedem Callback; lehnen Sie eine Abweichung ab.
- Schreiben Sie Kartennummern oder Sicherheitscodes nie in Logs, die Datenbank oder das Audit-Protokoll. Das Audit des
  Kernels erfasst Zahlungen ohne Kartendaten, und Ihr Code muss es ebenso halten.
- Behalten Sie den Schalter für den Testmodus und machen Sie in der Administration deutlich, welcher Modus aktiv ist.
- Siehe [Sicherheit für Anwendungen](../../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md) und
  [Formulare, CSRF und HTTP-Eingaben](../../ersteller/anwendungsentwickler/formulare-csrf-und-http-eingaben.md).

## Testplan

- [ ] Das Gateway erscheint in der Liste des Ereignisses Zahlungs-Gateway.
- [ ] Eine erfolgreiche Zahlung im Testmodus: Bestellung aufgegeben, Nummer vergeben, Mail versendet, Status gesetzt.
- [ ] Eine abgebrochene Zahlung: keine aufgegebene Bestellung, Warenkorb intakt, der Kunde kann es erneut versuchen.
- [ ] Ein Callback mit falschem Betrag: abgelehnt, ohne Kartendaten protokolliert.
- [ ] Ein wiederholter Callback: keine zweite Aktivierung.
- [ ] Ein später Callback (nach den 25 Sekunden): Die Bestellung wird trotzdem aktiviert, der Kunde findet sie in der
      Bestellliste.
- [ ] Die Anleitung des Kernels in `doc/eng-us/shop/examples/paypal/` mit Ihrem Code verglichen.

## Verwandte Seiten

- [Den Shop erweitern](README.md)
- [Das Ereignis Zahlungs-Gateway](../kasse/das-ereignis-zahlungs-gateway.md)
- [Einen Workflow-Ereignistyp schreiben](../../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
