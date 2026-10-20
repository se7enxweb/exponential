# Klassische Menüs: Design > Menüs (klassisch), menu.ini und Menü-Templates

Diese Anleitung erklärt die klassischen Menüeinstellungen von Exponential: was die Seite **Design > Menüs (klassisch)**
(`/visual/menuconfig`) tut, für welche Sites sie von Bedeutung ist und für welche nicht, was jede ihrer vier Anordnungen
einem Besucher zeigt, welche Rezepte die meisten Sites brauchen und wie Sie klassische Menüs in eigenen Templates
ausgeben. Sie endet mit einem kurzen geschichtlichen Abriss und den üblichen Problemen.

Sie richtet sich an Site-Entwickler, die eine Site noch mit einem der klassischen Designs betreiben, und an
Administratoren, die auf die Seite stoßen und sich fragen, ob sie sie anfassen sollen. Jede Regel und jede Datei unten
wurde am 6. Oktober 2026 gegen den Code dieses Repositorys geprüft, und jedes Template-Beispiel wurde mit den Inhalten
einer echten Installation gerendert.

[Anleitungen](README.md) · Siehe auch: [Templates und Design](templates-und-design.md) ·
[Exponential Layouts](../../en/us/bc/6.0/LAYOUTS.md)

## Kurz gesagt

- Die klassischen Designs (`design/base`, `ezwebin` sowie die darauf aufbauenden Demo- und Simple-Designs) zeichnen ein
  **oberes Menü** und ein **linkes Menü** anhand von drei Einstellungen in `menu.ini [SelectedMenu]`: `CurrentMenu`,
  `TopMenu` und `LeftMenu`. Die Seite Design > Menüs (klassisch) wählt pro Siteaccess eine von vier Anordnungen und
  schreibt diese drei Zeilen.
- **Sites, die mit Exponential Layouts gebaut sind, lesen sie nicht.** Ihre Menüs sind Blöcke in einem Layout und werden
  im Layout-Editor eingestellt (Reiter Layouts, `/explayouts_ui/dashboard`). Auch die Administrationsoberfläche liest
  sie nicht.
- Die Seite ermittelt selbst, welcher Siteaccess sie verwendet: Sie liest das Pagelayout, zu dem jeder Siteaccess
  aufgelöst wird. Siteaccesses, bei denen die Einstellungen nichts bewirken, werden mit Begründung eingeklappt, und
  das Speichern für einen von ihnen erfordert ein Häkchen, mit dem Sie bestätigen, dass Sie wissen, dass es keine
  Seite verändert.
- **Das Speichern schreibt nur die drei Zeilen**, und zwar in `settings/siteaccess/<siteaccess>/menu.ini.append.php`,
  bewahrt eine Kopie der Datei im vorherigen Zustand in `var/backup/ini` auf und leert den INI-Cache, die kompilierten
  Pagelayouts und die Template-Blöcke. Ein Neustart von PHP-FPM oder Velocity ist nicht nötig.

## 1. Wann diese Seite, wann Layouts

| Ihr Siteaccess | Seine Menüs stammen aus | Verwenden Sie |
|---|---|---|
| Einem klassischen Design (`base`, `ezwebin`, einem eigenen Design, das `{menu name=TopMenu}` oder `$pagedata.top_menu` enthält) | `menu.ini [SelectedMenu]` und `[MenuContentSettings]` | diese Seite oder die Dateien unten |
| Exponential Layouts (sein Pagelayout ruft `fetch( 'explayouts', 'resolve_layout' )` auf) | einem Menüblock in einer Layout-Zone | den Layout-Editor |
| Einem Administrationsdesign (`admin`, `admin2`, `admin3`, `admin4`, `admin4l`, `adminui`, dem Editor) | `menu.ini [TopAdminMenu]` und den Abschnitten `Leftmenu_*` | nicht diese Seite |

Auf der Seite erhält jeder Siteaccess aus `site.ini [SiteAccessSettings] RelatedSiteAccessList` eine dieser
Kennzeichnungen:

- **Verwendet diese Einstellungen**: Sein Pagelayout oder ein Template, das das Pagelayout einbindet, liest sie.
- **Verwendet diese Einstellungen, mit Layouts**: Er liest sie und rendert außerdem Layouts.
- **Nicht verwendet: rendert über Layouts**.
- **Administrations-Siteaccess**.
- **Von seinem Design nicht verwendet**: Sein Pagelayout zeichnet kein klassisches Menü.
- **Kein Seitenlayout gefunden**.

Die Seite öffnet sich beim ersten Siteaccess, der die Einstellungen verwendet. Verwendet sie keiner, sagt sie das
oben: Dort ist dann nichts zu tun.

## 2. Die vier Anordnungen

`menu.ini [MenuSettings] AvailableMenuArray` listet sie auf; jede hat eine Gruppe mit ihrem Titel und ihren zwei
Templates. Das Speichern einer Anordnung schreibt diese Zeilen (genau das, was die Seite unter „Template, Inhalt und
die geschriebenen Zeilen“ anzeigt):

| Anordnung | Geschriebene Zeilen | Oben | Links |
|---|---|---|---|
| Nur oberes Menü | `CurrentMenu=TopOnly` `TopMenu=flat_top` `LeftMenu=` | eine Zeile: die Seiten direkt unterhalb der Startseite | keines |
| Linkes Menü | `CurrentMenu=LeftOnly` `TopMenu=` `LeftMenu=flat_left` | keines | die Seiten direkt unterhalb der Startseite, unterhalb der Seite, auf der sich der Besucher befindet, weiter aufgeklappt |
| Doppeltes oberes Menü | `CurrentMenu=DoubleTop` `TopMenu=double_top` `LeftMenu=` | zwei Zeilen: die Seiten unterhalb der Startseite und darunter die Seiten der Seite der ersten Ebene, auf der sich der Besucher befindet | keines |
| Links und oben | `CurrentMenu=LeftTop` `TopMenu=flat_top` `LeftMenu=sub_left` | eine Zeile, wie oben | die Seiten der Seite der ersten Ebene, auf der sich der Besucher befindet, eine Ebene |

Was die Templates abrufen, gelesen aus `design/base/templates/menu/` und `extension/ezwebin/design/ezwebin/templates/menu/`:

- **Klassen.** Das obere Menü und beide Zeilen des doppelten oberen Menüs listen nur Objekte der Klassen in
  `menu.ini [MenuContentSettings] TopIdentifierList` auf; die linken Menüs verwenden `LeftIdentifierList`.
- **Reihenfolge.** Jede Liste wird so sortiert, wie ihre Elternseite ihre Unterseiten sortiert.
- **Versteckte Seiten** werden ausgelassen, wie für jeden Besucher, solange `site.ini [SiteAccessSettings] ShowHiddenNodes`
  auf `false` steht (die Voreinstellung).
- **Die Startseite.** Die Templates von ezwebin beginnen bei `content.ini [NodeSettings] RootNode` (und bei
  Unter-Sites bei `site.ini [SiteSettings] RootNodeDepth`). Die `design/base`-Templates `flat_top` und `double_top`
  beginnen immer bei Knoten 2, der Spitze des Inhaltsbaums. Auf einer Site, deren Startseite nicht Knoten 2 ist,
  listen sie die falschen Seiten auf; verwenden Sie die Templates von ezwebin oder eigene (Abschnitt 5).
- **Welche Datei es zeichnet,** hängt von den Designs des Siteaccess ab: Die Seite nennt die Datei, zu der jedes
  Template aufgelöst wird. Auf einer Site mit `ezwebin` vor `base` stammen `flat_top`, `double_top` und `flat_left`
  aus ezwebin und `sub_left` aus `design/base`.
- **ezwebin blendet das linke Menü aus** auf Seiten der Klassen in `menu.ini [MenuSettings] HideLeftMenuClasses`
  (standardmäßig `frontpage`, `blog`, `blog_post`).

Jede Anordnung hat auf der Seite eine kleine Vorschau, die aus den eigenen Seiten der Installation erstellt wird: die
Startseite, die Seiten des oberen Menüs und die Seiten der ersten Seite des oberen Menüs, die eigene Unterseiten hat,
so wie ein Besucher dort sie sehen würde.

## 3. Rezepte

Jedes Rezept nennt die Datei, die genauen Zeilen und das, was ein Besucher sieht. Tragen Sie sie in den gemeinten
Siteaccess ein; die Seite zeigt dieselben Rezepte mit dem Namen dieses Siteaccess bereits eingesetzt. Keines davon
bewirkt etwas bei einem Siteaccess, der über Exponential Layouts rendert: Dort ist ein Menü ein Block im Layout-Editor.

### Nur oberes Menü

`settings/siteaccess/<siteaccess>/menu.ini.append.php`

```ini
[SelectedMenu]
CurrentMenu=TopOnly
TopMenu=flat_top
LeftMenu=
```

Ein Besucher sieht unter dem Kopfbereich eine Zeile mit Links, die Seiten unterhalb der Startseite, und keine linke
Spalte.

### Oberes Menü plus ein linkes Menü für den aktuellen Bereich

```ini
[SelectedMenu]
CurrentMenu=LeftTop
TopMenu=flat_top
LeftMenu=sub_left
```

Ein Besucher sieht dieselbe Zeile; innerhalb einer Seite der ersten Ebene (etwa „Showcase“) listet eine linke Spalte
die Unterseiten dieser Seite auf. Auf der Startseite gibt es keine linke Spalte.

### Die Menüs auf Ordner und Landingpages beschränken

```ini
[MenuContentSettings]
TopIdentifierList[]
TopIdentifierList[]=folder
TopIdentifierList[]=frontpage
LeftIdentifierList[]
LeftIdentifierList[]=folder
LeftIdentifierList[]=frontpage
```

Die leere Zeile `TopIdentifierList[]` leert die aus den vorher gelesenen Dateien (`settings/menu.ini`, die
`menu.ini.append.php` von ezwebin) stammende Liste, sodass nur die folgenden Klassen Menüeinträge sind. `frontpage`
ist die Landingpage-Klasse von ezwebin. Ein Besucher sieht in den Menüs nur noch Ordner und Landingpages; Artikel,
Links und andere Seiten bleiben erreichbar, sind aber keine Menüeinträge mehr. Das gilt für jede Anordnung.

### Eine Seite aus den Menüs ausblenden

Verstecken Sie die Seite in der Inhaltsstruktur (über ihr Kontextmenü: **Verstecken / Zeigen**). Die Menü-Templates
rufen Seiten so ab, wie Besucher sie sehen, daher wird eine versteckte Seite in jedem klassischen Menü ausgelassen,
solange diese Einstellung wie standardmäßig bleibt:

`settings/siteaccess/<siteaccess>/site.ini.append.php`

```ini
[SiteAccessSettings]
ShowHiddenNodes=false
```

Ein Besucher sieht die Seite nicht mehr, weder in den Menüs noch anderswo; Redakteure sehen sie in der Administration
weiterhin. Wenn die Seite auf der Site bleiben, aber aus den Menüs verschwinden soll, geben Sie ihr eine Klasse, die
nicht in den Listen des vorigen Rezepts steht.

### Ein anderes Menü pro Siteaccess

Jeder Siteaccess liest seine eigene `settings/siteaccess/<name>/menu.ini.append.php`. Speichern Sie auf der Seite
einmal pro Siteaccess, oder tragen Sie die Zeilen in die beiden Dateien ein:

`settings/siteaccess/mysite_en/menu.ini.append.php`

```ini
[SelectedMenu]
CurrentMenu=TopOnly
TopMenu=flat_top
LeftMenu=
```

`settings/siteaccess/mysite_de/menu.ini.append.php`

```ini
[SelectedMenu]
CurrentMenu=LeftTop
TopMenu=flat_top
LeftMenu=sub_left
```

Besucher von `mysite_en` erhalten nur ein oberes Menü, Besucher von `mysite_de` ein oberes Menü und eine linke Spalte.

Ein Wert in `settings/override/menu.ini.append.php` wird nach den Siteaccess-Dateien gelesen und hat Vorrang vor allen.
Die Seite warnt, wenn das der Fall ist: Speichern würde dort nichts ändern, bis der Override entfernt ist.

## 4. Welche Designs klassische Menüs zeichnen

Ein Menü erscheint nur dort, wo ein Pagelayout es einbindet; die Einstellungen allein bewirken also nichts. In diesem
Repository:

| Design | Klassische Menüs |
|---|---|
| `design/base` | sein Pagelayout zeichnet sie, mit `{menu name=TopMenu}` und `{menu name=LeftMenu}`; Templates `double_top`, `flat_left`, `flat_top`, `sub_left` |
| `extension/ezwebin/design/ezwebin` | sein Pagelayout zeichnet sie, über `$pagedata.top_menu` und `$pagedata.left_menu` (`page_topmenu.tpl`, `page_leftmenu.tpl`); Templates `double_top`, `flat_left`, `flat_top` |
| `design/simple`, `extension/sevenx_themes_simple/design/simple` | hat Menü-Templates (`double_top`, `dropdown`, `flat_top`), aber die Bereiche für das obere und das seitliche Menü in seinem Pagelayout sind auskommentiert: Die Einstellungen bewirken nichts, bis ein eigenes Pagelayout ein Menü einbindet |
| `extension/sevenx_themes_media/design/media`, `extension/explayouts/design/standard` | rendern über Exponential Layouts; Menüs sind Layout-Blöcke |
| `design/standard`, `design/plain`, `design/mysite`, die Portal-Designs von `expservices` | keine Menü-Templates, und ihre Pagelayouts zeichnen kein klassisches Menü |

Die Seite listet die Designs der Installation auf, in der sie läuft, und ermittelt sie auf dieselbe Weise.

## 5. Verwendung in Ihren Templates

Jedes Beispiel unten ist derselbe Text, den die Seite mit einer Schaltfläche zum Kopieren anzeigt. Jedes wurde über die
Template-Engine mit den Inhalten einer echten Installation gerendert, als anonymer Besucher: ohne Template-Fehler oder
-Warnungen und mit den Links der echten Seiten, die es auflisten muss.

### Die gewählten Menüs aus einem Pagelayout zeichnen

Setzen Sie es dort in Ihre `pagelayout.tpl` ein, wo die Menüs hingehören. `{menu name=TopMenu}` bindet
`design:menu/<TopMenu>.tpl` ein und wird beim Kompilieren des Pagelayouts aufgelöst; deshalb löscht das Speichern auf
der Seite die kompilierten Pagelayouts. Die Schlüssel des cache-block sind die von `design/base`: die Adresse sowie die
Rollen und Einschränkungen des Benutzers, sodass jeder Besucher den ausgewählten Eintrag der Seite erhält und nur das,
was seine Rechte erlauben. Der Siteaccess ist von selbst Teil jedes cache-block-Schlüssels, und der Block verfällt,
sobald Inhalte veröffentlicht werden.

```smarty
{* In your pagelayout.tpl: draw the menus menu.ini [SelectedMenu] names.
   {menu name=TopMenu} includes design:menu/<TopMenu>.tpl, chosen when the page layout is compiled.
   ezwebin's menu templates also read $pagedata and $current_node_id; with that design, define them
   first, as its page layout does:
   {def $pagedata = ezpagedata() $current_node_id = $pagedata.node_id} *}
{def $menu_user = fetch( 'user', 'current_user' )}
{cache-block keys=array( $uri_string, $menu_user.role_id_list|implode( ',' ), $menu_user.limited_assignment_value_list|implode( ',' ) )}
<nav class="topmenu" aria-label="Main">
    {menu name=TopMenu}
</nav>
{if ezini( 'SelectedMenu', 'LeftMenu', 'menu.ini' )}
<nav class="leftmenu" aria-label="Section">
    {menu name=LeftMenu}
</nav>
{/if}
{/cache-block}
{undef $menu_user}
```

### Ein eigenes Template für das obere Menü

Die sichtbaren Unterseiten der Startseite (`content.ini [NodeSettings] RootNode`) aus den Klassen von
`TopIdentifierList`, wobei der Eintrag markiert ist, in dem sich der Besucher befindet. Anders als die
`design/base`-Templates richtet es sich nach `RootNode`.

```smarty
{* design:menu/my_top.tpl: the pages below the start page, of the classes in
   menu.ini [MenuContentSettings] TopIdentifierList, in the start page's sort order.
   fetch() leaves hidden pages out for visitors (ShowHiddenNodes=false). *}
{def $root_id = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $root = fetch( 'content', 'node', hash( 'node_id', $root_id ) )
     $items = fetch( 'content', 'list', hash( 'parent_node_id', $root_id,
                                              'sort_by', $root.sort_array,
                                              'class_filter_type', 'include',
                                              'class_filter_array', ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' ) ) )
     $in_path = first_set( $module_result.path[1].node_id, 0 )
     $here = first_set( $module_result.node_id, 0 )}
{if $items}
<ul class="menu-top">
{foreach $items as $item}
    <li{if eq( $item.node_id, $in_path )} class="selected"{/if}><a href={$item.url_alias|ezurl}{if eq( $item.node_id, $here )} aria-current="page"{/if}>{$item.name|wash}</a></li>
{/foreach}
</ul>
{/if}
{undef $root_id $root $items $in_path $here}
```

### Ein linkes Menü für den aktuellen Bereich

Die Unterseiten der Seite der ersten Ebene, in der sich der Besucher befindet, aus den Klassen von
`LeftIdentifierList`, mit markierter aktueller Seite; auf der Startseite nichts.

```smarty
{* design:menu/my_section_left.tpl: the pages of the first-level page the visitor is in
   ($module_result.path[1]; ezwebin's $pagedata.path_array holds the same path), of the
   classes in menu.ini [MenuContentSettings] LeftIdentifierList. Nothing on the start page. *}
{def $section_id = first_set( $module_result.path[1].node_id, 0 )}
{if $section_id}
{def $section = fetch( 'content', 'node', hash( 'node_id', $section_id ) )
     $items = fetch( 'content', 'list', hash( 'parent_node_id', $section_id,
                                              'sort_by', $section.sort_array,
                                              'class_filter_type', 'include',
                                              'class_filter_array', ezini( 'MenuContentSettings', 'LeftIdentifierList', 'menu.ini' ) ) )
     $here = first_set( $module_result.node_id, 0 )}
<h2><a href={$section.url_alias|ezurl}>{$section.name|wash}</a></h2>
{if $items}
<ul class="menu-left">
{foreach $items as $item}
    <li><a href={$item.url_alias|ezurl}{if eq( $item.node_id, $here )} aria-current="page"{/if}>{$item.name|wash}</a></li>
{/foreach}
</ul>
{/if}
{undef $section $items $here}
{/if}
{undef $section_id}
```

### Wohin die Dateien gehören

Bearbeiten Sie niemals ein mitgeliefertes Design: Ein Update ersetzt es. Eine eigene Design-Extension wird vor den
mitgelieferten Designs gefunden, und der zusätzliche Eintrag in `[MenuSettings]` bietet Ihre Templates auf der Seite
als fünfte Anordnung an.

```ini
# Your own menu templates belong in a design extension, never in a shipped design.
#
# extension/mysite_menus/settings/design.ini.append.php
[ExtensionSettings]
DesignExtensions[]=mysite_menus

# extension/mysite_menus/design/mysite/templates/menu/my_top.tpl
# extension/mysite_menus/design/mysite/templates/menu/my_section_left.tpl
#   ("mysite" is a design in your siteaccess's SiteDesign or AdditionalSiteDesignList)

# settings/siteaccess/<siteaccess>/menu.ini.append.php
# a fifth arrangement, offered on Design > Menus (classic) from then on
[MenuSettings]
AvailableMenuArray[]=MyTopAndSection

[MyTopAndSection]
TitleText=My top and section menus
TopMenu=my_top
LeftMenu=my_section_left
```

Leeren Sie nach dem Hinzufügen der Dateien den Template-Override-Cache und den INI-Cache (`php bin/php/ezcache.php
--clear-id=template-override --allow-root-user` und `--clear-tag=ini`), und führen Sie `exp:velocity cache clear` aus,
wenn Velocity die Site ausliefert.

### Mit Exponential Layouts

Ein Siteaccess, der über Layouts rendert, zeichnet seine Menüs mit Blöcken in einer Layout-Zone; keines dieser
Templates gilt dort. Klassische und Layouts-Siteaccesses können in einer Installation nebeneinander bestehen, jeder mit
seinem eigenen Design.

## 6. Was das Speichern bewirkt

1. Es übernimmt die Anordnung nur, wenn sie zu `AvailableMenuArray` gehört; alles andere, was gesendet wird, wird mit
   einer Meldung abgelehnt.
2. Es prüft, ob der Siteaccess zu `RelatedSiteAccessList` gehört, ob sein Name aus Buchstaben, Ziffern, `_` und `-`
   besteht und ob `settings/siteaccess/<name>` existiert und, mit aufgelösten Links, innerhalb von
   `settings/siteaccess` liegt.
3. Es schreibt die drei Zeilen von `[SelectedMenu]` über den INI-Editor von `exp:ini`: Nur die betroffenen Zeilen
   ändern sich, Kommentare, andere Abschnitte und der Rahmen `<?php /* ... */ ?>` bleiben erhalten, eine fehlende Datei
   wird mit dem Rahmen angelegt, die alte Datei wird nach `var/backup/ini` kopiert, Eigentümer und Zugriffsrechte
   bleiben erhalten, und die Änderung wird im Audit-Protokoll festgehalten (`system.setting.write`).
4. Es liest die Datei zurück; enthält sie die drei Werte nicht oder ist der Rahmen beschädigt, stellt es die alte Datei
   wieder her.
5. Es leert die INI-Caches (`global_ini`, `ini`), die kompilierten Pagelayouts im Cache-Verzeichnis des Siteaccess und
   die Template-Blöcke. Jede Anfrage liest die Einstellungen neu ein, unter Apache wie unter Velocity, daher braucht
   nichts einen Neustart; Seiten, die in einem Antwort-Cache liegen, zeigen die alten Menüs, bis dieser Cache geleert
   ist.

Vor 6.0.15 speicherte die Seite jede `menu.ini`-Einstellung, die der Siteaccess las, in diese Datei, wodurch alle
anderen Menüeinstellungen (darunter die Reiter der Administration) für den Siteaccess eingefroren wurden. Jetzt
schreibt sie nur die drei Zeilen.

Die Seite ist ein Link im linken Menü des Reiters Design (`menu.ini [Leftmenu_design]`, Schlüssel `menu_management`,
nach Werkzeugleisten Verwaltung) und markiert den Reiter Design. Bis 6.0.15 war sie ein Link im Setup-Menü, in der
Gruppe „Website und URLs“; ihre Adresse ist unverändert.

Das Formular behält seine alten Feldnamen (`CurrentSiteAccess`, `SelectCurrentSiteAccessButton`, `MenuType`,
`StoreButton`) und funktioniert ohne JavaScript. `/visual/menuconfig/(siteaccess)/<name>` öffnet die Seite für einen
Siteaccess. Die Seite erfordert die Rechte des Moduls `visual`, und der Editor-Siteaccess verweigert dieses Modul wie
bisher.

## 7. Ein kurzer geschichtlicher Abriss

Die klassischen Designs bauen jede Seite aus einem Pagelayout auf, und dieses Pagelayout fragt `menu.ini`, welche
Menüs es zeichnen soll. Diese Seite wählt das seit den ersten Versionen aus: vier Anordnungen aus einem oberen und
einem linken Menü, gespeichert pro Siteaccess.

Exponential Layouts hat das für neue Sites abgelöst: Ein Layout enthält Zonen und Blöcke, ein Menü ist einer der
Blöcke, und eine Regel wählt das Layout für jede Seite aus. Ein so gebauter Siteaccess liest diese Einstellungen nie.
Die Seite bleibt für Sites erhalten, die noch ein klassisches Design verwenden, und seit 6.0.15 gibt sie für jeden
Siteaccess an, ob sie dort von Bedeutung ist.

## 8. Wenn sich etwas nicht wie erwartet verhält

| Sie sehen | Warum | Was zu tun ist |
|---|---|---|
| Das Speichern meldet Erfolg, die Site ist unverändert | der Siteaccess rendert über Layouts, oder sein Design zeichnet kein klassisches Menü | lesen Sie die Kennzeichnung des Siteaccess; verwenden Sie den Layout-Editor |
| Die Seite warnt, dass `settings/override` den Wert bestimmt | `settings/override/menu.ini.append.php` setzt `[SelectedMenu]` | entfernen Sie die Zeilen dort |
| Das obere Menü listet die falschen Seiten auf | ein `design/base`-Template auf einer Site, deren Startseite nicht Knoten 2 ist | verwenden Sie die Templates von ezwebin oder das Beispiel in Abschnitt 5 |
| Eine Seite fehlt im Menü | versteckt, oder ihre Klasse steht nicht in `TopIdentifierList` / `LeftIdentifierList` | machen Sie sie wieder sichtbar oder fügen Sie ihre Klasse hinzu |
| „Der Webserver kann nicht schreiben“ | die Datei oder das Verzeichnis ist für den PHP-FPM-Benutzer nicht beschreibbar | korrigieren Sie den Eigentümer, oder tragen Sie die Zeilen von Hand in die Datei ein |

## Quellen

- Seite: `kernel/visual/menuconfig.php`, `kernel/private/classes/views/visual/menuconfig.php`,
  `design/admin/templates/visual/menuconfig.tpl` und `design/admin4/templates/visual/menuconfig.tpl`
- Erkennung: `kernel/classes/classicmenu/expclassicmenusiteaccessinspector.php`
- Lesen und Schreiben: `kernel/classes/classicmenu/expclassicmenusettings.php`; Template-Beispiele in
  `kernel/classes/classicmenu/examples/`
- Tests: `tests/tests/kernel/classes/classicmenu/expClassicMenuSettingsTest.php`
- Einstellungen: `settings/menu.ini` (`[MenuSettings]`, `[SelectedMenu]`, die vier Anordnungsgruppen,
  `[MenuContentSettings]`), `extension/ezwebin/settings/menu.ini.append.php`
- Templates: `design/base/templates/pagelayout.tpl`, `design/base/templates/menu/*.tpl`,
  `extension/ezwebin/design/ezwebin/templates/menu/*.tpl`, `lib/eztemplate/classes/eztemplatemenufunction.php`,
  `extension/ezwebin/autoloads/ezpagedata.php`
