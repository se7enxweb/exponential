---
title: Seitenpakete und die mitgelieferten Themes
audience: website-creator, admin
category: website-creation
order: 100
summary: Was ein Seitenpaket installiert (Klassen, Design, Inhalte, Rollen, Einstellungen), welche Pakete und Themes mit Exponential ausgeliefert werden, woher sie kommen und wie Sie zwischen einem vollständigen und einem leeren Paket wählen.
keywords: Seitenpaket, Paket, Theme, Paketindex, Demo-Inhalte, Website-Ersteller, site package, package, sevenx_multisite, sevenx_multisite_clean, sevenx_site, ezwebin_site, ezflow_site, ezdemo_site, plain_site, theme, ezpkg, package index, website creator
type: help
---
# Seitenpakete und die mitgelieferten Themes

Diese Seite richtet sich an Website-Ersteller, die wissen wollen, was sie vom Installer bekommen, bevor sie wählen. Ein
**Seitenpaket** ist der Ausgangspunkt einer neuen Installation: Inhaltsklassen, ein Design, Inhalte, Rollen und
Einstellungen in einem Paket. Ein **Theme** ist der Designteil, eine Erweiterung, die Sie nach der Installation
weiterverwenden können.

![Die Seite Über: die Erweiterungen der Installation, darunter die mitgelieferten Themes](../../bilder/creators/about-extensions.webp)

## Inhalt

1. [Was ein Seitenpaket enthält](#was-ein-seitenpaket-enthält)
2. [Die Seitenpakete dieser Version](#die-seitenpakete-dieser-version)
3. [Woher Pakete kommen](#woher-pakete-kommen)
4. [Vollständig oder leer](#vollständig-oder-leer)
5. [Die Themes hinter den Paketen](#die-themes-hinter-den-paketen)
6. [Pakete nach der Installation](#pakete-nach-der-installation)

## Was ein Seitenpaket enthält

Ein Seitenpaket ist ein Exponential-Paket vom Typ `site` (`package.xml` mit `<type>site</type>`) plus die Pakete, die
es voraussetzt ([4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)).
Das Standardpaket, `sevenx_multisite`, setzt `sevenx_classes` (die Inhaltsklassen) und `sevenx_multisite_democontent`
(die Demo-Inhalte) voraus. Ein Paket kann auch Installationsskripte mitbringen, die Siteaccess-Einstellungen, Rollen
und Voreinstellungen einrichten: `sevenx_multisite` listet `ini-site.php`, `ini-admin.php`, `ini-common.php`,
`sevenx-multi-site-installer.php`, `install-scripts.php`, `roles.php` und `preferences.php` auf.

| Teil | Wo er landet |
|---|---|
| Inhaltsklassen | Setup > Klassen |
| Inhalte (Demo oder leere Struktur) | der Inhaltsbaum und die Medienbibliothek |
| Rollen und Richtlinien | Benutzer > Rollen |
| Einstellungen für die Siteaccesses public, admin und editor | `settings/siteaccess/<name>/` und `settings/override/` |
| Das Design | eine Erweiterung, die in den Einstellungen aktiviert wird |

## Die Seitenpakete dieser Version

Der Paketindex dieser Version bietet elf Seitenpakete an. Diejenigen, denen Sie begegnen werden
([4.3](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)):

| Paket | Zusammenfassung im Index |
|---|---|
| `sevenx_multisite` | 7x MultiSite Default Installation (der Standard) |
| `sevenx_multisite_clean` | dasselbe, ohne Demo-Inhalte |
| `sevenx_site`, `sevenx_site_clean` | 7x Simple Website Interface |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site` (jeweils auch `_clean`), `plain_site` | die älteren Pakete, beibehalten für Websites, die darauf aufbauen |

## Woher Pakete kommen

- **Der entfernte Paketindex**, `[RepositorySettings] RemotePackagesIndexURL` in `settings/package.ini`. Ein gelistetes
  Paket, das noch nicht importiert ist, wird mit den Paketen, die es voraussetzt, heruntergeladen und importiert.
- **Das lokale Repository** unter `var/storage/packages/` (Anbieterverzeichnis `7x`, aus `[RepositorySettings] Vendor`).
  Wenn der Index nicht heruntergeladen werden kann, sucht der Installer dort nach einer `index.xml`: So funktioniert eine
  Offline-Installation.
- **Ein Upload**: Die Seite für Seitenpakete im Einrichtungsassistenten hat ein Feld **Paket hochladen** für eine
  `.ezpkg`-Datei.

Pakete werden auch nach der Installation unter **Setup > Pakete** verwaltet ([Pakete](../../../anleitungen/pakete.md)).

## Vollständig oder leer

| | Vollständiges Paket | `_clean`-Paket |
|---|---|---|
| Klassen, Design, Rollen | ja | ja |
| Demo-Artikel und -Bilder | ja | nein |
| Gut für | Lernen, Demos, jedes Template im Einsatz sehen | die echte Website, die Sie selbst füllen |

> **Tipp**
> Installieren Sie beide einmal, auf zwei Datenbanken: Das vollständige zeigt Ihnen, wie jede Klasse aussehen soll, das
> leere ist der Anfang Ihrer Website.

## Die Themes hinter den Paketen

| Theme (Erweiterung) | Design | Verwendet von |
|---|---|---|
| `sevenx_themes_simple` | `simple` | der Standardinstallation ([sevenx_themes_simple](../../../funktionen/6.0/erweiterungen/sevenx_themes_simple.md)) |
| `ezwebin` | `ezwebin` | `ezwebin_site` ([ezwebin](../../../funktionen/6.0/erweiterungen/ezwebin.md)) |
| `ezdemo` | `ezdemo` | `ezdemo_site` ([ezdemo](../../../funktionen/6.0/erweiterungen/ezdemo.md)) |
| `ezflow` | `ezflow` | `ezflow_site` ([ezflow](../../../funktionen/6.0/erweiterungen/ezflow.md)) |

Jedes Theme ist ein Composer-Paket; prüfen Sie Ihre Version mit `grep Version extension/<theme>/ezinfo.php` oder auf der
Seite Über (`/ezinfo/about`).

## Pakete nach der Installation

- Ein Paket ist eine Momentaufnahme: Wenn Sie eine Klasse in der Administration ändern, ändert sich das Paket nicht.
- Um Klassen oder Inhalte zwischen Installationen zu übertragen, exportieren Sie unter **Setup > Pakete** ein Paket
  und importieren es auf der anderen Seite ([Pakete und .ezpkg](../anwendungsentwickler/pakete-und-ezpkg.md)).
- Eine Neuinstallation ersetzt die Datenbank. Installieren Sie eine Website mit echten Inhalten niemals neu; siehe
  [4.4 Was eine Installation schreibt](../../../installation/04-installationsweg-waehlen.md#44-was-eine-installation-schreibt).

## Verwandte Seiten

- [Eine Installationsmethode wählen](eine-installationsmethode-waehlen.md)
- [Ein Design oder Theme wählen](ein-design-oder-theme-waehlen.md)
- [Standardauslieferung der Erweiterungen](../../../funktionen/6.0/standard-extension-auslieferung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
