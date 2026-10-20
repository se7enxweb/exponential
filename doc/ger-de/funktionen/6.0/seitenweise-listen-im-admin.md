# Blättern, Sortieren und Seitengrößen in der Administrationsoberfläche

Diese Seite ist für Administratoren großer Installationen und für alle, die in der Administrationsoberfläche längere
oder kürzere Listen möchten. Jede lange Liste der Administrationsoberfläche wird jetzt seitenweise angezeigt, und ihre
Seitengröße ist eine Einstellung. Vorher konnte eine Installation mit vielen Websites, Tausenden von Platzierungen oder
Tausenden von Rollen manche dieser Seiten gar nicht ausgeben: Die ganze Liste wurde gelesen, und für jede Zeile lief eine
Abfrage, bevor das erste Byte gesendet wurde.

## Eine Seitengröße ändern

Alle Größen stehen in `admininterface.ini`, Block `[PaginationSettings]`, mit `module/view` als Schlüssel:

```ini
# settings/override/admininterface.ini.append.php
[PaginationSettings]
DefaultItemsPerPage=25
ItemsPerPage[section/list]=50
ItemsPerPage[class/classlist]=100
```

Leeren Sie nach der Änderung die Caches:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
```

Um die Schlüssel zu sehen, die Ihre Installation mitbringt:

```bash
grep -n -A40 "^\[PaginationSettings\]" settings/admininterface.ini
```

Die vollständige Tabelle der Schlüssel, die vier Einstellungen, die älter als der Block sind, und die eine Liste, die
absichtlich nicht seitenweise angezeigt wird, stehen in
[Wo die Seitengrößen liegen](../../../eng-us/bc/6.0/pagination-settings.md).

## In der Unterelementliste eine eigene Größe wählen

Der Bereich **Einstellungen** (Table options) der Unterelementliste hat ein Feld **Individuell**: jede ganze Zahl von 1
bis 10000, gespeichert als Benutzereinstellung. Siehe
[Unterelementliste: eigene Seitengröße](../../../eng-us/features/6.0/subitems-table-options.md#custom-page-size).

## Was jetzt seitenweise angezeigt wird

| Liste | Hinweise |
|---|---|
| Reiter Platzierungen eines Elements | [Blättern und Sortieren im Reiter Platzierungen](../../../eng-us/bc/6.0/locations-tab-paging-and-sorting.md) |
| Rollen und die Richtlinien einer Rolle | [Blättern in Rollen und Richtlinien](../../../eng-us/bc/6.0/role-policy-paging.md); die Rollenliste zeigt außerdem die ID der Rolle und sortiert |
| Die Benutzer und Gruppen einer Rolle | [Seitenweise Rollenzuweisungen](../../../eng-us/features/6.0/role-assignment-paging.md): nach Namen sortiert, mit einem Namensfilter; die Rollenliste zählt sie pro Rolle |
| Klassenliste, Liste der Klassengruppen, Liste der Workflow-Gruppen, Liste der PDF-Exporte | keine wurde vorher seitenweise angezeigt |
| Rabattgruppen, Bestellstatus, MwSt.-Regeln, Produktkategorien | lasen vorher ihre ganze Liste |
| MwSt.-Typen, Übersetzungen, Extensions, Cronjob-Skripte, REST-Anwendungen | lasen vorher ihre ganze Liste |
| Paketliste | hatte eine Seitengröße, aber keine Seitennavigation, um Seite zwei zu erreichen, und gab auf jeder Seite jedes Paket aus |
| Sektionsliste, Übersicht der gesammelten Informationen, Zustandsgruppen, Suchstatistik | auf den Einstellungsblock umgestellt |
| RSS-Liste | [RSS-Liste](rss-podcast-und-feed-liste.md) |
| Layout-Listen des Layout-Editors | seitenweise über Einträge von `explayouts_ui` im selben Block |

Außerdem:

- Die Extension-Liste sortiert nach dem eigenen Namen der Extension und ihrer Lizenz und behält die Sortierung in der
  Adresse, sodass das Blättern sie nicht verliert.
- Die Reiter der Elementansicht laden nicht mehr alles, was sie zählen.
- In der Rollenansicht ist das Fenster der Richtlinien, das tatsächlich ausgegeben wird, dasjenige, das begrenzt ist.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/admininterface.ini` | `PaginationSettings` | `DefaultItemsPerPage` | `25` | global |
| `settings/admininterface.ini` | `PaginationSettings` | `ItemsPerPage[<module>/<view>]` | `25` (10 für `workflow/processlist` und `state/groups`, 15 für `shop/orderlist` und `shop/customerlist`, 50 für `shop/archivelist`) | global |
| `settings/content.ini` | `LocationsSettings` | `LocationsPerPage` | `25` | global |
| `settings/site.ini` | `RoleSettings` | `PoliciesPerPage`, `PolicyPreviewPerRole`, `RolesPerPageList[]` | `25`, `10`, `10`/`25`/`50` | global |

## Für Entwickler

Die Module lesen ihre Größe über einen Helfer statt über eine im Modul geschriebene Zahl. Der Helfer liefert den Listen
außerdem ihre Blätterparameter und die vom Benutzer gewählte Seitengröße. Siehe „Where the reading happens“ in
[Wo die Seitengrößen liegen](../../../eng-us/bc/6.0/pagination-settings.md).

## Verwandte Seiten

- [Wo die Seitengrößen liegen](../../../eng-us/bc/6.0/pagination-settings.md), [Blättern in Rollen und Richtlinien](../../../eng-us/bc/6.0/role-policy-paging.md), [Blättern und Sortieren im Reiter Platzierungen](../../../eng-us/bc/6.0/locations-tab-paging-and-sorting.md)
- [Unterelementliste: Spalten, Voreinstellungen und CSV-Export](../../../eng-us/features/6.0/subitems-table-options.md), [Rollen: Richtlinien-IDs, Sortierung und Schaltflächen für die Reihenfolge](reihenfolge-der-richtlinien-einer-rolle.md)
- [Mehrfachbearbeitung (Elemente aus der Unterelementliste)](../../../eng-us/bc/6.0/multi-node-edit.md)
- [Sektionen](../../anleitungen/sektionen.md): die Sektionsliste, ihre Seitengröße und die anderen Sektionsseiten
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md) (eigene Seitengröße), [September 2026, erste Hälfte: Blättern überall](../../../eng-us/history/2026/2026-09a.md#15-september-paging-everywhere)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
