# Ein erstes .twig, Schritt für Schritt

Dieses Tutorial ist für Template-Entwickler, die `.tpl`-Templates (oder Twig in einem anderen Projekt) geschrieben haben
und eine erste Exponential-Seite in Twig möchten. Es baut die Vollansicht eines Artikels als `.twig`: eine
Override-Regel, die Variablen des Knotens, Operatoren, eine Übersetzung, ein Fetch, eine Schleife und ein Include. Jede
Ausgabe unten wurde vom Template-System in einer Entwicklungsinstallation mit dem Media-Demo-Inhalt gedruckt (Knoten 98
ist der Artikel "Why Track Your Workouts?").

## 1. Prüfen, ob Twig da ist

```bash
./console exp:template:engines
```

```
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
AVAILABLE    twig (Twig templates)  Twig 3.30.0
PASS 3 engines available
```

Ist `twig` nicht verfügbar, siehe [Twig installieren](../funktionen/6.0/twig-templates.md#twig-installieren).

## 2. Eine Override-Regel, die auf ein .twig zeigt

Im Design Ihres Siteaccess, `settings/siteaccess/<siteaccess>/override.ini.append.php`:

```ini
[full_article_twig]
Source=node/view/full.tpl
MatchFile=full/article.twig
Subdir=templates
Match[class_identifier]=article
```

und die Datei `extension/<ext>/design/<design>/override/templates/full/article.twig`. Leeren Sie den Override-Cache
einmal (`./console exp:ezcache --clear-tag=template`); danach wird ein geändertes `.twig` bei der nächsten Anfrage
übernommen.

## 3. Der Knoten und seine Attribute

```twig
<h1>{{ node.name|wash }}</h1>
<p>{{ node.data_map|length }} attributes; title: {{ node.data_map.title.content|wash }}</p>
```

```
<h1>Why Track Your Workouts?</h1>
<p>18 attributes; title: Why Track Your Workouts?</p>
```

`node` ist derselbe `eZContentObjectTreeNode`, den ein `.tpl` als `$node` bekommt; `.` liest ein Attribut, wie in einem
`.tpl`.

## 4. Operatoren

Jeder Template-Operator funktioniert, als Filter (mit Eingabe) oder als Funktion (ohne Eingabe):

```twig
{{ node.url_alias|ezurl }}
{{ node.object.published|l10n('shortdate') }}
{{ 'Close'|i18n('design/standard/content') }}
```

```
"/fitness/why-track-your-workouts"
07/23/2018
Close
```

Denken Sie an `wash`: Nichts wird maskiert, solange Sie es nicht sagen, wie in einem `.tpl`. Mit `html` gesetzt auf
`<b>bold</b>`:

```twig
{{ html }} / {{ html|wash }}
```

```
<b>bold</b> / &lt;b&gt;bold&lt;/b&gt;
```

## 5. Ein Fetch und eine Schleife

```twig
{% set children = fetch('content', 'list', {parent_node_id: node.parent_node_id, limit: 3}) %}
{{ children|length }} children: {% for child in children %}{{ child.name|wash }}; {% endfor %}
```

```
3 children: 7 Keystone Habits to Maximize Your Mornings; The BiPolar Learning Curve; Split Options Weight Training Program;
```

In einer echten Liste rendern Sie jedes Kind mit seiner Zeilenansicht:

```twig
{% for child in children %}
    {{ node_view_gui({content_node: child, view: 'line'}) }}
{% endfor %}
```

## 6. Eine Bedingung

```twig
{{ cond(price|gt(10), 'expensive', 'cheap') }}
{% if price|gt(10) %}expensive{% else %}cheap{% endif %}
```

Mit `price` 12.5 drucken beide `expensive`. Die Operatoren des `.tpl` (`gt`, `and`, `cond`) und Twigs eigene Operatoren
(`>`, `and`, `? :`) funktionieren beide; ein umgewandeltes Template benutzt die Operatoren, sodass seine Ergebnisse genau
denen des `.tpl` entsprechen.

## 7. Ein Include

```twig
{% expinclude uri='design:parts/related.tpl' node=node %}
```

`expinclude` ist das `{include}` des Template-Systems: Es findet das Template über die Design-Ressource, Override-Regeln
und Fallback eingeschlossen, gleich welche Engine es hat. Parameter werden Variablen des eingebundenen Templates.

## 8. Prüfen

```bash
./console exp:template:check extension/<ext>/design/<design>/override/templates/full/article.twig
```

Ein Tippfehler wie `|wahs` ist in Twig ein Kompilierfehler (`Unknown "wahs" filter`), den die Prüfung mit Datei und
Zeile meldet, bevor ein Besucher die Seite sieht.

## Wie es weitergeht

- [.twig-Templates schreiben und ausführen](twig-templates.md): wie alles funktioniert
- [Operatoren und Fetch-Funktionen](twig-templates-operatoren.md)
- [Der Konverter und die Portierung eines Designs](twig-templates-konverter.md): ein ganzes `.tpl`-Design umwandeln,
  statt es von Hand zu schreiben
