---
title: Geschichte des Paketservers
audience: admin, developer, hosting, partner
category: install-upgrade
order: 32
summary: Wie der Paketserver entstand, erzählt aus der Git-Geschichte des Repositorys des Paketservers, der zwei wichtigsten Quell-Repositorys und der package.ini des Kernels - der Spiegel von Brookins Consulting 2014, die Release-Ordner von 6.0 ab Dezember 2023, der Wechsel zum Namen Exponential 2025 und die Seitenpakete von 6.0.15 im Jahr 2026.
keywords: Geschichte, Paketserver, Brookins Consulting, 7x, Spiegel, Pakete, 6.0, 5.4, Umbenennung, exponential.packages.exponential.earth, sevenx-ezpackage, cjw_multisite-ezpackage, GitHub Pages, history, package server, mirror, packages, rebranding
type: help
---
# Geschichte des Paketservers

Diese Seite ist für Leser, die wissen wollen, woher der Paketserver und seine Pakete kommen. Jedes Datum und jeder Betreff eines Commits
unten stammt aus der Git-Geschichte dreier Repositorys, die Sie selbst prüfen können:
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth) (der Server, 341
Commits), [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage) (die Pakete von 7x, 1226 Commits) und
[se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) (16 Commits), dazu aus der Geschichte von
`settings/package.ini` im Kernel und den Changelogs in `doc/changelogs/6.0/`. Die Betreffzeilen der Commits sind hier ins Deutsche
übertragen.

Der Hersteller, der all das pflegt, 7x, hieß früher **Brookins Consulting**; siehe [Wer ist 7x?](../../wer-ist-7x.md). Exponential ist aus
einem Content-Management-System hervorgegangen, das sein Hersteller nach fast 18 Jahren eingestellt hat.

## Inhalt

1. [Zeitleiste](#zeitleiste)
2. [Vor 2014: der Paketserver des ursprünglichen Herstellers](#vor-2014-der-paketserver-des-ursprünglichen-herstellers)
3. [2014 bis 2019: der Spiegel](#2014-bis-2019-der-spiegel)
4. [Dezember 2023: die Release-Ordner von 6.0](#dezember-2023-die-release-ordner-von-60)
5. [2024: ein Release-Ordner pro Release](#2024-ein-release-ordner-pro-release)
6. [2025: Speichergrenzen und der Name Exponential](#2025-speichergrenzen-und-der-name-exponential)
7. [2026: MongoDB, sevenx_multisite und cjw_multisite](#2026-mongodb-sevenx_multisite-und-cjw_multisite)
8. [Die Quell-Repositorys im Lauf der Zeit](#die-quell-repositorys-im-lauf-der-zeit)
9. [Commits pro Jahr](#commits-pro-jahr)
10. [Verwandte Seiten](#verwandte-seiten)

## Zeitleiste

| Datum | Ereignis | Quelle |
|---|---|---|
| 12.10.2006 | Erster Commit der Paketquellen, aus denen `sevenx-ezpackage` wurde (Projekt ezwcms hinzugefügt) | sevenx-ezpackage |
| 21.10.2014 | Erster Import des Spiegels des Legacy-Paketservers, durch Brookins Consulting | Repository des Servers |
| 15.11.2014, 14.03.2015 | Pakete der Releases 5.4 hinzugefügt | Repository des Servers |
| 24.12.2023 | Inhalte für 6.0 zum Testen des Release hinzugefügt, zunächst ausgehend von bekannten Paketen aus 5.4.7; `CNAME`, `FUNDING.yml` und der Workflow für Pages angelegt | Repository des Servers |
| 24.12.2023 | Die `package.ini` des Kernels nennt den neuen Server und den Hersteller `7x` (`12ce4a8168`) | Kernel, [Changelog 6.0.0](../../../../changelogs/6.0/6.0.0.md) |
| 29.02.2024 bis 03.12.2024 | Release-Ordner 6.0.1 bis 6.0.6 | Repository des Servers |
| 07.01.2025 | Der Server zieht auf eine Markendomain um; die alte Adresse leitet um (`5fb85b97f0`) | Kernel, [Changelog 6.0.7](../../../../changelogs/6.0/6.0.7.md) |
| 31.01.2025 | Die Ordner 3.x bis 5.x werden wegen der Speichergrenzen von GitHub Pages entfernt; 6.0.7 hinzugefügt | Repository des Servers |
| 10.06.2025 | Release-Ordner 6.0.8 und 6.0.9 | Repository des Servers |
| 13.08.2025 | Umbenennung: Der Ordner `ezpublish/` wird zu `exponential/`, der Host wird zu `exponential.packages.exponential.earth`; 6.0.10 hinzugefügt | Repository des Servers, Kernel `5d3f294bee` |
| 05.06.2026 | Release-Ordner 6.0.14, mit Unterstützung für MongoDB; der Kernel wechselt dorthin (`46540dc84e`) | Repository des Servers, Kernel |
| 07.08.2026 | Release-Ordner 6.0.15 mit `sevenx_multisite` und `sevenx_multisite_clean` | Repository des Servers |
| 15.08.2026 | Die `package.ini` des Kernels zeigt auf 6.0.15 (`f9e77f6740`) | Kernel |
| 07.10.2026 | `cjw_multisite` und `cjw_multisite_democontent` hinzugefügt; ihr Quell-Repository angelegt | Repository des Servers, cjw_multisite-ezpackage |
| 12.10.2026 | Ausrollvorgänge auf Pages laufen nacheinander | Repository des Servers |

## Vor 2014: der Paketserver des ursprünglichen Herstellers

Die Einstellung `RemotePackagesIndexURL` ist älter als der eigene Server von Exponential. Die Geschichte von `settings/package.ini` im Kernel
zeigt, dass sie sich ab 2007 mit jedem Release änderte und auf den Paketserver des ursprünglichen Herstellers der Software zeigte, mit einem
Ordner pro Version (der letzte davon `5.4/5.4.0`, Hersteller `eZ-systems`). Die Pakete dieser Zeit (darunter die Seitenpakete `ezwebin`,
`ezflow` und `ezdemo`) sind die Vorfahren mehrerer Pakete, die heute noch auf dem Server liegen.

## 2014 bis 2019: der Spiegel

Am 21.10.2014 importierte Brookins Consulting einen vollständigen Spiegel dieses Servers in Git: Release-Ordner von 3.8 bis 5.4, zusammen
mit `index.xml`-Dateien, Vorschaubildern und einer `README.md`. Zwei weitere Commits im November 2014 und März 2015 fügten die Releases 5.4
hinzu (die neuesten stabilen Releases der Pakete 5.4.x). Die Commits von 2017 und 2019 reparierten nur das JavaScript der Seite zum
Durchsuchen.

Auf GitHub ist das heutige Repository noch als Fork von `ezpublishlegacy/packages.ezpublishlegacy.com` markiert, das sich als Spiegel von
packages.ez.no beschreibt; dieses Repository wurde am 21.10.2014 angelegt, dem Datum des ersten Commits.

## Dezember 2023: die Release-Ordner von 6.0

Am 24.12.2023 begann 7x den eigenen Ordner von Exponential 6.0: erstes Hinzufügen von Paketinhalten für 6.0 zum Testen des Release,
zunächst ausgehend von bekannten Paketen aus 5.4.7. Am selben Tag:

- wurden `CNAME` und `FUNDING.yml` angelegt;
- wurde die `index.xml` von 6.0.0 geändert, um den geänderten Hersteller, die URLs und die Versionsnummern abzubilden;
- wurde die JavaScript-Seite zum Durchsuchen durch einen Workflow von GitHub Actions ersetzt, der Verzeichnisauflistungen erzeugt;
- wurden die Erweiterung und die Seitenpakete `ezdemo` als Releases mit Fehlerkorrekturen neu paketiert.

Eine Woche später wurde der erste Pull Request zusammengeführt (#1, der die gepatchten Pakete ezwebin site und site clean für das Release
6.0 hinzufügte), gefolgt von `plain_site` am 01.01.2024. Im Kernel ersetzte der Commit `12ce4a8168` den Server und den Herstellernamen des
ursprünglichen Herstellers in `package.ini` ([Changelog 6.0.0](../../../../changelogs/6.0/6.0.0.md)), und `9d38333df9` ersetzte Adressen,
die mit PHP 8 nicht funktionierten ([Changelog 6.0.1](../../../../changelogs/6.0/6.0.1.md)).

## 2024: ein Release-Ordner pro Release

Ab 2024 bekam jedes Release einen eigenen Ordner, und die `RemotePackagesIndexURL` des Kernels wanderte mit:

| Ordner | Hinzugefügt | Betreff des Commits (übertragen) |
|---|---|---|
| 6.0.1, 6.0.2 | 29.02.2024 | Release-Branches für die weitere Entwicklung der Pakete hinzugefügt |
| 6.0.3 | 02.04.2024 | Attribut des Datentyps xrowmetadata den meisten seitenbasierten Klassen hinzugefügt |
| 6.0.4 | 23.08.2024 | Import vereinheitlichter Paketabhängigkeiten der Plattform unter einem Kern-Release; außerdem Design admin3 zu den Installern der sevenx-Pakete hinzugefügt |
| 6.0.5 | 01.10.2024 | Release-Pakete für 6.0.5 hinzugefügt |
| 6.0.6 | 03.12.2024 | Installationspakete für das Release 6.0.6 hinzugefügt |

2024 war mit 112 Commits das bis dahin geschäftigste Jahr des Repositorys.

## 2025: Speichergrenzen und der Name Exponential

- **Januar.** Der Server zog auf eine Markendomain um; der Kernel-Commit `5fb85b97f0` vermerkt, dass die alte Adresse transparent
  umleitet. Am 31.01.2025 wurden die Release-Ordner der Zeit von 3.x bis 5.x entfernt: Wegen der Speichergrenzen von GitHub Pages
  konzentriert sich das Repository seitdem auf Pakete, die sich installieren lassen und laufen, also 6.0.x. Sie wurden in einem eigenen
  Repository aufbewahrt,
  [se7enxweb/deprecated.ezpublish.packages.ezpublish.one](https://github.com/se7enxweb/deprecated.ezpublish.packages.ezpublish.one), am
  nächsten Tag angelegt. Am 31.01.2025 selbst überarbeiteten fünfzehn nummerierte Commits (vom ersten bis zum fünfzehnten Versuch, das
  Ausrollen mit einer neuen Version von upload artifact wiederherzustellen) den Workflow für Pages.
- **Juni.** Ordner 6.0.8 und 6.0.9.
- **August.** Am 13.08.2025 benannte ein Commit den obersten Ordner von `ezpublish/` in `exponential/` um, änderte `CNAME` auf
  `exponential.packages.exponential.earth` und aktualisierte die `README.md`, um die URL des Paketservers und den früheren Produktnamen zu
  ersetzen. Der Ordner 6.0.10 folgte am selben Tag (Release 6.0.10 unter dem neuen Namen), und die Einstellungen des Kernels zogen auf die
  neue Adresse um (`5d3f294bee`; siehe [Changelog 6.0.10](../../../../changelogs/6.0/6.0.10.md)).

Es gibt keine Ordner für 6.0.11, 6.0.12 und 6.0.13: Die `package.ini` des Kernels änderte sich zwischen der Adresse für 6.0.10 (August 2025)
und der Adresse für 6.0.14 (Juni 2026) nicht, sodass diese Releases den Ordner 6.0.10 weiter nutzten.

## 2026: MongoDB, sevenx_multisite und cjw_multisite

- **Juni.** 6.0.14, Installationspakete des Release mit Unterstützung für MongoDB; der Kernel machte ihn zum Standard für neue
  Installationen (`46540dc84e`).
- **August.** 6.0.15 kam am 07.08.2026, einschließlich der Pakete sevenx_multisite (und _clean), und der Kernel wechselte am 15.08.2026
  dorthin. Der Ordner ist der bisher größte: 44 Pakete, etwa 87 MiB.
- **Oktober.** Die Pakete von CJW Multisite wurden am 07.10.2026 hinzugefügt. Ab Mitte Oktober wurden die Installer der Seitenpakete viele
  Male an Ort und Stelle aktualisiert, ein Commit pro Paket (zum Beispiel einer, mit dem der Installer des Pakets sevenx_multisite
  ezownerchange aktiviert), und am 12.10.2026 wurde der Workflow so geändert, dass Ausrollvorgänge auf Pages nacheinander laufen. Im Kernel
  wurde der Download im Setup so geändert, dass er es erneut versucht ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md),
  Eintrag vom 6. Oktober 2026 zum Paketdownload des Setups).

## Die Quell-Repositorys im Lauf der Zeit

- **sevenx-ezpackage** trägt die Paketquellen ab 2006: 1226 Commits, die meisten von Entwicklern des ursprünglichen Herstellers zwischen
  2006 und 2014 (es begann als Seitenpaket `ezwebin`, umbenannt am 15.11.2006). Die Arbeit von 7x beginnt am 24.12.2023; am 14.01.2024
  wurden die Basisquellen in `sevenx_site` umgebaut (massiver Umbau der Basisquellen des Seitenpakets ezwebin zu sevenx_site). Die
  Multisite-Pakete sind seit dem 07.09.2026 darin, das Build-Skript `packages/build.sh` seit dem 14.10.2026.
- **cjw_multisite-ezpackage** wurde am 07.10.2026 mit den zwei Paketen von CJW Multisite angelegt, aufgebaut wie das Paket-Repository von 7x,
  mit seinen Skripten, die jedes Paket in sein Archiv `.ezpkg` packen.

Siehe [Die Quell-Repositorys](die-quell-repositorys.md) für alle.

## Commits pro Jahr

| Jahr | Repository des Servers | sevenx-ezpackage |
|---|---|---|
| 2006 bis 2013 | keine | 990 |
| 2014 | 4 | 17 |
| 2015, 2017, 2019 | je 1 | keine |
| 2023 | 29 | 2 |
| 2024 | 112 | 92 |
| 2025 | 38 | 9 |
| 2026 | 155 | 116 |

Von den 341 Commits des Repositorys des Servers wurden 8 unter dem Namen Brookins Consulting und 333 unter 7x gemacht.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Was auf dem Paketserver liegt](was-auf-dem-paketserver-liegt.md)
- [Der Paketserver auf GitHub](der-paketserver-auf-github.md)
- [Wer ist 7x?](../../wer-ist-7x.md)
- [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
