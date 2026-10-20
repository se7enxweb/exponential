---
title: Wie die Fußzeile aufgebaut ist
audience: designer, developer, website-creator, admin
category: build-two-ways
order: 220
summary: Von der Zone footer des gemeinsamen Layouts Header / Footer zu pagelayout/footer.tpl und seinen drei Datenquellen (FooterMenuID, das Site-Info-Objekt und der Ordner Footer links), mit den echten Dateipfaden.
keywords: Fußzeile, gemeinsames Layout, Header / Footer, Zone footer, Template-Block, Block 14, pagelayout/footer.tpl, FooterMenuID, NexusFooterMenuID, FooterLinksRemoteID, Site-Info, ng_menu_item, footer, shared layout, zone footer, template block, block 14, site info
type: help
---
# Wie die Fußzeile aufgebaut ist

Die Fußzeile nimmt denselben Weg wie die Kopfzeile ([Wie die Kopfzeile aufgebaut ist](../../../eng-us/help/build-two-ways/how-the-header-is-built.md)). Pfade
sind relativ zu `extension/sevenx_themes_media/design/media/templates/`.

## Inhalt

1. [Vom Layout zum Template](#vom-layout-zum-template)
2. [In footer.tpl](#in-footertpl)
3. [Was wo zu ändern ist](#was-wo-zu-ändern-ist)

## Vom Layout zum Template

| Schritt | Was |
|---|---|
| 1 | Das Layout der Seite verknüpft seine Zone `footer` mit der Zone `footer` des gemeinsamen Layouts **Header / Footer** |
| 2 | Diese Zone enthält einen **Template block** mit dem Template-Namen `footer` (Block 14 auf dieser Site) |
| 3 | `explayouts/layout.tpl` setzt die Zone `footer` nach `<main>`, `explayouts/zone.tpl` stellt ihre Blöcke dar |
| 4 | `explayouts/block/tpl_block.tpl` fragt `tpl_block_template()`, das mit `pagelayout/footer.tpl` antwortet (über die Block-ID, sonst weil er ein Wurzelblock in der Zone `footer` ist) |

Eine Seite ohne Layout bindet `pagelayout/footer.tpl` direkt aus `pagelayout.tpl` ein.

## In footer.tpl

`pagelayout/footer.tpl` holt zuerst das **Site-Info-Objekt**, das Objekt, dessen Remote-ID
`menu.ini [SiteInfo] RemoteID` ist (`media-o-site-info` auf dieser Site). Ohne dieses Objekt stellt es nichts dar. Dann:

| Teil | Daten | Template-Code |
|---|---|---|
| Logo | das Logo des Designs | `{include uri='design:content/parts/site_logo.tpl'}` |
| Erste Linkzeile | `ezini('SiteInfo','FooterMenuID','menu.ini')`, ein `fetch('content','node', ...)` pro ID | `<li id="menu-item-additional_menu-location-id-...">` |
| Social-Media-Symbole | die Felder `facebook`, `twitter`, `instagram`, `linkedin` des Site-Info-Objekts, gezeigt, wenn sie Inhalt haben | `$ft_site_info.data_map.facebook.has_content` |
| Zweite Linkzeile | `fetch('content','list', ...)` der Kinder des Objekts mit der Remote-ID `[SiteInfo] FooterLinksRemoteID` (Standard `media-o-footer-links`), Klasse `ng_menu_item`, in der Sortierreihenfolge des Ordners | `<div class="footer-menu footer-links">` |
| Cookie-Einstellungen | `parts/cookie_settings_link.tpl` | |
| Infotexte | übersetzbare Zeichenketten, Kontext `design/media/pagelayout` | `'...'|i18n('design/media/pagelayout')` |

`NexusFooterMenuID[]` spielt dieselbe Rolle wie `NexusMainMenuID[]` in der Kopfzeile: Es gibt den Attributen `id` und
`data-location-id` der ersten Einträge die IDs der Referenz-Site. Ein Link, dessen URL-Alias mit `fit-healthy/` beginnt,
wird mit `ezroot` statt `ezurl` geschrieben.

## Was wo zu ändern ist

| Änderung | Weg | Seite |
|---|---|---|
| Eine Seite zur ersten Zeile hinzufügen | Einstellungen (`FooterMenuID[]`) | [Die Einträge des Fußzeilenmenüs](eintraege-des-fusszeilenmenues.md) |
| Einen Link zur zweiten Zeile hinzufügen | Inhalt (Menu item in Footer links) | [Fußzeilen-Links aus dem Inhaltsbaum](fusszeilen-links-aus-dem-inhaltsbaum.md) |
| Adressen der Social-Media-Profile | Inhalt (das Site-Info-Objekt bearbeiten) | |
| Blöcke über der Fußzeile auf Artikel- und Rezeptseiten | Layout-Editor (gemeinsames Layout Prefooter) | [Gemeinsame Layouts und Zonenverknüpfung](../../../eng-us/help/build-two-ways/shared-layouts-and-zone-linking.md) |
| Markup der Fußzeile | Template-Override in Ihrer eigenen Design-Erweiterung | [Zwei Wege zum Bauen](../../../eng-us/help/build-two-ways/two-ways-to-build.md) |

## Verwandte Seiten

- [Wie die Kopfzeile aufgebaut ist](../../../eng-us/help/build-two-ways/how-the-header-is-built.md)
- [Die Einträge des Fußzeilenmenüs](eintraege-des-fusszeilenmenues.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
