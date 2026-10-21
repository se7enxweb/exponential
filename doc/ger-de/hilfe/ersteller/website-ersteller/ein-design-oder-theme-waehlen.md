---
title: Ein Design oder Theme wählen
audience: website-creator, admin
category: website-creation
order: 90
summary: Drei Wege zu einem Design für Ihre Website - ein mitgeliefertes Theme verwenden, eines in Ihrer eigenen Erweiterung anpassen oder ein eigenes bauen - mit dem jeweiligen Aufwand bei Arbeit und Upgrades und der Umstellung eines Siteaccess auf ein Design.
keywords: Design, Theme, Erscheinungsbild, eigenes Design, Design-Erweiterung, Website-Ersteller, design, theme, look and feel, sevenx_themes_simple, ezwebin, ezdemo, media theme, own design, design extension, SiteDesign, website creator
type: help
---
# Ein Design oder Theme wählen

Diese Seite richtet sich an Website-Ersteller, die entscheiden, wie ihre Website aussehen wird. In Exponential ist ein
**Design** ein Verzeichnis mit Templates, Stylesheets, Bildern und Skripten; ein **Theme** ist ein als Erweiterung
verpacktes Design. Sie können ein Theme unverändert verwenden, es anpassen oder ein eigenes bauen. Diese Seite
vergleicht die drei Wege und zeigt, wie ein Siteaccess auf ein Design gerichtet wird.

## Inhalt

1. [Drei Wege](#drei-wege)
2. [Die mitgelieferten Themes](#die-mitgelieferten-themes)
3. [Ein Theme sicher anpassen](#ein-theme-sicher-anpassen)
4. [Ihr eigenes Design](#ihr-eigenes-design)
5. [Einen Siteaccess auf ein Design richten](#einen-siteaccess-auf-ein-design-richten)
6. [Checkliste für die Entscheidung](#checkliste-für-die-entscheidung)

## Drei Wege

| Weg | Arbeit | Upgrades | Wählen Sie ihn, wenn |
|---|---|---|---|
| Ein mitgeliefertes Theme unverändert verwenden | am wenigsten: nur Einstellungen | Sie erhalten die Updates des Themes mit Composer | Aussehen und Klassen des Themes passen |
| Ein Theme in Ihrer eigenen Erweiterung anpassen | etwas: Overrides der Templates, die Sie ändern | das Theme wird darunter aktualisiert; Ihre Overrides bleiben | Sie die Struktur des Themes mit Ihrer Marke wollen |
| Ein eigenes Design bauen | am meisten: jedes Template, das Sie brauchen | nur Ihr eigener Code | das Aussehen speziell ist oder Sie auf dem Layouts-Editor aufbauen |

## Die mitgelieferten Themes

| Theme (Erweiterung) | Design-Name | Was es ist | Lesen |
|---|---|---|---|
| `sevenx_themes_simple` | `simple` | das helle, responsive öffentliche Design der Standardinstallation; Kopfbereich mit mehrstufigem Menü und Warenkorb, Templates für Artikel, Blog, Galerie, Produkt und Warenkorb | [sevenx_themes_simple](../../../../eng-us/features/6.0/extensions/sevenx_themes_simple.md) |
| `ezwebin` | `ezwebin` | das klassische Website Interface: Seiten für Artikel, Blogs, Veranstaltungen, Foren, Galerien, Produkte, RSS und Shop | [ezwebin](../../../../eng-us/features/6.0/extensions/ezwebin.md) |
| `ezdemo` | `ezdemo` | das Design mit Demo-Inhalten | [ezdemo](../../../../eng-us/features/6.0/extensions/ezdemo.md) |
| `sevenx_themes_media` | `media` | das Design der Media-Site, das mit dem Layouts-Editor verwendet wird | [Layouts](../../../bc/6.0/layouts.md) |
| `ezflow` | `ezflow` | blockbasierte Seiten der klassischen Erweiterung flow | [ezflow](../../../../eng-us/features/6.0/extensions/ezflow.md) |

`sevenx_themes_simple` und `ezwebin` teilen sich die ezwebin-Klassen, sodass Inhalte ohne Änderungen zwischen ihnen
wechseln können.

## Ein Theme sicher anpassen

Ein von Composer installiertes Theme wird bei jedem Update ersetzt. Bearbeiten Sie seine Dateien niemals an Ort und
Stelle ([sevenx_themes_simple, „Use it“](../../../../eng-us/features/6.0/extensions/sevenx_themes_simple.md#use-it)). Stattdessen:

1. Legen Sie Ihre eigene Design-Erweiterung an, zum Beispiel `extension/mysite_design/` mit
   `design/simple/templates/` (derselbe Design-Name wie das Theme) und `settings/design.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?
   [ExtensionSettings]
   DesignExtensions[]=mysite_design
   */ ?>
   ```

2. Aktivieren Sie sie vor dem Theme, damit ihre Dateien zuerst gefunden werden:

   ```ini
   # settings/override/site.ini.append.php
   [ExtensionSettings]
   ActiveAccessExtensions[]=mysite_design
   ActiveAccessExtensions[]=sevenx_themes_simple
   ```

3. Kopieren Sie nur die Templates, die Sie ändern, in Ihre Erweiterung, behalten Sie ihre Pfade bei und ändern Sie sie
   dort.
4. Fügen Sie Ihr Stylesheet zu `[StylesheetSettings] CSSFileList[]` in der `design.ini.append.php` Ihrer Erweiterung
   hinzu ([Stylesheets und Skripte](stylesheets-und-skripte.md)).
5. Leeren Sie die Caches für neue Template-Pfade
   (`php bin/php/ezcache.php --clear-id=template-override --allow-root-user` und `--clear-tag=template`).

Warum das funktioniert: Designs werden in der Reihenfolge der Erweiterungen durchsucht, und die erste gefundene Datei
gewinnt ([Designs und die Fallback-Kette](designs-und-die-fallback-kette.md),
[Ladereihenfolge der Erweiterungen](../../../../eng-us/features/6.0/extension-loading-order.md)).

## Ihr eigenes Design

Beginnen Sie mit einem leeren Design und lassen Sie das Design `standard` alles beantworten, was Sie noch nicht
geschrieben haben:

```text
extension/mysite_design/
├── design/mysite/
│   ├── templates/pagelayout.tpl        the frame of every page
│   ├── override/templates/full/        templates per class, chosen by override rules
│   ├── stylesheets/
│   ├── images/
│   └── javascript/
└── settings/
    ├── design.ini.append.php           DesignExtensions[]=mysite_design, CSSFileList[], JavaScriptList[]
    └── siteaccess/<site>/override.ini.append.php
```

Schreiben Sie zuerst das Pagelayout ([Das Pagelayout](../../../../eng-us/help/creators/website-creators/the-pagelayout.md)), dann pro Klasse ein Full- und ein
Line-Template ([Template-Overrides](../../../../eng-us/help/creators/website-creators/template-overrides.md)).

## Einen Siteaccess auf ein Design richten

```ini
# settings/siteaccess/<site>/site.ini.append.php
[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=simple
```

`SiteDesign` wird zuerst durchsucht, dann jeder Eintrag in `AdditionalSiteDesignList[]`, dann `standard`
(`StandardDesign` in `settings/site.ini`). Prüfen Sie, was gerade gilt:

```bash
php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>
```

## Checkliste für die Entscheidung

- [ ] Passen die Klassen des mitgelieferten Themes zum Inhaltsmodell? Falls nicht, planen Sie eigene Templates.
- [ ] Wird die Schreibrichtung von rechts nach links gebraucht? Prüfen Sie die Stylesheets des Themes
  ([Websites mit Schreibrichtung von rechts nach links](websites-von-rechts-nach-links.md)).
- [ ] Erfüllt das Theme Ihr Ziel für Barrierefreiheit?
  ([Barrierefreiheit für Website-Ersteller](barrierefreiheit-fuer-website-ersteller.md))
- [ ] Wer aktualisiert das Theme, und wie testen Sie ein Update, bevor es live geht?
- [ ] Liegt das Design in Git, in Ihrer eigenen Erweiterung, nicht in `design/` der Installation?

## Verwandte Seiten

- [Seitenpakete und die mitgelieferten Themes](seitenpakete-und-mitgelieferte-themes.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Ihre Website markengerecht gestalten](ihre-website-markengerecht-gestalten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
