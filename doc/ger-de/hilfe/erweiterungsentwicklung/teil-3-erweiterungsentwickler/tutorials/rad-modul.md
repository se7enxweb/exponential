---
title: "Mit RAD: ein Modul mit Views"
audience: developer, app-creator
category: extension-development
order: 40
summary: Der Modul-Assistent schreibt ein Modul mit zwei Views und Richtlinien; lesen Sie die Ausgabe, korrigieren Sie den Template-Ordner von Hand und wissen Sie, wann Modul über vorhandenen Tabellen das Richtige ist.
keywords: RAD, Modul-Assistent, Modul, View, Richtlinie, ungeordnete Parameter, vorhandene Tabellen, persistentes Objekt, erzeugter Code, module wizard, setup/modulewizard, setup/moduleextension, module, view, policy, FunctionList, unordered parameters, module over existing tables, persistent object, generated code
type: help
---
# Mit RAD: ein Modul mit Views

Tutorial 4. Zwei Assistenten schreiben Module. Der **Modul-Assistent** (`/setup/modulewizard`) schreibt ein Modul mit
Views und Richtlinienfunktionen; er ist auch das Werkzeug für „View für ein vorhandenes Modul“ und
„Richtlinienfunktion und Einschränkung“. **Modul über vorhandenen Tabellen** (`/setup/moduleextension`) schreibt ein
Modul, Klassen für persistente Objekte und Seiten für Tabellen, die bereits existieren. Dieses Tutorial verwendet den
ersten. Die gezeigte Ausgabe wurde vom Generator des Assistenten (`expModuleWizard::files()`) mit denselben Eingaben
erzeugt; jede PHP-Datei hat `php -l` bestanden.

## 1. Das Formular

| Feld | Wert |
|---|---|
| Name der Extension | `mysite_hello` |
| Modul | `hello` |
| Titel | `Hello module` |
| Ansichten | `world: read Name?` und, in der nächsten Zeile, `admin: administrate` |
| Richtlinien | `read` und `administrate` |

Das Feld für die Views nimmt eine View pro Zeile auf: einen Namen, einen Doppelpunkt, dann Wörter. Die Regel des
Assistenten (`expModuleWizard::viewList()`): Ein großgeschriebenes Wort ist ein geordneter Parameter, ein
kleingeschriebenes Wort eine Richtlinie, und ein angehängtes `?` kennzeichnet einen Parameter, der weggelassen werden
darf.

## 2. Die Dateien

```text
.gitignore
LICENSE
README.md
composer.json
design/mysite_hello/templates/hello/admin.tpl
design/mysite_hello/templates/hello/world.tpl
doc/examples.php
extension.xml
ezinfo.php
modules/hello/admin.php
modules/hello/module.php
modules/hello/world.php
settings/design.ini.append.php
settings/menu.ini.append.php
settings/module.ini.append.php
```

Die erzeugte `modules/hello/module.php`:

```php
<?php
$Module = array( 'name' => 'Hello module' );

$ViewList = array();

$ViewList['world'] = array(
    'script' => 'world.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'unordered_params' => array( 'name' => 'Name' ),
);

$ViewList['admin'] = array(
    'script' => 'admin.php',
    'functions' => array( 'administrate' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
);

$FunctionList = array();

$FunctionList['read'] = array();

$FunctionList['administrate'] = array();
```

Jede View verlangt ihre Richtlinie, so wie es sein soll. Der optionale Parameter `Name?` wurde zu einem ungeordneten
Parameter, die Adresse lautet also `/hello/world/(name)/Ada` (so steht es auch in der Tabelle der erzeugten README).

Das View-Skript beginnt mit der richtigen Warnung und einer absichtlich sicheren Voreinstellung:

```php
// Everything in $Params came out of the address, which means it came from
// whoever typed it. The declared type is nothing: cast, and check.
$name = isset( $Params['Name'] )
          ? (int) $Params['Name'] : 0;
```

Das `(int)` geht von einer ID aus. Ersetzen Sie es für einen Namen durch eine Prüfung auf eine Zeichenkette, wie in
[View-Parameter](../view-parameter.md).

## 3. Eine Korrektur vor der Verwendung

Der Assistent schreibt die Templates nach `design/mysite_hello/templates/`, in ein Design, das nach der Erweiterung
benannt ist. Die Design-Kette durchsucht nur Designs, die ein Siteaccess verwendet (`SiteDesign`,
`AdditionalSiteDesignList[]`, `StandardDesign`), daher werden diese Templates nicht gefunden, und die Views antworten mit
einer leeren Seite. Verschieben Sie sie nach `standard` (oder in Ihr Admin-Design):

```bash
mv extension/mysite_hello/design/mysite_hello extension/mysite_hello/design/standard
php bin/php/ezcache.php --clear-id=template-override,design_base
```

Die erzeugten Templates verwenden das Box-Markup der klassischen Administration; schreiben Sie sie für admin4-Seiten mit
den gemeinsamen Komponenten neu
([Admin-Seiten im admin4-Stil](../admin-seiten-im-admin4-stil.md)).

## 4. Das Menü

`settings/menu.ini.append.php` fügt das Modul dem Menü eines Navigationsteils hinzu
(`[NavigationPartMenu_ezsetupnavigationpart]` mit `Menu[]=hello`, `MenuTitle[hello]` und `MenuURL[hello]=/hello/world`).
Prüfen Sie das Ergebnis in Ihrem Administrationsdesign und passen Sie es an; das linke Menü von admin4 gruppiert Links
so, wie es in
[Admin-Seiten im admin4-Stil](../admin-seiten-im-admin4-stil.md#das-linke-menü)
beschrieben ist.

## 5. Einschalten, gewähren, öffnen

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Gewähren Sie einer Rolle `hello/read` (**Benutzer > Rollen und Richtlinien**) und öffnen Sie dann
`/hello/world/(name)/Ada`.

## Modul über vorhandenen Tabellen

`/setup/moduleextension` ist für Daten gedacht, die bereits Tabellen haben. Es liest deren Struktur und schreibt unter
anderem eine Klasse für persistente Objekte pro Tabelle (`classes/<class>.php`), eine Verbindungs- und eine
Registry-Klasse, das Modul mit seiner `function_definition.php` und einer Funktionssammlung, eine View und ein Template
zum Entfernen, eine nach der Erweiterung benannte Einstellungsdatei, einen Menüeintrag und `share/db_schema.dba`.
Verwenden Sie es, wenn Sie Administrationsseiten für Daten bauen, die ein anderes System schreibt; für neue Daten
entwerfen Sie Tabelle und Klasse selbst
([Persistente Objekte und Ihre eigenen Tabellen](../persistente-objekte-und-tabellen.md)).

## Verwandte Seiten

- Zurück: [Mit RAD: ein Datentyp](rad-datentyp.md)
- Weiter: [Mit RAD: ein Workflow-Ereignis](rad-workflow-ereignis.md)
- [Module und Views](../module-und-views.md), [Von Hand: ein Modul mit einer View](von-hand-modul-mit-view.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
