---
title: Ein eigenes Modul mit Ansicht
audience: developer
category: examples
order: 50
summary: Das Modul expexample mit einer Listenansicht hinzufügen, die einen geordneten und einen ungeordneten URL-Parameter annimmt, und mit einer Richtlinienfunktion mit Sektions-Einschränkung, dazu seine Templates, Einstellungen und die Rolle, die es freigibt.
keywords: Modul, Ansicht, Richtlinie, Richtlinienfunktion, Einschränkung, Sektion, Rolle, URL-Parameter, module, view, module.php, ViewList, params, unordered_params, functions, FunctionList, policy, limitation, Section, ExtensionRepositories, ModuleList, eZModule, Result, handleError, hasAccessTo
type: example
---
# Ein eigenes Modul mit Ansicht

Ein Modul ist der erste Teil einer Adresse, der kein URL-Alias ist: `/content/view/full/2` führt die Ansicht `view`
des Moduls `content` aus. Sie fügen das Modul **expexample** mit zwei Ansichten hinzu:

- `/expexample/list/<NodeID>` und `/expexample/list/<NodeID>/offset/<n>`: die Unterknoten eines Knotens, zehn je
  Seite, für Benutzer mit der Richtlinie `expexample/read`, die sich auf Sektionen einschränken lässt;
- `/expexample/about`: eine Seite für Benutzer mit `expexample/about`.

Voraussetzungen: die Erweiterung aus [Ein Erweiterungsgerüst](erweiterungsgeruest.md), aktiviert;
[Ein eigener Template-Operator](eigener-template-operator.md) für `exp_initials`, den das Listen-Template verwendet
(oder entfernen Sie diese Zeile).

## 1. Dem Kernel das Modul bekannt machen

`extension/expexample/settings/module.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
# Look for modules/<module>/module.php in this extension
ExtensionRepositories[]=expexample
# The known module names: keeps a URL alias from taking over the module's address
ModuleList[]=expexample

*/ ?>
```

## 2. module.php

`extension/expexample/modules/expexample/module.php` deklariert die Ansichten, ihre Parameter und die
Richtlinienfunktionen:

```php
<?php
/**
 * The expexample module: /expexample/list/<NodeID>[/offset/<n>] and /expexample/about.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$Module = array( 'name' => 'Example',
                 'variable_params' => false );

$ViewList = array();

// /expexample/list/2, /expexample/list/2/offset/20: the children of a node, 10 a page
$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'params' => array( 'NodeID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

// /expexample/about: a page without parameters, for users with the policy expexample/about
$ViewList['about'] = array(
    'script' => 'about.php',
    'functions' => array( 'about' ),
    'params' => array() );

// The policy functions of the module, granted in Roles and policies.
// read takes a limitation by section: a role can allow the list of some sections only.
$SectionID = array(
    'name' => 'Section',
    'values' => array(),
    'class' => 'eZSection',
    'function' => 'fetchList',
    'parameter' => array( false ) );

$FunctionList = array();
$FunctionList['read'] = array( 'Section' => $SectionID );
$FunctionList['about'] = array();
```

| Schlüssel | Bedeutung |
|---|---|
| `params` | geordnete Parameter: die nächsten Segmente der Adresse, als `$Params['NodeID']` |
| `unordered_params` | `<Name in der Adresse>` => `<Name in $Params>`: `/offset/20` an beliebiger Stelle nach den geordneten ergibt `$Params['Offset']` = `20`, `false`, wenn er fehlt |
| `functions` | die Richtlinienfunktionen, die ein Benutzer braucht; der Kernel prüft sie, bevor das Skript läuft |
| `default_navigation_part` | der Reiter der Administration, der hervorgehoben wird |
| `$FunctionList[<function>]` | die Einschränkungen, die eine Richtlinie dieser Funktion haben kann; ein leeres Array: keine |

Ansichtsparameter in Klammern (`/(offset)/20`, `/(color)/red`) brauchen keine Deklaration: Sie kommen in
`$Params['UserParameters']` an und in Templates als `$view_parameters`.

Eine Ansicht ohne `functions` kann jeder öffnen, der die Website erreicht. Deklarieren Sie eine Funktion für jede
Ansicht, die irgendetwas zeigt, das nicht öffentlich ist.

## 3. Die Ansichten

`extension/expexample/modules/expexample/list.php`:

```php
<?php
/**
 * The view expexample/list: the children of a node the current user may read.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$Module = $Params['Module'];
$nodeID = (int)$Params['NodeID'];
$offset = max( 0, (int)$Params['Offset'] );   // false when the address has no /offset/<n>
$limit  = 10;

$node = eZContentObjectTreeNode::fetch( $nodeID );
if ( !$node instanceof eZContentObjectTreeNode )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
if ( !$node->canRead() )
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

// The section limitation of expexample/read: the policy may allow some sections only
$access = eZUser::currentUser()->hasAccessTo( 'expexample', 'read' );
if ( $access['accessWord'] === 'limited' )
{
    $allowed = array();
    foreach ( $access['policies'] as $limitations )
    {
        if ( !isset( $limitations['Section'] ) )
        {
            $allowed = false;   // a policy without the limitation allows every section
            break;
        }
        $allowed = array_merge( $allowed, $limitations['Section'] );
    }
    if ( $allowed !== false && !in_array( $node->attribute( 'object' )->attribute( 'section_id' ), $allowed ) )
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$children = $node->subTree( array( 'Depth' => 1,
                                   'DepthOperator' => 'eq',
                                   'Offset' => $offset,
                                   'Limit' => $limit,
                                   'SortBy' => $node->sortArray() ) );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'node', $node );
$tpl->setVariable( 'children', $children );
$tpl->setVariable( 'offset', $offset );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'total', $node->subTreeCount( array( 'Depth' => 1, 'DepthOperator' => 'eq' ) ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expexample/list.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'extension/expexample', 'Example list' ), 'url' => false ),
                         array( 'text' => $node->attribute( 'name' ), 'url' => false ) );
```

`subTree()` prüft `content/read` für jeden Unterknoten selbst, sodass die Liste nur enthält, was der Benutzer sehen
darf. `hasAccessTo()` antwortet mit den Richtlinien des Benutzers `yes`, `no` oder `limited`; `no` hat der Kernel
bereits abgewiesen.

`extension/expexample/modules/expexample/about.php`:

```php
<?php
/**
 * The view expexample/about.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$info = expexampleInfo::info();

$tpl = eZTemplate::factory();
$tpl->setVariable( 'version', $info['Version'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expexample/about.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'extension/expexample', 'About the example' ), 'url' => false ) );
```

`$Result['content']` ist der Seiteninhalt, um den das Pagelayout herum gelegt wird; `$Result['path']` ist die
Brotkrumennavigation.

## 4. Die Templates

`extension/expexample/design/standard/templates/expexample/list.tpl`:

```tpl
{* The view expexample/list. Variables: $node, $children, $offset, $limit, $total *}
<section class="expexample-list">
    <h1>{$node.name|wash}</h1>
    <p>{'%count items'|i18n( 'extension/expexample', '', hash( '%count', $total ) )}</p>
    <ul>
    {foreach $children as $child}
        <li>
            <a href={$child.url_alias|ezurl}>{$child.name|wash}</a>
            <span class="expexample-initials">{$child.name|exp_initials}</span>
        </li>
    {/foreach}
    </ul>
    <nav class="expexample-pages">
    {if $offset|gt( 0 )}
        <a href={concat( 'expexample/list/', $node.node_id, '/offset/', max( sub( $offset, $limit ), 0 ) )|ezurl}>{'Previous'|i18n( 'extension/expexample' )}</a>
    {/if}
    {if sum( $offset, $limit )|lt( $total )}
        <a href={concat( 'expexample/list/', $node.node_id, '/offset/', sum( $offset, $limit ) )|ezurl}>{'Next'|i18n( 'extension/expexample' )}</a>
    {/if}
    </nav>
</section>
```

`extension/expexample/design/standard/templates/expexample/about.tpl`:

```tpl
{* The view expexample/about. Variables: $version *}
<section class="expexample-about">
    <h1>{'About the example'|i18n( 'extension/expexample' )}</h1>
    <p>{'Version %version'|i18n( 'extension/expexample', '', hash( '%version', $version|wash ) )}</p>
</section>
```

Templates einer Ansicht, die in der Administration angezeigt wird, folgen den Gestaltungsregeln der Administration:
gemeinsame Komponentenklassen, kein `<style>` und kein `style=""`, kein Inline-Skript.

## 5. Die Richtlinie vergeben

1. Leeren Sie die Caches und erzeugen Sie die Autoloads neu:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

2. **Benutzer > Rollen und Richtlinien**: Bearbeiten Sie eine Rolle, **Neue Richtlinie**, Modul *expexample*,
   Funktion *read*; setzen Sie eine Einschränkung nach **Sektion** oder keine; speichern Sie. Weisen Sie die Rolle den
   Benutzern oder Gruppen zu.
3. Für Besucher der öffentlichen Website geben Sie auch der Rolle *Anonymous* `expexample/read`; andernfalls werden
   sie aufgefordert, sich anzumelden.

Prüfen Sie in der Shell, welche Benutzer sie haben: `./console exp:access:check` (siehe
[Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)).

## 6. Prüfen

- `/expexample/list/2` listet die Unterknoten des Wurzelknotens auf, jeweils zehn; **Weiter** führt zu
  `/expexample/list/2/offset/10`.
- Ein Benutzer, dessen Richtlinie auf eine andere Sektion eingeschränkt ist, erhält die Seite „Zugriff verweigert“.
- `/expexample/about` zeigt die Version; ohne die Richtlinie die Anmeldeseite oder „Zugriff verweigert“.
- `/expexample/nothing` beantwortet der Kernel mit seiner Seite *Sicht nicht gefunden*.

Für diese Seite geprüft (2026-10-18): `php -l` für die drei PHP-Dateien; `bin/php/eztemplatecheck.php` meldet beide
Templates als gültig (Syntax). Das Modul wurde für diese Seite nicht gegen eine Datenbank ausgeführt.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| *Modul nicht gefunden* | `ExtensionRepositories[]` fehlt in `module.ini.append.php` der Erweiterung, die Erweiterung ist inaktiv, oder der INI-Cache |
| Jeder kann die Ansicht öffnen | die Ansicht hat keinen Eintrag `functions` |
| `$Params['Offset']` ist immer `false` | die Adresse verwendet `/(offset)/10` (ein Ansichtsparameter, in `UserParameters`) statt `/offset/10` |
| Die Richtlinie bietet keine Einschränkung *Sektion* zur Auswahl | `$FunctionList['read']` ist `array()`; das Array der Einschränkung muss `class` und `function` nennen, die die Werte liefern |

## Abgeleitet aus

- `kernel/exphelp/module.php`, `kernel/section/module.php`, `kernel/content/module.php` (die Einschränkung `$SectionID`)
- `lib/ezutils/classes/ezmodule.php` (`params`, `unordered_params`, `UserParameters`)
- `kernel/classes/ezcontentobjecttreenode.php` (`fetch()`, `canRead()`, `subTree()`, `subTreeCount()`, `sortArray()`)
- `doc/eng-us/guides/extensions.md`, Abschnitt 6.1; `doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md` (die
  Klassenform einer Ansicht)

## Verwandte Seiten

- [Kochbuch](README.md)
- [Modulansichten](../modulansichten/README.md) und [Richtlinienfunktionen](../richtlinien/README.md): jede Ansicht
  und Funktion des Kernels, aus dem Code erzeugt
- [Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md#a-module-view):
  dieselbe Ansicht als Klasse geschrieben
- [Eine Erweiterung kann ein Kernel-Modul ersetzen](../../../eng-us/features/6.0/extension-module-override.md),
  [Anfrageregeln](../../../eng-us/features/6.0/request-rules.md)
- [Übersetzungen](uebersetzungen.md) für die Texte der Templates
- Die Programmierregeln und der Musterkatalog: [die Standards](../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
