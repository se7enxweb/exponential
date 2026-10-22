---
title: shopaccount.ini durchgegangen
audience: ecommerce, website-creator, admin
category: e-commerce
order: 33
summary: Die Einstellungen, die entscheiden, welches Formular für Kundendaten die Kasse zeigt und welcher Handler die Bestellmail versendet - HandlerSettings, AccountSettings mit den Handlern ezuser, ezsimple, ezdefault und bcusauser, Alias und ConfirmOrderSettings.
keywords: shopaccount.ini, Shop-Konto-Handler, AccountSettings, Handler, ezuser, ezsimple, ezdefault, bcusauser, userregister, register, Kasse als Gast, ConfirmOrderSettings, Alias, HandlerSettings, ExtensionRepositories, Kundendaten, shop account handler, guest checkout, customer data
type: help
---
# shopaccount.ini durchgegangen

Diese Seite ist für Entwickler und Administratoren, die entscheiden, wie ein Exponential-Shop an der Kasse
Kundendaten erfasst. `settings/shopaccount.ini` wählt zwei Handler: den Shop-Konto-Handler (welches Formular der
Kunde ausfüllt und wie Name, E-Mail und Adresse aus einer Bestellung zurückgelesen werden) und den Handler für die
Bestellbestätigung (der die Bestellmail versendet).

## Inhalt

1. [HandlerSettings](#handlersettings)
2. [AccountSettings](#accountsettings)
3. [Die mitgelieferten Konto-Handler](#die-mitgelieferten-konto-handler)
4. [ConfirmOrderSettings](#confirmordersettings)
5. [Einen Handler wählen](#einen-handler-wählen)

## HandlerSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Repositories[]` | `kernel/classes` | Wo nach Handlern gesucht wird; Konto-Handler in `shopaccounthandlers/`, Handler für die Bestellbestätigung in `confirmorderhandlers/` |
| `ExtensionRepositories[]` | leer | Erweiterungen mit eigenem Verzeichnis `shopaccounthandlers/` oder `confirmorderhandlers/` |

## AccountSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Handler` | `ezuser` | Der Shop-Konto-Handler. Die Klasse ist `<name>ShopAccountHandler` in `<name>shopaccounthandler.php` |
| `Alias[]` | leer | Ersetzt einen Handlernamen durch einen anderen, zum Beispiel `Alias[ezdefault]=eztest` |

## Die mitgelieferten Konto-Handler

Alle vier liegen in `kernel/classes/shopaccounthandlers/`.

| Handler | Der Kunde | Formular | Wo die Daten gespeichert werden |
|---|---|---|---|
| `ezuser` (ausgelieferte Wahl) | Gibt bei jeder Kasse Vor- und Nachnamen, E-Mail, zwei Straßenzeilen, Postleitzahl, Ort, Bundesland, Land und einen Kommentar ein, mit oder ohne Konto | `shop/userregister` | In der Bestellung (`data_text_1`, als XML) |
| `ezsimple` | Gibt Vor- und Nachnamen, E-Mail und ein Adressfeld ein | `shop/register` | In der Bestellung |
| `ezdefault` | Muss angemeldet sein; wird zu `user/login` und zurück zum Warenkorb geschickt; es wird nichts zusätzlich abgefragt | keines | Name und E-Mail des Benutzerkontos |
| `bcusauser` | Wie `ezuser`, für das US-Adressmodell: Stadt statt Ort, dazu eine Telefonnummer | `shop/userregister` | In der Bestellung |

Die Templates, die die Daten anzeigen, liegen in `design/standard/templates/shop/accounthandlers/` (`html/` für
Bildschirme, `ascii/` für reinen Text). Die Kasse als Gast ist mit `ezuser` und `ezsimple` möglich, solange anonyme
Benutzer die Richtlinie `shop/buy` haben. Die Sicht für Unternehmen auf diese Wahl steht unter
[Kundenkonten und Kasse als Gast](../../unternehmen/shops/kundenkonten-und-gastbestellung.md).

> **Hinweis**
> Die Erweiterung `ezauthorize` liefert ihren eigenen Konto-Handler (`ezcustom`) und eine Beispieldatei
> `shopaccount.ini.append.php` mit, in der jede Zeile auskommentiert ist. Lesen Sie sie, bevor Sie sie einschalten:
> Ihre Einstellungen in `ezauthorize.ini` ordnen die Kontofelder zu, die sie erwartet.

## ConfirmOrderSettings

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Handler` | `ezdefault` | `eZDefaultConfirmOrderHandler`: sendet die Bestellmail an den Kunden und an `[MailSettings] AdminEmail`, wenn `site.ini [ShopSettings] SendOrderEmail=enabled` gesetzt ist |
| `Alias[]` | leer | Ersetzt einen Handler durch einen anderen |

## Einen Handler wählen

| Sie brauchen | Handler |
|---|---|
| Gäste und Kunden geben jedes Mal eine Adresse ein | `ezuser` |
| Ein kürzeres Formular | `ezsimple` |
| Nur registrierte Kunden kaufen; ihr Konto enthält alles | `ezdefault` |
| US-Adressen mit Telefonnummer | `bcusauser` |
| Zusätzliche Felder (Firma, USt-IdNr., Lieferadresse) | Einen eigenen Handler ([Shop-Konto-Handler](../erweitern/shop-konto-handler.md)) |

Für USt-Regeln nach Land muss der Handler dem Kunden ein Land geben; prüfen Sie, dass Ihr Formular danach fragt
([Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md#das-land-des-kunden)).

## Verwandte Seiten

- [Die Shop-Einstellungen durchgegangen](README.md)
- [Kundenkonten (Ihren Shop betreiben)](../../kunden/handel/kundenkonten.md)
- [Handler für Warenkorbinformationen und Bestellbestätigung](../erweitern/handler-fuer-warenkorbinformationen-und-bestellbestaetigung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
