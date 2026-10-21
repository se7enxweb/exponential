---
title: Die Quell-Repositorys
audience: developer, designer, partner
category: install-upgrade
order: 37
summary: Wo die entpackte Quelle jedes Pakets auf dem Paketserver liegt - sevenx-ezpackage, cjw_multisite-ezpackage und die Repositorys -ezpackage für ezwebin, ezflow, ezdemo, ezpaypal, ezstarrating, ezgmaplocation, ezwt und ezcomments -, ihr gemeinsamer Aufbau, ihre Skripte und welches Paket woher kommt.
keywords: Quell-Repository, sevenx-ezpackage, cjw_multisite-ezpackage, ezwebin-ezpackage, ezflow-ezpackage, ezdemo-ezpackage, ezpaypal-ezpackage, ezstarrating-ezpackage, ezgmaplocation-ezpackage, ezwt-ezpackage, ezcomments-ezpackage, build.sh, pack.sh, Ordner packages, source repository, packages folder
type: help
---
# Die Quell-Repositorys

Diese Seite ist für Entwickler, die ein Paket ändern, neu bauen oder ihr eigenes Paket auf einem aufbauen wollen. Der Paketserver enthält nur
die fertigen Archive `.ezpkg`; ihre entpackte Quelle, ein Ordner pro Paket, liegt in eigenen GitHub-Repositorys von `se7enxweb`. Diese Seite
führt sie auf, zeigt ihren Aufbau und ordnet jedes Paket des Index von 6.0.15 seinem Repository zu. Jedes Repository wurde am 18.10.2026 mit
`gh api` (nur lesend) geprüft.

## Inhalt

1. [Die Repositorys](#die-repositorys)
2. [Der gemeinsame Aufbau](#der-gemeinsame-aufbau)
3. [Die Skripte](#die-skripte)
4. [Welches Paket woher kommt](#welches-paket-woher-kommt)
5. [Ein Repository lesen](#ein-repository-lesen)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Repositorys

| Repository | Paketordner in `packages/` | Hinweise |
|---|---|---|
| [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage) | 14: `sevenx_multisite`, `sevenx_multisite_clean`, `sevenx_multisite_democontent`, `sevenx_multisite_democontent_clean`, `sevenx_site`, `sevenx_site_clean`, `sevenx_classes`, `sevenx_democontent`, `sevenx_democontent_clean`, `sevenx_banners`, `sevenx_design_blue`, `sevenx_design_cleangray`, `sevenx_design_gray`, `sevenx_extension` | die Pakete von 7x, darunter das Standard-Seitenpaket; Geschichte ab 2006, 1226 Commits; README und LICENSE seit dem 19.10.2026 |
| [se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) | 2: `cjw_multisite`, `cjw_multisite_democontent` | angelegt am 07.10.2026; gepackt mit `bin/pack.sh` und seit dem 19.10.2026 `packages/build.sh`; README und LICENSE seit dem 19.10.2026 |
| [se7enxweb/ezwebin-ezpackage](https://github.com/se7enxweb/ezwebin-ezpackage) | 10: `ezwebin_site`, `ezwebin_site_clean`, `ezwebin_classes`, `ezwebin_democontent`, `ezwebin_democontent_clean`, `ezwebin_banners`, `ezwebin_design_blue`, `ezwebin_design_cleangray`, `ezwebin_design_gray`, `ezwebin_extension` | ein Fork von `ezwebin` des ursprünglichen Herstellers |
| [se7enxweb/ezflow-ezpackage](https://github.com/se7enxweb/ezflow-ezpackage) | 7: `ezflow_site`, `ezflow_site_clean`, `ezflow_classes`, `ezflow_democontent`, `ezflow_democontent_clean`, `ezflow_design`, `ezflow_extension` | ein Fork von `ezflow` |
| [se7enxweb/ezdemo-ezpackage](https://github.com/se7enxweb/ezdemo-ezpackage) | 6: `ezdemo_site`, `ezdemo_site_clean`, `ezdemo_classes`, `ezdemo_democontent`, `ezdemo_democontent_clean`, `ezdemo_extension` | ein Fork von `ezdemo` |
| [se7enxweb/ezpaypal-ezpackage](https://github.com/se7enxweb/ezpaypal-ezpackage) | 1: `ezpaypal_extension` | hat eine `README.md` |
| [se7enxweb/ezstarrating-ezpackage](https://github.com/se7enxweb/ezstarrating-ezpackage) | 1: `ezstarrating_extension` | ein Fork von `ezstarrating` |
| [se7enxweb/ezgmaplocation-ezpackage](https://github.com/se7enxweb/ezgmaplocation-ezpackage) | 1: `ezgmaplocation_extension` | ein Fork von `ezgmaplocation`; kein `packages/build.sh` |
| [se7enxweb/ezwt-ezpackage](https://github.com/se7enxweb/ezwt-ezpackage) | 1: `ezwt_extension` | ein Fork von `ezwt`; kein `packages/build.sh` |
| [se7enxweb/ezcomments-ezpackage](https://github.com/se7enxweb/ezcomments-ezpackage) | 1: `ezcomments_extension` | ein Fork von `ezcomments`; dieses Paket steht nicht im Index von 6.0.15 |

Die alten Paketordner der Releases 3.x bis 5.x, nicht mehr auf dem Server, werden in
[se7enxweb/deprecated.ezpublish.packages.ezpublish.one](https://github.com/se7enxweb/deprecated.ezpublish.packages.ezpublish.one)
aufbewahrt.

> **Hinweis**
> `plain_site` liegt auf dem Paketserver, aber in keinem dieser Repositorys. Um es zu ändern, gehen Sie von der `.ezpkg` selbst aus
> ([Ein Paket von Hand herunterladen](ein-paket-von-hand-herunterladen.md)).

## Der gemeinsame Aufbau

`sevenx-ezpackage` und `cjw_multisite-ezpackage` haben diesen Aufbau, ebenso ezwebin, ezflow, ezstarrating, ezgmaplocation und ezwt
(`ezflow-ezpackage` hat außerdem `doc/`). `ezdemo-ezpackage`, `ezpaypal-ezpackage` und `ezcomments-ezpackage` haben nur `packages/` (plus
Webdateien in `ezdemo-ezpackage` und eine `README.md` in `ezpaypal-ezpackage`). `build.sh` liegt in sieben davon: sevenx, cjw_multisite (seit
dem 19.10.2026), ezwebin, ezflow, ezdemo, ezpaypal und ezstarrating.

```text
packages/
  build.sh                    packs every folder into <folder>.ezpkg
  <name>/                     one package, unpacked
    package.xml               the definition
    documents/LICENCE         the license text
    files/default/...         thumbnail and other files
    settings/...              install scripts of a site package
    ezcontentclass/...        classes of a contentclass package
    ezextension/...           the extension of an extension package
bin/
  pack.sh                     packs every folder, into --output-dir
  repack_extension.sh         rebuilds an extension package from an installation
  ezpxmlextfilelist.php       lists the files of an extension directory (-e <extension>)
ant/
  createzpkg.sh               packs one folder
  ezwebin.properties
```

Was in jede Art von Paketordner gehört, ist in [Was ein Seiteninstallationspaket ist](../grundlagen/was-ein-seiteninstallationspaket-ist.md) und
[Die Datei .ezpkg](../grundlagen/die-datei-ezpkg.md) beschrieben.

## Die Skripte

| Skript | Was es tut | Lässt weg |
|---|---|---|
| `packages/build.sh` | in `packages/` ausgeführt: für jeden Ordner `tar -zcf ../<folder>.ezpkg` seines Inhalts, sodass die Dateien `.ezpkg` neben den Ordnern landen | `.git`, `.gitignore`, `.gitattributes`, `.gitmodules`, `.svn`, `.hg`, `.DS_Store`, `.idea`, `.vscode`, `node_modules`, `*~`, `*.swp`; in den Repositorys von 7x und CJW außerdem `*.bak` und `*.bak.*` (seit dem 19.10.2026) |
| `bin/pack.sh` | aus dem Wurzelverzeichnis des Repositorys ausgeführt: packt jeden Ordner von `packages/` außer `package_set` in `--output-dir=<dir>` (Standard `/tmp/ezwebin/packages`) | dieselbe Liste plus `.cache` und `pack_and_publish.bat` |
| `ant/createzpkg.sh` | `createzpkg.sh <source> <destination>`: packt einen Ordner in `<destination>.ezpkg` | `.svn`, `.cache`, `pack_and_publish.bat` |
| `bin/repack_extension.sh` | `--with-ezp=<dir>`: erstellt das Erweiterungspaket aus einer Installation neu; das Ergebnis landet in `var/storage/packages/local/` | |

Die oberste Ebene des Archivs ist der Inhalt des Paketordners, sodass `package.xml` oben in der `.ezpkg` liegt, wie der Importer es erwartet.
`build.sh` kam am 14.10.2026 zu `sevenx-ezpackage` (das Build-Skript für Pakete, das Ordner der Versionsverwaltung und Editor-Dateien aus
jedem Paket heraushält). Wie Sie es nutzen: [Mit build.sh neu bauen](../eigene/mit-build-sh-neu-bauen.md).

> **Warnung**
> Geben Sie `bin/pack.sh` ein eigenes `--output-dir`. Sein Standard liegt im geteilten temporären Ordner des Systems.

## Welches Paket woher kommt

| Pakete des Index von 6.0.15 | Repository |
|---|---|
| `sevenx_*` (14) | sevenx-ezpackage |
| `cjw_multisite`, `cjw_multisite_democontent` | cjw_multisite-ezpackage |
| `ezwebin_*` (10) | ezwebin-ezpackage |
| `ezflow_*` (7) | ezflow-ezpackage |
| `ezdemo_*` (6) | ezdemo-ezpackage |
| `ezpaypal_extension`, `ezstarrating_extension`, `ezgmaplocation_extension`, `ezwt_extension` | jeweils ihr eigenes Repository `-ezpackage` |
| `plain_site` | keines davon |

Das sind 43 der 44 Pakete des Index von 6.0.15.

## Ein Repository lesen

```bash
# the folders and their history, without a full checkout of large binaries
git clone --filter=blob:none https://github.com/se7enxweb/sevenx-ezpackage.git
cd sevenx-ezpackage
ls packages/
git log --oneline -- packages/sevenx_multisite
```

Commit-Nachrichten in diesen Repositorys beginnen mit `Added:`, `Updated:`, `Removed:` oder `Renamed:` und nennen das Paket, zum Beispiel
ein Commit, nach dem der Installer des Pakets sevenx_site ezownerchange aktiviert, sodass das Ändern des Eigentümers von Inhalten in einer
neuen Installation verfügbar ist. Dieselbe Änderung erscheint dann als eigener Commit auf dem Paketserver
([Wie ein Release den Paketserver erreicht](wie-ein-release-den-paketserver-erreicht.md)).

Um ein Paket für sich selbst zu ändern, forken Sie sein Repository ([Ein Seitenpaket forken](../eigene/ein-seitenpaket-forken.md)); um es
für alle zu ändern, öffnen Sie dort einen Pull Request, und für ein neues Paket siehe
[Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Wie ein Release den Paketserver erreicht](wie-ein-release-den-paketserver-erreicht.md)
- [Der Paketserver auf GitHub](der-paketserver-auf-github.md)
- [Ein eigenes Seitenpaket erstellen](../eigene/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
