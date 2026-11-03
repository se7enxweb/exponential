# Smarty-Templates: Operatoren und Fetch-Funktionen

Diese Seite ist für Template-Entwickler, die `.stpl`-Templates schreiben. Jeder Template-Operator von Exponential kann
in einem `.stpl` verwendet werden, als Smarty-Modifier und als Funktion, und jede Fetch-Funktion eines Moduls über
`fetch()`. Diese Seite erklärt die beiden Formen, wie ein Operator den Wert bekommt, mit dem er arbeitet, wann ein
Operator das Präfix `exp_` braucht und wie Sie die gängigen Operatoren schreiben, jeweils die `.tpl`-Form neben der
`.stpl`-Form. Die Beispiele stammen aus dem Media-Design und seiner Smarty-Portierung
(`extension/sevenx_themes_media/design/media/` und `.../design/smarty_media/`).

Beginnen Sie mit [Smarty-Templates: .stpl-Templates schreiben und ausführen](smarty-templates.md), wenn Sie noch kein
`.stpl` geschrieben haben.

## Zwei Formen: Modifier und Funktion

Ein Operator wird als **Modifier** aufgerufen, mit dem Wert, mit dem er arbeitet, vor dem senkrechten Strich und seinen
Parametern nach Doppelpunkten:

```smarty
{$node.name|wash}
{'Skip to main content'|i18n:'design/media/pagelayout'}
{$block.values.tag|ne:''}
```

oder als **Funktion**, mit seinen Parametern in Klammern:

```smarty
{concat('h', $block.values.level)}
{ezini('SiteSettings', 'SiteName')}
{fetch('content', 'node', ['node_id' => 2])}
```

Die beiden Formen sind derselbe Aufruf: Smarty kompiliert `op(a, b, c)` als `a|op:b:c`. Das erste Argument einer
Funktion ist das, was ein Modifier vor dem senkrechten Strich bekommt.

Zwei Gewohnheiten aus `.tpl` lassen sich nicht übertragen:

- Modifier-Parameter folgen auf Doppelpunkte. `$x|i18n('context')` ist ein Smarty-Syntaxfehler; schreiben Sie
  `$x|i18n:'context'`.
- Ein Parameter, der selbst ein Ausdruck mit einem senkrechten Strich oder einem Operator ist, steht in Klammern:
  `$attributes|merge:(['class' => $class])`, `'Play video'|i18n:'design/media/video':'':(['%title' => $ytp_title])`.

Modifier verketten sich von links nach rechts, und ein ganzer Ausdruck kann weitergereicht werden, wenn er in Klammern
steht:

```smarty
{(concat('/content/view/full/', $node.object.owner.id))|ezurl}
{('Read more about %name'|i18n:'design/media/content':'':(['%name' => $node.name]))|wash}
```

## Wie ein Operator seine Eingabe bekommt

In einem `.tpl` hat ein Operator eine Eingabe (den Wert vor dem senkrechten Strich) und Parameter. Die meisten
Operatoren arbeiten mit ihrer Eingabe: `wash` wäscht sie, `i18n` übersetzt sie. Andere übergehen die Eingabe und
arbeiten mit ihren Parametern: `concat` verbindet sie, `eq` vergleicht sie, `ezini` liest die Einstellung, die sie
nennen. Die Brücke (`expSmartyRenderer::operatorInputMode()`) liest die Template-Hinweise jedes Operators und ruft ihn
so auf, wie es das kompilierte Template-System tut:

| Der Operator | In einem `.stpl` | Beispiele |
|---|---|---|
| liest seine Eingabe | der Wert vor dem senkrechten Strich oder das erste Funktionsargument ist die Eingabe; der Rest sind Parameter | `i18n`, `ezurl`, `ezroot`, `ezimage`, `ezdesign`, `wash`, `contains`, `implode`, `explode`, `append`, `merge`, `count`, `l10n`, `upcase` |
| liest nur seine Parameter (Hinweis `input-as-parameter` true, oder gar keine Eingabe, oder ein Steueroperator) | der Wert vor dem senkrechten Strich wird zum ersten Parameter | `eq`, `ne`, `lt`, `gt`, `le`, `ge`, `and`, `or`, `not`, `concat`, `sum`, `sub`, `mul`, `div`, `ezini`, `ezini_hasvariable`, `is_set` und die anderen Typprüfungen, `fetch`, `cond`, `first_set` |

Für einen Template-Autor heißt das: **`a|op:b` und `op(a, b)` tun immer dasselbe**, und bei Operatoren der zweiten Art
spielt es keine Rolle, welcher Wert in welcher Form zuerst kommt. `$a|eq:2` und `eq($a, 2)` vergleichen `$a` mit 2;
`'a'|concat:'b'` und `concat('a', 'b')` ergeben beide `ab`; `1|sum:2` und `sum(1, 2)` ergeben beide 3.

Operatoren, bei denen der Hinweis `input-as-parameter` auf `always` steht (`contains`, `implode`, `append`, `merge`,
`i18n`, `ezurl` und andere), gehören zur ersten Art: Der Hinweis beschreibt nur, wie der Template-Compiler sie
einbettet, und zur Laufzeit lesen sie ihre Eingabe.

Wo es doch eine Rolle spielt:

- **Ein Operator, der seine Eingabe liest, wird ohne Eingabe aufgerufen.** Ein `.tpl` kann `count($list)` oder
  `merge($a, $b)` ohne senkrechten Strich schreiben; das Template-System übergibt dann keine Eingabe. Die genaue
  `.stpl`-Form übergibt `null` als Eingabe: `exp_count(null, $list)`, `merge(null, $a, $b)`. Das schreibt der
  Konverter. Bei den meisten solchen Operatoren ergibt es dasselbe, den Wert als Eingabe zu übergeben
  (`$list|exp_count`).
- **`cond` und `first_set` mit senkrechtem Strich.** In einem `.tpl` übergeht `$a|first_set($b)` das `$a` und ist
  `$b`, wenn `$b` gesetzt ist; auch `$a|cond(...)` übergeht `$a`. In einem `.stpl` wird der weitergereichte Wert zum
  ersten Parameter, sodass `$a|first_set:$b` „der erste von `$a`, `$b`, der gesetzt ist“ bedeutet und `$a|cond:x:y`
  `$a` als Bedingung verwendet. Schreiben Sie sie als Funktionen: `first_set($a, 'default')`,
  `cond($test, 'yes', 'no')`. Der Konverter behält das Verhalten des `.tpl` bei, indem er die Eingabe weglässt (siehe
  [was nach dem Konvertieren zu prüfen ist](smarty-templates-konverter.md#3-seine-hinweise-und-was-nach-dem-konvertieren-zu-prüfen-ist)).

## Das Präfix exp

Smartys eigene Modifier haben Vorrang. Wo Smarty einen Modifier mit demselben Namen wie ein Exponential-Operator hat,
wird der Operator mit dem Präfix `exp_` aufgerufen; `exp_<name>` ruft immer den Exponential-Operator `<name>` auf:

```smarty
{if $sections|exp_count}
<meta name="keywords" content="{($meta.keywords|exp_implode:',')|wash}" />
{if and(is_set(expvar('extra_data', 'Header')), exp_is_array(expvar('extra_data', 'Header')))}
{$node.data_map.success_text.data_text|exp_strip_tags}
```

Die Namen, die das Präfix brauchen, sind die Modifier, die Smarty 5 hat (die Liste des Konverters,
`expSmartyConverter::SMARTY_MODIFIERS`):
`capitalize`, `cat`, `count`, `count_characters`, `count_paragraphs`, `count_sentences`, `count_words`,
`date_format`, `debug_print_var`, `default`, `empty`, `escape`, `explode`, `from_charset`, `implode`, `in_array`,
`indent`, `is_array`, `isset`, `join`, `json_encode`, `lower`, `mb_wordwrap`, `nl2br`, `noprint`, `number_format`,
`raw`, `regex_replace`, `replace`, `round`, `spacify`, `split`, `str_repeat`, `string_format`, `strip`,
`strip_tags`, `strlen`, `substr`, `to_charset`, `truncate`, `unescape`, `upper`, `wordwrap`; dazu die Hilfsnamen der
Brücke selbst: `expattr`, `expvoid`, `expvar`, `expsequence`, `expslice`, `exprange`, `expsectionloop` (und
`expcolumn`).

Ohne das Präfix bekommen Sie Smartys Modifier, der sich anders verhalten kann:

| Ohne Präfix (Smarty) | Mit Präfix (Exponential) |
|---|---|
| `$text\|explode:','` nimmt das Trennzeichen als Eingabe: für eine `.tpl`-Gewohnheit verkehrt herum, und veraltet | `$text\|exp_explode:','` teilt `$text` an Kommas |
| `$list\|implode:','` funktioniert, ist in Smarty 5 aber veraltet | `$list\|exp_implode:','` |
| `'abc'\|count` ist 1 (alles, was kein Array und nicht zählbar ist, zählt 1) | `'abc'\|exp_count` ist 3 (die Länge einer Zeichenkette, wie in `.tpl`) |
| `is_array($x)` ist die PHP-Funktion | `exp_is_array($x)` ist der Operator |

Bei einem Operator, den Smarty nicht hat, funktioniert beides: `$x|wash` und `$x|exp_wash` sind dasselbe.

## Gängige Operatoren, .tpl neben .stpl

Jeder Operator der Installation, nach Bereich gruppiert, mit seinem Modus: [Operator-Referenz](smarty-templates-operator-referenz.md).

| `.tpl` | `.stpl` |
|---|---|
| `{'Read more'\|i18n('design/standard/node')}` | `{'Read more'\|i18n:'design/standard/node'}` |
| `{'Order %order_id'\|i18n('design/ezwebin/shop/orderview',,hash('%order_id', $order.order_nr))}` | `{'Order %order_id'\|i18n:'design/ezwebin/shop/orderview':expvoid():(['%order_id' => $order.order_nr])}` |
| `<a href={$node.url_alias\|ezurl}>` | `<a href={$node.url_alias\|ezurl}>` |
| `{'/content/view'\|ezurl('no')}` | `{'/content/view'\|ezurl:'no'}` |
| `{'/content/view'\|ezurl('no', 'full')}` | `{'/content/view'\|ezurl:'no':'full'}` |
| `<img src={$image.url\|ezroot} />` | `<img src={$image.url\|ezroot} />` |
| `{'logo.png'\|ezimage}` | `{'logo.png'\|ezimage}` |
| `{$node.name\|wash}`, `{$email\|wash('email')}` | `{$node.name\|wash}`, `{$email\|wash:'email'}` |
| `{if $methods\|contains('code')}` | `{if $methods\|contains:'code'}` |
| `{$keywords\|implode(', ')}` | `{$keywords\|exp_implode:', '}` |
| `{def $parts = $text\|explode(',')}` | `{$parts = $text\|exp_explode:','}` |
| `{set $items = $items\|append('large')}` | `{$items = $items\|append:'large'}` |
| `{set $attributes = $attributes\|merge(hash('class', $class))}` | `{$attributes = $attributes\|merge:(['class' => $class])}` |
| `{concat('h', $block.values.level)}` | `{concat('h', $block.values.level)}` |
| `{if $block.values.tag\|ne('')}` | `{if $block.values.tag\|ne:''}` |
| `{if eq($a, 2)}`, `{if $a\|eq(2)}` | `{if eq($a, 2)}`, `{if $a\|eq:2}` oder Smartys `{if $a == 2}` |
| `{if and($img_w\|gt(0), $img_h\|gt(0))}` | `{if and($img_w\|gt:0, $img_h\|gt:0)}` |
| `{if or($a, $b)}`, `{if $x\|not}` | `{if or($a, $b)}`, `{if $x\|not}` |
| `{cond(is_set($redirect_uri), $redirect_uri, '/user/edit')\|ezurl}` | `{(cond(is_set($redirect_uri), $redirect_uri, '/user/edit'))\|ezurl}` |
| `{first_set($redirect_uri, $redirect_url, '/')}` | `{first_set($redirect_uri, $redirect_url, '/')}` |
| `{ezini('SiteSettings', 'SiteName')}` | `{ezini('SiteSettings', 'SiteName')}` |
| `{ezini('SiteInfo', 'CookiePolicyID', 'menu.ini')}` | `{ezini('SiteInfo', 'CookiePolicyID', 'menu.ini')}` |
| `{if ezini_hasvariable('SiteInfo', 'CookiePolicyID', 'menu.ini')}` | `{if ezini_hasvariable('SiteInfo', 'CookiePolicyID', 'menu.ini')}` |
| `{set $total = sum($total, $item.item_count)}` | `{$total = sum($total, $item.item_count)}` |
| `{if $list\|count}`, `{count($list)}` | `{if $list\|exp_count}`, `{exp_count(null, $list)}` |
| `{if is_set($block.view_type)}` | `{if is_set($block.view_type)}` oder Smartys `isset($block.view_type)` |
| `{$node.object.published\|l10n('shortdate')}` | `{$node.object.published\|l10n:'shortdate'}` |
| `hash('node_id', 2, 'limit', 5)` | `['node_id' => 2, 'limit' => 5]` |
| `array('a', 'b')` | `['a', 'b']` |
| `true()`, `false()` | `true`, `false` |

Die PHP-Zeichenkettenfunktionen, die Operatoren sind (siehe
[PHP-Zeichenkettenfunktionen als Template-Operatoren](../funktionen/6.0/template-operatoren-fuer-zeichenketten.md)),
funktionieren genauso, mit `exp_`, wo Smarty den Namen hat: `{$text|exp_substr:0:5}`, `{$text|str_contains:'Expo'}`.

## Leere Parameter: expvoid()

Ein `.tpl` kann einen Parameter leer lassen, damit der Operator seinen Standardwert verwendet, wie in
`i18n('context',,hash(...))`. Smarty kennt keinen leeren Parameter; schreiben Sie an seiner Stelle `expvoid()`:

```smarty
{'My pending items [%pending_count]'|i18n:'design/ezwebin/content/pendinglist':expvoid():(['%pending_count' => $list_count])}
```

`expvoid()` wird dem Operator als leerer Parameter übergeben, sodass der Standardwert gilt. Allein gedruckt ist es
eine leere Zeichenkette.

## Fetch-Funktionen

`fetch()` ruft jede Fetch-Funktion eines Moduls auf, mit ihren Parametern als Smarty-Array-Literal (das `hash(...)` des
`.tpl`):

```smarty
{$node = fetch('content', 'node', ['node_id' => $module_result.content_info.node_id])}
{$cat_children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'sort_by' => [['published', false]], 'limit' => $cat_limit, 'offset' => $cat_offset])}
{$cat_count = fetch('content', 'list_count', ['parent_node_id' => $node.node_id])}
{$activate_user = fetch('content', 'object', ['object_id' => $user.contentobject_id])}
{$el_layout = fetch('explayouts', 'resolve_layout_for_node', ['node_id' => $node.node_id])}
{$el_layout = fetch('explayouts', 'resolve_layout', [])}
<li>Exponential <strong>{(fetch('setup', 'version'))|wash}</strong></li>
```

- Ein Fetch ohne Parameter nimmt `[]` (das `hash()` des `.tpl`).
- Verschachtelte Arrays sind verschachtelte Literale: Das `sort_by, array('priority', true())` des `.tpl` ist
  `'sort_by' => ['priority', true]`, und `array(array('published', false()))` ist `[['published', false]]`.
- Die Fetch-Funktionen und ihre Berechtigungsprüfungen sind dieselben wie aus einem `.tpl`; ein Fetch aus einer
  Erweiterung funktioniert, wenn diese Erweiterung im Siteaccess aktiv ist.
- Um ein Attribut eines Fetch-Ergebnisses ohne Variable zu lesen, verwenden Sie `expattr`:
  `{(fetch('shop', 'basket'))|expattr:'items'}`. Das Ergebnis zuerst einer Variablen zuzuweisen ist leichter zu
  lesen.
- Um über einen Fetch zu schleifen, weisen Sie ihn zuerst zu (`{foreach}` durchläuft eine Variable, keinen Ausdruck).
- `{expfetch assign=children module=content function=list parent_node_id=$node.node_id limit=10}` ist die Tag-Form:
  Jeder Parameter außer `assign`, `module` und `function` wird zu einem Fetch-Parameter.

## Die Helfer der Brücke

Diese Modifier sind keine Exponential-Operatoren; die Brücke fügt sie für das hinzu, was Smarty nicht ausdrücken kann.
Sie sind das, was der Konverter schreibt, und können von Hand verwendet werden:

| Helfer | Was er tut | Beispiel |
|---|---|---|
| `expattr` | ein Attribut oder Array-Schlüssel eines beliebigen Werts | `(fetch('shop', 'basket'))\|expattr:'items'` |
| `expvar` | eine Variable eines globalen Namensraums, das `$#name` und `$#Namespace:name` des `.tpl` | `expvar('extra_data', 'Header')` |
| `expvoid` | ein leerer Operator-Parameter | `i18n:'context':expvoid():[...]` |
| `expslice` | ein Array, nach Offset und Höchstzahl geschnitten, umgekehrt oder nicht, Schlüssel bleiben erhalten | `$list\|expslice:0:3:false` |
| `expsectionloop` | die Einträge eines `{section loop=...}`: ein Array oder eine Zahl n für 1 bis n | `($path)\|expsectionloop:0:null:false` |
| `exprange` | die ganzen Zahlen von einer Zahl bis zu einer anderen, aufwärts oder abwärts gezählt | `exprange(1, 5)` |
| `expsequence` | der Eintrag einer Folge an einer Position, zyklisch | `$__sequence1\|expsequence:$item@index` |
| `expcolumn` | ein Attribut jedes Eintrags eines Arrays | `$items\|expcolumn:'id'` |

## Verwandte Seiten

- Neu bei `.stpl`: [ein erstes .stpl, Schritt für Schritt](smarty-templates-tutorial.md)
- Referenz: [jeder Operator](smarty-templates-operator-referenz.md),
  [Template-Funktionen und Blöcke](smarty-templates-funktionen.md),
  [Problembehebung und FAQ](smarty-templates-problembehebung.md)
- Themen: [Caching und Leistung](smarty-templates-caching.md),
  [Leistung (Geschwindigkeit)](smarty-templates-leistung.md), [ein Design migrieren](smarty-templates-migrieren.md),
  [Operatoren und Funktionen schreiben](smarty-templates-plugins.md),
  [Sicherheit und Maskierung](smarty-templates-sicherheit.md)
- [Smarty-Templates: .stpl-Templates schreiben und ausführen](smarty-templates.md)
- [Smarty-Templates: der Konverter und das Portieren eines Designs](smarty-templates-konverter.md)
- [Smarty-Templates (.stpl) neben .tpl](../funktionen/6.0/smarty-templates.md), die Funktionsseite

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
