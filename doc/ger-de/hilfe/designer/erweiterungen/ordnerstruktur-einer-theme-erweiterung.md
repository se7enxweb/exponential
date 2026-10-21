---
title: "Die Ordnerstruktur einer Theme-Erweiterung"
audience: designer, developer
category: theme-extensions
order: 40
summary: Jeder Ordner und jede Datei, die eine Theme-Erweiterung enthalten kann, was sie liest und welche Pflicht sind, gezeigt an einem vollständigen Baum und verglichen mit drei veröffentlichten Themes.
keywords: Ordnerstruktur, Verzeichnisaufbau, Design-Ordner, settings-Ordner, Autoloads, Übersetzungen, share, packages, Module, Klassen, Erweiterungsbaum, folder structure, directory layout, design folder, settings folder, translations, modules, classes, extension tree
type: help
---
# Die Ordnerstruktur einer Theme-Erweiterung

Diese Seite ist für Designer, die wissen wollen, wohin jede Datei eines Themes gehört. Wie die Ordner unter
`design/<name>/` im Einzelnen gegliedert sind, erklärt [Aufbau des Design-Verzeichnisses](../theme-design/aufbau-des-design-verzeichnisses.md).

## Der vollständige Baum

```
extension/mytheme/
├── ezinfo.php                      metadata for the About page (required)
├── extension.xml                   packaged metadata, read first (required)
├── composer.json                   when the theme is a Composer package
├── LICENSE                         the license text (required for publishing)
├── README.md                       what it is and how to switch it on
├── INSTALL.md, CHANGELOG.md        install steps and history (recommended)
├── design/
│   └── mytheme/                    the design (its name is the SiteDesign value)
│       ├── templates/              pagelayout.tpl and the templates
│       ├── override/templates/     templates chosen by override rules
│       ├── stylesheets/
│       ├── javascript/
│       ├── images/
│       └── fonts/
├── settings/
│   ├── design.ini.append.php       DesignExtensions[]=mytheme (required)
│   ├── site.ini.append.php         optional
│   ├── override.ini.append.php     override rules (optional)
│   ├── mytheme.ini                 a settings file of your own (optional)
│   └── siteaccess/<siteaccess>/    settings for one siteaccess (optional)
├── autoloads/                      template operators (optional)
├── classes/                        PHP classes (optional)
├── modules/                        modules with views (optional)
├── translations/<locale>/translation.ts
├── share/                          demo images, files, database seed data
└── packages/                       .ezpkg files shipped with the theme
```

## Was jeden Teil liest

| Teil | Gelesen von | Seite |
|---|---|---|
| `ezinfo.php`, `extension.xml` | Setup > Über, Setup > Extensions, den Upgrade-Prüfungen | [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md) |
| `design/<name>/` | Dem Template-System, sobald `DesignExtensions[]` die Erweiterung nennt | [Das Design registrieren](das-design-registrieren.md) |
| `settings/*.ini.append.php` | Dem Einstellungssystem, für jede aktive Erweiterung | [Einstellungsdateien in einer Erweiterung](einstellungsdateien-in-einer-erweiterung.md) |
| `settings/siteaccess/<sa>/` | Dem Einstellungssystem, nur solange dieser Siteaccess läuft | [Siteaccess-Einstellungen in einem Theme](siteaccess-einstellungen-in-einem-theme.md) |
| `autoloads/`, `classes/` | Dem Autoload-Array, nach `php bin/php/ezpgenerateautoloads.php -e` | [Template-Operatoren in einem Theme](template-operatoren-in-einem-theme.md) |
| `translations/` | Dem Übersetzungssystem, wenn `TranslationExtensions[]` die Erweiterung nennt | [Übersetzungen in einem Theme](uebersetzungen-in-einem-theme.md) |
| `share/` | Installern und Ihren eigenen Skripten | [Der share-Ordner](der-share-ordner.md) |
| `packages/` | Wer sie mit ezpm oder Setup > Pakete importiert | [Inhaltsklassen-Pakete mitliefern](inhaltsklassen-pakete-beilegen.md) |

## Drei veröffentlichte Themes im Vergleich

| Ordner | `sevenx_themes_simple` | `cjw_themes_jumper` | `sevenx_themes_media` |
|---|---|---|---|
| `design/` | `simple` | `starter`, `standard` | `media` |
| `settings/siteaccess/` | ja | `cjw_starter` | ja |
| `classes/`, `modules/` | - | ja | ja |
| `translations/` | ja | `ger-DE` | ja |
| `packages/` | 20 Inhaltsklassen-Pakete | - | - |
| `share/` | - | `content-images`, `content-files` | Datenbankschema und Daten |
| `CHANGELOG.md` | - | ja | - |

Lesen Sie die Bäume selbst: `ls extension/cjw_themes_jumper` auf einer Installation, die es hat, oder durchsuchen Sie das
Repository auf GitHub.

## Schritt für Schritt: das Gerüst von Hand anlegen

1. `mkdir -p extension/mytheme/design/mytheme/{templates,stylesheets,javascript,images} extension/mytheme/settings`
2. Schreiben Sie `settings/design.ini.append.php` ([Das Design registrieren](das-design-registrieren.md)).
3. Schreiben Sie `ezinfo.php` und `extension.xml`.
4. Fügen Sie `LICENSE` und `README.md` hinzu.
5. Prüfen Sie: `php -l extension/mytheme/ezinfo.php` und `xmllint --noout extension/mytheme/extension.xml`.

Oder lassen Sie [den Design-Erweiterungs-Assistenten](der-design-erweiterungs-assistent.md) alles schreiben.

## Verwandte Seiten

- [Tutorial: eine Theme-Erweiterung anlegen](../theme-design/tutorial-eine-theme-erweiterung-anlegen.md)
- [Was eine Theme-Erweiterung ist](was-eine-theme-erweiterung-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
