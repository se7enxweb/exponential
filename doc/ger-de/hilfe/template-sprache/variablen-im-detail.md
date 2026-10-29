---
title: Variablen im Detail
audience: developer, designer, app-creator
category: development
order: 80
summary: Die genauen Regeln von def, undef, set, let und default aus eZTemplateDefFunction und eZTemplateSetFunction - die Warnungen bei doppelter Definition, beim Setzen einer unbekannten Variablen und beim Aufheben einer, die def nicht angelegt hat, undef ohne Argumente, die Lebensdauer einer Variablen (die Datei, der let-Block) und set mit scope=relative, root oder global über ein include hinweg.
keywords: Variablen, def, undef, set, let, default, Gültigkeitsbereich, scope, relative, root, global, Lebensdauer, Warnungen, lokale Variablen, include, variables, lifetime, warnings, local variables
type: help
---
# Variablen im Detail

[Variablen und def](../designer/vorlagen/variablen-und-def.md) zeigt, wie Sie Variablen nutzen. Diese Seite gibt die genauen Regeln, gelesen
aus `lib/eztemplate/classes/eztemplatedeffunction.php` und `lib/eztemplate/classes/eztemplatesetfunction.php`, und zeigt jeden Fall auf einer Entwicklungsinstallation,
einschließlich der Warnungen, die die Engine schreibt.

## Inhalt

1. [Die fünf Funktionen](#die-fünf-funktionen)
2. [def, set, undef](#def-set-undef)
3. [def auf eine vorhandene Variable](#def-auf-eine-vorhandene-variable)
4. [set auf eine nicht vorhandene Variable](#set-auf-eine-nicht-vorhandene-variable)
5. [undef mit und ohne Namen](#undef-mit-und-ohne-namen)
6. [let und default: Variablen für einen Block](#let-und-default-variablen-für-einen-block)
7. [Lebensdauer: Eine Variable gehört zu ihrer Datei](#lebensdauer-eine-variable-gehört-zu-ihrer-datei)
8. [set mit einem Gültigkeitsbereich](#set-mit-einem-gültigkeitsbereich)
9. [Verwandte Seiten](#verwandte-seiten)

## Die fünf Funktionen

| Funktion | Klasse | Legt an | Ändert | Hinweise |
|---|---|---|---|---|
| `def` | `eZTemplateDefFunction` | ja | nur mit einer Warnung | die Variable lebt bis zum Ende der Datei oder bis `undef` |
| `undef` | `eZTemplateDefFunction` | | entfernt | nur mit `def` angelegte Variablen; ohne Namen: alle |
| `set` | `eZTemplateSetFunction` | nein, warnt | ja | nimmt `scope` und `name` |
| `let` | `eZTemplateSetFunction` | ja, für seinen Block | | entfernt bei `{/let}`, was es angelegt hat |
| `default` | `eZTemplateSetFunction` | nur wenn fehlend, für seinen Block | | behält einen vorhandenen Wert |

Das Dollarzeichen vor einem Namen ist bei allen wahlweise (`{def x=1}` funktioniert); der Parser überspringt es.

## def, set, undef

Mehrere Variablen lassen sich in einem Tag behandeln:

```tpl
{def $count = 1 $label = 'items'}
[{$count} {$label}]
{set $count = $count|inc $label = 'things'}
[{$count} {$label}]
{undef $label}
[{if is_set( $label )}label still set{else}label removed{/if}] [{$count}]
```

Ausgabe:

```text
[1 items][2 things][label removed] [2]
```

## def auf eine vorhandene Variable

`process()` prüft zuerst `hasVariable()`. Existiert die Variable, schreibt es die Warnung `Variable '...' is already defined.` und weist den
neuen Wert trotzdem zu:

```tpl
{def $color = 'red'}
{def $color = 'blue'}
[{$color}]
```

Ausgabe:

```text
[blue]
```

Von der Template-Engine protokolliert:

```text
WARNING def: Variable 'color' is already defined.
```

Ein wiederholtes `{def}` ist also nicht fatal, aber die Warnung bedeutet, dass das Template zwei Definitionen hat, wo es eine wollte, oder dass
eine Variable aus einem einbindenden Template hereinsickert.

## set auf eine nicht vorhandene Variable

`set` ändert nur vorhandene Variablen. Bei einem unbekannten Namen schreibt es `Variable '...' doesn't exist, cannot set` und tut nichts:

```tpl
{set $size = 'large'}
[{if is_set( $size )}size exists{else}size was not created{/if}]
```

Ausgabe:

```text
[size was not created]
```

Von der Template-Engine protokolliert:

```text
WARNING set: Variable 'size' doesn't exist, cannot set
```

## undef mit und ohne Namen

`{undef $a $b}` entfernt die genannten Variablen, aber nur die, die in der aktuellen Datei mit `{def}` angelegt wurden (`hasLocalVariable()`);
bei jedem anderen Namen warnt es `Variable '...' is not defined with {def}.` und lässt sie stehen. Seit der im Changelog vermerkten Korrektur
in 3.8 kann `undef` keine Variablen entfernen, die es nicht angelegt hat. Ohne Namen entfernt `{undef}` jede Variable, die die aktuelle Datei
mit `def` definiert hat (`unsetLocalVariables()`):

```tpl
{def $a = 1 $b = 2 $c = 3}
{undef}
[{if is_set( $a )}a{/if}{if is_set( $b )}b{/if}{if is_set( $c )}c{/if}] all removed
```

Ausgabe:

```text
[] all removed
```

```tpl
{undef $never_defined}
done
```

Ausgabe:

```text
done
```

Von der Template-Engine protokolliert:

```text
WARNING undef: Variable 'never_defined' is not defined with {def}.
```

## let und default: Variablen für einen Block

`let` und `default` sind Block-Funktionen (`hasChildren()` gibt für sie true zurück). Sie definieren ihre Variablen, verarbeiten die Kinder
und entfernen dann nur, was sie definiert haben (`cleanupVariables()`). `default` definiert eine Variable nur, wenn sie fehlt, was es zum
klassischen Weg machte, einem eingebundenen Template wahlweise Parameter zu geben; `first_set` tut dasselbe in einem Ausdruck.

```tpl
{let greeting='hi'}inside let: [{$greeting}]{/let}
after let: [{if is_set( $greeting )}still set{else}gone{/if}]
{def $size = 'given'}
{default size='fallback' color='fallback'}[{$size}] [{$color}]{/default}
```

Ausgabe:

```text
inside let: [hi]after let: [gone][given] [fallback]
```

## Lebensdauer: Eine Variable gehört zu ihrer Datei

Jede Template-Datei öffnet ihre eigene Liste lokaler Variablen (`createLocalVariablesList()`), und wenn die Datei endet, werden die Variablen,
die sie mit `def` angelegt hat, entfernt. Eine in einem eingebundenen Template definierte Variable ist nach dem `{include}` also verschwunden:

```tpl
{def $inner = 'made in the part'}
part: [{$inner}]
```

```tpl
{include uri='file:var/tmp/examples/tl-vars/parts/maker.tpl'}
after: [{if is_set( $inner )}inner visible{else}inner gone after the include{/if}]
```

Ausgabe:

```text
part: [made in the part]
after: [inner gone after the include]
```

An `{include}` übergebene Parameter werden gesetzt, bevor die Datei läuft, und danach entfernt (oder auf ihren alten Wert zurückgesetzt), von
`eZTemplateIncludeFunction::process()`.

## set mit einem Gültigkeitsbereich

`set` (sowie `let` und `default`) akzeptieren `scope=relative`, `scope=root` oder `scope=global` und `name=`, um einen Namensraum zu wählen.
Ohne Dollarzeichen geschrieben, sind `scope` und `name` keine Variablen, sondern diese beiden Optionen (der Parser benennt sie in `-scope` und
`-name` um).

| Gültigkeitsbereich | Genutzter Namensraum | Typischer Einsatz |
|---|---|---|
| `relative` (Standard) | der aktuelle Namensraum | gewöhnliche Zuweisung |
| `root` | der Wurzel-Namensraum der aktuellen Template-Datei | einen Wert der Datei aus einem `section` oder `let` heraus ändern |
| `global` | der leere Namensraum, die oberste Ebene der Seite | einen Wert aus einem mit `name=` eingebundenen Template zurückgeben |

Die eingebundene Datei unten läuft in ihrem eigenen Namensraum (`name='part'`), sieht also `$total` des Aufrufers nicht, aber
`set scope=global` ändert die Variable des Aufrufers:

```tpl
{def $inner = 'made in the part'}
part sees: [{if is_set( $total )}{$total}{else}no $total{/if}]
{set scope=global total=10}
```

```tpl
{def $total = 1}
{include uri='file:var/tmp/examples/tl-vars/parts/counter.tpl' name='part'}
after include: [{$total}] [{if is_set( $inner )}inner visible{else}inner not visible{/if}]
```

Ausgabe:

```text
part sees: [no $total]after include: [10] [inner not visible]
```

Setzen Sie das sparsam ein: Ein Template, das die Variablen seines Aufrufers ändert, ist schwer nachzuvollziehen. Um Werte an das Pagelayout
zu übergeben, gibt es die persistente Variable ([Die persistente Variable](../designer/vorlagen/die-persistent-variable.md)).

## Verwandte Seiten

- Zurück: [Werte und Literale](werte-und-literale.md)
- Weiter: [Namensräume](namensraeume.md)
- [Variablen und def](../designer/vorlagen/variablen-und-def.md)
- [cond und first_set](../designer/vorlagen/cond-und-first-set.md)
- [def](../../beispiele/template-funktionen/def.md), [set](../../beispiele/template-funktionen/set.md), [undef](../../beispiele/template-funktionen/undef.md), [let](../../beispiele/template-funktionen/let.md), [default](../../beispiele/template-funktionen/default.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
