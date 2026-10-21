---
title: Steueroperatoren
audience: developer, designer, app-creator
category: development
order: 130
summary: cond und first_set aus eZTemplateControlOperator - Bedingungen und Ersatzwerte innerhalb eines Ausdrucks, die nur auswerten, was sie brauchen, wie sich Interpreter und kompiliertes Template bei einem falschen Standardwert unterscheiden, mit Beispielen, die auf alpha gerendert wurden.
keywords: cond, first_set, Bedingung, ternärer Operator, Ersatzwert, Standardwert, condition, ternary, fallback, default value, eZTemplateControlOperator
type: help
---
# Steueroperatoren

`cond` und `first_set` sind die Antwort der Template-Sprache auf einen ternären Ausdruck (`a ? b : c`) und einen
Null-Koaleszenz-Ausdruck (`a ?? b`). Beide werden von `eZTemplateControlOperator`
(`lib/eztemplate/classes/eztemplatecontroloperator.php`) über `lib/eztemplate/classes/eztemplateautoload.php`
registriert. Keiner der beiden nimmt eine gepipte Eingabe oder benannte Parameter entgegen; beide werten ihre
Parameter verzögert von links nach rechts aus und hören auf, sobald sie eine Antwort haben. Der Compiler macht aus
beiden verschachtelte PHP-`if`-Blöcke (`condTransform()`), sodass sie in einem kompilierten Template keinen
Operatoraufruf kosten.

## Inhalt

1. [cond](#cond)
2. [first_set](#first_set)
3. [Verwandte Seiten](#verwandte-seiten)

## cond

### cond

Nimmt Parameterpaare aus Bedingung und Wert entgegen. Gibt den Wert des ersten Paares zurück, dessen Bedingung wahr
ist. Ein ungerader letzter Parameter ist der Standardwert, der verwendet wird, wenn keine Bedingung zutraf. Mit einem
einzigen Parameter gibt er diesen Parameter zurück.

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| Paare aus Bedingung und Wert | mixed | ja | keiner |
| Standardwert (ungerader letzter Parameter) | mixed | nein | keiner |

Eingabe: keine. Gibt den gewählten Wert zurück oder nichts, wenn keine Bedingung zutraf und es keinen Standardwert
gibt. Generierte Referenz: [cond](../../../eng-us/examples/template-operators/cond.md).

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 144 ) )}
{cond( $node.children_count|gt( 10 ), 'many', $node.children_count|gt( 0 ), 'some', 'none' )} children ({$node.children_count})
{cond( false(), 'never', 'the default' )}
{cond( 'only one parameter' )}
```

Ausgabe auf alpha:

```text
many children (18)
the default
only one parameter
```

Eine häufige Verwendung ist ein Klassenattribut in einer Schleife, hier bei den Kindern der Startseite von
Fit & Healthy (Knoten 89):

```tpl
{foreach fetch( 'content', 'list', hash( 'parent_node_id', 89, 'limit', 4, 'sort_by', array( 'priority', true() ) ) ) as $i => $child}
<li class="{cond( $i|eq( 0 ), 'first', $child.class_identifier|eq( 'ng_topic' ), 'topic', 'item' )}">{$child.name|wash}</li>
{/foreach}
```

Ausgabe auf alpha:

```text
<li class="first">Fitness</li><li class="topic">Workout</li><li class="item">Fit &amp; Healthy site info - [SITEINFO]</li><li class="item">Tour</li>
```

**Ein Standardwert, der selbst falsch ist.** Der Interpreter (`modify()`) wertet den Standardwert wie eine Bedingung
aus und verwendet ihn nur, wenn er wahr ist; ein Standardwert `0`, `''` oder `false()` ergibt also nichts. Die
kompilierte Form weist den Standardwert zu, ohne ihn zu prüfen. Das folgende Beispiel wurde vom Interpreter gerendert:

```tpl
[{cond( false(), 'x', 0 )}]
```

Ausgabe auf alpha:

```text
[]
```

Kompiliert auf alpha, wo das Kompilieren von Templates aktiviert ist, gab dieselbe Zeile `[0]` aus.

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
[0]
```

Für ein verlässliches `0` prüfen Sie den Fall ausdrücklich: `cond( $x, 'x', true(), 0 )`.

## first_set

### first_set

Gibt den ersten Parameter zurück, der gesetzt ist, also nicht `null`. Nicht definierte Variablen und fehlende
Hash-Schlüssel sind hier erlaubt und protokollieren nichts (der Interpreter fragt sie mit der Existenzprüfung von
`elementValue()` ab, der kompilierte Code verwendet `isset()`). Ist keiner gesetzt, gibt er `null` zurück. Ein
konstanter Parameter beendet im kompilierten Code die Suche; setzen Sie den literalen Ersatzwert daher an das Ende.

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| Kandidaten | mixed | ja | keiner |

Eingabe: keine. Gibt den ersten Wert zurück, der nicht `null` ist. Beachten Sie, dass `0`, `''` und `false` als
gesetzt gelten. Generierte Referenz: [first_set](../../../eng-us/examples/template-operators/first_set.md).

```tpl
{def $view_parameters = hash( 'offset', 20 )}
offset = {first_set( $view_parameters.offset, 0 )}
limit = {first_set( $view_parameters.limit, $undefined_limit, 10 )}
title = {first_set( $no_such_variable, 'Untitled' )}
```

Ausgabe auf alpha:

```text
offset = 20
limit = 10
title = Untitled
```

So liest man üblicherweise einen optionalen Parameter eines eingebundenen Templates oder einen optionalen
Ansichtsparameter wie `$view_parameters.offset`.

## Verwandte Seiten

- [cond und first_set für Designer](../../../eng-us/help/designers/templates/cond-and-first-set.md)
- [Logische Operatoren](logische-operatoren.md): `choose`, `and`, `or`
- [Variablen und def](../../../eng-us/help/designers/templates/variables-and-def.md): `default` für optionale Parameter von include
- [Alle Template-Operatoren (generiert)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
