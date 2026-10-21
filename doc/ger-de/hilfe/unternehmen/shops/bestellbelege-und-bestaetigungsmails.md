---
title: Bestellbelege und Bestätigungsmails
audience: businesses, admin, website-creator
category: business-solutions
order: 110
summary: Was ein Kunde nach dem Kauf in einem Exponential-Shop erhält - die Bestellbestätigung per Mail an Kunde und Shop, den dauerhaften Beleg-Link, der nie abläuft, und was Sie vor dem Start prüfen.
keywords: Bestellbestätigung, Bestellmail, Bestell-E-Mail, Beleg, dauerhafter Beleg, orderreceipt, AdminEmail, EmailSender, SendOrderEmail, Beleg drucken, Mailzustellung, order confirmation, order mail, order email, receipt, permanent receipt, print receipt, mail delivery
type: help
---
# Bestellbelege und Bestätigungsmails

Diese Seite richtet sich an Geschäftsinhaber, die möchten, dass Kunden für jede Bestellung eine klare Bestätigung und
einen Beleg erhalten, den sie wiederfinden. Sie erklärt die Bestellmail, den dauerhaften Beleg-Link, wer einen Beleg
öffnen kann und die beteiligten Einstellungen. Die Referenzseiten sind [Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md)
und [Warenkorb, Kasse und Bestellungen](../../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md#bestellungen).

## Inhalt

1. [Was der Kunde erhält](#was-der-kunde-erhält)
2. [Die Bestätigungsmail](#die-bestätigungsmail)
3. [Der dauerhafte Beleg](#der-dauerhafte-beleg)
4. [Wer einen Beleg öffnen kann](#wer-einen-beleg-öffnen-kann)
5. [Mails, die ankommen](#mails-die-ankommen)
6. [Belege sind keine Rechnungen](#belege-sind-keine-rechnungen)
7. [Checkliste](#checkliste)

## Was der Kunde erhält

1. Nach der Zahlung eine Seite mit der aufgegebenen Bestellung.
2. Eine Bestätigungsmail.
3. Wenn Sie es einschalten, in beiden einen Link zum **dauerhaften Beleg**, der auf jedem Gerät funktioniert,
   jahrelang, mit oder ohne Konto.

## Die Bestätigungsmail

Der Shop sendet Bestellmails, wenn `site.ini [ShopSettings] SendOrderEmail` auf `enabled` steht, dem ausgelieferten
Wert. Der Standard-Handler für die Bestellbestätigung (`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`)
tut dann Folgendes:

- er rendert das Template `shop/orderemail.tpl`;
- er sendet es an die E-Mail-Adresse des Kunden, wie der Shop-Account-Handler sie kennt;
- er sendet eine zweite Kopie an `site.ini [MailSettings] AdminEmail`, die eigene Adresse des Shops;
- er verwendet `[MailSettings] EmailSender` als Absender oder `AdminEmail`, wenn kein Absender gesetzt ist;
- er kennzeichnet die Mail mit der Kategorie `orders`.

Die ausgelieferte `AdminEmail` ist ein Platzhalter (`nospam@example.com` in `settings/site.ini`); setzen Sie vor dem
Start Ihre echte Bestelladresse in einem Override, sonst gehen die Kopien des Shops ins Leere.

Bestellmails sind notwendige E-Mails: Die Funktion der Mail-Einstellungen sendet sie immer, und sie enthalten nie
Werbung ([Mail-Einstellungen](../../../funktionen/6.0/e-mail-einstellungen.md)). Halten Sie Werbung aus dem Template der
Bestellmail heraus und senden Sie Angebote stattdessen über einen Newsletter mit Einwilligung ([Newsletter](../newsletter/README.md)).

## Der dauerhafte Beleg

Mit einer einzigen Einstellung, `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt`, verlinken die Seite nach dem
Bezahlen, die Bestelllisten des Kunden und die Bestätigungsmail auf `shop/orderreceipt/<token>` statt auf
`shop/orderview/<id>` ([Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md#einschalten)).

Warum das für ein Unternehmen wichtig ist:

- Der klassische Link `shop/orderview/<id>` funktioniert für einen Gast nicht mehr, sobald die Browsersitzung abläuft;
  der Beleg-Link schon.
- Die Belegseite bietet **Drucken** (nur der Beleg, ohne die umgebende Website) und **Herunterladen** (eine
  eigenständige HTML-Datei), sodass Kunden ihn für ihre eigenen Unterlagen behalten können.
- Die Adresse ist signiert: Sie trägt die Bestellung, ihren Zeitpunkt und ihren Kunden, und in der Datenbank wird
  nichts zusätzlich gespeichert.
- Eine falsch geschriebene Adresse `orderreciept` leitet auf die richtige weiter.

Die Administration verlinkt Bestellungen weiterhin auf `shop/orderview`, wo die Mitarbeiter sie verwalten.

## Wer einen Beleg öffnen kann

| Besucher | Kann ihn öffnen? |
|---|---|
| Jeder, der den Link hat | ja, der Link ist also so privat wie der Beleg |
| Der angemeldete Kunde, der die Bestellung aufgegeben hat | ja, ohne den Link |
| Mitarbeiter mit `shop/administrate` | ja, jeden Beleg |
| Jeder mit einem falschen Token | nein, er bekommt das Anmeldeformular |

Quelle: [Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md#wer-einen-beleg-öffnen-kann). Die Fußzeile des
Belegs sagt dem Kunden, dass er den Link privat halten soll.

> **Warnung**
> Die Beleg-Adressen sind mit einem Geheimnis signiert (`shop.ini [OrderReceiptSettings] Secret`). Wenn Sie das
> Geheimnis ändern, ändert sich jede Beleg-Adresse, und Links in alten Mails funktionieren nicht mehr. Lassen Sie es
> unverändert, sobald der Shop in Betrieb ist, und halten Sie es aus der ausgelieferten Einstellungsdatei heraus (die
> Funktionsseite erklärt, wo es aufbewahrt wird).

## Mails, die ankommen

Eine Bestätigung, die im Spam landet, wird zu einem Support-Anruf. Die Mailzustellung hängt von der Mail-Einrichtung
des Servers, der Absenderdomain und ihren Einträgen ab, um die sich Ihr Hoster kümmert ([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).

- Senden Sie von einer Adresse Ihrer eigenen Domain (`EmailSender`).
- Stellen Sie sicher, dass Antworten auf die Bestellmail jemanden erreichen.
- Testen Sie mit den Mail-Anbietern, die Ihre Kunden am häufigsten nutzen.

## Belege sind keine Rechnungen

Der Beleg zeigt die Bestellung so, wie sie aufgegeben wurde: Artikel, Mengen, USt und Summen. Ob er als steuerliche
Rechnung genügt, hängt vom Recht ab, das für Sie gilt; fragen Sie Ihren Steuerberater. Rechnungen in einem
Buchhaltungssystem gehören zu den Dingen, die der Shop einer Integration überlässt
([Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md#was-der-shop-nicht-leistet)).

## Checkliste

- [ ] `SendOrderEmail` steht auf `enabled`.
- [ ] `AdminEmail` und `EmailSender` sind Ihre echten Adressen (in einem Override, nicht in der ausgelieferten Datei).
- [ ] Das Template der Bestellmail enthält Ihren Namen, Ihre Kontaktdaten und Ihre Geschäftsbedingungen.
- [ ] `OrderLinkView=orderreceipt` ist gesetzt, wenn Gäste kaufen.
- [ ] Die Mail einer Testbestellung kommt an, im Posteingang und nicht im Spam-Ordner.
- [ ] Der Beleg-Link öffnet sich auf einem Smartphone ohne Anmeldung und lässt sich sauber drucken.
- [ ] Das Beleg-Geheimnis wird einmal gesetzt und mit Ihren anderen Geheimnissen festgehalten.

## Verwandte Seiten

- [Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md)
- [Kundenkonten und Gastbestellung](kundenkonten-und-gastbestellung.md)
- [Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
