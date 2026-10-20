# nxc_powercontent: Inhalte aus Code und über REST anlegen und aktualisieren

Diese Seite ist für Entwickler, die Inhalte aus Skripten oder über REST anlegen oder ändern. `nxc_powercontent` („NXC
Powercontent“, ursprünglich von NXC) erweitert, wie Code Inhalte anlegt, aktualisiert und entfernt:

- Dateien aus externen Quellen herunterladen, wenn Objekte angelegt oder aktualisiert werden;
- HTML für Attribute vom Typ `ezxmltext` übergeben;
- Platzierungen setzen oder ändern (Haupt- und zusätzliche Platzierungen);
- eine eingebaute Debug-Funktion, praktisch in Kommandozeilenskripten;
- **beim Aktualisieren eines Objekts wird keine neue Version angelegt**.

In der Zeit von Exponential 6 wurde sie für die REST-Extension [ezprestapi](../../../../eng-us/features/6.0/extensions/ezprestapi.md) erweitert, für die sie die
Aufrufe zum Anlegen und Löschen der REST API v2 bereitstellt (1.1.0, Oktober 2024; Aktualisieren wird geprüft und
antwortet dann mit 501). Seit `ezprestapi` 1.2.6 bedient diese eine Extension REST API v1 und v2; siehe
[REST API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md).

## Inhaltsklassen in einem Template auflisten: `class_list`

Seit 1.3.0 (12. Juli 2026) listet die Fetch-Funktion `class_list` von `content` Inhaltsklassen ohne eigenes PHP auf. Sie
filtert nach Gruppe, sortiert nach Namen und blättert, wie die Klassenliste der Administrationsoberfläche:

```
{def $classes = fetch('content', 'class_list', hash('as_object', false(), 'sorts', true()))}
{foreach $classes as $class}
    {$class.name}
{/foreach}
```

| Parameter | Bedeutung |
|---|---|
| `as_object` | Objekte oder einfache Arrays zurückgeben |
| `group_list` | Auf Klassengruppen beschränken |
| `sorts` | Nach Namen sortieren |
| `limit` | Anzahl der Klassen |

Verwenden Sie sie für Admin-Dashboards, eigene Navigation oder API-Antworten.

## Administrationsoberfläche: Auswahl kopieren, Auswahl verstecken und einblenden

Die Extension überschreibt die Ansicht `content/action` des Kernels mit ihrer eigenen `modules/content/action.php` (dem
Modul `content` der Extension). Dieser Kopie fehlten die Handler für `CopyButton` und `HideButton`/`UnhideButton`,
sodass im Menü **Weitere Aktionen** der Unterelemente in admin3 **Auswahl kopieren** und **Auswahl
verstecken/einblenden** bei „Unknown content object action“ landeten und still scheiterten.

Seit 1.2.0 (21. Juni 2026) gibt es beide Handler:

- **Kopieren** prüft `canCreate`, sammelt Metadaten zu Klasse, Sektion und Knoten für die Einschränkungen der Auswahl und
  zeigt die Zielauswahl.
- **Verstecken/Einblenden** prüft `can_hide`, überspringt Knoten, die schon im Zielzustand sind, und kehrt zur Ansicht
  des übergeordneten Knotens zurück.

Siehe [Menü „Weitere Aktionen“ der Unterelemente](../../../../eng-us/bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md).

## Andere Änderungen

| Version | Datum | Änderung |
|---|---|---|
| 1.1.0 und später | Dezember 2025 | Korrektur eines nullbaren Typs für PHP 8.5. |
| 1.4.0 | 22. September 2026 | Modulansichten sind auf einem dauerhaften Worker (Velocity) sicher. |
| 1.4.1 | | Die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation. |
| 1.4.2 | 2. Oktober 2026 | Der **PDF-Export** endet nicht mehr mit einem fatalen Fehler: Die Informationen zum Inhalts-Cache werden aus einer Objektinstanz gelesen, wie PHP 8 es verlangt. Siehe [PDF-Export](../pdf-export.md). |
| 1.4.3 | | `content/edit` deklariert seinen Weiterleitungshelfer nur einmal, sodass ein dauerhafter PHP-Worker es mehr als einmal ausführen kann. |

## Verwandte Seiten

- [ezprestapi](../../../../eng-us/features/6.0/extensions/ezprestapi.md) und [REST API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)
- [Ausgewählte Unterelemente kopieren](../unterelemente-auswahl-kopieren.md)
- [Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md)
- [Chronik](../../../../eng-us/history/extensions/nxc_powercontent.md) und [Versionshinweise](../../../../changelogs/extensions/nxc_powercontent.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/nxc_powercontent.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2024-10](../../../../eng-us/history/extensions/months/2024-10.md), [2025-12](../../../../eng-us/history/extensions/months/2025-12.md), [2026-06](../../../../eng-us/history/extensions/months/2026-06.md), [2026-07](../../../../eng-us/history/extensions/months/2026-07.md), [2026-09](../../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
