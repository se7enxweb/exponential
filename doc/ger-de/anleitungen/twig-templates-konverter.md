# Twig-Templates: der Konverter und die Portierung eines Designs

Diese Anleitung ist für Site-Bauer und Template-Entwickler, die `.tpl`-Templates (oder `.stpl`) in `.twig` umwandeln
oder `.twig` zurück in `.tpl`. Sie zeigt die Befehle, was der Konverter für jedes Konstrukt schreibt, wie er von Hand
geänderte Dateien schützt und welche Hinweise er für das gibt, was kein genaues Gegenstück hat. Alle Richtungen und der
allgemeine Befehl stehen in [Templates zwischen Template-Engines umwandeln](template-konvertierung.md); die Ausgaben unten
wurden von den Befehlen in einer Entwicklungsinstallation gedruckt.

## Die Befehle

| Befehl | Wandelt um |
|---|---|
| `exp:template:convert:tpl-to-twig` | `.tpl` in `.twig` |
| `exp:template:convert:twig-to-tpl` | `.twig` (und `.html.twig`) in `.tpl` |
| `exp:template:convert:stpl-to-twig` | `.stpl` in `.twig` (über `.tpl`) |
| `exp:template:convert:twig-to-stpl` | `.twig` in `.stpl` (über `.tpl`) |
| `exp:template:convert --from-engine=<e> --to-engine=<e>` | jede Richtung |
| `exp:template:convert:diff --file=<file> --to-engine=<e>` | druckt eine Umwandlung, schreibt nichts |

Optionen der Richtungsbefehle: `--from=<design dir>` (jedes Template darunter), `--to=<design dir>` (Voreinstellung:
neben den Quellen), `--file=<one file>`, `--dry-run`, `--force`, `--override-ini=<file>` mit `-s <siteaccess>` (schreibt
die Override-Regeln, mit denen dieser Siteaccess die umgewandelten Dateien benutzt), `--twig-suffix=.html.twig`.

## Ein Design, umgewandelt

`templates/list.tpl` eines kleinen Designs:

```
{def $items = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 3 ) )}
<ul>
{foreach $items as $child}
    <li>{$child.name|wash}</li>
{/foreach}
</ul>
{if $items|count|gt( 2 )}{'More'|i18n( 'design/standard/content' )}{/if}
{include uri='design:parts/footer.tpl'}
{undef $items}
```

```bash
./console exp:template:convert:tpl-to-twig --from=<design> --to=<design>_twig
```

```
WRITE <design>_twig/templates/list.twig
WRITE <design>_twig/templates/parts/footer.twig
PASS tpl->twig 2 templates: 2 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

`templates/list.twig`:

```twig
{% set items = fetch('content', 'list', {'parent_node_id': node.node_id, 'limit': 3}) %}
<ul>{% for child in items %}
    <li>{{ child.name|wash }}</li>{% endfor %}
</ul>{% if (items|count)|gt(2) %}
{{ 'More'|i18n('design/standard/content') }}{% endif %}
{% expinclude uri='design:parts/footer.tpl' %}
{% set items = null %}
{# exp:template:convert tpl->twig md5:543e672301da17d7166e2c1ac83c7696 from templates/list.tpl -- generated; a file changed by hand is not replaced without --force #}
```

Die Zeilenumbrüche sind so gesetzt, dass das `.twig` genau das druckt, was das `.tpl` druckt: Twig lässt den
Zeilenumbruch nach einem Tag weg, das Template-System nicht. Das Include behält die `.tpl`-URI: Es geht über die
Design-Ressource und findet das `.twig` (oder das `.tpl`), wie es die Override-Regeln sagen.

## Was der Konverter schreibt

| `.tpl` | `.twig` |
|---|---|
| `{$x}`, `{$x\|op( a )}` | `{{ x }}`, `{{ x\|op(a) }}` |
| `{op( a, b )}` | `{{ op(a, b) }}` |
| `{def $x = 1}`, `{set $x = 2}` | `{% set x = 1 %}`, `{% set x = 2 %}` |
| `{undef $x}` | `{% set x = null %}` |
| `{if}`, `{elseif}`, `{else}` | `{% if %}`, `{% elseif %}`, `{% else %}` |
| `{foreach $l as $k => $v}` | `{% for k, v in l %}` (mit `offset=`, `max=`, `reverse` über `expslice()`) |
| `{delimiter}` | `{% if not loop.first %}` vor dem Element |
| `{section loop=$l var=v}` | `{% for key, v in expsectionloop(l, 0, null, false, true) %}` |
| `{for 1 to 5 as $i}` | `{% for i in exprange(1, 5) %}` |
| `{while}`, `{do}` | `{% expwhile %}` |
| `{break}`, `{continue}`, `{skip}` | Flags, die den Rest der Runde beenden oder überspringen |
| `{switch}`, `{case}` | eine `{% if %}`-Kette |
| `{include uri=...}` | `{% expinclude uri=... %}` |
| `{cache-block}`, `{set-block}`, `{append-block}`, `{run-once}` | `{% expcacheblock %}`, `{% set %}...{% endset %}` / `{% expsetblock %}`, `{% expappendblock %}`, `{% exprunonce %}` |
| `{attribute_view_gui attribute=$a}` | `{{ attribute_view_gui({attribute: a}) }}` |
| eine Blockfunktion | `{% name ... %}...{% endname %}` |
| `hash( 'a', 1 )`, `array( 1, 2 )` | `{'a': 1}`, `[1, 2]` |
| ein Operator, dessen Namen Twig selbst hat (`merge`, `trim`, `round`, `and` ...) | `exp_<name>` |

Eine Variable, die zum ersten Mal innerhalb einer Schleife gesetzt wird, wird vor der Schleife deklariert
(`{% set x = x ?? null %}`), sodass sie nach der Schleife noch gesetzt ist, wie in einem `.tpl`.

## Zurück zu .tpl

`exp:template:convert:twig-to-tpl` liest das `.twig` so, wie es geschrieben ist (Twigs Parser, ohne die Änderungen, die
für das Ausführen gemacht werden), und schreibt das `.tpl`:

```
{def $items = fetch('content', 'list', hash('parent_node_id', $node.node_id, 'limit', 3))}<ul>{foreach $items as $child}    <li>{$child.name|wash}</li>{/foreach}</ul>{if $items|count|gt(2)}{'More'|i18n('design/standard/content')}{/if}{include uri='design:parts/footer.tpl'}{undef $items}
```

Ein umgewandeltes Template kommt als dasselbe Template zurück (die Tests wandeln jeden Fall der eigenen Tests der
Template-Engine hin und zurück und vergleichen die Ausgabe). Was keine Form in einem `.tpl` hat, wird als Kommentar und
als NOTE behalten:

```bash
./console exp:template:convert:diff --file=macro.twig --to-engine=tpl
```

```
{* TWIG (not converted: the Twig node MacroDeclarationNode has no template form) line 1 *}{$user_name|upcase}
NOTE macro.twig: {% macros %} have no template form
NOTE macro.twig: line 1: the Twig node MacroDeclarationNode has no template form
```

## Von Hand geänderte Dateien

Jede geschriebene Datei endet mit einem Markierungskommentar, der die md5 des umgewandelten Texts enthält. Ein zweiter
Lauf schreibt nur, was sich geändert hat, und lässt ein Ziel in Ruhe, das keine Markierung hat oder nach dem Schreiben
geändert wurde:

```
SKIP templates/parts/footer.tpl: <design>_twig/templates/parts/footer.twig has no conversion marker (written by hand); --force replaces it
PASS tpl->twig 2 templates: 0 written, 1 unchanged, 1 skipped, 0 errors; 0 notes in 0 templates (a skipped file was changed or written by hand: --force replaces it)
```

Mit `--override-ini` zeigt die Override-Regel eines übersprungenen Templates weiterhin auf es, sodass ein von Hand
fertiggestelltes `.twig` in Gebrauch bleibt.

## Das Ergebnis prüfen

Jedes `.twig` wird mit Twig kompiliert, bevor es geschrieben wird; ein Template, das nicht kompiliert, ist ein ERROR und
wird nicht geschrieben. Prüfen Sie danach das ganze umgewandelte Design mit der strengen Stufe:

```bash
./console exp:template:check --design-dir=<design>_twig --level=strict
```

Vergleichen Sie dann Seiten: Rendern Sie dieselbe Seite aus dem `.tpl`-Siteaccess und aus dem `.twig`-Siteaccess und
vergleichen Sie das HTML ([Ein Design migrieren](twig-templates-migrieren.md)).

## Siehe auch

- [Templates zwischen Template-Engines umwandeln](template-konvertierung.md)
- [Ein Design migrieren](twig-templates-migrieren.md)
- [Template-Syntax prüfen](template-syntaxpruefung.md)
