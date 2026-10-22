---
title: "Controller: Modul-Views"
audience: web-framework-developer, developer
category: web-frameworks
order: 80
summary: Wo ein Framework Controller und Actions hat, hat Exponential Modul-Views – ein View-Skript oder eine Klasse Exponential\Runnable\ModuleView, die die Adressparameter erhält und ein Ergebnis-Array zurückgibt – wie Sie eine solche View schreiben, sie mit Diensten schlank halten, mit Ereignissen umschließen und eine Kernel-View ändern, ohne sie zu kopieren.
keywords: Controller, controller, Action, action, Modul-View, module view, ModuleView, Exponential\Runnable\ModuleView, Params, Result, eZTemplate, runnable, Implementation, RunnableSettings, schlanker Controller, thin controller, Dienst, service, Web-Framework-Entwickler, web framework developer
type: help
---
# Controller: Modul-Views

Diese Seite richtet sich an Entwickler, die Controller schreiben. In Exponential ist die Einheit, die eine Adresse
bearbeitet, eine **View** eines Moduls. Klassische Views sind PHP-Skripte, die der Kernel mit gesetztem `$Params` und
`$Module` einbindet; seit 6.0.15 schreiben Sie eine View am besten als Klasse, die `Exponential\Runnable\ModuleView`
erweitert, mit einem einzeiligen Skript, das sie aufruft
([Befehle, Cronjob-Teile und Modul-Views als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)).

## Inhalt

1. [Controller und View nebeneinander](#controller-und-view-nebeneinander)
2. [Die Skriptform](#die-skriptform)
3. [Die Klassenform](#die-klassenform)
4. [Schlank bleiben](#schlank-bleiben)
5. [Vor und nach der View](#vor-und-nach-der-view)
6. [Eine Kernel-View ändern](#eine-kernel-view-ändern)

## Controller und View nebeneinander

| Framework-Controller | Modul-View in Exponential |
|---|---|
| Eine Klasse mit Action-Methoden | eine View pro Adresse; in der Klassenform eine Klasse pro View |
| Routenparameter als Argumente | `$Params['ItemID']` (geordnete und ungeordnete Parameter) |
| Injiziertes `Request`-Objekt | `eZHTTPTool::instance()` für GET-, POST- und Sitzungsvariablen; Modulaktionen für Schaltflächen |
| Gibt eine `Response` zurück | gibt ein `$Result`-Array zurück; der Kernel setzt `content` in das Pagelayout ein |
| Autorisierungsattribut oder Middleware | `'functions' => array( 'read' )` in `module.php`, geprüft, bevor die View läuft |
| Helfer zum Rendern von Templates | `eZTemplate::factory()`, `setVariable()`, `fetch( 'design:...' )` |

## Die Skriptform

```php
<?php
// extension/expdemo/modules/expdemo/item.php
$Module = $Params['Module'];
$item = expDemoItem::fetch( (int)$Params['ItemID'] );
if ( !$item )
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'item', $item );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expdemo/item.tpl' );
$Result['path'] = array( array( 'text' => 'Items', 'url' => 'expdemo/list' ),
                         array( 'text' => $item->attribute( 'title' ), 'url' => false ) );
```

`expDemoItem::fetch()` steht für eine statische Methode Ihrer eigenen Persistent-Object-Klasse
([ORM: eZPersistentObject](orm-und-ezpersistentobject.md)). Die Schlüssel des Ergebnisses werden unter
[Responses, Weiterleitungen, Fehler und JSON](responses-weiterleitungen-und-fehler.md) erklärt.

## Die Klassenform

Aus [Eine Modul-View](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md#eine-modul-view):

```php
<?php
// extension/myext/classes/runnable/views/report/show.php
namespace Exponential\View\Extension\Myext\Report;

class Show extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'report', \myReportService::build( $scope['Params']['Month'] ) );
        return array( 'content' => $tpl->fetch( 'design:report/show.tpl' ),
                      'path' => array( array( 'text' => 'Report', 'url' => false ) ) );
    }
}
```

```php
<?php
// extension/myext/modules/report/show.php (named in module.php ViewList as before)
return \Exponential\View\Extension\Myext\Report\Show::main( __FILE__, get_defined_vars() );
```

Die Klasse lässt sich mit Unit-Tests prüfen, durch Ereignisse umschließen und über Einstellungen neu implementieren.
Denken Sie daran, nach dem Hinzufügen der Klasse die Autoloads der Erweiterungen neu zu erzeugen
(`php bin/php/ezpgenerateautoloads.php -e`).

## Schlank bleiben

Der Kernel selbst legt Arbeit, die sich eine View, ein Befehl und ein Cronjob-Teil teilen, in Dienstklassen ab, zum
Beispiel `Exponential\Service\Trash` für die Papierkorb-View, `bin/php/trashpurge.php` und den Cronjob-Teil `trashpurge`
([Dienste](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md#dienste-eine-implementierung-für-die-view-den-befehl-und-den-cronjob-teil)).
Machen Sie es genauso: Eingaben in der View einlesen und prüfen, für die eigentliche Arbeit eine einfache Klasse
aufrufen, das Ergebnis rendern. Diese einfache Klasse testen Sie ohne Datenbank ([Testen](testen.md)).

## Vor und nach der View

Jede klassenbasierte View läuft zwischen zwei Ereignissen: `runnable/view/before` (Benachrichtigung) und
`runnable/view/after` (ein Filter, der das Ergebnis erhält und zurückgeben muss). Registrieren Sie Listener in
`site.ini [RunnableSettings] Listeners[]`. Das kommt Interceptors auf Controller-Ebene wie dem `HandlerInterceptor` von
Spring oder einem Symfony-Listener für `kernel.controller` am nächsten ([Ereignisse und Listener](ereignisse-und-listener.md)).

## Eine Kernel-View ändern

| Bedarf | Mechanismus |
|---|---|
| Das Verhalten einer Kernel-View ändern | `site.ini [RunnableSettings] Implementation[<view class>]=<your subclass>`; die Unterklasse muss die Klasse erweitern, die sie ersetzt |
| Die Views oder Templates eines ganzen Kernel-Moduls ersetzen | ein Modul-Override durch eine Erweiterung ([Modul-Override durch Erweiterungen](../../funktionen/6.0/modulueberschreibung-durch-extensions.md)) |
| Nur das Markup ändern | ein Template-Override, ganz ohne PHP ([Template-Auflösung](template-aufloesung-designs-und-overrides.md)) |

```ini
# settings/override/site.ini.append.php, or an extension's settings/site.ini.append.php
[RunnableSettings]
Implementation[Exponential\View\Kernel\Content\History]=myHistoryView
```

Keine Kernel-Datei wird kopiert, sodass das nächste Release das Original unter Ihrer Unterklasse aktualisiert.

## Verwandte Seiten

- [Eigene Module und Views](../ersteller/anwendungsentwickler/eigene-module-und-ansichten.md)
- [Routing: Module, Views und Parameter](routing-mit-modulen-und-views.md)
- [Templates für Modul-Views](../template-sprache/templates-von-modulansichten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
