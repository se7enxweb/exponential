---
title: Template-Styleguide
audience: designer, developer
category: templates
order: 340
summary: Der Hausstil für Exponential-Templates - ein Kopfkommentar, Variablen mit Präfix und undef, wash bei der Ausgabe, i18n-Kontexte, einmal und mit Limits abrufen, kleine Teile, kein Inline-CSS oder -JavaScript, semantisches und barrierefreies Markup und HTML5 ohne XHTML-Schrägstriche.
keywords: Styleguide, Template-Konventionen, Coding-Standards für Templates, Benennung, Einrückung, Kopfkommentar, Präfixe, undef, wash, i18n-Kontext, HTML5, bewährte Praxis, style guide, template conventions, coding standards templates, naming, indentation, header comment, prefixes, html5, best practices
type: help
---
# Template-Styleguide

Diese Seite ist für Designer und Entwickler, die Templates wollen, die andere lesen und pflegen können. Sie sammelt die
Gewohnheiten, denen die mitgelieferten Designs folgen.

## Inhalt

1. [Dateien](#dateien)
2. [Variablen](#variablen)
3. [Ausgabe](#ausgabe)
4. [Daten](#daten)
5. [Markup](#markup)
6. [Formatierung](#formatierung)

## Dateien

- Ein Zweck pro Template; teilen Sie lange Templates in Teile unter einem eigenen Ordner auf (`pagelayout/`, `parts/`).
- Ein Kopfkommentar: was das Template zeichnet, welche Parameter es nimmt, jede nicht offensichtliche Entscheidung
  ([Kommentare](kommentare.md)).
- Pfade und Namen in Kleinbuchstaben mit `_` oder `-`; Klassenbezeichner in Dateinamen (`full/ng_article.tpl`).

## Variablen

- `{def}` nahe am Anfang, `{undef}` am Ende von Teilen.
- Stellen Sie den Variablen von Teilen ein Präfix voran (`$tz_`, `$pl_`), damit sie nie mit denen des Einbindenden kollidieren.
- Optionale Parameter mit `{default ...}` und im Kopfkommentar dokumentiert.

## Ausgabe

- `|wash` bei jedem Wert aus Inhalten, Einstellungen oder der Anfrage, zum Zeitpunkt der Ausgabe ([Wash und Maskierung](wash-und-maskierung.md)).
- Jeder Oberflächentext durch `i18n` mit einem Kontext `design/<theme>/<area>` ([Der Operator i18n](der-i18n-operator.md)).
- URLs nur über `ezurl`, `ezroot`, `ezimage`, `ezdesign` ([URL-Operatoren](url-operatoren.md)).

## Daten

- Einmal abrufen, mit einem `limit`, außerhalb von Schleifen ([Fetch-Leistung](fetch-leistung.md)).
- Optionale Felder vor dem Zeichnen mit `has_content` prüfen.
- Im Pagelayout Werte aus `content_info` dem erneuten Abrufen des Knotens vorziehen.
- Cache-Blöcke mit ausdrücklichen Schlüsseln um teure Teile ([Cache-Block](cache-block.md)).

## Markup

- HTML5: keine XHTML-Schrägstriche (`<br>`, nicht `<br />`), kein `type` an `<script>`- und `<style>`-Links (das Release 6.0.3
  von ezwebin hat sie aus 57 Dateien entfernt).
- Semantische Elemente (`<article>`, `<nav>`, `<main>`, `<time>`), eine `h1`, Überschriften in Reihenfolge.
- Kein `<style>`, kein `style=""`, keine Inline-Skripte oder `on*=`-Handler ([CSP-sicheres Design](../theme-design/csp-sicheres-design.md)).
- Klassen beschreiben die Komponente, nicht das Aussehen (`.teaser-title`, nicht `.big-red`).

## Formatierung

- Rücken Sie verschachtelte Tags um vier Leerzeichen ein, wie die mitgelieferten Templates.
- Halten Sie Template-Tags und HTML bei tiefer Verschachtelung auf getrennten Zeilen; Leerraum in Templates wird zu Leerraum
  in der Ausgabe, was in HTML harmlos ist.
- Lange Operatoraufrufe über Zeilen umbrechen, mit ausgerichteten Parametern (die Designs media und starter tun das bei den
  Listen von `ezcss_load`).

## Verwandte Seiten

- [Häufige Template-Fehler](haeufige-template-fehler.md)
- [Checkliste: Code-Review für Templates](../theme-design/checkliste-code-review-fuer-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
