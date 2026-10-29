---
title: Die Erweiterung CJW Themes Jumper
audience: designer, developer, admin
category: install-upgrade
order: 80
summary: Die Erweiterung cjw_themes_jumper, von der cjw_multisite abhängt. Was sie mitbringt (das Design starter, die Bilder und Dateien der Startinhalte, zwei Module), wie ihre globalen und Siteaccess-Einstellungen aufgeteilt sind, wie der Installer sie nutzt und wie Sie sie von Hand einrichten.
keywords: cjw_themes_jumper, CJW Themes Jumper, Design starter, ExtensionSettingsSiteAccess, cjw_starter, share/content-images, share/content-files, osm_proxy, cjw_paging, cjw_jumper.ini, Bildaliase, imagick, cjw_tmv, Theme-Erweiterung, design starter, image aliases, theme extension
type: help
---
# Die Erweiterung CJW Themes Jumper

`cjw_multisite` ist ein Seitenpaket, das um eine Theme-Erweiterung herum gebaut ist: **CJW Themes Jumper** (`cjw_themes_jumper`). Die
Erweiterung zeichnet die Website; die Pakete bringen nur die Inhalte und die Installationsskripte. Diese Seite beschreibt, was die
Erweiterung enthält, wie der Installer sie einbindet und was Sie wissen sollten, wenn Sie sie selbst einrichten. Sie ist für Designer, die
eine Theme-Erweiterung sehen wollen, von der ein Seitenpaket abhängt, und für Administratoren, die wissen müssen, warum die Erweiterung vor
der Installation vorhanden sein muss.

## Inhalt

1. [Die Erweiterung in Kürze](#die-erweiterung-in-kürze)
2. [Was sie mitbringt](#was-sie-mitbringt)
3. [Globale und Siteaccess-Einstellungen](#globale-und-siteaccess-einstellungen)
4. [Wie cjw_multisite sie nutzt](#wie-cjw_multisite-sie-nutzt)
5. [Sie von Hand einrichten](#sie-von-hand-einrichten)
6. [Anforderungen](#anforderungen)
7. [Verwandte Seiten](#verwandte-seiten)

## Die Erweiterung in Kürze

| Fakt | Wert | Quelle |
|---|---|---|
| Name | CJW Themes Jumper | `ezinfo.php`, `extension.xml` |
| Version in Exponential 6.0.15 | 0.1.6 | `ezinfo.php` `Version`; die `composer.json` im Wurzelverzeichnis verlangt `se7enxweb/cjw_themes_jumper` `~0.1.6` |
| Copyright | 2007 - 2026 CJW Network, JAC Systeme GmbH und 7x | `ezinfo.php` |
| Lizenz | GNU General Public License v2.0 (oder eine spätere Version) | `ezinfo.php`, `composer.json` (`GPL-2.0-or-later`) |
| Repository | [se7enxweb/cjw_themes_jumper](https://github.com/se7enxweb/cjw_themes_jumper) | `Info_url` |
| Composer-Typ | `ezpublish-legacy-extension` | `composer.json` |

Ihr README nennt sie das Design `starter` für Exponential 6: das Aussehen der Startseite von CJW aus Nexus v2, portiert in einfachen
Template-Code von Exponential. Das erste Release, 0.1.0, beschreibt ihr `CHANGELOG.md` als eine Portierung des Aussehens der Startseite von
CJW aus Nexus v2 auf Exponential 6; spätere Releases fügten Open-Graph-Tags hinzu (0.1.2), einen Link zur Registrierung nur für Besucher,
die sich registrieren dürfen (0.1.3), eine Korrektur der Maskierung im Popup der Karte (0.1.4) und Korrekturen an Kontrast und
Zielgröße (0.1.5).

## Was sie mitbringt

| Teil | Wo |
|---|---|
| Das Design `starter`: Seitenlayout, Kopf, Offcanvas-Menü, Brotkrumen, Pre-Footer, Footer; Ansichten full, line, list, tile, teaser, slide, gallery und embed der Startklassen; Seiten für Suche, Anmeldung, Registrierung, Passwort und Fehler; Feedback-Formulare mit einem Honeypot-Feld | `design/starter/` |
| Templates für die Layoutblöcke der Start-Layouts, einschließlich Listen- und Rasterblöcken mit Seitenumschaltung | `design/starter/`, Modul `cjw_paging` |
| Die eigenen XML-Tags der Startinhalte, mit einfachen Darstellungen für jedes andere Design | `design/standard/templates/`, `settings/content.ini.append.php` |
| Die Bildaliase `cjw_i30` ... `cjw_i1920` und Zuschnitte 16:9, gezeichnet von `cjwJumperImagineImageHandler` | `settings/image.ini.append.php`, `classes/` |
| Zwischengespeicherte Kartenkacheln für die Leaflet-Karten | Modul `osm_proxy`, `settings/osm_proxy.ini.append.php` |
| Deutsche Texte des Designs | `translations/ger-DE/` |
| **Die Bilder und Dateien der Startinhalte** | `share/content-images/` (74 Dateien in 6.0.15), `share/content-files/` (3 Dateien) |

Die Ordner `share/` sind der Grund, warum das Inhaltspaket klein ist: `cjw_multisite_democontent` nennt jede Datei mit einem
`extension-path` in diese Ordner, statt sie mitzubringen (siehe
[Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md#simple-files-die-in-die-erweiterung-zeigen)).

## Globale und Siteaccess-Einstellungen

Die Erweiterung ist als **globale** Erweiterung gedacht (`ActiveExtensions[]`), aber nur wenige Dinge sind global:

| Global (`settings/`) | Pro Siteaccess (`settings/siteaccess/cjw_starter/`) |
|---|---|
| `content.ini.append.php` (eigene XML-Tags), `design.ini.append.php`, `image.ini.append.php`, `cjw_jumper.ini.append.php`, `osm_proxy.ini.append.php` | `site.ini.append.php`, `override.ini.append.php`, `module.ini.append.php`, `collect.ini.append.php`, `explayouts.ini.append.php` |

Der globale Teil lässt jeden Siteaccess (auch die Administration und den Online-Editor) Startinhalte öffnen und speichern, ohne ihre eigenen
Tags zu verlieren. Alles, was das Verhalten einer Website ändert (Template-Overrides, Behandlung der Suche, Sammeln von Informationen,
Ansichtstypen der Blöcke, die Module `osm_proxy` und `cjw_paging`), liegt im Ordner `cjw_starter` und erreicht einen Siteaccess nur über:

```ini
[SiteAccessSettings]
ExtensionSettingsSiteAccess=cjw_starter
```

Das Design findet, was sein Seitenrahmen braucht, anhand der Remote-ID, nie anhand der Knoten-ID (`settings/cjw_jumper.ini.append.php`):
zum Beispiel `[SiteInfo] NodeRemoteID=cjw-starter-n-65` und `[Header] TopLinkContainerNodeRemoteID=cjw-starter-n-1292`. Deshalb funktionieren
dieselben Einstellungen auf jeder Installation der Startinhalte.

## Wie cjw_multisite sie nutzt

| Wann | Was der Installer tut | Code |
|---|---|---|
| Liste der Erweiterungen | Setzt `cjw_themes_jumper` (und `cjw_tmv`, wenn vorhanden) in `ActiveExtensions[]`, nach den anderen Themes | `initSettings()` |
| Vor den Paketen | Verlinkt die 158 Simple Files aus `share/` in das Inhaltspaket | `cjwLinkStarterFilesFromExtension()` |
| Benutzer-Siteaccess | `SiteDesign=starter`, `AdditionalSiteDesignList[]=standard`, `ExtensionSettingsSiteAccess=cjw_starter`; protokolliert, dass `extension/cjw_themes_jumper` fehlt und damit das Design starter, wenn der Ordner nicht vorhanden ist | `postInstallUserSiteaccessINIUpdate()` |
| Overrides anderer Erweiterungen | Override-Blöcke, die eine andere aktive Erweiterung für einen Siteaccess gleichen Namens mitbringt, werden in der eigenen `override.ini.append.php` des Siteaccess außer Gebrauch gesetzt | `cjwWriteStarterSiteaccessOverrides()` |
| Rollen | Anonymous und Member bekommen `osm_proxy/tiles` | `siteRoles()` |
| Nach der Installation | Entfernt die Links in `share/` wieder | `cjwUnlinkStarterFilesFromExtension()` |

Den Schritt für die Overrides gibt es, weil `standard` der einzige Rückfall des Designs starter ist: Ein Override, dessen Template die Kette
von `starter` zu `standard` nicht hat, würde eine leere Seite rendern. Jeder solche Block bekommt
`Source=cjw/override_not_used_by_the_starter_design.tpl`. Die eigenen Overrides der Erweiterung in `cjw_starter` werden nicht angefasst.

> **Warnung**
> Lassen Sie `share/content-images/` und `share/content-files/` auf jeder Installation an ihrem Platz, die `cjw_multisite` erneut
> installieren könnte. Die INSTALL.md der Erweiterung sagt dasselbe: Das Inhaltspaket liest die Bilder und Dateien dieser Inhalte aus diesen
> Ordnern.

## Sie von Hand einrichten

Für einen eigenen Siteaccess (nicht von `cjw_multisite` installiert) gibt die `INSTALL.md` der Erweiterung diese Schritte an:

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=cjw_themes_jumper

# settings/siteaccess/<siteaccess>/site.ini.append.php
[SiteAccessSettings]
ExtensionSettingsSiteAccess=cjw_starter

[DesignSettings]
SiteDesign=starter
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard
```

Geben Sie dem Siteaccess dann seine Inhaltswurzel (`[NodeSettings] RootNode`, `IndexPage`, `DefaultPage`), fügen Sie ihn zu
`AvailableSiteAccessList[]` und `SiteList[]` hinzu und leeren Sie die Caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=template-override --allow-root-user
```

Ohne die Startinhalte hat das Design wenig zu zeichnen: Es erwartet die Klassen und Remote-IDs von `cjw_multisite_democontent`.

## Anforderungen

Aus dem README der Erweiterung:

- [ ] Exponential 6 mit dem Design `standard`, PHP 7.4 oder neuer.
- [ ] `explayouts` und `ezjscore` (jQuery) für die Seiten und Blöcke.
- [ ] Die PHP-Erweiterung `imagick` für die Bildaliase `cjw_*`.
- [ ] Wahlweise: `cjw_tmv`, der Veranstaltungs-Feed von TMV, für die Seiten der Veranstaltungscontainer. Exponential 6.0.15 verlangt
  `se7enxweb/cjw_tmv` `~0.1.2` in seiner `composer.json` im Wurzelverzeichnis; sein Konto wird pro Installation in `settings/override`
  gesetzt, nie in einem Paket (laut Kommentar des Installers).

Die Kartenkacheln holt `osm_proxy` von dem Server, der in `settings/osm_proxy.ini.append.php` genannt ist; beachten Sie die Nutzungsrichtlinie
dieses Servers für Kacheln.

## Verwandte Seiten

- [cjw_multisite](README.md)
- [Das Demo-Inhaltspaket](das-demo-inhaltspaket-cjw-multisite-democontent.md)
- [Die Installerskripte](die-installerskripte-von-cjw-multisite.md)
- [Was eine Theme-Erweiterung ist](../../designer/erweiterungen/was-eine-theme-erweiterung-ist.md)
- [Siteaccess-Einstellungen in einer Erweiterung](../../designer/erweiterungen/siteaccess-einstellungen-in-einem-theme.md)
- [Assets im Ordner share](../../designer/erweiterungen/der-share-ordner.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
