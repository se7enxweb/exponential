---
title: Barrierefreiheit im Shop
audience: ecommerce, website-creator, designer
category: e-commerce
order: 72
summary: Einen Exponential-Shop für alle nutzbar machen - woher das Markup des Shops stammt, barrierefreie Produktseiten, Optionsfelder, Warenkorb, Formulare der Kasse, Fehlermeldungen, Auswahl des Gateways und Bestellmail, mit einer Checkliste und wie Sie es testen.
keywords: Barrierefreiheit, barrierefreier Shop, barrierefreie Kasse, Formulare, Labels, Fehlermeldungen, Tastatur, Screenreader, Kontrast, Produktseite, Warenkorb, Shop-Templates, Barrierefreiheit testen, accessibility, accessible store, checkout accessibility, forms, labels, error messages, keyboard, screen reader, contrast, product page, basket, shop templates, WCAG, testing accessibility
type: help
---
# Barrierefreiheit im Shop

Diese Seite richtet sich an Designer, Entwickler und Händler, die möchten, dass alle in ihrem Shop kaufen können,
einschließlich Menschen, die eine Tastatur, einen Screenreader oder eine Vergrößerung verwenden. Eine Kasse, die sich
nicht abschließen lässt, ist ein verlorener Verkauf und vielerorts ein rechtliches Problem. Diese Seite nennt die Teile
des Shop-Markups, die Sie steuern, und was Sie bei jedem prüfen sollten; die allgemeine Anleitung finden Sie unter
[Barrierefreiheit für Site-Ersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md).

## Inhalt

1. [Woher das Markup stammt](#woher-das-markup-stammt)
2. [Produktseiten](#produktseiten)
3. [Warenkorb](#warenkorb)
4. [Formulare der Kasse](#formulare-der-kasse)
5. [Fehler- und Statusmeldungen](#fehler--und-statusmeldungen)
6. [Zahlung und Belege](#zahlung-und-belege)
7. [Checkliste](#checkliste)

## Woher das Markup stammt

Alle Shop-Seiten sind Templates, die Sie in Ihrem Design überschreiben können
([Template-Overrides](../../ersteller/website-ersteller/template-overrides.md)):

| Seite | Template (Design standard) |
|---|---|
| Vollständige Produktansicht mit „Zum Warenkorb hinzufügen“ | das Produkt-Template Ihres Designs |
| Warenkorb | `shop/basket.tpl` (oder `shop/<BasketViewName>.tpl`) |
| Kundendaten | `shop/userregister.tpl`, `shop/register.tpl` |
| Bestätigung | `shop/confirmorder.tpl` |
| Auswahl des Gateways | `workflow/selectgateway.tpl` |
| Probleme an der Kasse | `shop/checkoutagain.tpl`, `shop/cancelcheckout.tpl` |
| Bestellung und Beleg | `shop/orderview.tpl`, `shop/orderreceipt.tpl` |
| Bestellmail | `shop/orderemail.tpl` |
| Länderauswahl | `shop/country/edit.tpl` |

Prüfen Sie die Templates, die Ihr Theme tatsächlich verwendet, nicht nur die Standard-Templates; die mitgelieferten
Themes bringen ihre eigenen Shop-Templates mit ([Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md)).

## Produktseiten

- Der Preis ist Text, mit der Währung, nicht nur eine Grafik.
- Das Steuerelement **Zum Warenkorb hinzufügen** ist eine echte Schaltfläche mit einer Textbeschriftung.
- Optionsfelder (Größe, Farbe) haben jeweils ein sichtbares, mit dem Steuerelement verbundenes Label; Aufpreise stehen
  im Text des Labels.
- Produktbilder haben Textalternativen, die das Produkt beschreiben; dekorative Bilder haben leere Alternativen.

## Warenkorb

- Der Warenkorb ist eine Tabelle mit Kopfzellen für Produkt, Menge, Preis und Summe.
- Mengenfelder haben Labels, die das Produkt nennen („Menge von Blaues Hemd“).
- Steuerelemente zum Entfernen sagen, was sie entfernen.
- Eine Änderung der Warenkorbanzahl im Seitenkopf wird angesagt, oder der Kunde wird zum Warenkorb geführt
  (`RedirectAfterAddToBasket=basket`), damit die Änderung nicht still bleibt.

## Formulare der Kasse

- Jedes Feld hat ein sichtbares Label; Pflichtfelder sind im Text markiert, nicht nur durch Farbe.
- Felder haben die richtigen Eingabetypen und Autocomplete-Hinweise für Name, E-Mail und Adresse.
- Die Länderauswahl ist mit der Tastatur bedienbar; die mitgelieferte Auswahl listet die meistgenutzten Länder zuerst
  ([Ländergruppen und Regionen](../../unternehmen/shops/laendergruppen-und-regionen.md)).
- Das Formular eines eigenen Shop-Konto-Handlers folgt denselben Regeln ([Shop-Konto-Handler](../erweitern/shop-konto-handler.md)).

## Fehler- und Statusmeldungen

- Fehler wie eine ungültige Menge oder entfernte Artikel (die Liste `removed_items` des Warenkorbs) werden als Text
  oben angezeigt, wobei der Fokus dorthin verschoben oder die Meldung angesagt wird.
- `shop/checkoutagain.tpl` und `shop/cancelcheckout.tpl` erklären in klaren Worten, was passiert ist und was als Nächstes
  zu tun ist.

## Zahlung und Belege

- Auf der Seite zur Auswahl des Gateways ist jedes Gateway eine beschriftete Auswahlmöglichkeit.
- Eine Weiterleitung zum Anbieter wird auf der Schaltfläche angekündigt („Weiter zu PayPal“).
- Der Beleg hat eine Ansicht **Drucken** nur für den Beleg und einen **Download** als eigenständige HTML-Datei
  ([Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md)); prüfen Sie beides mit einem Screenreader.
- Eine HTML-Bestellmail hat eine sinnvolle Lesereihenfolge und Alternativen für Bilder.

## Checkliste

- [ ] Der vollständige Weg, vom Produkt bis zum Beleg, nur mit der Tastatur.
- [ ] Derselbe Weg mit einem Screenreader.
- [ ] Zoom auf 200 Prozent: Nichts überlappt oder braucht horizontales Scrollen.
- [ ] Kontrast von Preisen, Schaltflächen und Fehlermeldungen.
- [ ] Fehlermeldungen für jedes Formularfeld.
- [ ] Getestet mit den Methoden aus [Barrierefreiheit testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md).

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Barrierefreiheit für Site-Ersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- [Einen Shop testen](einen-shop-testen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
