---
title: Literal und Begrenzer
audience: designer, developer
category: templates
order: 150
summary: Geschweifte Klammern in der Template-Ausgabe mit ldelim und rdelim ausgeben oder einen ganzen Abschnitt unverändert mit literal; warum Designs heute, da CSS und JavaScript in Dateien liegen, selten eines davon brauchen.
keywords: literal, ldelim, rdelim, Klammern, geschweifte Klammern, Klammern maskieren, Rohausgabe, Template-Begrenzer, JavaScript im Template, CSS im Template, braces, curly braces, escape braces, raw output, template delimiters, javascript in template, css in template
type: help
---
# Literal und Begrenzer

Diese Seite ist für Designer, die in der Ausgabe ein wörtliches `{` oder `}` brauchen.

## Inhalt

1. [ldelim und rdelim](#ldelim-und-rdelim)
2. [literal](#literal)
3. [Meist: nicht nötig](#meist-nicht-nötig)

## ldelim und rdelim

`{ldelim}` gibt `{` aus und `{rdelim}` gibt `}` aus (`eZTemplateDelimitFunction`):

```tpl
<code>function x() {ldelim} return 1; {rdelim}</code>
```

## literal

Alles zwischen `{literal}` und `{/literal}` wird unverändert ausgegeben; Tags darin werden nicht gelesen (der Parser behandelt
`literal` als Literal-Tag, `eZTemplate::registerLiteral( 'literal' )`):

```tpl
{literal}
<pre>{ "a": 1, "b": [1, 2] }</pre>
{/literal}
```

Innerhalb eines literal-Blocks können Sie keine Variable ausgeben; schließen Sie ihn zuerst.

## Meist: nicht nötig

Die meisten Klammern in alten Templates sind CSS oder JavaScript, das in das Template geschrieben wurde. In aktuellen Designs
liegt dieser Code in Dateien unter `stylesheets/` und `javascript/` ([Kein Inline-CSS](../theme-design/kein-inline-css.md),
[Kein Inline-JavaScript](../theme-design/kein-inline-javascript.md)); JSON für Skripte kommt aus `json_encode`. Das eigene
`node/view/full.tpl` des Kernels zeigt noch den alten Stil mit `{literal}` um ein Skript, was neue Templates genau vermeiden.

## Verwandte Seiten

- [TPL-Syntax: Grundlagen](tpl-syntax-grundlagen.md)
- [Kommentare](kommentare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
