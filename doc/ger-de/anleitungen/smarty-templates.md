# Smarty-Templates: .stpl-Templates schreiben und ausführen

Diese Anleitung richtet sich an Template-Entwickler, die Templates in Smarty-5-Syntax in einem Exponential-Design
schreiben möchten. Sie prüfen, dass Smarty verfügbar ist, bringen mit einer Override-Regel ein erstes `.stpl` auf eine
Seite und lernen, was ein `.stpl` sieht und kann: Variablen und wie sie zurückfließen, Exponential-Objekte,
Einbindungen, Schleifen, Template-Funktionen, Blöcke, Caching, Fehlersuche und die Unterschiede zu `.tpl`. Arbeiten Sie
die Teile 1 bis 3 von oben nach unten durch (etwa fünfzehn Minuten); der Rest ist Nachschlagestoff, zu dem Sie
zurückkehren. Die Funktionsseite ist [Smarty-Templates (.stpl) neben .tpl](../funktionen/6.0/smarty-templates.md).

Zwei weitere Seiten ergänzen diese:

- [Smarty-Templates: Operatoren und Fetch-Funktionen](smarty-templates-operatoren.md)
- [Smarty-Templates: der Konverter und die Portierung eines Designs](smarty-templates-konverter.md)

Jeder Befehl läuft im Wurzelverzeichnis der Installation. Wenn Sie als `root` angemeldet sind, ergänzen Sie
`--allow-root-user` (die Beispiele schreiben es aus). Sie müssen wissen, wie ein `.tpl` gefunden und überschrieben wird:
[Templates und Design](templates-und-design.md) erklärt es.

## 1. Was mit einem .stpl geschieht

Das Template-System findet Templates über seine Ressourcen (`design:` und die Datei-Ressource). Wenn ein Template-Name zu
einer Datei mit der Endung `.stpl` aufgelöst wird:

1. Die Ressource übergibt dem Template-System nicht den Text der Datei. Sie übergibt einen einzeiligen Stub,
   `{exp_smarty_render( '<file>' )}` (`expSmartyRenderer::stubFor()`, aufgerufen aus
   `eZTemplateFileResource::handleResourceData()`).
2. Das Template-System parst, kompiliert und cacht den Stub wie jedes Template, unter einem eigenen Schlüssel für
   kompilierte Templates (`expSmartyRenderer::cacheKey()`, aufgerufen aus `eZTemplateFileResource::cacheKey()`), sodass
   sich ein Stub nie eine kompilierte Datei mit einem Parse des Smarty-Texts teilt.
3. Bei jeder Anfrage, aus dem geparsten wie aus dem kompilierten Stub, rendert der Operator `exp_smarty_render` die
   Datei mit Smarty 5 und übergibt die Variablen des Template-Namensraums, in dem er aufgerufen wurde.

Die Template-Caches enthalten also nur den Stub. Was eine Anfrage gerendert hat, wird nie in ihnen gespeichert. Smarty
hält seine eigenen kompilierten Templates (PHP, nie Ausgabe: Smartys Ausgabe-Caching ist aus) in
`<Cache-Verzeichnis>/smarty/compile/`, zum Beispiel `var/site/cache/smarty/compile/`.

Die Endung entscheidet. Ein `.tpl` wird nie an Smarty geschickt, und eine Anfrage nach `node/view/full.tpl` findet von
sich aus nie ein `.stpl`: Ein `.stpl` wird verwendet, wenn eine Override-Regel auf es zeigt, wenn eine Einbindung es
nennt oder wenn ein Aufruf des Template-Systems es nennt (`$tpl->fetch( 'design:parts/x.stpl' )`).

Der Stub-Operator lässt sich auch direkt aus einem `.tpl` aufrufen, mit einem Pfad relativ zum Wurzelverzeichnis der
Installation:

```
{exp_smarty_render( 'design/standard/templates/smarty/demo_full.stpl' )}
```

## 2. Prüfen, dass Smarty verfügbar ist

Die `composer.json` im Wurzelverzeichnis verlangt `smarty/smarty ^5`, eine mit Composer eingerichtete Installation hat
es also in `vendor/`:

```bash
ls vendor/smarty/smarty/src/Smarty.php
```

Erwartet: Der Pfad wird ausgegeben. Hat die Installation kein Composer-`vendor/`, legen Sie einen Smarty-5-Checkout
irgendwo ab und nennen ihn in `settings/override/smarty.ini.append.php`:

```ini
[SmartySettings]
# das Verzeichnis, das src/Smarty.php enthält, relativ zum Wurzelverzeichnis der Installation oder absolut
LibraryPath=lib-external/smarty
```

Die Bibliothek wird zuerst aus Composers Autoload geladen und nur dann aus `LibraryPath`, wenn die Klasse
`\Smarty\Smarty` dort nicht ist; Smartys `src/functions.php` wird aus demselben Verzeichnis geladen. Smarty 3 und 4
werden nicht unterstützt.

Die anderen Einstellungen von `settings/smarty.ini`:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `CompileCheck` | `enabled` | Smarty vergleicht ein `.stpl` mit seiner kompilierten Datei und kompiliert ein geändertes neu. `disabled` prüft nie: schneller, für den Produktivbetrieb, aber dann leeren Sie den Template-Cache nach jeder Template-Änderung. |
| `Debugging` | `disabled` | `enabled` schaltet Smartys Debug-Konsole ein. |
| `FunctionList[]` | `attribute_view_gui`, `attribute_edit_gui`, `attribute_result_gui`, `attribute_pdf_gui`, `node_view_gui`, `content_view_gui`, `content_version_view_gui`, `class_attribute_view_gui`, `class_attribute_edit_gui`, `related_view_gui`, `shop_account_view_gui` | Template-Funktionen von Exponential, die immer als Smarty-Tags aufrufbar sind (Teil 8). |

Es muss nichts aktiviert werden: `settings/site.ini` registriert den Operator über
`[TemplateSettings] AutoloadPathList[]=lib/smarty/classes/` und den Smarty-Cache über `[Cache_smarty]`.

## 3. Ein erstes .stpl auf einer Seite

Verwenden Sie die Demo, die der Kernel mitliefert, `design/standard/templates/smarty/demo_full.stpl`, als
Vollansicht von Ordnern. In `settings/siteaccess/<siteaccess>/override.ini.append.php`:

```ini
[folder_smarty_demo]
Source=node/view/full.tpl
MatchFile=../../templates/smarty/demo_full.stpl
Subdir=templates
Match[class_identifier]=folder
```

`MatchFile` wird unterhalb von `override/templates/` jedes Designs gesucht; die Demo liegt in `templates/`, daher das
`../../templates/` (für Ihr eigenes `.stpl` in `override/templates/` schreiben Sie den einfachen Pfad,
`MatchFile=full/folder.stpl`).

Leeren Sie die Caches, die die Override-Liste und die gerenderten Ansichten halten:

```bash
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=content --allow-root-user
```

Öffnen Sie einen Ordner auf der Website. Erwartet: Die Seite zeigt den Namen des Ordners, die Zeile "Rendered by
Smarty 5.x for node &lt;id&gt; (folder)" und eine Liste von bis zu fünf Kindern. Die Demo ist kurz:

```smarty
<div class="content-view-full smarty-demo" data-smarty-node="{$node.node_id}">
    <h1>{$node.name|wash}</h1>
    <p class="smarty-demo-marker">Rendered by Smarty {$smarty.version} for node {$node.node_id} ({$node.object.class_identifier|escape})</p>
    {if isset($node.data_map.short_description) && $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 5, 'sort_by' => ['priority', true]])}
    {include file='smarty/demo_children.stpl' children=$children}
</div>
```

Laden Sie einen zweiten Ordner: Er zeigt seine eigene Knoten-ID. Der zweite Aufruf führt den kompilierten Stub aus, der
das `.stpl` mit dem neuen `$node` erneut rendert. Bearbeiten Sie das `.stpl` und laden Sie neu: Mit
`CompileCheck=enabled` ist die Änderung sofort da (leeren Sie den Content-Cache, wenn der View-Cache die Seite
ausliefert). Entfernen Sie die Override-Regel, wenn Sie fertig sind.

## 4. Variablen

### Was ein .stpl sieht

Ein `.stpl` sieht die Variablen seines Wurzel-Namensraums (was ein `.tpl` als `$name` liest) und seines aktuellen
Namensraums, genau das, was ein `.tpl` an derselben Stelle sehen würde:

- eine Knotenansicht: `$node`, `$view_parameters` und die anderen Variablen dieser Ansicht (eine Formularansicht hat
  auch `$validation`);
- ein Pagelayout: `$module_result`, `$site` und die übrigen Variablen des Pagelayouts;
- ein eingebundenes Template: seine Einbindungsparameter und, ohne `name=`, die Variablen des einbindenden Templates;
- `$smarty` gehört Smarty selbst (`{$smarty.version}`), eine Template-Variable namens `smarty` wird also nicht
  übergeben.

Übergeben werden nur Namen, die Smarty verwenden kann: Buchstaben, Ziffern und Unterstriche, nicht mit einer Ziffer
beginnend. Eine Template-Variable mit einem anderen Namen (zum Beispiel mit einem Bindestrich) ist in einem `.stpl`
nicht sichtbar.

### Definieren, ändern und entfernen

Smartys Zuweisung ist alles, was ein `.stpl` braucht. Wenn das Template fertig ist, wird jede Variable, die es
definiert oder geändert hat, in seinen Wurzel-Namensraum zurückgeschrieben, und eine auf `null` gesetzte Variable wird
dort entfernt:

| `.tpl` | `.stpl` |
|---|---|
| `{def $title = $node.name}` | `{$title = $node.name}` |
| `{set $title = 'Other'}` | `{$title = 'Other'}` |
| `{undef $title}` | `{$title = null}` |
| `{undef}` (jede Variable des Wurzel-Namensraums) | `{expundefall}` |
| `{set-block scope=root variable=subject}...{/set-block}` | `{capture assign=subject}...{/capture}` |

Das lässt ein `.stpl`-Mail-Template funktionieren: Das Modul liest `$subject`, nachdem das Template gelaufen ist. Aus
dem portierten Media-Design, `templates/emails/info_collection/email.stpl`:

```smarty
{capture assign=subject}
{ezini('SiteSettings', 'SiteName')} – {$content.name|wash} [{$collected_fields.sender_email.value}]{/capture}
```

Regeln, die Sie kennen sollten:

- Schleifenvariablen sind auch Variablen: Nach `{foreach $children as $child}` wird auch das letzte `$child`
  zurückgeschrieben.
- Namen, die mit zwei Unterstrichen beginnen (`$__loop1`, `$__sequence2`, die Hilfsvariablen konvertierter Templates),
  bleiben in Smarty und werden nie zurückgeschrieben.
- Variablen werden in den Namensraum zurückgeschrieben, in dem das `.stpl` gerendert wurde, nicht weiter nach oben.

### Geltungsbereiche und Namensräume: {expset}

Für die Fälle, die eine einfache Zuweisung nicht ausdrücken kann, schreibt `{expset}` in die Namensräume des
Template-Systems:

```smarty
{expset name='persistent_variable' value=['left_menu' => false] scope='global'}
```

| Parameter | Bedeutung |
|---|---|
| `name` | der Variablenname (Pflicht) |
| `value` | ihr Wert |
| `scope` | `relative` (der Standard: der aktuelle Namensraum), `root` (der Wurzel-Namensraum des Templates) oder `global` (ein globaler Namensraum) |
| `namespace` | ein Namensraum unterhalb des Geltungsbereichs; mit `scope='global'` der globale Namensraum dieses Namens |

Das ist die `.tpl`-Form `{set scope=global persistent_variable=...}`. Wenn das Ziel der eigene Wurzel-Namensraum des
Templates ist, wird auch die Smarty-Variable gesetzt.

Um eine Variable eines globalen Namensraums zu lesen (die `.tpl`-Formen `$#name` und `$#Namespace:name`), verwenden Sie
`expvar()`:

```smarty
{if and(is_set(expvar('extra_data', 'Header')), exp_is_array(expvar('extra_data', 'Header')))}
```

## 5. Exponential-Objekte

Exponential-Objekte (Knoten, Objekte, Attribute, alles mit `attribute()` und `hasAttribute()`) erreichen Smarty in
einen schreibgeschützten Proxy verpackt (`expSmartyObjectProxy`), sodass Smartys Punktsyntax Attribute so liest wie ein
`.tpl`:

```smarty
{$node.name|wash}
{$node.object.class_identifier}
{$node.data_map.body.has_content}
{$node.path_array|exp_implode:'/'}
{if isset($node.data_map.image)}...{/if}
{$node->attribute('name')}
```

- `isset($node.data_map.image)` ist wahr, wenn das Attribut existiert und nicht `null` ist.
- Attributwerte werden auf dem Weg nach draußen erneut verpackt, Arrays von Objekten Element für Element, sodass
  `$node.data_map.body.content` und `$node.children` in jeder Tiefe weiter funktionieren.
- Methodenaufrufe (`$node->attribute('name')`) werden an das Objekt weitergereicht; Proxys unter den Argumenten werden
  ausgepackt.
- Wann immer ein Wert zurück an Exponential geht (ein Operator, eine Template-Funktion, ein Fetch, ein Zurückschreiben),
  wird der Proxy wieder durch das echte Objekt ersetzt.
- Der Proxy ist schreibgeschützt: `{$node.name = 'x'}` ändert nichts am Objekt.
- Ein Proxy, der für sich allein ausgegeben wird, druckt das `__toString()` des Objekts oder nichts.
- Iterieren Sie über Arrays (`$node.data_map`, `$node.children`, ein Fetch-Ergebnis), nicht über Objekte: Smartys
  `{foreach}` iteriert keinen Proxy.
- Ein Attribut eines Werts, der keine Variable ist, braucht `expattr`, da Smarty dafür keine Syntax hat:

  ```smarty
  {$__loop901 = (fetch('shop', 'basket'))|expattr:'items'}
  <html lang="{(((ezini('RegionalSettings', 'Locale'))|locale)|expattr:'http_locale_code')|wash}">
  ```

## 6. Einbindungen

Es gibt zwei Arten, und sie verhalten sich unterschiedlich.

**`{expinclude}` ist das `{include}` des Template-Systems.** Die URI geht durch die Design-Ressource und die
Override-Regeln, das eingebundene Template darf ein `.tpl` oder ein `.stpl` sein, und die anderen Parameter werden zu
seinen Variablen:

```smarty
{expinclude uri='design:pagelayout/head/title.tpl'}
{expinclude uri='design:content/views/payload/ng_contact_form.tpl' node=$node referer=$requested_uri_string form_class='embed-form js-form-embed'}
{expinclude uri=(concat('design:explayouts/block/list/', $list_view_type, '.tpl')) block=$block}
```

- Ohne `name=` teilt sich das eingebundene Template den Namensraum des einbindenden: Es sieht alle seine Variablen,
  auch die, die das `.stpl` bis dahin zugewiesen hat, und was es definiert, ist nach der Einbindung sichtbar, wie bei
  `{include}` in einem `.tpl`.
- Mit `name=` läuft es in einem Namensraum dieses Namens unterhalb des aktuellen.
- Die Parameter ersetzen Variablen gleichen Namens nur für das eingebundene Template; danach sind die alten Werte
  wieder da.
- Eine fehlende `uri` bricht das Rendern mit "expinclude: missing parameter 'uri'" ab.
- **Ein ohne `name=` eingebundenes `.stpl` läuft als Smarty-Subtemplate.** Die Brücke fragt zuerst die
  Design-Ressource, für welche Datei die URI steht (`expSmartyRenderer::resolveURI()`: dieselbe Ressource, dieselben
  Override-Regeln und Schlüssel, die das Template-System verwenden würde, ohne die Datei zu laden). Ist diese Datei ein
  `.stpl`, wird sie direkt als Smarty-Subtemplate des einbindenden gerendert (`expSmartyRenderer::renderSubtemplate()`),
  nicht über das Template-System, seinen Stub und ein zweites Rendern. Es sieht die Variablen des einbindenden
  Templates, seine Parameter sind seine eigenen, und was es definiert, ändert oder auf `null` setzt, ist danach im
  einbindenden Template sichtbar, genau wie über das Template-System. Das ist der Hauptgrund, warum eine `.stpl`-Seite
  schnell ist: Die Startseite des Media-Designs rendert 37 Templates über die Brücke statt 266. Bei eingeschalteter
  Debug-Ausgabe steht ein so gerendertes Subtemplate im Template-Baum der Debug-Leiste unter dem Template, das es
  eingebunden hat, so wie ein `{include}` eines `.tpl`; ohne sie wird nichts vermerkt.

**`{include file=...}` ist Smartys eigenes Include.** Es sucht in `override/templates/` und `templates/` jedes Designs
des Siteaccess, in der Fallback-Reihenfolge der Designs, und bindet ein anderes `.stpl` ohne das Template-System ein:

```smarty
{include file='smarty/demo_children.stpl' children=$children}
```

Verwenden Sie es nur für `.stpl`-Dateien: Es wendet keine Override-Regel an, und ein so eingebundenes `.tpl` würde als
Smarty gelesen. Variablen, die die eingebundene Datei zuweist, bleiben in ihr (Smartys Include-Geltungsbereich).

## 7. Schleifen und Bedingungen

Smartys eigene `{if}`, `{elseif}`, `{else}`, `{foreach}`, `{foreachelse}`, `{for}`, `{while}`, `{break}` und
`{continue}` werden verwendet, wie sie sind. Bedingungen können Smartys Operatoren (`==`, `&&`, `!`) oder die von
Exponential (`and(...)`, `$a|eq:2`) verwenden, siehe [die Anleitung zu den Operatoren](smarty-templates-operatoren.md).

Eine Regel erwischt jeden, der ein `.tpl` portiert: **`{foreach}` iteriert über eine Variable, nicht über einen
Ausdruck.** Weisen Sie eine berechnete Quelle zuerst zu:

```smarty
{* abgelehnt: "missing 'from' attribute" *}
{foreach ($validation.attributes) as $item}...{/foreach}

{* funktioniert *}
{$__loop1 = fetch('content', 'list', ['parent_node_id' => $node.node_id])}
{foreach $__loop1 as $child}...{/foreach}
```

Hilfsmittel für das, was eine `.tpl`-Schleife kann und eine Smarty-Schleife nicht:

| Hilfsmittel | Verwendung |
|---|---|
| `expslice` | `{foreach}` mit Offset, Maximum und Umkehrung: `{$__loop1 = $list|expslice:2:5:false}` |
| `expsectionloop` | die Elemente eines `{section loop=...}`: ein Array oder eine Zahl n für 1..n, mit Offset, Maximum und Umkehrung: `{$__loop1 = ($path)|expsectionloop:0:null:false}` |
| `exprange` | ein Bereich, der auf- oder abwärts zählt, für `{for}`: `{foreach exprange(1, 5) as $r_star}` |
| `expsequence` | ein Wert einer Sequenz nach Position: `{$row_class = $__sequence1|expsequence:$item@index}` |
| `expcolumn` | ein Attribut jedes Elements eines Arrays: `$items|expcolumn:'id'` |

Das `.tpl`-`{delimiter}` wird als `{if !$item@first}...{/if}` am Anfang des Schleifenrumpfs geschrieben. `{section}`,
`{switch}`, `{let}`, `{default}` und `{do}` haben kein Smarty-Tag; der Konverter schreibt sie als die Konstrukte oben
([die Anleitung zum Konverter](smarty-templates-konverter.md#2-was-er-schreibt) hat die Tabelle).

## 8. Template-Funktionen als Tags

Jede Funktion und jeder Block mit ihren Parametern: [Template-Funktionen und Blöcke](smarty-templates-funktionen.md).

Jede Template-Funktion des Template-Systems, die keine Template-Struktur ist, lässt sich als Smarty-Tag mit benannten
Parametern aufrufen. Die Ausgabe wird gedruckt oder mit `assign=` in eine Variable gelegt:

```smarty
{attribute_view_gui attribute=$node.data_map.body}
{node_view_gui view='line' content_node=$child}
{content_view_gui view='embed' content_object=$object}
{attribute_view_gui attribute=$node.data_map.body assign=body_html}
```

- Die Funktionen in `[SmartySettings] FunctionList[]` werden immer als Tags überbrückt. Jede andere Funktion, die das
  Template-System im Siteaccess registriert hat, wird ebenfalls überbrückt: als Tag, wenn sie keinen Rumpf hat, als
  Block, wenn sie einen hat.
- Die Tags, die das Template-System für die Struktur verwendet, werden nie überbrückt (`if`, `foreach`, `section`,
  `def`, `set`, `let`, `default`, `switch`, `include`, `set-block`, `append-block`, `cache-block`, `run-once`,
  `literal`, `delimiter` und dergleichen); verwenden Sie die Smarty-Entsprechungen aus dieser Anleitung.
- Parameternamen müssen aus Buchstaben, Ziffern und Unterstrichen bestehen und mit einem Buchstaben beginnen, sonst
  druckt der Aufruf nichts (einen HTML-Kommentar bei eingeschalteter Debug-Ausgabe) und protokolliert
  "invalid parameter name". Eine Funktion mit einem Bindestrich im Namen (`debug-log`) kann kein Smarty-Tag sein.
- Smartys eigene Tags haben Vorrang vor einer Exponential-Funktion gleichen Namens (`include`, `capture`, `assign`,
  `cycle`, ...).
- Die Templates, die die Funktionen verwenden (das Ansichts-Template eines Datentyps, die Zeilenansicht eines Knotens),
  dürfen `.tpl` oder `.stpl` sein.
- Jeder Aufruf ist ein kleines erzeugtes `.tpl` in `<Cache-Verzeichnis>/smarty/functions/`, das das Template-System wie
  jedes Template kompiliert und cacht, sodass eine Funktion genau so läuft wie aus einem `.tpl`.

**Funktionen mit einem Rumpf sind Smarty-Blöcke.** Die Ausgabe des Blocks wird zuerst von Smarty gerendert und der
Funktion als ihre Kinder übergeben, als ein Stück Text. Die Layout-Block-Funktion von Exponential Layouts ist der
übliche Fall, aus den portierten Vollansichten:

```smarty
{explblock name='content'}
<div class="content-view-full">
    {explblock name='article_body'}
    <div class="full-page-body">...</div>
    {/explblock}
</div>
{/explblock}
```

Eine Funktion, die die Struktur ihrer Kinder untersucht (statt ihrer Ausgabe), sieht nur diesen Text.

**`{expfetch}`** ist eine Tag-Form von `fetch()`, für alle, die Attribute bevorzugen:

```smarty
{expfetch assign=children module=content function=list parent_node_id=$node.node_id limit=10}
```

`assign`, `module` und `function` sind Pflicht; jeder andere Parameter wird zu einem Fetch-Parameter.

## 9. Blöcke: set-block, append-block, cache-block, run-once

| `.tpl` | `.stpl` |
|---|---|
| `{set-block variable=x}...{/set-block}` (auch `scope=root`) | `{capture assign=x}...{/capture}` |
| `{set-block scope=global variable=cache_ttl}0{/set-block}` | `{expsetblock variable='cache_ttl' scope='global'}0{/expsetblock}` |
| `{append-block variable=scripts}...{/append-block}` | `{expappendblock variable='scripts'}...{/expappendblock}` |
| `{cache-block keys=array($node.node_id) expiry=3600}...{/cache-block}` | `{expcacheblock id=1 keys=[$node.node_id] expiry=3600}...{/expcacheblock}` |
| `{run-once}...{/run-once}` | `{exprunonce id=1}...{/exprunonce}` |

- `{expsetblock}` und `{expappendblock}` nehmen `variable` (Pflicht), `scope` (`relative`, `root`, `global`) und `name`
  (einen Namensraum), wie `{expset}`. `{expappendblock}` hängt die Ausgabe des Blocks an ein Array an.
- `{expcacheblock}` ist das `{cache-block}` des Template-Systems: gespeichert im Template-Block-Cache mit denselben
  Schlüsseln plus dem Siteaccess, verfallen durch `expiry` (Sekunden), durch Inhaltsänderungen, außer bei
  `ignore_content_expiry=true`, und durch `subtree_expiry` (das ebenfalls den Inhaltsverfall ignoriert, wie in einem
  `.tpl`). Ist der Block gecacht, läuft sein Rumpf nicht. Er cacht nur bei `[TemplateSettings] TemplateCache=enabled`.
- `{exprunonce}` gibt seinen Rumpf nur beim ersten Durchlauf in einer Anfrage aus.
- Geben Sie jedem `{expcacheblock}` und `{exprunonce}` in einer Datei eine eigene `id`: Die ID und der Dateiname bilden
  den Schlüssel. Der Konverter nummeriert sie für Sie.

## 10. Caching und Leeren

Die Ebenen im Detail, der Seitenblock mit Platzhaltern pro Besucher, Schlüssel für angemeldete Besucher und wie man
misst: [Caching und Leistung](smarty-templates-caching.md).

| Was | Wo | Geleert durch |
|---|---|---|
| Der Stub, vom Template-System kompiliert | `<Cache-Verzeichnis>/template/compiled/` | die Template-Caches (`--clear-tag=template`) |
| Smartys kompilierte `.stpl`-Dateien | `<Cache-Verzeichnis>/smarty/compile/` | `[Cache_smarty]`: `--clear-id=smarty`, und mit den Template-Caches (`--clear-tag=template`) |
| Die erzeugten Aufrufe von Template-Funktionen | `<Cache-Verzeichnis>/smarty/functions/` | dasselbe |
| Gerenderte Seiten | der Content-View-Cache, `{expcacheblock}`, der Antwort-Cache von Velocity | wie bei `.tpl`-Seiten |

```bash
php bin/php/ezcache.php --clear-id=smarty --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
```

- Mit `CompileCheck=enabled` wird ein geändertes `.stpl` beim nächsten Rendern neu kompiliert; es muss nichts geleert
  werden, außer den Caches, die gerenderte Seiten halten.
- Mit `CompileCheck=disabled` leeren Sie den Smarty-Cache nach einer Template-Änderung.
- Ein neues `.stpl`, eine neue Override-Regel oder eine umbenannte Datei erfordert das Leeren der Template-Caches, wie
  bei einem `.tpl`.

## 11. Fehlersuche

Jede Meldung mit Ursache und Abhilfe sowie die Probleme, die keine Meldung erzeugen, stehen unter
[Problembehebung und FAQ](smarty-templates-problembehebung.md); dieser Teil ist die Kurzfassung.

Ein `.stpl`, das scheitert, bricht die Seite nicht. Seine Ausgabe wird ersetzt, und der Grund wird protokolliert:

- in der Debug-Ausgabe, als Fehler mit dem Label `smarty` (und im Fehlerlog wie jeder Debug-Fehler);
- auf der Seite, als HTML-Kommentar `<!-- smarty: ... -->` dort, wo die Ausgabe des Templates gestanden hätte, nur bei
  eingeschalteter Debug-Ausgabe. Bei ausgeschalteter Debug-Ausgabe druckt das Template nichts.

Die Meldungen:

| Meldung | Ursache |
|---|---|
| `rendering <file> failed: <reason>` | Smarty konnte das Template nicht kompilieren oder ausführen; der Grund ist Smartys Meldung oder die eines der `ez*`-Tags (`expinclude: missing parameter 'uri'`, `expfetch: missing parameter 'module'`, `expset: missing parameter 'name'`, `expsetblock: missing parameter 'variable'`) |
| `template not found: <file>` | die Datei, die der Stub nennt, ist weg (umbenannt oder entfernt): Leeren Sie die Template-Caches |
| `the Smarty library is not available (...)` | Smarty 5 ist weder in Composers Autoload noch in `LibraryPath` |
| `invalid parameter name for <function>: <name>` | eine Template-Funktion wurde mit einem Parameternamen aufgerufen, der kein einfacher Name ist |
| `<!-- smarty: unsupported characters in the template file name -->` | der Dateiname enthält ein Anführungszeichen, einen Backslash, eine geschweifte Klammer oder einen Zeilenumbruch; benennen Sie die Datei um |

Smartys Kompilierfehler lauten `Syntax error in template "<file>"  on line <n> "<the line>" <reason>`. Die häufigen:

| Grund | Abhilfe |
|---|---|
| `missing 'from' attribute` | ein `{foreach}` über einen Ausdruck: Weisen Sie ihn zuerst einer Variablen zu (Teil 7) |
| `unexpected closing tag` | ein schließendes Tag ohne sein öffnendes Tag: ein `.tpl`-Tag, das bei einer Portierung von Hand übrig blieb (`{/section}`, `{/let}`, `{/cache-block}`), oder ein schließendes Tag, dessen öffnendes Tag Smarty nicht als solches gelesen hat |
| `unclosed {if} tag`, `unclosed '{if}' tag` | das Gegenteil: ein Tag, das nie geschlossen wird, oder falsch herum verschachtelte Tags |
| `unknown modifier 'x'` | kein Operator `x` in diesem Siteaccess (eine Erweiterung, die ihn registriert, ist dort nicht aktiv) oder ein Tippfehler |
| `unknown tag 'x'` | keine Template-Funktion `x`, oder ein `.tpl`-Tag wie `{def}` oder `{set-block}` ist in der Datei geblieben |
| ` - Unexpected "..."` | Smarty-Syntaxfehler; oft eine `.tpl`-Gewohnheit: Modifier-Parameter in Klammern (`$x|i18n('ctx')` muss `$x|i18n:'ctx'` sein) oder eine geschweifte Klammer in CSS oder JavaScript (unten) |

Zwei Probleme, die keine Kompilierfehler sind:

- **Geschweifte Klammern in CSS und JavaScript.** Ein `{`, auf das etwas anderes als Leerraum folgt, beginnt ein
  Smarty-Tag. Schreiben Sie `{ldelim}` (wie der Konverter: `body {ldelim}margin:0;}`), setzen Sie ein Leerzeichen nach
  die Klammer oder schließen Sie den Code in `{literal}...{/literal}` ein.
- **"Array to string conversion".** Ein Array wurde ausgegeben oder übergeben, wo Text erwartet wurde. Meistens lief
  ein Smarty-Modifier statt des gleichnamigen Exponential-Operators: Smartys `explode` nimmt seine Parameter in
  umgekehrter Reihenfolge, sein `implode` ist veraltet, sein `count` zählt einen String als 1. Verwenden Sie
  `exp_explode`, `exp_implode`, `exp_count` ([das Präfix `exp_`](smarty-templates-operatoren.md#das-präfix-exp)).

`Debugging=enabled` in `smarty.ini` zeigt Smartys Debug-Konsole mit den Variablen jedes Templates. Schalten Sie es
wieder aus; lassen Sie es auf einer öffentlichen Website nie an.

## 12. Velocity und dauerhafte Worker

Warum Velocity für `.stpl`-Seiten einen solchen Unterschied macht, mit Messungen:
[Velocity und PHP-FPM](smarty-templates-leistung.md#velocity-und-php-fpm).

Unter Velocity oder jedem Server, der PHP-Prozesse zwischen Anfragen behält:

- Die Smarty-Instanz wird pro Prozess gehalten und durch ihre Konfiguration identifiziert: die Template-Verzeichnisse
  der Designs, das Kompilierverzeichnis, `CompileCheck` und `Debugging`. Siteaccesses mit anderen Designs bekommen
  eigene Instanzen. Die Instanz wird allein über die Design-Basen und das Cache-Verzeichnis gefunden, ohne
  `smarty.ini` erneut zu lesen, sodass eine Änderung von `CompileCheck` oder `Debugging` einen Worker erst nach einem
  Neustart erreicht (ein Deploy erledigt das).
- Wie ein Operator seine Eingabe nimmt ([die Eingaberegeln](smarty-templates-operatoren.md#wie-ein-operator-seine-eingabe-bekommt))
  wird einmal pro Operator und Prozess ermittelt. Nach einer Änderung an einer Operator-Klasse starten Sie die Worker
  neu (ein Deploy erledigt das).
- Wofür ein Name als Modifier, Funktion oder Block steht, wird einmal pro Prozess nachgeschlagen
  (`expSmartyExtension` merkt sich die Antwort, auch ein "nicht gefunden"). Ein Operator oder eine Template-Funktion,
  die später verfügbar wird (eine aktivierte Erweiterung), wird nach einem Neustart erkannt.
- Von den Variablen einer Anfrage wird nichts für die nächste behalten: Jedes Rendern bekommt die Variablen seiner
  eigenen Anfrage. Die während einer Seite für Smarty umgewandelten Werte (`expSmartyRenderer::wrapNamed()`) und die am
  Ende jedes Templates erfassten Variablen werden verworfen, wenn das äußerste `.stpl` eines Renderns fertig ist.
- Velocity läuft als `root`, PHP-FPM als Benutzer der Website, und beide schreiben `<Cache-Verzeichnis>/smarty/`.
  Smarty legt seine Verzeichnisse mit `0777` und seine Dateien mit `0666` an, jeweils abzüglich der umask des Prozesses.
  Hat einer von beiden das Verzeichnis angelegt und der andere kann nicht hineinschreiben, scheitert das Rendern mit
  einem Fehler "rendering ... failed" über eine Datei, die nicht geschrieben werden kann: Leeren Sie den Smarty-Cache
  als Benutzer der Website (oder übergeben Sie das Verzeichnis diesem Benutzer), und er wird neu angelegt.

## 13. Grenzen und Unterschiede zu .tpl

- Nur Smarty 5, auf PHP 8. Die Endung ist fest: `.stpl`.
- Durchgehend Smarty-Syntax: Modifier-Parameter folgen auf Doppelpunkte (`$x|op:a:b`), nie in Klammern;
  Array-Literale sind `[...]` und `['key' => value]`; `true`, `false` und `null` sind Literale.
- Smartys eigene Modifier und Tags haben Vorrang vor gleichnamigen Exponential-Operatoren und -Funktionen; die
  Operatoren erreichen Sie mit `exp_`.
- Kein automatisches Maskieren, wie in `.tpl`: Maskieren Sie mit `|wash` (oder Smartys `|escape`).
- Objekte sind schreibgeschützte Proxys; iterieren Sie über Arrays, nicht über Objekte.
- Variablen fließen in den Namensraum zurück, in dem das `.stpl` gerendert wurde; Smartys `{include file=...}` behält
  seine Variablen.
- Eine Template-Funktion mit Rumpf bekommt ihre Kinder als Text.
- Template-Variablen, deren Namen keine einfachen Namen sind, und eine Variable namens `smarty` sind nicht sichtbar.
- Smartys eigenes Ausgabe-Caching ist aus: Das Caching von Seiten bleibt bei Exponential (View-Cache,
  `{expcacheblock}`, der Antwort-Cache von Velocity).

## Verwandte Seiten

- Neu bei `.stpl`: [ein erstes .stpl, Schritt für Schritt](smarty-templates-tutorial.md)
- Referenz: [jeder Operator](smarty-templates-operator-referenz.md), [Template-Funktionen und Blöcke](smarty-templates-funktionen.md), [Problembehebung und FAQ](smarty-templates-problembehebung.md)
- Themen: [Caching und Leistung](smarty-templates-caching.md), [Leistung (Geschwindigkeit)](smarty-templates-leistung.md), [ein Design migrieren](smarty-templates-migrieren.md), [Operatoren und Funktionen schreiben](smarty-templates-plugins.md), [Sicherheit und Maskierung](smarty-templates-sicherheit.md)
- [Smarty-Templates (.stpl) neben .tpl](../funktionen/6.0/smarty-templates.md), die Funktionsseite
- [Smarty-Templates: Operatoren und Fetch-Funktionen](smarty-templates-operatoren.md)
- [Smarty-Templates: der Konverter und die Portierung eines Designs](smarty-templates-konverter.md)
- [Templates und Design](templates-und-design.md) und [Caches](caches.md)
- [Velocity: Exponential in einem Webserver mit dauerhaften Workern betreiben](../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- Die dritte Engine: [Twig-Templates](twig-templates.md); alle drei verglichen in [Die drei Template-Engines im
  Vergleich](template-engines.md), [ihre Geschwindigkeit](template-engines-geschwindigkeit.md) und
  [Templates nebeneinander](../hilfe/template-sprache/tpl-twig-smarty-nebeneinander.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
