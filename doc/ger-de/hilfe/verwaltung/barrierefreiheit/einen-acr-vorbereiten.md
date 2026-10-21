---
title: Einen Accessibility Conformance Report für Ihre Website vorbereiten
audience: government, project-manager, developer
category: public-sector
order: 140
summary: Für öffentliche Stellen und Lieferanten, die um ein VPAT oder einen Accessibility Conformance Report gebeten werden - Exponential hat keinen, daher erklärt diese Seite, wie Sie für die Konfiguration, die Sie tatsächlich liefern, einen ehrlichen ACR abgrenzen, testen und schreiben.
keywords: ACR, Accessibility Conformance Report, Konformitätsbericht, VPAT, Konformitätsstufe, unterstützt, teilweise unterstützt, nicht unterstützt, nicht bewertet, Nachweise, Ausschreibung, Beschaffung, Umfang, Voluntary Product Accessibility Template, conformance level, supports, partially supports, does not support, not evaluated, evidence, tender, procurement, scope
type: help
---
# Einen Accessibility Conformance Report für Ihre Website vorbereiten

Diese Seite richtet sich an Projektverantwortliche und Lieferanten, die in einer Ausschreibung oder von einem Käufer
um einen Accessibility Conformance Report (ACR) gebeten werden, oft in Form eines VPAT. Es gibt **kein VPAT und keinen
ACR für Exponential**
([Standards zur Barrierefreiheit: Section 508](../../../standards/barrierefreiheit.md#section-508)). Was Sie erstellen
können, ist ein Bericht für **Ihre Konfiguration**: eine bestimmte Version von Exponential, ein Administrationsdesign,
ein öffentliches Design und eine Menge von Erweiterungen. Diese Seite zeigt, wie.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Das Produkt abgrenzen](#das-produkt-abgrenzen)
2. [Die Standards wählen](#die-standards-wählen)
3. [Testen](#testen)
4. [Jede Zeile schreiben](#jede-zeile-schreiben)
5. [Was Sie vom Projekt übernehmen dürfen](#was-sie-vom-projekt-übernehmen-dürfen)
6. [Verwandte Seiten](#verwandte-seiten)

## Das Produkt abgrenzen

Schreiben Sie den Umfang auf die erste Seite des Berichts:

| Punkt | Wo Sie ihn ablesen |
|---|---|
| Version von Exponential | Setup > Systeminformationen oder `composer show se7enxweb/exponential` |
| Administrationsdesign | `[DesignSettings] SiteDesign` des Administrations-Siteaccess (admin4 empfohlen) |
| Engine des Online-Editors | `ezoe.ini [EditorSettings] EditorEngine` (standardmäßig TinyMCE 3, TinyMCE 8 auf Wunsch) |
| Öffentliches Design | Ihr Design und seine Erweiterung |
| Aktivierte Erweiterungen | Setup > Extensions |
| Getestete Browser und assistive Technologien | Ihr Testplan |

Alles außerhalb des Umfangs steht im Bericht als „Nicht bewertet“.

## Die Standards wählen

Fragen Sie den Käufer, welche Ausgabe er erwartet: WCAG 2.x (A und AA), Revised Section 508, EN 301 549 oder alle.
Die Ausgaben der VPAT-Vorlage veröffentlicht der ITI außerhalb dieses Projekts; verwenden Sie die, die der Käufer
nennt.

## Testen

1. **Öffentliche Website**: Bewerten Sie mit WCAG-EM (siehe „Ihr öffentliches Design mit WCAG-EM prüfen“).
2. **Administration**: Wählen Sie redaktionelle Aufgaben (anmelden, Inhalte finden, einen Artikel mit Bild anlegen und
   veröffentlichen, eine Datei hochladen, Versionen lesen, abmelden) und testen Sie sie mit der Tastatur, einem
   Screenreader, 200 % Zoom und erzwungenen Farben.
3. **Online-Editor**: gesondert testen (siehe „Der Online-Editor und die Barrierefreiheit“).
4. Führen Sie Testnotizen je Kriterium: Datum, Seite oder Aufgabe, Werkzeug, Ergebnis.

## Jede Zeile schreiben

| Konformitätsstufe | Verwenden Sie sie, wenn |
|---|---|
| Supports (unterstützt) | jede getestete Seite und Aufgabe das Kriterium erfüllt |
| Partially supports (teilweise unterstützt) | manche es erfüllen, manche nicht; nennen Sie welche |
| Does not support (nicht unterstützt) | die meisten es nicht erfüllen |
| Nicht anwendbar | das Kriterium nicht auftreten kann (zum Beispiel kein Audio auf der Website) |
| Not evaluated (nicht bewertet) | Sie es nicht getestet haben; raten Sie nie |

Sagen Sie in den Anmerkungen, woher eine Einschränkung stammt (Ihr Template, ein Inhaltselement, eine Erweiterung, der
Online-Editor) und was die Umgehung oder der Plan ist.

## Was Sie vom Projekt übernehmen dürfen

Sie dürfen, mit dem Link, die eigenen Nachweise des Projekts für die Administration zitieren:

- die Statusangaben der [Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md) (Teilweise erfüllt,
  Nicht geprüft);
- die Liste der Designtests und was sie absichern (Formularbeschriftungen, Landmarks, Tastaturmuster, Kontrast-Tokens,
  Fehlerübersichten);
- die manuellen axe-Ergebnisse, festgehalten im [Changelog von 6.0.15](../../../../changelogs/6.0/6.0.15.md).

Machen Sie aus „Teilweise erfüllt“ nicht „Supports“. Die Tests des Projekts lesen Template-Quellen; sie sind Nachweise
für Ihre Anmerkungen, kein Ersatz für Ihren Test.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md)
- [Systeminformationen](../../../anleitungen/systeminformationen.md)
- [Die Barrierefreiheit Ihrer Website testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
