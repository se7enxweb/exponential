---
title: eZModule und Modulergebnisse
audience: developer
category: extension-development
order: 130
summary: Die Arrays in module.php, die $Params, die eine View erhält, das $Result, das sie zurückgibt, und die eZModule-Methoden, die Views verwenden - redirectTo(), redirectToView(), handleError(), isCurrentAction(), actionParameter().
keywords: Modul, View, Modulergebnis, Weiterleitung, Formularaktionen, Richtlinienfunktionen, eZModule, module.php, ViewList, FunctionList, Params, Result, redirectTo, redirectToView, handleError, isCurrentAction, actionParameter, currentAction, view, module result
type: help
---
# eZModule und Modulergebnisse

Teil 8, Referenz 13. Ein Modul ist ein Ordner unter `modules/` mit einer `module.php` und einem Skript (oder einer
Klasse) je View. `eZModule` (`lib/ezutils/classes/ezmodule.php`) lädt es und führt die Views aus. Das Tutorial ist
[Module und Views](../../../../eng-us/help/extension-development/part-3-extension-developer/modules-and-views.md); diese Seite ist die Referenz.

## module.php

| Variable | Enthält |
|---|---|
| `$Module` | `array( 'name' => 'myext' )`, optional `'variable_params' => true` |
| `$ViewList` | einen Eintrag je View: `'script'`, `'params'` (geordnete Parameter), `'unordered_params'`, `'functions'` (Richtlinienfunktionen), `'default_navigation_part'`, `'single_post_actions'`, `'post_action_parameters'`, `'ui_context'` |
| `$FunctionList` | die Richtlinienfunktionen des Moduls mit ihren Einschränkungen |

## Was eine View erhält

Ein View-Skript findet `$Params` vor:

| Schlüssel | Enthält |
|---|---|
| `$Params['Module']` | das `eZModule`-Objekt |
| `$Params['<Name>']` | jeden geordneten Parameter, der in `'params'` genannt ist |
| `$Params['UserParameters']` | die Paare `(name)/value` der URL ([View-Parameter](../../../../eng-us/help/extension-development/part-3-extension-developer/view-parameters.md)) |

## Was eine View zurückgibt

Eine View setzt `$Result` (oder gibt es als Klasse aus `run()` zurück):

| Schlüssel | Bedeutung |
|---|---|
| `content` | der Seiteninhalt |
| `path` | der Brotkrumenpfad, eine Liste von `array( 'text' => ..., 'url' => ... )` |
| `navigation_part` | der aktive obere Reiter in der Administration |
| `pagelayout` | `false` für keinen Rahmen oder ein anderes Pagelayout-Template |
| `content_info` | bei Inhalts-Views Informationen über den Knoten |

Einzelheiten: [View-Ergebnisse, der Pfad und das Pagelayout](../../../../eng-us/help/extension-development/part-3-extension-developer/view-results-and-the-pagelayout.md).

## eZModule-Methoden, die Views verwenden

| Methode | Verwendung |
|---|---|
| `redirectTo( $uri )` | auf eine Adresse der Website weiterleiten; `return $Module->redirectTo( '/myext/list' );` |
| `redirectToView( $viewName = '', $parameters = array(), ... )` | auf eine andere View desselben Moduls weiterleiten |
| `redirect( $moduleName, $viewName, $parameters = array(), ... )` | auf eine View eines anderen Moduls weiterleiten |
| `handleError( $errorCode, $errorType = false, $parameters = array(), $userParameters = false )` | die Fehlerseite des Kernels anzeigen; `return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );` |
| `isCurrentAction( $actionName, $view = '' )` | ob eine über `single_post_actions` zugeordnete Formularschaltfläche gedrückt wurde |
| `actionParameter( $parameterName, $view = '' )`, `hasActionParameter( ... )` | die über `post_action_parameters` zugeordneten Werte |
| `currentView()`, `currentAction( $view = '' )` | die aktuelle View und Aktion |
| `setTitle( $title )` | den Seitentitel |

Statische Hilfsmethoden: `eZModule::exists( $moduleName, ... )` und `eZModule::findModule( $moduleName, ... )` finden
ein Modul über seinen Namen.

## Formulare mit Aktionen

```php
// module.php
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'params' => array( 'ItemID' ),
    'functions' => array( 'manage' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Name' => 'Name' ) ),
);
```

```php
<?php
// edit.php
$Module = $Params['Module'];
if ( $Module->isCurrentAction( 'Cancel' ) )
{
    return $Module->redirectTo( '/myext/list' );
}
if ( $Module->isCurrentAction( 'Store' ) )
{
    $name = trim( (string)$Module->actionParameter( 'Name' ) );
    // validate and store
}
```

## Fehlercodes

`eZError` (`kernel/classes/ezerror.php`) enthält die Codes für `handleError()`: siehe
[Fehlercodes und Modulergebnisse](fehlercodes-und-modulergebnisse.md).

## Verwandte Seiten

- Zurück: [eZTemplate aus PHP](eztemplate.md)
- Weiter: [eZScript und eZCLI](ezscript-und-ezcli.md)
- [Module und Views](../../../../eng-us/help/extension-development/part-3-extension-developer/modules-and-views.md)
- [Richtlinienfunktionen und Zugriff](../../../../eng-us/help/extension-development/part-3-extension-developer/policy-functions-and-access.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
