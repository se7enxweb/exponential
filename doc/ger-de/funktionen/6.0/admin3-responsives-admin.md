# Das responsive Admin-Design (admin3)

Diese Seite ist für Administratoren, die möchten, dass Redakteure von einem Telefon, einem Tablet oder einem breiten
Monitor aus arbeiten. `admin3` ist die klassische Administrationsoberfläche (die Designs `admin` und `admin2`) mit einer
einklappbaren Seitenleiste, Menüs, die per Berührung funktionieren, und Inhaltsformularen, die sich stapeln statt
überzulaufen. Jeder Name von Modul, Berechtigung und Template ist derselbe wie in `admin`. Es wurde zuerst im August 2024
(6.0.4) ausgeliefert und im Oktober und November 2024 (6.0.6) nach Tests auf Telefonen und Tablets überarbeitet.

## Einschalten

`admin3` erweitert `admin` und wird daher als Design eines Admin-Siteaccess aufgeführt. Der mitgelieferte Siteaccess
`admin` führt es bereits als Rückfall hinter [admin4](admin4-design.md):

```ini
# settings/siteaccess/admin/site.ini.append.php
[DesignSettings]
SiteDesign=admin4
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=admin3
AdditionalSiteDesignList[]=admin2
AdditionalSiteDesignList[]=admin
```

Zuerst aufgeführte Designs gewinnen. Um `admin3` selbst zu betreiben, setzen Sie es in `SiteDesign`:

```ini
[DesignSettings]
SiteDesign=admin3
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=admin2
AdditionalSiteDesignList[]=admin
```

Leeren Sie dann die Caches:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
```

Das responsive Skript wird von `settings/design.ini` geladen, `[JavaScriptSettings] BackendJavaScriptList[]=ezadmin_menubar.js`.
Im ersten Release hieß das Skript `main.js` und musste von Hand eingeschaltet werden; seit November 2024 heißt es
`ezadmin_menubar.js` und ist standardmäßig eingeschaltet.

## Was anders ist

- **Seitenleisten klappen ein.** Das linke Menü (Inhaltsbaum und Schnellzugriffe) und das rechte Menü klappen mit einem
  Schalter weg; die Wahl übersteht das Neuladen von Seiten. Auf schmalen Bildschirmen öffnen sie sich als Ebene über der
  Seite, mit einem tieferen Schatten.
- **Tabellen scrollen, statt die Seite zu sprengen.** `pagelayout.css` gibt `.table-responsive` und jedem Element, das
  direkt eine Tabelle enthält (`div:has(> table)`), `overflow-x: auto`, sodass eine breite Tabelle in ihrem Kasten
  scrollt. `ezadmin_menubar.js` definiert außerdem einen Helfer `wrapTable()`, aber sein Aufruf im Handler für „Seite
  bereit“ ist auskommentiert; die CSS-Regel erledigt die Arbeit. Prüfen mit
  `grep -n wrapTable design/admin3/javascript/ezadmin_menubar.js`.
- **Kopfbereich und Dashboard folgen dem Bildschirm.** Die Höhe des Dashboards wird aus der echten Höhe des Kopfbereichs
  berechnet und in einer CSS-Variablen gespeichert (`--header-height`).
- **Browser auf Telefonen verhalten sich.** Eine Korrektur im November 2024 verhinderte, dass iOS-Browser die
  Seitenleisten beim Scrollen der Seite verbargen. Die Schichtung der Menüs (`z-index`) wurde korrigiert, sodass jeder
  Untermenüeintrag anklickbar ist.
- **Bearbeitungsformulare passen auf kleine Bildschirme.** `content/edit.tpl`, `content/edit_draft.tpl`,
  `content/edit_menu.tpl`, `content/history.tpl` und `content/view/versionview.tpl` sind in
  `design/admin3/templates/` überschrieben.
- **Den aktuellen Knoten beim Auswählen nehmen.** In einem Auswahldialog (Wahl einer übergeordneten Platzierung, eines
  Beziehungsziels, eines Tauschziels) zeigt die Überschreibung `content/browse_current_node.tpl` (Juni 2025), eingebunden
  von `content/browse_mode_list.tpl` und `content/browse_mode_thumbnail.tpl`, den Knoten, in dem Sie sind, als
  auswählbare Zeile. Sie müssen nicht mehr eine Ebene hinauf- und wieder hinabgehen, um ihn zu wählen.
- **Das Design-Menü ist zurück.** Der Menüeintrag `/design` steht im neuen Menübereich wieder zur Verfügung (Dezember
  2024).

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/siteaccess/<admin>/site.ini.append.php` | `DesignSettings` | `SiteDesign` (das zuerst verwendete Design) | `admin4` im mitgelieferten Admin-Siteaccess | Siteaccess |
| `settings/siteaccess/<admin>/site.ini.append.php` | `DesignSettings` | `AdditionalSiteDesignList[]` (Rückfall-Designs, in Reihenfolge) | `admin3`, `admin2`, `admin` | Siteaccess |
| `settings/design.ini` | `JavaScriptSettings` | `BackendJavaScriptList[]` (Backend-Skripte) | enthält `ezadmin_menubar.js` | global |

## Dateien

| Pfad | Was es ist |
|---|---|
| `design/admin3/stylesheets/pagelayout.css`, `responsive.css` | Das Layout und die Regeln für kleine Bildschirme |
| `design/admin3/javascript/ezadmin_menubar.js` | Schalter der Seitenleisten, Tabellen-Hülle, Höhe des Kopfbereichs, Steuerung der Seitenleistenbreite |
| `design/admin3/templates/pagelayout.tpl`, `page_header.tpl`, `page_topmenu.tpl`, `page_leftmenu.tpl` | Der Rahmen jeder Admin-Seite |

## Verwandte Seiten

- [Breite und Schriftgröße der linken Seitenleiste](admin3-seitenleistenbreite-und-schriftgroesse.md), [das Design admin4](admin4-design.md)
- [Ausgewählte Unterelemente kopieren](unterelemente-auswahl-kopieren.md), [ausgewählte Unterelemente ausblenden und einblenden](../../../eng-us/bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md), [Unterelementliste: Spalten, Voreinstellungen und CSV-Export](optionen-der-unterelementtabelle.md)
- [Blättern und Seitengrößen](seitenweise-listen-im-admin.md)
- [Die Entfernung von YUI](../../../eng-us/bc/6.0/yui-removal.md), die alle Admin-Designs auf jQuery umstellte
- Changelogs: [6.0.4](../../../changelogs/6.0/6.0.4.md), [6.0.6](../../../changelogs/6.0/6.0.6.md), [6.0.10](../../../changelogs/6.0/6.0.10.md)
- Chronik: [August 2024](../../../eng-us/history/2024/2024-08.md), [Oktober 2024](../../../eng-us/history/2024/2024-10.md), [November 2024](../../../eng-us/history/2024/2024-11.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md), [Juni 2025](../../../eng-us/history/2025/2025-06.md), [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
