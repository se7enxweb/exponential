---
title: Eigene Module und Ansichten
audience: app-creator, developer
category: app-creation
order: 100
summary: Die Bildschirme einer Anwendung als Modulansichten bauen - ein Modul registrieren, geordnete und ungeordnete Parameter, POST-Aktionen verarbeiten, ein Ergebnis zurückgeben, weiterleiten, Fehler und die klassenbasierte Form einer Ansicht.
keywords: Modul, Ansicht, Parameter, Aktionen, Ergebnis, Weiterleitung, Fehlerbehandlung, Anwendungsentwickler, module, view, module.php, ViewList, params, unordered_params, single_post_actions, Result, redirectTo, handleError, ModuleView, eZModule, app creator
type: help
---
# Eigene Module und Ansichten

Diese Seite ist für Anwendungsentwickler, die Exponential um Bildschirme ergänzen. Ein **Modul** ist der erste Teil
einer Adresse (`/expdemo/...`), eine **Ansicht** der zweite (`/expdemo/latest/...`). Die `module.php` des Moduls
deklariert seine Ansichten und Richtlinien; jede Ansicht ist ein Skript (oder eine Klasse), das ein Ergebnis
zurückgibt.

## Inhalt

1. [Ein Modul registrieren](#ein-modul-registrieren)
2. [Parameter](#parameter)
3. [Formulare verarbeiten: Aktionen](#formulare-verarbeiten-aktionen)
4. [Das Ergebnis](#das-ergebnis)
5. [Weiterleitungen und Fehler](#weiterleitungen-und-fehler)
6. [Die klassenbasierte Ansicht](#die-klassenbasierte-ansicht)
7. [Eine Ansicht des Kernels überschreiben](#eine-ansicht-des-kernels-überschreiben)

## Ein Modul registrieren

```ini
# extension/expdemo/settings/module.ini.append.php
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
ModuleList[]=expdemo
*/ ?>
```

Module liegen in `extension/expdemo/modules/<module>/`. Die vollständige Liste der Schlüssel von `module.php` steht in
der [Referenz zu module.php](referenz-der-moduldefinition.md).

## Parameter

```php
$ViewList['item'] = array(
    'script' => 'item.php',
    'functions' => array( 'read' ),
    'params' => array( 'ItemID' ),                      // /expdemo/item/42
    'unordered_params' => array( 'offset' => 'Offset' ) // /expdemo/item/42/(offset)/20
);
```

In der Ansicht enthalten `$Params['ItemID']` und `$Params['Offset']` die Werte, als Zeichenketten aus der Adresse:
Wandeln und prüfen Sie sie vor der Verwendung (`(int)`, Positivlisten)
([Eingabeprüfung und Escaping der Ausgabe](eingabepruefung-und-escaping.md)).

## Formulare verarbeiten: Aktionen

Deklarieren Sie die Schaltflächen eines Formulars als Aktionen:

```php
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'edit' ),
    'params' => array( 'ItemID' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Title' => 'ItemTitle' ) ) );
```

```php
$Module = $Params['Module'];
if ( $Module->isCurrentAction( 'Cancel' ) )
    return $Module->redirectTo( '/expdemo/list' );
if ( $Module->isCurrentAction( 'Store' ) && $Module->hasActionParameter( 'Title' ) )
{
    $title = trim( (string)$Module->actionParameter( 'Title' ) );
    // validate, store, then redirect (POST - redirect - GET)
}
```

Formulare müssen mit dem Formular-Token senden, das `ezformtoken` automatisch in Formulare mit `method="post"` einfügt
([Formulare, CSRF-Schutz und HTTP-Eingaben](formulare-csrf-und-http-eingaben.md)).

## Das Ergebnis

```php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'item', $item );
$Result = array(
    'content' => $tpl->fetch( 'design:expdemo/item.tpl' ),
    'path' => array( array( 'text' => 'Items', 'url' => 'expdemo/list' ),
                     array( 'text' => $item->attribute( 'title' ), 'url' => false ) ) );
```

`content` kommt in das Pagelayout, aus `path` werden Brotkrumennavigation und Titel. Weitere Schlüssel und die Wahl des
Pagelayouts: [Ergebnisse von Ansichten, Templates und das Pagelayout](../../../../eng-us/help/creators/app-creators/view-results-and-the-pagelayout.md).

## Weiterleitungen und Fehler

| Bedarf | Aufruf |
|---|---|
| Zu einer Adresse weiterleiten | `return $Module->redirectTo( '/expdemo/list' );` |
| Zu einer anderen Ansicht des Moduls weiterleiten | `$Module->redirectToView( 'list', array() );` |
| Nicht gefunden | `return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );` |
| Zugriff verweigert | `return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );` |

Weiterleitungsziele, die aus der Anfrage stammen, müssen geprüft werden; die Hilfsfunktionen des Kernels für sichere
Weiterleitungen folgen nur Adressen der Website ([Sichere Weiterleitungen](../../../../eng-us/features/6.0/safe-redirects.md)).

## Die klassenbasierte Ansicht

Neuer Code schreibt die Ansicht als Klasse, die `Exponential\Runnable\ModuleView` erweitert; die Ansichtsdatei wird zu
einer Zeile, die sie aufruft. Die Klasse lässt sich testen und per Einstellung durch eine andere Implementierung
ersetzen ([Eine Modulansicht](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md#eine-modul-view)).

## Eine Ansicht des Kernels überschreiben

Eine Erweiterung kann die Templates oder die Ansichten eines Kernel-Moduls überschreiben
([Override von Modulen durch Erweiterungen](../../../../eng-us/features/6.0/extension-module-override.md)). Fügen Sie lieber ein
eigenes Modul hinzu; ein Override muss jedem Kernel-Update folgen.

## Verwandte Seiten

- [Ihre erste Erweiterung](../../../../eng-us/help/creators/app-creators/your-first-extension.md)
- [Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)
- [Muster Modul und Ansicht](../../../../eng-us/standards/patterns/module-view.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
