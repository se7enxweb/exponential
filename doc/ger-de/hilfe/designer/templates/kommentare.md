---
title: Kommentare
audience: designer, developer
category: templates
order: 160
summary: Template-Kommentare, die nie den Browser erreichen, wie Sie Variablen und Zweck eines Templates an seinem Anfang dokumentieren, wie es die mitgelieferten Designs tun, Code auskommentieren und warum HTML-Kommentare das falsche Werkzeug sind.
keywords: Kommentare, Template-Kommentare, {* *}, Dokumentation in Templates, Kopfkommentar, auskommentieren, HTML-Kommentare, comments, template comments, documentation in templates, header comment, comment out, html comments
type: help
---
# Kommentare

Diese Seite ist für Designer, die Templates dokumentieren.

## Inhalt

1. [Template-Kommentare](#template-kommentare)
2. [Ein Kopfkommentar](#ein-kopfkommentar)
3. [Auskommentieren](#auskommentieren)
4. [Keine HTML-Kommentare](#keine-html-kommentare)

## Template-Kommentare

```tpl
{* One line *}
{* Several
   lines *}
```

Template-Kommentare werden beim Kompilieren des Templates entfernt. Sie kosten zur Laufzeit nichts und erreichen nie
Besucher.

## Ein Kopfkommentar

Die mitgelieferten Designs beginnen wichtige Templates mit einem Kommentar, der sagt, was das Template tut, welche Variablen
es erwartet und warum es so geschrieben ist. Die `pagelayout/head/style.tpl` des Designs media erklärt ihre
Dateireihenfolge und die Entscheidung zu den Schriften; das Pagelayout von starter listet seine `pl_`-Variablen. Tun Sie
dasselbe:

```tpl
{* Teaser of a node, used in lists and layout blocks.
   Parameters: node (required), show_image (default false), image_class (default 'medium').
   Prints an <article>; the heading level is h3 because lists sit under an h2. *}
```

## Auskommentieren

Umschließen Sie Code mit `{* ... *}`, um ihn abzuschalten; das Design simple hält alternative Menü-Includes auf diese
Weise. Entfernen Sie toten Code vor einem Release, statt ihn lange auskommentiert stehen zu lassen.

## Keine HTML-Kommentare

`<!-- ... -->` wird an jeden Besucher geschickt, kann interne Notizen verraten und fügt Bytes hinzu. Verwenden Sie ihn nur,
wenn der Kommentar für den HTML-Quelltext gedacht ist (selten). Um zu debuggen, welches Template was gezeichnet hat,
verwenden Sie stattdessen Template-Pfadkommentare ([Template-Pfadkommentare](template-pfad-kommentare.md)).

## Verwandte Seiten

- [Template-Styleguide](template-styleguide.md)
- [TPL-Syntax: Grundlagen](tpl-syntax-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
