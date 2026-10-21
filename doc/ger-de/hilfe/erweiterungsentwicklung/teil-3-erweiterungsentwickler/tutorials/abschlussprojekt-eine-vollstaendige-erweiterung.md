---
title: "Abschlussprojekt: eine vollständige Erweiterung von der Idee bis zum Release"
audience: developer, app-creator, designer
category: extension-development
order: 300
summary: Alle Tutorials in einer veröffentlichungsfähigen Erweiterung - ein Plan, der vollständige Dateibaum, composer.json, README, Tests, die Prüfungen vor einem Release, Version 0.1.0, Veröffentlichen und Ankündigen.
keywords: Abschlussprojekt, vollständige Erweiterung, Release, Plan, Dateibaum, Tests, Checkliste, Version, Tag, veröffentlichen, ankündigen, capstone, complete extension, release, plan, file tree, composer.json, README, tests, checklist, version, tag, Packagist, GitHub, announce
type: help
---
# Abschlussprojekt: eine vollständige Erweiterung von der Idee bis zum Release

Tutorial 23. Die Tutorials haben `mysite_hello` Stück für Stück gebaut. Dieses Abschlussprojekt macht daraus etwas, das
Sie anderen Menschen geben können: geplant, dokumentiert, getestet und veröffentlicht. Jeder Schritt verlinkt das
Kapitel, das ihn erklärt.

## 1. Die Idee, aufgeschrieben

Schreiben Sie vor dem Code fünf Zeilen in `README.md`:

```text
# My Site Hello

Greets visitors, lets editors compose greetings, rates content, and sends a nightly digest.
For: site builders who want a friendly front page.
Requires: Exponential 6.0.14 or later, PHP 8.0 or later.
License: GNU General Public License v2.0 (or any later version).
```

Wenn Sie diese Zeilen nicht schreiben können, ist die Erweiterung nicht bereit, gebaut zu werden. Prüfen Sie zuerst den
[Katalog](../../teil-1-vom-benutzer-zum-entwickler/der-se7enxweb-erweiterungskatalog.md):
Vielleicht gibt es sie schon.

## 2. Der vollständige Baum

```text
extension/mysite_hello/
    README.md  LICENSE  composer.json  ezinfo.php  extension.xml  phpunit.xml.dist  .gitignore
    autoloads/eztemplateautoload.php                         tutorial 10
    classes/mysitehellooperators.php                          tutorial 10
    classes/mysitehellofunctioncollection.php                 tutorial 11
    classes/mysitehellogreeting.php                           tutorial 17
    classes/mysitehelloserverfunctions.php                    tutorial 18
    classes/runnable/cronjobs/hellodigest.php                 tutorial 16
    cronjobs/hellodigest.php                                  tutorial 16
    datatypes/mysiterating/mysiteratingtype.php              tutorial 14
    eventtypes/event/mysitepublishlog/mysitepublishlogtype.php tutorial 15
    modules/hello/module.php  world.php  compose.php  function_definition.php   tutorials 11 to 13
    design/standard/templates/hello/world.tpl                 tutorial 12
    design/standard/templates/content/datatype/{edit,view}/mysiterating.tpl   tutorial 14
    design/standard/templates/content/datatype/view/ezxmltags/mysite_note.tpl  tutorial 19
    design/standard/templates/workflow/eventtype/view/event_mysitepublishlog.tpl tutorial 15
    design/standard/javascript/mysite_hello.js                tutorial 18
    design/admin4/templates/hello/compose.tpl                 tutorial 13
    settings/site.ini.append.php  module.ini.append.php  design.ini.append.php  content.ini.append.php
    settings/workflow.ini.append.php  cronjob.ini.append.php  ezjscore.ini.append.php  exphelp.ini.append.php
    share/db_schema.dba                                       tutorial 17
    translations/ger-DE/translation.ts  translations/untranslated/translation.ts   tutorial 20
    doc/README.md  doc/compose.md                             tutorial 21
    tests/bootstrap.php  tests/unit/*.php                     tutorial 22
```

## 3. composer.json

```json
{
    "name": "your-organization/mysite_hello",
    "description": "Greetings, a rating field and a nightly digest for Exponential.",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "authors": [ { "name": "Your Organization", "role": "Author" } ],
    "require": {
        "php": ">=8.0",
        "se7enxweb/exponential": "^6.0.14",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": { "ezpublish-legacy-extension-name": "mysite_hello" },
    "support": { "issues": "https://github.com/your-organization/mysite_hello/issues" }
}
```

`composer validate composer.json` darf keine Fehler melden
([composer.json für eine Erweiterung](../composer-json-fuer-erweiterungen.md)).

## 4. Die Prüfungen vor einem Release

```bash
find extension/mysite_hello -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
xmllint --noout extension/mysite_hello/extension.xml extension/mysite_hello/translations/*/translation.ts
(cd extension/mysite_hello && php ../../vendor/bin/phpunit)
php bin/php/checkclasses.php --quiet-ok
./console exp:checkcodestyle
```

Dann von Hand, auf einer Entwicklungsinstallation mit eingeschalteter Debug-Ausgabe: jede Seite im hellen und im
dunklen Modus, die Konsole frei von Meldungen der Content Security Policy, die RAD-Zustandsprüfung (`/setup/rad`) ohne
Befunde für Ihre Erweiterung. Die vollständige Liste steht in
[Vor einem Release testen](../../teil-7-veroeffentlichen/vor-einem-release-testen.md).

## 5. Version 0.1.0

Wählen Sie die Nummer anhand dessen, was bereits veröffentlicht ist (für ein erstes Release: nichts), tragen Sie sie in
`ezinfo.php` (`'Version' => "0.1.0"`) und `extension.xml` (`<version>0.1.0</version>`) ein, committen Sie und taggen
Sie dann diesen Commit:

```bash
git -C extension/mysite_hello commit -am "Updated: Version 0.1.0"
git -C extension/mysite_hello tag -a v0.1.0 -m "Version 0.1.0"
```

Ein gepushter Tag lässt sich nie verschieben oder ersetzen; prüfen Sie, bevor Sie pushen
([Tags und Releases sind dauerhaft](../../teil-7-veroeffentlichen/tags-sind-dauerhaft.md)).

## 6. Veröffentlichen

1. Legen Sie das Repository auf GitHub an und pushen Sie den Branch und den Tag.
2. Legen Sie für den Tag ein GitHub-Release mit dem Changelog an
   (`gh release create v0.1.0 --notes-file CHANGELOG.md`).
3. Melden Sie das Repository bei Packagist an und fügen Sie den Webhook hinzu
   ([Composer und Packagist](../../teil-7-veroeffentlichen/composer-und-packagist.md)).
4. Installieren Sie es auf einer frischen Installation mit `composer require your-organization/mysite_hello:^0.1`, um
   zu beweisen, dass es auch bei anderen funktioniert.

## 7. Ankündigen und pflegen

Kündigen Sie es dort an, wo Menschen nachsehen
([Ein Release ankündigen](../../teil-7-veroeffentlichen/ein-release-ankuendigen.md)),
beantworten Sie Issues und veröffentlichen Sie Korrekturen als neue Versionen. Wenn es allen dienen könnte, bieten Sie
es dem Projekt an
([Mit Pull Requests zurückgeben](../../teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)).

## Verwandte Seiten

- Zurück: [Von Hand: ein PHPUnit-Test für Ihre Erweiterung](von-hand-phpunit-test.md)
- Weiter: [Teil 4: Eine alte Erweiterung auf Exponential 6 aktualisieren](../../teil-4-aktualisieren/README.md)
- [Teil 7: Ihre Erweiterung veröffentlichen](../../teil-7-veroeffentlichen/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
