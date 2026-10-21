---
title: Ein Erweiterungsgerüst
audience: developer
category: examples
order: 10
summary: Die kleinste vollständige Erweiterung mit ihren Metadaten (ezinfo.php, extension.xml, composer.json), ihren Einstellungen und den Befehlen, die sie aktivieren und ihre Klassen bekannt machen.
keywords: Erweiterung, Gerüst, Metadaten, Autoload, Version, Veröffentlichung, ezinfo.php, extension.xml, composer.json, ActiveExtensions, ExtensionRepositories, DesignExtensions, ezpgenerateautoloads, extension, skeleton, autoload, version, release
type: example
---
# Ein Erweiterungsgerüst

Sie bauen den leeren Rahmen der Beispielerweiterung **expexample**, den der Rest des Kochbuchs füllt: den Ordner,
die beiden Metadatendateien, die jede Erweiterung mitbringt, eine `composer.json` für die Installation mit Composer
und die Einstellungsdateien, die dem Kernel sagen, wo ihre Module, Templates, Operatoren und Übersetzungen liegen. Am
Ende ist die Erweiterung aktiviert und wird unter **Setup > Extensions** mit ihrer Version aufgeführt.

Voraussetzungen: eine laufende Exponential-6-Installation, eine Shell in ihrem Wurzelverzeichnis und eine
Administratoranmeldung. Als `root` braucht jeder Befehl `--allow-root-user`; besser führen Sie die Befehle als der
Benutzer der Website aus.

## 1. Der Ordner

```bash
mkdir -p extension/expexample/settings \
         extension/expexample/classes \
         extension/expexample/design/standard/templates
```

Der Ordnername ist der Name der Erweiterung: Kleinbuchstaben, Ziffern und Unterstriche. Neuer Code in diesem Projekt
beginnt mit `exp`; die eigenen Erweiterungen von 7x verwenden `sevenx` oder `exp`, und Ihre eigene Erweiterung
verwendet Ihr eigenes Präfix ([Namenspräfixe: sevenx und exp](../../../eng-us/standards/naming-prefixes-sevenx-and-exp.md)).

## 2. ezinfo.php

`extension/expexample/ezinfo.php` wird von **Setup > Über** und **Setup > Extensions** gelesen. Die Klasse heißt
`<extension>Info`, `info()` ist statisch, und die Schlüssel sind Zeichenketten in Anführungszeichen:

```php
<?php
/**
 * The metadata of the expexample extension, read by Setup > About and Setup > Extensions.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expexampleInfo
{
    public static function info()
    {
        return array(
            'Name'      => "Exponential example extension",
            'Version'   => "0.1.0",
            'Copyright' => "Copyright (C) 2026 Example Company",
            'License'   => "GNU General Public License v2.0 (or any later version)",
            'Info_url'  => "https://github.com/example/expexample"
        );
    }
}
```

## 3. extension.xml

`extension/expexample/extension.xml` wird zuerst gelesen, wenn sie `<metadata>` enthält; ihre Version muss deshalb
dieselbe Nummer sein:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>Exponential example extension</name>
        <version>0.1.0</version>
        <copyright>Copyright (C) 2026 Example Company</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/example/expexample</info_url>
    </metadata>
    <dependencies>
    </dependencies>
</software>
```

`<dependencies>` nennt die Erweiterungen, die diese braucht (`<requires><extension name="ezjscore" /></requires>`)
oder auf denen sie aufbaut (`<extends>`); mit `site.ini [ExtensionSettings] ExtensionOrdering=enabled` bestimmen sie
die Ladereihenfolge. Schreiben Sie niemals `//autogentag//` oder `dev-master` als Version, sondern die Nummer der
Veröffentlichung.

## 4. composer.json

Nur nötig, wenn die Erweiterung mit Composer installiert wird. Der Pakettyp `ezpublish-legacy-extension` und
`ezpublish-legacy-extension-name` sorgen dafür, dass der Installer sie in `extension/expexample` ablegt:

```json
{
    "name": "example/expexample",
    "description": "An example extension for Exponential 6",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "require": {
        "php": ">=8.0",
        "se7enxweb/exponential": "^6.0",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "expexample"
    }
}
```

## 5. Die Einstellungen, die sagen, wo die Dinge liegen

Jeder Teil einer Erweiterung wird über eine Einstellung gefunden, die in der eigenen Datei
`settings/<file>.ini.append.php` der Erweiterung steht. Die Datei ist PHP, das nichts ausgibt, mit dem INI-Text in
einem Kommentar:

`extension/expexample/settings/module.ini.append.php` (Module, [Ein eigenes Modul mit Ansicht](eigenes-modul-mit-ansicht.md)):

```ini
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
# Look for modules/<module>/module.php in this extension
ExtensionRepositories[]=expexample
# The known module names: keeps a URL alias from taking over the module's address
ModuleList[]=expexample

*/ ?>
```

`extension/expexample/settings/design.ini.append.php` (Templates, Skripte und Stylesheets):

```ini
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
# Look for design/<design>/templates/... in this extension
DesignExtensions[]=expexample

[JavaScriptSettings]
# design/standard/javascript/expexample.js, packed and loaded by the pagelayouts of the site designs
FrontendJavaScriptList[]=expexample.js

*/ ?>
```

`extension/expexample/settings/site.ini.append.php` (Template-Operatoren und Übersetzungen):

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
# Look for autoloads/eztemplateautoload.php in this extension (template operators)
ExtensionAutoloadPath[]=expexample

[RegionalSettings]
# Look for translations/<locale>/translation.ts in this extension
TranslationExtensions[]=expexample

*/ ?>
```

Fügen Sie die weiteren Dateien (`content.ini.append.php`, `workflow.ini.append.php`, `cronjob.ini.append.php`,
`ezjscore.ini.append.php`) hinzu, wenn Sie den Teil hinzufügen, den sie aktivieren; die Seiten zu diesen Teilen zeigen
sie. Eine Erweiterung ändert niemals eine Kernel-Datei in `settings/` und schreibt niemals nach `settings/override/`.

## 6. Aktivieren

Tragen Sie die Erweiterung in `site.ini [ExtensionSettings] ActiveExtensions[]` der Installation ein. Der Befehl
`exp:ini` schreibt die Zeile in `settings/override/site.ini.append.php` und legt eine Sicherungskopie an:

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' expexample global --dry-run --allow-root-user
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' expexample global --allow-root-user
```

Oder klicken Sie auf ihrer Karte unter **Setup > Extensions** auf **Aktivieren**. Für nur einen Siteaccess verwenden
Sie den Geltungsbereich `siteaccess:<name>` und `ActiveAccessExtensions[]`.

Danach machen Sie dem Kernel ihre Klassen und Einstellungen bekannt:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

`ezpgenerateautoloads.php -e` durchsucht das Verzeichnis `classes/` (und jedes andere PHP-Verzeichnis) der aktiven
Erweiterungen und schreibt `var/autoload/ezp_extension.php`; führen Sie es erneut aus, nachdem Sie eine Klasse
hinzugefügt oder umbenannt haben. Unter PHP-FPM oder Velocity sehen die laufenden Worker neue Klassen erst nach einem
Reload: siehe [Deployment mit Velocity](velocity-deployment.md).

## 7. Prüfen

```bash
php -l extension/expexample/ezinfo.php
xmllint --noout extension/expexample/extension.xml
./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
```

Erwartet: `No syntax errors detected`, keine Ausgabe von `xmllint` und `expexample` unter den aktiven Erweiterungen.
**Setup > Extensions** führt *Exponential example extension* mit Version 0.1.0, Lizenz und Website auf.

Für diese Seite geprüft (2026-10-18): `php -l` auf `ezinfo.php` und die vier Einstellungsdateien, `xmllint --noout`
auf `extension.xml`, `composer.json` als JSON eingelesen.

## 8. Eine Version veröffentlichen

Wenn Sie die Erweiterung veröffentlichen, wird die Version in `ezinfo.php` und in `extension.xml` (und `version` in
`composer.json`, falls Sie es führen) in einem Commit **vor** dem Tag auf die Nummer der Veröffentlichung gesetzt.
Wählen Sie die Nummer anhand der vorhandenen Tags (`git tag -l 'v*' --sort=version:refname`), niemals aus dem
Gedächtnis, und verschieben Sie niemals einen Tag, der bereits gepusht wurde. Das ganze Verfahren steht in
[Erweiterungen, Abschnitt 7](../../anleitungen/extensions.md#7-die-extension-veröffentlichen).

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Die Liste der Erweiterungen zeigt einen Strich als Version | `ezinfo.php` oder `extension.xml` fehlt oder lässt sich nicht einlesen; oder die beiden stimmen nicht überein |
| `Class '...' not found` | das Autoload-Array wurde nicht neu erzeugt, oder PHP-FPM oder Velocity verwendet noch das alte |
| Eine Einstellung der Erweiterung wirkt nicht | die Erweiterung ist nicht aktiv, oder eine früher geladene Erweiterung oder `settings/override/` setzt dieselbe Variable: `./console exp:ini where <file>/<Block>/<Variable>` |
| Ein neues Template wird nicht verwendet | der Template-Override-Cache: `php bin/php/ezcache.php --clear-id=template-override --allow-root-user` |

## Abgeleitet aus

- `extension/expservices/ezinfo.php`, `extension/expservices/extension.xml` (Metadaten einer mitgelieferten Erweiterung)
- `extension/ezjscore/settings/site.ini.append.php`, `extension/ezjscore/settings/module.ini.append.php`
- `settings/module.ini` (`ExtensionRepositories[]`, `ModuleList[]`), `settings/design.ini` (`[JavaScriptSettings]`)
- `lib/eztemplate/classes/eztemplate.php` (`ExtensionAutoloadPath`), `lib/ezi18n/classes/eztstranslator.php`
  (`TranslationExtensions`)
- `doc/eng-us/guides/extensions.md`, `doc/eng-us/specifications/6.0/extension-metadata.md`

## Verwandte Seiten

- [Kochbuch](README.md) und [Beispiele](../README.md)
- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../anleitungen/extensions.md)
- [Extension-Metadaten](../../spezifikationen/6.0/extension-metadaten.md),
  [Ladereihenfolge der Erweiterungen](../../../eng-us/features/6.0/extension-loading-order.md),
  [Platzierungen von Einstellungs-Overrides](../../spezifikationen/6.0/ini-override-platzierungen.md)
- [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md): ein Assistent, der dieses Gerüst für Sie schreibt
- [INI-Einstellungen](../ini-einstellungen/README.md) und [Erweiterungspunkte](../erweiterungspunkte/README.md)
- Die Programmierregeln und der Musterkatalog: [die Standards](../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
