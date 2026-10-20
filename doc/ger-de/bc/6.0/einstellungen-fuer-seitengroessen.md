# Wo die Seitengrößen stehen

Lesen Sie diese Seite, wenn Sie ändern möchten, wie viele Zeilen eine Liste in der Administrationsoberfläche zeigt,
oder wenn Sie ein Listen-Template oder ein Modul überschrieben haben, in das früher seine Seitengrößen fest
eingetragen waren. Jede Liste mit Seiten in der Administration nimmt ihre Seitengröße jetzt aus einer Einstellung,
sodass eine Website ihre eigene wählen kann, ohne ein Template oder ein Modul zu bearbeiten. Diese Seite ist das
Verzeichnis dieser Einstellungen.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Seitengrößen kommen aus Einstellungen: meist `admininterface.ini [PaginationSettings]`, dazu fünf ältere Einstellungen. In `role/list` und `rss/list` sind die Größen nicht mehr fest in Modul und Template eingetragen. |
| Wer betroffen ist | Websites, die andere Seitengrößen möchten; Overrides der Templates von `role/list` oder `rss/list`. Gespeicherte Wahlen von Benutzern funktionieren weiter. |
| Wie Sie es prüfen | `grep -n -A40 "^\[PaginationSettings\]" settings/admininterface.ini` |
| Wie Sie es beheben | Nichts ist nötig. Um eine Größe zu ändern, überschreiben Sie den Schlüssel und leeren die Caches (Beispiel unten). |

## Eine Seitengröße in einer Minute ändern

50 Sektionen pro Seite statt 25 anzeigen:

```ini
# settings/override/admininterface.ini.append.php
[PaginationSettings]
ItemsPerPage[section/list]=50
```

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
```

Laden Sie `section/list` neu: Es zeigt 50 Zeilen.

## Die Einstellungen

Die meisten stehen in einem Block, `admininterface.ini [PaginationSettings]`, geschlüsselt nach Modul und View, die
die Liste zeichnen, sodass Sie eine Einstellung über die Adresse der Seite finden, die sie steuert.

```ini
[PaginationSettings]
DefaultItemsPerPage=25
ItemsPerPage[section/list]=25
ItemsPerPage[shop/archivelist]=50
```

| View | Schlüssel |
|---|---|
| `setup/extensions` | `ItemsPerPage[setup/extensions]` |
| `setup/cronjobs` | `ItemsPerPage[setup/cronjobs]` |
| `section/list` | `ItemsPerPage[section/list]` |
| `state/groups` | `ItemsPerPage[state/groups]` |
| `workflow/grouplist` | `ItemsPerPage[workflow/grouplist]` |
| `workflow/processlist` | `ItemsPerPageList_workflow_processlist[]` (10, 25; `ItemsPerPage[workflow/processlist]=10` wird ebenfalls ausgeliefert) |
| `oauthadmin/list` | `ItemsPerPage[oauthadmin/list]` |
| `package/list` | `ItemsPerPage[package/list]` |
| `pdf/list` | `ItemsPerPage[pdf/list]` |
| `content/translations` | `ItemsPerPage[content/translations]` |
| `infocollector/overview` | `ItemsPerPage[infocollector/overview]` |
| `class/grouplist` | `ItemsPerPage[class/grouplist]` |
| `class/classlist` | `ItemsPerPage[class/classlist]` |
| `shop/orderlist` | `ItemsPerPage[shop/orderlist]` |
| `shop/customerlist` | `ItemsPerPage[shop/customerlist]` |
| `shop/archivelist` | `ItemsPerPage[shop/archivelist]` |
| `shop/discountgroup` | `ItemsPerPage[shop/discountgroup]` |
| `shop/status` | `ItemsPerPage[shop/status]` |
| `shop/vattype` | `ItemsPerPage[shop/vattype]` |
| `shop/vatrules` | `ItemsPerPage[shop/vatrules]` |
| `shop/productcategories` | `ItemsPerPage[shop/productcategories]` |
| `shop/currencylist` | `ItemsPerPageList_shop_currencylist[]` |
| `shop/productsoverview` | `ItemsPerPageList_shop_productsoverview[]` |
| `search/stats` (Suchstatistik) | `ItemsPerPageList_search_stats[]` (10, 25, 50) |
| `explayouts_ui/layout_list` | `ItemsPerPage[explayouts_ui/layout_list]` |
| `explayouts_ui/shared_layouts_list` | `ItemsPerPage[explayouts_ui/shared_layouts_list]` |
| `explayouts_ui/components` | `ItemsPerPage[explayouts_ui/components]` |
| `explayouts_ui/rule_list` | `ItemsPerPage[explayouts_ui/rule_list]` |

Die vier Einträge für `explayouts_ui` liefert die eigene `settings/admininterface.ini.append.php` dieser Erweiterung
mit, sodass ihre Verwendung nicht bedeutet, Einträge von Hand zu den Einstellungen des Kernels hinzuzufügen.

Diese fünf sind älter als der Block und behalten eigene Einstellungen:

| View | Einstellung | Datei | Standard |
|---|---|---|---|
| Tab Orte eines Elements | `[LocationsSettings] LocationsPerPage` | `content.ini` | 25 |
| Richtlinien auf `role/edit` und `role/view` | `[RoleSettings] PoliciesPerPage` | `site.ini` | 25 |
| Benutzer und Gruppen einer Rolle auf `role/view` ([Blättern und Filter](../../../eng-us/features/6.0/role-assignment-paging.md)) | `[RoleSettings] AssignmentsPerPage` (0: alle) | `site.ini` | 50 |
| Vorschau der Richtlinien pro Rolle, bei einem Benutzer oder einer Benutzergruppe | `[RoleSettings] PolicyPreviewPerRole` | `site.ini` | 10 |
| `role/list` | `[RoleSettings] RolesPerPageList[]` | `site.ini` | 10, 25, 50 |
| `rss/list` (Exporte und Importe) | `[RSSListSettings] ItemsPerPageList[]` | `content.ini` | 25, 50, 250 |

Überschreiben Sie jede davon pro Siteaccess wie jede andere Einstellung — `settings/siteaccess/<name>/` oder
`settings/override/`.

## Bewusst ohne Seiten

`shop/preferredcurrency` ist eine Auswahlliste, um eine Währung zu wählen, keine Liste von Zeilen. Sie in Seiten
aufzuteilen würde sie unbrauchbar machen.

## Wo gelesen wird

`expAdminPagination` beantwortet alles:

```php
expAdminPagination::limit( 'section/list' );          // one size
expAdminPagination::sizes( 'shop/currencylist' );     // the sizes a selector offers
expAdminPagination::chosen( $view, $preferenceName ); // size, choice, sizes
expAdminPagination::offset( $Params );                // the page asked for
expAdminPagination::page( $rows, $offset, $limit );   // one page of a list in memory
```

`offset()` liest die Benutzerparameter ebenso wie ein deklariertes `Offset`, sodass eine Liste in Seiten aufgeteilt
werden kann, ohne die Moduldefinition zu ändern, zu der sie gehört.

`page()` ist die zweitbeste Lösung und sagt das auch: Es begrenzt, was **gezeichnet** wird, und das bringt eine Seite
mit Hunderten von Zeilen herunter, aber nicht, was **gelesen** wird. Es wird dort verwendet, wo das Modell keinen
Fetch mit Offset und Limit hat. Wo es einen gibt, wird stattdessen dieser verwendet — `role/list`, `section/list`,
`state/groups`, `oauthadmin/list` und der Tab Orte teilen alle in der Abfrage in Seiten auf.


## Eine Größe oder eine Liste davon

Oben erscheinen zwei Formen, weil es zwei Arten von Bildschirmen gibt.

Ein Bildschirm **ohne Auswahl der Einträge pro Seite** nimmt eine einzelne Ganzzahl:

```ini
[LocationsSettings]
LocationsPerPage=25
```

Ein Bildschirm **mit** einer Auswahl nimmt die Liste der Größen, die er anbietet; die erste erhält, wer noch nie
gewählt hat:

```ini
[RoleSettings]
RolesPerPageList[]
RolesPerPageList[]=10
RolesPerPageList[]=25
RolesPerPageList[]=50
```

Die Liste ist auch das, was die Abfrage begrenzt. Eine Größe, die nicht darauf steht, lässt sich auch nicht über die
URL anfordern; eine Seite größer zu erlauben geht daher nur, indem Sie die Liste verlängern.

Beide Formen fallen auf ihren Standard zurück, wenn die Einstellung geleert oder mit etwas gefüllt wird, das keine
positive Zahl ist — eine Liste ohne verwendbare Größe würde sonst weiter unten durch null teilen.


## Was die Benutzereinstellung speichert

Die Auswahl merkt sich eine Wahl pro Benutzer, in `admin_role_list_limit` und `admin_rss_list_limit`.

`role/list` speichert **die Position in der Liste, von eins an gezählt** — nicht die Größe. Das hat es schon immer
gespeichert: Die Werte 1, 2 und 3 bedeuteten die erste, zweite und dritte Größe aus einer fest im Modul eingetragenen
Menge. Die Bedeutung beizubehalten heißt, dass eine Website, die `RolesPerPageList` ändert, die Wahl aller behält,
statt sie zurückzusetzen, und dass keine gespeicherte Einstellung migriert werden muss.

`rss/list` speichert die Größe selbst und ignoriert eine gespeicherte Größe, die nicht mehr auf der Liste steht.


## Was das ersetzt hat

In `role/list` waren die Größen fest im Modul eingetragen:

```php
switch( eZPreferences::value( 'admin_role_list_limit' ) )
{
    case '2': { $limit = 25; } break;
    case '3': { $limit = 50; } break;
    default:  { $limit = 10; } break;
}
```

und **noch einmal im Template**, als `choose( 10, 10, 25, 50 )` für die Auswahl. Zwei Listen, die von Hand
übereinstimmen mussten, und keine Möglichkeit, eine davon zu ändern.

In `rss/list` gab `eZRSSListPager::limits()` `array( 25, 50, 250 )` zurück.


## So prüfen Sie es

Die Einstellungen zu ändern und neu zu laden, ist der Test:

| Gesetzt auf | Die Seite zeigt dann |
|---|---|
| `RolesPerPageList = 3, 7, 11, 99` | eine Auswahl `3 7 11 99` und 3 Zeilen |
| `ItemsPerPageList = 4, 8` | eine Auswahl `4 8` |
| `LocationsPerPage = 7` | 7 Zeilen |

Nach jeder Änderung an einer Einstellungsdatei ausführen:

```
php bin/php/ezcache.php --clear-all --allow-root-user
```

Die ausgelieferten Werte lesen Sie mit `grep -n -A40 "^\[PaginationSettings\]" settings/admininterface.ini`.

## Verwandte Seiten

- [Blättern, Sortieren und Seitengrößen in der Administrationsoberfläche](../../../eng-us/features/6.0/admin-list-paging.md)
- [Blättern in Rollen und Richtlinien](seitenweise-anzeige-von-rollen-und-richtlinien.md) und [Der Tab Orte: Blättern und Sortieren](tab-orte-blaettern-und-sortieren.md)
- [September 2026, erste Hälfte: 15. September](../../../eng-us/history/2026/2026-09a.md#15-september-paging-everywhere)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
