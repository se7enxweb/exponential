---
title: Die Themes, die mit Exponential geliefert werden
audience: designer
category: theme-design
order: 90
summary: Eine Übersicht über jedes Design und jede Theme-Erweiterung, die das Exponential-Hauptpaket mitbringt oder diese Demonstrationsinstallation hinzufügt, mit den Siteaccesses, die sie verwenden, und wo Sie mehr lesen.
keywords: Themes, Theme-Liste, mitgelieferte Themes, media, simple, starter, ezwebin, ezdemo, ezflow, adminui, admin4, admin4l, editor, sevenx_themes, cjw_themes_jumper, verfügbare Designs, themes, theme list, shipped themes, available designs
type: help
---
# Die Themes, die mit Exponential geliefert werden

Diese Seite ist für Designer, die sehen möchten, was schon existiert, bevor sie beginnen. Sie listet die Designs, die mit dem
Exponential-Hauptpaket kommen (seiner `composer.json`), die Designs im Ordner `design/` des Kernels und wo jedes verwendet wird. Verwenden Sie
sie, um ein Template zum Lernen oder ein Design zum Aufbauen zu finden.

## Inhalt

1. [Themes für öffentliche Websites](#themes-für-öffentliche-websites)
2. [Designs der Administration](#designs-der-administration)
3. [Basisdesigns des Kernels](#basisdesigns-des-kernels)
4. [Wo jedes auf der Demonstrationsinstallation verwendet wird](#wo-jedes-auf-der-demonstrationsinstallation-verwendet-wird)
5. [Wie Sie sich eines ansehen](#wie-sie-sich-eines-ansehen)

## Themes für öffentliche Websites

| Erweiterung (Composer-Paket) | Design(s) | In einer Zeile | Mehr lesen |
|---|---|---|---|
| `sevenx_themes_media` (`se7enxweb/sevenx-themes-media`) | `media` | das Aussehen der Media-Site: Hunderte Templates, Block-Templates für den Layout-Editor, selbst gehostete Schriften, gepacktes CSS und JS | [Das Theme media](rundgang-durch-das-media-theme.md) |
| `sevenx_themes_simple` (`se7enxweb/sevenx_themes_simple`) | `simple` | das leichte, responsive öffentliche Standarddesign, auf den Klassen von ezwebin aufgebaut | [Das Theme simple](rundgang-durch-das-simple-theme.md) |
| `cjw_themes_jumper` (`se7enxweb/cjw_themes_jumper`) | `starter`, `standard` | das CJW-Starter-Aussehen, mit Layouts gezeichnet, mit eigenen Bildaliasen und einem Proxy für Kartenkacheln | [Das Theme starter](rundgang-durch-das-starter-theme.md) |
| `ezwebin` (`se7enxweb/ezwebin`) | `ezwebin` | das klassische Design website interface und seine Inhaltsklassen | [Das Design ezwebin als Grundlage](ezwebin-als-grundlage.md) |
| `ezdemo` (`se7enxweb/ezdemo`) | `ezdemo`, `admin` | das klassische Design der Demo-Website | [ezdemo](../../../funktionen/6.0/erweiterungen/ezdemo.md) |
| `ezflow` (`se7enxweb/ezflow`) | `ezflow`, `standard`, `admin`, `iphone` | der klassische Page Builder (Datentyp page mit Zonen und Blöcken) | [ezflow](../../../funktionen/6.0/erweiterungen/ezflow.md) |

![Der Siteaccess bold der Demonstrationsinstallation, der das Design media mit anderen Inhalten zeigt](../../bilder/designers/themes-public-site.webp)

Die Demonstrationsinstallation hat außerdem `sevenx_themes_super`, ein kleines Design, das mit dem Assistenten für Design-Erweiterungen
erzeugt wurde ([Das Theme super](rundgang-durch-das-super-theme.md)). Es ist nicht Teil des Hauptpakets.

## Designs der Administration

| Design | Wo | Hinweise |
|---|---|---|
| `admin` | `design/admin` | das klassische Administrationsdesign |
| `admin2`, `admin3` | `design/admin2`, `design/admin3` | spätere Administrationsdesigns, weiterhin in der Rückfallkette von admin4 |
| `admin4` | `design/admin4` | das aktuelle Administrationsdesign: Token, Komponenten, dunkler Modus |
| `admin4l` | `design/admin4l` | ein kleines Design auf admin4, hier das `SiteDesign` des Siteaccess `admin` |
| `editor` | `design/editor` | der Siteaccess editor, auf den Admin-Designs |
| `adminui` | `extension/exp_adminui/design/adminui` | die Administration AdminUI |

Siehe [Admin-Designs im Überblick](admin-designs-ueberblick.md).

## Basisdesigns des Kernels

| Design | Rolle |
|---|---|
| `standard` | das Standard-`StandardDesign`: ein Template für alles, wonach der Kernel und seine Module fragen können. Jede Kette endet hier. |
| `base` | ein kleines Design, das mehrere öffentliche Ketten nach `standard` aufführen (es hat `templates`, `override`, `stylesheets`, `images`) |
| `plain` | ein minimales Design in `design/plain` |

## Wo jedes auf der Demonstrationsinstallation verwendet wird

| Siteaccess | Design-Kette |
|---|---|
| `site` | `media` > `ezwebin` > `standard` > `base` |
| `bold` | `media` > `simple` > `ezwebin` > `standard` > `base` |
| `starter`, `starter_ger` | `starter` > `standard` |
| `portaljq` | `expportal_jquery` > `standard` > `base` |
| `admin` | `admin4l` > `admin4` > `admin3` > `admin2` > `admin` > `standard` |
| `editor` | `editor` > `admin4l` > `admin4` > `admin3` > `admin2` > `admin` > `standard` |
| `adminui` | `adminui` > `admin4l` > `admin4` > `admin3` > `admin2` > `admin` > `standard` |

(Aus `settings/siteaccess/*/site.ini.append.php` gelesen; `standard` fügt `StandardDesign` zuletzt hinzu.)

## Wie Sie sich eines ansehen

- **Im Dateibaum.** Das `design/<design>/templates/` jeder Erweiterung ist Klartext: Öffnen Sie zuerst `pagelayout.tpl`, dann die Ordner,
  die es einbindet.
- **Im Browser.** Öffnen Sie einen Siteaccess, der das Design verwendet, und schalten Sie auf einem Test-Siteaccess die Kommentare mit
  Template-Pfaden ein ([Ein Design debuggen](ein-design-debuggen.md)).
- **In der Administration.** **Design > Template-Editor** (`/visual/templatelist`) listet die Templates eines Siteaccess und ihre Overrides
  und legt neue Overrides an ([Overrides im Template-Editor](../../../funktionen/6.0/template-editor-ueberschreibungen.md)).

## Verwandte Seiten

- [Ein Ausgangs-Theme wählen](ein-ausgangs-theme-waehlen.md)
- [Designs und Siteaccesses](designs-und-siteaccesses.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
