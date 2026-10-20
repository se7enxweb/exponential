---
title: Einstellungen des Paketservers
audience: admin, developer, hosting
category: install-upgrade
order: 39
summary: Jeder Schlüssel von settings/package.ini [RepositorySettings] mit seinem Standard und dem, was ihn liest - die Adresse des Index, ihr Rückfall, das Hersteller-Repository und die Versuche, Wartezeiten und Timeouts beim Herunterladen -, dazu die Einstellungen für Proxy und Speicher, die dazugehören, und wie Sie sie überschreiben.
keywords: package.ini, RepositorySettings, RemotePackagesIndexURL, RemotePackagesIndexURLBase, Vendor, RepositoryDirectory, DownloadAttempts, DownloadRetryDelays, DownloadTimeout, DownloadConnectTimeout, ProxySettings, StorageDir, Überschreibung, Einstellungen, override, settings
type: help
---
# Einstellungen des Paketservers

Diese Seite ist für Administratoren und Entwickler, die eine Installation auf einen Paketserver richten oder abstimmen, wie sie
herunterlädt. Sie führt jeden Schlüssel von `settings/package.ini [RepositorySettings]` in 6.0.15 mit seinem Standard auf, sagt, welcher Code
ihn liest, und ergänzt die zwei Einstellungen aus `site.ini`, die dazugehören.

## Inhalt

1. [Die Schlüssel auf einen Blick](#die-schlüssel-auf-einen-blick)
2. [RemotePackagesIndexURL](#remotepackagesindexurl)
3. [RemotePackagesIndexURLBase](#remotepackagesindexurlbase)
4. [Weitere Paketserver](#weitere-paketserver)
5. [Vendor und RepositoryDirectory](#vendor-und-repositorydirectory)
6. [Die Einstellungen für das Herunterladen](#die-einstellungen-für-das-herunterladen)
7. [Einstellungen in site.ini, die dazugehören](#einstellungen-in-siteini-die-dazugehören)
8. [Die Einstellungen überschreiben](#die-einstellungen-überschreiben)
9. [Verwandte Seiten](#verwandte-seiten)

## Die Schlüssel auf einen Blick

```ini
# settings/package.ini (6.0.15)
[RepositorySettings]
RepositoryDirectory=packages
RemotePackagesIndexURL=https://exponential.packages.exponential.earth/exponential/6.0/6.0.15
RemotePackagesIndexURLBase=https://exponential.packages.exponential.earth/exponential
AdditionalRemotePackagesIndexURLs[]
RemotePackagesIndexSources[]
RemotePackagesIndexSources[]=ezupdate.ini;PackageServerSettings;Servers
Vendor=7x
DownloadAttempts=3
DownloadRetryDelays[]
DownloadRetryDelays[]=2
DownloadRetryDelays[]=5
DownloadRetryDelays[]=10
DownloadTimeout=300
DownloadConnectTimeout=30
```

| Schlüssel | Standard | Gelesen von | Bedeutung |
|---|---|---|---|
| `RemotePackagesIndexURL` | der Ordner 6.0.15 | Setup-Assistent, Kickstarter, `exp:install`, ezupdate, `exp:ezwebininstall` | der Release-Ordner, dessen `index.xml` gelesen wird |
| `RemotePackagesIndexURLBase` | `.../exponential` | denselben, nur wenn `RemotePackagesIndexURL` leer ist; der Link des Assistenten unter **Weitere Pakete** | die oberste Ebene des Servers |
| `AdditionalRemotePackagesIndexURLs[]` | leer | Setup-Assistent, Kickstarter, `exp:install` (ab 6.0.15) | weitere Server, nach dem Hauptserver gelesen |
| `RemotePackagesIndexSources[]` | die Liste von ezupdate | denselben | Listen weiterer Server in anderen Einstellungsdateien |
| `Vendor` | `7x` | `eZPackage::systemRepositoryInformation()` | der Repository-Ordner der Pakete des Installers |
| `RepositoryDirectory` | `packages` | `eZPackage::repositoryPath()` | der Ordner aller Paket-Repositorys unter dem Speicherverzeichnis |
| `DownloadAttempts` | `3` | `eZStepSiteTypes::downloadFile()` | wie oft ein Download versucht wird |
| `DownloadRetryDelays[]` | `2`, `5`, `10` | derselben | Sekunden, die vor jedem weiteren Versuch gewartet werden |
| `DownloadTimeout` | `300` | derselben | Sekunden, die ein ganzer Download dauern darf |
| `DownloadConnectTimeout` | `30` | derselben (nur curl) | Sekunden, die auf die Verbindung gewartet wird |

## RemotePackagesIndexURL

Die Adresse eines **Release-Ordners**, ohne `index.xml`. `eZStepSiteTypes` hängt `/index.xml` an (oder `index.xml`, wenn der Wert auf `/`
endet). Die Adressen der Pakete selbst kommen aus den Attributen `url` in diesem Index, sodass sie auf einem anderen Host liegen dürfen.

Jedes Release von Exponential ändert diesen Wert in seiner `package.ini` auf seinen eigenen Ordner; die Geschichte des Schlüssels steht in
[Geschichte des Paketservers](geschichte-des-paketservers.md). Die URL darf für den Setup-Assistenten `https://` oder `http://` nutzen;
ezupdate akzeptiert nur `https://`.

Derselbe Wert wird, nur lesbar, als erster Server auf der Seite **Package servers** von ezupdate gezeigt und ist der Standard von
`exp:ezwebininstall`, wenn dessen Option `--url` nicht angegeben ist (`kernel/private/classes/commands/ezwebininstall.php`).

## RemotePackagesIndexURLBase

Genutzt, wenn `RemotePackagesIndexURL` **leer** ist. Der Konstruktor von `eZStepSiteTypes` baut dann:

```text
<RemotePackagesIndexURLBase>/<major>.<minor>/<version>/index.xml
```

mit `<major>.<minor>/<version>` aus `ExponentialSDK::packageServerPath()`: `6.0/6.0.15` für dieses Release, mit angehängtem Release-Zustand
nur für ein Release, das nicht stabil ist (`6.0/6.0.16alpha1`).

> **Hinweis**
> In 6.0.15 behoben. Vorher rief der Rückfall `ExponentialSDK::version()` mit seinen Standards auf, die den Release-Zustand anhängen: Das
> Ergebnis war `6.0/6.0.15stable/`, ein Ordner, den der Server nicht hat (er antwortete mit `404`). Ab 6.0.15 baut der Setup-Assistent
> `6.0/6.0.15/`, den Ordner, den der Paketserver hat, und ebenso ezupdate (`eZUpdatePackageServers::builtinIndexURL()`) ab seinem Release,
> das mit 6.0.15 ausgeliefert wird. `RemotePackagesIndexURL` auf den gewünschten Release-Ordner zu setzen, bleibt die klarste Wahl.

Der Schritt **Site Paket** verlinkt außerdem das vollständige Archiv aller Pakete mit diesem Wert.

## Weitere Paketserver

Ab 6.0.15 lesen der Setup-Assistent, der Kickstarter und `exp:install` (alle drei lesen den Index über
`eZStepSiteTypes::retrieveRemotePackagesList()`) mehr als einen Server:

```ini
[RepositorySettings]
# folders with an index.xml, read after RemotePackagesIndexURL
AdditionalRemotePackagesIndexURLs[]
AdditionalRemotePackagesIndexURLs[]=https://packages.example.com/exponential/6.0/6.0.15
# lists of servers kept in other settings files: <file>.ini;<block>;<setting>, an array of <name>=<URL>
RemotePackagesIndexSources[]
RemotePackagesIndexSources[]=ezupdate.ini;PackageServerSettings;Servers
```

- Der Hauptserver (`RemotePackagesIndexURL` oder seine Offline-Kopie) kommt zuerst. Ein Paket, das ein früherer Index aufführt, wird
  behalten, wie es ist; ein weiterer Server fügt nur die Pakete hinzu, die die früheren nicht haben.
- Die Standardquelle ist die Liste der Seite **Package servers** von ezupdate
  ([Paketserver in ezupdate](../admin-oberflaeche/paketserver-in-ezupdate.md)), geführt in `settings/override/ezupdate.ini.append.php`. Leeren
  Sie `RemotePackagesIndexSources[]` in einer Überschreibung, um nur die in `package.ini` genannten Server zu lesen.
- Nur Adressen `http://` und `https://` werden gelesen, jede einmal. Ein weiterer Server, der sich nicht lesen lässt, wird mit einer Warnung
  in der Debug-Ausgabe übersprungen; der Schritt zeigt einen Fehler nur für den Hauptserver.
- Eine Anforderung wird von dem Server heruntergeladen, der sie aufführt, sodass ein Seitenpaket auf einem Server Pakete eines anderen
  verlangen darf.

## Vendor und RepositoryDirectory

Pakete werden in `var/<StorageDir>/<RepositoryDirectory>/<vendor>/<name>/` gespeichert, was mit den Standards
`var/storage/packages/7x/<name>/` ist. Ein importiertes Paket geht in den Ordner, den sein eigener `<vendor>` nennt; auf der Installation
erstellte Pakete gehen nach `local/`. `Vendor` nennt das **System-Repository**, in dem der Setup-Assistent nach einer Offline-`index.xml`
sucht (`var/storage/packages/7x/index.xml`). Siehe [Das lokale Paket-Repository](../grundlagen/das-lokale-paket-repository.md) und
[Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md).

## Die Einstellungen für das Herunterladen

In 6.0.15 hinzugefügt ([Changelog](../../../../changelogs/6.0/6.0.15.md), Eintrag vom 6. Oktober 2026 zum Paketdownload des Setups). Sie
gelten für den Index und für jedes Paket, das das Setup herunterlädt.

- **`DownloadAttempts`**: mindestens 1. Ein Versuch scheitert bei einem Übertragungsfehler, einem HTTP-Status außer 200, einer leeren oder
  abgeschnittenen Datei oder einer `.ezpkg`, die keine gzip-Daten ist; ein gescheiterter Versuch hinterlässt keine Datei.
- **`DownloadRetryDelays[]`**: Die Wartezeit vor Versuch 2 ist der erste Wert, vor Versuch 3 der zweite und so weiter; der letzte Wert gilt
  für alle späteren Versuche. Mit den Standards (3 Versuche) sind die Wartezeiten 2 und 5 Sekunden; die 10 wird genutzt, wenn Sie
  `DownloadAttempts` erhöhen.
- **`DownloadTimeout`**: die Grenze für die ganze Übertragung, mit curl und ohne.
- **`DownloadConnectTimeout`**: die Grenze für das Öffnen der Verbindung, mit curl.

Ein leerer Wert bedeutet den eingebauten Standard (dieselben Zahlen). Fehlschläge, die durch Warten nicht besser werden, etwa ein
Cache-Ordner, der sich nicht beschreiben lässt, werden nicht wiederholt. Was Sie sehen, wenn alle Versuche scheitern:
[Wenn der Paketserver nicht erreichbar ist](wenn-der-paketserver-nicht-erreichbar-ist.md).

## Einstellungen in site.ini, die dazugehören

```ini
[ProxySettings]
ProxyServer=
User=
Password=

[FileSettings]
StorageDir=storage
```

`ProxyServer` (mit `User` und `Password`, wenn der Proxy sie braucht) wird für die Downloads des Setups über curl genutzt und von ezupdate.
`StorageDir` ist Teil des Repository-Pfads oben.

## Die Einstellungen überschreiben

Setzen Sie Änderungen in `settings/override/package.ini.append.php`, nie in `settings/package.ini` selbst, das ein Update des Kernels
ersetzt:

```ini
<?php /* #?ini charset="utf-8"?

[RepositorySettings]
RemotePackagesIndexURL=https://packages.example.com/6.0/6.0.15
DownloadAttempts=5

*/ ?>
```

Der Setup-Assistent läuft, bevor irgendein Siteaccess existiert, sodass eine Überschreibung in einem Siteaccess auf ihn keine Wirkung hat;
nutzen Sie `settings/override/`. Leeren Sie nach der Änderung den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`). Wie Sie eine Website
auf Ihren eigenen Server richten, einschließlich der Liste von ezupdate und `kickstart.ini`, steht in
[Eine Website auf Ihren Paketserver richten](../eigene/eine-website-auf-ihren-paketserver-richten.md).

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Das Format index.xml](das-format-index-xml.md)
- [Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md)
- [Paketserver in ezupdate](../admin-oberflaeche/paketserver-in-ezupdate.md)
- [Eine Installationsmethode wählen, 4.3 Seitenpakete](../../../../en/us/install/04-choosing-an-install-method.md#43-site-packages-what-is-installed)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
