---
title: If und Bedingungen
audience: designer, developer
category: templates
order: 50
summary: Bedingte Ausgabe mit if, elseif und else; die Vergleichs- und Logikoperatoren in Bedingungen (eq, ne, gt, lt, and, or, not); auf Inhalt, Leere und Vorhandensein prüfen; und Bedingungen innerhalb von Attributwerten.
keywords: if, elseif, else, Bedingung, eq, ne, gt, lt, ge, le, and, or, not, is_set, has_content, count, leer, bedingte Ausgabe, condition, empty, conditional output
type: help
---
# If und Bedingungen

Diese Seite ist für Designer, die Teile eines Templates zeigen oder ausblenden. Sie behandelt die Funktion `{if}` und die
Operatoren, aus denen Bedingungen gebaut werden.

## Inhalt

1. [Die Funktion if](#die-funktion-if)
2. [Bedingungen bauen](#bedingungen-bauen)
3. [Übliche Prüfungen](#übliche-prüfungen)
4. [Innerhalb von Attributen](#innerhalb-von-attributen)
5. [Stattdessen cond und first_set](#stattdessen-cond-und-first_set)

## Die Funktion if

```tpl
{if $node.data_map.image.has_content}
    {attribute_view_gui attribute=$node.data_map.image}
{elseif $node.data_map.video.has_content}
    {attribute_view_gui attribute=$node.data_map.video}
{else}
    <p class="no-media">{'No media'|i18n( 'design/mytheme' )}</p>
{/if}
```

(`eZTemplateIfFunction`, mit den Attributen `elseif` und `else`.)

## Bedingungen bauen

Bedingungen sind Operatoren ([Logik- und Vergleichsoperatoren](logik-und-vergleichsoperatoren.md)):

| Operator | Wahr, wenn |
|---|---|
| `eq( $a, $b )` | gleich |
| `ne( $a, $b )` | ungleich |
| `gt`, `ge`, `lt`, `le` | größer, größer oder gleich, kleiner, kleiner oder gleich |
| `and( $a, $b, ... )` | alle wahr |
| `or( $a, $b, ... )` | mindestens einer wahr |
| `not( $a )` oder `$a\|not` | falsch |

Die Pipe-Form funktioniert auch: `$count|gt( 10 )`, `$node.class_identifier|eq( 'article' )`. Einfache Vergleiche in
Infix-Schreibweise (`$a == $b`) gehören nicht zur Sprache; verwenden Sie die Operatoren.

## Übliche Prüfungen

```tpl
{if $node.data_map.intro.has_content}          {* a field has content *}
{if $children|count}                           {* a list is not empty *}
{if is_set( $view_parameters.offset )}         {* a value exists *}
{if $current_user.is_logged_in}                {* a visitor is signed in *}
{if $list|contains( $node.node_id )}           {* an array contains a value *}
{if $node.class_identifier|eq( 'article' )}    {* class test *}
```

## Innerhalb von Attributen

```tpl
<li class="menu-item{if eq( $item.node_id, $current_node_id )} is-current{/if}">
<a href={$item.url_alias|ezurl}{if eq( $item.node_id, $current_node_id )} aria-current="page"{/if}>
```

## Stattdessen cond und first_set

Für einen Wert statt Markup ist `cond( condition, value, else_value )` kürzer ([cond und first_set](cond-und-first-set.md)):

```tpl
<body class="{cond( $current_user.is_logged_in, 'signed-in', 'anonymous' )}">
```

## Verwandte Seiten

- [Switch und case](switch-und-case.md)
- [Logik- und Vergleichsoperatoren](logik-und-vergleichsoperatoren.md)
- [cond und first_set](cond-und-first-set.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
