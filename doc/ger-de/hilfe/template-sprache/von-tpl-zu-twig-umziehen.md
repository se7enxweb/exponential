---
title: Von Exponential-Templates (.tpl) zu Twig-Templates (.twig) umziehen
audience: developer, designer, app-creator
category: templates
order: 56
summary: Exponential rendert Twig-3-Templates (.twig, .html.twig) selbst, neben .tpl und .stpl - wann Sie .twig wählen, was sich ändert und was bleibt (Operatoren als Filter und Funktionen, fetch, Template-Funktionen, Includes, Caching, die Sandbox, Escaping), eine Tabelle nebeneinander, der Konverter in beide Richtungen mit echter Ausgabe, und die Prüfung des Ergebnisses.
keywords: Twig, twig, html.twig, tpl, umwandeln, Konverter, exp:template:convert:tpl-to-twig, exp:template:convert:twig-to-tpl, portieren, migrieren, expinclude, expcacheblock, exp_-Präfix, Sandbox, wash, convert, converter, port, migrate
type: help
---
# Von Exponential-Templates (.tpl) zu Twig-Templates (.twig) umziehen

Ein Design kann Twig-3-Templates (`.twig`, und `.html.twig` für Dateien, die es mit Projekten im Symfony-Stil teilt)
neben seinen `.tpl`- und `.stpl`-Templates enthalten, und der Kernel rendert sie mit jedem Operator, jeder
Fetch-Funktion, jeder Template-Funktion, jeder Override-Regel und jedem Cache des Template-Systems. Dieses Kapitel ist
für Entwickler und Designer, die `.tpl`-Templates haben und überlegen, sie in `.twig` zu schreiben oder umzuwandeln,
oder die `.twig` haben und wieder `.tpl` brauchen. Die Beispiele wurden auf einer Entwicklungsinstallation gerendert
und die Befehle dort ausgeführt; die Ausgabe steht so da, wie sie ausgegeben wurde.

## Wann Sie .twig wählen, und wann Sie bei .tpl bleiben

Wählen Sie `.twig`, wenn die Menschen, die die Templates schreiben, Twig kennen (Symfony, Drupal, Craft), wenn
Templates mit einem solchen Projekt geteilt werden, oder wenn Sie die Editor-Unterstützung von Twig, Vererbung von
Templates und Makros wollen. Bleiben Sie bei `.tpl` für ein Design, das funktioniert, und ein Team, das es kennt: jedes
mitgelieferte Design, jedes Beispiel und die Templates jeder Erweiterung sind `.tpl`. Die Geschwindigkeit entscheidet
es nicht ([Die drei Template-Engines im Vergleich](../../../eng-us/guides/template-engines.md)).

## Was gleich bleibt

- **Templates finden**: die Design-Ressource, Override-Regeln (`MatchFile=full/article.twig`), der Design-Fallback.
- **Variablen**: `node`, `view_parameters`, `module_result` ... mit denselben Attributen (`node.data_map.title.content`).
- **Jeder Operator und jede Fetch-Funktion**, mit dem eigenen Code und den Rechten des Template-Systems.
- **Jede Template-Funktion**: `attribute_view_gui`, `node_view_gui` und die anderen.
- **Caches**: View-Cache, Cache-Blöcke, HTTP- und Antwort-Cache; die Debug-Leiste.
- **Escaping**: nichts wird maskiert, wenn das Template es nicht sagt; `wash` maskiert.

## Was sich ändert

| `.tpl` | `.twig` |
|---|---|
| `{$node.name\|wash}` | `{{ node.name\|wash }}` |
| `{def $x = 1}` / `{set $x = 2}` / `{undef $x}` | `{% set x = 1 %}` / `{% set x = 2 %}` / `{% set x = null %}` |
| `{if $a}...{elseif $b}...{else}...{/if}` | `{% if a %}...{% elseif b %}...{% else %}...{% endif %}` |
| `{foreach $list as $item}...{/foreach}` | `{% for item in list %}...{% endfor %}` |
| `{delimiter}, {/delimiter}` | `{% if not loop.first %}, {% endif %}` |
| `{$list\|count}` | `{{ list\|count }}` (oder Twigs `{{ list\|length }}`) |
| `{and( $a, $b )}` | `{{ exp_and(a, b) }}` (oder Twigs `{{ a and b }}`) |
| `hash( 'a', 1 )`, `array( 1, 2 )` | `{a: 1}`, `[1, 2]` |
| `fetch( 'content', 'list', hash( 'parent_node_id', 2 ) )` | `fetch('content', 'list', {parent_node_id: 2})` |
| `{attribute_view_gui attribute=$a}` | `{{ attribute_view_gui({attribute: a}) }}` |
| `{include uri='design:parts/x.tpl' a=$b}` | `{% expinclude uri='design:parts/x.tpl' a=b %}` |
| `{cache-block keys=$node.node_id}...{/cache-block}` | `{% expcacheblock keys=[node.node_id] %}...{% endexpcacheblock %}` |
| `{* Kommentar *}` | `{# Kommentar #}` |

Drei Dinge sollten Sie wissen:

- **Operatoren sind Filter und Funktionen.** `x|op(a)` ist `{$x|op( a )}`, `op(a)` ist `{op( a )}`. Wo Twig einen
  Filter oder eine Funktion gleichen Namens hat (`merge`, `round`, `trim`, `replace` ...) oder der Name eines von Twigs
  Wörtern ist (`and`, `or`, `not`, `true`, `false`, `null`), heißt der Operator `exp_<Name>`.
- **Die Sandbox ist an.** Twigs eigene Tags, Filter und Funktionen funktionieren, wie `twig.ini` sie aufführt;
  Methodenaufrufe an Objekten werden abgewiesen. Lesen Sie Werte über Attribute, wie in einer `.tpl`.
- **Kein `{break}` und `{continue}`** in Twig: der Konverter schreibt sie als Merker; von Hand filtern Sie die
  Schleife oder nutzen `{% if %}`.

Ein kleines Template nebeneinander; beide geben für `list` = `['a', 'b', 'c']` `1:a 2:b 3:c ` aus:

```
{foreach $list as $i => $item}{$i|inc}:{$item} {/foreach}
```

```twig
{% for item in list %}{{ loop.index }}:{{ item }} {% endfor %}
```

## Umwandeln

```bash
./console exp:template:convert:tpl-to-twig --from=<design> --to=<design>_twig
./console exp:template:convert:twig-to-tpl --from=<design>_twig --to=<design>_tpl
./console exp:template:convert:diff --file=<design>/templates/list.tpl --to-engine=twig
```

Für das Template

```
{def $items = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 3 ) )}
<ul>
{foreach $items as $child}
    <li>{$child.name|wash}</li>
{/foreach}
</ul>
{if $items|count|gt( 2 )}{'More'|i18n( 'design/standard/content' )}{/if}
```

schreibt der Konverter

```twig
{% set items = fetch('content', 'list', {'parent_node_id': node.node_id, 'limit': 3}) %}
<ul>{% for child in items %}
    <li>{{ child.name|wash }}</li>{% endfor %}
</ul>{% if (items|count)|gt(2) %}
{{ 'More'|i18n('design/standard/content') }}{% endif %}
```

und gibt `WRITE` für jede Datei aus und `PASS tpl->twig 2 templates: 2 written, 0 unchanged, 0 skipped, 0 errors; 0
notes in 0 templates`. Jede `.twig` wird kompiliert, bevor sie geschrieben wird, jede Datei endet mit einem
Markierungskommentar, und eine von Hand geänderte Datei wird ohne `--force` nicht ersetzt. `--override-ini=<Datei>`
mit `-s <Siteaccess>` schreibt die Override-Regeln, mit denen ein Siteaccess die umgewandelten Dateien verwendet. Die
Einzelheiten stehen in [The converter and porting a design](../../../eng-us/guides/twig-templates-converter.md).

## Prüfen

```bash
./console exp:template:check --design-dir=<design>_twig --level=strict
```

kompiliert jede `.twig` und meldet auf der Stufe strict unbekannte Operatoren, Includes, die sich nicht auflösen, und
was die Sandbox abweisen würde ([Template-Syntax prüfen](../../anleitungen/template-syntaxpruefung.md)).

## Wie es weitergeht

- [Zwischen Template-Engines umwandeln](zwischen-template-engines-umwandeln.md)
- [Von Exponential-Templates (.tpl) zu Exponential-Smarty-Templates (.stpl) umziehen](von-tpl-zu-stpl-umziehen.md)
- [Twig templates](../../../eng-us/features/6.0/twig-templates.md) (Englisch)
- [Templates nebeneinander](tpl-twig-smarty-nebeneinander.md), [Twig-Templates nativ schreiben](../../anleitungen/twig-templates-nativ-schreiben.md), [Rezepte](../../anleitungen/twig-templates-rezepte.md) und [die Geschwindigkeit der drei Engines](../../anleitungen/template-engines-geschwindigkeit.md)
