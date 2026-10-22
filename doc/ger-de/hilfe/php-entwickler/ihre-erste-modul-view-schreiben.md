---
title: Schreiben Sie Ihre erste Modul-View
audience: php-developer, developer
category: php-development
order: 210
summary: Eine Schritt-für-Schritt-Anleitung - eine Erweiterung mit einem Modul anlegen, eine View und eine Richtlinienfunktion in module.php deklarieren, die View als Klasse implementieren, ein Template rendern, einer Rolle die Richtlinie geben, Autoloads neu generieren und /acmereport/list im Browser öffnen.
keywords: erstes Modul, Modul-View, Tutorial, Anleitung, Richtlinienfunktion, first module, module view, tutorial, task guide, module.php, ViewList, FunctionList, ModuleView, Exponential\View\Extension, eZTemplate::factory, Result, policy function, ActiveExtensions, module.ini, ExtensionRepositories
type: help
---
# Schreiben Sie Ihre erste Modul-View

Diese Anleitung richtet sich an PHP-Entwickler, die eine neue Seite unter einer eigenen Adresse möchten. Am Ende haben
Sie eine Erweiterung `acmereport` mit einem Modul `acmereport` und einer View `list` unter `/acmereport/list`,
geschrieben als Klasse, durch eine Richtlinie geschützt und ein Template rendernd. Ersetzen Sie `acme` durch Ihr eigenes
Präfix ([Klassenbenennung und das Präfix exp](klassenbenennung-und-das-praefix-exp.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritt 1: die Erweiterung und ihre Aktivierung](#schritt-1-die-erweiterung-und-ihre-aktivierung)
3. [Schritt 2: das Modul registrieren](#schritt-2-das-modul-registrieren)
4. [Schritt 3: die View und die Richtlinie deklarieren](#schritt-3-die-view-und-die-richtlinie-deklarieren)
5. [Schritt 4: die View-Klasse](#schritt-4-die-view-klasse)
6. [Schritt 5: die View-Datei](#schritt-5-die-view-datei)
7. [Schritt 6: das Template](#schritt-6-das-template)
8. [Schritt 7: Autoloads, Caches, eine Richtlinie](#schritt-7-autoloads-caches-eine-richtlinie)
9. [Schritt 8: prüfen](#schritt-8-prüfen)
10. [Wie es weitergeht](#wie-es-weitergeht)

## Bevor Sie beginnen

Sie brauchen einen Entwicklungs-Checkout mit einer Installation ([Einen PHP-Entwicklungs-Checkout einrichten](ein-php-entwicklungs-checkout.md))
und ein Administratorkonto, um eine Richtlinie zuzuweisen. Die Dateien, die Sie anlegen:

```text
extension/acmereport/
  settings/site.ini.append.php
  settings/module.ini.append.php
  modules/acmereport/module.php
  modules/acmereport/list.php
  classes/runnable/views/acmereport/list.php
  design/standard/templates/acmereport/list.tpl
```

## Schritt 1: die Erweiterung und ihre Aktivierung

Legen Sie das Verzeichnis `extension/acmereport/` an und aktivieren Sie es für die Installation in
`settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=acmereport
```

## Schritt 2: das Modul registrieren

`extension/acmereport/settings/module.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=acmereport
ModuleList[]=acmereport
*/ ?>
```

`ExtensionRepositories[]` sagt dem Kernel, dass er in `extension/acmereport/modules/` nach Modulen suchen soll.

## Schritt 3: die View und die Richtlinie deklarieren

`extension/acmereport/modules/acmereport/module.php`:

```php
<?php
$Module = array( 'name' => 'Acme report' );

$ViewList = array();
$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'offset' => 'Offset' ) );

$FunctionList = array();
$FunctionList['read'] = array();
```

- `functions` nennt die Richtlinie, die ein Benutzer braucht: `acmereport/read`.
- `unordered_params` ordnet `/acmereport/list/(offset)/20` `$Params['Offset']` zu.
- Jeder Schlüssel ist in der [Referenz zu module.php](../ersteller/anwendungsentwickler/referenz-der-moduldefinition.md) beschrieben.

## Schritt 4: die View-Klasse

`extension/acmereport/classes/runnable/views/acmereport/list.php`:

```php
<?php
namespace Exponential\View\Extension\Acmereport\Acmereport;

class ListView extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $params = $scope['Params'];
        $offset = isset( $params['Offset'] ) ? max( 0, (int)$params['Offset'] ) : 0;

        $items = \eZPersistentObject::fetchObjectList( \acmeReportItem::definition(), null, null,
                                                       array( 'created' => 'desc' ),
                                                       array( 'offset' => $offset, 'length' => 20 ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'items', is_array( $items ) ? $items : array() );
        $tpl->setVariable( 'offset', $offset );

        return array(
            'content' => $tpl->fetch( 'design:acmereport/list.tpl' ),
            'path' => array( array( 'text' => \ezpI18n::tr( 'extension/acmereport', 'Reports' ), 'url' => false ) ) );
    }
}
```

`list` ist in PHP ein reserviertes Wort, deshalb heißt die Klasse `ListView`, so wie der Kernel seine eigene benennt
(`Exponential\View\Kernel\Section\ListView`); der Namespace folgt `Exponential\View\Extension\<Ext>\<Module>\<View>`
([Modul-Views als Klassen](modul-views-als-klassen.md)). Die Klasse `acmeReportItem` ist das persistente Objekt aus
[Definitionen persistenter Objekte im Detail](definitionen-persistenter-objekte-im-detail.md); für einen ersten Versuch
übergeben Sie stattdessen ein Array.

## Schritt 5: die View-Datei

`extension/acmereport/modules/acmereport/list.php`, die Datei, die `module.php` nennt, besteht aus einer Zeile:

```php
<?php
return \Exponential\View\Extension\Acmereport\Acmereport\ListView::main( __FILE__, get_defined_vars() );
```

`get_defined_vars()` übergibt der Klasse, was `eZProcess::runFile()` definiert hat: `$Params` (mit dem Modul als
`$Params['Module']`) und den Rest.

## Schritt 6: das Template

`extension/acmereport/design/standard/templates/acmereport/list.tpl`:

```text
<h1>{'Reports'|i18n( 'extension/acmereport' )}</h1>
{if $items|count}
<ul>
{foreach $items as $item}
    <li>{$item.status|wash}: {$item.amount|wash}</li>
{/foreach}
</ul>
{else}
<p>{'No reports yet.'|i18n( 'extension/acmereport' )}</p>
{/if}
```

Registrieren Sie das Design, damit das Template gefunden wird, in `extension/acmereport/settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=acmereport
*/ ?>
```

Jeder Wert läuft durch `|wash` ([Ausgaben aus PHP maskieren](ausgaben-aus-php-maskieren.md)).

## Schritt 7: Autoloads, Caches, eine Richtlinie

```bash
php -l extension/acmereport/classes/runnable/views/acmereport/list.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

In der Administration unter **Rollen und Richtlinien**: Geben Sie einer Testrolle die Richtlinie `acmereport/read`
(Modul `acmereport`, Funktion `read`) und weisen Sie die Rolle Ihrem Testbenutzer zu. Führen Sie unter Velocity statt
des Cache-Befehls `php bin/php/console exp:velocity deploy` aus, damit die Worker die neue Klasse laden.

## Schritt 8: prüfen

Öffnen Sie `/acmereport/list` im Siteaccess der Site, angemeldet als Testbenutzer.

| Sie sehen | Ursache |
|---|---|
| das Modul oder die View wurde nicht gefunden | `ModuleList[]` oder `ExtensionRepositories[]` fehlt, oder der INI-Cache wurde nicht geleert |
| Zugriff verweigert | dem Benutzer fehlt `acmereport/read` |
| `Class ... not found` | Autoloads nicht neu generiert, oder Namespace und Klassenname weichen von der View-Datei ab |
| das Template wurde nicht gefunden | `DesignExtensions[]` fehlt, oder der Pfad unter `templates/` weicht ab |
| eine leere Seite | ein PHP-Fehler: Aktivieren Sie die Debug-Ausgabe und lesen Sie `var/log/error.log` |

## Wie es weitergeht

- Fügen Sie ein Formular mit Aktionen und dem Formular-Token hinzu: [Eigene Module und Views](../ersteller/anwendungsentwickler/eigene-module-und-ansichten.md#formulare-verarbeiten-aktionen).
- Geben Sie Templates die Daten mit einer Fetch-Funktion: [Eine Fetch-Funktion schreiben](eine-fetch-funktion-schreiben.md).
- Testen Sie die Logik ohne Datenbank: [PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md).
- Machen Sie es beim nächsten Mal schneller mit **Setup > RAD** ([RAD-Werkzeuge](../ersteller/anwendungsentwickler/rad-werkzeuge.md)).

## Verwandte Seiten

- [Module und Views (Erweiterungsentwickler)](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md)
- [View-Ergebnisse, Templates und das Pagelayout](../ersteller/anwendungsentwickler/ergebnisse-von-ansichten-und-das-pagelayout.md)
- [Richtlinienfunktionen und Einschränkungen](../ersteller/anwendungsentwickler/richtlinienfunktionen-und-einschraenkungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
