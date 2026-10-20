---
title: Die persistente Variable, von innen
audience: developer, designer, app-creator
category: development
order: 330
summary: Wie $persistent_variable von einem Template einer Knotenansicht zum Pagelayout gelangt - der Kernel setzt sie auf false, liest sie nach dem Rendern aus dem Wurzel-Namensraum zurück, speichert sie in content_info und im Content-View-Cache und macht sie zu einem Override-Schlüssel - und warum scope='global' zählt, mit Namensräumen auf alpha belegt.
keywords: persistent_variable, persistente Variable, scope global, content_info, View-Cache, Pagelayout, Namensraum, set, Override-Schlüssel, eZNodeviewfunctions, ezpagedata, persistent variable, view cache, pagelayout, namespace, override key
type: help
---
# Die persistente Variable, von innen

Ein Template einer Knotenansicht läuft vor dem Pagelayout und, wenn die Seite aus dem View-Cache kommt, überhaupt nicht. Die persistente
Variable ist der eine Kanal vom Ansichts-Template zum Pagelayout, der in beiden Fällen funktioniert. Die Anleitung für Designer ist
[Die persistente Variable](../../../en/us/help/designers/templates/persistent-variable.md); diese Seite zeigt den Kernel-Code dahinter.

## Inhalt

1. [Der Weg durch den Kernel](#der-weg-durch-den-kernel)
2. [Warum scope='global'](#warum-scopeglobal)
3. [Was den View-Cache übersteht](#was-den-view-cache-übersteht)
4. [Helfer in Erweiterungen](#helfer-in-erweiterungen)
5. [Verwandte Seiten](#verwandte-seiten)

## Der Weg durch den Kernel

Alles in `eZNodeviewfunctions::generateNodeViewData()` (`kernel/classes/eznodeviewfunctions.php`):

1. Bevor das Ansichts-Template läuft: `$tpl->setVariable( 'persistent_variable', false );`
2. Das Template wird geholt: `$Result['content'] = $tpl->fetch( 'design:node/view/' . $viewMode . '.tpl' );`
3. Danach liest der Kernel die Variable aus dem **Wurzel-Namensraum** zurück und speichert sie:

```php
$contentInfoArray['persistent_variable'] = false;
if ( $tpl->variable( 'persistent_variable' ) !== false )
{
    $contentInfoArray['persistent_variable'] = $tpl->variable( 'persistent_variable' );
    $keyArray[] = array( 'persistent_variable', $contentInfoArray['persistent_variable'] );
    $res->setKeys( $keyArray );
}
```

4. `content_info` kommt in `$Result`, sodass das Pagelayout `$module_result.content_info.persistent_variable` liest.
5. Der Wert wird außerdem zum Override-Schlüssel `persistent_variable`, auf den `override.ini` prüfen kann.

Derselbe Schritt des Kernels lässt sich im Renderer der Beispiele nachspielen: die Variable im Wurzel-Namensraum als false definieren (wie
Schritt 1), ein Template einbinden, das sie mit `scope='global'` setzt, und sie dann zurücklesen:

```tpl
{* the kernel does this before it fetches node/view/<viewmode>.tpl *}
{def $persistent_variable = false()}
{include uri='file:var/tmp/examples/tm-persistent/view-part.inc'}

after the view template: body_class={$persistent_variable.body_class}, hide_sidebar={$persistent_variable.hide_sidebar|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
<article>...</article>

after the view template: body_class=article, hide_sidebar=true
```

Der eingebundene Teil (`view-part.inc` neben dem Beispiel) ist:

```tpl
{* stands in for node/view/full.tpl: sets the variable in the global namespace *}
{set scope='global' persistent_variable=hash( 'body_class', 'article', 'hide_sidebar', true() )}
<article>...</article>
```

## Warum scope='global'

`set` ohne Gültigkeitsbereich sucht die Variable im **aktuellen Namensraum** des Templates (Gültigkeitsbereich `relative`,
`lib/eztemplate/classes/eztemplatesetfunction.php`). Das Template der Knotenansicht selbst läuft im Wurzel-Namensraum, ein einfaches
`{set persistent_variable=...}` würde dort also funktionieren. Ein Template aber, das mit `name=` eingebunden wird oder über `node_view_gui`
(Namensraum `NodeView`), `content_view_gui` (`ContentView`) oder `attribute_view_gui` (`ContentAttribute`) läuft, hat einen eigenen Namensraum
(`kernel/common/eztemplateautoload.php` gibt diese an), und dort existiert die Variable nicht:

```tpl
{def $persistent_variable = false()}
{include uri='file:var/tmp/examples/tm-persistent/view-part-local.inc' name=part}

included with name=part, without scope: {if $persistent_variable}set{else}still false{/if}


{include uri='file:var/tmp/examples/tm-persistent/view-part.inc' name=part}

included with name=part, with scope='global': {$persistent_variable.body_class}
```

Ausgabe auf alpha:

```text

included with name=part, without scope: still false
<article>...</article>

included with name=part, with scope='global': article
```

Von der Template-Engine protokolliert:

```text
WARNING set: Variable 'part:persistent_variable' doesn't exist, cannot set
```

Der hier eingebundene Teil setzt die Variable ohne Gültigkeitsbereich:

```tpl
{* the same assignment, without scope='global' *}
{set persistent_variable=hash( 'body_class', 'article' )}
```

`scope='global'` schreibt von überall in den globalen Namensraum (`''`), wo Schritt 3 liest (`scope='root'` meint den Wurzel-Namensraum des
aktuellen Templates, der innerhalb eines include nicht immer derselbe ist). Nutzen Sie es immer, damit die Zuweisung weiter funktioniert, wenn
das Template in ein include verschoben wird.

Beachten Sie, was `set` nicht tut: Es legt nie eine Variable an. Das tut nur `def`, und ein `def` auf einen bereits vorhandenen Namen
protokolliert `already defined` und weist trotzdem zu (`lib/eztemplate/classes/eztemplatedeffunction.php`).

## Was den View-Cache übersteht

`content_info`, mit der persistenten Variablen darin, ist Teil des `$Result`, das der Content-View-Cache speichert. Bei einem Cache-Treffer gibt
`eZNodeviewfunctions::contentViewRetrieve()` das gespeicherte `$Result` zurück und setzt die Override-Schlüssel erneut aus `content_info`,
`persistent_variable` eingeschlossen. Das Ansichts-Template läuft nicht, aber das Pagelayout bekommt den Wert, den es beim Anlegen des Cache
gesetzt hat.

Das ist auch die Grenze: Der Wert ist das, was das Template gesetzt hat, als die Cache-Datei geschrieben wurde. Was pro Besucher oder pro
Anfrage variiert, darf nicht hinein; es würde jedem späteren Besucher ausgeliefert, bis der Cache verfällt.

## Helfer in Erweiterungen

- `ezpagedata_set( key, value )` und `ezpagedata_append( key, value )` der Erweiterung ezwebin (`extension/ezwebin/autoloads/ezpagedata.php`)
  rufen `ezjscPackerTemplateFunctions::setPersistentArray()` von ezjscore auf, das in dieselbe Variable schreibt; `ezpagedata()` im Pagelayout
  führt `content_info.persistent_variable` mit seinen Seitendaten zusammen.
- Die Admin-Designs lesen Schlüssel wie `left_menu` und `extra_menu` daraus (zum Beispiel `design/admin/templates/pagelayout.tpl`).
- `design/standard/templates/page_head.tpl` schreibt ein Tag `<meta name>` pro Eintrag von `$site.meta` und nimmt den Inhalt aus der
  persistenten Variablen, wenn sie einen Schlüssel desselben Namens hat (ein Ansichts-Template kann zum Beispiel seine eigene `description`
  setzen).

## Verwandte Seiten

- [Die persistente Variable](../../../en/us/help/designers/templates/persistent-variable.md)
- [Templates von Modulansichten](templates-von-modulansichten.md)
- [Referenz des Modulergebnisses](referenz-des-modulergebnisses.md)
- [Variablen und def](../../../en/us/help/designers/templates/variables-and-def.md)
- [Template-Caches](template-caches.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
