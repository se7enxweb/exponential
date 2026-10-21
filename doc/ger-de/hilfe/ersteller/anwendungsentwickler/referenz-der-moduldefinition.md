---
title: Referenz der Moduldefinition
audience: app-creator, developer
category: app-creation
order: 110
summary: Jeder Schlüssel einer Moduldefinition - $Module, Einträge in $ViewList (script, functions, params, unordered_params, Aktionen, Navigationsteil, UI-Kontext) und $FunctionList mit Einschränkungen - samt dem Kernel-Code, der jeden davon liest.
keywords: Moduldefinition, Ansichten, Parameter, Richtlinienfunktionen, Einschränkungen, Anwendungsentwickler, module.php, ViewList, FunctionList, params, unordered_params, single_post_actions, post_actions, post_action_parameters, default_navigation_part, ui_context, functions, limitations, eZModule, app creator
type: help
---
# Referenz der Moduldefinition

Diese Seite richtet sich an Anwendungsentwickler, die `module.php`-Dateien schreiben. Sie listet die Schlüssel auf, die
der Kernel liest, und was jeder bewirkt. Gelesen wird die Datei von `lib/ezutils/classes/ezmodule.php` (`eZModule`);
die Kernel-Module in `kernel/<module>/module.php` sind die besten Beispiele.

## Inhalt

1. [$Module](#module)
2. [Einträge in $ViewList](#einträge-in-viewlist)
3. [$FunctionList und Einschränkungen](#functionlist-und-einschränkungen)
4. [Ein vollständiges Beispiel](#ein-vollständiges-beispiel)

## $Module

```php
$Module = array( 'name' => 'Demo', 'variable_params' => false );
```

| Schlüssel | Bedeutung |
|---|---|
| `name` | der Anzeigename des Moduls |
| `variable_params` | `true` macht jeden Ansichtsparameter zusätzlich zu `$Params` zu einer eigenen Variablen im Ansichtsskript |

## Einträge in $ViewList

| Schlüssel | Bedeutung |
|---|---|
| `script` | die Ansichtsdatei im Modulverzeichnis |
| `functions` | Richtlinienfunktionen, die der Benutzer braucht (eine davon genügt); wird geprüft, bevor das Skript läuft |
| `params` | geordnete Parameter aus der Adresse: `/module/view/<p1>/<p2>` |
| `unordered_params` | benannte Parameter `/(name)/value`, abgebildet auf Schlüssel in `$Params`: `array( 'offset' => 'Offset' )` |
| `single_post_actions` | Name der POST-Schaltfläche auf Aktion: `array( 'StoreButton' => 'Store' )` |
| `post_actions` | POST-Variablen, deren Wert der Aktionsname ist |
| `post_action_parameters` | je Aktion die POST-Variablen, die als Aktionsparameter bereitgestellt werden |
| `default_action` | die Aktion, wenn keine Schaltfläche passte |
| `default_navigation_part` | welcher Reiter im oberen Menü der Administration aktiv ist (`ezcontentnavigationpart`, `ezsetupnavigationpart` ...) |
| `ui_context` | `edit`, `administration`, `browse` ...: teilt den Designs mit, welche Art von Seite es ist |
| `ui_component` | der Name der Komponente zum selben Zweck |

In der Ansicht: `$Params['Module']->isCurrentAction( 'Store' )`, `->hasActionParameter( 'Title' )`,
`->actionParameter( 'Title' )`.

## $FunctionList und Einschränkungen

```php
$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['edit'] = array( 'Section' => $SectionLimitation );
```

Eine Funktion mit leerem Array hat keine Einschränkungen: Eine Rolle hat sie oder hat sie nicht. Eine Einschränkung
wird beschrieben wie in `kernel/content/module.php`:

```php
$SectionLimitation = array(
    'name' => 'Section',
    'values' => array(),
    'class' => 'eZSection',
    'function' => 'fetchList',
    'parameter' => array( false ) );
```

`values` listet feste Auswahlwerte (`array( array( 'Name' => 'Self', 'value' => '1' ) )`); `class`, `function` und
`parameter` benennen eine statische Methode, die die Auswahlwerte für den Rolleneditor liefert. Ihr Code muss die
Einschränkung selbst anwenden ([Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)).

Funktionen erscheinen unter **Benutzer > Rollen > Neue Richtlinie**, sobald das Modul registriert ist und die Caches
geleert sind.

## Ein vollständiges Beispiel

```php
<?php
$Module = array( 'name' => 'Demo' );

$ViewList = array();
$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'params' => array(),
    'unordered_params' => array( 'offset' => 'Offset' ) );
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'edit' ),
    'ui_context' => 'edit',
    'params' => array( 'ItemID' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Title' => 'ItemTitle' ) ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['edit'] = array();
```

## Verwandte Seiten

- [Eigene Module und Ansichten](eigene-module-und-ansichten.md)
- [Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)
- [Muster für Richtlinienfunktionen](../../../../eng-us/standards/patterns/policy-function.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
