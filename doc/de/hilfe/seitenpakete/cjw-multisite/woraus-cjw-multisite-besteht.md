---
title: Woraus cjw_multisite besteht
audience: developer, admin, designer
category: install-upgrade
order: 76
summary: Die Teile des Startseiten-Pakets von CJW, eines nach dem anderen. Das Seitenpaket cjw_multisite und seine Einstellungsdateien, das Inhaltspaket cjw_multisite_democontent, das verlangte sevenx_classes, die Erweiterung, die die Bilder enthält, und das Quell-Repository mit seinem Packskript.
keywords: cjw_multisite, package.xml, settings-file, ini-site.php, ini-admin.php, ini-common.php, roles.php, preferences.php, install-scripts.php, post-install-fix.php, cjw-starter-install.php, pack.sh, cjw_multisite-ezpackage, Bestandteile, Packskript, parts, pack script
type: help
---
# Woraus cjw_multisite besteht

Diese Seite nimmt `cjw_multisite` auseinander: aus welchen Paketen es besteht, was jeder Ordner enthält und welche Datei das Setup wann
aufruft. Sie ist für Entwickler, die das Paket ändern oder ein ähnliches bauen wollen, und für Administratoren, die wissen wollen, was in
`var/storage/packages/7x/` landet. Jede hier genannte Datei liegt im Quell-Repository
[se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) und in den importierten Kopien einer Installation.

## Inhalt

1. [Die Pakete auf einen Blick](#die-pakete-auf-einen-blick)
2. [Das Seitenpaket cjw_multisite](#das-seitenpaket-cjw_multisite)
3. [Die Einstellungsdateien und wer sie aufruft](#die-einstellungsdateien-und-wer-sie-aufruft)
4. [Das Inhaltspaket cjw_multisite_democontent](#das-inhaltspaket-cjw_multisite_democontent)
5. [Was außerhalb der Pakete liegt](#was-außerhalb-der-pakete-liegt)
6. [Das Quell-Repository](#das-quell-repository)
7. [Verwandte Seiten](#verwandte-seiten)

## Die Pakete auf einen Blick

| Paket | Typ | Installationstyp | Enthält | Archivgröße |
|---|---|---|---|---|
| `cjw_multisite` | `site` | `import` | Vorschaubild, Logo, Lizenz, sieben Einstellungsdateien und ihre zwei Hilfsdateien | 97.634 Bytes |
| `cjw_multisite_democontent` | `contentobject` | `install` | 40 Klassendefinitionen, 138 Objektdateien, vier Nebendateien | 169.949 Bytes |
| `sevenx_classes` | `contentclass` | (sein eigener) | Die grundlegenden Klassen, geteilt mit `sevenx_multisite` | siehe [sevenx_classes](../sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md) |

Die Größen sind die `content-length` der Dateien unter `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/`. Das
Inhaltspaket ist klein, weil es keine Bilder mitbringt: Seine 158 Einträge `<simple-file>` zeigen in die Erweiterung (siehe
[Was außerhalb der Pakete liegt](#was-außerhalb-der-pakete-liegt)).

## Das Seitenpaket cjw_multisite

```text
packages/cjw_multisite/
  package.xml
  documents/LICENCE
  files/default/thumbnail/thumbnail.png
  files/default/image/logo.png
  settings/ini-site.php
  settings/ini-admin.php
  settings/ini-common.php
  settings/cjw-multi-site-installer.php
  settings/install-scripts.php
  settings/roles.php
  settings/preferences.php
  settings/post-install-fix.php
  settings/cjw-starter-install.php
```

Die Teile von `package.xml`, die zählen:

```xml
<dependencies>
  <requires>
    <require type="ezpackage" name="sevenx_classes" min-version="5.1" />
    <require type="ezpackage" name="cjw_multisite_democontent" min-version="5.1" />
  </requires>
</dependencies>
<settings>
  <settings-file filename='ini-site.php'/>
  <settings-file filename='ini-admin.php'/>
  <settings-file filename='ini-common.php'/>
  <settings-file filename='cjw-multi-site-installer.php'/>
  <settings-file filename='install-scripts.php'/>
  <settings-file filename='roles.php'/>
  <settings-file filename='preferences.php'/>
</settings>
<install />
<uninstall />
```

Das Seitenpaket hat keine eigenen Installationselemente (`<install />` ist leer). Seine Liste `<files>` nennt das Vorschaubild und das Logo
mit ihren MD5-Summen; das Logo wird am Ende der Installation auf dem Objekt des Template-Looks gesetzt. Die Einträge für Betreuer und
Changelog tragen den Namen „Administrator User“ und eine E-Mail des Betreuers.

## Die Einstellungsdateien und wer sie aufruft

Jede aufgeführte Einstellungsdatei definiert eine Funktion mit festem Namen; der Schritt zum Anlegen der Websites im Setup sucht nach diesen
Namen (siehe [Installationsskripte in einem Seitenpaket](../grundlagen/installationsskripte-in-einem-seitenpaket.md)). Jede davon erzeugt nur einen
`cjwMultiSiteInstaller` und übergibt an ihn:

| Datei | Funktion | Ruft auf |
|---|---|---|
| `ini-site.php` | `eZSiteINISettings( $parameters )` | `siteINISettings()`: Einstellungen für Menü, Override, Toolbar, Website, Bild, Inhalt, Template, Menü der Inhaltsstruktur und ODF des Benutzer-Siteaccess |
| `ini-admin.php` | `eZSiteAdminINISettings( $parameters )` | `adminINISettings()` |
| `ini-common.php` | `eZSiteCommonINISettings( $parameters )` | `commonINISettings()`: die Dateien in `settings/override` |
| `install-scripts.php` | `eZSitePreInstall()` und `eZSitePostInstall( &$parameters )` | `preInstall()` vor den Paketen, `postInstall()` danach |
| `roles.php` | `eZSiteRoles( $parameters )` | `siteRoles()` |
| `preferences.php` | `eZSitePreferences( $parameters )` | `sitePreferences()` |
| `cjw-multi-site-installer.php` | keine | Definiert `class cjwMultiSiteInstaller extends eZSiteInstaller` (5.939 Zeilen) |

`post-install-fix.php` und `cjw-starter-install.php` stehen nicht in der Liste `<settings>`. Der Installer lädt sie oben mit
`require_once`:

```php
require_once dirname( __FILE__ ) . '/post-install-fix.php';
require_once dirname( __FILE__ ) . '/cjw-starter-install.php';
```

`post-install-fix.php` (593 Zeilen) sagt von sich, dass es aus den Hilfsfunktionen des MultiSite-Installers von 7x erzeugt wurde, indem
Funktionen `sevenx*` in `cjw*` umbenannt wurden, damit beide Dateien nie als eine Deklaration aufeinandertreffen, und dass weggelassen wurde,
was nur die Demo-Inhalte von 7x brauchen. `cjw-starter-install.php` (827 Zeilen) enthält die Schritte für die Startinhalte. Beide sind in
[Die Installerskripte](die-installerskripte-von-cjw-multisite.md) beschrieben.

## Das Inhaltspaket cjw_multisite_democontent

```text
packages/cjw_multisite_democontent/
  package.xml
  documents/LICENCE
  ezcontentclass/class-<identifier>.xml     40 files
  ezcontentobject/contentobjects.xml
  ezcontentobject/object-cjw-starter-o-*.xml  138 files
  cjw-class-additions.xml
  cjw-explayouts.json
  cjw-paths.json
  cjw-starter-import.json
```

Seine Liste `<install>` hat 40 Elemente `ezcontentclass` und ein Element `ezcontentobject` (`contentobjects`); seine Liste `<uninstall>`
entfernt die Objekte wieder. Die vier Nebendateien sind keine Installationselemente: Die Pakethandler des Kernels lesen sie nie, der
Installer des Seitenpakets tut es. Details in [Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md).

## Was außerhalb der Pakete liegt

| Was | Wo | Gelesen von |
|---|---|---|
| Die Bilder (155 Einträge) und Dateien (3 Einträge) der Startinhalte | `extension/cjw_themes_jumper/share/content-images/`, `share/content-files/` | `cjwLinkStarterFilesFromExtension()`, bevor die Pakete installiert werden |
| Das Design `starter` und seine Einstellungen | `extension/cjw_themes_jumper/` | den Benutzer-Siteaccesses zur Laufzeit |
| Der Veranstaltungs-Feed von TMV (wahlweise) | `extension/cjw_tmv/` | aus der Liste der Erweiterungen weggelassen, wenn der Ordner nicht da ist |
| Die Schemas der Erweiterungen | `extension/<name>/share/db_schema.dba` | `extensionSchemas()` in `preInstall()` |
| Die Standardlayouts der Administration | `extension/explayouts/share/fragments/admin_layouts_db_data.dba` | `postInstallImportAdminLayouts()` |

Die 158 Einträge für Simple Files nennen 77 verschiedene Dateien in der Erweiterung; die Kopie der Erweiterung in 6.0.15 enthält in diesen
beiden Ordnern 74 Bilder und 3 Dateien.

## Das Quell-Repository

[se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) ist laut seinem ersten Commit wie das
Paket-Repository von 7x aufgebaut:

| Pfad | Zweck |
|---|---|
| `packages/cjw_multisite/`, `packages/cjw_multisite_democontent/` | Die zwei Pakete, entpackt |
| `bin/pack.sh` | Packt jeden Ordner unter `packages/` (außer `package_set`) mit `tar -cz` in `<name>.ezpkg` in `--output-dir` |
| `bin/repack_extension.sh`, `bin/ezpxmlextfilelist.php`, `ant/` | Aus dem Paket-Repository von 7x übernommene Hilfsmittel |
| `.github/FUNDING.yml` | Die Förderlinks des Repositorys |

`pack.sh` lässt Ordner und Dateien der Versionsverwaltung (`.git`, `.gitignore`, `.gitattributes`, `.gitmodules`, `.svn`, `.hg`), Reste
von Editoren und Systemen (`.DS_Store`, `.idea`, `.vscode`, `*~`, `*.swp`), `node_modules`, `.cache` und `pack_and_publish.bat` (der Commit
vom 14.10.2026) weg. Übergeben Sie `--output-dir`; der Standard des Skripts ist ein Verzeichnis unter `/tmp`.

```bash
cd cjw_multisite-ezpackage
sh bin/pack.sh --output-dir=../build
tar -tzf ../build/cjw_multisite.ezpkg | head
```

Wie ein neu gebautes Archiv den Paketserver erreicht, ist in
[Wie ein Release den Paketserver erreicht](../paketserver/wie-ein-release-den-paketserver-erreicht.md) beschrieben.

## Verwandte Seiten

- [cjw_multisite](README.md)
- [Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md)
- [Die Installerskripte](die-installerskripte-von-cjw-multisite.md)
- [Die Datei .ezpkg](../grundlagen/die-datei-ezpkg.md)
- [Referenz zu package.xml](../grundlagen/referenz-zu-package-xml.md)
- [Abhängigkeiten und die Installationsreihenfolge](../grundlagen/abhaengigkeiten-und-die-installationsreihenfolge.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
