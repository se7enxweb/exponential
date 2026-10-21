---
title: Was ein Design ist
audience: designer
category: theme-design
order: 10
summary: Die Grundidee des Designsystems von Exponential: Ein Design ist ein benannter Ordner mit Templates, Stylesheets, Skripten und Bildern; ein Siteaccess wählt Designs; der Kernel sucht jede Datei über eine Rückfallkette.
keywords: Design, Theme, Skin, Aussehen, Template-Satz, Design-Ordner, Siteaccess, Rückfall, Design standard, Design base, was ist ein Design, design, theme, skin, look, template set, design folder, fallback, standard design, base design, what is a design
type: help
---
# Was ein Design ist

Diese Seite ist für Designer, die neu bei Exponential sind und die eine Idee suchen, auf der alles andere aufbaut. Sie beantwortet: was
Exponential ein Design nennt, was darin steckt, wie eine Website ihre Designs wählt und wie ein Design mit den Wörtern „Theme“ und „Template“
zusammenhängt, die Sie vielleicht aus anderen Systemen kennen.

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Was ein Design enthält](#was-ein-design-enthält)
3. [Wo Designs liegen](#wo-designs-liegen)
4. [Wie eine Website ihre Designs wählt](#wie-eine-website-ihre-designs-wählt)
5. [Design, Theme, Template: die Wörter](#design-theme-template-die-wörter)
6. [Was ein Design nicht enthält](#was-ein-design-nicht-enthält)
7. [Nächste Schritte](#nächste-schritte)

## In einem Absatz

Ein **Design** ist ein Ordner mit einem Namen, zum Beispiel `media`, `starter` oder `admin4`. Darin liegen die Templates, die das HTML jeder
Seite erzeugen, und die Stylesheets, Skripte, Schriften und Bilder, die diese Seiten verwenden. Ein **Siteaccess** (eine „Website“ einer
Installation: ein Hostname, ein Pfadpräfix oder ein Port) sagt, welches Design er zeigt. Braucht der Kernel eine Datei, etwa `pagelayout.tpl`
oder `stylesheets/main.css`, sucht er nicht nur in einem Ordner: Er geht eine Liste von Designs durch, die **Design-Kaskade**, und nimmt die
erste Kopie, die er findet. Deshalb kann ein neues Design klein sein: Es enthält nur die Dateien, die sich von den Designs darunter
unterscheiden.

## Was ein Design enthält

Jeder Design-Ordner folgt demselben Aufbau. Die Ordner sind optional; ein Design hat nur, was es braucht.

| Ordner | Was hineingehört | Gelesen von |
|---|---|---|
| `templates/` | TPL-Templates: `pagelayout.tpl`, `node/view/full.tpl`, `content/datatype/view/ezstring.tpl` ... | der Template-Engine (`eZTemplateDesignResource`) |
| `override/templates/` | Templates, die ein anderes Template nur unter Bedingungen ersetzen (`override.ini`) | dem Override-System |
| `stylesheets/` | CSS-Dateien | den Operatoren `ezcss*` von ezjscore und dem Operator `ezdesign` |
| `javascript/` | JavaScript-Dateien | den Operatoren `ezscript*` von ezjscore |
| `images/` | Bilder des Designs selbst (Logos, Symbole, Hintergründe) | den Operatoren `ezimage` und `ezdesign` |
| `fonts/` | Webfonts | Ihren Stylesheets (`url()` in CSS) |

Die vollständige Beschreibung mit jedem Ordner, den der Kernel kennt, steht in [Der Aufbau des Design-Verzeichnisses](aufbau-des-design-verzeichnisses.md).

## Wo Designs liegen

Designs kommen von zwei Stellen:

- **Der Ordner `design/` des Kernels**, zum Beispiel `design/standard`, `design/base`, `design/admin4`, `design/admin4l`, `design/editor`.
- **Erweiterungen**, in `extension/<extension>/design/<design>/`. Eine Erweiterung sagt mit `DesignExtensions[]` in ihrer
  `settings/design.ini.append.php`, dass sie Designs trägt. Die Theme-Erweiterungen dieser Installation tun genau das:
  `extension/sevenx_themes_media/design/media`, `extension/sevenx_themes_simple/design/simple`,
  `extension/cjw_themes_jumper/design/starter`, `extension/sevenx_themes_super/design/sevenx_themes_super`.

Ein Design desselben Namens kann an mehreren Stellen gleichzeitig existieren: zum Beispiel `design/standard` im Kernel und
`extension/explayouts/design/standard` in einer Erweiterung. Der Kernel behandelt sie als ein Design und durchsucht zuerst die Kopien in
Erweiterungen (siehe [Die Design-Kaskade](die-design-kaskade.md)).

## Wie eine Website ihre Designs wählt

Drei Einstellungen in `site.ini [DesignSettings]` entscheiden es (Standardwerte in `settings/site.ini`):

```ini
[DesignSettings]
# The standard design, used as fallback design if the element is not in the sitedesign
StandardDesign=standard
# The design of the site
SiteDesign=admin
# Designs searched after SiteDesign and before StandardDesign
AdditionalSiteDesignList[]
```

Jeder Siteaccess setzt eigene Werte in `settings/siteaccess/<siteaccess>/site.ini.append.php`. Auf dieser Installation verwendet der
öffentliche Siteaccess `site`:

```ini
[DesignSettings]
SiteDesign=media
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

Ein Template wird also in `media` gesucht, dann in `ezwebin`, dann in `standard`, dann in `base`.

## Design, Theme, Template: die Wörter

| Wort | Bedeutung in Exponential |
|---|---|
| **Design** | Ein benannter Ordner mit Templates und Assets, wie oben. Das eigene Wort des Kernels. |
| **Theme** | Umgangssprachlich ein Design, das einer Website ihr Aussehen gibt, meist als Erweiterung ausgeliefert (die Erweiterungen `sevenx_themes_*`, `cjw_themes_jumper`). Eine Theme-Erweiterung kann mehr als ein Design tragen und auch Einstellungen, Übersetzungen und Klassen. |
| **Template** | Eine `.tpl`-Datei in einem Design, geschrieben in der Template-Sprache (TPL). |
| **Pagelayout** | Das äußerste Template, `pagelayout.tpl`, das den Rahmen jeder Seite zeichnet und das Ergebnis des Moduls hineinsetzt. |
| **Override** | Ein Template, das ein anderes unter Bedingungen ersetzt, konfiguriert in `override.ini`. |

## Was ein Design nicht enthält

- **Inhalte.** Seiten, Artikel, von Redakteuren hochgeladene Bilder liegen im Inhaltsspeicher, nicht im Design.
- **Inhaltsstruktur.** Inhaltsklassen (die Felder eines Artikels) werden in der Administration definiert. Ein Design entscheidet nur, wie sie
  aussehen. Braucht Ihr Design ein neues Feld, fragen Sie den Entwickler, der die Klassen pflegt.
- **Einstellungen der ganzen Installation.** Eine Theme-Erweiterung kann Einstellungen mitliefern (`settings/*.ini.append.php`), aber der
  Design-Ordner selbst enthält keine INI-Dateien.
- **Geschäftslogik.** Template-Operatoren und fetch-Funktionen geben Templates ihre Daten; neue sind PHP-Code in einer Erweiterung (siehe
  die Seiten zu Erweiterungen in dieser Hilfe).

## Nächste Schritte

- [Designs und Siteaccesses](designs-und-siteaccesses.md): wie ein Siteaccess gewählt wird und wie er seine Designs nennt.
- [Die Design-Kaskade](die-design-kaskade.md): die genaue Suchreihenfolge.
- [Das Pagelayout](das-pagelayout.md): das Template, das jede Seite einrahmt.
- [Templates im Überblick](../vorlagen/templates-ueberblick.md): die Template-Sprache.

## Verwandte Seiten

- [Designer: hier beginnen](../designer-hier-beginnen.md)
- [Design-Erweiterungen](design-erweiterungen.md)
- [Design-Glossar](design-glossar.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
