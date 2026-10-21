---
title: Section 508 und Exponential
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Für US-Bundesbehörden und ihre Auftragnehmer - was Section 508 und die Revised 508 Standards allgemein von Webinhalten und Autorenwerkzeugen verlangen, der Status des Projekts (Unverified, kein VPAT) und wie Sie eine mit Exponential gebaute Website bewerten.
keywords: Section 508, Rehabilitation Act, Revised 508 Standards, US Access Board, VPAT, ACR, Konformitätsbericht, Bundesbehörden, Vereinigte Staaten, Beschaffung, Accessibility Conformance Report, WCAG 2.0 AA, federal, United States, procurement
type: help
---
# Section 508 und Exponential

Diese Seite richtet sich an US-Bundesbehörden, an die Auftragnehmer, die ihre Websites bauen und hosten, und an
Beschaffungsstellen, die ein Open-Source-CMS bewerten müssen. Sie erklärt allgemein, wie sich Section 508 zu
Exponential verhält und was die Erklärung des Projekts zur Barrierefreiheit sagt.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Section 508 in Kürze](#section-508-in-kürze)
2. [Der Status des Projekts](#der-status-des-projekts)
3. [Was zu bewerten ist](#was-zu-bewerten-ist)
4. [Wenn ein VPAT verlangt wird](#wenn-ein-vpat-verlangt-wird)
5. [Verwandte Seiten](#verwandte-seiten)

## Section 508 in Kürze

Section 508 des Rehabilitation Act verpflichtet Bundesbehörden, die Informations- und Kommunikationstechnik, die sie
entwickeln, beschaffen, pflegen oder nutzen, barrierefrei zu gestalten. Die Revised 508 Standards, 2017 vom US Access
Board erlassen, übernehmen für Webinhalte WCAG 2.0 Stufe AA durch Verweis (<https://www.access-board.gov/ict/>). Die
regierungsweite Programmseite ist <https://www.section508.gov/>. Hersteller dokumentieren die Konformität meist in einem
VPAT (Voluntary Product Accessibility Template), aus dem ein Accessibility Conformance Report entsteht.

## Der Status des Projekts

Aus [Standards zur Barrierefreiheit: Section 508](../../../../eng-us/standards/accessibility.md#section-508):

- **Status: Unverified.** Es gibt kein VPAT und keinen Accessibility Conformance Report für Exponential.
- **Inhaltlich** deckt die Arbeit an WCAG es ab: WCAG 2.0 AA ist eine Teilmenge dessen, wogegen die Administration admin4
  getestet wird (WCAG 2.2 AA, bewertet mit **Partly complies**).
- Nicht überprüft: der Online-Editor (Integrationen von TinyMCE, **Unverified**), Designs öffentlicher Websites (nicht
  geprüft), als eigene Pakete ausgelieferte Erweiterungen (keine Gesamterklärung).

## Was zu bewerten ist

Eine Bewertung eines Exponential-Projekts nach Section 508 hat meist zwei Gegenstände:

| Gegenstand | Benutzer | Was zu prüfen ist |
|---|---|---|
| Die öffentliche Website | die Öffentlichkeit, einschließlich Menschen mit Behinderungen | die Templates Ihres Designs, die Inhalte, Dokumente, Formulare, Medien; mindestens WCAG 2.0 AA |
| Die Autorenumgebung | Beschäftigte der Behörde, die bearbeiten | die Administration admin4, der Online-Editor, die Upload-Dialoge; Bedienung per Tastatur und Screenreader, Kontrast, Fehlerbehandlung |

Für die Autorenseite beginnen Sie mit dem, was das Projekt testet (Beschriftungen, Landmarks, Tastaturmuster,
Kontrast-Tokens, Fehlerübersichten; siehe „Was admin4 von Exponential abdeckt“ in diesem Bereich), und testen Sie den
Online-Editor und alle Erweiterungen, die Sie aktivieren, selbst, weil das Projekt es nicht getan hat.

## Wenn ein VPAT verlangt wird

Eine Ausschreibung kann vom Bieter ein VPAT verlangen. Mit Exponential:

1. Legen Sie die Erklärung des Projekts nicht als VPAT vor. Sie ist ein Statusbericht mit Nachweisen und sagt, dass kein
   VPAT existiert.
2. Brauchen Sie (oder der Bieter) eines, erstellen Sie einen ACR **für das konfigurierte Produkt, das Sie liefern**: Ihre
   Version von Exponential, Ihr Administrationsdesign (admin4), Ihr öffentliches Design, Ihre aktivierten Erweiterungen.
   Die VPAT-Vorlage des ITI wird außerhalb dieses Projekts gepflegt; verwenden Sie die Ausgabe, die Ihre Behörde verlangt.
3. Stützen Sie jede Zeile auf einen Test, den Sie durchgeführt haben, und bewahren Sie die Testnotizen auf.
   „Supports“, „Partially supports“ und „Does not support“ müssen durch Nachweise belegt sein; wo Sie nicht getestet
   haben, schreiben Sie „Not evaluated“.
4. Eine Arbeitsmethode finden Sie unter „Einen Accessibility Conformance Report für Ihre Website vorbereiten“ in diesem
   Bereich.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md)
- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
