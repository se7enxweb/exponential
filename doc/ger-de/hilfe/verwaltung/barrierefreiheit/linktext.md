---
title: Linktext
audience: government, editor, designer
category: public-sector
order: 180
summary: Für Redaktion und Design - wie Sie Linktexte schreiben, die für sich allein verständlich sind, wie Links auf Dokumente und neue Fenster angekündigt werden sollten und wie die Link-Werkzeuge und die Linkverwaltung von Exponential helfen, Links funktionsfähig zu halten.
keywords: Linktext, Links, hier klicken, mehr lesen, Zweck von Links, neues Fenster, Dokumente, Dateigröße, defekte Links, Linkverwaltung, Linkprüfung, Redaktion, link text, links, click here, read more, link purpose, WCAG 2.4.4, new window, target, documents, file size, broken links, link management, link check, editors
type: help
---
# Linktext

Diese Seite richtet sich an die Redaktion, die Links schreibt, und an das Design, das Listen und Teaser baut. Wer einen
Screenreader nutzt, lässt sich oft alle Links einer Seite auflisten und wählt aus dieser Liste, daher sind zwanzigmal
„hier klicken“ und „mehr lesen“ für diese Menschen nutzlos. WCAG 2.2 behandelt das unter 2.4.4 Linkzweck (im Kontext)
(<https://www.w3.org/TR/WCAG22/>).

## Inhalt

1. [Regeln für die Redaktion](#regeln-für-die-redaktion)
2. [Links auf Dokumente](#links-auf-dokumente)
3. [Links, die ein neues Fenster öffnen](#links-die-ein-neues-fenster-öffnen)
4. [Regeln für das Design](#regeln-für-das-design)
5. [Links funktionsfähig halten](#links-funktionsfähig-halten)
6. [Verwandte Seiten](#verwandte-seiten)

## Regeln für die Redaktion

| Statt | Schreiben Sie |
|---|---|
| Für einen Antrag klicken Sie hier. | Bewohnerparkausweis beantragen (die ganze Wortgruppe ist der Link) |
| Weitere Informationen | Öffnungszeiten des Bürgerbüros |
| https://www.example.gov/services/permits/parking | Parkausweise |
| Herunterladen | Jahresbericht 2025 (PDF, 2 MB) |

- Setzen Sie die Wörter, die sagen, wohin der Link führt, in den Link.
- Lassen Sie denselben Text auf einer Seite zum selben Ziel führen.
- Verlinken Sie keine ganzen Sätze; verlinken Sie die Schlüsselwörter.

## Links auf Dokumente

Nennen Sie Format und Größe im Linktext: „Haushaltsplan (PDF, 1,2 MB)“. Menschen mit langsamen Verbindungen und
Nutzerinnen und Nutzer von Screenreadern wissen dann, was geschehen wird. Bevorzugen Sie, wo möglich, eine Webseite
gegenüber einem Dokument (siehe „Dokumente und PDFs“).

## Links, die ein neues Fenster öffnen

Der Link im Rich Text hat eine Option für das Ziel; das Standarddesign schreibt sie als Attribut `target`
(`design/standard/templates/content/datatype/view/ezxmltags/link.tpl`). Das Öffnen eines neuen Fensters verwirrt viele
Benutzer. Verwenden Sie es selten, und wenn, dann sagen Sie es im Text: „Karte des Stadtteils (öffnet in einem neuen
Fenster)“. Die eigenen Hilfeseiten von Exponential folgen derselben Regel und ergänzen Links, die ein neues Fenster
öffnen, um einen Hinweis
([Hilfesystem: Sprachen und Barrierefreiheit](../../../../eng-us/features/6.0/help-system.md#languages-and-accessibility)).

## Regeln für das Design

- Teaser-Listen: Machen Sie den Titel zum Link statt eines separaten „mehr lesen“; wenn ein „mehr lesen“ nötig ist,
  geben Sie ihm einen verborgenen Text mit dem Titel (eine visuell verborgene Klasse, wie admin4 es mit `.a4-sr-only`
  tut).
- Links müssen wie Links aussehen (nicht nur durch Farbe) und einen sichtbaren Fokusstil haben.
- Links, die nur aus einem Symbol bestehen, brauchen einen zugänglichen Namen.

## Links funktionsfähig halten

- **Setup > URL-Verwaltung** (`/url/list`) listet jede externe Adresse auf, auf die veröffentlichte Inhalte verlinken,
  mit ihrem Zustand (gültig, ungültig, nie geprüft) und den Objekten, die sie verwenden.
- Die **Linkprüfung** (`linkcheck.php`, Cronjob-Teil `infrequent`) testet sie; ihre Einstellungen stehen in
  `cronjob.ini [linkCheckSettings]`
  ([Links, URL-Aliase, Platzhalter](../../../anleitungen/urls-und-aliase.md#2-wie-links-geprüft-werden)).
- **Defekte Beziehungen** (`/contentcheck/brokenrelations/<node id>`) findet Inhalte, die auf entfernte, verborgene oder
  unveröffentlichte Inhalte verlinken oder sie einbetten ([Die Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)).

## Verwandte Seiten

- [Barrierefreie Inhalte für die Redaktion](barrierefreie-inhalte-fuer-die-redaktion.md)
- [Link- und Inhaltsprüfungen](../../../../eng-us/help/creators/website-creators/link-and-content-checks.md)
- [Inhalte bearbeiten](../../inhalte-bearbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
