---
title: cjw_multisite
audience: admin, developer, designer, hosting
category: install-upgrade
order: 75
summary: Ein Überblick über cjw_multisite, das Startseiten-Paket von CJW in Exponential 6.0.15. Was es installiert, welche Pakete und welche Erweiterung es braucht, wie Sie es installieren und wo die Seiten dieses Teils ins Detail gehen.
keywords: cjw_multisite, cjw_multisite_democontent, CJW Multisite, CJW-Startseite, cjw_themes_jumper, Design starter, CJW JAC Exponential, Seitenpaket, ger-DE, Startinhalte, CJW starter, design starter, site package, starter content
type: help
---
# cjw_multisite

`cjw_multisite` ist das zweite Multisite-Paket im Paketindex von Exponential 6.0.15, neben dem Standard
[sevenx_multisite](../sevenx-multisite/README.md). Es installiert die **Startseite von CJW**: eine Website, die vom Design `starter` der
Erweiterung [CJW Themes Jumper](die-erweiterung-cjw-themes-jumper.md) gezeichnet wird, mit ihren Startinhalten auf Deutsch und Englisch, ihren
Layouts und den üblichen Siteaccesses der Administration.

Diese Seite ist für Administratoren, die es installieren wollen, Hoster, die es zur Wahl anbieten, und Entwickler oder Designer, die wissen
wollen, wie ein Seitenpaket funktioniert, das um eine Theme-Erweiterung herum gebaut ist. Sie gibt den Überblick; die unter
[Die Seiten dieses Teils](#die-seiten-dieses-teils) aufgeführten Seiten gehen auf jedes Stück ein.

## Inhalt

1. [Was cjw_multisite ist](#was-cjw_multisite-ist)
2. [Die drei Dinge, die es braucht](#die-drei-dinge-die-es-braucht)
3. [Es installieren](#es-installieren)
4. [Woher es kommt](#woher-es-kommt)
5. [Die Seiten dieses Teils](#die-seiten-dieses-teils)
6. [Verwandte Seiten](#verwandte-seiten)

## Was cjw_multisite ist

Der Paketindex von 6.0.15 beschreibt es so (das Attribut `description` seines Eintrags in
[index.xml](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml)):

```text
CJW Multisite is the starter site of the CJW Themes Jumper design (design starter) for Exponential:
an English and a German site with the starter content and its layouts, and the administration.
```

| Fakt | Wert | Quelle |
|---|---|---|
| Paketname | `cjw_multisite` | `package.xml` `<name>` |
| Zusammenfassung | `CJW Multisite` | `package.xml` `<summary>` |
| Typ | `site` | `<type value="site" />` |
| Hersteller | `7x` | `<vendor>` |
| Version | Nummer `6.0`, Release `15-stable`; der Index führt es als `6.0-15-stable` (`6.0.15-0` bis zum 19.10.2026) | `<version>` in `package.xml`, `version` in `index.xml` |
| Installationstyp | `import` | `install_type="import"` |
| Lizenz | GPL (`<licence>GPL</licence>`, mit `documents/LICENCE`) | `package.xml` |
| Archiv | `cjw_multisite.ezpkg`, 97.634 Bytes auf dem Paketserver | `content-length` des Downloads |
| Standardtitel der Website | `CJW JAC Exponential` | `initSettings()` des Installers |

Das Seitenpaket selbst bringt keine Inhalte mit: Wie jedes Seitenpaket nennt es die Pakete, die es braucht, und bringt die
Installationsskripte mit, die die Siteaccesses und Einstellungen um sie herum bauen. Siehe [Woraus es besteht](woraus-cjw-multisite-besteht.md).

## Die drei Dinge, die es braucht

| Gebraucht | Was es bringt | Woher es kommt |
|---|---|---|
| `sevenx_classes` | Die grundlegenden Inhaltsklassen (`<require type="ezpackage" name="sevenx_classes" min-version="5.1" />`) | Der Paketserver, dasselbe Paket, das `sevenx_multisite` nutzt |
| `cjw_multisite_democontent` | Die Startinhalte, 40 Inhaltsklassen und die Layouts, Tags und URL-Pfade als JSON- und XML-Nebendateien | Der Paketserver; siehe [Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md) |
| Die Erweiterung `cjw_themes_jumper` | Das Design `starter` und die **Bilder und Dateien** der Startinhalte (`share/content-images`, `share/content-files`) | `extension/cjw_themes_jumper`; die `composer.json` im Wurzelverzeichnis von Exponential 6.0.15 verlangt `se7enxweb/cjw_themes_jumper` `~0.1.6` |

> **Warnung**
> Die Bilder und Dateien der Startinhalte stehen in keinem Paket. Der Installer verlinkt sie für die Dauer der Installation aus
> `extension/cjw_themes_jumper/share/` in das Inhaltspaket. Ohne die Erweiterung in `extension/` protokolliert die Installation die
> fehlenden Dateien, und die Website hat kein Design. Siehe [Die Erweiterung CJW Themes Jumper](die-erweiterung-cjw-themes-jumper.md).

## Es installieren

`cjw_multisite` installiert sich wie jedes Seitenpaket: Wählen Sie es im [Setup-Assistenten](../../../installation/05-setup-assistent.md), nennen Sie
es als `Site_package` in `kickstart.ini` für den [Kickstarter](../../../installation/06-kickstarter.md) oder übergeben Sie es der
[Installation über die Konsole](../../../installation/07-installation-ueber-die-konsole.md). Die eigene Beschreibung des Pakets gibt diesen Befehl an:

```bash
./console exp:install --package=cjw_multisite --languages=eng-US,ger-DE
```

Dieselbe Beschreibung rät, auch Deutsch zu wählen, weil die meisten Startinhalte deutsch sind. Von den 137 Startobjekten, die in
`cjw-starter-import.json` aufgeführt sind, gibt es 111 nur in `ger-DE`, 22 nur in `eng-US` und 4 in beiden. Der Changelog von 6.0.15 hält
fest, dass eine Installation nur mit `eng-US` früher 125 Fehler protokollierte und diese Inhalte nicht zeigen konnte; der Kernel fügt die
Sprache der Inhalte nun als Rückfallsprache hinzu (`eZStepCreateSites::contentFallbackLanguages()`). Dennoch macht erst ein deutscher
Siteaccess die Startseite vollständig. Siehe [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-cjw-multisite.md).

Für den Kickstarter der entsprechende Teil von `kickstart.ini`:

```ini
[language_options]
Continue=true
Primary=eng-US
Languages[]=ger-DE

[site_types]
Continue=true
Site_package=cjw_multisite
```

## Woher es kommt

| Ort | Was dort liegt |
|---|---|
| [se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) | Die Quelle: `packages/cjw_multisite/`, `packages/cjw_multisite_democontent/` und `bin/pack.sh`, das jeden Ordner in seine `.ezpkg` packt |
| [Der Paketserver, 6.0.15](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml) | [cjw_multisite.ezpkg](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/cjw_multisite.ezpkg), [cjw_multisite_democontent.ezpkg](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/cjw_multisite_democontent.ezpkg) und das [Vorschaubild](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/thumbnails/cjw_multisite.png) |
| [se7enxweb/cjw_themes_jumper](https://github.com/se7enxweb/cjw_themes_jumper) | Das Design und die Bilder und Dateien der Inhalte |
| `var/storage/packages/7x/cjw_multisite` und `.../cjw_multisite_democontent` | Die importierten Kopien in einer Installation |

Das Quell-Repository wurde am 07.10.2026 angelegt (der erste Commit fügte das Quell-Repository der Installationspakete von CJW Multisite
hinzu, aufgebaut wie das Paket-Repository von 7x), und die beiden Pakete erreichten den Paketserver am selben Tag. Bis zum 16.10.2026 hat das
Repository 16 Commits; die späteren ändern den Installer (expqueue und expai, ezoracle, ezurlmultisite, die Oberflächenwahl der Redaktion,
exp_adminui, ezownerchange).

Eine laufende Kopie der Startseite ist auf [Demo-Websites](../../../../eng-us/help/designers/distribution/demo-sites.md) aufgeführt:
[site.cjw.demo.exponential.earth](https://site.cjw.demo.exponential.earth/).

## Die Seiten dieses Teils

| Seite | Was sie beantwortet |
|---|---|
| [Woraus es besteht](woraus-cjw-multisite-besteht.md) | Die zwei Pakete, ihre Dateien, die Einstellungsdateien und das Quell-Repository |
| [Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md) | `cjw_multisite_democontent`: Klassen, Objekte, `cjw-class-additions.xml`, `cjw-explayouts.json`, `cjw-paths.json`, `cjw-starter-import.json` |
| [Die Installerskripte](die-installerskripte-von-cjw-multisite.md) | `cjw-multi-site-installer.php` und `cjw-starter-install.php`, Schritt für Schritt |
| [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-cjw-multisite.md) | `site`, die Übersetzungs-Siteaccesses, `admin`, `editor`, `adminui` und wie Sprachen gewählt werden |
| [Die Erweiterung CJW Themes Jumper](die-erweiterung-cjw-themes-jumper.md) | Das Design `starter`, `share/`, die Einstellungen `cjw_starter` |
| [Was Sie nach der Installation haben](was-sie-nach-der-installation-von-cjw-multisite-haben.md) | Die Website, der Inhaltsbaum, Layouts, Rollen, Einstellungsdateien und eine Checkliste |
| [sevenx_multisite und cjw_multisite im Vergleich](sevenx-multisite-und-cjw-multisite-im-vergleich.md) | Nebeneinander: Design, Inhalte, Daten, Siteaccesses, Größe |

> **Tipp**
> Ein Seitenpaket, das um Ihre eigene Theme-Erweiterung herum gebaut ist, kann demselben Muster folgen. 7x und die Exponential Foundation
> ermutigen Sie ausdrücklich, es per Pull Request einzureichen, damit jede Installation es anbieten kann:
> [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [sevenx_multisite](../sevenx-multisite/README.md)
- [Was ein Seiteninstallationspaket ist](../grundlagen/was-ein-seiteninstallationspaket-ist.md)
- [Was auf dem Paketserver liegt](../paketserver/was-auf-dem-paketserver-liegt.md)
- [Glossar](../glossar-der-seitenpakete.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
