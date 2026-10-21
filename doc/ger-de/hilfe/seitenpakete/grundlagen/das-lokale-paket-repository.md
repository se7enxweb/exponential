---
title: "Das lokale Paket-Repository"
audience: admin, developer, hosting
category: install-upgrade
order: 20
summary: Wo eine Installation ihre Pakete aufbewahrt (var/storage/packages), wie die Herstellerverzeichnisse, local und das System-Repository gewählt werden, was das Verzeichnis .cache ist, wie eine lokale index.xml eine Offline-Installation ermöglicht und was Sie gefahrlos von Hand ändern können.
keywords: Paket-Repository, var/storage/packages, RepositoryDirectory, StorageDir, Vendor, Herstellerverzeichnis, 7x, ez-systems, local, System-Repository, .cache, package.php, index.xml, Offline-Installation, dryrun, packageRepositories, package repository, vendor directory, system repository, offline install
type: help
---
# Das lokale Paket-Repository

Diese Seite ist für Administratoren und Hosting-Teams, die in `var/storage/packages/` hineinsehen, Pakete zwischen Servern kopieren oder
eine Installation ohne Netzwerkzugang vorbereiten. Sie beantwortet: wo Pakete aufbewahrt werden, wie die Verzeichnisse benannt sind, wofür
jedes da ist und was Sie anfassen dürfen.

## Inhalt

1. [Wo es liegt](#wo-es-liegt)
2. [Der Aufbau](#der-aufbau)
3. [Wie ein Herstellerverzeichnis gewählt wird](#wie-ein-herstellerverzeichnis-gewählt-wird)
4. [Das lokale Repository und das System-Repository](#das-lokale-repository-und-das-system-repository)
5. [Das Verzeichnis .cache](#das-verzeichnis-cache)
6. [Eine Offline-index.xml](#eine-offline-indexxml)
7. [Was Sie von Hand tun dürfen](#was-sie-von-hand-tun-dürfen)
8. [Verwandte Seiten](#verwandte-seiten)

## Wo es liegt

`eZPackage::repositoryPath()` baut den Pfad aus drei Teilen:

```text
var / site.ini [FileSettings] StorageDir / package.ini [RepositorySettings] RepositoryDirectory
var / storage                            / packages
```

Der erste Teil ist wörtlich `var`; das Repository wandert nicht mit `[FileSettings] VarDir`. In der Auslieferung (`StorageDir=storage`,
`RepositoryDirectory=packages`) ist es `var/storage/packages/` unterhalb des Wurzelverzeichnisses der Installation, für jeden Siteaccess.
Eine neue Installation hat das Verzeichnis vielleicht noch nicht; der Kernel listet dann außer `local` kein Repository auf.

## Der Aufbau

```text
var/storage/packages/
├── 7x/                              vendor "7x": the setup wizard's system repository
│   ├── index.xml                    optional: the offline index (see below)
│   ├── sevenx_multisite/
│   │   ├── package.xml
│   │   ├── .cache/package.php       the kernel's parsed copy, not part of the package
│   │   ├── documents/  files/  settings/
│   ├── sevenx_classes/
│   └── sevenx_multisite_democontent/
├── ez-systems/                      vendor "eZ systems"
│   └── ezstarrating_extension/
├── local/                           packages without a vendor, and new packages from the wizards
└── dryrun/                          only during a kickstarter dry run, removed afterwards
```

Jedes Verzeichnis direkt unter `var/storage/packages/` ist ein **Repository**; jedes Verzeichnis in einem Repository, das eine
`package.xml` oder `package.json` enthält, ist ein **Paket** (`eZPackage::packageRepositories()`, `fetchPackages()`). Sein Verzeichnisname
ist der `<name>` des Pakets. Auf einer frischen Installation von `sevenx_multisite` enthält das Repository `7x` die Pakete
`sevenx_multisite`, `sevenx_classes` und `sevenx_multisite_democontent`.

## Wie ein Herstellerverzeichnis gewählt wird

| Paket | Repository |
|---|---|
| Importiert, mit einem `<vendor>` | der Hersteller, durch die Transformation `urlalias` geleitet: `7x` bleibt `7x`, `eZ systems` wird zu `ez-systems` |
| Importiert, ohne `<vendor>` | `local` |
| Importiert mit ausdrücklichem Repository (`ezpm import` mit `-r`, ein Probelauf des Kickstarters) | dieses Repository |
| Von einem Assistenten oder `ezpm create` erstellt | `local` (`eZPackage::create()`: Neue Pakete nutzen immer das lokale Repository) |

Zwei Pakete gleichen Namens in zwei Repositorys können existieren; `eZPackage::fetch( $name )` ohne Repository gibt das erste zurück, das
es findet, zuerst `local`. Halten Sie Namen über alle Repositorys hinweg eindeutig.

## Das lokale Repository und das System-Repository

| Name | Was es ist | Typ im Code |
|---|---|---|
| `local` | Immer das erste Repository, bei Bedarf angelegt; neue Pakete gehen hierher | `local` |
| jedes andere Verzeichnis | Ein Repository pro Hersteller | `global` |
| das System-Repository | Das von `[RepositorySettings] Vendor` (`7x`) genannte Repository: wo der Setup-Assistent nach einer Offline-`index.xml` sucht | `eZPackage::systemRepositoryInformation()` |

Die Paketliste der Administration zeigt die Repositorys und lässt Sie zwischen ihnen wechseln; siehe
[Die Paketliste und die Paketseite](../admin-oberflaeche/die-paketliste-und-die-paketseite.md).

## Das Verzeichnis .cache

Wenn der Kernel ein Paket liest, schreibt er die gelesene Definition nach `<package>/.cache/package.php` und nutzt diese Kopie, solange sie
nicht älter als `package.xml` ist und von derselben Codeversion geschrieben wurde (`CACHE_CODE_DATE`). Sie ist ein Cache: Sie zu löschen
schadet nicht, und sie wird nie exportiert oder archiviert (`exportToArchive()` kopiert nur `documents`, `files`, `simplefiles`, `settings`
und die Verzeichnisse der Elemente). Wenn Sie eine `package.xml` an Ort und Stelle bearbeiten, lässt ihr neuerer Zeitstempel den Kernel sie
erneut lesen.

## Eine Offline-index.xml

Wenn der Setup-Assistent oder der Kickstarter `<RemotePackagesIndexURL>/index.xml` nicht herunterladen kann, kopiert er
`var/storage/packages/<Vendor>/index.xml` (mit `Vendor=7x`: `var/storage/packages/7x/index.xml`) und nutzt sie stattdessen
(`eZStepSiteTypes::retrieveRemotePackagesList()`). Zusammen mit bereits importierten Paketen ermöglicht das eine Installation ohne
Netzwerkzugang:

```bash
# on a machine with access, from the installation root
curl -s -o var/storage/packages/7x/index.xml \
  https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml
php ezpm.php import sevenx_multisite.ezpkg
php ezpm.php import sevenx_classes.ezpkg
php ezpm.php import sevenx_multisite_democontent.ezpkg
```

Die Attribute `url` in diesem Index zeigen weiterhin auf den Server, sodass jedes Paket, das ein Lauf braucht, bereits importiert sein muss.
Das vollständige Verfahren, einschließlich Spiegeln, steht in [Offline-Installationen und Spiegel](../paketserver/offline-installationen-und-spiegel.md).

## Was Sie von Hand tun dürfen

| Aktion | Gefahrlos? | Hinweise |
|---|---|---|
| Ein Paketverzeichnis in ein Repository kopieren | ja | Wie ein Import, ohne die Prüfungen des Archivs; halten Sie den Verzeichnisnamen gleich `<name>` |
| Ein Verzeichnis `.cache/` löschen | ja | Es wird neu geschrieben |
| Ein Paketverzeichnis löschen | ja, für das Repository | Ziehen Sie **Entfernen** unter **Setup > Pakete** oder `ezpm delete` vor: Sie löschen auch den Vermerk der Installation. Was eine Installation auf die Website gebracht hat, bleibt in beiden Fällen |
| `package.xml` an Ort und Stelle bearbeiten | mit Vorsicht | Eine kaputte Definition verbirgt das Paket. Nehmen Sie dauerhafte Änderungen im Quell-Repository des Pakets vor |
| Ein Paket in ein Verzeichnis legen, das kein Repository ist | nein | Es wird nicht gefunden |
| Symbolische Links in einem Paket anlegen | nein | Die Bestätigung beim Entfernen weist ein Paket ab, dessen Verzeichnis einen Link enthält |

> **Tipp**
> `var/storage/packages/` gehört in Ihre Backups, wenn Sie offline installieren oder dort eigene Pakete aufbewahren. Die Pakete des
> Paketservers lassen sich jederzeit erneut herunterladen.

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Anleitung zu Paketen, 1. Wo Pakete liegen](../../../anleitungen/pakete.md#1-wo-pakete-liegen)
- [Importieren oder installieren](importieren-oder-installieren.md)
- [Wie der Kickstarter und die Konsole Pakete nutzen](wie-der-kickstarter-und-die-konsole-pakete-nutzen.md)
- [Einstellungen des Paketservers](../paketserver/einstellungen-des-paketservers.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
