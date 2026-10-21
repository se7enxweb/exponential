---
title: sevenx_multisite und cjw_multisite im Vergleich
audience: admin, developer, hosting, designer
category: install-upgrade
order: 82
summary: Die zwei Multisite-Pakete von Exponential 6.0.15 nebeneinander. Pakete und Größen, Design und Theme-Erweiterung, Inhalte und Sprachen, woher Layouts und Bilder kommen, Siteaccesses, Schritte des Installers und Quell-Repositorys, mit einem Rat, welches Sie wählen und von welchem Sie für ein eigenes Paket ausgehen.
keywords: sevenx_multisite, cjw_multisite, vergleichen, Vergleich, Design media, Design starter, cjw_themes_jumper, sevenx_themes_media, Fit & Healthy, Bold Agency, CJW JAC Exponential, db_data.dba, cjw-explayouts.json, simplefiles, extension-path, compare, comparison, media design, starter design
type: help
---
# sevenx_multisite und cjw_multisite im Vergleich

Exponential 6.0.15 bietet zwei Multisite-Pakete an: **sevenx_multisite**, den Standard, und **cjw_multisite**, die Startseite von CJW. Der
Installer von CJW wurde aus dem von 7x erzeugt, sodass sie den größten Teil ihres Codes teilen, aber sie unterscheiden sich darin, woher
Design, Inhalte, Bilder und Layouts kommen. Diese Seite stellt sie nebeneinander, für Administratoren und Hoster, die eines wählen, und für
Entwickler, die entscheiden, von welchem sie für ein eigenes Paket ausgehen.

Alle Fakten stammen aus den beiden Quell-Repositorys, [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage)
(`packages/sevenx_multisite`, `packages/sevenx_multisite_democontent`) und
[se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage), und aus der
[index.xml](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml) von 6.0.15.

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Pakete und Größen](#pakete-und-größen)
3. [Design, Inhalte und Daten](#design-inhalte-und-daten)
4. [Siteaccesses und Sprachen](#siteaccesses-und-sprachen)
5. [Die Installer](#die-installer)
6. [Welches Sie wählen](#welches-sie-wählen)
7. [Verwandte Seiten](#verwandte-seiten)

## Auf einen Blick

| | sevenx_multisite | cjw_multisite |
|---|---|---|
| Zusammenfassung im Index | `7x MultiSite Default Installation` | `CJW Multisite` |
| Version im Index | `6.0-15-stable` (`1.0.0-0` bis zum 19.10.2026) | `6.0-15-stable` (`6.0.15-0` bis zum 19.10.2026) |
| Standardpaket von `exp:install` und der Kickstarter-Beispiele | ja | nein |
| Design der öffentlichen Website | `media` (dann `ezwebin`, `standard`, `base`) | `starter` (dann `standard`) |
| Theme-Erweiterung der öffentlichen Website | `sevenx_themes_media` (mit `sevenx_themes_simple`) | `cjw_themes_jumper` (beide Theme-Erweiterungen von 7x bleiben in `ActiveExtensions[]`) |
| Websites | Fit & Healthy, plus Bold Agency (`bold`, `bold_ger`), wenn das Theme sie anbietet | nur die Startseite |
| Standardtitel der Website | Fit & Healthy (Bold Agency auf einem Siteaccess `bold`) | `CJW JAC Exponential` |
| Variante ohne Demo-Inhalte | `sevenx_multisite_clean` | keine |

## Pakete und Größen

| | sevenx_multisite | cjw_multisite |
|---|---|---|
| Verlangte Pakete | `sevenx_classes`, `sevenx_multisite_democontent` | `sevenx_classes`, `cjw_multisite_democontent` |
| Archiv des Seitenpakets | 90.369 Bytes | 97.634 Bytes |
| Archiv des Inhaltspakets | 62.314.497 Bytes | 169.949 Bytes |
| Klassendefinitionen im Inhaltspaket | 62 | 40 |
| Objektdateien im Inhaltspaket | 404 | 138 |
| Einträge für Simple Files | 314, mitgebracht in `simplefiles/` des Pakets (194 Dateien) | 158, die in `extension/cjw_themes_jumper/share/` zeigen |
| Nebendateien, die der Installer liest | keine | `cjw-class-additions.xml`, `cjw-explayouts.json`, `cjw-paths.json`, `cjw-starter-import.json` |

Die Archivgrößen sind die `content-length` der Dateien von 6.0.15 auf dem Paketserver. Der große Unterschied sind die Bilder:
`sevenx_multisite_democontent` bringt eigene mit, während `cjw_multisite_democontent` sie während der Installation aus der Theme-Erweiterung
liest (siehe [Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md)).

## Design, Inhalte und Daten

| | sevenx_multisite | cjw_multisite |
|---|---|---|
| Remote-ID der Startseite | `media-n-939` (Fit & Healthy) | `cjw-starter-n-779` (Startseite) |
| Objektdateien | nach der 32-stelligen Remote-ID benannt (`object-<remote id>.xml`) | `object-cjw-starter-o-<id>.xml`; Knoten `cjw-starter-n-<id>`, Tags `cjw-starter-t-<id>` |
| Sprachen der Inhalte | siehe [Die Demo-Inhaltspakete](../sevenx-multisite/die-demo-inhaltspakete.md) | überwiegend `ger-DE` (111 von 137 Objekten nur deutsch) |
| Zeilen der Layouts | aus `extension/sevenx_themes_media/share/db_data.dba` und `extension/explayouts/share/db_data.dba` (`declaredExtensionSchemas()` lädt Daten für beide) | aus `cjw-explayouts.json` im Inhaltspaket, IDs ab 900000 (`cjwSeedStarterLayouts()`); keine `db_data.dba` einer Erweiterung wird geladen |
| Tags | eztags-Zeilen in der `db_data.dba` von `sevenx_themes_media` und `explayouts` | aus `cjw-starter-import.json` unterhalb eines Tags „Starter“ angelegt |
| Sternebewertungen | nach der Installation neu verschlüsselt (`postInstallRekeyStarRatings`) | keine |
| Einstellungen der Erweiterung für die Website | die Einstellungen des Themes für einen Siteaccess namens `site`; Übersetzungen nehmen sie mit `ExtensionSettingsSiteAccess=<user siteaccess>` | `ExtensionSettingsSiteAccess=cjw_starter` |
| Quelle des RSS-Feeds | unterhalb der Startseite von Fit & Healthy | unterhalb der Wurzel der Startseite |
| Zusätzliche Rollenrichtlinie | keine | `osm_proxy/tiles` für die Karten der Startseite |

Die Details der Seite von 7x stehen auf [Layoutdaten und share-DBA-Dateien](../sevenx-multisite/layoutdaten-und-share-dba-dateien.md).

## Siteaccesses und Sprachen

| | sevenx_multisite | cjw_multisite |
|---|---|---|
| Benutzer-Siteaccess | standardmäßig `site` | standardmäßig `site` |
| Übersetzungs-Siteaccesses | einer pro weiterer Sprache | einer pro weiterer Sprache (`ger` für `ger-DE`) |
| Zweite Siteaccesses | `bold`, `bold_ger`, wenn verfügbar | keine |
| Administration | `admin` (`admin4l`), `editor`, `adminui` | dieselben |
| Rat zu Sprachen | siehe [Siteaccesses und Sprachen von sevenx_multisite](../sevenx-multisite/siteaccesses-und-sprachen-von-sevenx-multisite.md) | auch `ger-DE` wählen: `--languages=eng-US,ger-DE` (Beschreibung des Pakets) |

## Die Installer

| | sevenx_multisite | cjw_multisite |
|---|---|---|
| Klasse | `sevenxMultiSiteInstaller` | `cjwMultiSiteInstaller` |
| Hauptdatei | `sevenx-multi-site-installer.php`, 7.692 Zeilen | `cjw-multi-site-installer.php`, 5.939 Zeilen |
| Hilfsdateien | `post-install-fix.php`, 1.050 Zeilen | `post-install-fix.php`, 593 Zeilen (Funktionen von `sevenx*` in `cjw*` umbenannt), `cjw-starter-install.php`, 827 Zeilen |
| `solutionExtensionName()` | `sevenx_multisite_default_installer` | `cjw_multisite_default_installer` |
| `ActiveExtensions[]` | die Liste von 7x | dieselbe Liste plus `cjw_themes_jumper` und `cjw_tmv` (weggelassen, wenn `extension/cjw_tmv` fehlt) |
| Schritte nur hier | `postInstallRekeyStarRatings`, `postInstallCleanUrlText` | `postInstallStarterContent`, `postInstallStarterPaths`, `postInstallStarterPolicyPages` |
| Gemeinsame Schritte | Reparaturen von Sprachen und Namen, Einstellungen der Siteaccesses für Administration und Benutzer, Übersetzungen, Rollen, Layouts der Administration, URL-Aliase, Startseite und Präfix, `ezinfo` auf öffentlichen Siteaccesses geschlossen, adminui, Suchstatistik, `ezurlmultisite` | dieselben |

Beide Listen der Einstellungen in `package.xml` haben dieselbe Form: `ini-site.php`, `ini-admin.php`, `ini-common.php`, der Installer,
`install-scripts.php`, `roles.php`, `preferences.php`.

| Quell-Repository | sevenx-ezpackage | cjw_multisite-ezpackage |
|---|---|---|
| Geschichte | 1.226 Commits, der erste am 12.10.2006 | 16 Commits, der erste am 07.10.2026 |
| Pakete | alle Pakete von 7x (`packages/<name>/`, `packages/build.sh`) | die zwei Pakete von CJW (`bin/pack.sh`) |

## Welches Sie wählen

| Sie wollen | Wählen Sie |
|---|---|
| Die Standard-Demo von Exponential mit mehreren Websites, englischen Inhalten und dem Design media | `sevenx_multisite` |
| Dasselbe ohne Demo-Inhalte | `sevenx_multisite_clean` |
| Eine deutsche und englische Startseite im Aussehen von CJW Themes Jumper | `cjw_multisite` |
| Ein Vorbild für ein Paket, das um **Ihre eigene Theme-Erweiterung** gebaut ist, mit den Bildern in der Erweiterung | gehen Sie von `cjw_multisite` aus |
| Ein Vorbild für ein Paket mit mehreren Websites und seinen Daten in `.dba`-Dateien von Erweiterungen | gehen Sie von `sevenx_multisite` aus |

> **Tipp**
> Von welchem Sie auch ausgehen, 7x und die Exponential Foundation ermutigen Sie ausdrücklich, das Ergebnis per Pull Request beim
> Repository des Paketservers einzureichen: [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).
> [Ein Seitenpaket forken](../eigene/ein-seitenpaket-forken.md) erklärt die ersten Schritte.

## Verwandte Seiten

- [cjw_multisite](README.md)
- [sevenx_multisite](../sevenx-multisite/README.md)
- [Die Clean-Variante und sevenx_site](../sevenx-multisite/die-clean-variante-und-sevenx-site.md)
- [Die Installerskripte von cjw_multisite](die-installerskripte-von-cjw-multisite.md)
- [Das Skript des Multisite-Installers von sevenx_multisite](../sevenx-multisite/das-skript-des-multisite-installers.md)
- [Seitenpakete und die mitgelieferten Themes](../../../../eng-us/help/creators/website-creators/site-packages-and-shipped-themes.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
