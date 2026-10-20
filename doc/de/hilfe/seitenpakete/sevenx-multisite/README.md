---
title: sevenx_multisite
audience: admin, developer, hosting, website-creator
category: install-upgrade
order: 60
summary: Ein Überblick über sevenx_multisite, das Standard-Seitenpaket von Exponential 6.0.15 - was es installiert, welche Pakete es mitzieht und wo Sie über jeden Teil lesen.
keywords: sevenx_multisite, Seitenpaket, Multisite, Fit & Healthy, Bold Agency, sevenx_classes, sevenx_multisite_democontent, Standardinstallation, exp:install, Kickstarter, site package, multisite, default install, kickstarter
type: help
---
# sevenx_multisite

Dieser Abschnitt ist für Administratoren, Entwickler und Hoster, die Exponential mit seinem Standard-Seitenpaket installieren oder verstehen
wollen, was dieses Paket tut, bevor sie es ändern oder ersetzen. Er beantwortet: was `sevenx_multisite` ist, woraus es besteht, was sein
Installerskript schreibt und was Sie haben, wenn die Installation fertig ist.

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Die Paketfamilie](#die-paketfamilie)
3. [Was eine Installation Ihnen gibt](#was-eine-installation-ihnen-gibt)
4. [Woher es kommt](#woher-es-kommt)
5. [Seiten in diesem Abschnitt](#seiten-in-diesem-abschnitt)

## In einem Absatz

`sevenx_multisite` ist ein Seitenpaket (`<type value="site" />` in seiner `package.xml`) vom Hersteller `7x`. Seine Zusammenfassung im Paket
ist `MultiSite Default Installation`, und der Paketindex von 6.0.15 führt es als `7x MultiSite Default Installation`. Es ist das Paket, das
`exp:install --package` und `exp:kickstarter ini --yes` installieren, wenn nichts anderes gewählt ist (siehe
[4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)). Es richtet zwei öffentliche Websites
in einer Installation ein, **Fit & Healthy** und **Bold Agency**, plus die Siteaccesses der Administration, der Redaktion und (wenn die
Erweiterung `exp_adminui` vorhanden ist) der Admin UI, mit Seitenlayouts aus den Erweiterungen `explayouts`.

## Die Paketfamilie

| Paket | Typ | Rolle |
|---|---|---|
| `sevenx_multisite` | `site` | das Seitenpaket: Skripte für Einstellungen, der Installer, ein Logo und ein Vorschaubild |
| `sevenx_multisite_clean` | `site` | dasselbe Seitenpaket ohne die Demo-Artikel, Rezepte und Bilder |
| `sevenx_classes` | `contentclass` | 33 allgemeine Inhaltsklassen (Artikel, Ordner, Bild, Produkt ...) |
| `sevenx_multisite_democontent` | `contentobject` | 31 Inhaltsklassen `ng_*` und 291 Inhaltsobjekte: die Demo-Inhalte beider Websites |
| `sevenx_multisite_democontent_clean` | `contentobject` | dieselben 31 Klassen und 35 Inhaltsobjekte: nur die Struktur, die Formulare und die rechtlichen Seiten |

`sevenx_multisite` verlangt `sevenx_classes` und `sevenx_multisite_democontent`; `sevenx_multisite_clean` verlangt `sevenx_classes` und
`sevenx_multisite_democontent_clean`. Beide Anforderungen tragen `min-version="5.1"`.

> **Hinweis**
> Die Layouts der zwei Websites (21 veröffentlichte Layouts mit ihren Zonen, Blöcken und Regeln) stehen in keinem dieser Pakete. Der
> Installer befüllt sie aus den Dateien `share/*.dba` der Erweiterungen `sevenx_themes_media` und `explayouts`. Siehe
> [Layoutdaten und share-DBA-Dateien](layoutdaten-und-share-dba-dateien.md).

## Was eine Installation Ihnen gibt

- Die Siteaccesses `site` (Fit & Healthy), `bold` und `bold_ger` (Bold Agency auf Englisch und Deutsch), `admin`, `editor`, `adminui`, wenn
  verfügbar, und einen zusätzlichen Siteaccess pro weiterer gewählter Sprache.
- 60 Erweiterungen in `ActiveExtensions` (gezählt aus der `extension_list` des Installers) und `expai` und `exp_adminui` als
  Zugriffserweiterungen der Administration, wenn sie vorhanden sind.
- Rollen für die Benutzer Anonymous, Member, Editor und Partner, eine Sektion Restricted und eine Benutzergruppe Partners.
- Einen RSS-Feed, Einstellungen für den statischen Cache und den HTTP-Cache (ausgeschaltet installiert), Suchstatistik auf jedem Siteaccess
  und Einstellungen für Links zwischen den zwei Websites.

Das vollständige Bild steht auf [Was Sie nach der Installation haben](was-sie-nach-der-installation-von-sevenx-multisite-haben.md).

## Woher es kommt

Die Quelle aller fünf Pakete ist der Ordner `packages/` des GitHub-Repositorys
[se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage). Das Installerskript stammt vom Installer des Pakets
`ezwebin_site` ab, der laut Git-Geschichte am 28.03.2007 hinzugefügt wurde; im Januar 2024 zog er nach `sevenx_site` um, und am 07.09.2026
wurde er als `sevenx-multi-site-installer.php` nach `sevenx_multisite` kopiert (`git log --follow` auf diese Datei). Die gebauten Archive
liegen auf dem Paketserver unter `exponential/6.0/6.0.15/`, zum Beispiel
[sevenx_multisite.ezpkg](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_multisite.ezpkg).

> **Tipp**
> Um aus dieser Website eine eigene zu bauen, lesen Sie [Ein eigenes Seitenpaket erstellen](../eigene/README.md). 7x und die Exponential
> Foundation ermutigen Sie, das Ergebnis allen anzubieten: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Seiten in diesem Abschnitt

| Seite | Was sie beantwortet |
|---|---|
| [Woraus es besteht](woraus-sevenx-multisite-besteht.md) | die Dateien des Seitenpakets und der Pakete, die es verlangt |
| [sevenx_classes](sevenx-classes-das-paket-der-inhaltsklassen.md) | die 33 Inhaltsklassen des Klassenpakets |
| [Die Demo-Inhaltspakete](die-demo-inhaltspakete.md) | die 31 Klassen `ng_*`, die Demo-Objekte und die Variante `_clean` |
| [Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md) | was `sevenx-multi-site-installer.php` tut, der Reihe nach |
| [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-sevenx-multisite.md) | jeder Siteaccess, den es schreibt, und wie aus Sprachen Siteaccesses werden |
| [Erweiterungen, die es aktiviert](erweiterungen-die-sevenx-multisite-aktiviert.md) | die Liste der Erweiterungen, ihre Reihenfolge und die Tabellen der Erweiterungen |
| [Rollen und Richtlinien](rollen-und-richtlinien-von-sevenx-multisite.md) | die Rollen, Sektionen und Benutzergruppen, die es anlegt |
| [Layoutdaten und share-DBA-Dateien](layoutdaten-und-share-dba-dateien.md) | wie die Zeilen `explayouts_*` in die Datenbank kommen |
| [Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md) | `post-install-fix.php` und `install-scripts.php` |
| [Was Sie nach der Installation haben](was-sie-nach-der-installation-von-sevenx-multisite-haben.md) | eine Checkliste der fertigen Installation |
| [Die Clean-Variante und sevenx_site](die-clean-variante-und-sevenx-site.md) | `sevenx_multisite_clean`, `sevenx_site` und `sevenx_site_clean` |

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Was ein Seiteninstallationspaket ist](../grundlagen/was-ein-seiteninstallationspaket-ist.md)
- [cjw_multisite](../cjw-multisite/README.md)
- [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
