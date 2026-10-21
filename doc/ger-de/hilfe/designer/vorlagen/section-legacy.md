---
title: Section (veraltet)
audience: designer, developer
category: templates
order: 110
summary: Die alte Funktion section in älteren Templates lesen (loop, sequence, show, section-else, Variablen mit Namensraum wie $Item:item) und sie in foreach, if und def umwandeln.
keywords: section, section loop, section-else, section show, veraltete Templates, Namensraum-Variablen, $:item, section in foreach umwandeln, alte Template-Syntax, legacy templates, namespace variables, convert section to foreach, old template syntax
type: help
---
# Section (veraltet)

Diese Seite ist für Designer, die ältere Templates pflegen. `{section}` war die allgemeine Schleife und Bedingung früher
Template-Versionen; sie funktioniert noch (`eZTemplateSectionFunction`) und kommt in mitgelieferten Templates noch vor. Neue
Templates verwenden `{foreach}`, `{if}` und `{def}`.

## Inhalt

1. [Sie lesen](#sie-lesen)
2. [Sie umwandeln](#sie-umwandeln)

## Sie lesen

```tpl
{section name=Child loop=$children}
    <li>{$Child:item.name|wash}</li>
{section-else}
    <li>{'None'|i18n( 'design/mytheme' )}</li>
{/section}
```

- `loop=` das Array (oder eine Zahl: so oft durchlaufen).
- `name=` ein Namensraum; darin ist der aktuelle Eintrag `$Child:item`, der Schlüssel `$Child:key`, der Index `$Child:index`.
- `section-else` läuft, wenn das Array leer ist.
- `show=` macht die section zu einer Bedingung: `{section show=$node.can_edit} ... {/section}`.
- `sequence=`, `max=`, `offset=`, `{delimiter}` funktionieren wie ihre Gegenstücke bei foreach.

## Sie umwandeln

| Section | Modern |
|---|---|
| `{section name=X loop=$list}` ... `$X:item` | `{foreach $list as $item}` ... `$item` |
| `{section-else}` | `{if $list\|count}{foreach ...}{/foreach}{else}...{/if}` |
| `{section show=$cond}` | `{if $cond}` |
| `{let a=1}` ... `{/let}` | `{def $a=1}` ... `{undef $a}` |
| `$:item` (aktueller Namensraum) | die Schleifenvariable |

Wandeln Sie ein Template vollständig um und vergleichen Sie dann die Ausgabe einiger Seiten vorher und nachher.

## Verwandte Seiten

- [Foreach-Schleifen](foreach-schleifen.md)
- [If und Bedingungen](if-und-bedingungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
