# Twig-Templates: Rezepte

Diese Seite ist für Template-Entwickler, die einen funktionierenden Ausgangspunkt für die Templates wollen, die eine
Website am häufigsten braucht, in Twig für Exponential geschrieben: ein Pagelayout, eine Zeilenansicht, ein Block von
Exponential Layouts, ein Mail-Template, Cache-Blöcke je Rolle und je Benutzer, ein Formular mit dem Formular-Token, Texte mit
einer Anzahl und Bilder in einem Bild-Alias. Jedes Rezept sagt, wohin die Datei gehört, zeigt das Template und zeigt,
was es in einer Entwicklungsinstallation mit den Media-Demo-Inhalten druckte (Knoten 98 ist der Artikel „Why Track Your
Workouts?“). Hostnamen in Ausgaben stehen als `www.example.com` da.

Wo ein Rezept Variablen braucht, die nur eine echte Anfrage hat (das `module_result` einer Seite, den `block` eines
Layouts), lief das Beispiel mit einem dafür eingerichteten Wert; das Rezept sagt es.

## Ein Pagelayout

**Datei**: `override/templates/pagelayout.twig` Ihres Designs. Der Kernel fragt die Design-Ressource nach
`pagelayout.tpl`, wie nach jedem Template; eine Override-Regel lenkt diese Anfrage auf das `.twig`:

```ini
# settings/siteaccess/<siteaccess>/override.ini.append.php
[pagelayout_twig]
Source=pagelayout.tpl
MatchFile=pagelayout.twig
Subdir=templates
```

`MatchFile` ist relativ zum Verzeichnis `override/templates/` des Designs. Der Konverter, der die umgewandelten Dateien an
den Stellen der `.tpl`-Dateien ablegt, schreibt für eine Datei in `templates/` `MatchFile=../../templates/pagelayout.twig`;
beide Formen funktionieren.

**Template**:

```twig
{%- set title = module_result.path|last.text -%}
<!DOCTYPE html>
<html lang="{{ ezini('RegionalSettings', 'Locale')|replace({'eng-US': 'en'}) }}">
<head>
  <meta charset="utf-8">
  <title>{{ title|wash }} - {{ ezini('SiteSettings', 'SiteName')|wash }}</title>
  <link rel="stylesheet" href={{ 'stylesheets/core.css'|ezdesign }}>
</head>
<body>
  <nav aria-label="breadcrumb"><ol>
  {%- for item in module_result.path %}
    <li>{% if item.url %}<a href={{ item.url|ezurl }}>{{ item.text|wash }}</a>{% else %}{{ item.text|wash }}{% endif %}</li>
  {%- endfor %}
  </ol></nav>
  <main>{{ module_result.content }}</main>
  <footer>{{ current_user.is_logged_in ? current_user.login|wash : 'Not signed in'|i18n('design/standard/pagelayout') }}</footer>
</body>
</html>
```

**Ausgabe** (gelaufen mit einem `module_result` aus drei Pfadeinträgen und dem Rumpf `<p>The page body.</p>`):

```
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Why Track Your Workouts? - Fit &amp; Healthy</title>
  <link rel="stylesheet" href="/extension/ezwebin/design/ezwebin/stylesheets/core.css">
</head>
<body>
  <nav aria-label="breadcrumb"><ol>    <li><a href="/">Home</a></li>    <li><a href="/fitness">Fitness</a></li>    <li>Why Track Your Workouts?</li>  </ol></nav>
  <main><p>The page body.</p></main>
  <footer>Not signed in</footer>
</body>
</html>
```

Hinweise:

- `module_result.content` ist die gerenderte Modulansicht: HTML, gedruckt, wie es ist. Nie `wash` darauf anwenden.
- `ezurl` und `ezdesign` drucken die Adresse mit Anführungszeichen (`href="/fitness"`), das Attribut wird also ohne sie
  geschrieben: `href={{ ... }}`. Nehmen Sie `ezurl('no')`, wo Sie die Adresse ohne Anführungszeichen brauchen.
- `{%- ... -%}` hält den Doctype in der ersten Zeile: Ein Pagelayout darf nicht mit einer Leerzeile beginnen.
- Für eine schnelle Seite halten Sie die ganze Seite in einem Cache-Block und füllen die eigenen Teile des Besuchers
  danach ein; das Muster steht in
  [Caching](../../eng-us/guides/twig-templates-caching.md#one-block-for-the-whole-page-a-fresh-user-menu) (Englisch).

## Eine Zeilenansicht

**Datei**: `override/templates/line/article.twig`, mit der Regel

```ini
[line_article_twig]
Source=node/view/line.tpl
MatchFile=line/article.twig
Subdir=templates
Match[class_identifier]=ng_article
```

**Template**:

```twig
<article class="line line-{{ node.class_identifier }}">
  {% set image = node.data_map.image %}
  {% if image is defined and image.has_content %}
  <a href={{ node.url_alias|ezurl }}><img src={{ image.content.small.url|ezroot }} width="{{ image.content.small.width }}" height="{{ image.content.small.height }}" alt=""></a>
  {% endif %}
  <h3><a href={{ node.url_alias|ezurl }}>{{ node.name|wash }}</a></h3>
  <p class="date">{{ node.object.published|l10n('shortdate') }}</p>
  {% if node.data_map.teaser_intro.has_content %}{{ attribute_view_gui({attribute: node.data_map.teaser_intro}) }}{% endif %}
</article>
```

**Ausgabe** für Knoten 98 (sein Teaser ist leer):

```
<article class="line line-ng_article">
      <a href="/fitness/why-track-your-workouts"><img src="/var/site/storage/images/fit-healthy/fitness/why-track-your-workouts/615-1-eng-US/why-track-your-workouts_small.jpg" width="100" height="66" alt=""></a>
    <h3><a href="/fitness/why-track-your-workouts">Why Track Your Workouts?</a></h3>
  <p class="date">07/23/2018</p>
  </article>
```

Eine Liste rendert jedes Kind damit: `{{ node_view_gui({content_node: child, view: 'line'}) }}`. Das Bild ist neben einem
verlinkten Titel dekorativ, daher `alt=""`.

## Ein Block von Exponential Layouts

**Datei**: das Ansichts-Template des Blocks in Ihrem Design, zum Beispiel `templates/explayouts/block/button.twig`. Die
Layouts-Extension fragt die Design-Ressource nach `explayouts/block/button.tpl`; eine Override-Regel lenkt das auf das
`.twig`, wie bei jedem Template (der Konverter schreibt diese Regeln mit `--override-ini`):

```ini
[twig_explayouts_block_button]
Source=explayouts/block/button.tpl
MatchFile=../../templates/explayouts/block/button.twig
Subdir=templates
```

Die Einstellungen des Blocks stehen in `block.values`; das Template unten ist der Button-Block des Media-Designs, von Hand
geschrieben:

```twig
{% if block.values.text is not empty %}
  {% if block.values.link is not empty %}
<a href={{ block.values.link|ezurl }} class="{{ block.values.class|wash }}">{{ block.values.text|wash }}</a>
  {% else %}
<span class="{{ block.values.class|wash }}">{{ block.values.text|wash }}</span>
  {% endif %}
{% endif %}
```

**Ausgabe**, gelaufen mit `block.values` = `{text: 'Read more', link: '/fitness', class: 'btn btn-primary'}`:

```
  <a href="/fitness" class="btn btn-primary">Read more</a>
```

Die umgewandelten Block-Templates des Media-Designs zeigen die weiteren Variablen, die ein Block-Template bekommt
(`block`, das `layout` der Zone, die Operatoren `layout_title` und ähnliche); öffnen Sie das `.twig` neben seinem `.tpl`,
um sie zu sehen.

## Ein Mail-Template

**Datei**: das Template, das ein Modul für die Mail rendert, zum Beispiel ein Benachrichtigungs-Template Ihres Designs.
Ein Mail-Template wird gerendert wie ein Seitenteil, und das Modul liest danach Werte daraus zurück: den Betreff, den
Inhaltstyp, manchmal den Absender. In einem `.tpl` geschieht das mit `{set-block scope=root variable=subject}`; in einem
`.twig` reicht ein schlichtes `{% set %}`, weil das, was ein `.twig` setzt, am Ende in seinen Namensraum zurückgeschrieben
wird.

```twig
{% set subject %}New on {{ ezini('SiteSettings', 'SiteName') }}: {{ node.name|wash }}{% endset %}
{% set content_type = 'text/html' %}
<p>{{ 'Hello'|i18n('design/standard/notification') }},</p>
<p><a href="{{ node.url_alias|ezurl('no', 'full') }}">{{ node.name|wash }}</a></p>
```

**Ausgabe**, und was der Aufrufer danach aus dem Template zurückgelesen hat:

```
<p>Hello,</p>
<p><a href="http://www.example.com/fitness/why-track-your-workouts">Why Track Your Workouts?</a></p>
```

```
$subject = 'New on Fit & Healthy: Why Track Your Workouts?'
$content_type = 'text/html'
```

Hinweise:

- `ezurl('no', 'full')`: Eine Mail braucht absolute Adressen; der Host kommt aus `[SiteSettings] SiteURL`.
- Mail-Programme ignorieren Stylesheets: Halten Sie Stile im Markup. Das ist der eine Ort, an den Inline-Stile gehören
  (die Regel gegen `style=""` gilt für die Admin-Designs).
- Der Betreff ist Text, kein HTML: Maskieren Sie ihn nicht für HTML; die Mail-Bibliothek kodiert ihn.

## Cache-Blöcke je Rolle und je Benutzer

Ein Cache-Block wird je Wert seiner Schlüssel aufbewahrt. Mit den Rollen des Besuchers als Schlüssel bedient eine Kopie
jeden Besucher mit denselben Rechten; mit dem Benutzer als Schlüssel bekommt jeder Benutzer eine eigene Kopie:

```twig
{% set user = fetch('user', 'current_user') %}
{% expcacheblock keys=[node.node_id, user.role_id_list|implode(','), user.limited_assignment_value_list|implode(',')] expiry=3600 %}
<p>Shown to every visitor with the same roles: {{ node.name|wash }}</p>
{% endexpcacheblock %}
{% expcacheblock keys=[node.node_id, user.contentobject_id] expiry=600 %}
<p>Only for user {{ user.contentobject_id }}</p>
{% endexpcacheblock %}
```

```
<p>Shown to every visitor with the same roles: Why Track Your Workouts?</p>
<p>Only for user 10</p>
```

(Benutzer 10 ist der anonyme Benutzer.) Ziehen Sie Rollen vor: Ein Block je Benutzer wird für jeden besuchenden Benutzer
einmal gerendert, und das Cache-Verzeichnis wächst mit ihnen; nehmen Sie den Benutzer nur als Schlüssel für das, was sich
wirklich je Benutzer unterscheidet, und geben Sie ihm ein kurzes `expiry`. Für einen Teil je Benutzer in einer sonst
geteilten Seite rendern Sie den Teil außerhalb des Blocks und setzen ihn über eine Markierung ein, wie
[Caching](../../eng-us/guides/twig-templates-caching.md#one-block-for-the-whole-page-a-fresh-user-menu) (Englisch) es
für das Benutzermenü zeigt.

Variablen, die in einem Block gesetzt werden, sind nicht gesetzt, wenn der Block aus dem Cache kommt: Setzen Sie mit
`{% set %}`, was Sie nach dem Block brauchen, außerhalb davon.

## Ein Formular mit dem Formular-Token

Formulare, die an den Kernel senden (`content/action`, `user/edit`, ein eigenes Modul), brauchen in Twig nichts
Besonderes. Die Formular-Token-Extension fügt das Token in jedes `<form method="post">` der fertigen Seite ein, nachdem
alle Templates gelaufen sind, gleich welcher Engine:

```twig
<form method="post" action={{ 'content/action'|ezurl }}>
<input type="hidden" name="ContentNodeID" value="{{ node.node_id }}" />
<button type="submit">{{ 'Add to basket'|i18n('design/ezwebin/full/product') }}</button>
</form>
<div data-token="@$ezxFormToken@"></div>
```

```
<form method="post" action="/content/action">
<input type="hidden" name="ContentNodeID" value="98" />
<button type="submit">Add to basket</button>
</form>
<div data-token="@$ezxFormToken@"></div>
```

Das druckt das Template. Für einen angemeldeten Besucher mit einer Sitzung fügt danach der Ausgabefilter von
`extension/ezformtoken` nach jedem Tag `<form ... method="post">` ein `<input type="hidden" name="ezxform_token"
value="...">` ein und ersetzt `@$ezxFormToken@` überall in der Seite durch das Token, das JavaScript, das selbst sendet,
aus dem Attribut lesen kann. (Der Filter lief für dieses Beispiel nicht: Er wirkt nur in einer Browser-Sitzung.) Der
Filter markiert die Seite außerdem als privat, sodass kein geteilter Cache eine Seite mit Token behält.

## Texte mit einer Anzahl

Das Übersetzungssystem kennt keine Pluralformen: Ein Text wird als Ganzes übersetzt. Wählen Sie den Text zuerst nach der
Anzahl und übersetzen Sie ihn dann mit der Anzahl als Argument:

```twig
{% for n in [0, 1, 2, 5] %}{{ (n|eq(1) ? '%count comment' : '%count comments')|i18n('design/standard/content/view', '', {'%count': n}) }}; {% endfor %}
```

```
0 comments; 1 comment; 2 comments; 5 comments; 
```

Beide Texte sind konstant; ein Übersetzer bekommt also zwei Einträge, einen für eins und einen für viele. Sprachen mit mehr
Formen (mehrere Pluralformen, ein Dual) brauchen mehr Texte und eine Bedingung je Form; schreiben Sie die Bedingung für
die Sprache des Siteaccess. Kleben Sie keine Texte zusammen (`count ~ ' ' ~ 'comments'|i18n(...)`): Die Wortstellung ist
von Sprache zu Sprache verschieden.

## Bilder in einem Bild-Alias

Der `content` eines Bildattributs enthält jeden Alias des Bildes nach Namen, jeden mit `url`, `width` und `height`; die
Aliasse sind in `image.ini` definiert (`[AliasSettings] AliasList[]`):

```twig
{% for name in ['small', 'medium', 'large', 'i320'] %}{{ name }}: {{ node.data_map.image.content[name].width|default('-') }}x{{ node.data_map.image.content[name].height|default('-') }}; {% endfor %}
```

```
small: 100x66; medium: 200x133; large: 1200x800; i320: 320x213; 
```

Ein `<img>` mit dem Alias Ihrer Wahl und einem Ersatz, wenn es den Alias nicht gibt:

```twig
{% set image = node.data_map.image %}
{% if image.has_content %}
{% set alias = image.content['i320']|default(image.content.medium) %}
<img src={{ alias.url|ezroot }} width="{{ alias.width }}" height="{{ alias.height }}" alt="{{ image.content.alternative_text|default(node.name)|wash }}" loading="lazy" />
{% endif %}
```

```
<img src="/var/site/storage/images/fit-healthy/fitness/why-track-your-workouts/615-1-eng-US/why-track-your-workouts_i320.jpg" width="320" height="213" alt="Why Track Your Workouts?" loading="lazy" />
```

Hinweise:

- Ein Alias wird beim ersten Abruf erzeugt und dann aufbewahrt; wer auf einer kalten Website eine lange Liste von Aliassen
  abfragt, erzeugt sie alle auf einmal.
- `url` ist relativ zur Installation; `ezroot` macht eine Adresse daraus und fügt die Anführungszeichen hinzu.
- `attribute_view_gui({attribute: node.data_map.image, image_class: 'small'})` rendert stattdessen das eigene Template des
  Datentyps für den Alias, mit seinen Override-Regeln.

## Verwandte Seiten

- [Twig-Templates nativ schreiben](twig-templates-nativ-schreiben.md)
- [Templates nebeneinander](../hilfe/template-sprache/tpl-twig-smarty-nebeneinander.md)
- [Twig templates: recipes](../../eng-us/guides/twig-templates-recipes.md) (Englisch)
- [Twig templates: caching](../../eng-us/guides/twig-templates-caching.md) (Englisch)
