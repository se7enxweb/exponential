---
title: "Von Hand: ein Modul mit einer View"
audience: developer, app-creator, designer
category: extension-development
order: 130
summary: Die Seite /hello/world/<name> - module.php mit einer Richtlinie, eine View, die ihren Parameter prüft und die Fetch-Funktion verwendet, ein Template im Design standard, alles geprüft.
keywords: Tutorial, von Hand, Modul, View, Richtlinie, Parameter, Template, Pfad, tutorial, by hand, module, view, module.php, ViewList, FunctionList, policy, Params, template, design standard, Result, path
type: help
---
# Von Hand: ein Modul mit einer View

Tutorial 12. Sie fügen `mysite_hello` eine Seite hinzu: `/hello/world/Ada` begrüßt Ada und listet die neuesten Artikel
auf, mithilfe der Fetch-Sammlung aus dem vorigen Tutorial. Die Dateien haben `php -l` bestanden; mit geladenem Kernel
wurde das Modul samt seiner View von `eZModule::exists( 'hello' )` gefunden, und das Template wurde wie gezeigt
gerendert.

## 1. module.php

`extension/mysite_hello/modules/hello/module.php` (der Ordner existiert bereits seit der Fetch-Funktion, und
`ExtensionRepositories[]=mysite_hello` registriert ihn):

```php
<?php
$Module = array( 'name' => 'Hello' );

$ViewList = array();
$ViewList['world'] = array(
    'script' => 'world.php',
    'params' => array( 'Name' ),
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart' );

$FunctionList = array();
$FunctionList['read'] = array();
```

Die View verlangt die Richtlinie `hello/read`.

## 2. Die View

`extension/mysite_hello/modules/hello/world.php`:

```php
<?php
/** @var eZModule $Module */
$Module = $Params['Module'];

$name = isset( $Params['Name'] ) && is_string( $Params['Name'] ) && $Params['Name'] !== ''
      ? $Params['Name'] : 'world';

$tpl = eZTemplate::factory();
$tpl->setVariable( 'name', $name );
$tpl->setVariable( 'newest', mysiteHelloFunctionCollection::fetchNewest( 'article', 2, 3 )['result'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/world.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'extension/mysite_hello', 'Hello' ), 'url' => false ) );
```

## 3. Das Template

`extension/mysite_hello/design/standard/templates/hello/world.tpl`:

```text
<section class="mysite-hello">
    <h1>{'Hello, %name!'|i18n( 'extension/mysite_hello',, hash( '%name', $name|wash ) )}</h1>
    {if $newest|count}
    <h2>{'Newest articles'|i18n( 'extension/mysite_hello' )}</h2>
    <ul>
    {foreach $newest as $node}
        <li><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
    {/foreach}
    </ul>
    {/if}
</section>
```

und registrieren Sie das Design in `extension/mysite_hello/settings/design.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

*/ ?>
```

Mit dem Namen `Ada <b>` gerendert, lautete die Überschrift `Hello, Ada &lt;b&gt;!`: Der Name wird maskiert, bevor er in
den übersetzten Satz eingesetzt wird.

## 4. Einschalten, gewähren, öffnen

```bash
php -l extension/mysite_hello/modules/hello/module.php
php -l extension/mysite_hello/modules/hello/world.php
php bin/php/ezcache.php --clear-all
```

Gewähren Sie unter **Benutzer > Rollen und Richtlinien** der Rolle Anonymous (oder einer anderen Rolle) `hello/read` und
öffnen Sie dann `/hello/world/Ada`. Ohne die Richtlinie erhalten Sie die Seite „Zugriff verweigert“, und genau darum
geht es in Schritt 1.

## 5. Wenn es nicht funktioniert

| Symptom | Abhilfe |
|---|---|
| „The requested module does not exist“ | `ExtensionRepositories[]`, Caches |
| Zugriff verweigert | `hello/read` gewähren |
| Layout mit leerem Inhaltsbereich | der Template-Pfad, `DesignExtensions[]` und `--clear-id=template-override` bei einem neuen Template |

## 6. Committen

```bash
git -C extension/mysite_hello add modules design settings
git -C extension/mysite_hello commit -m "Added: The page hello/world, greeting a visitor and listing the newest articles"
```

## Verwandte Seiten

- Zurück: [Von Hand: eine Fetch-Funktion](von-hand-fetch-funktion.md)
- Weiter: [Von Hand: ein Modul, das durch eine Richtlinie geschützt ist](von-hand-modul-mit-richtlinie.md)
- [Module und Views](../module-und-views.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
