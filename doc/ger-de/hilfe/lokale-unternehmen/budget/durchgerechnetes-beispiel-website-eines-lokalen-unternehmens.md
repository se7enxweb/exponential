---
title: "Durchgerechnetes Beispiel: die Website eines lokalen Unternehmens"
audience: local-business, businesses, customer
category: local-business
order: 72
summary: Eine Kostenrechnung über drei Jahre für die Website eines erfundenen lokalen Unternehmens mit Exponential - Einrichtung, Hosting, Updates, externe Dienste und die Zeit des Inhabers -, in der jede Zahl eine Beispielannahme in neutralen Währungseinheiten ist, gefolgt von einer einfachen Prüfung der Gewinnschwelle und dem, was Sie für Ihr Unternehmen ändern.
keywords: durchgerechnetes Beispiel, Kostenrechnung, Beispielannahmen, Gewinnschwelle, kleines Unternehmen, drei Jahre, Hosting, Einrichtung, Zeit des Inhabers, lokales Unternehmen, worked example, cost calculation, example assumptions, break-even, local business
type: help
---
# Durchgerechnetes Beispiel: die Website eines lokalen Unternehmens

Diese Seite ist für Inhaber eines kleinen Unternehmens, die sehen wollen, wie ein Website-Budget berechnet wird. Sie
begleitet ein erfundenes Unternehmen über drei Jahre.

> **Hinweis**
> Das Unternehmen und jede Zahl unten sind **Beispielannahmen**: runde Zahlen in neutralen Währungseinheiten, so
> gewählt, dass die Rechnung leicht fällt. Es sind keine Marktpreise, keine Preise eines Anbieters und keine Ergebnisse
> eines echten Unternehmens. Ersetzen Sie jede durch Ihre eigenen Angebote und Zahlen, im
> [Budget-Arbeitsblatt](budget-arbeitsblatt.md).

## Inhalt

1. [Das erfundene Unternehmen](#das-erfundene-unternehmen)
2. [Die Eingaben](#die-eingaben)
3. [Schritt 1: das erste Jahr](#schritt-1-das-erste-jahr)
4. [Schritt 2: die folgenden Jahre](#schritt-2-die-folgenden-jahre)
5. [Schritt 3: drei Jahre insgesamt](#schritt-3-drei-jahre-insgesamt)
6. [Schritt 4: was die Website einbringen muss](#schritt-4-was-die-website-einbringen-muss)
7. [Was Sie für Ihr Unternehmen ändern](#was-sie-für-ihr-unternehmen-ändern)

## Das erfundene Unternehmen

Eine Reparaturwerkstatt mit einem Standort. Die Website hat eine Startseite, Leistungen, Öffnungszeiten mit Karte, ein
Kontaktformular mit CAPTCHA, ein Formular für Angebotsanfragen und einen kleinen Ordner „Neuigkeiten“. Kein Shop. Eine
Sprache. Der Inhaber bearbeitet Texte und Öffnungszeiten; ein Partner aus der Region baut die Website und hält sie
aktuell.

Was sie verwendet, alles in der Standard-`composer.json`: `xrowmetadata`, `bcgooglesitemaps`, `ezgmaplocation` (mit
einem Google-Schlüssel) oder den Karten-Block der Layouts, `recaptcha` oder `hcaptcha`. Lizenzgebühr: keine (Tatsache:
GNU GPL, [Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md)).

## Die Eingaben

| Eingabe | Wert |
|---|---|
| Beispielannahme: Einrichtung durch einen Partner (Theme angepasst, Klassen, Formulare, Karte, Sitemap) | 3.000 Einheiten, einmalig |
| Beispielannahme: Inhalte, Texte und Fotos vom Inhaber selbst | 15 Stunden, einmalig |
| Beispielannahme: Domain | 20 Einheiten pro Jahr |
| Beispielannahme: Hosting mit Sicherungen und HTTPS | 30 Einheiten pro Monat |
| Beispielannahme: Updates und kleiner Support durch den Partner | 8 Stunden pro Jahr zu 60 Einheiten pro Stunde |
| Beispielannahme: Karten- und CAPTCHA-Anbieter | 0 Einheiten (innerhalb eines kostenlosen Kontingents; prüfen Sie die aktuellen Bedingungen der Anbieter) |
| Beispielannahme: Zeit des Inhabers für die Pflege | 2 Stunden pro Monat |
| Beispielannahme: Wert einer Stunde des Inhabers | 40 Einheiten |
| Lizenzgebühr | 0 (Tatsache) |

## Schritt 1: das erste Jahr

1. Einrichtung: **3.000**.
2. Inhaltsarbeit des Inhabers: 15 x 40 = **600**.
3. Domain: **20**.
4. Hosting: 30 x 12 = **360**.
5. Updates und Support: 8 x 60 = **480**.
6. Externe Dienste: **0**.
7. Pflege durch den Inhaber: 2 x 12 x 40 = **960**.
8. Erstes Jahr: 3.000 + 600 + 20 + 360 + 480 + 0 + 960 = **5.420**.

Davon an andere gezahlt: 3.000 + 20 + 360 + 480 = **3.860**. Der Rest (1.560) ist die Zeit des Inhabers.

## Schritt 2: die folgenden Jahre

1. Domain 20 + Hosting 360 + Updates 480 = **860** bar.
2. Pflege durch den Inhaber: **960** Zeit.
3. Jedes folgende Jahr: **1.820**.

## Schritt 3: drei Jahre insgesamt

1. Jahr 1: 5.420.
2. Jahre 2 und 3: 1.820 x 2 = 3.640.
3. Drei Jahre: 5.420 + 3.640 = **9.060**, davon bar 3.860 + 860 x 2 = **5.580**.

Pro Monat über drei Jahre: 9.060 / 36 = etwa **252**, davon bar etwa **155**.

## Schritt 4: was die Website einbringen muss

Eine einfache Prüfung der Gewinnschwelle, wieder mit Beispielannahmen:

| Eingabe | Wert |
|---|---|
| Beispielannahme: durchschnittlicher Gewinn pro neuem Auftrag | 60 Einheiten |
| Kosten pro Monat über drei Jahre (aus Schritt 3) | etwa 252 Einheiten |

Neue Aufträge pro Monat, die die Website bringen muss, um sich zu bezahlen: 252 / 60 = etwa **4,2**, also etwa **5**
pro Monat.

Messen Sie es, indem Sie neue Kunden fragen, wie sie Sie gefunden haben ([Besuche messen](../gefunden-werden/besuche-messen.md#fragen-sie-ihre-kunden)).

## Was Sie für Ihr Unternehmen ändern

| Wenn | Ändern Sie |
|---|---|
| Sie online verkaufen | ergänzen Sie die Shop-Posten ([Durchgerechnetes Beispiel: ein kleiner Shop](../../unternehmen/kosten/durchgerechnetes-beispiel-kleiner-shop.md)) |
| Sie zwei Sprachen brauchen | ergänzen Sie einmalig die Übersetzung und mehr Pflegestunden |
| Ihr Partner ein festes Jahrespaket anbietet | ersetzen Sie Hosting und Updates durch diese Zahl |
| Sie die Updates selbst machen | ersetzen Sie die Partnerstunden durch Ihre eigenen Stunden, und rechnen Sie Lernzeit mit |
| Sie eine Buchungs- oder Bestellplattform nutzen | ergänzen Sie deren Gebühren ([Was einen externen Dienst braucht](was-einen-externen-dienst-braucht.md)) |

## Verwandte Seiten

- [Das Kostenbild](das-kostenbild.md)
- [ROI-Arbeitsblatt](../../kunden/nutzen/roi-arbeitsblatt.md)
- [Beispielannahmen für den ROI](../../kunden/nutzen/roi-beispielannahmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
