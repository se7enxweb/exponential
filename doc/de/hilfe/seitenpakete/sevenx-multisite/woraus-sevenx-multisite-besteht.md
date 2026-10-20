---
title: Woraus sevenx_multisite besteht
audience: developer, admin, designer
category: install-upgrade
order: 61
summary: Die Dateien im Seitenpaket sevenx_multisite und in den Paketen, die es verlangt, was jede Einstellungsdatei tut und welche Teile der Installation von außerhalb der Pakete kommen.
keywords: sevenx_multisite, package.xml, settings-file, ini-site.php, ini-admin.php, ini-common.php, roles.php, preferences.php, install-scripts.php, post-install-fix.php, sevenx-multi-site-installer.php, ezpkg, Bestandteile, Dateien, parts, files
type: help
---
# Woraus sevenx_multisite besteht

Diese Seite ist für Entwickler und Administratoren, die das Paket `sevenx_multisite` öffnen und wissen wollen, wofür jede Datei da ist. Sie
beantwortet: welche Dateien das Seitenpaket enthält, was seine `package.xml` angibt, welche Pakete es mitzieht und welche Teile einer
fertigen Installation von woanders kommen.

## Inhalt

1. [Die Dateien des Seitenpakets](#die-dateien-des-seitenpakets)
2. [Was package.xml angibt](#was-packagexml-angibt)
3. [Die Einstellungsdateien](#die-einstellungsdateien)
4. [Die verlangten Pakete](#die-verlangten-pakete)
5. [Was nicht in den Paketen ist](#was-nicht-in-den-paketen-ist)
6. [Das Paket selbst öffnen](#das-paket-selbst-öffnen)

## Die Dateien des Seitenpakets

Der Ordner `packages/sevenx_multisite/` von [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage) enthält zwölf
Dateien, und die veröffentlichte `sevenx_multisite.ezpkg` von 6.0.15 enthält dieselben:

```text
package.xml
documents/LICENCE
files/default/image/logo.png
files/default/thumbnail/thumbnail.png
settings/ini-admin.php
settings/ini-common.php
settings/ini-site.php
settings/install-scripts.php
settings/post-install-fix.php
settings/preferences.php
settings/roles.php
settings/sevenx-multi-site-installer.php
```

Im Seitenpaket selbst gibt es keine Inhalte und keine Klassendefinition: Diese kommen aus den Paketen, die es verlangt.

## Was package.xml angibt

| Element | Wert in `sevenx_multisite` |
|---|---|
| `<package version>` | `6.0.15-0`, mit `install_type="import"` |
| `<name>` | `sevenx_multisite` |
| `<summary>` | `MultiSite Default Installation` |
| `<vendor>` | `7x` |
| `<type value>` | `site` |
| `<ezpublish><version>` | `6.0.15-stable` (benannte Version `6.0`) |
| `<version>` | Nummer `6.0`, Release `15-stable` |
| `<licence>` | `GPL`, mit dem Text in `documents/LICENCE` |
| `<state>` | `stable` |
| `<maintainers>` | ein Betreuer mit der Rolle `lead` und einer E-Mail des Betreuers |
| `<files>` | eine Sammlung `default` mit einem `thumbnail` (`thumbnail.png`) und einem `image` (`logo.png`), jeweils mit einer `md5sum` |
| `<requires>` | `sevenx_classes` und `sevenx_multisite_democontent`, beide `type="ezpackage"`, `min-version="5.1"` |
| `<settings>` | sieben Einträge `<settings-file>`, unten aufgeführt |
| `<install>`, `<uninstall>` | leer: Das Paket installiert keine eigenen Elemente |

Der `<host>` in `<packaging>` nennt die Maschine, auf der das Paket gebaut wurde. Die Referenz Element für Element ist
[Referenz zu package.xml](../grundlagen/referenz-zu-package-xml.md).

## Die Einstellungsdateien

`CreateSites` (`kernel/setup/steps/ezstep_create_sites.php`) bindet jedes `<settings-file>` des gewählten Seitenpakets aus seinem Ordner
`settings/` ein und ruft dann die Funktionen auf, die diese Dateien definieren, wenn sie existieren. Die sieben Dateien, die in der
`package.xml` von `sevenx_multisite` aufgeführt sind, in ihrer Reihenfolge dort:

| Datei | Definiert | Ruft an `sevenxMultiSiteInstaller` auf |
|---|---|---|
| `ini-site.php` | `eZSiteINISettings()` | `siteINISettings()`: Einstellungen des öffentlichen Siteaccess |
| `ini-admin.php` | `eZSiteAdminINISettings()` | `adminINISettings()`: Einstellungen des Siteaccess der Administration |
| `ini-common.php` | `eZSiteCommonINISettings()` | `commonINISettings()`: Einstellungen, die nach `settings/override/` geschrieben werden |
| `sevenx-multi-site-installer.php` | die Klasse `sevenxMultiSiteInstaller` | (die Klasse selbst, 7692 Zeilen) |
| `install-scripts.php` | `eZSitePreInstall()`, `eZSitePostInstall()` | `preInstall()` und `postInstall()` |
| `roles.php` | `eZSiteRoles()` | `siteRoles()` |
| `preferences.php` | `eZSitePreferences()` | `sitePreferences()` |

Jede Hülle hat wenige Zeilen: Sie erzeugt einen neuen `sevenxMultiSiteInstaller` mit den Parametern, die sie bekommen hat, und gibt zurück,
was die Methode zurückgibt.

`post-install-fix.php` ist die achte Datei. Sie ist **kein** `<settings-file>`: Der Installer lädt sie selbst mit
`require_once dirname( __FILE__ ) . '/post-install-fix.php';` in seiner ersten Codezeile. Sie enthält die Funktionen, die die Platzierung der
Knoten, Verweise der Layouts, Menüs, Tags und URL-Aliase reparieren, nachdem die Inhalte da sind. Siehe
[Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md).

> **Hinweis**
> `sevenx_multisite` und `sevenx_multisite_clean` bringen Byte für Byte gleiche Kopien aller acht PHP-Dateien mit. Nur ihre `package.xml`
> unterscheidet sich: Name, Zusammenfassung, Beschreibung und das Demo-Inhaltspaket, das sie verlangen.

## Die verlangten Pakete

| Paket | Typ | Inhalt | Seite |
|---|---|---|---|
| `sevenx_classes` | `contentclass` | 33 Klassendefinitionen in `ezcontentclass/`, `install_type="install"` | [sevenx_classes](sevenx-classes-das-paket-der-inhaltsklassen.md) |
| `sevenx_multisite_democontent` | `contentobject` | 31 Klassendefinitionen, 291 Objektdateien, 194 Bild- und Datei-Blobs in `simplefiles/` | [Die Demo-Inhaltspakete](die-demo-inhaltspakete.md) |

Wie das Setup `<requires>` auflöst und in welcher Reihenfolge es sie installiert, ist in
[Abhängigkeiten und die Installationsreihenfolge](../grundlagen/abhaengigkeiten-und-die-installationsreihenfolge.md) beschrieben.

## Was nicht in den Paketen ist

Mehrere Teile einer fertigen Installation reisen in keiner `.ezpkg`:

| Teil | Woher es kommt |
|---|---|
| Die Templates und das Design beider Websites | die Erweiterung `sevenx_themes_media` (Design `media`), mit `sevenx_themes_simple` danach |
| Die Siteaccess-Einstellungen von `bold` und `bold_ger` | von `sevenx_themes_media` in `settings/siteaccess/bold` und `bold_ger` mitgebracht, vom Installer vervollständigt |
| Layouts, Zonen, Blöcke, Sammlungen und Regeln | `extension/sevenx_themes_media/share/db_data.dba`, siehe [Layoutdaten und share-DBA-Dateien](layoutdaten-und-share-dba-dateien.md) |
| Tags (`eztags`) und die Sternebewertung des Testprodukts | dieselbe `db_data.dba` |
| Die Newsletter-Klassen und der Newsletter-Baum | die eigenen Pakete der Erweiterung `cjw_newsletter`, von einem Schritt der Nachinstallation importiert |
| Die Layouts der Administration | `extension/explayouts/share/fragments/admin_layouts_db_data.dba` |

Die Erweiterungen müssen also in der Installation liegen, bevor das Seitenpaket installiert wird. Der Installer sieht nach einigen davon
(`exp_adminui`, `expai`, `sevenx_authentication_2fa`, `cjw_newsletter`) und überspringt ihre Teile, wenn sie fehlen; die anderen werden
einfach in `ActiveExtensions` aufgeführt.

## Das Paket selbst öffnen

Eine `.ezpkg` ist ein mit gzip komprimiertes tar-Archiv, die üblichen Werkzeuge lesen sie also:

```bash
curl -sO https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_multisite.ezpkg
tar -tzf sevenx_multisite.ezpkg
tar -xzOf sevenx_multisite.ezpkg package.xml | less
```

Eine importierte Kopie liegt unter `var/storage/packages/7x/sevenx_multisite/` jeder Installation, die es installiert hat, neben einem
Ordner `.cache`, den der Kernel schreibt. Mehr zum Format: [Die Datei .ezpkg](../grundlagen/die-datei-ezpkg.md).

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Installationsskripte in einem Seitenpaket](../grundlagen/installationsskripte-in-einem-seitenpaket.md)
- [Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md)
- [Das lokale Paket-Repository](../grundlagen/das-lokale-paket-repository.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
