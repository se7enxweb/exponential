---
title: Logik- und Vergleichsoperatoren
audience: designer, developer
category: templates
order: 200
summary: Die Operatoren, aus denen Bedingungen gebaut werden - eq, ne, lt, gt, le, ge, and, or, not, true, false, null, choose und die Typprüfungen is_set, is_unset, is_array, is_string, is_numeric, is_object, is_null, get_type, get_class.
keywords: eq, ne, lt, gt, le, ge, and, or, not, true, false, null, choose, is_set, is_unset, is_array, is_string, is_numeric, is_object, is_null, get_type, get_class, Vergleich, Logik, comparison, logic
type: help
---
# Logik- und Vergleichsoperatoren

Diese Seite ist eine Referenz für Bedingungen in Templates. Logikoperatoren stammen aus `eZTemplateLogicOperator`,
Typprüfungen aus `eZTemplateTypeOperator` (`lib/eztemplate/classes/`).

## Inhalt

1. [Vergleich](#vergleich)
2. [Logik](#logik)
3. [Konstanten](#konstanten)
4. [Typprüfungen](#typprüfungen)
5. [choose](#choose)

## Vergleich

| Operator | Bedeutet |
|---|---|
| `eq( $a, $b )` | gleich (auch `$a\|eq( $b )`) |
| `ne( $a, $b )` | ungleich |
| `lt`, `le` | kleiner als, kleiner oder gleich |
| `gt`, `ge` | größer als, größer oder gleich |

`eq` mit mehr als zwei Werten ist wahr, wenn alle gleich sind; der Vergleich ist locker (`0` ist gleich `false()`).

> **Hinweis**
> `lt`, `le`, `gt` und `ge` vergleichen **Anzahlen**, wie der Kommentar der Klasse sie definiert: Eine Zahl ist ihr Wert, ein
> Array die Zahl seiner Einträge, ein Objekt die Zahl seiner Attribute, ein boolescher Wert 0 oder 1 und jede andere
> Zeichenkette ihre **Länge**. `$children|gt( 3 )` bedeutet also „mehr als drei Kinder“, und `'10'|gt( 9 )` vergleicht Zahlen,
> aber eine nicht numerische Zeichenkette wird nach Länge verglichen. Vergleichen Sie Datumsangaben als Zeitstempel (Zahlen).

## Logik

```tpl
{if and( $node.can_edit, $current_user.is_logged_in )} ... {/if}
{if or( $a, $b, $c )} ... {/if}
{if $node.is_hidden|not} ... {/if}
```

`and` gibt den letzten wahren Wert zurück (oder falsch), `or` den ersten wahren Wert, sodass `or( $title, $name )` auch als
„der erste nicht leere“ funktioniert (aber [first_set](cond-und-first-set.md) prüft „gesetzt“ statt „wahr“).

## Konstanten

`true()` und `false()` erzeugen boolesche Werte. `null` ist eine Prüfung: `$value|null` ist wahr, wenn der Wert null ist.

## Typprüfungen

| Operator | Wahr, wenn der Wert |
|---|---|
| `is_set( $x )` / `is_unset( $x )` | definiert ist / nicht ist |
| `is_array`, `is_string`, `is_integer`, `is_float`, `is_numeric`, `is_boolean` | diesen Typ hat |
| `is_object`, `is_class( 'eZContentObjectTreeNode' )` | ein Objekt ist, dieser Klasse |
| `is_null` | null ist |

`get_type` und `get_class` geben den Typ und den Klassennamen zurück, nützlich beim Debuggen.

## choose

`$index|choose( 'zero', 'one', 'two' )` gibt den Parameter an der Position der Eingabe zurück: eine kompakte Art, eine kleine
Zahl einem Text zuzuordnen. Für wahr/falsch ist `cond()` klarer.

## Verwandte Seiten

- [If und Bedingungen](if-und-bedingungen.md)
- [cond und first_set](cond-und-first-set.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
