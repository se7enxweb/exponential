---
title: cond und first_set
audience: designer, developer
category: templates
order: 220
summary: Zwei Operatoren, die einen Wert wählen - cond (der erste Wert, dessen Bedingung wahr ist, wie eine Kette ternärer Ausdrücke) und first_set (der erste Wert, der gesetzt ist) - mit typischen Verwendungen für Klassen, Standardwerte und Rückfälle.
keywords: cond, first_set, ternär, bedingter Wert, Standardwert, Rückfall, Wert wählen, Inline-If, ternary, conditional value, default value, fallback, choose value, inline if
type: help
---
# cond und first_set

Diese Seite ist für Designer, die einen Wert brauchen, der von einer Bedingung abhängt, ohne einen `{if}`-Block. Beide
Operatoren stammen aus `eZTemplateControlOperator` (`lib/eztemplate/classes/eztemplatecontroloperator.php`).

## Inhalt

1. [cond](#cond)
2. [first_set](#first_set)
3. [Welcher](#welcher)

## cond

`cond( condition1, value1, condition2, value2, ..., default )` gibt den Wert nach der ersten wahren Bedingung zurück, oder
das letzte Argument, wenn keine wahr ist:

```tpl
<body class="{cond( $current_user.is_logged_in, 'signed-in', 'anonymous' )}">

{def $badge = cond( $node.is_hidden, 'Hidden'|i18n( 'design/mytheme' ),
                    $node.object.published|gt( $recent ), 'New'|i18n( 'design/mytheme' ),
                    '' )}
```

Das Pagelayout des Designs simple verwendet `cond( $current_user.is_logged_in, fetch( shop, basket ).is_empty, 1 )`, um
den Warenkorb nur für angemeldete Benutzer zu lesen.

## first_set

`first_set( $a, $b, ..., fallback )` gibt das erste Argument zurück, das gesetzt (definiert) ist, egal welchen Wert es hat:

```tpl
{def $offset = first_set( $view_parameters.offset, 0 )}
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Die Pagelayouts der Administration verwenden `first_set( $site.text_direction, 'ltr' )`, damit eine Seite, die von einem
älteren Kernel ohne diese Variable dargestellt wird, von links nach rechts bleibt.

## Welcher

| Sie wollen | Verwenden Sie |
|---|---|
| einen Wert abhängig von einer Bedingung | `cond` |
| einen Wert, der fehlen kann, mit einem Standardwert | `first_set` |
| den ersten nicht leeren Text | `cond( $a\|trim\|ne( '' ), $a, $b )` (`first_set` gäbe ein leeres, aber gesetztes `$a` zurück) |

## Verwandte Seiten

- [If und Bedingungen](if-und-bedingungen.md)
- [Logik- und Vergleichsoperatoren](logik-und-vergleichsoperatoren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
