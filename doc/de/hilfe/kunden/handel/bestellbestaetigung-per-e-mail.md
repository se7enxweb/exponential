---
title: Bestellbestätigung per E-Mail
audience: customer, admin, hosting
category: customer-success
order: 160
summary: Die Bestellbestätigung eines Exponential-Shops funktionsfähig und nützlich halten - wer sie erhält, die Einstellungen dahinter, eine monatliche Zustellprüfung, was hineingehört und was nicht, und was Sie Ihren Hoster fragen, wenn E-Mails fehlen.
keywords: Bestellbestätigung, Bestell-E-Mail, AdminEmail, EmailSender, SendOrderEmail, orderemail.tpl, E-Mail-Zustellung, Spam-Ordner, notwendige E-Mail, order confirmation, order mail, order email, mail delivery, spam folder, essential mail
type: help
---
# Bestellbestätigung per E-Mail

Diese Seite ist für den Eigentümer und die Shop-Leitung. Die Bestätigungs-E-Mail ist oft das Einzige, was ein Kunde
von einer Bestellung aufbewahrt, und eine E-Mail, die nicht ankommt, wird zu einem Supportanruf. Diese Seite gibt die
Prüfungen vor, mit denen sie weiterhin ankommt, und die Entscheidungen über ihren Inhalt. Was die E-Mail enthält und
welchen Beleg-Link sie tragen kann, ist unter
[Bestellbelege und Bestellbestätigung für Unternehmen](../../../../en/us/help/businesses/stores/order-receipts-and-confirmation-mail.md) erklärt.

## Wie sie versendet wird

Wenn `site.ini [ShopSettings] SendOrderEmail` auf `enabled` steht (der ausgelieferte Wert), rendert der
Standard-Handler für die Bestellbestätigung (`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`)
`shop/orderemail.tpl` und sendet:

- eine E-Mail an die E-Mail-Adresse des Kunden;
- eine Kopie an `site.ini [MailSettings] AdminEmail`, die eigene Adresse des Shops;
- mit `[MailSettings] EmailSender` als Absender, oder `AdminEmail`, wenn kein Absender gesetzt ist.

Die ausgelieferte `AdminEmail` ist ein Platzhalter (`nospam@example.com` in `settings/site.ini`), und `EmailSender`
ist leer. Ihr Partner setzt beide in einem Override.

> **Hinweis**
> Bestell-E-Mails sind notwendige E-Mails: Die Funktion für E-Mail-Einstellungen versendet sie immer, und sie enthalten
> nie Werbung ([E-Mail-Einstellungen](../../../../en/us/features/6.0/mail-preferences.md)). Kunden können sie nicht abbestellen,
> und sie sollten nicht für Angebote genutzt werden.

## Wer was tut

| Aufgabe | Wer |
|---|---|
| Liest jeden Tag die Kopien des Shops | Shop-Leitung |
| Entscheidet über Wortlaut, Kontaktdaten und Bedingungen der E-Mail | Eigentümer |
| Ändert das Template `shop/orderemail.tpl` im Design der Website | Partner oder Designer |
| E-Mail-Versand vom Server, DNS-Einträge der Absenderdomain, Zustellung | Hoster ([E-Mail-Zustellung](../../../../en/us/help/hosting/operations/mail-delivery.md)) |

## Eine monatliche Zustellprüfung

- [ ] Geben Sie eine Testbestellung auf (oder nutzen Sie eine echte kleine) mit Adressen bei den E-Mail-Anbietern, die Ihre Kunden am häufigsten nutzen.
- [ ] Die E-Mail landet im Posteingang, nicht im Spam-Ordner.
- [ ] Der Absender ist eine Adresse Ihrer eigenen Domain, und Antworten erreichen einen Menschen.
- [ ] Die Kopie des Shops ist an der Adresse `AdminEmail` angekommen.
- [ ] Links in der E-Mail lassen sich öffnen (der Beleg-Link, wenn dauerhafte Belege eingeschaltet sind: [Rechnungen und Belege](rechnungen-und-belege.md)).
- [ ] Die E-Mail liest sich gut auf dem Smartphone.

## Was in die E-Mail gehört

| Hinein | Draußen lassen |
|---|---|
| Ihr Firmenname und Ihre Kontaktdaten | Werbung und Newsletter-Angebote |
| Die Bestellnummer und der Beleg-Link | Alles, was der Kunde nicht bestellt hat |
| Wie es weitergeht: wann versendet wird, wie man Fragen stellt | Interne Kürzel oder Notizen der Mitarbeitenden |
| Ihre Rückgabebedingungen oder ein Link darauf | Kartendaten jeglicher Art |

Änderungen am Wortlaut sind Änderungen am Template; sammeln Sie sie daher und beauftragen Sie Ihren Partner auf einmal damit.

## Wenn E-Mails fehlen

| Anzeichen | Wahrscheinliche Ursache | Fragen Sie |
|---|---|---|
| Seit einem bestimmten Tag keine Kopien an der Adresse `AdminEmail` | E-Mail-Versand auf dem Server, oder die Adresse wurde geändert | Hoster |
| Kunden finden die E-Mail im Spam | DNS-Einträge der Absenderdomain, oder die Absenderadresse | Hoster ([E-Mail-Zustellung](../../../../en/us/help/hosting/operations/mail-delivery.md)) |
| Nur die Kunden eines E-Mail-Anbieters vermissen sie | Dieser Anbieter weist die E-Mail ab oder filtert sie | Hoster, mit einer Beispielbestellung |
| Überhaupt keine E-Mails, weder an Kunden noch an den Shop | `SendOrderEmail` ausgeschaltet, oder der E-Mail-Transport | Partner |

Bis das behoben ist, senden Sie betroffenen Kunden ihren Beleg-Link oder eine kurze Bestätigung von Hand aus dem
Postfach des Shops.

## Verwandte Seiten

- [Rechnungen und Belege](rechnungen-und-belege.md)
- [Bestellbelege und Bestellbestätigung für Unternehmen](../../../../en/us/help/businesses/stores/order-receipts-and-confirmation-mail.md)
- [E-Mail-Zustellung](../../../../en/us/help/hosting/operations/mail-delivery.md)
- [E-Mail-Einstellungen](../../../../en/us/features/6.0/mail-preferences.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
