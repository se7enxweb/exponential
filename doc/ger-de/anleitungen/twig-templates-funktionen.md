# Twig-Templates: Template-Funktionen und Tags

Diese Anleitung ist für Template-Entwickler, die Template-Funktionen von Exponential aus einem `.twig` aufrufen: die
`*_view_gui`-Funktionen, die Funktionen mit Rumpf (Blockfunktionen) und die `exp*`-Tags, die für die eigenen Konstrukte
des `.tpl` stehen (`{include}`, `{set-block}`, `{cache-block}`, `{run-once}`). Sie sagt auch, welche von Twigs eigenen
Tags ein Template benutzen darf. Die Beispiele wurden vom Template-System in einer Entwicklungsinstallation gerendert
(Knoten 98 ist ein Artikel, `user_name` ist `'Ada'`).

## Funktionen ohne Rumpf: Twig-Funktionen mit einem Hash

Jede Template-Funktion ohne Rumpf ist eine Twig-Funktion, die einen Hash benannter Parameter nimmt, die
`name=value`-Paare des `.tpl`:

| `.tpl` | `.twig` |
|---|---|
| `{attribute_view_gui attribute=$node.data_map.title}` | `{{ attribute_view_gui({attribute: node.data_map.title}) }}` |
| `{node_view_gui content_node=$child view='line'}` | `{{ node_view_gui({content_node: child, view: 'line'}) }}` |
| `{content_view_gui content_object=$object view='embed'}` | `{{ content_view_gui({content_object: object, view: 'embed'}) }}` |

```twig
{{ attribute_view_gui({attribute: node.data_map.title}) }}
```

```
Why Track Your Workouts?
```

Die Funktion läuft im Template-System genau wie in einem `.tpl`: Sie findet das Template des Datentyps oder der Ansicht
über die Design-Ressource, sodass ihre Override-Regeln gelten und sie ein `.tpl`, `.stpl` oder `.twig` rendern kann.
`[TwigSettings] FunctionList[]` listet die `*_view_gui`-Funktionen; jede andere Funktion ohne Rumpf wird trotzdem
überbrückt.

## Funktionen mit Rumpf: Tags

Eine Template-Funktion mit Rumpf (eine Blockfunktion des Kernels oder einer Extension) ist ein Twig-Tag gleichen Namens
mit `end<name>`:

```twig
{% explblock name='content' %}
    ...
{% endexplblock %}
```

Die Parameter werden wie im `.tpl` geschrieben, `name=value`, die Werte in Twig-Syntax.

## Die exp-Tags

| `.tpl` | `.twig` | Was es tut |
|---|---|---|
| `{include uri='design:parts/x.tpl' a=$b}` | `{% expinclude uri='design:parts/x.tpl' a=b %}` | bindet über die Design-Ressource ein, gleich mit welcher Engine die gefundene Datei arbeitet; Parameter werden Variablen des eingebundenen Templates |
| `{set-block variable=x}...{/set-block}` | `{% set x %}...{% endset %}` | der Rumpf als Variable (Twigs eigenes Erfassen) |
| `{set-block variable=x scope=global}...{/set-block}` | `{% expsetblock variable='x' scope='global' %}...{% endexpsetblock %}` | der Rumpf als Variable eines anderen Geltungsbereichs |
| `{append-block variable=x}...{/append-block}` | `{% expappendblock variable='x' %}...{% endexpappendblock %}` | hängt den Rumpf an die Liste `x` an |
| `{cache-block keys=...}...{/cache-block}` | `{% expcacheblock keys=[...] %}...{% endexpcacheblock %}` | der Cache-Block des Template-Systems ([Caching](twig-templates-caching.md)) |
| `{run-once}...{/run-once}` | `{% exprunonce %}...{% endexprunonce %}` | der Rumpf einmal je Seite |
| `{set scope=global x=2}` | `{% expset name='x' value=2 scope='global' %}` | setzt eine Variable in einem anderen Geltungsbereich |
| `{while ...}...{/while}`, `{do}...{/do}` | `{% expwhile ... %}...{% endexpwhile %}` | eine Schleife mit Bedingung, höchstens `[TwigSettings] WhileLoopLimit` Runden |
| `{undef}` (ohne Namen) | `{% expundefall %}` | entfernt jede Variable, die das Template gesetzt hat |

```twig
{% expappendblock variable='more' %}a{% endexpappendblock %}{% expappendblock variable='more' %}b{% endexpappendblock %}{{ more|implode(',') }}
{% expsetblock variable='greeting' scope='global' %}Hello {{ user_name|wash }}{% endexpsetblock %}[{{ expvar('greeting') }}]
{% for i in [1, 2, 3] %}{% exprunonce %}once {% endexprunonce %}{{ i }} {% endfor %}
```

```
a,b
[Hello Ada]
once 1 2 3
```

## Includes zwischen Engines

- Ein `.twig` bindet jedes Template über die Design-Ressource ein: `{% expinclude uri='design:parts/menu.tpl' %}`. Hat
  eine Override-Regel oder das Design ein `.twig` oder `.stpl` für diese URI, rendert diese Datei.
- Ein `.tpl` bindet ein `.twig` mit `{include uri='design:parts/menu.twig'}` ein, oder über eine `.tpl`-URI, die eine
  Override-Regel auf ein `.twig` richtet.
- Ein eingebundenes `.twig` im gemeinsamen Namensraum schreibt die Variablen zurück, die es setzt, wie es ein
  Smarty-Untertemplate tut.

Twigs eigenes `{% include 'file.twig' %}` und `{% embed %}` funktionieren für `.twig`-Dateien über ihren Pfad unterhalb
der Template-Verzeichnisse des Designs; sie wenden keine Override-Regeln an.

## Twigs eigene Tags

Die Sandbox (standardmäßig an) erlaubt diese von Twigs Tags: `if`, `for`, `set`, `apply`, `block`, `extends`,
`include`, `embed`, `macro`, `import`, `from`, `use`, `with`, `verbatim`, `guard`, `types` und jedes `exp`-Tag.
`[TwigSettings] SecurityAllowedTagList[]` ändert die Liste; ein Tag außerhalb davon meldet
`exp:template:check --level=strict` (`TWIG-SECURITY-001`), und es wird beim Kompilieren des Templates abgelehnt.

## Siehe auch

- [Operatoren und Fetch-Funktionen](twig-templates-operatoren.md)
- [Beispiele für Template-Funktionen](../beispiele/template-funktionen/README.md)
- [Operatoren und Funktionen schreiben](twig-templates-plugins.md)
