---
title: Der Verzeichnisaufbau einer Erweiterung
audience: developer, app-creator
category: extension-development
order: 10
summary: Jeder Ordner- und Dateiname, den eine Erweiterung haben kann, was der Kernel damit macht, welche Einstellung den Kernel dorthin führt und wie viele der installierten Erweiterungen ihn verwenden - die Landkarte für den Rest von Teil 3.
keywords: Verzeichnisaufbau, Verzeichnisstruktur, Ordner, Einstellungen, Klassen, Module, Design, Datentypen, Übersetzungen, Tests, extension layout, directory structure, ezinfo.php, extension.xml, settings, classes, autoloads, modules, design, datatypes, eventtypes, cronjobs, bin, translations, share, sql, update, tests, doc
type: help
---
# Der Verzeichnisaufbau einer Erweiterung

Teil 3, Kapitel 1. Eine Erweiterung ist ein Ordner unter `extension/`, dessen Unterordner festen Namen folgen. Der
Kernel findet jede Art von Code über einen Namen und eine Einstellung, die auf Ihre Erweiterung verweist. Dieses
Kapitel ist die Landkarte; jede Zeile verlinkt das Kapitel, das sie erklärt.

## Der vollständige Baum

```text
extension/mysite_hello/
    ezinfo.php                     metadata (class mysite_helloInfo)
    extension.xml                  metadata and dependencies (read first)
    composer.json                  for Composer and Packagist
    README.md, LICENSE, doc/       documentation and license
    settings/                      *.ini.append.php (and your own *.ini)
        siteaccess/<sa>/           per-siteaccess settings the extension owns
    classes/                       PHP classes (autoloaded)
    autoloads/                     eztemplateautoload.php: template operators
    modules/<module>/              module.php, view scripts, function_definition.php
    design/<design>/
        templates/                 templates
        override/templates/        override templates
        stylesheets/, javascript/, images/
    datatypes/<datatype>/          <datatype>type.php
    eventtypes/event/<event>/      <event>type.php
    cronjobs/                      cronjob scripts
    bin/php/                       command line scripts (ext:<extension>:<name>)
    translations/<locale>/         translation.ts
    share/                         db_schema.dba, db_data.dba, filelist.md5
    sql/<database>/                schema.sql per database engine
    update/                        upgrade scripts and SQL
    packages/                      packages the extension ships
    tests/                         PHPUnit tests
```

Nichts davon ist Pflicht außer einem Grund, zu existieren: Eine Erweiterung, die nur `settings/` hat, ist vollständig.
`ezinfo.php` und `extension.xml` werden für jede Erweiterung dringend empfohlen, weil die Administration und die
Upgrade-Prüfungen sie lesen.

## Was der Kernel mit jedem Teil macht

| Pfad | Gefunden, weil | Kapitel |
|---|---|---|
| `ezinfo.php`, `extension.xml` | von `expInfo` für jede Erweiterung auf der Festplatte gelesen | [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md) |
| `settings/` | den Override-Verzeichnissen der Einstellungen einer aktiven Erweiterung hinzugefügt | [Einstellungen in einer Erweiterung](einstellungen-in-einer-erweiterung.md) |
| `classes/` | von `php bin/php/ezpgenerateautoloads.php -e` nach `var/autoload/ezp_extension.php` eingelesen | [Autoloads und Klassennamen](autoloads-und-klassennamen.md) |
| `autoloads/eztemplateautoload.php` | `site.ini [TemplateSettings] ExtensionAutoloadPath[]=<ext>` | [Template-Operatoren](template-operatoren.md) |
| `modules/<module>/module.php` | `module.ini [ModuleSettings] ExtensionRepositories[]=<ext>` | [Module und Views](module-und-views.md) |
| `design/<design>/` | `design.ini [ExtensionSettings] DesignExtensions[]=<ext>` | [Ein Design in einer Erweiterung](design-in-einer-erweiterung.md) |
| `datatypes/<type>/<type>type.php` | `content.ini [DataTypeSettings] ExtensionDirectories[]=<ext>` und `AvailableDataTypes[]=<type>` | [Datentypen](datentypen.md) |
| `eventtypes/event/<type>/<type>type.php` | `workflow.ini [EventSettings] ExtensionDirectories[]=<ext>` und `AvailableEventTypes[]=event_<type>` | [Workflow-Ereignistypen](workflow-ereignistypen.md) |
| `cronjobs/<script>.php` | `cronjob.ini [CronjobSettings] ExtensionDirectories[]=<ext>` und `Scripts[]` eines Teils | [Cronjob-Teile](cronjob-teile.md) |
| `bin/php/<name>.php` | die Console findet es als `ext:<extension>:<name>` | [CLI-Skripte und Console-Befehle](cli-skripte-und-console-befehle.md) |
| `translations/<locale>/translation.ts` | `site.ini [RegionalSettings] TranslationExtensions[]=<ext>` | [Übersetzungen in einer Erweiterung](uebersetzungen-in-einer-erweiterung.md) |
| `share/db_schema.dba`, `sql/` | der Paket-Installer oder Ihr Upgrade-Skript | [Datenbankschema, .dba-Dateien und Upgrades](datenbankschema-und-dba-dateien.md) |
| `tests/` | Ihre `phpunit.xml.dist` | [Tests für Erweiterungen](tests-fuer-erweiterungen.md) |

Die Regel hinter der Tabelle: **Der Ordnername ist eine Konvention, und eine Einstellung im eigenen `settings/` Ihrer
Erweiterung registriert ihn**. Ein Ordner `datatypes/` in einer Erweiterung, die sich nicht selbst in
`ExtensionDirectories[]` einträgt, wird nie angesehen.

Die Pfade für Datentypen und Ereignistypen sind genau die, die der Kernel bildet: `eZDataType::loadAndRegisterType()`
bindet `<directory>/<type>/<type>type.php` ein, und `eZWorkflowType::loadAndRegisterType()` zerlegt einen
Ereignistyp-String wie `event_mysite_notify` in die Gruppe `event` und den Typ `mysite_notify` und bindet
`<extension>/eventtypes/event/mysite_notify/mysite_notifytype.php` ein.

## Wie verbreitet ist jeder Ordner?

Auf der Referenzinstallation (87 Erweiterungen auf der Festplatte) wurden die Ordner so oft verwendet: `settings/` 80,
`classes/` 68, `design/` 62, `doc/` 60, `translations/` 50, `modules/` 45, `autoloads/` 39, `tests/` 35, `bin/` 28,
`sql/` 19, `datatypes/` 17, `share/` 17, `cronjobs/` 15, `update/` 8, `eventtypes/` 6, `packages/` 4. Zählen Sie Ihre:

```bash
for d in settings classes design modules autoloads datatypes eventtypes cronjobs bin translations tests; do
    printf "%-12s %s\n" "$d" "$(ls -d extension/*/$d 2>/dev/null | wc -l)"
done
```

## Die Erweiterung benennen

- Kleinbuchstaben, Ziffern und Unterstriche: `mysite_hello`, nicht `MySite-Hello`.
- Eindeutig und Ihr eigener: Ein Präfix Ihrer Organisation oder Ihres Projekts (`mysite_`, `acme_`) vermeidet
  Kollisionen mit jeder anderen Erweiterung. Erweiterungen von 7x verwenden `sevenx` (das offizielle öffentliche
  Präfix für PHP-Code von 7x; "7x" kann keines sein, da ein Name nicht mit einer Ziffer beginnen kann) oder `exp`
  (bewusst verwendet für Arbeit, die dem Projekt Exponential und der Exponential Foundation zugeordnet ist): siehe
  [Namenspräfixe: sevenx und exp](../../../../eng-us/standards/naming-prefixes-sevenx-and-exp.md). Beginnen Sie neue
  Namen nicht mit `ez`, das zu den historischen Klassen des Kernels gehört.
- Der Ordnername wird überall verwendet (Einstellungen, `extra` von Composer, Klassennamen wie `mysite_helloInfo`),
  wählen Sie ihn also einmal.

## Von den RAD-Werkzeugen erzeugt

Jeder Assistent unter **Setup > RAD** schreibt diese Struktur für die Teile, die Sie auswählen, und listet jede Datei
auf, bevor er sie schreibt. Die Ausgabe eines Assistenten mit dieser Landkarte zu vergleichen, ist ein guter Weg, sie
zu lernen; siehe
[Der Rundgang durch die RAD-Werkzeuge](tutorials/rundgang-durch-die-rad-werkzeuge.md).

## Verwandte Seiten

- Zurück: [Teil 3: Der Erweiterungsentwickler](README.md)
- Weiter: [ezinfo.php: wer sie gemacht hat und welche Version](ezinfo-php.md)
- [Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md)
- [Was eine Erweiterung ist](../teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
