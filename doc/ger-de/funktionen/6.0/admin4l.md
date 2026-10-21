# Das Design admin4l: die Admin-Seite aus Layouts gebaut

Diese Seite ist für Administratoren, die die Administrationsoberfläche anordnen wollen, und für Entwickler, die
Admin-Blöcke hinzufügen. `admin4l` ist admin4, dessen Seite von Exponential Layouts zusammengesetzt wird: Kopfbereich,
linke und rechte Seitenleiste, Hauptbereich und Fußzeile sind Zonen eines **Admin-Layouts**, und was jede Zone zeigt,
sind **Blöcke**. Mit den mitgelieferten Layouts sieht die Seite genau wie admin4 aus: dasselbe Markup, dieselben Klassen,
dieselben Stylesheets.

## Darauf umstellen

1. In der `site.ini.append.php` des Admin-Siteaccess:

   ```ini
   [DesignSettings]
   SiteDesign=admin4l
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=admin4
   AdditionalSiteDesignList[]=admin3
   AdditionalSiteDesignList[]=admin2
   AdditionalSiteDesignList[]=admin
   ```

   `admin4l` enthält nur, was sich von admin4 unterscheidet (das Seitenlayout, das Zonen-Template und die
   Block-Templates); jedes andere Template und alle Stylesheets kommen aus `admin4`.

2. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, dann
   `--clear-id=template,template-override,template-block,content`.

## Der Sicherheitsschalter

Eine Einstellung bringt jede Admin-Seite zum einfachen admin4 zurück, gleich, was die Layouts und Regeln sagen. In
`settings/siteaccess/<admin siteaccess>/explayouts.ini.append.php`:

```ini
[AdminLayoutSettings]
Enabled=disabled
```

`Enabled=enabled` schaltet die Layouts ein. Zwei weitere Fälle greifen ohne jede Einstellung auf das eigene Template von
admin4 zurück:

- die Seite, wenn kein Admin-Layout aufgelöst wird oder das aufgelöste Layout keine Blöcke hat;
- eine **Zone**, die nichts ausgibt (sie wird stattdessen aus dem eigenen Template von admin4 gezeichnet). Die
  Hauptzone zeigt immer das Ergebnis des Moduls, auch wenn kein Block für das Modulergebnis in ihr platziert ist.

Ein Block, der auf halbem Weg scheitert, lässt stehen, was er schon ausgegeben hat; nur eine Zone, die leer herauskommt,
greift auf den Rückfall zurück.

## Layouts, Zonen und Blöcke

| Layout-Typ | Zonen | Seitenspalten |
|---|---|---|
| `admin_3col` | header, topmenu, left, right, main_top, main, main_bottom, footer | links und rechts |
| `admin_2col` | header, topmenu, left, main_top, main, main_bottom, footer | links |
| `admin_full` | header, topmenu, main_top, main, main_bottom, footer | keine |

Die Layouts werden im Reiter **Layouts** bearbeitet, wie Website-Layouts. Admin-Layouts, -Regeln und -Blöcke sind eine
eigene Gruppe: Der Editor führt sie nur als Admin-Layouts auf (`?group=admin` oder `all`), die Blockliste eines
Website-Layouts bietet nie einen Admin-Block an, und ein Website-Layout erreicht nie die Administrationsoberfläche.

Blöcke (`[AdminBlockSettings]` in `explayouts.ini`, jeder mit `Group=admin`; das Ansichts-Template eines Blocks ist
`design:explayouts/block/<identifier>.tpl`):

| Teil der Seite | Blöcke |
|---|---|
| Kopfbereich | `admin_logo`, `admin_menu_button`, `admin_search`, `admin_theme_switch`, `admin_sidebar_controls` (die Seite zeichnet das Logo und die Menüschaltfläche vor dem Menübereich und den Rest der Kopfzone danach, sodass die Tab-Reihenfolge die von admin4 ist; `admin_sidebar_toggles`, die Menüschaltfläche und die Steuerungen der Seitenleisten in einem Block, funktioniert weiter in Layouts, die vor der Aufteilung gespeichert wurden, und wird nach den Werkzeugen gezeichnet); `admin_tab_menu` für die Zone **topmenu**: Es zeichnet nur die Listeneinträge, die Seite zeichnet den einen Menübereich darum |
| Linke Seitenleiste | `admin_left_menu` (das Menü des aktuellen Navigationsteils; der optionale Parameter `part` erzwingt einen), `admin_content_tree` |
| Rechte Seitenleiste | `admin_toolbar_right`, `admin_toolbar_developer` (die Werkzeugleisten `admin_right` und `admin_developer` aus `toolbar.ini`, wie admin4 sie zeichnet, in der Reihenfolge von admin4) oder die einzelnen Werkzeuge `admin_clear_cache`, `admin_bookmarks`, `admin_current_user`, `admin_preferences`, `admin_quick_settings` |
| Seite | `admin_breadcrumb`, `admin_module_result`, `admin_footer`, `admin_popup_menu`, `admin_overlay`, `admin_debug_area` |

Jeder Block bindet den Teil von admin4 ein, nach dem er benannt ist, mit den eigenen Bedingungen von admin4, sodass das
Markup gleich bleibt. Die Hüllen (`#navbar`, `#leftmenu`, `#footer-design` ...) zeichnet die Seite, nicht die Blöcke.
Das Kontextmenü, die Debug-Markierung und die Überlagerung werden dort gezeichnet, wo admin4 sie zeichnet (außerhalb der
Fußzeile), aus den Blöcken der Fußzone.

`admin_left_menu` der Navigationsteile my, content, media und user enthält bereits die Inhaltsstruktur. Setzen Sie
`admin_content_tree` nur dann in eine Zone, wenn Sie den Baum auf einer Seite möchten, deren eigenes Menü keinen hat; in
den mitgelieferten Layouts ist er nicht platziert.

## Welches Layout eine Seite bekommt

Regeln wählen nach **Modul und Ansicht** (Ziele `module` und `module_view`; Muster `content`, `content/*`,
`content/view`, `*`). Die erste eingeschaltete Regel nach Priorität, deren Layout ein Admin-Layout ist, gewinnt; dann
`[AdminLayoutSettings] DefaultLayout` (Standard `admin_3col`). Regeln gelten nur auf den Siteaccesses aus
`SiteAccessMatch[]` (`admin`, `admin_*`, `admintest_*`). Mitgelieferte Regeln:

| Priorität | Ziele | Layout |
|---|---|---|
| 200 | jede Ansicht, deren Moduldefinition `ui_context=edit` sagt (content/edit, ...) | `admin_full` |
| 100 | content/view, content/dashboard | `admin_3col` |
| 90 | Module setup, user, shop | `admin_3col` |
| 10 | jedes Modul | `admin_3col` |

## Caching

Das aufgelöste Layout wird `CacheTTL` Sekunden lang pro Modul, Ansicht und Siteaccess gemerkt. Die Cache-Blöcke der Seite
haben als Schlüssel die ID des Layouts und eine **Admin-Generation**, die sich ändert, wann immer ein Admin-Layout
veröffentlicht oder gelöscht wird oder sich eine Regel ändert, sodass eine Bearbeitung sofort erscheint und nichts
geleert werden muss. Kopfbereich und Fußzeile haben außerdem die Rollen des Benutzers als Schlüssel (das Kontextmenü
zeigt Einträge nach Richtlinie). Der GET-Parameter `SectionID` geht als Zahl in die Schlüssel ein, sodass seltsame Werte
keine Cache-Dateien erzeugen.

## Unterschiede zu admin4

- Die Tab-Reihenfolge des Kopfbereichs unterscheidet sich: admin4 hat Logo, Menüschaltfläche, Menü, Suche, Werkzeuge;
  admin4l hat die Kopfzone (Logo, Suche, Theme, Menüschaltfläche und Steuerungen der Seitenleisten) vor der Zone
  topmenu, weil die Menüschaltfläche und die Steuerungen der Seitenleisten ein Block sind.

## Prüfen

```bash
php vendor/bin/phpunit --testsuite explayouts_admin
```

deckt die Layout-Typen, Muster, Auflösung pro Modul, den Sicherheitsschalter, die Trennung von der Website, den
Cache-Schlüssel und die Generation sowie die Blockdefinitionen und -Templates ab (Live-Einstellungen und -Datenbank,
nichts wird geschrieben außer der eigenen Cache-Generation des Resolvers).

Siehe [die Verhaltensänderung](../../../eng-us/bc/6.0/admin4l.md) für das, was sich von admin4 unterscheidet, und
[admin4](admin4-design.md) für das Design darunter.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
