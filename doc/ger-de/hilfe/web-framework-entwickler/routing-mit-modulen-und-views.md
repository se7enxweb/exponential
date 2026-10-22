---
title: "Routing: Module, Views und Parameter"
audience: web-framework-developer, developer
category: web-frameworks
order: 60
summary: Wie Exponential eine Adresse auf Code abbildet - die Modul/View-Konvention statt einer Routentabelle, geordnete und ungeordnete Parameter, das Registrieren eines Moduls, das Erzeugen von URLs mit ezurl und eZURI::build, HTTP-Methoden und Aktionen sowie das, was Nutzer eines Routers vermissen werden.
keywords: Routing, Routen, Modul, View, ViewList, params, unordered_params, module.ini, ModuleList, ExtensionRepositories, ezurl, eZURI::build, URL-Erzeugung, Router, Web-Framework-Entwickler, routes, module, URL generation, web framework developer
type: help
---
# Routing: Module, Views und Parameter

Diese Seite richtet sich an Entwickler, die eine Routentabelle gewohnt sind (`routes.yaml`, `routes/web.php`,
`urls.py`, `config/routes.rb`). Exponential hat keine Routentabelle. Eine Adresse wird als
`/<module>/<view>/<parameters>` gelesen, und die `module.php` des Moduls legt fest, welche Views es gibt und welche
Parameter sie annehmen. Sprechende Adressen sind eine eigene Schicht, die URL-Aliase, die vor diesem Schritt in eine
Moduladresse übersetzt werden ([Sprechende URLs](url-aliase-und-platzhalter.md)).

## Inhalt

1. [Die Konvention](#die-konvention)
2. [Ein Modul registrieren](#ein-modul-registrieren)
3. [Parameter](#parameter)
4. [HTTP-Methoden und Aktionen](#http-methoden-und-aktionen)
5. [URLs erzeugen](#urls-erzeugen)
6. [Was Nutzer eines Routers vermissen werden](#was-nutzer-eines-routers-vermissen-werden)

## Die Konvention

| Adresse | Modul | View | Parameter |
|---|---|---|---|
| `/content/view/full/2` | `content` | `view` | `full`, `2` |
| `/user/login` | `user` | `login` | keine |
| `/expdemo/item/42/(offset)/20` | `expdemo` | `item` | `ItemID=42`, `Offset=20` |

Bei URI-basierter Siteaccess-Erkennung steht der Name des Siteaccess an erster Stelle (`/admin/content/view/full/2`)
und wird entfernt, bevor das Modul gesucht wird ([Siteaccesses](siteaccesses-hosts-und-mandanten.md)).

## Ein Modul registrieren

Ein Modul ist ein Verzeichnis mit einer `module.php` in einem Modul-Repository. Die Repositories des Kernels sind
`settings/module.ini [ModuleSettings] ModuleRepositories[]` (`kernel`, `kernel/private/modules`); eine Erweiterung
ergänzt ihr eigenes und führt das Modul auf, wodurch es erreichbar wird:

```ini
# extension/expdemo/settings/module.ini.append.php
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
ModuleList[]=expdemo
*/ ?>
```

```php
<?php
// extension/expdemo/modules/expdemo/module.php
$Module = array( 'name' => 'Demo' );

$ViewList = array();
$ViewList['item'] = array(
    'script' => 'item.php',
    'functions' => array( 'read' ),
    'params' => array( 'ItemID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

$FunctionList = array();
$FunctionList['read'] = array();
```

Alle Schlüssel der `$ViewList`-Einträge sind in der [module.php-Referenz](../ersteller/anwendungsentwickler/referenz-der-moduldefinition.md)
aufgeführt. Leeren Sie nach dem Hinzufügen eines Moduls den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und
deployen Sie unter Velocity ([Server und Deployment](server-und-deployment.md)).

## Parameter

| Art | Deklariert als | Adresse | In der View |
|---|---|---|---|
| Geordnet | `'params' => array( 'ItemID' )` | `/expdemo/item/42` | `$Params['ItemID']` ist `'42'` |
| Ungeordnet (benannt) | `'unordered_params' => array( 'offset' => 'Offset' )` | `/expdemo/item/42/(offset)/20` | `$Params['Offset']` ist `'20'` |
| View-Parameter für Templates | jedes `(name)/value`-Paar in Inhalts-Views | `/content/view/full/2/(color)/red` | `$view_parameters.color` in Templates ([View-Parameter](../template-sprache/ansichtsparameter.md)) |

Parameter kommen als Strings an. Die Deklaration kennt keine Typbeschränkungen oder Anforderungen: Casten und
validieren Sie in der View (`(int)`, Allow-Lists) ([Validierung](validierung.md)).

## HTTP-Methoden und Aktionen

Eine View beantwortet jede Methode; es gibt kein `methods: [POST]` in der Deklaration. Formulare werden als
**Aktionen** behandelt: Die View legt fest, welcher POST-Button welche Aktion bedeutet, und der Code fragt
`$Module->isCurrentAction( 'Store' )` ab:

```php
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'manage' ),
    'params' => array( 'ItemID' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Title' => 'ItemTitle' ) ) );
```

Prüfen Sie die Methode selbst, wo es darauf ankommt (ein JSON-Endpunkt, der nur POST annehmen darf), und lassen Sie
`ezformtoken` jeden POST schützen ([Formulare und CSRF](formulare-und-csrf-schutz.md)).

## URLs erzeugen

| Wo | Wie |
|---|---|
| Templates | `{'expdemo/item/42'\|ezurl}` gibt eine in Anführungszeichen gesetzte, maskierte URL mit dem Siteaccess-Präfix aus; `ezurl( 'no' )` ohne Anführungszeichen; `ezurl( 'no', 'full' )` absolut ([URL-Operatoren](../template-operatoren/url-operatoren.md)) |
| Inhalte in Templates | `{$node.url_alias\|ezurl}` verlinkt auf den URL-Alias des Knotens |
| PHP | `eZURI::build( '/expdemo/item/42' )`; `eZURI::build( $url, true )` für eine absolute URL; ein drittes Argument `false` für eine URL, die in einen Header oder in JSON geht ([Für Entwickler](../../anleitungen/urls-und-aliase.md#für-entwickler)) |

Es gibt keine Routennamen: Der Modul/View-Pfad ist der Name. Bündeln Sie die Pfade Ihres Moduls in einer
Hilfsklasse, wenn viele Stellen sie erzeugen.

## Was Nutzer eines Routers vermissen werden

- **Routennamen und Reverse Routing über den Namen**: Erzeugen Sie stattdessen den Modulpfad.
- **Anforderungen, Standardwerte und Konverter pro Parameter**: Erledigen Sie dies in der View.
- **Methodenspezifische Routen**: Verwenden Sie Aktionen oder prüfen Sie die Methode in der View.
- **Annotations- oder Attribut-Routen**: `module.php` ist die einzige Deklaration.
- **Beliebige sprechende Pfade für Ihre Modul-Views**: URL-Aliase verweisen auf Inhaltsknoten; für Ihre eigenen Views
  verwenden Sie globale URL-Aliase oder Platzhalter, um einer Moduladresse eine freundlichere zu geben
  ([Sprechende URLs](url-aliase-und-platzhalter.md)).

## Verwandte Seiten

- [Controller: Modul-Views](controller-als-modul-views.md)
- [Eigene Module und Views](../ersteller/anwendungsentwickler/eigene-module-und-ansichten.md)
- [Das Modul-und-View-Muster](../../standards/muster/modulansicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
