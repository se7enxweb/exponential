---
title: Bestellmails und Belege für Betreiber
audience: ecommerce, customer, admin
category: e-commerce
order: 54
summary: Die Bestellmail und die Beleglinks eines Exponential-Shops aus Sicht der Betreiber - wann die Mail versendet wird, an wen und von welcher Adresse, das Template, die Mail-Kategorie, dauerhafte Beleglinks im Kundenservice und was Sie prüfen, wenn eine Mail nicht ankommt.
keywords: Bestellmail, Bestellbestätigung, SendOrderEmail, AdminEmail, EmailSender, orderemail.tpl, Handler für die Bestellbestätigung, Beleglink, orderreceipt, OrderLinkView, Kundenservice, Mail nicht erhalten, order mail, order confirmation, confirm order handler, receipt link, customer service, mail not received
type: help
---
# Bestellmails und Belege für Betreiber

Diese Seite ist für Shop-Manager und Administratoren, die auf „Ich habe meine Bestätigung nicht erhalten“ und „Können Sie
mir meinen Beleg noch einmal schicken“ antworten. Sie erklärt, wann und wie die Bestellmail versendet wird und wie
dauerhafte Beleglinks den Kundenservice erleichtern. Die Sicht des Kunden steht unter
[Bestellbelege und Bestätigungsmail](../../unternehmen/shops/bestellbelege-und-bestaetigungsmails.md) und der Wortlaut der
Mail unter [Bestellbestätigungsmail](../../kunden/handel/bestellbestaetigung-per-e-mail.md).

## Inhalt

1. [Wann die Mail versendet wird](#wann-die-mail-versendet-wird)
2. [An wen und von wo](#an-wen-und-von-wo)
3. [Was darin steht](#was-darin-steht)
4. [Beleglinks im Kundenservice](#beleglinks-im-kundenservice)
5. [Wenn eine Mail nicht ankommt](#wenn-eine-mail-nicht-ankommt)

## Wann die Mail versendet wird

Die Methode `send-order-email` der Kassenoperation läuft direkt nach der Aktivierung der Bestellung
([Die Kassenoperationen Schritt für Schritt](../kasse/die-kassenoperationen-schritt-fuer-schritt.md#schritt-5-zur-kasse-gehen-und-bezahlen)).
Sie bittet den Handler für die Bestellbestätigung (`shopaccount.ini [ConfirmOrderSettings] Handler`, ausgeliefert
`ezdefault`), sie zu versenden, und dieser Handler versendet nur, wenn `site.ini [ShopSettings] SendOrderEmail=enabled`
(ausgeliefert). Die Mail geht also einmal hinaus, wenn die Bestellung aufgegeben wird, und nicht bei späteren
Statusänderungen. Mails für spätere Status brauchen ein eigenes Ereignis oder einen eigenen Handler.

## An wen und von wo

| Kopie | Empfänger | Absender |
|---|---|---|
| Kunde | Die E-Mail-Adresse der Bestellung (vom Konto-Handler) | `[MailSettings] EmailSender`, sonst `AdminEmail` |
| Shop | `site.ini [MailSettings] AdminEmail` | ebenso |

Beide Kopien haben denselben Betreff und Text und die Mail-Kategorie `orders`
(`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`). Wenn die Adresse des Website-Administrators
nicht das richtige Postfach für Bestellungen ist, ändern Sie den Handler statt `AdminEmail`, das auch für andere
Systemmails verwendet wird ([Handler für Warenkorbinformationen und Bestellbestätigung](../erweitern/handler-fuer-warenkorbinformationen-und-bestellbestaetigung.md)).

## Was darin steht

Text und Betreff kommen aus dem Template `shop/orderemail.tpl`; der Betreff ist eine Template-Variable, die darin gesetzt
wird. Das Template kann `content_type` für eine HTML-Mail setzen. Mit `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt`
verweist die Mail auf den dauerhaften Beleg ([shop.ini durchgegangen](../settings/shop-ini-durchgegangen.md#orderviewsettings)).

## Beleglinks im Kundenservice

Mit eingeschalteten dauerhaften Belegen:

- kann der Kunde den Beleg von jedem Gerät aus ohne Anmeldung öffnen, drucken und herunterladen;
- können Mitarbeitende mit `shop/administrate` jeden Beleg öffnen und genau sehen, was der Kunde sieht;
- ist eine verlorene Mail kein Problem: Der Beleglink des Kunden funktioniert weiter, und angemeldete Kunden finden ihn
  in ihrer Bestellliste.

Behandeln Sie einen Beleglink wie den Beleg selbst: Senden Sie ihn nur an die eigene Adresse des Kunden
([Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md)).

## Wenn eine Mail nicht ankommt

| Prüfung | Wie |
|---|---|
| Wurde die Bestellung überhaupt aufgegeben? | Sie hat eine Bestellnummer in der Bestellliste; andernfalls siehe [Fehlersuche an der Kasse](../betrieb/fehlersuche-an-der-kasse.md) |
| Ist `SendOrderEmail` eingeschaltet? | **Setup > INI-Einstellungen**, `site.ini`, `ShopSettings` |
| Stimmt die Adresse in der Bestellung? | Bestellansicht, Kundendaten |
| Ist die Shop-Kopie bei `AdminEmail` angekommen? | Wenn ja, liegt das Problem bei der Adresse oder dem Spamfilter des Kunden |
| Versendet die Website überhaupt Mails? | [Benachrichtigungen und Mail](../../ersteller/anwendungsentwickler/benachrichtigungen-und-mail.md), die Einstellungen des Mail-Transports |
| Darf die Absenderdomain senden? | Fragen Sie Ihren Hoster nach der Absenderadresse und den Mail-Einträgen der Domain |

## Verwandte Seiten

- [Bestellbetrieb](README.md)
- [Rechnungen und Belege](../../kunden/handel/rechnungen-und-belege.md)
- [Die Shop-Einstellungen in site.ini durchgegangen](../settings/shop-einstellungen-in-site-ini-durchgegangen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
