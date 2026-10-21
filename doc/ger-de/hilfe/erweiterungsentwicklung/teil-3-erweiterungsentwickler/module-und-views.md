---
title: Module und Views
audience: developer, app-creator
category: extension-development
order: 90
summary: Seiten mit eigener Adresse - module.php und jeder ViewList-Schlüssel, ein View-Skript und eine View-Klasse, POST-Aktionen, Weiterleitungen und Fehler, anhand von eZModule und einer echten Erweiterung.
keywords: Modul, View, Ansicht, Skript, Parameter, ungeordnete Parameter, Weiterleitung, Fehler, POST-Aktion, module, view, module.php, ViewList, FunctionList, script, params, unordered_params, functions, default_navigation_part, post_actions, single_post_actions, eZModule, redirectTo, handleError, ModuleView
type: help
---
# Module und Views

Teil 3, Kapitel 9. Jede Seite von Exponential, die keine Inhaltsseite ist, ist eine **View** eines **Moduls**:
`/user/login`, `/setup/extensions`, `/owner/change/123`. Eine Erweiterung fügt Seiten hinzu, indem sie Module
hinzufügt. Dieses Kapitel erklärt die Definitionsdatei, das View-Skript und das, was eine View zurückgibt. Die Klasse,
die das alles ausführt, ist `eZModule` (`lib/ezutils/classes/ezmodule.php`).

## Den Modulordner registrieren

In `extension/mysite_hello/settings/module.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=mysite_hello

*/ ?>
```

Jeder Ordner unter `extension/mysite_hello/modules/` ist dann ein Modul. Die Adresse `/hello/world/Ada` führt die View
`world` des Moduls `hello` mit dem Parameter `Ada` aus.

## module.php

Eine echte Definition aus se7enxweb/ezownerchange (`modules/owner/module.php`):

```php
<?php
$Module = array( 'name' => 'Owner', 'variable_params' => false );

$ViewList = array();
$ViewList['change'] = array(
    'script' => 'change.php',
    'functions' => array( 'change' ),
    'params' => array( 'ObjectID' ),
    'unordered_params' => array( 'group' => 'StartNode', 'node' => 'NodeID', 'scope' => 'Scope' ),
    'ui_context' => 'edit',
    'default_navigation_part' => 'ezcontentnavigationpart',
    'post_actions' => array( 'BrowseActionName' ),
    'single_post_actions' => array( 'BrowseCancelButton' => 'Cancel',
                                    'CancelButton' => 'Cancel',
                                    'BrowseOwnerButton' => 'BrowseOwner',
                                    'PreviewButton' => 'Preview',
                                    'ConfirmButton' => 'Confirm' ),
    'post_action_parameters' => array( 'Cancel' => array( 'CancelURI' => 'BrowseCancelURI' ) )
);

$FunctionList = array();
$FunctionList['change'] = array();
$FunctionList['subtree'] = array();
```

| Schlüssel | Bedeutung |
|---|---|
| `$Module['name']` | ein lesbarer Name |
| `$Module['variable_params']` | `true` macht im View-Skript jeden Parameter zusätzlich zu `$Params` zu einer eigenen Variablen |
| `'script'` | die Datei der View, neben `module.php` |
| `'params'` | geordnete Parameter: `/owner/change/123` ergibt `$Params['ObjectID'] = '123'` |
| `'unordered_params'` | benannte Parameter an beliebiger Stelle dahinter: `/(node)/59` ergibt `$Params['NodeID'] = '59'` |
| `'functions'` | die Richtlinienfunktionen, die ein Benutzer braucht, um die View zu öffnen ([Richtlinienfunktionen und Zugriffsprüfungen](richtlinienfunktionen-und-zugriff.md)) |
| `'default_navigation_part'` | welcher Reiter im oberen Menü der Administration aktiv ist |
| `'ui_context'` | der Kontext der Oberfläche (`edit`, `navigation`, ...), den die Designs der Administration auswerten |
| `'single_post_actions'` | der Name einer POST-Schaltfläche und die Aktion, die sie bedeutet |
| `'post_actions'` | POST-Variablen, deren Wert die Aktion benennt |
| `'post_action_parameters'` | POST-Variablen, die je Aktion bereitgestellt werden |
| `$FunctionList` | die Richtlinienfunktionen, die dieses Modul den Rollen anbietet |

## Ein View-Skript

`modules/hello/world.php`:

```php
<?php
/** @var eZModule $Module */
$Module = $Params['Module'];
$name = isset( $Params['Name'] ) && $Params['Name'] !== '' ? $Params['Name'] : 'world';

$tpl = eZTemplate::factory();
$tpl->setVariable( 'name', $name );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/world.tpl' );
$Result['path'] = array( array( 'text' => 'Hello', 'url' => false ) );
```

Das Skript erhält `$Params` (mit dem Modul als `$Params['Module']`) und setzt `$Result`. Das Template maskiert, was
aus der Adresse kam: `{$name|wash}`. Was `$Result` enthalten darf, steht im übernächsten Kapitel,
[View-Ergebnisse und das Pagelayout](view-ergebnisse-und-das-pagelayout.md).

## Eine View als Klasse

Seit 6.0.15 kann eine View eine Klasse sein; das einfache Skript oben funktioniert weiterhin. Die Klasse erweitert
`\Exponential\Runnable\ModuleView` (`kernel/private/classes/runnable/moduleview.php`) und gibt das Ergebnis zurück:

```php
<?php
// extension/mysite_hello/classes/runnable/views/hello/world.php
namespace Exponential\View\Extension\MysiteHello\Hello;

class World extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $name = isset( $scope['Params']['Name'] ) && $scope['Params']['Name'] !== '' ? $scope['Params']['Name'] : 'world';
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'name', $name );
        return array( 'content' => $tpl->fetch( 'design:hello/world.tpl' ),
                      'path' => array( array( 'text' => 'Hello', 'url' => false ) ) );
    }
}
```

und die in `module.php` genannte View-Datei schrumpft auf eine Zeile:

```php
<?php
return \Exponential\View\Extension\MysiteHello\Hello\World::main( __FILE__, get_defined_vars() );
```

Klassen lassen sich ohne Request testen und teilen sich Code mit Befehlen und Cronjob-Teilen; siehe
[Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md).

## Formulare und Aktionen

Eine View, die ein Formular verarbeitet, liest die Aktion, die das Modul aus dem POST abgeleitet hat:

```php
<?php
$Module = $Params['Module'];
$http = eZHTTPTool::instance();

if ( $Module->isCurrentAction( 'Cancel' ) )
{
    return $Module->redirectTo( '/content/dashboard' );
}
if ( $Module->isCurrentAction( 'Confirm' ) )
{
    $comment = $http->postVariable( 'Comment', '' );
    // ... do the work, then redirect so a reload does not post again
    return $Module->redirectToView( 'world', array( 'Ada' ) );
}
```

Mit `single_post_actions` macht ein POST, der `ConfirmButton` enthält, `isCurrentAction( 'Confirm' )` wahr. Formulare,
die etwas ändern, müssen das Formular-Token mitführen (die Erweiterung `ezformtoken` fügt es in Formulare ein und prüft
es); siehe [ezformtoken und CSRF-Schutz](../teil-8-referenz/ezformtoken-und-csrf.md).

## Fehler

```php
<?php
$object = eZContentObject::fetch( (int)$Params['ObjectID'] );
if ( !$object instanceof eZContentObject )
{
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}
if ( !$object->canRead() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
```

`eZError` (`kernel/classes/ezerror.php`) enthält die Codes: `KERNEL_ACCESS_DENIED` (1), `KERNEL_NOT_FOUND` (2),
`KERNEL_NOT_AVAILABLE` (3), `KERNEL_MOVED` (4), `KERNEL_LANGUAGE_NOT_FOUND` (5), `KERNEL_FORM_TOKEN_REFUSED` (6),
`KERNEL_MODULE_NOT_FOUND` (20), `KERNEL_MODULE_VIEW_NOT_FOUND` (21) und weitere. Der Kernel zeigt die passende
Fehlerseite an.

## Prüfen

1. `php -l` für jede Datei.
2. Aktivieren, Caches leeren ([Eine Erweiterung aktivieren](eine-erweiterung-aktivieren.md)).
3. `/hello/world/Ada` öffnen.
4. Wenn gemeldet wird, dass das Modul nicht existiert: `./console exp:ini where module.ini/ModuleSettings/ExtensionRepositories`.

## Ein Kernel-Modul ersetzen

Eine Erweiterung kann ein ganzes Kernel-Modul durch ein eigenes ersetzen; siehe
[Eine Erweiterung kann ein Kernel-Modul ersetzen](../../../funktionen/6.0/modulueberschreibung-durch-extensions.md).
Eine View zu einem vorhandenen Modul fügen Sie mit dem RAD-Werkzeug **View für ein vorhandenes Modul** hinzu.

## RAD

**Assistent für Modul-Extensions** (`/setup/moduleextension`) schreibt ein Modul mit Views, Templates, Richtlinien und
Einstellungen; **Modul-Assistent** (`/setup/modulewizard`) schreibt ein Modul auf Grundlage vorhandener
Datenbanktabellen. Siehe [Mit RAD: ein Modul mit Views](tutorials/rad-modul.md).

## Verwandte Seiten

- Zurück: [Autoloads und Klassenbenennung](autoloads-und-klassennamen.md)
- Weiter: [View-Parameter, ungeordnete Parameter und POST](view-parameter.md)
- [Von Hand: ein Modul mit einer View](tutorials/von-hand-modul-mit-view.md)
- [eZModule und Modulergebnisse](../teil-8-referenz/ezmodule.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
