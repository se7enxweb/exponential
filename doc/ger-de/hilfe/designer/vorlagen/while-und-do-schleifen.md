---
title: While- und do-Schleifen
audience: designer, developer
category: templates
order: 70
summary: Die Schleifenfunktionen while und do der Template-Sprache, ihre Syntax mit sequence, delimiter, break, continue und skip und warum foreach fast immer die bessere Wahl ist.
keywords: while, do, do while, Schleife, Bedingungsschleife, Template-Schleife, break, continue, loop, condition loop, template loop
type: help
---
# While- und do-Schleifen

Diese Seite ist für Designer, die in einem Template auf `{while}` oder `{do}` stoßen. Beide wiederholen, solange eine Bedingung
gilt. In Design-Templates sind sie selten: Die meisten Schleifen laufen über eine Liste, was `{foreach}` besser kann
([Foreach-Schleifen](foreach-schleifen.md)).

## Inhalt

1. [while](#while)
2. [do ... while](#do--while)
3. [Wann Sie sie verwenden](#wann-sie-sie-verwenden)

## while

Aus `lib/eztemplate/classes/eztemplatewhilefunction.php`:

```text
{while <condition> [sequence <array> as $seqVar]}
    [{delimiter}...{/delimiter}]
    [{break}] [{continue}] [{skip}]
{/while}
```

```tpl
{def $level = $node.depth}
{while gt( $level, 1 )}
    <span class="indent"></span>
    {set $level = $level|dec}
{/while}
{undef $level}
```

Die Bedingung wird vor jedem Durchlauf geprüft; ändern Sie im Inhalt eine Variable, sonst endet die Schleife nie.

## do ... while

Aus `lib/eztemplate/classes/eztemplatedofunction.php`:

```text
{do}
    ...
{/do while <condition> [sequence <array> as $seqVar]}
```

Der Inhalt läuft einmal, bevor die Bedingung geprüft wird.

## Wann Sie sie verwenden

In einem Design fast nie. Zählen ist mit `{for}` klarer ([For-Schleifen](for-schleifen.md)), Listen mit `{foreach}`. Eine Schleife
ohne Ende rendert bis zum Zeitlimit von PHP; `template.ini [ControlSettings] MaxLevel` der Template-Engine begrenzt die
Verschachtelung von Templates, nicht die Schleifendurchläufe.

## Verwandte Seiten

- [Foreach-Schleifen](foreach-schleifen.md)
- [For-Schleifen](for-schleifen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
