---
title: Ein Ausgangs-Theme wählen
audience: designer, project-manager
category: theme-design
order: 80
summary: Ob Sie ein neues Design von Grund auf beginnen, auf dem Theme simple, media oder starter oder auf ezwebin, mit den Vor- und Nachteilen jedes Wegs und einer kurzen Entscheidungstabelle.
keywords: Ausgangs-Theme, Basis-Theme, welches Theme, von Grund auf, Theme simple, Theme media, Theme starter, ezwebin, Design wählen, neues Aussehen der Website, Theme-Entscheidung, starting theme, base theme, which theme, from scratch, simple theme, media theme, starter theme, choose design, new site look, theme decision
type: help
---
# Ein Ausgangs-Theme wählen

Diese Seite ist für Designer und Projektleiter am Beginn eines Website-Projekts. Sie beantwortet: Soll das neue Aussehen aus dem
Nichts entstehen oder auf einem Design, das diese Installation schon hat, und auf welchem.

## Inhalt

1. [Die vier Wege zu beginnen](#die-vier-wege-zu-beginnen)
2. [Entscheidungstabelle](#entscheidungstabelle)
3. [Von Grund auf beginnen](#von-grund-auf-beginnen)
4. [Auf simple oder ezwebin aufbauen](#auf-simple-oder-ezwebin-aufbauen)
5. [Mit media oder starter beginnen](#mit-media-oder-starter-beginnen)
6. [Was Sie auch wählen](#was-sie-auch-wählen)

## Die vier Wege zu beginnen

1. **Von Grund auf**: Ihr eigenes Design mit eigenem Pagelayout, das nur auf `standard` (und `base`) zurückfällt.
2. **Auf einem klassischen Design**: `simple` (Erweiterung `sevenx_themes_simple`) oder `ezwebin` in Ihrer
   `AdditionalSiteDesignList[]`, sodass Sie deren Inhaltsansichten erben und nur ändern, was Sie brauchen.
3. **Auf einem Design, das auf Layouts beruht**: `media` (Erweiterung `sevenx_themes_media`) oder `starter` (Erweiterung
   `cjw_themes_jumper`), die Seiten mit den Zonen und Blöcken des Layout-Editors zeichnen.
4. **Ein Fork**: Kopieren Sie eine Theme-Erweiterung unter neuem Namen und ändern Sie sie frei (behandelt in den Seiten zum
   Lebenszyklus in dieser Hilfe).

## Entscheidungstabelle

| Wenn Ihr Projekt ... | Beginnen Sie |
|---|---|
| sein eigenes vollständiges HTML/CSS von einer Agentur oder aus einem Styleguide hat | von Grund auf ([Tutorial: ein statisches HTML-Design portieren](tutorial-ein-statisches-html-design-portieren.md)) |
| die Inhaltsklassen der Standardinstallation verwendet (Artikel, Blog, Ordner, Produkt ...) und schnell ein Ergebnis möchte | auf `simple` |
| Seiten braucht, die Redakteure aus Blöcken zusammenstellen (Landingpages, Kampagnen) | auf `media` oder `starter` oder von Grund auf mit den Layout-Templates ([Layouts für Designer](layouts-fuer-designer.md)) |
| wie eine bestehende Website aussehen muss, die ezwebin verwendet | auf `ezwebin` |
| eine kleine Broschüren-Website ist | von Grund auf: ein Pagelayout, ein Stylesheet, ein paar Overrides |

## Von Grund auf beginnen

**Was Sie bekommen:** volle Kontrolle, kein ungenutztes CSS oder JavaScript, Markup, das Sie Zeile für Zeile kennen.

**Was es kostet:** Sie schreiben das Pagelayout, das Menü, den Brotkrumenpfad und die Voll- und line-Ansichten jeder Klasse, die
die Website zeigt. Alles andere fällt auf `design/standard` zurück, dessen Markup schlicht und ungestaltet ist.

**Einstellungen:**

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard
```

So macht es der Siteaccess `starter` dieser Installation (sein Design `starter` mit nur `standard` darunter).

## Auf simple oder ezwebin aufbauen

**Was Sie bekommen:** funktionierende Templates für die Klassenfamilie von ezwebin (Artikel, Blogs, Galerien, Veranstaltungen,
Produkte, Foren), einen Kopf- und Fußbereich, Blättern, Suchseiten.

**Was es kostet:** Ihr Markup und ihre Klassennamen kommen mit; Ihr CSS gestaltet deren HTML. Ihre Stylesheets werden über
`CSSFileList[]` geladen; lassen Sie sie weg, indem Sie Ihrem Design ein eigenes Template für den Seitenkopf geben.

**Einstellungen:**

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=simple
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

Kopieren Sie nur die Templates, die Sie ändern möchten, an denselben Pfad in `mytheme`.

## Mit media oder starter beginnen

**Was Sie bekommen:** vollständige, moderne Designs mit vielen Ansichtstemplates, den Block-Templates des Layout-Editors und
einem getesteten responsiven und barrierefreien Rahmen.

**Was es kostet:** Sie sind groß (das Design media hat nach seinem eigenen README mehrere hundert Templates und etwa 166
Override-Regeln) und hängen von ihren Inhaltsklassen ab (den Klassen `ng_*` der Media-Site-Daten). Sie passen zu Websites, die
dieses Inhaltsmodell verwenden.

## Was Sie auch wählen

- [ ] Halten Sie Ihr Design in einer eigenen Erweiterung ([Design-Erweiterungen](design-erweiterungen.md)).
- [ ] Bearbeiten Sie nie die Dateien eines mitgelieferten oder von Composer verwalteten Themes; überschreiben Sie sie
      stattdessen aus Ihrem Design.
- [ ] Planen Sie dunklen Modus, rechts nach links und Barrierefreiheit ab dem ersten Stylesheet, nicht am Ende
      ([Design-Token](design-tokens.md), [Designs für rechts nach links](rechts-nach-links-designs.md),
      [Barrierefreiheit: Grundlagen für Themes](barrierefreiheit-grundlagen-fuer-themes.md)).
- [ ] Entscheiden Sie früh, ob Seiten mit dem Layout-Editor zusammengestellt werden; das ändert das Pagelayout.

## Verwandte Seiten

- [Die Themes auf dieser Installation](themes-die-mit-exponential-kommen.md)
- [Tutorial: eine Theme-Erweiterung anlegen](tutorial-eine-theme-erweiterung-anlegen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
