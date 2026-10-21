---
title: "Smarty und die Template-Sprache: die Unterschiede im Detail"
audience: developer, designer, app-creator
category: development
order: 40
summary: Die Unterschiede, über die Smarty-Benutzer stolpern, jeder auf alpha gezeigt - keine Arithmetik oder Vergleiche in Infixschreibweise, kein PHP in Templates, keine automatische Maskierung, unbekannte Operatoren, die ihre Eingabe durchreichen, strenges def und set, Klammern in CSS und JavaScript, Operatoren als registrierte PHP-Klassen, fetch und Objekte, Overrides statt Vererbung, Namensräume und Leerraum.
keywords: Smarty, Unterschiede, Infix, Operatoren, PHP-Tag, Maskierung, wash, def, set, Warnungen, Klammern, CSS, literal, Override, Vererbung, Namensräume, Leerraum, differences, infix, escaping, warnings, braces, inheritance, namespaces, whitespace
type: help
---
# Smarty und die Template-Sprache: die Unterschiede im Detail

[Die Tabelle nebeneinander](smarty-nebeneinander.md) zeigt, wo die beiden Sprachen übereinstimmen. Diese Seite nimmt sich die Stellen vor, an
denen sie es nicht tun, erklärt jede aus dem Code der Template-Engine und zeigt, was auf alpha geschieht, wenn eine Gewohnheit aus Smarty auf
die Template-Sprache trifft.

## Inhalt

1. [Keine Arithmetik in Infixschreibweise](#keine-arithmetik-in-infixschreibweise)
2. [Keine Vergleiche in Infixschreibweise](#keine-vergleiche-in-infixschreibweise)
3. [Operatoren statt Symbole](#operatoren-statt-symbole)
4. [Keine automatische Maskierung](#keine-automatische-maskierung)
5. [Unbekannte Operatoren reichen ihre Eingabe durch](#unbekannte-operatoren-reichen-ihre-eingabe-durch)
6. [def und set sind streng](#def-und-set-sind-streng)
7. [Klammern in CSS und JavaScript](#klammern-in-css-und-javascript)
8. [Kein PHP in Templates](#kein-php-in-templates)
9. [Operatoren und Funktionen sind registrierte PHP-Klassen](#operatoren-und-funktionen-sind-registrierte-php-klassen)
10. [Daten kommen aus fetch, und Werte sind Objekte](#daten-kommen-aus-fetch-und-werte-sind-objekte)
11. [Overrides statt Vererbung](#overrides-statt-vererbung)
12. [Namensräume](#namensräume)
13. [Leerraum um Tags](#leerraum-um-tags)
14. [Verwandte Seiten](#verwandte-seiten)

## Keine Arithmetik in Infixschreibweise

Der Ausdrucksparser (`parseVariableTag()` in `lib/eztemplate/classes/eztemplateelementparser.php`) kennt Werte, Variablen, Zugriffe auf
Attribute (`.` und `[ ]`), Operatoren (`name(` oder `|name`) und sonst nichts. Ein `+` gehört nicht zur Sprache. Er liest die Zahl `1`, findet
Zeichen, mit denen er nichts anfangen kann, protokolliert einen Parserfehler und gibt aus, was er hatte:

```tpl
[{1+2}]
```

Ausgabe auf alpha:

```text
[1]
```

Von der Template-Engine protokolliert:

```text
ERROR : parser error @ tl-diff/01-no-infix-arithmetic.tpl:1[2]
Extra characters found in expression, they will be ignored.
+2' (1)
```

## Keine Vergleiche in Infixschreibweise

Dasselbe gilt für `>`, `<`, `==`, `&&` und `||`. In einem `{if}` hört der Bedingungsparser nach `$a` auf, der Rest wird als ungültig gemeldet,
und die tatsächlich geprüfte Bedingung ist nur `$a`. Mit `$a = 0` ist sie falsch, also gibt das Template `not greater` aus, aber nur zufällig:
Mit `$a = 1` würde dasselbe Tag `greater` ausgeben, obwohl 1 nicht größer als 1 ist.

```tpl
{def $a = 0}
[{if $a > 1}greater{else}not greater{/if}]
```

Ausgabe auf alpha:

```text
[not greater]
```

Von der Template-Engine protokolliert:

```text
ERROR : parser error @ tl-diff/02-no-infix-comparison.tpl:2[7]
Invalid parameter characters in function 'if': '> 1'
```

## Operatoren statt Symbole

Jede Berechnung und jeder Vergleich ist ein Operator, aufgerufen oder über die Pipe: `sum`, `sub`, `mul`, `div`, `mod`, `inc`, `dec`; `eq`,
`ne`, `gt`, `ge`, `lt`, `le`; `and`, `or`, `not`. Sie lassen sich beliebig verschachteln:

```tpl
{def $a = 0}
[{sum( 1, 2 )}] [{1|sum( 2 )}] [{mul( 3, 4 )}] [{10|sub( 4 )}]
[{if $a|gt( 1 )}greater{else}not greater{/if}]
[{if and( $a|ge( 0 ), $a|lt( 10 ), not( $a|eq( 5 ) ) )}in range{/if}]
```

Ausgabe auf alpha:

```text
[3] [3] [12] [6]
[not greater]
[in range]
```

## Keine automatische Maskierung

Die Engine maskiert Ausgaben nie. Was eine Variable enthält, wird so geschrieben, wie es ist; Text aus Inhalten, URLs oder Anfrageparametern
muss also dort, wo er ausgegeben wird, durch `wash` (oder einen anderen Maskierungsoperator) laufen:

```tpl
{def $text = '<script>alert(1)</script> & "quotes"'}
raw:    {$text}
washed: {$text|wash}
```

Ausgabe auf alpha:

```text
raw:    <script>alert(1)</script> & "quotes"
washed: &lt;script&gt;alert(1)&lt;/script&gt; &amp; &quot;quotes&quot;
```

Attribute von Inhaltsobjekten, die bereits HTML sind, etwa die Ausgabe eines Rich-Text-Felds, werden über ihre Ansichts-Templates
(`attribute_view_gui`) ausgegeben und dürfen nicht ein zweites Mal gewaschen werden. Siehe
[Wash und Maskierung](../designer/vorlagen/wash-und-maskierung.md).

## Unbekannte Operatoren reichen ihre Eingabe durch

Smarty kann ein Template, das einen unbekannten Modifikator nutzt, nicht kompilieren. Die Template-Sprache protokolliert eine Warnung
(`Operator '...' is not registered`) und lässt den Wert, wie er war. Ein falsch geschriebener Operator hält die Seite also nicht an; er tut
stillschweigend nichts, lesen Sie deshalb die Debug-Ausgabe:

```tpl
[{'unchanged'|nosuchoperator}]
```

Ausgabe auf alpha:

```text
[unchanged]
```

Von der Template-Engine protokolliert:

```text
WARNING : Operator 'nosuchoperator' is not registered
```

## def und set sind streng

`{def}` legt eine Variable an und warnt, wenn sie schon existiert (es weist den Wert dann trotzdem zu); `{set}` ändert eine Variable und
weigert sich, eine anzulegen. Beide schreiben eine Warnung, keinen Fehler, die Seite wird also weiter gerendert:

```tpl
{def $color = 'red'}
{def $color = 'blue'}
{set $size = 'large'}
[{$color}]
```

Ausgabe auf alpha:

```text
[blue]
```

Von der Template-Engine protokolliert:

```text
WARNING def: Variable 'color' is already defined.
WARNING set: Variable 'size' doesn't exist, cannot set
```

Die Regeln stammen aus `eZTemplateDefFunction::process()` und `eZTemplateSetFunction::process()`; siehe
[Variablen im Detail](variablen-im-detail.md).

## Klammern in CSS und JavaScript

Jede `{` beginnt ein Tag. Eine CSS-Regel oder ein JavaScript-Objekt in einem Template wird als Tag gelesen, das nach dem benannt ist, was auf
die Klammer folgt; das ist ein Fehler, und alles danach geht verloren. Hier verschwindet sogar das Wort `after`:

```tpl
before
.box { color: red; }
after
```

Ausgabe auf alpha:

```text
before
.box
```

Von der Template-Engine protokolliert:

```text
ERROR : parser error @ tl-diff/07-braces-in-text.tpl:2[11]
Invalid parameter characters in function '': ': red; '
WARNING : Function "" is not registered
```

Die Abhilfen sind dieselben wie bei Smarty: `{literal}...{/literal}` um den Block oder `{ldelim}` und `{rdelim}` für einzelne Klammern. In den
Admin-Designs von Exponential gehören Skripte und Styles überhaupt nicht in Templates; sie liegen in Dateien, die über die Skript- und
Stylelisten des Designs geladen werden.

```tpl
before
{literal}.box { color: red; }{/literal}
.box {ldelim} color: red; {rdelim}
after
```

Ausgabe auf alpha:

```text
before
.box { color: red; }
.box { color: red; }after
```

## Kein PHP in Templates

Es gibt kein Tag `{php}`. Der Parser behandelt es als Funktionsnamen, die Engine hat keine solche Funktion, und der Block wird mit einer
Warnung verworfen:

```tpl
[{php}echo 1;{/php}]
```

Ausgabe auf alpha:

```text
[]
```

Von der Template-Engine protokolliert:

```text
WARNING : Function "php" is not registered
```

Logik, die PHP braucht, gehört in einen Operator, eine Funktion oder eine Fetch-Funktion einer Erweiterung.

## Operatoren und Funktionen sind registrierte PHP-Klassen

Ein Plugin von Smarty ist eine PHP-Funktion, die über ihren Dateinamen gefunden wird. Ein Template-Operator ist eine Methode einer Klasse, die
eine Datei `eztemplateautoload.php` aufführt (`$eZTemplateOperatorArray`); die Engine liest diese Listen aus den Pfaden in `site.ini`
[TemplateSettings] `AutoloadPathList` und aus jeder in `ExtensionAutoloadPath` genannten Erweiterung (`eZTemplate::factory()` und
`eZTemplate::autoload()`). Die Klasse wird geladen, wenn einer ihrer Operatoren zum ersten Mal genutzt wird. Template-Funktionen werden auf
dieselbe Weise über `$eZTemplateFunctionArray` registriert. Die beiden werden in [Funktionen und Operatoren](funktionen-und-operatoren.md)
verglichen.

## Daten kommen aus fetch, und Werte sind Objekte

Ein Template von Smarty bekommt meist alle seine Daten von dem PHP, das es rendert. Ein Template von Exponential fragt Daten oft selbst ab:
`fetch( 'content', 'list', hash( ... ) )` führt eine Fetch-Funktion des Moduls `content` mit den Berechtigungen des aktuellen Benutzers aus.
Die Werte sind PHP-Objekte (Knoten, Inhaltsobjekte, Attribute), gelesen über `attribute()`: `$node.data_map.title.content`. Siehe
[Zugriff auf Attribute](zugriff-auf-attribute.md) und [Grundlagen von fetch](../designer/vorlagen/fetch-grundlagen.md).

## Overrides statt Vererbung

Templates von Smarty erweitern einander mit `{extends}` und `{block}`. In Exponential umschließt das Pagelayout jede Seite und gibt das
Ergebnis der Modulansicht aus (`$module_result.content`), und das Override-System (`override.ini`) wählt nach Klasse, Sektion, Knoten,
Elternknoten, Ansichtsmodus und mehr, welches Template eine Ansicht rendert. Teile einer Seite werden mit `include` und den Funktionen
`*_view_gui` geteilt. Siehe [Grundlagen der Overrides](../designer/vorlagen/override-grundlagen.md).

## Namensräume

Variablen leben in Namensräumen. Ein `include` ohne `name` läuft im Namensraum des Aufrufers und sieht alle seine Variablen; mit `name='box'`
läuft es in einem eigenen Namensraum und sieht nur die Parameter, die es bekommen hat. `section` und `let` mit einem `name` legen ebenfalls
Namensräume an. Siehe [Namensräume](namensraeume.md).

## Leerraum um Tags

Der Parser entfernt einen Zeilenumbruch direkt vor und einen direkt nach jedem Funktions-Tag (`{if}`, `{/foreach}`, `{def ...}`), aber nicht
um Ausgabe-Tags wie `{$x}`. Templates können also jede Funktion in eine eigene Zeile setzen, ohne dem HTML Leerzeilen hinzuzufügen. Siehe
[Lexikalische Struktur](lexikalische-struktur.md#leerraum-um-tags).

## Verwandte Seiten

- Zurück: [Smarty und die Template-Sprache nebeneinander](smarty-nebeneinander.md)
- Weiter: [Ein Template von Smarty umziehen](ein-template-von-smarty-umziehen.md)
- [Häufige Fehler in Templates](../designer/vorlagen/haeufige-template-fehler.md)
- [Logik- und Vergleichsoperatoren](../designer/vorlagen/logik-und-vergleichsoperatoren.md)
- [Literal und Begrenzer](../designer/vorlagen/literal-und-begrenzer.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
