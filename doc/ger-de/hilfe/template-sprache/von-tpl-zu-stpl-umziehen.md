---
title: Von Exponential-Templates (.tpl) zu Exponential-Smarty-Templates (.stpl) umziehen
audience: developer, designer, app-creator
category: templates
order: 55
summary: Exponential rendert Smarty-5-Templates (.stpl) selbst, neben .tpl - wann Sie .stpl wählen und wann Sie bei .tpl bleiben, was sich ändert und was bleibt (Operatoren, fetch, Funktionen, Overrides, Caching, Includes in beide Richtungen, Namensräume), die Konverter in beide Richtungen mit jeder Option, die Markierung, NOTE-Zeilen und Exit-Codes, Hin- und Rückweg, ein Template oder ein ganzes Design, beide im Gleichschritt halten, Beispiele und eine Tabelle nebeneinander.
keywords: Smarty, stpl, tpl, umwandeln, Konverter, exp:template:convert, exp:template:convert:tpl-to-stpl, exp:template:convert:stpl-to-tpl, portieren, migrieren, expinclude, expcacheblock, exp_-Präfix, convert, converter, port, migrate
type: help
---
# Von Exponential-Templates (.tpl) zu Exponential-Smarty-Templates (.stpl) umziehen

Die vorigen Kapitel vergleichen Smarty und die Template-Sprache als zwei verschiedene Welten. Exponential führt
Smarty-Templates aber auch selbst aus: Ein Design kann Smarty-5-Templates mit der Endung `.stpl` neben seinen
`.tpl`-Templates enthalten, und der Kernel rendert sie mit jedem Operator, jeder Fetch-Funktion, jeder
Template-Funktion, jeder Override-Regel und jedem Cache des Template-Systems. Dieses Kapitel ist für Entwickler und
Designer, die `.tpl`-Templates haben und überlegen, sie als `.stpl` zu schreiben oder umzuwandeln, oder die
`.stpl`-Templates haben und wieder `.tpl` brauchen. Es erklärt, wann welche Sprache die bessere Wahl ist, was sich
ändert und was bleibt, und die beiden Konsolenbefehle, die in beide Richtungen umwandeln, mit echter Ausgabe.

Die Beispiele der Abschnitte zur Umwandlung entstanden, indem die Befehle auf einer Entwicklungsinstallation ausgeführt
wurden; die Ausgabe steht so da, wie die Befehle sie geschrieben haben. Adressen verwenden `https://www.example.com`.
Die ausführlichen Anleitungen zu Smarty-Templates gibt es auf Englisch; die Links unten führen dorthin.

## Inhalt

1. [Wann .stpl, wann .tpl](#wann-stpl-wann-tpl)
2. [Was gleich bleibt](#was-gleich-bleibt)
3. [Was sich ändert](#was-sich-ändert)
4. [Nebeneinander](#nebeneinander)
5. [Die beiden Befehle](#die-beiden-befehle)
6. [Jede Option](#jede-option)
7. [Ausgabe, NOTE-Zeilen und Exit-Codes](#ausgabe-note-zeilen-und-exit-codes)
8. [Die Markierung: von Hand geänderte Dateien](#die-markierung-von-hand-geänderte-dateien)
9. [Beispiel: .tpl zu .stpl](#beispiel-tpl-zu-stpl)
10. [Beispiel: .stpl zu .tpl](#beispiel-stpl-zu-tpl)
11. [Hin und zurück: was genau umgewandelt wird](#hin-und-zurück-was-genau-umgewandelt-wird)
12. [Ein Template oder ein ganzes Design](#ein-template-oder-ein-ganzes-design)
13. [Beide im Gleichschritt halten und zurückgehen](#beide-im-gleichschritt-halten-und-zurückgehen)
14. [Verwandte Seiten](#verwandte-seiten)

## Wann .stpl, wann .tpl

Beide Sprachen sind vollständig und rendern dieselben Seiten. Keine muss aktiviert werden, und beide können im selben
Design nebeneinander stehen, Template für Template.

| Wählen Sie `.stpl`, wenn | Bleiben Sie bei `.tpl`, wenn |
|---|---|
| Ihr Team Smarty kennt oder Sie dafür einstellen | Ihr Team die Template-Sprache kennt |
| Sie Infix-Ausdrücke wollen (`$a + 1`, `$a == 2 && $b`), `{foreachelse}`, `@first`, `@iteration` | Ihre Templates geschrieben sind und funktionieren; eine Umwandlung wäre Arbeit ohne sichtbares Ergebnis |
| die Seiten so schnell wie möglich sein sollen: die gemessenen `.stpl`-Seiten waren unter Apache mit PHP-FPM 18 bis 25 % und unter Velocity 32 bis 60 % schneller ([Speed](../../../eng-us/guides/smarty-templates-performance.md)) | Sie von einer Erweiterung abhängen, deren `.tpl`-Templates Sie oft überschreiben (mischen geht trotzdem) |
| ein neues Design von Grund auf beginnt | die Templates von Leuten gepflegt werden, die nur die Template-Sprache kennen |
| | Sie nichts von dem brauchen, was Smarty hinzufügt |

Mischen ist normal: ein `.stpl`-Pagelayout mit `.tpl`-Ansichten oder umgekehrt. Ein `.stpl` bindet ein `.tpl` mit
`{expinclude uri='design:...'}` ein, ein `.tpl` ein `.stpl` mit `{include uri='design:x.stpl'}`.

## Was gleich bleibt

| Bereich | In einem `.stpl` |
|---|---|
| Templates finden | der Design-Fallback und die Regeln in `override.ini` wirken wie bei `.tpl`; `MatchFile` einer Regel darf ein `.stpl` nennen |
| Variablen | ein `.stpl` sieht die Variablen, die ein `.tpl` an seiner Stelle sähe (`$node`, `$view_parameters`, `$module_result` ...); was es zuweist, geht an das aufrufende Template zurück, wie bei `{def}` und `{set}` |
| Objekte | `$node.data_map.body.content`, `$node.path_array`: dieselbe Punkt-Schreibweise, dieselben Attribute |
| Operatoren | jeder Operator des Kernels und der aktiven Erweiterungen, als Smarty-Modifikator oder Funktion, mit dem eigenen Code des Template-Systems |
| fetch | `fetch('content', 'list', [...])`, jede Fetch-Funktion jedes Moduls, dieselben Rechteprüfungen |
| Template-Funktionen | `{attribute_view_gui}`, `{node_view_gui}`, `{content_view_gui}` und jede andere Funktion als Tags; Funktionen mit Inhalt (`{explblock}`) als Blöcke |
| Übersetzungen | `i18n` mit denselben Kontexten und Übersetzungsdateien |
| Caching | der Ansichts-Cache, der Template-Block-Cache (`{expcacheblock}` ist `{cache-block}`), die Ganzseiten-Caches |
| Includes | in beide Richtungen, mit und ohne Namensraum (`name=`) |
| Namensräume | Wurzel-, aktueller und globaler Namensraum sind die des Template-Systems; `{expset scope=...}` und `expvar()` erreichen sie |

## Was sich ändert

| Bereich | `.tpl` | `.stpl` |
|---|---|---|
| Parameter von Modifikatoren | `$x\|op(a, b)` | `$x\|op:a:b` |
| Literale | `array(...)`, `hash(...)`, `true()`, `false()` | `[...]`, `['k' => v]`, `true`, `false`, `null` |
| Ausdrücke | nur Operatoren: `sum($a, 1)`, `eq($a, 2)` | Operatoren oder Smartys Infix: `$a + 1`, `$a == 2` |
| Operatoren, die Smarty auch hat | `count`, `implode`, `explode`, `is_array` ... | mit `exp_`: `exp_count`, `exp_implode`, `exp_explode`, `exp_is_array` |
| Zuweisung | `{def}`, `{set}`, `{undef}`, `{let}`, `{default}` | `{$x = ...}`, `{$x = null}`; `{expset}` für einen Bereich oder Namensraum |
| Ausgabe in eine Variable | `{set-block}`, `{append-block}` | `{capture assign=x}`, `{expsetblock}`, `{expappendblock}` |
| Includes | `{include uri=...}` | `{expinclude uri=...}` (Smartys `{include file=...}` ist Smartys eigenes, ohne Overrides) |
| Schleifen | `{foreach}` mit `offset`, `max`, `reverse`, `sequence`, `{delimiter}`; `{section}`; `{for}`; `{do}`; `{switch}` | `{foreach}` mit `@first`, `@iteration`, `{foreachelse}`; die Helfer `expslice`, `expsequence`, `exprange`; `{if}`-Ketten statt `{switch}` |
| Schleife über einen Ausdruck | `{foreach fetch(...) as $x}` | erst zuweisen: `{$list = fetch(...)}{foreach $list as $x}` |
| Block cachen | `{cache-block}` | `{expcacheblock id=N}` |
| Einmal ausführen | `{run-once}` | `{exprunonce id=N}` |
| Klammern in CSS und JavaScript | `{ldelim}`, `{literal}` | dasselbe; ein `{` mit folgendem Leerraum ist Text |
| Kommentare | `{* ... *}` | dasselbe |
| Maskierung | nichts automatisch; `\|wash` | dasselbe; Smartys `\|escape` gibt es auch, `\|wash` hält beide Sprachen gleich |
| Was ein Template erreicht | Einstellungen, Inhalte, die Anfrage über Operatoren | dasselbe, dazu Smartys eigene Mittel (`{fetch file=...}`, `$smarty.server` ...), die `[SmartySettings] Security=enabled` einschränkt |

Jeder Operator mit seiner `.stpl`-Form: [operator reference](../../../eng-us/guides/smarty-templates-operator-reference.md).
Jedes Tag: [template functions and blocks](../../../eng-us/guides/smarty-templates-functions.md).

## Nebeneinander

| Konstrukt | `.tpl` | `.stpl` |
|---|---|---|
| Ausgabe, maskiert | `{$node.name\|wash}` | `{$node.name\|wash}` |
| Übersetzung mit Argumenten | `{'%count items'\|i18n( 'design/mysite', , hash( '%count', $n ) )}` | `{'%count items'\|i18n:'design/mysite':expvoid():(['%count' => $n])}` |
| Link | `<a href={$node.url_alias\|ezurl}>` | `<a href={$node.url_alias\|ezurl}>` |
| Link aus Teilen | `<a href={concat( '/content/edit/', $id )\|ezurl}>` | `<a href={(concat('/content/edit/', $id))\|ezurl}>` |
| Definieren | `{def $limit = 10}` | `{$limit = 10}` |
| Ändern | `{set $limit = 20}` | `{$limit = 20}` |
| Entfernen | `{undef $limit}` | `{$limit = null}` |
| Globale Variable | `{set scope=global persistent_variable=hash( 'left_menu', false() )}` | `{expset name='persistent_variable' value=['left_menu' => false] scope='global'}` |
| Globale Variable lesen | `$#exp_dynamic_parts` | `expvar('exp_dynamic_parts', '')` |
| fetch | `{def $list = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 10 ) )}` | `{$list = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 10])}` |
| Bedingung | `{if and( $a, $b\|gt( 0 ) )}` | `{if and($a, $b\|gt:0)}` oder `{if $a && $b > 0}` |
| Zählen | `{$list\|count}` | `{$list\|exp_count}` |
| Schleife mit Trenner | `{foreach $list as $x}{delimiter}, {/delimiter}{$x}{/foreach}` | `{foreach $list as $x}{if !$x@first}, {/if}{$x}{/foreach}` |
| Schleife, leerer Fall | `{if $list\|count}{foreach ...}...{/foreach}{else}None{/if}` | `{foreach $list as $x}...{foreachelse}None{/foreach}` |
| Zählschleife | `{for 1 to 5 as $i}...{/for}` | `{foreach exprange(1, 5) as $i}...{/foreach}` |
| Include | `{include uri='design:parts/paging.tpl' item_limit=10}` | `{expinclude uri='design:parts/paging.tpl' item_limit=10}` |
| Template-Funktion | `{attribute_view_gui attribute=$node.data_map.body}` | `{attribute_view_gui attribute=$node.data_map.body}` |
| Ausgabe in eine Variable | `{set-block variable=subject}...{/set-block}` | `{capture assign=subject}...{/capture}` |
| Cache-Block | `{cache-block keys=array( $node.node_id )}...{/cache-block}` | `{expcacheblock id=1 keys=[$node.node_id]}...{/expcacheblock}` |
| Attribut eines Ausdrucks | `{fetch( 'shop', 'basket' ).items}` | `{(fetch('shop', 'basket'))\|expattr:'items'}` |

## Die beiden Befehle

| Befehl | Wandelt um | Skript | Die Arbeit macht |
|---|---|---|---|
| `./console exp:template:convert:tpl-to-stpl` | jedes `.tpl` in ein `.stpl`, das dasselbe ausgibt | `bin/php/exptemplateconverttpltostpl.php` | `expSmartyConverter`, gesteuert von `expTemplateDesignConverter` |
| `./console exp:template:convert:stpl-to-tpl` | jedes `.stpl` in ein `.tpl`, das dasselbe ausgibt | `bin/php/exptemplateconvertstpltotpl.php` | `expSmartyReverseConverter`, gesteuert von `expTemplateDesignConverter` |

Beide sind der allgemeine Befehl `./console exp:template:convert` mit festen Engines: `--from-engine=tpl
--to-engine=stpl` und umgekehrt (`exp` und `exponential` sind andere Namen für `tpl`, `smarty` für `stpl`). Die beiden
Konverter liegen in `lib/smarty/classes/`, `expTemplateDesignConverter` in `lib/templatebridge/classes/`; die Klassen
der Befehle (`kernel/private/classes/commands/exptemplateconvert*.php`) geben ihm nur ihre Optionen weiter, also lässt
sich dieselbe Arbeit aus PHP aufrufen: `( new expTemplateDesignConverter( 'tpl', 'stpl' ) )->run( $options, $out, $err )`.
Die Befehle für alle Engines sind einmal in [Templates zwischen Template-Engines konvertieren](../../anleitungen/template-konvertierung.md)
beschrieben.

**tpl zu stpl** liest jedes `.tpl` mit dem eigenen Parser des Template-Systems (`eZTemplateMultiPassParser`), sieht also
den Baum, den das Template-System ausführen würde, mit seinen Regeln für Leerraum, und schreibt jeden Knoten als Smarty.
Bevor es ein Ergebnis schreibt, kompiliert es dieses mit Smarty 5 (wenn Smarty installiert ist); ein Ergebnis, das nicht
kompiliert, ist ein Fehler und wird nicht geschrieben.

**stpl zu tpl** zerlegt ein `.stpl` mit einem Tokenizer, der Smartys eigene Regeln befolgt, in Text, Kommentare und
Tags, liest jedes Tag als Ausdrucksbaum und schreibt ihn als `.tpl`. Es liest, was der andere Befehl schreibt, und das
übliche von Hand geschriebene Smarty. Was keine genaue `.tpl`-Form hat, wird nie geraten: Es bleibt als Kommentar
`{* SMARTY ... *}` stehen, der nichts ausgibt, und wird gemeldet. Bevor es ein Ergebnis schreibt, muss der Parser des
Template-Systems es ohne Fehler lesen.

Beide schreiben die Zeilenumbrüche, die die andere Sprache braucht, um genau denselben Text auszugeben: Smarty lässt den
Zeilenumbruch nach einem Tag weg, das nichts ausgibt, das Template-System den Zeilenumbruch nach und vor jedem
Funktions-Tag.

## Jede Option

Die beiden Befehle haben dieselben Optionen; der allgemeine Befehl `exp:template:convert` nimmt dazu `--from-engine`
und `--to-engine`.

| Option | Bedeutung |
|---|---|
| `--from-engine=<tpl\|stpl>`, `--to-engine=<tpl\|stpl>` | nur `exp:template:convert`: die Engine der Quellen und die Engine, in die umgewandelt wird |
| `-s <siteaccess>` | der Siteaccess, dessen Erweiterungen bestimmen, welche Operatoren und Template-Funktionen es gibt und wie jeder Operator seine Eingabe nimmt, und dessen `override.ini` `--override-ini` übernimmt. Nehmen Sie den Siteaccess, der das Design ausliefert |
| `--from=<Verzeichnis>` | das Design-Verzeichnis, das gelesen wird; jede Quelle darunter wird umgewandelt (`templates/` und `override/templates/` gleichermaßen) |
| `--to=<Verzeichnis>` | das Design-Verzeichnis, in das geschrieben wird; jedes Ziel an dieselbe Stelle darunter. Standard: `--from`, also neben die Quelle |
| `--file=<Datei>` | ein einzelnes Template: ein Pfad unter `--from` (`--file=templates/pagelayout.tpl`) oder, ohne `--from`, eine eigene Datei, die daneben geschrieben wird, oder nach `--to`, das dann die Zieldatei nennt |
| `--override-ini=<Datei>` | schreibt eine `override.ini.append.php` für einen Siteaccess, der das `--to`-Design nutzt: jede Override-Regel des `-s`-Siteaccess, mit `MatchFile` auf das umgewandelte Template, wo eines umgewandelt wurde; tpl zu stpl fügt je umgewandeltem `templates/*.tpl` eine Regel hinzu, die das `.stpl` an seine Stelle setzt. Braucht `--from` |
| `--dry-run` | zeigt, was geschrieben würde (`WRITE` neu, `REPLACE` geändert, `SKIP` behalten); nichts wird geschrieben |
| `--force` | ersetzt auch ein Ziel, das von Hand geändert oder geschrieben wurde |
| `--allow-root-user` | als root ausführen (eine allgemeine Option jedes Skripts) |

## Ausgabe, NOTE-Zeilen und Exit-Codes

| Zeile | Bedeutet |
|---|---|
| `NOTE <Template>: <Hinweis>` | ein Konstrukt, das nicht genau umgewandelt werden konnte, oder ein Fehler, den der Parser des Template-Systems in der Quelle fand (`the source has an error: ...`) |
| `WRITE <Datei>`, `REPLACE <Datei>` | ein Ziel neu geschrieben, ein erzeugtes Ziel ersetzt, weil sich seine Quelle geändert hat; mit `--dry-run`: würde |
| `SKIP <Template>: <Datei> was changed after it was generated; --force replaces it` | die Markierung des Ziels passt nicht mehr zu seinem Inhalt |
| `SKIP <Template>: <Datei> has no conversion marker (written by hand); --force replaces it` | das Ziel wurde nicht vom Befehl geschrieben |
| `ERROR <Template>: ...` | die Quelle konnte nicht gelesen oder umgewandelt werden, oder das Ergebnis kompiliert (Smarty) oder parst (Template-System) nicht; für sie wird nichts geschrieben |
| `PASS tpl->stpl <n> templates: <w> written, <u> unchanged, <s> skipped, <e> errors; <m> notes in <k> templates` | die Zusammenfassung, mit der Richtung (`stpl->tpl` in die andere); mit `--dry-run` steht `to write` statt `written` |
| `PASS wrote <Datei>: ...` | `--override-ini` wurde geschrieben |
| `FAIL ...` | Fehler oder falsche Optionen |

| Exit-Code | Wann |
|---|---|
| 0 | `PASS`, auch wenn Dateien übersprungen oder Hinweise ausgegeben wurden |
| 1 | `FAIL`: ein Template konnte nicht gelesen, umgewandelt, geprüft oder geschrieben werden, oder die Override-Datei nicht geschrieben |
| 2 | falsche Optionen: `FAIL --from must be a design directory: ...`, `FAIL --file must be an existing .tpl file: ...`, `FAIL --override-ini needs --from (the design directory)`, `FAIL --from-engine is needed: tpl, stpl` ... |
| 3 | `exp:template:convert` mit zwei Engines, zwischen denen die Installation keinen Konverter hat |

Die Hinweise von tpl zu stpl und was bei jedem zu tun ist, stehen im
[converter guide](../../../eng-us/guides/smarty-templates-converter.md#3-its-notes-and-what-to-check-after-converting).
Ein Hinweis von stpl zu tpl nennt die Zeile, die Smarty-Quelle und den Grund, zum Beispiel:

```
NOTE news_article.stpl: line 1: {$title = $node.data_map.title.content|default:$node.name}: Smarty's default has no exact template operator; kept as a comment
```

## Die Markierung: von Hand geänderte Dateien

Jede Datei, die einer der Befehle schreibt, endet mit einem Kommentar, der die Richtung, die Prüfsumme von allem davor
und die Quelle nennt:

```
{* exp:template:convert tpl->stpl md5:c456cf0887833e135d9cc40a0ca25f7d from tags.tpl -- generated; a file changed by hand is not replaced without --force *}
```

Ein Ziel wird nur ersetzt, solange seine Markierung noch zu seinem Inhalt passt. Ein erneuter Lauf auf eine unveränderte
Quelle lässt die Datei in Ruhe:

```bash
./console exp:template:convert:tpl-to-stpl --file=design/mysite/override/templates/tags.tpl --allow-root-user
```

```
PASS tpl->stpl 1 templates: 0 written, 1 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

nach einer Änderung von Hand an `tags.stpl` wird es übersprungen:

```
SKIP tags.tpl: design/mysite/override/templates/tags.stpl was changed after it was generated; --force replaces it
PASS tpl->stpl 1 templates: 0 written, 0 unchanged, 1 skipped, 0 errors; 0 notes in 0 templates (a skipped file was changed or written by hand: --force replaces it)
```

mit `--dry-run --force` sehen Sie, dass es ersetzt würde:

```
Dry run: nothing is written.
REPLACE design/mysite/override/templates/tags.stpl
PASS tpl->stpl 1 templates: 1 to write, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

und mit `--force` allein wird es ersetzt:

```
REPLACE design/mysite/override/templates/tags.stpl
PASS tpl->stpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Die Markierung ist ein Kommentar, gibt also nichts aus, und sie wird aus einer Quelle entfernt, bevor diese
umgewandelt wird. Löschen Sie die Zeile mit der Markierung, wird die Datei endgültig Ihre: Sie gilt dann als von Hand
geschrieben.

## Beispiel: .tpl zu .stpl

### Eine Vollansicht mit fetch, Funktion, Schleife und Include

Die Quelle, `full_folder.tpl`:

```
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                  'limit', 10,
                                                  'sort_by', $node.sort_array ) )}
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    <p>{'%count items'|i18n( 'design/mysite', , hash( '%count', $children|count ) )}</p>
    {foreach $children as $child}
        {node_view_gui view='line' content_node=$child}
    {/foreach}
    {include uri='design:parts/paging.tpl' item_count=$node.children_count item_limit=10}
</div>
{undef $children}
```

```bash
./console exp:template:convert:tpl-to-stpl --file=design/mysite/override/templates/full_folder.tpl --allow-root-user
```

```
WRITE design/mysite/override/templates/full_folder.stpl
PASS tpl->stpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Das Ergebnis, `full_folder.stpl`:

```smarty
{$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 10, 'sort_by' => $node.sort_array])}
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    <p>{'%count items'|i18n:'design/mysite':expvoid():(['%count' => $children|exp_count])}</p>
    {foreach $children as $child}
        {node_view_gui view='line' content_node=$child}    {/foreach}
    {expinclude uri='design:parts/paging.tpl' item_count=$node.children_count item_limit=10}</div>{$children = null}
{* exp:template:convert tpl->stpl md5:bd6e0a97ddf340e97a9b76fccb0ec0e2 from full_folder.tpl -- generated; a file changed by hand is not replaced without --force *}
```

Was geschah:

- `{def}` wurde eine Zuweisung, `hash()` ein Array-Literal, `{undef}` eine Zuweisung von `null`.
- Der leere Kommentar-Parameter von `i18n` wurde `expvoid()`, `count` wurde `exp_count` (Smarty hat ein `count`).
- `{include}` wurde `{expinclude}`; die URI behält ihren `.tpl`-Namen, die Override-Regeln entscheiden, welche Datei genutzt wird.
- Die Zeilen folgen dem Leerraum, den das Template-System ausgibt: Es lässt den Zeilenumbruch um Funktions-Tags weg,
  daher steht `{/foreach}` in derselben Zeile wie die Zeilenansicht, und der Konverter schreibt genau das, was das
  `.tpl` ausgab.

### Eine Schleife mit delimiter und eine section mit show=

Die Quelle, `tags.tpl`:

```
<p class="tags">{foreach $node.data_map.tags.content.keywords as $index => $keyword}{delimiter}, {/delimiter}<a href={concat( '/content/keyword/', $keyword )|ezurl}>{$keyword|wash}</a>{/foreach}</p>
{section show=$node.can_edit}<a href={concat( '/content/edit/', $node.contentobject_id )|ezurl}>{'Edit'|i18n( 'design/mysite' )}</a>{/section}
```

Das Ergebnis:

```smarty
<p class="tags">{foreach $node.data_map.tags.content.keywords as $index => $keyword}
{if !$keyword@first}
, {/if}
<a href={(concat('/content/keyword/', $keyword))|ezurl}>{$keyword|wash}</a>{/foreach}
</p>{if $node.can_edit}
<a href={(concat('/content/edit/', $node.contentobject_id))|ezurl}>{'Edit'|i18n:'design/mysite'}</a>{/if}
```

Der `{delimiter}` wurde ein `{if !$keyword@first}` am Anfang des Schleifenkörpers, die `{section}` mit nur `show=`
ein `{if}`, und ein Operator auf dem Ergebnis von `concat()` bekam Klammern. Die Zeilenumbrüche nach `{foreach ...}`
und `{if ...}` lässt Smarty weg, die Ausgabe ist also die eine Zeile des `.tpl`.

## Beispiel: .stpl zu .tpl

Der andere Befehl wandelt seine eigene Ausgabe zurück. In eine eigene Datei, damit die `.tpl`-Quelle bleibt, wie sie
ist:

```bash
./console exp:template:convert:stpl-to-tpl --file=design/mysite/override/templates/full_folder.stpl \
    --to=var/tmp/full_folder.tpl --allow-root-user
```

```
WRITE var/tmp/full_folder.tpl
PASS stpl->tpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Aus `full_folder.stpl` oben wird (ohne die Zeile mit der Markierung):

```
{def $children = fetch('content', 'list', hash('parent_node_id', $node.node_id, 'limit', 10, 'sort_by', $node.sort_array))}
<div class="content-view-full class-folder">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    <p>{'%count items'|i18n('design/mysite', , hash('%count', $children|count))}</p>
    {foreach $children as $child}
        {node_view_gui view='line' content_node=$child}    {/foreach}
    {include uri='design:parts/paging.tpl' item_count=$node.children_count item_limit=10}</div>{undef $children}
```

und `tags.stpl` bekommt seinen `{delimiter}` zurück:

```
<p class="tags">{foreach $node.data_map.tags.content.keywords as $index => $keyword}{delimiter}
, {/delimiter}
<a href={concat('/content/keyword/', $keyword)|ezurl}>{$keyword|wash}</a>{/foreach}
</p>{if $node.can_edit}
<a href={concat('/content/edit/', $node.contentobject_id)|ezurl}>{'Edit'|i18n('design/mysite')}</a>{/if}
```

### Von Hand geschriebenes Smarty

Ein von Hand geschriebenes `.stpl`, `news_article.stpl`, mit Infix-Operatoren, `{foreachelse}`, `@first`, einem `{expinclude}` und Smartys
`default`:

```smarty
{$title = $node.data_map.title.content|default:$node.name}
<article class="article">
    <h1>{$node.name|wash}</h1>
    {if $node.data_map.image.has_content && $node.data_map.image.content.is_valid}
        {attribute_view_gui attribute=$node.data_map.image image_class='large'}
    {/if}
    <ul class="related">
    {foreach $node.object.related_contentobject_array as $related}
        {if !$related@first}<li class="sep"></li>{/if}
        <li><a href={$related.main_node.url_alias|ezurl}>{$related.name|wash}</a></li>
    {foreachelse}
        <li>{'Nothing related.'|i18n:'design/mysite'}</li>
    {/foreach}
    </ul>
    {expinclude uri='design:parts/share.stpl' node=$node}
    <p>{$node.children_count + 1} items, {$node.object.published|l10n:'shortdate'}</p>
</article>
```

```bash
./console exp:template:convert:stpl-to-tpl --file=design/mysite/override/templates/news_article.stpl --allow-root-user
```

```
NOTE news_article.stpl: line 1: {$title = $node.data_map.title.content|default:$node.name}: Smarty's default has no exact template operator; kept as a comment
WRITE design/mysite/override/templates/news_article.tpl
PASS stpl->tpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 1 notes in 1 templates
```

Das Ergebnis, `news_article.tpl` (ohne die Zeile mit der Markierung):

```
{* SMARTY (not converted: Smarty's default has no exact template operator): {$title = $node.data_map.title.content|default:$node.name} *}<article class="article">
    <h1>{$node.name|wash}</h1>
    {if and($node.data_map.image.has_content, $node.data_map.image.content.is_valid)}
        {attribute_view_gui attribute=$node.data_map.image image_class='large'}

    {/if}
    <ul class="related">
    {if $node.object.related_contentobject_array|count}{def $__index_related = -1}{foreach $node.object.related_contentobject_array as $related}{set $__index_related = $__index_related|inc}
        {if not(eq($__index_related, 0))}<li class="sep"></li>{/if}
        <li><a href={$related.main_node.url_alias|ezurl}>{$related.name|wash}</a></li>
    {/foreach}{undef $__index_related}{else}
        <li>{'Nothing related.'|i18n('design/mysite')}</li>
    {/if}
    </ul>
    {include uri='design:parts/share.stpl' node=$node}

    <p>{sum($node.children_count, 1)} items, {$node.object.published|l10n('shortdate')}</p>
</article>
```

- `&&` wurde `and()`, `+` wurde `sum()`, `{foreachelse}` ein `{if ...|count}` um die Schleife mit `{else}`.
- `@first` wurde ein Zähler, den das `.tpl` selbst führt (`$__index_related`), weil dieses `{if}` nicht das Erste im
  Schleifenkörper ist (nur dann macht der Konverter aus `{if !$x@first}` einen `{delimiter}`).
- `{expinclude}` wurde `{include}`; die eingebundene Datei ist weiter das `.stpl`, das ein `.tpl` einbinden darf.
- Smartys `default` hat in der Template-Sprache keinen genau entsprechenden Operator: Die Zeile bleibt als Kommentar
  stehen und wird gemeldet. Schreiben Sie sie von Hand neu:
  `{def $title = first_set( $node.data_map.title.content, $node.name )}` kommt nahe heran, ist aber nicht dasselbe
  (`default` ersetzt auch eine leere Zeichenkette).

## Hin und zurück: was genau umgewandelt wird

Das Media-Design (516 Templates, von denen 58 Fehler enthalten, die der Parser des Template-Systems in der Quelle meldet)
wurde von `.tpl` zu `.stpl` und wieder zu `.tpl` umgewandelt:

| Prüfung | Templates |
|---|---|
| das `.tpl` parst ohne Fehler | 516 |
| das `.tpl` erneut umgewandelt ergibt wieder das erste `.stpl` | 512 |
| dieses `.stpl` erneut zurückgewandelt ergibt dasselbe `.tpl` | 512 |
| derselbe Template-Baum wie das Original, bis auf gleichwertige Schreibweisen | 403 |
| derselbe Template-Baum wie das Original | 209 |
| Byte für Byte das Original | 15 |

Die Unterschiede sind Schreibweisen (Leerzeichen in Klammern, `{def}`, wo das Original `{set}` hatte), eine `{section}`
mit nur `show=`, die als `{if}` zurückkommt, und die Fehler der Quelle, die der Parser verwirft; die Ausgabe ist
dieselbe. Jeder Fall der eigenen Tests der Template-Engine (453 Fälle) rendert nach `.tpl` zu `.stpl` zu `.tpl` gleich,
und jeder Fall von Engine und Operatoren gibt als `.stpl` dieselbe Ausgabe
(`tests/tests/lib/smarty/expSmartyOperatorParityTest.php`).

Nie von `.stpl` zu `.tpl` umgewandelt werden: Smartys eigene Modifikatoren ohne genau entsprechenden Operator (`escape`,
`default`, `truncate`, `date_format`, `replace`, `json_encode` ...), `$smarty`, `===` und `!==`, Methodenaufrufe auf
Objekten, statische Klassenmitglieder, Konstanten, eine Zuweisung an ein Array-Element, Smartys `{include file=...}`,
`{section}`, `{strip}`, `{capture name=...}`, `{for}` mit `step` oder `max`. Die vollständige Zuordnung in beide
Richtungen: [converter guide](../../../eng-us/guides/smarty-templates-converter.md#2-what-it-writes).

## Ein Template oder ein ganzes Design

**Ein Template** ist der Anfang: Wandeln Sie das Template um, an dem Sie arbeiten, richten Sie eine Override-Regel
darauf und vergleichen Sie die Seite.

```bash
./console exp:template:convert:tpl-to-stpl -s site --from=design/mysite --file=override/templates/folder.tpl --dry-run --allow-root-user
```

```
Dry run: nothing is written.
WRITE design/mysite/override/templates/folder.stpl
PASS tpl->stpl 1 templates: 1 to write, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Ohne `--dry-run` schreibt der Befehl `design/mysite/override/templates/folder.stpl` neben das `.tpl`. Ändern Sie
`MatchFile` der Regel auf `folder.stpl` und leeren Sie die Template-Caches.

**Ein ganzes Design** kommt in ein eigenes Design, ausgeliefert von einem eigenen Siteaccess, dessen Design-Liste das
Original als Fallback behält:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=extension/sevenx_themes_media/design/smarty_media \
    --override-ini=var/tmp/smarty-override.ini.append.php --allow-root-user
```

```
PASS wrote var/tmp/smarty-override.ini.append.php: 74 rules pointed at .stpl, 444 rules for the design's templates
PASS tpl->stpl 516 templates: 516 written, 0 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Kopieren Sie dann die Override-Datei in den neuen Siteaccess, setzen Sie seine `[DesignSettings]`, machen Sie ihn in
`SiteList[]` und `AvailableSiteAccessList[]` bekannt, geben Sie der Richtlinie `user/login` der Rolle Anonymous den
neuen Siteaccess und leeren Sie die Caches. Das ganze Vorgehen mit den Prüfungen Seite für Seite:
[migrating a design](../../../eng-us/guides/smarty-templates-migrating.md).

## Beide im Gleichschritt halten und zurückgehen

Solange beide bestehen, ist eines davon die Quelle:

- **Das `.tpl`-Design ist die Quelle.** Ändern Sie nur `.tpl`-Dateien und wandeln Sie nach jeder Änderung erneut um.
  Unveränderte Ziele bleiben, wie sie sind, geänderte Quellen ersetzen ihre erzeugten Ziele, von Hand geänderte Ziele
  werden übersprungen. Ein Probelauf listet die `.stpl`-Dateien, die ihrem `.tpl` hinterherhinken (`REPLACE`).
- **Das `.stpl`-Design ist die Quelle.** Ändern Sie nur `.stpl`-Dateien. Wo noch ein `.tpl` gebraucht wird, wandeln Sie
  mit `exp:template:convert:stpl-to-tpl` zurück; was keine `.tpl`-Form hat, wird gemeldet und als Kommentar behalten.

Endgültig von `.stpl` zu `.tpl` zurück ist derselbe Befehl auf das ganze Design, in ein eigenes Design oder neben die
`.stpl`-Dateien, gefolgt von Override-Regeln, die auf die `.tpl` zeigen (`--override-ini` schreibt sie). In das
ursprüngliche `.tpl`-Design zu schreiben ist sicher: Seine eigenen Dateien haben keine Markierung, also wird jede
übersprungen, solange Sie nicht `--force` angeben.

## Verwandte Seiten

- Zurück: [Ein Template von Smarty umziehen](ein-template-von-smarty-umziehen.md)
- Weiter: [Lexikalische Struktur](lexikalische-struktur.md)
- [Smarty templates (.stpl) next to .tpl](../../../eng-us/features/6.0/smarty-templates.md), die Seite zur Funktion (Englisch)
- Anleitungen (Englisch): [a first .stpl, step by step](../../../eng-us/guides/smarty-templates-tutorial.md),
  [the converter and porting a design](../../../eng-us/guides/smarty-templates-converter.md),
  [migrating a design](../../../eng-us/guides/smarty-templates-migrating.md),
  [operators and fetch functions](../../../eng-us/guides/smarty-templates-operators.md),
  [operator reference](../../../eng-us/guides/smarty-templates-operator-reference.md),
  [template functions and blocks](../../../eng-us/guides/smarty-templates-functions.md),
  [caching and performance](../../../eng-us/guides/smarty-templates-caching.md),
  [performance (Speed)](../../../eng-us/guides/smarty-templates-performance.md),
  [troubleshooting and FAQ](../../../eng-us/guides/smarty-templates-troubleshooting.md),
  [security and escaping](../../../eng-us/guides/smarty-templates-security.md)
- [Templates zwischen Template-Engines konvertieren](../../anleitungen/template-konvertierung.md): die Befehle von `exp:template:convert`, jede Option, Ausgabezeile und jeder Exit-Code
- Befehlsreferenz: [exp:template:convert](../../beispiele/befehle/exptemplateconvert.md),
  [exp:template:convert:tpl-to-stpl](../../beispiele/befehle/exptemplateconverttpltostpl.md),
  [exp:template:convert:stpl-to-tpl](../../beispiele/befehle/exptemplateconvertstpltotpl.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
