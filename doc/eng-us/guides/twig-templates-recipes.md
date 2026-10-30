# Twig templates: recipes

This page is for template developers who want a working starting point for the templates a site needs most, written
in Twig for Exponential: a page layout, a line view, a block of Netgen Layouts, a mail template, cache blocks per role
and per user, a form with the form token, texts with a count, and images in an image alias. Each recipe says where the
file goes, shows the template, and shows what it printed on a development installation with the media demo content
(node 98 is the article "Why Track Your Workouts?"). Host names in output are shown as `www.example.com`.

Where a recipe needs variables that only a real request has (the `module_result` of a page, the `block` of a layout),
the example ran with a value set up for it; the recipe says so.

## A page layout

**File**: `override/templates/pagelayout.twig` of your design. The kernel asks the design resource for
`pagelayout.tpl`, like any template, so an override rule points that request at the `.twig`:

```ini
# settings/siteaccess/<siteaccess>/override.ini.append.php
[pagelayout_twig]
Source=pagelayout.tpl
MatchFile=pagelayout.twig
Subdir=templates
```

`MatchFile` is relative to the design's `override/templates/` directory. The converter, which keeps the converted files
at the places of the `.tpl` files, writes `MatchFile=../../templates/pagelayout.twig` for a file in `templates/`; both
forms work.

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

**Output** (run with a `module_result` of three path items and the body `<p>The page body.</p>`):

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

Notes:

- `module_result.content` is the rendered module view: HTML, printed as it is. Never `wash` it.
- `ezurl` and `ezdesign` print the address with quotes (`href="/fitness"`), so the attribute is written without
  them: `href={{ ... }}`. Use `ezurl('no')` where you need the address without quotes.
- `{%- ... -%}` keeps the doctype on the first line: a pagelayout must not start with a blank line.
- For a fast page, keep the whole page in one cache block with the visitor's own parts filled in afterward; the
  pattern is in [Caching](twig-templates-caching.md#one-block-for-the-whole-page-a-fresh-user-menu).

## A line view

**File**: `override/templates/line/article.twig`, with the rule

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

**Output** for node 98 (its teaser is empty):

```
<article class="line line-ng_article">
      <a href="/fitness/why-track-your-workouts"><img src="/var/site/storage/images/fit-healthy/fitness/why-track-your-workouts/615-1-eng-US/why-track-your-workouts_small.jpg" width="100" height="66" alt=""></a>
    <h3><a href="/fitness/why-track-your-workouts">Why Track Your Workouts?</a></h3>
  <p class="date">07/23/2018</p>
  </article>
```

A list renders each child with it: `{{ node_view_gui({content_node: child, view: 'line'}) }}`. The image is decorative
next to a linked title, hence `alt=""`.

## A block of Netgen Layouts

**File**: the block's view template in your design, for example `templates/explayouts/block/button.twig`. The layouts
extension asks the design resource for `explayouts/block/button.tpl`, so an override rule points it at the `.twig`,
as for any template (the converter writes these rules with `--override-ini`):

```ini
[twig_explayouts_block_button]
Source=explayouts/block/button.tpl
MatchFile=../../templates/explayouts/block/button.twig
Subdir=templates
```

The block's settings are in `block.values`; the template below is the button block of the media design, written by
hand:

```twig
{% if block.values.text is not empty %}
  {% if block.values.link is not empty %}
<a href={{ block.values.link|ezurl }} class="{{ block.values.class|wash }}">{{ block.values.text|wash }}</a>
  {% else %}
<span class="{{ block.values.class|wash }}">{{ block.values.text|wash }}</span>
  {% endif %}
{% endif %}
```

**Output**, run with `block.values` = `{text: 'Read more', link: '/fitness', class: 'btn btn-primary'}`:

```
  <a href="/fitness" class="btn btn-primary">Read more</a>
```

Converted block templates of the media design show the other variables a block template gets (`block`, the zone's
`layout`, the operators `layout_title` and the like); open the `.twig` next to its `.tpl` to see them.

## A mail template

**File**: the template a module renders for the mail, for example a notification template of your design. A mail
template is rendered like a page part, and the module reads values back from it after it ran: the subject, the
content type, sometimes the sender. In a `.tpl` that is `{set-block scope=root variable=subject}`; in a `.twig` a
plain `{% set %}` does it, because what a `.twig` sets is written back to its namespace when it ends.

```twig
{% set subject %}New on {{ ezini('SiteSettings', 'SiteName') }}: {{ node.name|wash }}{% endset %}
{% set content_type = 'text/html' %}
<p>{{ 'Hello'|i18n('design/standard/notification') }},</p>
<p><a href="{{ node.url_alias|ezurl('no', 'full') }}">{{ node.name|wash }}</a></p>
```

**Output**, and what the caller read back from the template afterward:

```
<p>Hello,</p>
<p><a href="http://www.example.com/fitness/why-track-your-workouts">Why Track Your Workouts?</a></p>
```

```
$subject = 'New on Fit & Healthy: Why Track Your Workouts?'
$content_type = 'text/html'
```

Notes:

- `ezurl('no', 'full')`: a mail needs absolute addresses; the host comes from `[SiteSettings] SiteURL`.
- Mail clients ignore stylesheets: keep styles in the markup. That is the one place where inline styles belong (the
  rule against `style=""` is for the admin designs).
- The subject is text, not HTML: do not wash it for HTML; the mail library encodes it.

## Cache blocks per role and per user

A cache block is kept per value of its keys. Keyed by the visitor's roles, one copy serves every visitor with the
same rights; keyed by the user, every user gets a copy of their own:

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

(User 10 is the anonymous user.) Prefer roles: a block per user is rendered once for every user who visits, and the
cache directory grows with them; key by user only what really differs per user, and give it a short `expiry`. For a
per-user part inside an otherwise shared page, render the part outside the block and put it in through a marker, as
[Caching](twig-templates-caching.md#one-block-for-the-whole-page-a-fresh-user-menu) shows for the user menu.

Variables set inside a block are not set when the block comes from cache: `{% set %}` what you need after the block
outside it.

## A form with the form token

Forms that post to the kernel (`content/action`, `user/edit`, a module of your own) need nothing special in Twig. The
form token extension adds the token to every `<form method="post">` of the finished page, after all templates ran,
whatever their engine:

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

That is what the template prints. For a signed-in visitor with a session, the output filter of `extension/ezformtoken`
then inserts `<input type="hidden" name="ezxform_token" value="...">` after each `<form ... method="post">` tag and
replaces `@$ezxFormToken@` anywhere in the page with the token, which JavaScript that posts by itself can read from the
attribute. (The filter was not run for this example: it acts only within a browser session.) The filter also marks
the page as private, so no shared cache keeps a page with a token.

## Texts with a count

The translation system has no plural forms: a text is translated as a whole. Choose the text by the count first, then
translate it with the count as an argument:

```twig
{% for n in [0, 1, 2, 5] %}{{ (n|eq(1) ? '%count comment' : '%count comments')|i18n('design/standard/content/view', '', {'%count': n}) }}; {% endfor %}
```

```
0 comments; 1 comment; 2 comments; 5 comments; 
```

Both texts are constant, so a translator gets two entries to translate, one for one and one for many. Languages with
more forms (several plural forms, a dual) need more texts and a condition per form; write the condition for the
language of the siteaccess. Do not glue texts together (`count ~ ' ' ~ 'comments'|i18n(...)`): the word order differs
between languages.

## Images in an image alias

An image attribute's `content` holds every alias of the image, by name, each with `url`, `width` and `height`; the
aliases are defined in `image.ini` (`[AliasSettings] AliasList[]`):

```twig
{% for name in ['small', 'medium', 'large', 'i320'] %}{{ name }}: {{ node.data_map.image.content[name].width|default('-') }}x{{ node.data_map.image.content[name].height|default('-') }}; {% endfor %}
```

```
small: 100x66; medium: 200x133; large: 1200x800; i320: 320x213; 
```

An `<img>` with the alias of your choice and a fallback when the alias does not exist:

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

Notes:

- An alias is created the first time it is asked for, then kept; asking for a large list of aliases on a cold site
  creates them all at once.
- `url` is relative to the installation; `ezroot` makes it an address and adds the quotes.
- `attribute_view_gui({attribute: node.data_map.image, image_class: 'small'})` renders the datatype's own template
  for the alias instead, with its override rules.

## Related

- [Writing Twig-native templates](twig-templates-native.md)
- [Caching](twig-templates-caching.md)
- [Template functions and tags](twig-templates-functions.md)
- [Templates side by side: .tpl, .twig and .stpl](../help/template-language/tpl-twig-smarty-side-by-side.md)
