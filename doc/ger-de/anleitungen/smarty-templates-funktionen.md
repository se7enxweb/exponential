# Smarty-Templates: Template-Funktionen und Blöcke

Diese Seite ist für Template-Entwickler, die die Tags eines `.stpl` brauchen: die Exponential-Template-Funktionen, als
Smarty-Tags überbrückt (`{attribute_view_gui}`, `{node_view_gui}` ...), die Funktionen mit Rumpf, als Smarty-Blöcke
überbrückt (`{explblock}`), die Tags und Blöcke der Brücke selbst (`{expinclude}`, `{expset}`, `{expundefall}`,
`{expfetch}`, `{expsetblock}`, `{expappendblock}`, `{expcacheblock}`, `{exprunonce}`) und Smartys eigene Tags. Für
jedes: seine Parameter, in welchem Namensraum es läuft, was es zurückgibt, und ein Beispiel. Der Code liegt in
`lib/smarty/classes/`: `expSmartyExtension` entscheidet, wofür ein Tag-Name steht, die anderen Klassen sind die Tags.

## Wie ein Tag-Name aufgelöst wird

Wenn Smarty ein Tag kompiliert, das es selbst nicht kennt, fragt es `expSmartyExtension`, das in dieser Reihenfolge
antwortet:

1. **Smartys eigene Tags** kommen immer zuerst: `{if}`, `{foreach}`, `{for}`, `{while}`, `{section}`, `{include}`,
   `{capture}`, `{assign}`, `{function}`, `{call}`, `{block}`, `{extends}`, `{literal}`, `{ldelim}`, `{strip}`,
   `{nocache}`, `{config_load}`, `{debug}`, `{eval}`, `{setfilter}`, und die Funktionen `{counter}`, `{cycle}`,
   `{fetch}`, `{html_checkboxes}`, `{html_image}`, `{html_options}`, `{html_radios}`, `{html_select_date}`,
   `{html_select_time}`, `{html_table}`, `{mailto}`, `{math}`, `{textformat}`.
2. **Die eigenen Tags der Brücke**: `{expinclude}`, `{expset}`, `{expundefall}`, `{expfetch}` und die Blöcke
   `{expsetblock}`, `{expappendblock}`, `{expcacheblock}`, `{exprunonce}`.
3. **Die Funktionen in `[SmartySettings] FunctionList[]`** von `settings/smarty.ini`, immer als Tags ohne Rumpf.
4. **Jede andere Template-Funktion**, die das Template-System im Siteaccess registriert hat, sofern sie nicht
   Template-Struktur ist: als Tag, wenn sie keinen Rumpf hat, als Block (`{name}...{/name}`), wenn sie einen hat (ihre
   Klasse sagt `hasChildren()`).

Nie überbrückt, weil sie die Struktur eines `.tpl` sind und Smarty-Entsprechungen haben: `if`, `elseif`, `else`,
`foreach`, `for`, `while`, `do`, `def`, `undef`, `set`, `let`, `default`, `section`, `section-else`,
`section-exclude`, `section-include`, `delimiter`, `switch`, `case`, `set-block`, `append-block`, `cache-block`,
`run-once`, `include`, `literal`, `ldelim`, `rdelim`, `break`, `continue`, `skip`
(`expSmartyExtension::NOT_BRIDGED`). Eine Funktion, deren Name einen Bindestrich hat (`debug-log`,
`debug-timing-point`, `debug-accumulator`, `debug-trace`), kann kein Smarty-Tag-Name sein.

Die Antwort für jeden Namen wird für die Lebensdauer des PHP-Prozesses gemerkt. Eine Funktion, die später verfügbar
wird (eine aktivierte Extension), ist zu sehen, nachdem PHP-FPM neu geladen oder Velocity neu gestartet wurde.

## Template-Funktionen als Tags

```smarty
{attribute_view_gui attribute=$node.data_map.body}
{node_view_gui view='line' content_node=$child}
{content_view_gui view='embed' content_object=$object}
{attribute_view_gui attribute=$node.data_map.body assign=body_html}
```

- **Parameter** sind benannt, wie im `.tpl`, mit Smarty-Werten: `view='line'`, `content_node=$child`,
  `image_class=(concat('i', $size))`. Ein Parameter, dessen Wert ein Ausdruck mit Pipe-Zeichen oder Operator ist,
  kommt in Klammern. Parameternamen müssen aus Buchstaben, Ziffern und Unterstrichen bestehen und mit einem Buchstaben
  beginnen, sonst druckt der Aufruf nichts und protokolliert `invalid parameter name for <function>: <name>`.
- **`assign=name`** legt die Ausgabe in die Smarty-Variable `$name`, statt sie zu drucken.
- **Wie es läuft.** Jeder Aufruf ist ein kleines erzeugtes `.tpl`, das nur den Aufruf enthält
  (`{node_view_gui content_node=$content_node view=$view}`), in
  `<cache directory>/smarty/functions/<function>-<md5 of the parameter names>.tpl`, kompiliert und gecacht vom
  Template-System wie jedes Template. Eine Funktion läuft also genau so wie aus einem `.tpl`, und ihre eigenen
  Templates dürfen `.tpl` oder `.stpl` sein.
- **Namensraum.** Der Aufruf läuft in einem eigenen Namensraum (`ExpSmarty<n>`), der nur die Parameter enthält, die Sie
  übergeben. Die Templates der Funktion sehen die anderen Variablen Ihres `.stpl` nicht: Übergeben Sie, was sie
  brauchen, als Parameter (die `*_view_gui`-Funktionen geben jeden zusätzlichen benannten Parameter als Variable an ihr
  Template weiter). Globale Variablen (`$#name` in einem `.tpl`) bleiben global. Eine Funktion, die Variablen setzt
  (das `.tpl`-`{sequence}`), setzt sie in diesem Namensraum, also kommt nichts zurück; verwenden Sie die
  Smarty-Entsprechung (`expsequence`, Smartys `{cycle}`).

### Die Funktionen des Kernels

| Funktion | Hauptparameter | Ihre Templates | `FunctionList[]` |
|---|---|---|---|
| `attribute_view_gui` | `attribute` | `content/datatype/view/<datatype>` (nach Ansicht, Klasse und Attribut) | ja |
| `attribute_edit_gui` | `attribute` | `content/datatype/edit/<datatype>` | ja |
| `attribute_result_gui` | `attribute` | `content/datatype/result/<datatype>` | ja |
| `attribute_pdf_gui` | `attribute` | `content/datatype/pdf/<datatype>` | ja |
| `attribute_diff_gui` | `attribute` | `content/datatype/diff/<datatype>` | nein (trotzdem als Tag überbrückt) |
| `node_view_gui` | `content_node`, `view` | `node/view/<view>` | ja |
| `content_view_gui` | `content_object`, `view` | `content/view/<view>` | ja |
| `content_pdf_gui` | `content_object` | `content/pdf/<view>` | nein |
| `content_version_view_gui` | `content_version` | `content/version/view/<view>` | ja |
| `related_view_gui` | `related_object` | `content/related/<view>` | ja |
| `shop_account_view_gui` | `order` | `shop/accounthandlers/<view>` | ja |
| `class_attribute_view_gui` | `class_attribute` | `class/datatype/view/<datatype>` | ja |
| `class_attribute_edit_gui` | `class_attribute` | `class/datatype/edit/<datatype>` | ja |
| `collaboration_view_gui`, `collaboration_icon` | `collaboration_item` | `collaboration/handlers/view`, `.../icon` | nein |
| `collaboration_simple_message_view` | `collaboration_message` | `collaboration/message/view` | nein |
| `collaboration_participation_view` | `collaboration_participant` | nach ihren Match-Regeln (`template_root` vom Typ `multi_match`) | nein |
| `event_view_gui`, `event_edit_gui` | `event` | `workflow/eventtype/view`, `.../edit` | nein |
| `tool_bar` | `name`, `view` | die Werkzeugleiste aus `toolbar.ini` | nein |
| `menu` | `name` | das Menü aus `menu.ini` | nein |

„nein“ in der letzten Spalte ändert für diese nichts: Jede Funktion, die das Template-System kennt, wird überbrückt.
Fügen Sie einen Namen nur dann zu `FunctionList[]` hinzu, wenn eine Funktion mit Rumpf als einfaches Tag aufgerufen
werden soll.

Echte Zeilen aus der Media-Portierung:

```smarty
<div class="exp_richtext-field">{attribute_view_gui attribute=$af_map.body}</div>
{attribute_view_gui attribute=$node.object.data_map.billboard}
```

## Funktionen mit Rumpf als Blöcke

Eine Template-Funktion, die im `.tpl` einen Rumpf hat (`hasChildren()` ist wahr), ist ein Smarty-Block:

```smarty
{explblock name='content'}
<div class="content-view-full">
    {explblock name='article_body'}
    <div class="full-page-body">...</div>
    {/explblock}
</div>
{/explblock}
```

Der Rumpf des Blocks wird zuerst von Smarty gerendert und dann der Funktion als ihre Kinder übergeben, ein Textknoten
(`expSmartyBlockFunction`). Funktionen, die ihre Kinder umschließen oder nachbearbeiten (wie `{explblock}` von
Exponential Layouts: Es markiert einen Bereich der Ausgabe, damit ein Layout ihn herausnehmen kann), verhalten sich wie
in einem `.tpl`. Eine Funktion, die die Struktur ihrer Kinder statt deren Ausgabe betrachtet, sieht nur diesen Text.
Die Parameter werden als Werte übergeben; die Funktion läuft im Namensraum des `.stpl`.

Überbrückte Blöcke der ausgelieferten Extensions: `{explblock name=... [if=...] [wrap=...]}` (explayouts). Andere
(`block_view_gui` und `block_edit_gui` von ezflow, die `powercontent_*`-Funktionen) werden nach denselben Regeln
überbrückt, wenn ihre Extension aktiv ist.

## Die Tags der Brücke

### {expinclude}: das Include des Template-Systems

```smarty
{expinclude uri='design:pagelayout/head/title.tpl'}
{expinclude uri='design:parts/paging.stpl'}
{expinclude uri=(concat('design:explayouts/block/list/', $list_view_type, '.tpl')) block=$block}
{expinclude uri='design:navigator/google.tpl' name='navigator' page_uri=$node.url_alias item_count=$count view_parameters=$view_parameters item_limit=10}
```

| Parameter | Bedeutung |
|---|---|
| `uri` | erforderlich: eine Template-URI (`design:...`, `file:...`), durch die Design-Ressource und die Override-Regeln; `.tpl` oder `.stpl` |
| `name` | optional: das eingebundene Template in einem Namensraum dieses Namens unterhalb des aktuellen ausführen |
| jeder andere | eine Variable des eingebundenen Templates, nur für dieses Template |

- **Ohne `name=`** teilt das eingebundene Template den Namensraum des einbindenden: Es sieht dessen Variablen, und was
  es definiert, ändert oder entfernt, ist danach sichtbar. Wenn die URI zu einem `.stpl` aufgelöst wird, läuft es
  direkt als Smarty-Subtemplate (`expSmartyRenderer::resolveURI()`, `renderSubtemplate()`), mit derselben Wirkung und
  ohne den Umweg über das Template-System. Wenn sie zu einem `.tpl` aufgelöst wird, werden die Variablen des `.stpl`
  zuerst in den Namensraum kopiert und danach zurückgelesen.
- **Mit `name=`** läuft es über das Template-System in diesem Namensraum; seine Variablen kommen nicht zurück.
- Die anderen Parameter ersetzen Variablen gleichen Namens nur für das eingebundene Template; danach gelten wieder die
  alten Werte.
- Eine fehlende `uri` bricht das Rendern ab: `expinclude: missing parameter 'uri'`.
- Das `.tpl`-`{include}` entspricht ihm eins zu eins (der Konverter schreibt `{expinclude}` für jedes `{include}`).

### {expset}: setzen mit einem Geltungsbereich oder einem Namensraum

```smarty
{expset name='persistent_variable' value=['left_menu' => false] scope='global'}
{expset name='cache_ttl' value=0 scope='global'}
{expset name='title' value=$node.name scope='root'}
```

| Parameter | Bedeutung |
|---|---|
| `name` | erforderlich: der Variablenname |
| `value` | ihr Wert (`null`, wenn weggelassen) |
| `scope` | `relative` (Voreinstellung, der aktuelle Namensraum), `root` (der Wurzel-Namensraum des Templates) oder `global` |
| `namespace` | ein Namensraum unterhalb des Geltungsbereichs; mit `scope='global'` der globale Namensraum dieses Namens |

Für eine Variable des Templates selbst tut eine einfache Smarty-Zuweisung dasselbe (`{$title = $node.name}`); der
Renderer schreibt sie zurück, wenn das Template endet. `{expset}` ist für die Fälle, in denen ein `.tpl`
`{set scope=global ...}` oder `{set name=Namespace ...}` schreibt. Ist das Ziel der eigene Wurzel-Namensraum des
Templates, wird auch die Smarty-Variable gesetzt. `expset: missing parameter 'name'`, wenn `name` fehlt.

### {expundefall}: jede Variable entfernen

`{expundefall}` ist das `.tpl`-`{undef}` ohne Parameter: Es entfernt jede Variable des Wurzel-Namensraums des Templates
und setzt jede Smarty-Variable des Templates auf `null`. Um eine einzelne Variable zu entfernen, weisen Sie `null` zu:
`{$tag = null}`.

### {expfetch}: ein Fetch als Tag

```smarty
{expfetch assign=children module=content function=list parent_node_id=$node.node_id limit=10 sort_by=$node.sort_array}
```

`assign`, `module` und `function` sind erforderlich (`expfetch: missing parameter 'module'`); jeder andere Parameter ist
ein Parameter der Fetch-Funktion. Es ist dasselbe wie
`{$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 10, 'sort_by' => $node.sort_array])}`.

## Die Blöcke der Brücke

### {expsetblock} und {expappendblock}

```smarty
{expsetblock variable='cache_ttl' scope='global'}0{/expsetblock}
{expappendblock variable='scripts' scope='global'}<script src="/a.js"></script>{/expappendblock}
```

| Parameter | Bedeutung |
|---|---|
| `variable` | erforderlich: der Variablenname (`expsetblock: missing parameter 'variable'`) |
| `scope` | `relative` (Voreinstellung), `root` oder `global` |
| `name` | ein Namensraum unterhalb des Geltungsbereichs |

`{expsetblock}` legt die Ausgabe des Blocks in die Variable; `{expappendblock}` hängt sie an ein Array an (ein neues
Array, wenn die Variable keines ist). Für die eigene Variable des Templates ist Smartys
`{capture assign=x}...{/capture}` dasselbe wie `{set-block variable=x}` und ist das, was der Konverter schreibt; sie
wird an das aufrufende Template zurückgeschrieben, wenn das `.stpl` endet.

Smarty verwirft den Zeilenumbruch direkt nach einem Tag, das nichts druckt. `{expsetblock ...}`, gefolgt von einem
Zeilenumbruch und `1`, speichert `1`, nicht einen Zeilenumbruch und `1`: Das Media-Pagelayout schreibt es absichtlich
so.

### {expcacheblock}

```smarty
{expcacheblock id=1 keys=[$node.node_id, $view_parameters.offset] expiry=3600}
    ...
{/expcacheblock}
```

| Parameter | Bedeutung |
|---|---|
| `id` | eine in der Datei eindeutige Zahl; mit dem Dateinamen ist sie der Ort des Blocks (`stpl:<file>:<id>`), Teil des Schlüssels |
| `keys` | ein Wert oder ein Array von Werten, die zum Schlüssel hinzukommen |
| `expiry` | Sekunden, die der Block lebt; Voreinstellung `eZTemplateCacheFunction::DEFAULT_TTL` (zwei Stunden); `0` bedeutet keine Zeitgrenze |
| `ignore_content_expiry` | `true`: eine Veröffentlichung von Inhalten lässt den Block nicht ablaufen |
| `subtree_expiry` | eine Knoten-ID oder ein Pfad: nur eine Veröffentlichung unterhalb dieses Knotens lässt den Block ablaufen (und der globale Inhaltsablauf wird ignoriert) |

Es ist das `{cache-block}` des Template-Systems: im Template-Block-Cache gespeichert, unter einem Schlüssel aus den
`keys`, seinem Ort und dem Siteaccess (dazu die vom UI-Umschalter gewählte Variante des Administrations-Designs), und es
läuft ab wie ein Cache-Block eines `.tpl`. Wenn der Block im Cache ist, läuft sein Rumpf nicht. Ist
`[TemplateSettings] TemplateCache` nicht `enabled`, läuft der Rumpf immer und nichts wird gespeichert. Alles über
Schlüssel, Ablauf und die Teile pro Besucher steht in [Caching und Leistung](smarty-templates-caching.md).

### {exprunonce}

```smarty
{exprunonce id=1}<script src={'javascript/gallery.js'|ezdesign}></script>{/exprunonce}
```

Der Rumpf wird nur beim ersten Mal ausgegeben, wenn der Block in einer Anfrage läuft (das `.tpl`-`{run-once}`); ein
Template, das viele Male auf einer Seite eingebunden ist, lädt sein Skript einmal. `id` ist in der Datei eindeutig; mit
dem Dateinamen ist sie der Schlüssel.

## Smartys eigene Tags, die hier wichtig sind

| Tag | Verwendung in einem `.stpl` |
|---|---|
| `{$x = value}`, `{assign var='x' value=...}` | eine Variable definieren oder ändern; wird an das aufrufende Template zurückgeschrieben |
| `{capture assign=x}...{/capture}` | Ausgabe in eine Variable (das `.tpl`-`{set-block variable=x}`) |
| `{if}`, `{elseif}`, `{else}` | Bedingungen |
| `{foreach $list as $k => $v}`, `{foreachelse}` | Schleifen über eine Variable; `$v@index`, `@iteration`, `@first`, `@last`, `@total` |
| `{for $i = 1 to 10}` | aufwärts zählen |
| `{while}`, `{break}`, `{continue}` | Schleifen |
| `{include file='part.stpl' a=$b}` | ein anderes `.stpl` aus den Template-Verzeichnissen der Designs, ohne Override-Regeln; nie ein `.tpl` |
| `{literal}...{/literal}` | Text mit geschweiften Klammern (JavaScript, CSS) |
| `{ldelim}`, `{rdelim}` | eine einzelne geschweifte Klammer |
| `{function name=...}`, `{call}` | Smarty-Template-Funktionen, die in der Datei definiert sind |
| `{cycle values='odd,even'}` | abwechselnde Werte in einer Schleife |
| `{section}` | **Smartys** Section, nicht die des `.tpl`: andere Parameter und Variablen. Das `.tpl`-`{section}` wird zu `{foreach}` konvertiert |
| `{fetch file=...}` | **Smartys** Abruf einer Datei oder URL, nicht der Modul-Fetch; verwenden Sie `fetch(...)` in einem Ausdruck oder `{expfetch}` |

## Verwandte Seiten

- [Smarty-Templates: .stpl-Templates schreiben und ausführen](smarty-templates.md)
- [Operator-Referenz](smarty-templates-operator-referenz.md)
- [Caching und Leistung](smarty-templates-caching.md)
- [Operatoren und Funktionen für .stpl schreiben](smarty-templates-plugins.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
