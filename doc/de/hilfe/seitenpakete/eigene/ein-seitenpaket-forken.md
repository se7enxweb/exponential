---
title: Ein Seitenpaket forken
audience: developer, designer, partner
category: install-upgrade
order: 86
summary: Ein eigenes Seitenpaket von einem funktionierenden aus beginnen - welches Paket Sie kopieren, wie die Quell-Repositorys aufgebaut sind, was Sie in package.xml und den Dateien des Installers umbenennen und welche Namen eindeutig sein müssen, damit eine Installation Ihr Paket nie mit dem Original verwechselt.
keywords: Fork, Seitenpaket, sevenx-ezpackage, cjw_multisite-ezpackage, package.xml, umbenennen, Paketname, Hersteller, requires, Installer-Klasse, settings-file, cjwMultiSiteInstaller, fork, site package, rename, package name, vendor, installer class
type: help
---
# Ein Seitenpaket forken

Diese Seite ist für Entwickler, die ein eigenes Seitenpaket wollen und lieber von einem beginnen, das sich installieren lässt, als von einer
leeren `package.xml`. Sie zeigt, woher die Pakete von 7x kommen, wie Sie eines kopieren und was sich ändern muss, damit Ihre Kopie ein
eigenes Paket ist.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, das Ergebnis per Pull Request an
se7enxweb/exponential.packages.exponential.earth einzureichen, damit es jede Installation über den Setup-Assistenten, den Kickstarter und
ezupdate erreicht: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Wählen, wovon Sie ausgehen](#wählen-wovon-sie-ausgehen)
2. [Der Aufbau des Quell-Repositorys](#der-aufbau-des-quell-repositorys)
3. [Das Repository kopieren](#das-repository-kopieren)
4. [Das Paket umbenennen](#das-paket-umbenennen)
5. [Den Installer umbenennen](#den-installer-umbenennen)
6. [Entscheiden, was als Anforderung bleibt](#entscheiden-was-als-anforderung-bleibt)
7. [Checkliste](#checkliste)

## Wählen, wovon Sie ausgehen

| Ausgehen von | Wann | Quelle |
|---|---|---|
| `sevenx_multisite` | eine Website mit Demo-Inhalten, mehreren Siteaccesses und Sprachen, Layouts | [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage), `packages/sevenx_multisite/` |
| `sevenx_multisite_clean` | dieselbe Struktur ohne die Demo-Artikel | dasselbe Repository |
| `sevenx_site`, `sevenx_site_clean` | eine einfachere Website | dasselbe Repository |
| `cjw_multisite` | ein Fork, der die Demo-Inhalte und die Installer-Klasse bereits ersetzt hat: das beste Beispiel dafür, was ein Fork ändert | [se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) |

Was jedes installiert, ist in [sevenx_multisite](../sevenx-multisite/README.md) und [cjw_multisite](../cjw-multisite/README.md) beschrieben.

## Der Aufbau des Quell-Repositorys

Beide Repositorys haben einen Ordner pro Paket unter `packages/`; der Ordnername ist der Paketname, und der Ordner enthält, was das Archiv
`.ezpkg` enthalten wird:

```text
packages/
├── build.sh                         (sevenx-ezpackage) packs every folder into <folder>.ezpkg
├── sevenx_multisite/
│   ├── package.xml                  the definition
│   ├── documents/LICENCE
│   ├── files/default/thumbnail/     the picture the setup wizard shows
│   ├── files/default/image/
│   └── settings/                    the installer files named in <settings>
├── sevenx_classes/
│   ├── package.xml
│   └── ezcontentclass/class-*.xml
└── sevenx_multisite_democontent/
    ├── package.xml
    ├── ezcontentclass/ ezcontentobject/ simplefiles/
    └── documents/LICENCE
bin/pack.sh, bin/repack_extension.sh, ant/createzpkg.sh
```

`cjw_multisite-ezpackage` hat dieselben Skripte in `bin/` und `ant/` und seit dem 19.10.2026 dasselbe `packages/build.sh`; siehe
[Mit build.sh neu bauen](mit-build-sh-neu-bauen.md) für beide Wege des Packens.

## Das Repository kopieren

```bash
# on GitHub: Fork, or create an empty repository <your-org>/<yourname>-ezpackage
git clone https://github.com/<your-org>/<yourname>-ezpackage.git
cd <yourname>-ezpackage
# keep only the package folders you will change or ship
git mv packages/sevenx_multisite packages/acme_site
```

Behalten Sie die Geschichte: Das Quell-Repository von 7x reicht bis 2006 zurück, und `git log -- packages/sevenx_multisite` erklärt, warum die
meisten Zeilen des Installers da sind.

## Das Paket umbenennen

Der Paketname ist das, was der Index aufführt, was `kickstart.ini` in `Site_package` nennt, und das Verzeichnis, in das das Paket importiert
wird. Ändern Sie ihn in `package.xml` und im Ordnernamen zusammen:

```xml
<package version="6.0.15-0" development="false" is_active="true" install_type="import">
  <name>acme_site</name>
  <summary>Acme starter site</summary>
  <description>What an editor gets after installing it.</description>
  <vendor>acme</vendor>
  <type value="site" />
  ...
</package>
```

| Feld | Regel | Warum |
|---|---|---|
| `<name>` | nur Kleinbuchstaben, Ziffern und `_`; `eZPackage::isValidName()` akzeptiert einen Namen nur, wenn die Transformation `identifier` ihn unverändert lässt | die Seite zum Hochladen und `eZPackage::import()` weisen andere Namen ab |
| `<name>` | eindeutig: nicht der Name eines Pakets von 7x | ein Name bedeutet für eine Installation ein Paket: Der Kickstarter nutzt ein bereits importiertes Paket, statt es herunterzuladen, der Setup-Assistent im Browser entfernt ein importiertes Seitenpaket dieses Namens und lädt es erneut herunter, ein verlangtes Paket dieses Namens wird behalten, wenn seine Version hoch genug ist (`downloadAndImportPackage()` und `downloadDependantPackages()` in `kernel/setup/steps/ezstep_site_types.php`), und ein Hochladen eines vorhandenen Namens wird abgewiesen (existiert bereits) |
| `<vendor>` | Ihre Organisation | der Import legt das Paket in `var/storage/packages/<vendor>/`, den Hersteller in einen Verzeichnisnamen umgewandelt (`eZ systems` wurde zu `ez-systems`) |
| `<summary>` | eine Zeile | der Setup-Assistent zeigt sie unter dem Vorschaubild |
| `<maintainers>`, `<changelog>` | Ihre Namen und eine eigene Funktionsadresse | behalten Sie nicht die E-Mail des Betreuers von 7x; Prüfende und Benutzer schreiben an sie |
| `<packaging><host>` | Ihr Host, oder lassen Sie es so, wie der Kernel es schreibt | zur Information |

Die vollständige Liste der Elemente steht auf [Referenz zu package.xml](../grundlagen/referenz-zu-package-xml.md); die Pflichtfelder auf
[Pflichtfelder in package.xml](../anforderungen/pflichtfelder-in-package-xml.md).

## Den Installer umbenennen

Das Element `<settings>` führt PHP-Dateien aus `settings/` auf, die `CreateSites` mit `include_once` einbindet:

```xml
<settings>
  <settings-file filename='ini-site.php'/>
  <settings-file filename='ini-admin.php'/>
  <settings-file filename='ini-common.php'/>
  <settings-file filename='acme-site-installer.php'/>
  <settings-file filename='install-scripts.php'/>
  <settings-file filename='roles.php'/>
  <settings-file filename='preferences.php'/>
</settings>
```

Benennen Sie die Datei des Installers und ihre Klasse um (`sevenxMultiSiteInstaller` wurde im Fork von CJW zu `cjwMultiSiteInstaller`) und
aktualisieren Sie die kleinen Hook-Dateien, die sie erzeugen (`install-scripts.php`, `ini-site.php`, `roles.php` und die anderen rufen jeweils
`new sevenxMultiSiteInstaller( ... )` auf). Eine Klasse mit eigenem Namen kann nicht mit einer Installer-Klasse von 7x kollidieren, und die
Debug-Ausgabe nennt Ihren Installer. Details: [Das Installerskript ändern](das-installerskript-aendern.md).

## Entscheiden, was als Anforderung bleibt

`<dependencies><requires>` nennt die Pakete, die vor dem Seitenpaket installiert werden:

```xml
<requires>
  <require type="ezpackage" name="sevenx_classes" min-version="5.1" />
  <require type="ezpackage" name="acme_site_democontent" min-version="1.0.0-1" />
</requires>
```

Sie können ein Paket von 7x behalten, wie es ist, so wie `cjw_multisite` `sevenx_classes` behält, und nur ersetzen, was Sie ändern. Ein
verlangtes Paket wird in derselben `index.xml` nachgeschlagen wie das Seitenpaket, sodass Ihr Index es auch aufführen muss (seine `url` darf
auf den Server von 7x zeigen). Siehe [Abhängigkeiten angeben](../anforderungen/abhaengigkeiten-angeben.md).

## Checkliste

- [ ] Ordnername, `<name>` und jeder Verweis auf den alten Namen geändert.
- [ ] `<vendor>`, `<summary>`, `<description>`, Betreuer und Changelog sind Ihre.
- [ ] Datei und Klasse des Installers umbenannt, Hook-Dateien aktualisiert.
- [ ] `<requires>` führt genau die Pakete auf, die Sie ausliefern oder wiederverwenden.
- [ ] Vorschaubild ersetzt (`files/default/thumbnail/`), Lizenzdatei geprüft.
- [ ] Version für Ihr erstes Release zurückgesetzt ([Eigene Pakete versionieren](eigene-pakete-versionieren.md)).

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Was ein Seiteninstallationspaket ist](../grundlagen/was-ein-seiteninstallationspaket-ist.md)
- [Woraus sevenx_multisite besteht](../sevenx-multisite/woraus-sevenx-multisite-besteht.md)
- [sevenx und CJW im Vergleich](../cjw-multisite/sevenx-multisite-und-cjw-multisite-im-vergleich.md)
- [Das GitHub-Repository anlegen](../../../../en/us/help/designers/distribution/creating-the-github-repository.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
