---
title: Was auf dem Paketserver liegt
audience: admin, developer, hosting
category: install-upgrade
order: 33
summary: Der Inhalt des Paketservers Ordner für Ordner, die Release-Ordner 6.0.0 bis 6.0.15 mit ihrer Anzahl an Paketen und ihren Größen und jedes der 44 Pakete von 6.0.15 mit Typ, Größe und Version im Index.
keywords: Paketserver, Release-Ordner, 6.0.15, index.xml, ezpkg, thumbnails, sevenx_multisite, cjw_multisite, sevenx_site, ezwebin_site, ezflow_site, ezdemo_site, plain_site, Pakettyp, Größe, package server, release folder, package type, size
type: help
---
# Was auf dem Paketserver liegt

Diese Seite ist für Administratoren und Hoster, die genau wissen wollen, was eine Installation herunterladen kann. Sie führt die Ordner des
Paketservers auf, die Release-Ordner und jedes Paket des Ordners 6.0.15. Die Zahlen stammen aus dem Repository des Servers (`git ls-tree`
seines Branches `master`, geprüft am 18.10.2026) und aus dem laufenden
[Index von 6.0.15](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml).

## Inhalt

1. [Die oberste Ebene](#die-oberste-ebene)
2. [Die Release-Ordner](#die-release-ordner)
3. [In einem Release-Ordner](#in-einem-release-ordner)
4. [Die Seitenpakete von 6.0.15](#die-seitenpakete-von-6015)
5. [Jedes Paket von 6.0.15](#jedes-paket-von-6015)
6. [Die Vorschaubilder](#die-vorschaubilder)
7. [Die Version im Index und die Version des Pakets](#die-version-im-index-und-die-version-des-pakets)
8. [Verwandte Seiten](#verwandte-seiten)

## Die oberste Ebene

| Pfad | Was es ist |
|---|---|
| `exponential/6.0/` | ein Ordner pro Release von Exponential 6.0 |
| `README.md` | eine kurze Beschreibung des Servers, mit Links zum Projekt, zur Dokumentation und zur Community |
| `CNAME` | der Hostname, unter dem GitHub Pages die Website ausliefert, `exponential.packages.exponential.earth` |
| `crossdomain.xml` | eine Datei mit Cross-Domain-Richtlinie, die Zugriff von jeder Domain erlaubt |
| `.github/` | `FUNDING.yml` und der Workflow für Pages (nicht als Seiten ausgeliefert; siehe [Der Paketserver auf GitHub](der-paketserver-auf-github.md)) |

Die Ordner der Releases 3.x bis 5.x liegen nicht mehr hier; sie zogen 2025 in ein eigenes Repository um
([Geschichte des Paketservers](geschichte-des-paketservers.md#2025-speichergrenzen-und-der-name-exponential)).

## Die Release-Ordner

| Ordner | Pakete | Seitenpakete | Größe | Zuletzt geändert |
|---|---|---|---|---|
| `6.0.0` bis `6.0.3` | je 26 | 9 | je 21,4 MiB | 13.08.2025 |
| `6.0.4` bis `6.0.9` | je 38 | 9 | je 26,4 MiB | 13.08.2025 |
| `6.0.10` | 38 | 9 | 26,4 MiB | 16.08.2025 |
| `6.0.14` | 38 | 9 | 26,3 MiB | 05.06.2026 |
| `6.0.15` | 44 | 12 | 86,8 MiB | 16.10.2026 |

Die neun Seitenpakete von 6.0.0 bis 6.0.14 sind `sevenx_site`, `sevenx_site_clean`, `ezflow_site`, `ezflow_site_clean`, `ezwebin_site`,
`ezwebin_site_clean`, `ezdemo_site`, `ezdemo_site_clean` und `plain_site`. 6.0.15 fügt `sevenx_multisite`, `sevenx_multisite_clean` und
`cjw_multisite` hinzu. Es gibt keine Ordner für 6.0.11 bis 6.0.13. Die Größen schließen die Vorschaubilder ein.

> **Hinweis**
> Eine Installation liest nur den Ordner, den ihre `RemotePackagesIndexURL` nennt. Eine Installation von 6.0.15 sieht die Pakete von 6.0.10
> nie und umgekehrt ([Einstellungen des Paketservers](einstellungen-des-paketservers.md)).

## In einem Release-Ordner

```text
exponential/6.0/6.0.15/
  index.xml                   the list of the packages of this release
  <name>.ezpkg                one archive per package (44 in 6.0.15)
  thumbnails/<name>.png       the picture the setup wizard shows next to a site package
```

Die Dateien `.ezpkg` sind mit gzip komprimierte tar-Archive mit einer `package.xml` oben ([Die Datei .ezpkg](../grundlagen/die-datei-ezpkg.md)).
`index.xml` ist in [Das Format index.xml](das-format-index-xml.md) beschrieben.

## Die Seitenpakete von 6.0.15

Das sind die zwölf Einträge mit `type="site"`, in der Reihenfolge des Index, mit der Zusammenfassung, die der Setup-Assistent zeigt:

| Seitenpaket | Zusammenfassung im Index | Version im Index | Größe |
|---|---|---|---|
| `sevenx_multisite` | `7x MultiSite Default Installation` | 6.0-15-stable | 88,3 KiB |
| `sevenx_multisite_clean` | `7x MultiSite Default Installation (without demo content)` | 6.0-15-stable | 88,3 KiB |
| `cjw_multisite` | `CJW Multisite` | 6.0-15-stable | 95,3 KiB |
| `sevenx_site` | `7x Simple Website Interface` | 6.0-15-stable | 77,4 KiB |
| `sevenx_site_clean` | `7x Simple Website Interface (without demo content)` | 6.0-15-stable | 67,5 KiB |
| `ezflow_site` | `eZ Flow` | 5.1-0 | 45,2 KiB |
| `ezflow_site_clean` | `eZ Flow (without demo content)` | 5.1-0 | 31,5 KiB |
| `ezwebin_site` | `Website Interface` | 1.10-0 | 51,1 KiB |
| `ezwebin_site_clean` | `Website Interface (without demo content)` | 1.10-0 | 40,2 KiB |
| `ezdemo_site` | `Exponential Demo Site` | 6.0.0 | 31,3 KiB |
| `ezdemo_site_clean` | `Exponential Demo Site (without demo content)` | 6.0.0 | 26,1 KiB |
| `plain_site` | `Plain site` | 1.1-2 | 28,7 KiB |

Ein Seitenpaket ist klein: Es bringt Einstellungen, Installationsskripte und ein Vorschaubild mit. Die Inhalte und Klassen stehen in den
Paketen, die es verlangt und die der Installer als Nächstes herunterlädt. Was jedes Seitenpaket installiert, erklären
[sevenx_multisite](../sevenx-multisite/README.md) und [cjw_multisite](../cjw-multisite/README.md).

## Jedes Paket von 6.0.15

Alle 44 Pakete, nach Familie gruppiert. „Typ“ ist das Attribut `type` des Index; die Typen sind in [Pakettypen](../grundlagen/pakettypen.md)
erklärt.

| Paket | Typ | Größe |
|---|---|---|
| `sevenx_multisite`, `sevenx_multisite_clean` | site | 88,3 KiB, 88,3 KiB |
| `sevenx_multisite_democontent` | contentobject | 59,4 MiB |
| `sevenx_multisite_democontent_clean` | contentobject | 213,2 KiB |
| `sevenx_site`, `sevenx_site_clean` | site | 77,4 KiB, 67,5 KiB |
| `sevenx_classes` | contentclass | 10,2 KiB |
| `sevenx_democontent` | contentobject | 6,8 MiB |
| `sevenx_democontent_clean` | contentobject | 5,0 KiB |
| `sevenx_banners` | contentobject | 315,0 KiB |
| `sevenx_design_blue`, `sevenx_design_cleangray`, `sevenx_design_gray` | sitestyle | 104,6 KiB, 23,6 KiB, 20,7 KiB |
| `sevenx_extension` | extension | 4,2 MiB |
| `cjw_multisite` | site | 95,3 KiB |
| `cjw_multisite_democontent` | contentobject | 166,0 KiB |
| `ezwebin_site`, `ezwebin_site_clean` | site | 51,1 KiB, 40,2 KiB |
| `ezwebin_classes` | contentclass | 8,3 KiB |
| `ezwebin_democontent` | contentobject | 1,9 MiB |
| `ezwebin_democontent_clean` | contentobject | 5,2 KiB |
| `ezwebin_banners` | contentobject | 314,9 KiB |
| `ezwebin_design_blue`, `ezwebin_design_cleangray`, `ezwebin_design_gray` | sitestyle | 104,2 KiB, 23,0 KiB, 20,7 KiB |
| `ezwebin_extension` | extension | 654,5 KiB |
| `ezflow_site`, `ezflow_site_clean` | site | 45,2 KiB, 31,5 KiB |
| `ezflow_classes` | contentclass | 8,9 KiB |
| `ezflow_democontent` | contentobject | 2,5 MiB |
| `ezflow_democontent_clean` | contentobject | 2,2 KiB |
| `ezflow_design` | sitestyle | 20,7 KiB |
| `ezflow_extension` | extension | 766,9 KiB |
| `ezdemo_site`, `ezdemo_site_clean` | site | 31,3 KiB, 26,1 KiB |
| `ezdemo_classes` | contentclass | 8,4 KiB |
| `ezdemo_democontent` | contentobject | 7,4 MiB |
| `ezdemo_democontent_clean` | contentobject | 3,0 KiB |
| `ezdemo_extension` | extension | 551,1 KiB |
| `plain_site` | site | 28,7 KiB |
| `ezpaypal_extension` | extension | 15,4 KiB |
| `ezstarrating_extension` | extension | 36,5 KiB |
| `ezgmaplocation_extension` | extension | 25,5 KiB |
| `ezwt_extension` | extension | 81,0 KiB |

Nach Typ: 12 Pakete `site`, 13 `contentobject`, 4 `contentclass`, 7 `sitestyle` und 8 `extension`. Die mit Abstand größte Datei ist
`sevenx_multisite_democontent` (62.314.497 Bytes), die Demo-Inhalte des Standard-Seitenpakets mit ihren Bildern; die Installation von
`sevenx_multisite_clean` lädt stattdessen sein Gegenstück `_clean` mit 213 KiB herunter.

## Die Vorschaubilder

`thumbnails/` enthält 13 PNG-Dateien: eine für jedes der zwölf Seitenpakete und `sevenx_site_paypal.png`, das kein Eintrag des Index nutzt.
`sevenx_multisite.png` und `sevenx_multisite_clean.png` sind dasselbe Bild. Der Index zeigt mit `thumbnail_url` auf sie; der
Setup-Assistent zeigt sie neben jedem Seitenpaket, das noch nicht importiert ist.

## Die Version im Index und die Version des Pakets

Das Attribut `version` in `index.xml` wiederholt die Version der eigenen `package.xml` des Pakets, so geschrieben, wie
`eZPackage::getVersion()` sie schreibt: `<number>-<release>`. `sevenx_multisite` hat
`<version><number>6.0</number><release>15-stable</release></version>` und wird als `6.0-15-stable` aufgeführt. Der Setup-Assistent
vergleicht die Version im Index mit einer importierten Kopie, um zu entscheiden, welchen Eintrag er zeigt: Eine gleiche Version behält die
importierte Kopie, eine höhere Version im Index bietet den Download an. Der Kickstarter zieht immer den Eintrag des Index vor
([Die Kommandozeile des Kickstarters](../../../bc/6.0/kickstartercli.md#warum-verwendet-der-entfernte-paketindex-eine-andere-version)). Siehe
[Versionsnummern](../anforderungen/versionsnummern.md).

> **Hinweis**
> Am 19.10.2026 behoben: Bis dahin führte der Index die vier Seitenpakete von 7x als `1.0.0-0` und `cjw_multisite` als `6.0.15-0`, was
> beides nicht die Version in ihrer `package.xml` war. Die alten Seitenpakete (`ezflow_site`, `ezwebin_site`, `ezdemo_site`, `plain_site`)
> behalten die Versionen, mit denen sie veröffentlicht wurden.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Das Format index.xml](das-format-index-xml.md)
- [Den Paketserver im Browser durchsuchen](den-paketserver-im-browser-durchsuchen.md)
- [Die Quell-Repositorys](die-quell-repositorys.md)
- [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
