# Admin-Reiter ausblenden: HiddenTabs

Diese Seite ist für Administratoren, die auf einem Siteaccess eine einfachere Administrationsoberfläche möchten, zum
Beispiel auf einem Editor-Siteaccess. Zwei Einstellungen nehmen einen Reiter aus dem oberen Menü der
Administrationsoberfläche oder aus der Knotenansicht, gleich, was ihn hinzugefügt hat. Hinzugefügt am 01.10.2026.

Vorher konnte ein Siteaccess die Liste der Reiter neu festlegen, aber nie den Reiter einer Extension entfernen:
Extensions hängen ihre Reiter an, und Extension-Einstellungen werden nach denen eines Siteaccess gelesen.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/menu.ini` | `TopAdminMenu` | `HiddenTabs[]` | leer | Siteaccess oder Override |
| `settings/admininterface.ini` | `WindowControlsSettings` | `HiddenTabs[]` | leer | Siteaccess oder Override |

- `[TopAdminMenu] HiddenTabs[]` wird vom Template-Operator `topmenu` gelesen (das obere Menü).
- `[WindowControlsSettings] HiddenTabs[]` wird von `window_controls.tpl` in `admin`, `admin3` und `admin4` gelesen (die
  Reiter der Knotenansicht).

Die Bezeichner der Reiter sind die Einträge von `Tabs[]` und `AdditionalTabs[]`, zum Beispiel `dashboard`, `design`,
`roles`.

## Beispiel: kein Design-Menü und kein Knotenreiter Layouts

`settings/siteaccess/<admin-like siteaccess>/menu.ini.append.php`:

```ini
<?php /*
[TopAdminMenu]
HiddenTabs[]
HiddenTabs[]=design
*/ ?>
```

`settings/siteaccess/<admin-like siteaccess>/admininterface.ini.append.php`:

```ini
<?php /*
[WindowControlsSettings]
HiddenTabs[]
HiddenTabs[]=layouts
*/ ?>
```

Leeren Sie dann den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Laden Sie die Administrationsoberfläche neu: Der Reiter Design und der Reiter Layouts der Knotenansicht sind nur auf
diesem Siteaccess verschwunden.

Jede Stelle finden, die die Einstellungen liest:

```bash
grep -rn HiddenTabs kernel design settings
```

## Design, Audit und Dashboard ohne ihre Reiter (admin4)

In admin4 geht beim Ausblenden von `design` oder `audit` keine Seite verloren: Das Setup-Menü und die Setup-Übersicht
zeigen dann eine Gruppe Design oder Audit mit den Links des Reiters, und die Seiten des Reiters markieren den Reiter
Setup. Ohne `dashboard` führt das Logo zum Dashboard. Siehe
[Einen Reiter in Setup verschieben](admin4-design.md#einen-tab-nach-setup-verschieben). Leeren Sie auch den Template-Cache: Die
Menüs kompilieren die Einstellungen ein.

## Der mitgelieferte Editor-Siteaccess

Der Editor-Siteaccess verwendet beide Einstellungen. Er blendet im oberen Menü `explayouts_ui_dashboard`, `setup`,
`design`, `gitmanager`, `xrowextract`, `bccie_overview` und `syndication` aus und in der Knotenansicht `layouts`. Siehe
[den Einrichtungsassistenten und den Editor-Siteaccess](einrichtungsassistent-und-editor-siteaccess.md).

## Am selben Tag geändert

- Die Knotenansicht zeigt jeden zusätzlichen Reiter einmal. Ein Reiter, den eine Extension zweimal anhängte, erschien
  früher zweimal.
- Das Dashboard, seine Menüs und die oberen Reiter zeigen einen Link nur einem Benutzer, der ihn öffnen kann; siehe
  [Admin-Links folgen den Berechtigungen](admin-links-folgen-den-berechtigungen.md).
- Die Shop-Seitenleiste (`[Leftmenu_shop]` in `menu.ini`) führt Dashboard, Bestellungen, Produktübersicht,
  Produkt-Statistiken und Produktkategorien zuerst auf.

## Verwandte Seiten

- [Admin-Links folgen den Berechtigungen](admin-links-folgen-den-berechtigungen.md)
- [Das Design admin4](admin4-design.md)
- [Einrichtungsassistent und Editor-Siteaccess](einrichtungsassistent-und-editor-siteaccess.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../geschichte/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
