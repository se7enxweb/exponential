---
title: Das Prinzip Verständlich
audience: government, editor, designer, developer
category: public-sector
order: 320
summary: WCAG-Prinzip 3, verständlich, für eine öffentliche Stelle, die Exponential betreibt - lesbare Sprache, vorhersehbare Seiten und Hilfe bei der Eingabe, mit der Praxis einfacher Sprache für die Redaktion und Regeln für Formulare und Fehler für die Entwicklung.
keywords: verständlich, WCAG-Prinzip 3, lesbar, Sprache der Seite, einfache Sprache, Leichte Sprache, vorhersehbar, konsistente Navigation, Hilfe bei der Eingabe, Fehlererkennung, Beschriftungen, Korrekturvorschlag, redundante Eingabe, barrierefreie Authentifizierung, understandable, WCAG principle 3, readable, language of page, plain language, predictable, consistent navigation, input assistance, error identification, labels, error suggestion, redundant entry, accessible authentication, 3.1, 3.2, 3.3
type: help
---
# Das Prinzip Verständlich

Diese Seite richtet sich an alle im Team einer öffentlichen Website. Das dritte Prinzip von WCAG besagt, dass
Informationen und die Bedienung der Benutzerschnittstelle verständlich sein müssen (<https://www.w3.org/TR/WCAG22/>).
Für öffentliche Stellen überschneidet es sich mit Pflichten zur einfachen Sprache: Menschen müssen verstehen, was der
Staat ihnen mitteilt und von ihnen verlangt.

## Inhalt

1. [3.1 Lesbar](#31-lesbar)
2. [3.2 Vorhersehbar](#32-vorhersehbar)
3. [3.3 Hilfestellung bei der Eingabe](#33-hilfestellung-bei-der-eingabe)
4. [Verwandte Seiten](#verwandte-seiten)

## 3.1 Lesbar

| Rolle | Tut |
|---|---|
| Entwicklung | `<html lang>` je Siteaccess; `lang` an Passagen aus einer anderen Sprache |
| Redaktion | einfache Sprache: kurze Sätze, gebräuchliche Wörter, aktive Formulierungen, erklärte Begriffe; Abkürzungen bei der ersten Verwendung ausgeschrieben |
| Administration | ein Siteaccess und eine Übersetzung je Sprache ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)) |

admin4 schreibt `lang` aus dem Locale des Siteaccess. Für einfache Sprache ist die Leitlinie der US-Bundesverwaltung
unter <https://www.plainlanguage.gov/> eine kostenlose Referenz. Siehe „Sprache der Seite und ihrer Teile“.

## 3.2 Vorhersehbar

| Rolle | Tut |
|---|---|
| Design | dieselbe Navigation an derselben Stelle auf jeder Seite; dieselbe Funktion mit demselben Namen |
| Entwicklung | kein Kontextwechsel allein beim Fokus oder bei einer Auswahl (eine Auswahlliste, die sich selbst absendet) |
| Redaktion | Links auf Dokumente und neue Fenster mit einem Hinweis im Text |

Gemeinsame Kopf- und Fußbereiche halten die Navigation konsistent
([Gemeinsamer Kopf- und Fußbereich](../../../../eng-us/help/creators/website-creators/shared-header-and-footer.md)).

## 3.3 Hilfestellung bei der Eingabe

| Rolle | Tut |
|---|---|
| Entwicklung | Fehler im Text benannt, verlinkt aus einer Übersicht, die den Fokus erhält; Vorschläge zur Korrektur; Werte bleiben erhalten |
| Design | sichtbare Beschriftungen und Anleitungen vor den Feldern |
| Redaktion | klare Feldbeschreibungen in Formularklassen; eine Bestätigungsseite, die sagt, was als Nächstes geschieht |
| Administration | eine Anmeldung, die mit Passwortmanagern funktioniert; bedenken, was die zweistufige Anmeldung von den Benutzern verlangt (3.3.8) |

admin4: Fehlerübersicht und Feldmeldungen (`AdminFormErrorsTest.php`). Siehe „Barrierefreie Formulare“.

## Verwandte Seiten

- [Barrierefreie Formulare](barrierefreie-formulare.md)
- [Barrierefreie Inhalte für die Redaktion](barrierefreie-inhalte-fuer-die-redaktion.md)
- [WCAG 2.2 AA auf Exponential abgebildet](wcag-2-2-zuordnung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
