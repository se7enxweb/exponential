---
title: Der Paketserver
audience: admin, developer, hosting
category: install-upgrade
order: 30
summary: Überblick über den Paketserver, der die Seiteninstallationspakete von Exponential veröffentlicht, exponential.packages.exponential.earth, mit einer Karte der Seiten, die erklären, was er ist, was er enthält, wie er gebaut und veröffentlicht wird und was zu tun ist, wenn er nicht erreichbar ist.
keywords: Paketserver, exponential.packages.exponential.earth, index.xml, ezpkg, RemotePackagesIndexURL, GitHub Pages, Spiegel, Offline-Installation, Seitenpakete, Release-Ordner, package server, mirror, offline install, site packages, release folder
type: help
---
# Der Paketserver

Dieser Abschnitt ist für Administratoren, Entwickler und Hosting-Unternehmen, die wissen wollen, woher der Setup-Assistent, der
Kickstarter und `exp:install` ihre Seitenpakete bekommen. Er beantwortet: was der Paketserver ist, was darauf liegt, wie ein Paket dorthin
kommt, welche Einstellungen eine Installation darauf richten und was ohne ihn zu tun ist.

> **Tipp**
> Sie haben ein Seitenpaket gebaut, das andere nutzen könnten? 7x und die Exponential Foundation ermutigen Sie ausdrücklich, es per Pull
> Request an das Repository hinter dem Paketserver einzureichen: [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Kurz gefasst](#kurz-gefasst)
2. [Die Seiten dieses Abschnitts](#die-seiten-dieses-abschnitts)
3. [Welche Seite für welche Frage](#welche-seite-für-welche-frage)
4. [Die Adressen auf einen Blick](#die-adressen-auf-einen-blick)
5. [Verwandte Seiten](#verwandte-seiten)

## Kurz gefasst

- Der **Paketserver** ist eine statische HTTPS-Website:
  [exponential.packages.exponential.earth](https://exponential.packages.exponential.earth/). Nichts darauf führt Code aus; er liefert
  Dateien aus.
- Er hat einen Ordner pro Release von Exponential, `exponential/6.0/<version>/`, jeder mit einer `index.xml`, den Archiven `.ezpkg` und
  einem Ordner `thumbnails/`.
- Eine Installation liest `<RemotePackagesIndexURL>/index.xml`, gesetzt in `settings/package.ini [RepositorySettings]`. Für 6.0.15 ist das
  `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15`.
- Die Website wird von GitHub Pages aus dem Repository
  [se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth) veröffentlicht;
  ein Workflow von GitHub Actions fügt die Seiten der Verzeichnisauflistung hinzu.
- Die Pakete werden aus Quell-Repositorys gebaut, etwa [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage) und
  [se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage).
- Der Ordner 6.0.15 enthält 44 Pakete, zwölf davon Seitenpakete.
- Ohne den Server kann eine Installation eine lokale `index.xml` und bereits importierte Pakete nutzen oder einen eigenen Spiegel.

## Die Seiten dieses Abschnitts

| Seite | Was sie beantwortet |
|---|---|
| [Was der Paketserver ist](was-der-paketserver-ist.md) | Seine Aufgabe, was er nicht ist und wie eine Installation mit ihm spricht |
| [Geschichte des Paketservers](geschichte-des-paketservers.md) | Vom Spiegel 2014 zu den Release-Ordnern von Exponential 6.0, aus der Git-Geschichte |
| [Was auf dem Paketserver liegt](was-auf-dem-paketserver-liegt.md) | Jeder Release-Ordner und jedes Paket von 6.0.15 mit Typ und Größe |
| [Das Format index.xml](das-format-index-xml.md) | Die Elemente und Attribute des Paketindex und welche davon der Code liest |
| [Den Paketserver im Browser durchsuchen](den-paketserver-im-browser-durchsuchen.md) | Die Seiten der Verzeichnisauflistung und was jede Spalte bedeutet |
| [Der Paketserver auf GitHub](der-paketserver-auf-github.md) | Das Repository, der Workflow für Pages, die Action für die Verzeichnisauflistung, `CNAME` und `FUNDING.yml` |
| [Die Quell-Repositorys](die-quell-repositorys.md) | Wo die entpackte Quelle jedes Pakets liegt |
| [Wie ein Release den Paketserver erreicht](wie-ein-release-den-paketserver-erreicht.md) | Von einem Commit in einem Quell-Repository zu einer Datei auf dem Server |
| [Einstellungen des Paketservers](einstellungen-des-paketservers.md) | Jeder Schlüssel von `[RepositorySettings]` mit seinem Standard |
| [Ein Paket von Hand herunterladen](ein-paket-von-hand-herunterladen.md) | `curl`, `tar -tzf`, eine Datei prüfen, sie importieren |
| [Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md) | Installieren ohne Internetzugang und einen Release-Ordner kopieren |
| [Wenn der Paketserver nicht erreichbar ist](wenn-der-paketserver-nicht-erreichbar-ist.md) | Wiederholungen, Fehlermeldungen und was zu tun ist |

## Welche Seite für welche Frage

| Sie wollen ... | Lesen Sie |
|---|---|
| sehen, aus welchen Paketen eine Installation wählen kann | [Was auf dem Paketserver liegt](was-auf-dem-paketserver-liegt.md) |
| die Dateien selbst ansehen | [Den Paketserver im Browser durchsuchen](den-paketserver-im-browser-durchsuchen.md) |
| ein Paket prüfen, bevor Sie ihm vertrauen | [Ein Paket von Hand herunterladen](ein-paket-von-hand-herunterladen.md) |
| auf einem Server ohne Internetzugang installieren | [Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md) |
| einen Fehler im Schritt **Site Paket** verstehen | [Wenn der Paketserver nicht erreichbar ist](wenn-der-paketserver-nicht-erreichbar-ist.md) |
| eine Installation auf einen anderen Server richten | [Einstellungen des Paketservers](einstellungen-des-paketservers.md), dann [Eine Website auf Ihren Paketserver richten](../eigene/eine-website-auf-ihren-paketserver-richten.md) |
| einen eigenen Paketserver betreiben | [Einen eigenen Paketserver betreiben](../eigene/einen-eigenen-paketserver-betreiben.md) |
| Ihr Paket auf den gemeinsamen Server bringen | [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md) |

## Die Adressen auf einen Blick

| Was | Adresse |
|---|---|
| Der Paketserver | <https://exponential.packages.exponential.earth/> |
| Alle Release-Ordner von 6.0 | <https://exponential.packages.exponential.earth/exponential/6.0/> |
| Der Release-Ordner 6.0.15 | <https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/> |
| Der Paketindex von 6.0.15 | <https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml> |
| Das Repository dahinter | <https://github.com/se7enxweb/exponential.packages.exponential.earth> |
| Die Einstellung, die ihn nennt | `settings/package.ini [RepositorySettings] RemotePackagesIndexURL` |
| Wo heruntergeladene Pakete landen | `var/storage/packages/7x/<name>/` |

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Grundlagen der Seitenpakete](../grundlagen/README.md)
- [Ein eigenes Seitenpaket erstellen](../eigene/README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Glossar](../glossar-der-seitenpakete.md)
- [Eine Installationsmethode wählen, 4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
