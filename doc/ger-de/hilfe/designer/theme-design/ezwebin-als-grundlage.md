---
title: Das Design ezwebin als Grundlage
audience: designer
category: theme-design
order: 140
summary: Das klassische Design ezwebin (Erweiterung ezwebin) als Rückfall unter Ihrem eigenen Design verwenden: seine Seitenteile, die Operatoren ezpagedata, mit denen View-Templates den Rahmen steuern, die Menüeinstellungen und wie Sie nur überschreiben, was Sie brauchen.
keywords: ezwebin, Website Interface, Basisdesign, Rückfalldesign, ezpagedata, ezpagedata_set, ezpagedata_append, page_header, page_leftmenu, menu.ini, SelectedMenu, template_look, klassisches Design, website interface, base design, fallback design, classic design
type: help
---
# Das Design ezwebin als Grundlage

Diese Seite ist für Designer, die auf dem Design ezwebin („Exponential Website Interface LS“, Erweiterung `ezwebin`) aufbauen,
entweder direkt oder über das Design simple, das dieselben Klassen verwendet. Sie beantwortet: wie der Rahmen von ezwebin in Teile
geschnitten ist, wie ein View-Template den Rahmen mit `ezpagedata_set` ändern kann und wie Sie Teile in Ihrem Design überschreiben.
Die eigene Seite der Erweiterung ist [ezwebin](../../../funktionen/6.0/erweiterungen/ezwebin.md).

## Inhalt

1. [Die Seitenteile](#die-seitenteile)
2. [ezpagedata: die Fakten der Seite](#ezpagedata-die-fakten-der-seite)
3. [Den Rahmen aus einem View-Template steuern](#den-rahmen-aus-einem-view-template-steuern)
4. [Menüs aus menu.ini](#menüs-aus-menuini)
5. [Teile in Ihrem Design überschreiben](#teile-in-ihrem-design-überschreiben)
6. [Wann Sie es nicht verwenden](#wann-sie-es-nicht-verwenden)

## Die Seitenteile

`extension/ezwebin/design/ezwebin/templates/` schneidet den Rahmen in kleine Teile, die sich jeweils einzeln ersetzen lassen:

| Template | Zeichnet |
|---|---|
| `pagelayout.tpl` | den Rahmen, bindet die Teile unten ein |
| `page_head.tpl`, `page_head_style.tpl`, `page_head_script.tpl` | den Kopf, Stylesheets, Skripte |
| `page_header.tpl`, `page_header_logo.tpl`, `page_header_searchbox.tpl`, `page_header_links.tpl`, `page_header_languages.tpl` | den Kopfbereich und seine Teile |
| `page_topmenu.tpl`, `page_leftmenu.tpl`, `page_extramenu.tpl` | die Menüs |
| `page_toppath.tpl` | den Brotkrumenpfad |
| `page_mainarea.tpl` | den Inhaltsbereich |
| `page_toolbar.tpl` | die Website-Werkzeugleiste für angemeldete Redakteure |
| `page_footer.tpl`, `page_footer_script.tpl` | den Fußbereich |

## ezpagedata: die Fakten der Seite

Die Erweiterung registriert die Operatoren `ezpagedata`, `ezpagedata_set` und `ezpagedata_append`
(`extension/ezwebin/autoloads/ezpagedata.php`). `ezpagedata()` gibt einen Hash zurück, den der Rahmen verwendet, zum Beispiel:

| Schlüssel | Bedeutung |
|---|---|
| `node_id`, `class_identifier` | der aktuelle Knoten und seine Klasse |
| `is_edit` | ob die Seite eine Bearbeitungsseite ist |
| `show_path` | ob der Brotkrumenpfad gezeigt wird |
| `website_toolbar` | ob die Werkzeugleiste gezeigt wird (angemeldeter Benutzer auf einer Inhaltsseite) |
| `top_menu`, `left_menu`, `extra_menu` | welche Menüs gezeigt werden (aus `menu.ini`) |
| `template_look` | das Objekt „template look“ (Logo, Website-Einstellungen, die Redakteure pflegen) |
| `css_classes` | Klassen für den Seitenrahmen: `sidemenu`/`nosidemenu`, `extrainfo`/`noextrainfo`, `section_id_<n>` ... |
| `persistent_variable` | was View-Templates übergeben haben |

Der eigene Kommentar des Operators weist darauf hin, dass seine Funktionen nicht sicher in Cache-Blöcken sind: Rufen Sie ihn
außerhalb eines Cache-Blocks auf oder nehmen Sie sein Ergebnis in die Schlüssel auf.

## Den Rahmen aus einem View-Template steuern

Eine Vollansicht kann dem Rahmen sagen, dass er das linke Menü oder den Brotkrumenpfad ausblenden soll, ohne dass das Pagelayout die
Klasse kennt:

```tpl
{* in override/templates/full/landing_page.tpl of your design *}
{ezpagedata_set( 'left_menu', false() )}
{ezpagedata_set( 'show_path', false() )}
```

`ezpagedata_set` schreibt in die persistent variable der View, die das Pagelayout über
`$module_result.content_info.persistent_variable` erreicht; `ezpagedata()` liest sie dort. `ezpagedata_append` ergänzt eine Liste,
statt sie zu ersetzen. Siehe [Die persistent variable](../vorlagen/die-persistent-variable.md).

## Menüs aus menu.ini

`ezpagedata` liest `menu.ini [SelectedMenu] TopMenu` und `LeftMenu` sowie `[MenuSettings] HideLeftMenuClasses[]` (Klassen, deren
Seiten kein linkes Menü bekommen). Den Menütyp einer Website zu ändern, ist eine Änderung der Einstellungen in der
`menu.ini.append.php` Ihres Siteaccess, keine Template-Änderung.

## Teile in Ihrem Design überschreiben

Legen Sie Ihre Fassung an denselben Pfad in Ihrem Design, das in der Kette über `ezwebin` steht:

```
extension/mytheme/design/mytheme/templates/page_header_logo.tpl
extension/mytheme/design/mytheme/templates/page_footer.tpl
```

und führen Sie `ezwebin` nach Ihrem Design auf:

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

Beginnen Sie mit dem Logo, dem Fußbereich und dem Stylesheet; mehr braucht eine Website mit eigener Marke oft nicht.

## Wann Sie es nicht verwenden

- Ihre Website verwendet die Inhaltsklassen von ezwebin nicht: Die meisten seiner Templates würden nie laufen.
- Sie brauchen auf der öffentlichen Website eine strenge Content Security Policy: Prüfen Sie seine Templates zuerst auf Inline-Skripte.
- Sie beginnen ein Designsystem mit Token und dunklem Modus: Die Stylesheets von ezwebin sind älter, sodass ein neues Pagelayout
  weniger Arbeit sein kann, als sie zu überschreiben.

## Verwandte Seiten

- [Ein Rundgang durch das Theme simple](rundgang-durch-das-simple-theme.md)
- [Die Design-Kaskade](die-design-kaskade.md)
- [Menüs und Navigation](../vorlagen/menues-und-navigation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
