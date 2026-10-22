---
title: Ein Seitenpaket und ein Theme wählen
audience: individual, website-creator
category: personal-sites
order: 1632
summary: Mit welchem Seitenpaket und Theme eine Privatperson in Exponential beginnen sollte - das Standardpaket sevenx_multisite und seine saubere Variante, das einfachere sevenx_site, die älteren Pakete ezwebin, ezflow und ezdemo, die Themes dahinter einschließlich sevenx_themes_simple und sevenx_themes_media und wie viel Sie ohne Programmierung ändern können.
keywords: Seitenpaket, Theme, Design, sevenx_multisite, sevenx_multisite_clean, sevenx_site, ezwebin, ezflow, ezdemo, plain_site, sevenx_themes_simple, sevenx_themes_media, Aussehen, Markenbild, Templates, Privatperson, site package, design, look and feel, branding
type: help
---
# Ein Seitenpaket und ein Theme wählen

Diese Seite ist für Privatpersonen, die wählen, womit ihre neue Website beginnt. Ein **Seitenpaket** ist der
Ausgangspunkt einer Installation: Inhaltsklassen, ein Design, Inhalte, Rollen und Einstellungen. Ein **Theme** ist
der Designteil, als Erweiterung abgelegt
([Seitenpakete und die mitgelieferten Themes](../../ersteller/website-ersteller/seitenpakete-und-mitgelieferte-themes.md)).

## Inhalt

1. [Die Pakete, denen Sie begegnen](#die-pakete-denen-sie-begegnen)
2. [Voll oder sauber](#voll-oder-sauber)
3. [Die Themes](#die-themes)
4. [Welcher Anfang für welche Website](#welcher-anfang-für-welche-website)
5. [Wie viel Sie ohne Programmierung ändern können](#wie-viel-sie-ohne-programmierung-ändern-können)
6. [Später umentscheiden](#später-umentscheiden)

## Die Pakete, denen Sie begegnen

Der Paketindex dieser Version bietet elf Seitenpakete an; diese sind die wichtigen
([4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)):

| Paket | Was Sie bekommen |
|---|---|
| `sevenx_multisite` | Der Standard: Klassen, Design, Demo-Inhalte, Rollen (Members, Partners) und eine Sektion Restricted |
| `sevenx_multisite_clean` | Dasselbe ohne Demo-Inhalte |
| `sevenx_site`, `sevenx_site_clean` | Die einfachere Website-Oberfläche |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site` (jeweils auch `_clean`), `plain_site` | Ältere Pakete, für darauf gebaute Websites erhalten |

Das Standardpaket installiert die Klassen, auf die sich dieser Bereich stützt: *Blog*, *Blog post*, *Comment*,
*Gallery*, *Image*, *Event*, *Event calendar*, *Poll*, *Feedback form* und die Forenklassen (das Paket
`sevenx_classes`).

## Voll oder sauber

| | Voll | `_clean` |
|---|---|---|
| Klassen, Design, Rollen | Ja | Ja |
| Demo-Artikel und -Bilder | Ja | Nein |
| Verwenden Sie es für | Das Lernen: sehen, wie jede Klasse aussehen soll | Ihre echte Website |

Installieren Sie das volle Paket einmal zum Lernen auf Ihrem eigenen Computer und das saubere für die echte Website.

## Die Themes

| Theme (Erweiterung) | Design | Hinweise |
|---|---|---|
| `sevenx_themes_simple` | `simple` | Von der Standardinstallation verwendet; enthält die Templates für Blog, Kommentare und Galerie ([sevenx_themes_simple](../../../funktionen/6.0/erweiterungen/sevenx_themes_simple.md)) |
| `ezwebin` | `ezwebin` | Die ältere Website-Oberfläche; die Quelle vieler gemeinsam genutzter Templates ([ezwebin](../../../funktionen/6.0/erweiterungen/ezwebin.md)) |
| `ezdemo` | `ezdemo` | Das Design des Demo-Pakets ([ezdemo](../../../funktionen/6.0/erweiterungen/ezdemo.md)) |
| `ezflow` | `ezflow` | Blockbasierte Landingpages der älteren Art ([ezflow](../../../funktionen/6.0/erweiterungen/ezflow.md)) |
| `sevenx_themes_media` | `media` | Ein vollständiges Frontend für Medien-Websites, gebaut für Exponential Layouts; wird mit dem Code installiert (`composer.json` im Wurzelverzeichnis), ist keines der obigen Seitenpakete und ein größeres Theme, als eine persönliche Website meist braucht |

Die installierten Versionen sehen Sie auf der Info-Seite (`/ezinfo/about`).

## Welcher Anfang für welche Website

| Ihre Website | Beginnen Sie mit |
|---|---|
| Persönliche Website, Blog, Portfolio | `sevenx_multisite_clean` |
| Familienseite | `sevenx_multisite_clean` (es hat bereits eine Sektion Restricted und Mitgliederrollen) |
| Vereinsseite | `sevenx_multisite_clean` |
| Sie möchten zuerst lernen | `sevenx_multisite` (voll) auf Ihrem eigenen Computer |
| Sie führen eine alte, auf `ezwebin` gebaute Website weiter | Behalten Sie deren Paket und Design |

## Wie viel Sie ohne Programmierung ändern können

| Änderung | Wie | Kenntnisse |
|---|---|---|
| Name der Website, Logo, einige Farben | Einstellungen und die Optionen des Designs ([Ihrer Website ein Markenbild geben](../../ersteller/website-ersteller/ihre-website-markengerecht-gestalten.md)) | Keine |
| Menüeinträge und ihre Reihenfolge | Der Inhaltsbaum ([Menüs und Navigation](../../ersteller/website-ersteller/menues-und-navigation.md)) | Keine |
| Seitenlayouts mit Blöcken | Exponential Layouts ([Layout-Editor](../../layout-editor.md)) | Keine |
| Schriften, Farben im Detail, Abstände | Stylesheets ([Stylesheets und Skripte](../../ersteller/website-ersteller/stylesheets-und-skripte.md)) | CSS |
| Wie ein Seitentyp aussieht | Template-Overrides ([Template-Overrides](../../ersteller/website-ersteller/template-overrides.md)) | HTML und die Template-Sprache |
| Neue Felder | **Setup > Klassen** ([Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md)) | Sorgfalt; zuerst testen |

Nehmen Sie Designänderungen in Ihrer eigenen Design-Erweiterung vor, nicht im mitgelieferten Theme, damit Updates
sie nicht überschreiben ([Designs und die Fallback-Kette](../../ersteller/website-ersteller/designs-und-die-fallback-kette.md)).

## Später umentscheiden

- Ein Paket ist eine Momentaufnahme; nach der Installation ändern Sie Klassen und Design in der Website selbst.
- Ein späterer Wechsel zu einem anderen Theme ist möglich, heißt aber, jeden Seitentyp zu prüfen; planen Sie ihn wie
  eine kleine Neugestaltung ([Ein Design oder Theme wählen](../../ersteller/website-ersteller/ein-design-oder-theme-waehlen.md)).
- Eine Neuinstallation, um ein anderes Paket auszuprobieren, ersetzt die Datenbank: Tun Sie das nie bei einer Website
  mit echten Inhalten.

## Verwandte Seiten

- [Einrichtung für Privatpersonen](README.md)
- [Seitenpakete und die mitgelieferten Themes](../../ersteller/website-ersteller/seitenpakete-und-mitgelieferte-themes.md)
- [Seitenpakete](../../seitenpakete/README.md)
- [Ein Design oder Theme wählen](../../ersteller/website-ersteller/ein-design-oder-theme-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
