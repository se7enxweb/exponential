---
title: Headless Commerce mit expservices
audience: ecommerce, app-creator, developer
category: e-commerce
order: 78
summary: Den Exponential-Shop mit der Erweiterung expservices über HTTP steuern - die Dienstdomänen für den Handel (Produkt, Warenkorb, Bestellung, Zahlung, Versand, USt, Währung, Rabatt, Wunschliste), Authentifizierung mit Sitzungen oder API-Tokens, Richtlinien je Dienst, typische Verwendungen und die Vorbehalte einer Erweiterung im ersten Entwurf.
keywords: Headless Commerce, entfernte Dienste, API-Token, mobile App, Integration, Warenkorb, Bestellungen, headless commerce, expservices, remote services, exporder, expbasket, expproduct, exppayment, expshipping, expvat, expcurrency, expdiscount, expwishlist, API token, ezjscore call, JSON, mobile app, integration
type: help
---
# Headless Commerce mit expservices

Diese Seite richtet sich an Entwickler, die möchten, dass eine App, ein Skript oder ein anderes System den Shop nutzt:
Produkte und Preise lesen, einen Warenkorb füllen, Bestellungen nachschlagen oder Kennzahlen abrufen. Die Erweiterung
`expservices`, die im Ordner `extension/` des Repositorys mitgeliefert wird, stellt die Funktionen der Administration
als entfernte Dienste über ezjscore bereit, und der Shop ist eine ihrer Gruppen. Die allgemeine Einführung ist
[Entfernte Dienste mit expservices](../../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md).

## Inhalt

1. [Die Domänen für den Handel](#die-domänen-für-den-handel)
2. [Einen Dienst aufrufen](#einen-dienst-aufrufen)
3. [Wer was aufrufen darf](#wer-was-aufrufen-darf)
4. [Typische Verwendungen](#typische-verwendungen)
5. [Vorbehalte](#vorbehalte)

## Die Domänen für den Handel

Registriert in `extension/expservices/settings/ezjscore.ini.append.php`, Klassen in
`extension/expservices/classes/commerce/`:

| Domäne | Einige ihrer Dienste |
|---|---|
| `expproduct` | `classes`, `list`, `search`, `view`, `price`, `prices`, `options`, `categories`, `priceRange`, `related`, `vat`, `setPrice` |
| `expbasket` | `view`, `items`, `count`, `totals`, `add`, `update`, `remove`, `empty`, `refreshPrices`, `setCurrency`, `startCheckout`, `review`, `cancelCheckout`, `placeOrder` |
| `exporder` | `mine`, `myOrder`, `myReceipt`, `statuses`, `list`, `view`, `byNumber`, `items`, `history`, `setStatus`, `archive`, `unarchive`, `remove`, `search`, `statistics`, `customers`, `dashboard` |
| `exppayment` | `list`, `view`, `byOrder`, `myStatus`, `gateways`, `workflowEvents`, `approve` |
| `expshipping` | `status`, `handler`, `simpleShipping`, `basket`, `order`, `vatOfShipping`, `settings` |
| `expvat` | `types`, `rules`, `categories`, `forProduct`, `userCountry`, `setUserCountry`, `countries` und die Dienste zum Anlegen, Ändern und Entfernen |
| `expcurrency` | `list`, `rate`, `convert`, `preferred`, `setPreferred`, `updateRates`, `updateAutoprices` und mehr |
| `expdiscount` | `groups`, `rules`, `members`, `forUser`, `mine` und die Dienste zum Bearbeiten |
| `expwishlist` | `view`, `items`, `add`, `remove`, `moveToBasket`, `popular` |

Die vollständige, aktuelle Liste kommt aus dem Dienstkatalog selbst (`expservices::catalog`), der jeden Dienst mit
seinen Parametern beschreibt ([Entfernte Dienste (Funktion)](../../../funktionen/6.0/entfernte-dienste-expservices.md)).

## Einen Dienst aufrufen

```bash
curl -s -H 'Authorization: Bearer <token>' \
  'https://example.com/ezjscore/call/exporder::byNumber::1042?ContentType=json'
```

Jede Antwort ist eine Hülle: `{"ok":true,"data":...,"meta":{...}}` oder `{"ok":false,"error":{...}}`; Listen sind
seitenweise. Der Hauptschalter ist `expservices.ini [Services] Enabled`.

## Wer was aufrufen darf

Jeder Dienst deklariert die Richtlinie, die er braucht, und prüft sie als aufrufender Benutzer. Die Bestelldienste für
Mitarbeiter, etwa `exporder::list`, `statistics` und `customers`, brauchen `shop/administrate`; die Dienste namens
`mine`, `myOrder` und `myReceipt` antworten für die eigenen Bestellungen des angemeldeten Kunden. Schreibvorgänge
brauchen POST. Geben Sie jedem Skript und jeder App einen eigenen Benutzer und ein persönliches API-Token mit nur den
Richtlinien, die es braucht; Tokens werden einmal angezeigt, nur als Hash gespeichert, und ihr Anlegen und Widerrufen
wird im Audit festgehalten.

## Typische Verwendungen

| Verwendung | Dienste |
|---|---|
| Eine mobile App, die Produkte und Preise zeigt | `expproduct::list`, `view`, `prices` |
| Die Bestellhistorie eines Kunden in einer App | `exporder::mine`, `myOrder`, `myReceipt` |
| Ein nächtlicher Export von Kennzahlen | `exporder::list`, `statistics` ([Shop-Kennzahlen und Exporte](shop-kennzahlen-und-exporte.md)) |
| Eine Packstation, die Bestellungen als versendet markiert | `exporder::byNumber`, `setStatus` (mit einer durch `FromStatus` und `ToStatus` eingeschränkten Rolle) |
| Preisaktualisierungen aus einem Lieferantensystem | `expproduct::setPrice` |

Für Inhalte in Apps im Allgemeinen und OAuth für Dritte ist die REST-API der andere Weg
([Exponential headless](../../ersteller/anwendungsentwickler/headless-exponential.md)).

## Vorbehalte

- Die Erweiterung wurde am 2. Oktober 2026 als Version 0.1.0 hinzugefügt, und ihre Funktionsseite nennt sie einen ersten
  Entwurf. Halten Sie die Dienste, die Sie verwenden, in Ihren Tests fest und prüfen Sie sie nach Updates.
- Das Aufgeben von Bestellungen über Dienste führt weiterhin die Workflows der Kasse aus; eine Zahlung per
  Weiterleitung braucht weiterhin den Kunden beim Anbieter. Testen Sie den vollständigen Weg, bevor Sie eine Kasse in
  einer App darauf aufbauen.
- Senden Sie niemals Kartendaten über diese Dienste.

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [OAuth und API-Schlüssel](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)
- [Mobil und headless (Unternehmen)](../../unternehmen/mobil-und-headless/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
