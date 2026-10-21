---
title: Designs und Siteaccesses
audience: designer, admin
category: theme-design
order: 20
summary: Wie eine Anfrage ihren Siteaccess erreicht (über Pfad, Host oder Port), wo ein Siteaccess seine Designs nennt und wie mehrere Websites einer Installation ihr Aussehen teilen oder trennen.
keywords: Siteaccess, Design pro Website, SiteDesign, AdditionalSiteDesignList, MatchOrder, URIMatchType, HostMatchType, Host, Pfadpräfix, Multisite, Vorschau-Website, siteaccess, site access, design per site, host, path prefix, multisite, preview site
type: help
---
# Designs und Siteaccesses

Diese Seite ist für Designer, die wissen müssen, mit welchem Design eine Seite gezeichnet wird und wo sich das ändern lässt. Sie
beantwortet: was ein Siteaccess ist, wie eine Anfrage einem zugeordnet wird, wo ein Siteaccess seine Designs nennt und wie zwei
Websites ein Design teilen oder zwei verwenden können.

## Inhalt

1. [Was ein Siteaccess ist](#was-ein-siteaccess-ist)
2. [Wie eine Anfrage ihren Siteaccess findet](#wie-eine-anfrage-ihren-siteaccess-findet)
3. [Wo ein Siteaccess seine Designs nennt](#wo-ein-siteaccess-seine-designs-nennt)
4. [Die Siteaccesses dieser Installation](#die-siteaccesses-dieser-installation)
5. [Einstellungen zwischen Siteaccesses teilen](#einstellungen-zwischen-siteaccesses-teilen)
6. [Welches Design gerade verwendet wird](#welches-design-gerade-verwendet-wird)
7. [Häufige Fragen](#häufige-fragen)

## Was ein Siteaccess ist

Ein Siteaccess ist ein Zugang zu einer Installation. Er hat einen Namen (einen Ordner `settings/siteaccess/<name>/`) und eigene
Einstellungen: welches Design, welche Sprachen, welcher Startknoten, ob Besucher sich anmelden müssen. Eine Installation hat meist
mindestens zwei: eine öffentliche Website und die Administration. Beide lesen dieselbe Inhaltsdatenbank; sie sehen verschieden aus,
weil sie verschiedene Designs verwenden.

Ein Siteaccess ist aktiv, wenn er in `site.ini [SiteAccessSettings] AvailableSiteAccessList[]` steht (in
`settings/override/site.ini.append.php` der Installation, die ein Administrator pflegt).

## Wie eine Anfrage ihren Siteaccess findet

`site.ini [SiteAccessSettings] MatchOrder` listet die Methoden, die der Reihe nach versucht werden. Der mitgelieferte Standard
(`settings/site.ini`) ist:

```ini
MatchOrder=uri;host;port
URIMatchType=element
URIMatchElement=1
```

- **uri**: Mit `URIMatchType=element` und `URIMatchElement=1` ist das erste Pfadelement der Name des Siteaccess:
  `https://example.com/eng/news` ist der Siteaccess `eng`. Andere Typen sind `map`, `text` und `regexp`.
- **host**: `HostMatchType=map` mit `HostMatchMapItems[]=<host>;<siteaccess>` ordnet Hostnamen zu. Seit 6.0 lässt sich pro Eintrag
  eine Methode angeben (`strict`, `start`, `end`, `part`), siehe
  [Siteaccess über ein Host-Präfix](../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md).
- **port**: Eine Portnummer wird einem Siteaccess zugeordnet.

Wenn nichts passt, wird `site.ini [SiteSettings] DefaultAccess` verwendet.

Die Zuordnung ist Aufgabe eines Administrators, aber Sie müssen sie kennen, um ein Design in der Vorschau anzusehen: Ein neuer
Siteaccess für Ihr Design ist über sein Pfadelement (`/mytheme/`) erreichbar, sobald er aufgeführt ist und die Methode uri in
`MatchOrder` steht.

## Wo ein Siteaccess seine Designs nennt

In `settings/siteaccess/<name>/site.ini.append.php`:

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

- `SiteDesign` wird zuerst durchsucht.
- `AdditionalSiteDesignList[]` wird danach durchsucht, in der aufgeführten Reihenfolge. Die leere Zeile `AdditionalSiteDesignList[]`
  leert zuerst, was eine tiefer liegende Einstellungsdatei gesetzt hat.
- `StandardDesign` (Standard `standard`, aus `settings/site.ini`) wird zuletzt durchsucht.

Eine Theme-Erweiterung kann diese Zeilen auch für einen Siteaccess mit bekanntem Namen mitliefern, in
`extension/<extension>/settings/siteaccess/<siteaccess>/site.ini.append.php`. Das Theme media tut das für `site` und `bold`; das Theme
starter liefert sie für `cjw_starter` mit.

## Die Siteaccesses dieser Installation

Die Designs unten sind aus `settings/siteaccess/*/site.ini.append.php` dieser Installation gelesen (nur die Design-Zeilen):

| Siteaccess | SiteDesign | Danach durchsucht |
|---|---|---|
| `site` | `media` | `ezwebin`, `standard`, `base` |
| `bold` | `media` | `simple`, `ezwebin`, `standard`, `base` |
| `starter` | `starter` | `standard` |
| `portaljq` | `expportal_jquery` | `standard`, `base` |
| `admin` | `admin4l` | `admin4`, `admin3`, `admin2`, `admin` |
| `editor` | `editor` | `admin4l`, `admin4`, `admin3`, `admin2`, `admin` |
| `adminui` | `adminui` | `admin4l`, `admin4`, `admin3`, `admin2`, `admin` |
| `classic` | `admin` | (keine) |

Zwei Dinge lassen sich aus dieser Tabelle lernen:

- **Ein Design kann mehreren Siteaccesses dienen.** `site` und `bold` verwenden beide `media`; nur ihre Rückfalllisten unterscheiden
  sich.
- **Auch ein Administrationsdesign ist ein Stapel.** `admin4l` ist ein kleines Design auf `admin4`, das auf `admin3` sitzt und so
  weiter. Ein neues Aussehen der Administration lässt sich genauso bauen (siehe [Admin-Designs im Überblick](admin-designs-ueberblick.md)).

## Einstellungen zwischen Siteaccesses teilen

Ein Übersetzungs-Siteaccess (zum Beispiel `starter_ger` neben `starter`) soll genau wie sein Original aussehen. Statt jede Datei zu
kopieren, nennt `site.ini [SiteAccessSettings] ExtensionSettingsSiteAccess` einen anderen Siteaccess, dessen
Erweiterungseinstellungen er ebenfalls liest. `settings/siteaccess/starter/site.ini.append.php` setzt:

```ini
[SiteAccessSettings]
ExtensionSettingsSiteAccess=cjw_starter
```

sodass die Einstellungen des Themes starter in `extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/` (seine Overrides,
seine Design-Zeilen) für `starter` und `starter_ger` gleichermaßen gelten. Die Einstellung wird nur aus der eigenen
`site.ini.append.php` des Siteaccess gelesen (Kommentar in `settings/site.ini`).

## Welches Design gerade verwendet wird

- In einem Template enthält `$site.design` das `SiteDesign` des aktuellen Siteaccess (vom Kernel in
  `kernel/private/classes/ezpkernelweb.php` gesetzt).
- Mit `site.ini [TemplateSettings] ShowTemplatePathComments=enabled` schreibt jedes Template einen HTML-Kommentar mit seinem Dateipfad,
  sodass der Seitenquelltext zeigt, aus welchem Design jeder Teil stammt
  ([Kommentare mit Template-Pfaden](../../../funktionen/6.0/template-pfad-kommentare.md)).
- Mit `ShowUsedTemplates=enabled` (Debug-Ausgabe eingeschaltet) listet der Debug-Bereich jedes verwendete Template.

## Häufige Fragen

**Ich habe SiteDesign geändert, aber nichts ist passiert.** Der INI-Cache hat noch den alten Wert. Leeren Sie ihn mit
`php bin/php/ezcache.php --clear-tag=ini` (das Tag deckt auch den globalen INI-Cache ab) und den Template-Override-Cache mit
`--clear-id=template-override`.

**Kann ein angemeldeter Benutzer das Design wechseln?** In der Administration ja: Der Oberflächenschalter lässt einen Benutzer ein
anderes Admin-Design wählen ([Oberflächenschalter: Design](../../../funktionen/6.0/ui-umschalter-design.md)).

**Können zwei Websites Templates teilen, sich aber in der Farbe unterscheiden?** Ja: Legen Sie die gemeinsamen Templates in ein
Design und geben Sie jedem Siteaccess ein kleines eigenes Design mit nur seinem Stylesheet als `SiteDesign` und das gemeinsame Design
in `AdditionalSiteDesignList[]` (siehe [Designs in einer Multisite-Installation](designs-in-einer-multisite-installation.md)).

## Verwandte Seiten

- [Was ein Design ist](was-ein-design-ist.md)
- [Die Design-Kaskade](die-design-kaskade.md)
- [Ein Design auf einem Siteaccess in der Vorschau ansehen](design-vorschau-auf-einem-siteaccess.md)
- [INI-Overrides für mehrere Websites](../../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
