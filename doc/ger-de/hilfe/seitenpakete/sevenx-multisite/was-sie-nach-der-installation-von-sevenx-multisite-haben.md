---
title: Was Sie nach der Installation von sevenx_multisite haben
audience: admin, hosting, website-creator
category: install-upgrade
order: 70
summary: Eine Checkliste einer fertigen Installation von sevenx_multisite - Websites, Siteaccesses, Einstellungsdateien, Erweiterungen, Inhalte, Rollen, Caches und Feeds - mit den Dateien, die Sie ansehen, um jeden Punkt zu bestätigen.
keywords: nach der Installation, Checkliste, sevenx_multisite, Fit & Healthy, Bold Agency, settings/override, settings/siteaccess, statischer Cache, HTTP-Cache, LogSearchStats, ezinfo, ezurlmultisite, RSS-Feed, my_feed, after install, checklist, static cache, HTTP cache, RSS feed
type: help
---
# Was Sie nach der Installation von sevenx_multisite haben

Diese Seite ist für Administratoren, Hoster und Website-Ersteller, die gerade `sevenx_multisite` (oder seine Clean-Variante) installiert
haben und das Ergebnis prüfen wollen oder wissen wollen, wo sie anfangen. Sie beantwortet: woraus die Installation besteht, welche Dateien
ihre Einstellungen enthalten und was bewusst ausgeschaltet ist.

![Der Inhaltsbaum nach der Installation: ein Ordner pro Website](../../../../eng-us/help/images/help/sp-sevenx-tree.webp)

## Inhalt

1. [Die zwei Websites](#die-zwei-websites)
2. [Siteaccesses und Einstellungsdateien](#siteaccesses-und-einstellungsdateien)
3. [Inhalte, Klassen und Layouts](#inhalte-klassen-und-layouts)
4. [Benutzer und Rollen](#benutzer-und-rollen)
5. [Caches, Feeds und Statistik](#caches-feeds-und-statistik)
6. [Was ausgeschaltet ist](#was-ausgeschaltet-ist)
7. [Eine Checkliste](#eine-checkliste)

## Die zwei Websites

| Website | Remote-ID des Startknotens | Siteaccesses | Standardname |
|---|---|---|---|
| Fit & Healthy | `media-n-939` | `site` und einer pro zusätzlicher Sprache | der im Setup eingegebene Titel, sonst „Fit & Healthy“ |
| Bold Agency | `media-n-940` | `bold` (Englisch), `bold_ger` (Deutsch) | „Bold Agency“ |

Beide nutzen das Design `media` von `sevenx_themes_media`. Die Siteaccesses von Bold werden über den Pfad auf der Hauptadresse erreicht
(`/bold/`, `/bold_ger/`). Links zwischen den beiden Websites werden für den Siteaccess geschrieben, der das Ziel ausliefert, über
`ezurlmultisite` (siehe [Links zwischen Websites einer Multisite](../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)).

## Siteaccesses und Einstellungsdateien

| Pfad | Geschrieben von |
|---|---|
| `settings/override/site.ini.append.php` | `commonSiteINISettings()`: Listen und Zuordnung der Siteaccesses, `ActiveExtensions`, Datenbank, Sitzung, HTTP-Header, Schalter für den statischen Cache |
| `settings/override/content.ini`, `menu.ini`, `viewcache.ini`, `staticcache.ini`, `httpcache.ini`, `override.ini`, `forum.ini`, `ezoe_attributes.ini`, `ezxml.ini` (`.append.php`) | `commonINISettings()` |
| `settings/override/sevenxauthentication2fa.ini.append.php` | wenn `sevenx_authentication_2fa` installiert ist |
| `settings/override/ezurlmultisite.ini.append.php` | Schritt 48: `[MultisiteSettings] SiteAccessList[]` und `RootNodeList[]` mit den Knoten-IDs dieser Installation |
| `settings/siteaccess/site/` | dem Setup und dem Installer, elf Gruppen aus `siteINISettings()` |
| `settings/siteaccess/bold/`, `bold_ger/` | `site.ini.append.php` und `menu.ini.append.php` vom Installer, der Rest aus der Theme-Erweiterung |
| `settings/siteaccess/admin/` | dem Setup und dem Installer, zehn Gruppen aus `adminINISettings()` |
| `settings/siteaccess/editor/`, `adminui/` | Kopien des Siteaccess der Administration, nach der Nachinstallation angelegt |

Details pro Siteaccess: [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-sevenx-multisite.md).

## Inhalte, Klassen und Layouts

- Die 33 Klassen von `sevenx_classes` (sechs davon aus den Basisdaten behalten) und die 31 Klassen `ng_*` in den Klassengruppen `Netgen` und
  `Netgen Components`; die Klassen von `cjw_newsletter` in einer Gruppe `Newsletter` mit einem Newsletter-Baum.
- Mit `sevenx_multisite`: 291 Inhaltsobjekte der Demo-Inhalte. Mit `sevenx_multisite_clean`: 35 Objekte, die Struktur, die Formulare und die
  rechtlichen Seiten.
- 21 veröffentlichte Layouts mit Zonen, Blöcken, Sammlungen und Regeln, zwei davon geteilt (`Header / Footer`, `Prefooter`), und die drei
  Standardlayouts der Administration.
- 31 Tags und die Tag-Links der Demo-Inhalte.
- Ein Bild für das Logo der Website (`logo.png` aus dem Paket) und der Seitenstil `ezwebin_design_blue` auf dem Objekt `template_look`, dazu
  zwölf neue Felder für Aussehen und Bedienung wie der Text im Footer
  (`Copyright © <year> 7x (except where otherwise noted). All rights reserved.`).

Siehe [Die Demo-Inhaltspakete](die-demo-inhaltspakete.md) und [Layoutdaten und share-DBA-Dateien](layoutdaten-und-share-dba-dateien.md).

## Benutzer und Rollen

- Eine Sektion `Restricted` und eine Benutzergruppe `Partners` darin; `Guest accounts` in `Members` umbenannt.
- Richtlinien für Anonymous, Member, Editor und Partner; siehe [Rollen und Richtlinien](rollen-und-richtlinien-von-sevenx-multisite.md).
- Die Voreinstellungen des Administrators für die Administration: Inhaltsstruktur, Rollen, Richtlinien, Baummenü und Lesezeichen gezeigt.

## Caches, Feeds und Statistik

| Funktion | Zustand nach der Installation | Wo |
|---|---|---|
| Statischer Cache | **aus** (`[ContentSettings] StaticCache=disabled`), aber vollständig konfiguriert: `CachedSiteAccesses` = jeder öffentliche Siteaccess, `MaxCacheDepth=12`, `StaticStorageDir=static` | `settings/override/site.ini`, `staticcache.ini` |
| HTTP-Cache | welche Siteaccesses: jeder öffentliche; ob er an ist, bleibt der Standard des Kernels | `settings/override/httpcache.ini` |
| Caching in Browser und Proxy | `Cache-Control: public, max-age=300` und `Expires` 300 für anonyme Besucher auf Inhaltsseiten | `[HTTPHeaderSettings]` in `settings/override/site.ini` |
| Ansichts-Cache | `SmartCacheClear=enabled` mit Abhängigkeitsregeln pro Klasse | `viewcache.ini` |
| Suchstatistik | `[SearchSettings] LogSearchStats=enabled` auf jedem Siteaccess, den der Installer geschrieben hat | jede `site.ini.append.php` |
| RSS-Feed | `My RSS Feed` unter `/rss/feed/my_feed`, die Objekte `ng_article` unter der Startseite von Fit & Healthy, 10 Einträge, RSS 2.0; von Paketen `_clean` nicht angelegt | die Liste der RSS-Exporte der Administration |

Der statische Cache wird ausgeschaltet installiert, weil jede Veröffentlichung die zwischengespeicherten Seiten jeder Adresse auffrischt, an
der das Objekt erscheint, was eine einzelne Veröffentlichung Sekunden dauern ließ. Der Kommentar in `commonSiteINISettings()` sagt, wie Sie
ihn einschalten: zuerst den Cache erzeugen, dann die eine Einstellung setzen.

## Was ausgeschaltet ist

| Element | Einstellung |
|---|---|
| Das Modul `ezinfo` auf öffentlichen Siteaccesses | `[SiteAccessRules] Rules` `access;enable`, `moduleall`, `access;disable`, `module;ezinfo` auf jedem Siteaccess, dessen Design kein Design der Administration ist |
| Debug-Ausgabe | `[DebugSettings] DebugOutput=disabled`, `[TemplateSettings] Debug=disabled` |
| Verzögerte Indizierung | `[SearchSettings] DelayedIndexing=disabled` |
| Erzwingen der zweistufigen Anmeldung | `Enforce2FA=disabled` (Benutzer wählen selbst) |
| `expai` | von der Erweiterung selbst mit `[AISettings] Enabled=false` installiert, ohne Anbieterschlüssel |

## Eine Checkliste

- [ ] `settings/override/site.ini.append.php` führt `site`, `bold`, `bold_ger`, `admin`, `editor` (und `adminui`) in
      `AvailableSiteAccessList` auf.
- [ ] Die Startseite zeigt Fit & Healthy, und `/bold/` zeigt Bold Agency.
- [ ] `settings/siteaccess/site/site.ini.append.php` hat ein nicht leeres `PathPrefix` und eine `IndexPage` außer `/content/view/full/2`.
- [ ] `settings/override/ezurlmultisite.ini.append.php` existiert und nennt Knoten-IDs.
- [ ] Die Administration öffnet sich im Design `admin4l` und der Siteaccess der Redaktion in `editor`.
- [ ] Das Setup hat kein `EZSW-080`, `EZSW-081` oder `EZSW-082` gemeldet.

> **Tipp**
> Wenn eine Seite unter einer Wurzel einer Website mit 404 antwortet, prüfen Sie zuerst `PathPrefix` und die URL-Aliase: Das sind die Teile,
> die die Nachinstallation zuletzt baut. Siehe [Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md#url-aliase-und-präfixe-der-websites).

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-sevenx-multisite.md)
- [Generator für den statischen Cache](../../../funktionen/6.0/generator-fuer-den-statischen-cache.md)
- [Erste Schritte](../../erste-schritte.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
