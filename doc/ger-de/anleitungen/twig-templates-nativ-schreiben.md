# Twig-Templates für Exponential nativ schreiben

Diese Anleitung ist für Template-Entwickler, die `.twig`-Templates von Hand schreiben, so wie man Twig in Symfony-,
Drupal- oder Craft-Projekten schreibt, statt `.tpl`-Dateien umzuwandeln. Ein umgewandeltes Template sieht aus wie ein
`.tpl` in Twig-Syntax; ein handgeschriebenes kann nutzen, was Twig am besten kann: Template-Vererbung, `embed`, Makros,
Tests, die Operatoren `?:` und `??`, Whitespace-Steuerung. Diese Seite zeigt, wie all das auf die eigene Maschinerie von
Exponential trifft: das Override-System, den Design-Fallback, die Namensräume der Templates, die Sandbox und `wash`.

Jedes Beispiel wurde vom Template-System in einer Entwicklungsinstallation mit den Media-Demo-Inhalten gerendert, im
Siteaccess des Twig-Designs (Knoten 98 ist der Artikel „Why Track Your Workouts?“; `list` = `['a', 'b', 'c']`,
`user_name` = `'Ada'`, `price` = `12.5`, `html` = `'<b>bold</b>'`). Die Ausgabe steht so da, wie sie gedruckt wurde.
Die Layout-, Komponenten- und Makrodateien der Beispiele (`layout/article_base.twig`, `components/card.twig`,
`macros/ui.twig`) waren beim Ausführen Arbeitskopien unterhalb des Installationsverzeichnisses; in einem Design liegen
sie in dessen Verzeichnis `templates/`.

## Zwei Wege, ein Template zu finden

Ein `.twig` trifft auf zwei verschiedene Suchen, und zu wissen, welche gilt, ist der Schlüssel zu allem auf dieser Seite.

| Suche | Benutzt von | Override-Regeln | Design-Fallback | Findet Engines |
|---|---|---|---|---|
| **Die Design-Ressource** | dem Kernel (`content/view` und jedes Modul), `{% expinclude %}`, `node_view_gui`, `attribute_view_gui` und den anderen `*_view_gui`-Funktionen, `{include}` in einem `.tpl` | ja | ja | `.tpl`, `.stpl`, `.twig` |
| **Twigs eigener Loader** | `{% extends %}`, `{% include %}`, `{% embed %}`, `{% import %}`, `{% from %}`, `{% use %}` | nein | ja, nach Verzeichnisreihenfolge | nur `.twig` |

Twigs Loader sucht einen Namen in den Template-Verzeichnissen der Designs des Siteaccess, in der Reihenfolge des
Design-Fallbacks (je Design `override/templates` und `templates`, das erste Design zuerst), und zuletzt unterhalb des
Installationsverzeichnisses. `{% include 'parts/cookie_settings_link.twig' %}` findet also die Datei des ersten Designs,
das `templates/parts/cookie_settings_link.twig` hat, und `'extension/<ext>/design/<design>/templates/x.twig'` bezeichnet
genau eine Datei:

```twig
{% include 'parts/cookie_settings_link.twig' %}
```

```
<a href="/cookie-policy#cookie-settings" class="js-open-ng-cc d-block my-2">Cookie settings</a>
```

**Faustregel:** Nehmen Sie die Design-Ressource (`expinclude`, `*_view_gui`) für alles, was eine Override-Regel ersetzen
können soll, etwa Ansichten von Knoten und Attributen und die Teile, die eine Website anpasst. Nehmen Sie Twigs Loader
für die Bausteine Ihres eigenen Designs: Layouts, die Sie erweitern, Komponenten, die Sie einbetten, Makrodateien.

## Template-Vererbung mit extends und Blöcken

Ein Basis-Template definiert Blöcke; ein Kind erweitert es und füllt die Blöcke, die es ändern will. `parent()` druckt
den Inhalt des Blocks aus der Basis.

`templates/layout/article_base.twig` des Designs:

```twig
<article class="{% block css_class %}article{% endblock %}">
  <h1>{% block title %}{{ node.name|wash }}{% endblock %}</h1>
  {% block body %}<p>No body.</p>{% endblock %}
</article>
```

Die volle Ansicht eines Artikels, über eine Override-Regel gefunden wie jedes `.twig`:

```twig
{% extends 'layout/article_base.twig' %}
{% block css_class %}{{ parent() }} article--featured{% endblock %}
{% block body %}<p>{{ node.data_map|length }} fields</p>{% endblock %}
```

```
<article class="article article--featured">
  <h1>Why Track Your Workouts?</h1>
  <p>18 fields</p></article>
```

Beachten Sie den fehlenden Zeilenumbruch vor `</article>`: Twig entfernt den ersten Zeilenumbruch nach einem Block-Tag,
hier nach `{% endblock %}` in der Basis. Schreiben Sie den Umbruch doppelt oder das schließende Tag in dieselbe Zeile,
wo es darauf ankommt.

### Vererbung zusammen mit Override-Regeln

Das Override-System entscheidet, **welche Datei** eine Anfrage rendert; die Vererbung entscheidet, **wie diese Datei
aufgebaut ist**. Zusammen arbeiten sie so:

1. Eine Override-Regel lenkt die URI des Kernels auf ein `.twig`, zum Beispiel `node/view/full.tpl` für Artikel auf
   `full/article.twig`.
2. `full/article.twig` erweitert ein Basis-Layout über seinen Namen: `{% extends 'layout/article_base.twig' %}`.
3. Twig findet `layout/article_base.twig` im ersten Design des Siteaccess, das es hat. Ein Design weiter oben in der
   Designliste kann das Basis-Layout also für jede Artikelansicht auf einmal ersetzen, ohne Override-Regel, einfach
   durch eine Datei gleichen Namens.

Was nicht geht: Ein Kind kann kein `.tpl` oder `.stpl` erweitern, und ein Name in `{% extends %}` läuft nie durch
Override-Regeln. Soll die Basis selbst von einer Override-Regel gewählt werden, rendern Sie sie mit `{% expinclude %}` und
geben die Teile als Variablen mit, oder nehmen Sie für eine Komponente `embed` (unten).

Das Pagelayout ist ein Template wie jedes andere: `pagelayout.twig` darf ein `layout/base.twig` Ihres Designs erweitern,
und Modulansichten, die in `module_result.content` gerendert sind, bleiben, was sie sind.

## include, embed und expinclude

| | Findet die Datei über | Override-Regeln | Variablen | Schreibt Variablen zurück |
|---|---|---|---|---|
| `{% expinclude uri='design:x.tpl' a=b %}` | die Design-Ressource | ja | den Namensraum und die Parameter | ja, was das eingebundene `.twig` setzt |
| `{% include 'x.twig' with {a: b} %}` | Twigs Loader | nein | eine Kopie des Kontexts, dazu `with` (`only` für keine) | nein |
| `{% embed 'x.twig' %}...{% endembed %}` | Twigs Loader | nein | wie `include` | nein |

`embed` bindet ein Template ein und lässt Sie seine Blöcke an der Stelle des Einbindens überschreiben: eine Komponente
mit Platzhaltern.

`templates/components/card.twig`:

```twig
<div class="card">
  <h2>{% block heading %}Card{% endblock %}</h2>
  <div class="card-body">{% block content %}{% endblock %}</div>
</div>
```

```twig
{% embed 'components/card.twig' %}
  {% block heading %}{{ user_name|wash }}'s list{% endblock %}
  {% block content %}{{ list|join(', ') }}{% endblock %}
{% endembed %}
```

```
<div class="card">
  <h2>Ada's list</h2>
  <div class="card-body">a, b, c</div>
</div>
```

Ein optionaler Teil, der fehlt: `ignore missing` druckt nichts, statt zu scheitern.

```twig
[{% include 'parts/no_such_file.twig' ignore missing %}]
```

```
[]
```

`with {...} only` gibt dem eingebundenen Template nur die Variablen, die Sie aufzählen:

```twig
{% with {greeting: 'Hello'} only %}{{ greeting }} {{ user_name|default('nobody') }}{% endwith %}
```

```
Hello nobody
```

## Makros

Makros sind Twigs wiederverwendbare Schnipsel mit Parametern; ein `.tpl` hat nichts Entsprechendes (am nächsten kommt
ein `{include}` mit Parametern). Legen Sie sie in eine Datei und importieren Sie sie:

`templates/macros/ui.twig`:

```twig
{% macro badge(text, kind = 'info') %}<span class="badge badge-{{ kind }}">{{ text|wash }}</span>{% endmacro %}
{% macro price(amount) %}{{ amount|l10n('currency') }}{% endmacro %}
```

```twig
{% import 'macros/ui.twig' as ui %}
{{ ui.badge('New') }} {{ ui.badge('Sale', 'warning') }} {{ ui.price(price) }}
```

```
<span class="badge badge-info">New</span> <span class="badge badge-warning">Sale</span> $12.50
```

Ein Makro derselben Datei wird über `_self` aufgerufen:

```twig
{% macro star(n) %}{% for i in 1..n %}*{% endfor %}{% endmacro %}
{{ _self.star(3) }}
```

```
***
```

Makros laufen in einem eigenen Gültigkeitsbereich: Sie sehen ihre Parameter, nicht die Variablen des aufrufenden
Templates, und was sie setzen, bleibt drinnen. Die Operatoren von Exponential funktionieren in ihnen wie überall (`l10n`
oben).

## Filter, Funktionen und Tests

Twig kennt drei Arten benannter Dinge, und die Operatoren des Template-Systems sind Teil von zweien:

| Art | Syntax | Der Anteil von Exponential |
|---|---|---|
| Filter | `wert\|name(args)` | jeder Operator, mit dem Wert als Eingabe (`node.name\|wash`) |
| Funktion | `name(args)` | jeder Operator ohne Eingabe (`cond(...)`, `fetch(...)`), jede Template-Funktion ohne Rumpf (`attribute_view_gui({...})`) |
| Test | `wert is name` | nur Twigs eigene: `defined`, `empty`, `null`, `odd`, `even`, `iterable`, `same as`, `divisible by` |

Tests prüft die Sandbox nicht, alle Tests von Twig funktionieren also:

```twig
[{% if missing is defined %}defined{% else %}not defined{% endif %}] [{{ list is empty ? 'empty' : 'not empty' }}] [{% for i in 1..4 %}{{ i is odd ? 'o' : 'e' }}{% endfor %}] [{{ price is same as(12.5) ? 'same' : 'different' }}]
```

```
[not defined] [not empty] [oeoe] [same]
```

Twigs eigene Operatoren funktionieren neben denen von Exponential, Bedingungen und Rechnungen lassen sich also auf
Twig-Art schreiben:

```twig
{% set total = 0 %}{% for n in [2, 3, 4] %}{% set total = total + n %}{% endfor %}{{ total }} {{ 7 // 2 }} {{ 7 % 3 }} {{ 2 ** 10 }} {{ 'a' ~ 'b' }} {{ price > 10 and price < 20 ? 'mid' : 'other' }}
```

```
9 3 1 1024 ab mid
```

Gibt es einen Namen sowohl in Twig als auch als Operator, gehört der einfache Name Twig, und der Operator heißt
`exp_<name>` ([Operators and fetch functions](../../eng-us/guides/twig-templates-operators.md#the-prefix-exp),
Englisch). Achten Sie auf Namen, die gar kein Operator sind: `exp_upper` ist keiner (der Operator heißt `upcase`), er
reicht seine Eingabe also unverändert durch und schreibt `Unknown operator upper` in die Debug-Ausgabe:

```twig
{{ node.name|upcase }} / {{ node.name|exp_upper }}
```

```
WHY TRACK YOUR WORKOUTS? / Why Track Your Workouts?
```

`./console exp:template:check --level=strict` meldet einen solchen Namen als `TWIG-STRICT-UNKNOWN-OPERATOR`, bevor eine
Seite es tut.

## Escaping: wash, Autoescape und |raw

Standardmäßig (`[TwigSettings] Autoescape=disabled`) maskiert ein `.twig` von sich aus nichts, genau wie ein `.tpl`:
Maskieren Sie jeden Wert aus Eingaben oder Inhalten mit `wash` (oder Twigs `escape`, `e`):

```twig
{{ html }}|{{ html|raw }}|{{ html|wash }}|{{ attribute_view_gui({attribute: node.data_map.title})|trim }}
```

```
<b>bold</b>|<b>bold</b>|&lt;b&gt;bold&lt;/b&gt;|Why Track Your Workouts?
```

Mit `Autoescape=enabled` in der `twig.ini.append.php` des Siteaccess maskiert Twig jeden gedruckten Wert, und dasselbe
Template druckt:

```
&lt;b&gt;bold&lt;/b&gt;|<b>bold</b>|&amp;lt;b&amp;gt;bold&amp;lt;/b&amp;gt;|Why Track Your Workouts?
```

Drei Dinge folgen daraus:

- `|raw` ist der Weg, einen Wert zu drucken, der schon HTML ist, sobald Autoescape an ist.
- `|wash` maskiert unter Autoescape **doppelt** (`&amp;lt;`): Nehmen Sie eins von beiden, nicht beides. Ein umgewandeltes
  Design benutzt überall `wash`; lassen Sie Autoescape dafür aus.
- Die Ausgabe der `*_view_gui`-Funktionen wird gedruckt, wie sie ist: Sie rendern Templates, die selbst maskieren.

Für handgeschriebene Designs ist Autoescape eine vernünftige Wahl; schalten Sie es aber je Siteaccess ein und nie für ein
Design, das auch umgewandelte Templates enthält. [Security and escaping](../../eng-us/guides/twig-templates-security.md)
(Englisch) hat die Einzelheiten.

## Whitespace-Steuerung

Twig entfernt den ersten Zeilenumbruch nach einem Tag und sonst nichts; ein `-` im Tag-Begrenzer schneidet allen
Leerraum auf dieser Seite ab:

```twig
A {{- ' B ' -}} C|{{ '  x  '|trim }}|
{%- if true %}
  yes
{%- endif %}|
```

```
A B C|x|  yes|
```

```twig
<ul>
{%- for item in list %}
  <li>{{ item }}</li>
{%- endfor %}
</ul>
```

```
<ul>  <li>a</li>  <li>b</li>  <li>c</li></ul>
```

Der Filter `spaceless` steht standardmäßig nicht in der Liste der Sandbox; schneiden Sie mit `-`, oder erlauben Sie den
Filter für einen Siteaccess, dem Sie vertrauen (`SecurityAllowedFilterList[]=spaceless`).

## Was die Sandbox erlaubt

Die Sandbox (`[TwigSettings] Security=enabled`, der Standard) entscheidet, was ein handgeschriebenes Template vom eigenen
Vokabular Twigs benutzen darf. Alles, was Exponential hinzufügt (Operatoren, Template-Funktionen, die `exp`-Tags), ist
erlaubt. Von Twigs eigenem:

| Standardmäßig erlaubt | Standardmäßig abgelehnt (Beispiele) |
|---|---|
| Tags `if`, `for`, `set`, `apply`, `block`, `extends`, `include`, `embed`, `macro`, `import`, `from`, `use`, `with`, `verbatim`, `guard`, `types` | `do`, `autoescape`, `sandbox`, `flush`, `deprecated` |
| Filter `abs`, `batch`, `capitalize`, `column`, `date`, `default`, `e`, `escape`, `first`, `format`, `join`, `json_encode`, `keys`, `last`, `length`, `lower`, `merge`, `nl2br`, `number_format`, `raw`, `replace`, `reverse`, `round`, `slice`, `sort`, `split`, `striptags`, `title`, `trim`, `upper`, `url_encode` | `filter`, `map`, `reduce`, `spaceless`, `format_*` von Erweiterungen |
| Funktionen `attribute`, `block`, `cycle`, `max`, `min`, `parent`, `random`, `range` | `constant`, `include` (die Funktion; das Tag ist erlaubt), `source`, `dump` |
| jeder Test | — |
| Attribute lesen (`node.name`) | Methodenaufrufe (`node.name()`), Objekteigenschaften |

So sieht ein abgelehntes Konstrukt aus, wenn die Seite rendert, mit eingeschalteter Debug-Ausgabe:

```
<!-- twig: rendering <root>/var/tmp/doc-examples/example.twig failed: Filter &quot;filter&quot; is not allowed in &quot;var/tmp/doc-examples/example.twig&quot; at line 1. (example.twig:1) -->
```

Die Filter mit Pfeilfunktionen (`filter`, `map`, `reduce`) sind abgelehnt, weil ihre Pfeilfunktionen beliebige Ausdrücke
ausführen; nehmen Sie eine `for`-Schleife mit einem `if`, oder erlauben Sie sie für einen Siteaccess, dem Sie vertrauen.
Die Listen stehen in `settings/twig.ini`.

## Welche Variablen ein Template sieht

Ein `.twig` sieht die Variablen des Namensraums, in dem es läuft, wie ein `.tpl`. Welche das sind, hängt davon ab, wer es
rendert:

| Template | Variablen |
|---|---|
| `pagelayout.twig` | `module_result` (mit `content`, `path`, `content_info`, `view_parameters`, `node_id` ...), `site` (Titel, URIs, das Design), `current_user`, `anonymous_user_id`, `access_type` (der Siteaccess: `access_type.name`), `navigation_part`, `uri_string`, `requested_uri_string`, `ui_context`, `ui_component`, `warning_list` |
| eine Knotenansicht (`content/view`) | `node`, `viewmode`, `language_code`, `view_parameters`, `collection_attributes`, `validation`, `persistent_variable` und die Design-Keys; eine Ansicht, die `node_view_gui` rendert, bekommt `node` und die Parameter der Funktion |
| eine Attributansicht (`attribute_view_gui`) | `attribute` und die anderen Parameter der Funktion |
| ein eingebundenes Template | die Parameter des Includes, bei `expinclude` dazu den gemeinsamen Namensraum |
| ein Mail- oder Benachrichtigungs-Template | die Variablen, die das Modul setzt (zum Beispiel `node`, `object`); es setzt `subject` und Ähnliches, damit das Modul es zurückliest |

Die **Design-Keys**, mit denen eine Knotenansicht gewählt wurde, liegen im Namensraum `DesignKeys` (`$DesignKeys:used` in
einem `.tpl`); lesen Sie sie mit `expvar(name, namensraum)`:

```twig
{{ expvar('used', 'DesignKeys').class_identifier }} / {{ expvar('used', 'DesignKeys').viewmode }} / {{ expvar('matched', 'DesignKeys')|keys|join(',') }}
```

```
article / line / class_identifier,viewmode
```

Werte, die sich jedes Template selbst holen kann, in welchem Namensraum es auch läuft:

| Wert | Twig |
|---|---|
| der aktuelle Benutzer | `fetch('user', 'current_user')` (`.login`, `.is_logged_in`, `.contentobject.name`) |
| eine Einstellung | `ezini('SiteSettings', 'SiteName')`, `ezini('TwigSettings', 'Security', 'twig.ini')` |
| der aktuelle Siteaccess | `access_type.name` im Pagelayout; anderswo `ezini('SiteAccessSettings', ...)` |
| ein Knoten | `fetch('content', 'node', {node_id: 2})` |

```twig
{% set user = fetch('user', 'current_user') %}{{ user.login }} {{ user.is_logged_in ? 'signed in' : 'anonymous' }} {{ user.contentobject.name|wash }}
{{ ezini('SiteSettings', 'SiteName') }} / {{ ezini('DesignSettings', 'SiteDesign') }} / {{ ezini('TwigSettings', 'Security', 'twig.ini') }}
```

```
anonymous anonymous Anonymous User
Fit & Healthy / twig_media / enabled
```

Twig hat keine globalen Variablen von Exponential: Alles kommt aus dem Namensraum oder aus einem Fetch, wie in einem
`.tpl`, sodass sich ein Template in jeder Engine und unter Velocitys dauerhaften Workern gleich verhält.

## Variablen für die Seite schreiben

Was ein `.twig` mit `{% set %}` setzt, wird am Ende in seinen Namensraum zurückgeschrieben; eine Knotenansicht kann dem
Pagelayout also einen Wert übergeben wie ein `.tpl`. Für einen anderen Gültigkeitsbereich nehmen Sie `expset` und
`expsetblock`:

```twig
{% expsetblock variable='subject' scope='root' %}New article: {{ node.name|wash }}{% endexpsetblock %}[{{ subject }}]
```

```
[New article: Why Track Your Workouts?]
```

`scope='global'` erreicht das Pagelayout aus jeder Tiefe (die persistente Variable einer Knotenansicht, `cache_ttl`), wie
in [Template functions and tags](../../eng-us/guides/twig-templates-functions.md#the-exp-tags) (Englisch).

## Eine vollständige handgeschriebene volle Ansicht

Zusammengesetzt: eine volle Ansicht, die ein Basis-Layout erweitert, Makros importiert, den Rumpf über das eigene
Template des Attributs rendert (damit seine Override-Regeln gelten) und Geschwister über ihre Zeilenansicht auflistet:

```twig
{% extends 'layout/article_base.twig' %}
{% import 'macros/ui.twig' as ui %}

{% block title %}{{ node.name|wash }} {{ ui.badge(node.object.class_identifier) }}{% endblock %}

{% block body %}
  <p class="meta">{{ node.object.published|l10n('shortdate') }}</p>
  {{ attribute_view_gui({attribute: node.data_map.body}) }}
  {% set siblings = fetch('content', 'list', {parent_node_id: node.parent_node_id, limit: 3}) %}
  {% if siblings is not empty %}
    <ul>
    {%- for child in siblings %}{% if child.node_id != node.node_id %}
      <li>{{ node_view_gui({content_node: child, view: 'line'}) }}</li>
    {%- endif %}{% endfor %}
    </ul>
  {% endif %}
{% endblock %}
```

Twig 3 hat keinen Schleifenfilter `for ... if` (er wurde mit Twig 3.0 entfernt), und der Filter `filter` wird von der
Sandbox abgelehnt; die Bedingung gehört deshalb in die Schleife. Dasselbe Template, gerendert ohne den Rumpf und die
Zeilenansichten, damit die Ausgabe kurz bleibt, druckte:

```
<article class="article">
  <h1>Why Track Your Workouts? <span class="badge badge-info">ng_article</span></h1>
    <p class="meta">07/23/2018</p>
        <ul>      <li>7 Keystone Habits to Maximize Your Mornings</li>      <li>The BiPolar Learning Curve</li>      <li>Split Options Weight Training Program</li>    </ul>
  </article>
```

## Verwandte Seiten

- [Twig-Templates: Rezepte](twig-templates-rezepte.md)
- [Fehlersuche in Twig-Templates](twig-templates-fehlersuche.md)
- [Templates nebeneinander](../hilfe/template-sprache/tpl-twig-smarty-nebeneinander.md)
- [Writing Twig-native templates](../../eng-us/guides/twig-templates-native.md) (Englisch)
- [Von tpl zu twig umziehen](../hilfe/template-sprache/von-tpl-zu-twig-umziehen.md)
