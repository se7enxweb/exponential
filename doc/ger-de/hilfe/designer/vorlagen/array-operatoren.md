---
title: Array-Operatoren
audience: designer, developer
category: templates
order: 190
summary: Arrays und Hashes in Templates bilden und bearbeiten - array, hash, append, prepend, merge, contains, extract, extract_left, extract_right, implode, explode, reverse, unique, insert, remove, repeat, begins_with, ends_with, compare, array_sum, count.
keywords: Array-Operatoren, array, hash, append, prepend, merge, contains, extract, extract_left, extract_right, implode, explode, reverse, unique, insert, remove, repeat, array_sum, count, Listen bearbeiten, array operators, list handling
type: help
---
# Array-Operatoren

Diese Seite ist für Designer, die Listen bilden und darin nachschlagen. Die Operatoren stammen aus
`eZTemplateArrayOperator` (`lib/eztemplate/classes/eztemplatearrayoperator.php`). Die meisten funktionieren auch mit
Zeichenketten.

## Inhalt

1. [Bilden](#bilden)
2. [Prüfen](#prüfen)
3. [Teile entnehmen](#teile-entnehmen)
4. [Verbinden und aufteilen](#verbinden-und-aufteilen)
5. [Ändern](#ändern)
6. [Ältere Namen](#ältere-namen)

## Bilden

```tpl
{def $sizes = array( 'small', 'medium', 'large' )}
{def $labels = hash( 'small', 'S', 'medium', 'M', 'large', 'L' )}
{set $sizes = $sizes|append( 'xlarge' )}
{set $sizes = $sizes|prepend( 'tiny' )}
{def $all = $sizes|merge( $more_sizes )}
```

`append`, `prepend` und `merge` geben ein neues Array zurück; weisen Sie es mit `{set}` zu.

## Prüfen

| Ausdruck | Wahr, wenn |
|---|---|
| `$list\|contains( 'x' )` | das Array (oder die Zeichenkette) den Wert enthält |
| `$text\|begins_with( 'http' )` | damit beginnt |
| `$text\|ends_with( '.pdf' )` | damit endet |
| `$a\|compare( $b )` | beide gleich sind |
| `$list\|count` | (eine Zahl) die Zahl der Einträge; `0` gilt in `{if}` als falsch |

## Teile entnehmen

```tpl
{$list|extract( 2, 3 )}        {* 3 items from position 2 *}
{$list|extract_left( 3 )}      {* the first 3 *}
{$list|extract_right( 2 )}     {* the last 2 *}
{$list|reverse}
{$list|unique}
```

## Verbinden und aufteilen

```tpl
{$tags|implode( ', ' )}                         {* array to string *}
{$csv|explode( ',' )}                           {* string to array *}
{$user.role_id_list|implode( ',' )}             {* a common cache-block key *}
```

## Ändern

```tpl
{$list|insert( 1, 'new' )}      {* insert at position 1 *}
{$list|remove( 0, 1 )}          {* remove 1 item at position 0 *}
{'-'|repeat( 10 )}              {* '----------' *}
{$numbers|array_sum}
```

## Ältere Namen

`array_append`, `array_prepend` und `array_merge` sind in der Autoload-Datei als veraltet gekennzeichnet; verwenden Sie
`append`, `prepend` und `merge`.

## Verwandte Seiten

- [Foreach-Schleifen](foreach-schleifen.md)
- [Operatorreferenz](operatorreferenz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
