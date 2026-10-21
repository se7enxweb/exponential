---
title: Ihre eigene Design-Erweiterung
audience: designer, developer, website-creator
category: build-two-ways
order: 500
summary: Eine kleine Design-Erweiterung (hier „mysite“) neben einem Theme einrichten, sie in der richtigen Reihenfolge aktivieren und jedes Template des Themes oder von Layouts überschreiben, ohne deren Dateien anzufassen. Jeder Schritt auf einer Kopie der Site Fit & Healthy getestet.
keywords: Design-Erweiterung, Template-Override, mysite, DesignExtensions, ActiveExtensions, Reihenfolge der Erweiterungen, design.ini, Theme, sevenx_themes_media, Templates überschreiben, Cache leeren, design extension, template override, extension order, theme, override templates, clear cache
type: help
---
# Ihre eigene Design-Erweiterung

Der Weg über Templates beginnt mit einer eigenen Erweiterung. Sie enthält Ihre Templates, Einstellungen und Stylesheets,
liegt in einem eigenen git-Repository und ändert die Site, ohne das Theme (`sevenx_themes_media`) oder die
Layouts-Erweiterungen zu bearbeiten; deren Updates überschreiben Ihre Arbeit also nie. Diese Seiten nennen sie `mysite`.

## Inhalt

1. [Die Dateien](#die-dateien)
2. [Sie aktivieren, an der richtigen Stelle](#sie-aktivieren-an-der-richtigen-stelle)
3. [Ein Template überschreiben](#ein-template-überschreiben)
4. [Die Caches leeren](#die-caches-leeren)
5. [Prüfen, welche Datei verwendet wird](#prüfen-welche-datei-verwendet-wird)

## Die Dateien

```text
extension/mysite/
    settings/
        design.ini.append.php                      registers the design extension
        menu.ini.append.php, explayouts.ini.append.php ...   your settings
    design/
        media/                                     the design the site uses (SiteDesign of the siteaccess)
            templates/                             same paths as the templates you replace
            stylesheets/                           your CSS
```

`settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite

*/ ?>
```

Der Ordner unter `design/` muss das Design sein, das der Siteaccess verwendet: `media` für die Site Fit & Healthy.
Templates in `design/standard/` würden für jedes Design gefunden, nach dem eigenen Design der Site.

## Sie aktivieren, an der richtigen Stelle

Fügen Sie `mysite` zu `[ExtensionSettings] ActiveExtensions[]` der `site.ini` hinzu (Setup > INI-Einstellungen oder
`./console exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] mysite global`). **Die Position zählt.** Was auf der
Testkopie gemessen wurde:

| `mysite` in `ActiveExtensions[]` | wirksame `DesignExtensions[]` | Ergebnis |
|---|---|---|
| am Ende angehängt (was `exp:ini add` macht) | `mysite` vor `sevenx_themes_media` aufgeführt | die Templates des Themes gewinnen; der Override wird ignoriert |
| vor `sevenx_themes_media` gesetzt | `mysite` nach dem Theme aufgeführt | Ihre Templates gewinnen |
| an die erste Stelle gesetzt | `mysite` als letzte aufgeführt | Ihre Templates gewinnen, und Ihre `explayouts.ini` wird nach der von Layouts gelesen (nötig für neue Blocktypen) |

Die Regel dahinter: Die Erweiterungen werden in umgekehrter Reihenfolge von `ActiveExtensions[]` gelesen, und in
`DesignExtensions[]` gewinnt die zuletzt aufgeführte. Eine Erweiterung, die **früher** in `ActiveExtensions[]` steht,
gewinnt also gegen die nach ihr. `exp:ini add` hängt an; um die Zeile an den Anfang zu setzen, bearbeiten Sie die Liste
unter **Setup > INI-Einstellungen** (ein `=name` pro Zeile, in Reihenfolge) oder in der Datei.

## Ein Template überschreiben

Kopieren Sie das Template, das Sie ändern wollen, aus dem Theme an denselben Pfad in Ihrer Erweiterung und ändern Sie dann
Ihre Kopie. Für das Kopfmenü zum Beispiel:

```text
extension/sevenx_themes_media/design/media/templates/pagelayout/header/main_menu.tpl     (the theme's)
extension/mysite/design/media/templates/pagelayout/header/main_menu.tpl                  (yours, wins)
```

Dasselbe funktioniert für jedes Block-Template von Layouts; zum Beispiel ersetzt
`extension/mysite/design/media/templates/explayouts/block/list/grid.tpl` die Rasteransicht des List-Blocks
([Wie ein Block seinen Templates zugeordnet wird](zuordnung-block-zu-template.md)).

## Die Caches leeren

Eine neue Template-Datei braucht eine neu aufgebaute Override-Liste: Leeren Sie **Template & Inhalt**
(`./console exp:cache clear --tag=template,content`). Leeren Sie nach einer Änderung von `ActiveExtensions[]` auch die
INI-Caches (`--tag=template,content,ini`).

## Prüfen, welche Datei verwendet wird

```text
./console exp:ini get design.ini/ExtensionSettings/DesignExtensions -s site     # mysite must come after the theme
```

Im HTML der Seite erscheint Ihr Markup. Die **Kurzeinstellungen** der Administration (rechte Spalte) haben
**Liste der verwendeten Templates** und **Template-Debug** für einen genaueren Blick.

## Verwandte Seiten

- [Ein Kopfmenü, das dem Baum folgt](kopfmenue-aus-dem-baum.md)
- [Einen Ansichtstyp hinzufügen](ansichtstyp-hinzufuegen.md)
- [Einen Blocktyp hinzufügen](blocktyp-hinzufuegen.md)
- [Eigene Template-Blöcke](eigene-template-bloecke.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
