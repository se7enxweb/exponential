---
title: Themes und Siteaccesses
audience: designer, developer
category: extension-development
order: 50
summary: Welche Website welches Theme verwendet - SiteDesign, AdditionalSiteDesignList und StandardDesign je Siteaccess, Themes, die auf anderen Themes aufbauen, ein Theme für mehrere Websites und wie Sie die Kette sehen, die ein Siteaccess tatsächlich verwendet.
keywords: Siteaccess, Design-Kette, Theme-Vererbung, Multisite, Variante, siteaccess, SiteDesign, AdditionalSiteDesignList, StandardDesign, design chain, theme inheritance, multisite, ActiveAccessExtensions, DesignExtensions
type: help
---
# Themes und Siteaccesses

Teil 6, Kapitel 5. Ein Theme bewirkt nichts, solange kein Siteaccess es verwendet. Dieses Kapitel zeigt die drei
Einstellungen, die entscheiden, welche Designs ein Siteaccess durchsucht, wie Sie ein Theme auf einem anderen aufbauen
und wie Sie ein Theme mit kleinen Unterschieden für mehrere Websites verwenden.

## Die drei Einstellungen

In `site.ini [DesignSettings]`, je Siteaccess:

| Einstellung | Durchsucht | Typischer Wert |
|---|---|---|
| `SiteDesign` | zuerst | das Design Ihres Themes: `mytheme` |
| `AdditionalSiteDesignList[]` | danach, der Reihe nach | die Designs, auf denen Ihr Theme aufbaut: `base` |
| `StandardDesign` | zuletzt | `standard` (der Standard des Kernels) |

Für jeden Design-Namen werden zuerst die Ordner der Design-Erweiterungen in der Reihenfolge der Erweiterungen
durchsucht, dann das eigene `design/<name>/` des Kernels. Die vollständige Regel und eine Möglichkeit, die Kette eines
Siteaccess auszugeben, stehen in [Die Design-Kette](../teil-2-builder/die-design-kette.md).

## Zwei Schalter, die beide eingeschaltet sein müssen

Ein Theme wird verwendet, wenn:

1. **die Erweiterung für den Siteaccess aktiv ist**: in `site.ini [ExtensionSettings] ActiveExtensions[]` (alle
   Siteaccesses) oder im `ActiveAccessExtensions[]` des Siteaccess; und
2. **der Siteaccess ihr Design** in einer der drei Einstellungen oben **nennt**.

`DesignExtensions[]` in der eigenen `design.ini` des Themes macht nur seinen Ordner durchsuchbar. Den zweiten Schalter
zu vergessen ist der häufigste Grund, warum ein neues Theme „nichts tut“; auch die vom RAD-Assistenten erzeugte README
liegt hier falsch ([Ein Theme mit dem RAD-Assistenten](ein-theme-mit-dem-rad-assistenten.md)).

## Wohin die Siteaccess-Einstellungen gehören

| Wo | Wann |
|---|---|
| `settings/siteaccess/<name>/site.ini.append.php` der Installation | die Website entscheidet, welches Theme sie verwendet (der übliche Fall) |
| `settings/siteaccess/<name>/` in der Theme-Erweiterung | das Theme ist für einen bekannten Siteaccess-Namen gemacht und bringt dessen Einstellungen mit |
| `./console exp:ini set ...` | von der Kommandozeile, in die Einstellungen der Installation geschrieben ([Der Befehl exp:ini](../teil-2-builder/der-befehl-exp-ini.md)) |

```bash
./console exp:ini set site.ini/DesignSettings/SiteDesign mytheme siteaccess:mysite
./console exp:ini clear 'site.ini/DesignSettings/AdditionalSiteDesignList[]' siteaccess:mysite
./console exp:ini add 'site.ini/DesignSettings/AdditionalSiteDesignList[]' base siteaccess:mysite
```

## Ein Theme auf einem Theme

Weil Designs der Reihe nach durchsucht werden, kann ein Theme auf einem anderen aufbauen: Führen Sie das Design des
anderen Themes in `AdditionalSiteDesignList[]` nach Ihrem auf.

```ini
[DesignSettings]
SiteDesign=mytheme_red
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=mytheme
AdditionalSiteDesignList[]=base
```

`mytheme_red` braucht dann nur die Templates und Stylesheets, die sich unterscheiden: einen Kopfbereich, ein Stylesheet
mit anderen Farben. Alles andere kommt aus `mytheme`. Das ist der sauberste Weg, Varianten zu bauen, und der Weg, ein
Theme anzupassen, das Sie nicht geschrieben haben, ohne es zu forken
([Erweitern, ohne zu forken](../teil-5-erweitern-forken-zurueckgeben/erweitern-ohne-zu-forken.md)).

## Ein Theme, mehrere Websites

Eine Installation hat oft mehrere öffentliche Siteaccesses (Sprachen, Marken). Sie können sich ein Theme teilen:

| Unterscheidet sich je Website | Wie |
|---|---|
| Nur der Inhalt | dasselbe `SiteDesign` für alle |
| Farben oder Logo | ein kleines Design je Website auf dem gemeinsamen, wie oben |
| Einige Einstellungen | Siteaccess-Einstellungen in der eigenen INI-Datei des Themes, in Templates mit `ezini()` gelesen |
| Der Wurzelknoten des Menüs | eine Einstellung, nie eine in ein Template geschriebene Knoten-ID |

## Admin-Siteaccesses

Siteaccesses der Administration verwenden die Admin-Designs (`admin4`, `admin4l`, `admin`). Ein öffentliches Theme
gehört nicht in ihre Kette. Enthält die Erweiterung Ihres Themes auch Templates für die Administration (ein
Bearbeitungs-Template für einen Datentyp, den es hinzufügt), legen Sie sie in `design/standard/` oder `design/admin/`
der Erweiterung, wo jede Kette sie findet, und halten Sie das öffentliche Aussehen im eigenen Design des Themes.

## Das Ergebnis prüfen

1. Das Skript zur Design-Kette aus [Die Design-Kette](../teil-2-builder/die-design-kette.md) gibt die Ordner eines
   Siteaccess der Reihe nach aus.
2. Mit `ShowUsedTemplates=enabled` und eingeschalteter Debug-Ausgabe listet jede Seite die Templates auf, die sie
   verwendet hat, und das Design, aus dem jedes stammt.
3. Leeren Sie nach einer Änderung der Design-Einstellungen den INI- und den Template-Cache; eine neue Template-Datei
   braucht außerdem einen geleerten Template-Override-Cache.

## Verwandte Seiten

- Zurück: [Ein Theme von Hand](ein-theme-von-hand.md)
- Weiter: [Theme-Assets, der Packer und CSP](theme-assets-der-packer-und-csp.md)
- [Siteaccess-Einstellungen](../teil-2-builder/siteaccess-einstellungen.md)
- [Einstellungsebenen und ihre Reihenfolge](../teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
