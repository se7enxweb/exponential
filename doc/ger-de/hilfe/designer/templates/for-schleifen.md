---
title: For-Schleifen
audience: designer, developer
category: templates
order: 80
summary: Zählschleifen mit for - von einer Zahl bis zu einer Zahl, rückwärts zählen, Sequenzen und typische Verwendungen wie Sternebewertungen, Platzhalter und Seitenzahlen.
keywords: for, for-Schleife, zählen, Bereich, 1 bis 5, rückwärts zählen, Template-Schleife, Bewertungssterne, Seitenzahlen, for loop, counting, range, count down, template loop, rating stars, page numbers
type: help
---
# For-Schleifen

Diese Seite ist für Designer, die etwas eine bestimmte Anzahl von Malen wiederholen müssen.

## Inhalt

1. [Syntax](#syntax)
2. [Beispiele](#beispiele)
3. [Hinweise](#hinweise)

## Syntax

Aus `lib/eztemplate/classes/eztemplateforfunction.php`:

```text
{for <number> to <number> as $itemVar [sequence <array> as $seqVar]}
    [{delimiter}...{/delimiter}]
    [{break}] [{continue}] [{skip}]
{/for}
```

`{for 1 to 5 as $i}` zählt aufwärts; `{for 5 to 1 as $i}` zählt abwärts.

## Beispiele

Fünf Sterne, bis zu einer Bewertung gefüllt:

```tpl
<span class="rating" role="img" aria-label="{'%count of 5 stars'|i18n( 'design/mytheme',, hash( '%count', $rating ) )|wash}">
{for 1 to 5 as $star}
    <span class="star{if le( $star, $rating )} is-filled{/if}" aria-hidden="true"></span>
{/for}
</span>
```

Seitenzahlen (wenn Sie nicht die mitgelieferten Navigator-Templates verwenden, siehe [Seitenumbruch](../../../../eng-us/help/designers/templates/pagination.md)):

```tpl
{for 1 to $page_count as $page}
    <a href={concat( $node.url_alias, '/(offset)/', $page|dec|mul( $limit ) )|ezurl}>{$page}</a>{delimiter} {/delimiter}
{/for}
```

## Hinweise

- Die Grenzen können Variablen und Operatoren sein: `{for 1 to $items|count as $i}`.
- Für Listen ist `{foreach}` mit `offset` und `max` klarer ([Foreach-Schleifen](foreach-schleifen.md)).

## Verwandte Seiten

- [Foreach-Schleifen](foreach-schleifen.md)
- [Mathematische Operatoren](mathematische-operatoren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
