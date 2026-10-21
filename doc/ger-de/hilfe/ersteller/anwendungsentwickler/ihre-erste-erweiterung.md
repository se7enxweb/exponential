---
title: Ihre erste Erweiterung
audience: app-creator, developer
category: app-creation
order: 60
summary: In fünfzehn Minuten eine funktionierende Erweiterung bauen - von Hand oder mit dem RAD-Modul-Assistenten - mit einem Modul, einer Ansicht, einer Richtlinienfunktion, einem Template und Einstellungen, sie dann aktivieren, ihr eine Rolle geben und im Browser prüfen.
keywords: erste Erweiterung, Tutorial, Modul, Ansicht, Template, Richtlinie, Modul-Assistent, Hallo Welt, Anwendungsentwickler, first extension, tutorial, module, view, template, policy, module.php, ActiveExtensions, RAD, module wizard, hello world, app creator
type: help
---
# Ihre erste Erweiterung

Diese Seite richtet sich an Anwendungsentwickler, die schnell eine laufende Erweiterung möchten. Sie bauen `expdemo`,
eine Erweiterung mit einem Modul, einer Ansicht, die die neuesten Artikel auflistet, einer Richtlinienfunktion und einem
Template. Sie können sie von **Setup > RAD** schreiben lassen ([RAD-Werkzeuge](rad-werkzeuge.md)) oder wie unten selbst
eintippen; beides führt zum selben Ergebnis.

## Inhalt

1. [Die Dateien](#die-dateien)
2. [Aktivieren und registrieren](#aktivieren-und-registrieren)
3. [Eine Rolle vergeben](#eine-rolle-vergeben)
4. [Prüfen](#prüfen)
5. [Was Sie gelernt haben](#was-sie-gelernt-haben)

## Die Dateien

```text
extension/expdemo/
├── extension.xml
├── ezinfo.php
├── modules/expdemo/module.php
├── modules/expdemo/latest.php
├── design/standard/templates/expdemo/latest.tpl
└── settings/module.ini.append.php
```

`settings/module.ini.append.php` teilt dem Kernel mit, dass die Erweiterung Module hat:

```ini
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
ModuleList[]=expdemo
*/ ?>
```

`modules/expdemo/module.php` deklariert das Modul, seine Ansicht und seine Richtlinienfunktion:

```php
<?php
$Module = array( 'name' => 'Demo' );

$ViewList = array();
$ViewList['latest'] = array(
    'script' => 'latest.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'params' => array( 'Limit' ) );

$FunctionList = array();
$FunctionList['read'] = array();
```

`modules/expdemo/latest.php` ist die Ansicht; sie ruft Inhalte ab und rendert ein Template:

```php
<?php
/** @var array $Params set by eZModule */
$limit = isset( $Params['Limit'] ) ? max( 1, min( 50, (int)$Params['Limit'] ) ) : 10;

$nodes = eZContentObjectTreeNode::subTreeByNodeID(
    array( 'ClassFilterType' => 'include',
           'ClassFilterArray' => array( 'article' ),
           'SortBy' => array( 'published', false ),
           'Limit' => $limit ),
    (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'nodes', $nodes ?: array() );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expdemo/latest.tpl' );
$Result['path'] = array( array( 'text' => 'Latest articles', 'url' => false ) );
```

`subTreeByNodeID()` prüft `content/read` für den aktuellen Benutzer, sodass die Liste nur zeigt, was der Benutzer lesen
darf.

`design/standard/templates/expdemo/latest.tpl`:

```text
<h1>{'Latest articles'|i18n( 'extension/expdemo' )}</h1>
<ul>
{foreach $nodes as $node}
  <li><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
{/foreach}
</ul>
```

`ezinfo.php` und `extension.xml` beschreiben die Erweiterung für die Seite „Über“
([Aufbau einer Erweiterung](aufbau-einer-erweiterung.md)).

## Aktivieren und registrieren

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=expdemo
```

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=design_base,template-override --allow-root-user
```

Unter Velocity: `php bin/php/console exp:velocity deploy`.

## Eine Rolle vergeben

Fügen Sie unter **Benutzer > Rollen** der Rolle, die die Seite sehen soll, die Richtlinie `expdemo / read` hinzu
(Anonymous für eine öffentliche Seite). Ohne sie antwortet die Ansicht mit „Zugriff verweigert“: Die Richtlinie des
Moduls wird geprüft, bevor das Skript läuft.

## Prüfen

Öffnen Sie `/expdemo/latest/5` auf der Website. Erwartet: eine Überschrift und fünf Links. Dann `/expdemo/latest/abc`:
Die Umwandlung in `int` macht daraus 1, niemals einen Fehler. Abgemeldet ohne die Richtlinie: Zugriff verweigert.

## Was Sie gelernt haben

| Bestandteil | Lektion |
|---|---|
| `module.ini.append.php` | die Module einer Erweiterung werden über Einstellungen registriert |
| `module.php` | Ansichten, Parameter und Richtlinienfunktionen werden deklariert, nicht programmiert |
| die Ansicht | `$Params` hinein, `$Result` heraus; das HTML machen Templates |
| die Richtlinie | der Zugriff wird vom Kernel vor Ihrem Code geprüft |
| das Template | `|wash` auf allem, was Sie ausgeben |

Als Nächstes: [Eigene Module und Ansichten](eigene-module-und-ansichten.md) und die klassenbasierte Form von Ansichten
([Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)).

## Verwandte Seiten

- [Aufbau einer Erweiterung](aufbau-einer-erweiterung.md)
- [Referenz der Moduldefinition](referenz-der-moduldefinition.md)
- [RAD-Werkzeuge](rad-werkzeuge.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
