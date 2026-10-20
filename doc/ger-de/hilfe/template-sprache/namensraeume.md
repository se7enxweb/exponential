---
title: Namensräume
audience: developer, designer, app-creator
category: development
order: 90
summary: Wie die Template-Engine Variablen auseinanderhält - der Wurzel- und der aktuelle Namensraum, die vier Arten, eine Variable zu benennen ($var, $ns:var, $:var, $#var), was include mit und ohne name= tut, die Namensräume, die section und let anlegen, und der Fehler bei einer Variablen im falschen Namensraum.
keywords: Namensräume, Wurzel-Namensraum, aktueller Namensraum, relativ, global, $:var, $#var, include name, section name, let name, Gültigkeitsbereich von Variablen, eZTemplate, namespaces, root namespace, current namespace, relative, variable scope
type: help
---
# Namensräume

Jede Template-Variable lebt in einem Namensraum, einem Namen wie `''` (die oberste Ebene der Seite), `box` oder `Outer:Inner`. Die Engine
speichert Variablen als `$this->Variables[<namespace>][<name>]` (`lib/eztemplate/classes/eztemplate.php`), und jedes Template wird mit zwei
Namensräumen zur Hand verarbeitet: dem **Wurzel-Namensraum** der Datei und dem **aktuellen Namensraum**, den ein `section` oder `let` mit
einem Namen innerhalb der Datei ändern kann. Namensräume sorgen dafür, dass die Variablen eingebundener Templates und verschachtelter
Schleifen einander nicht überschreiben.

## Inhalt

1. [Vier Arten, eine Variable zu benennen](#vier-arten-eine-variable-zu-benennen)
2. [include ohne und mit Namen](#include-ohne-und-mit-namen)
3. [Namensräume von section und let](#namensräume-von-section-und-let)
4. [Eine Variable im falschen Namensraum](#eine-variable-im-falschen-namensraum)
5. [Regeln für die Praxis](#regeln-für-die-praxis)
6. [Verwandte Seiten](#verwandte-seiten)

## Vier Arten, eine Variable zu benennen

Der Variablenparser (`variableEndPos()` in `lib/eztemplate/classes/eztemplateelementparser.php`) liest ein wahlweise vorhandenes Zeichen für
den Gültigkeitsbereich, beliebig viele Präfixe `name:` und den Variablennamen. `elementValue()` in `eztemplate.php` findet dann den
Namensraum:

| Geschrieben | Gültigkeitsbereich | Durchsuchter Namensraum |
|---|---|---|
| `$title` | lokal | der Wurzel-Namensraum der Datei |
| `$box:title` | lokal | Wurzel-Namensraum + `:box` (oder `box` ganz oben) |
| `$:title` | relativ | der aktuelle Namensraum |
| `$#title` | global | genau der geschriebene Namensraum, hier `''`, die oberste Ebene der Seite |
| `$#box:title` | global | genau `box` |

Auf der obersten Ebene einer Seite sind all das derselbe Namensraum `''`. Sie unterscheiden sich in eingebundenen Templates, Sections und
`let`-Blöcken.

## include ohne und mit Namen

`eZTemplateIncludeFunction::process()` entscheidet über den Namensraum der eingebundenen Datei:

- **Ohne `name`** läuft die Datei im aktuellen Namensraum des Aufrufers. Sie sieht jede Variable des Aufrufers, und die Parameter, die sie
  bekommt, werden für die Dauer des include in diesem Namensraum gesetzt.
- **Mit `name='box'`** läuft die Datei im Namensraum `box` (oder `<current>:box`). Ihr `$title` bedeutet `box:title`, sie sieht also nur ihre
  Parameter; die Variablen des Aufrufers bleiben mit `$#name` erreichbar.

Diese Datei wird unten zweimal eingebunden:

```tpl
title=[{$title}] local=[{if is_set( $node )}{$node.name}{else}no $node{/if}] global=[{$#node.name}] relative=[{$:title}]
```

Ohne Namen sieht sie `$node` des Aufrufers:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
{include uri='file:var/tmp/examples/tl-ns/parts/show.tpl' title='A'}
```

Ausgabe auf alpha:

```text
title=[A] local=[Fit & Healthy] global=[Fit & Healthy] relative=[A]
```

Mit `name='box'` fehlt das lokale `$node`, das globale `$#node.name` funktioniert weiterhin, und der Parameter `$title` wird nach dem include
wieder entfernt:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
{include uri='file:var/tmp/examples/tl-ns/parts/show.tpl' title='B' name='box'}
after: [{if is_set( $title )}title leaked{else}no $title here{/if}]
```

Ausgabe auf alpha:

```text
title=[B] local=[no $node] global=[Fit & Healthy] relative=[B]
after: [no $title here]
```

Beide Formen kommen in ausgelieferten Designs vor. Ohne Namen ist bequem, bindet den Teil aber an seine Aufrufer; ein Name macht den Teil
eigenständig.

## Namensräume von section und let

`section` mit `name=` verarbeitet seinen Rumpf im Namensraum `<current>:<name>` und setzt dort `item`, `key`, `index`, `number` (und
`sequence`) (`eZTemplateSectionFunction::process()`). Verschachtelte Sections verschachteln die Namen, und `$:item` meint immer die innerste:

```tpl
{section name=Outer loop=array( 'x', 'y' )}{section name=Inner loop=array( 1, 2 )}[{$Outer:item}{$Outer:Inner:item}|{$:item}]{/section}{/section}
```

Ausgabe auf alpha:

```text
[x1|1][x2|2][y1|1][y2|2]
```

`let` und `default` mit `name=` tun dasselbe für ihre Variablen:

```tpl
{let name=Box width=3}[{$Box:width}] [{$:width}]{/let}
```

Ausgabe auf alpha:

```text
[3] [3]
```

`foreach`, `for`, `while` und `def` legen keine Namensräume an; ihre Variablen sind gewöhnliche Variablen der Datei.

## Eine Variable im falschen Namensraum

Ein fehlgeschlagener Zugriff nennt den durchsuchten Namensraum; daran sehen Sie, ob die Variable fehlt oder nur außer Reichweite ist:

```tpl
{def $title = 'root'}
[{$Box:title}]
```

Ausgabe auf alpha:

```text
[]
```

Von der Template-Engine protokolliert:

```text
ERROR : Unknown template variable 'title' in namespace 'Box'
```

## Regeln für die Praxis

- Nutzen Sie in neuen Templates `def` und `foreach`; sie brauchen überhaupt keine Namensräume.
- Geben Sie einem wiederverwendbaren Teil beim Einbinden einen `name`, und übergeben Sie alles, was er braucht, als Parameter.
- Greifen Sie zu `$#var` nur, um seitenweite Werte aus einem benannten Teil heraus zu lesen; um Werte nach oben zu geben, siehe
  [set mit einem Gültigkeitsbereich](variablen-im-detail.md#set-mit-einem-gültigkeitsbereich).
- Beim Lesen alter Templates ist `$Name:item` ein Namensraum von `section` oder `let`: siehe [Sections und foreach](sections-und-foreach.md).

## Verwandte Seiten

- Zurück: [Variablen im Detail](variablen-im-detail.md)
- Weiter: [Zugriff auf Attribute](zugriff-auf-attribute.md)
- [Include](../../../eng-us/help/designers/templates/include.md)
- [Section (veraltet)](../../../eng-us/help/designers/templates/section-legacy.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
