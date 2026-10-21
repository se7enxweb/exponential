# ngclasslist: ein Datentyp, der eine Liste von Inhaltsklassen speichert

Diese Seite ist für Website-Entwickler, die Redakteure wählen lassen, welche Arten von Inhalten eine Seite auflistet.
`ngclasslist` (Netgen Class List Datatype) lässt einen Redakteur eine Liste von Inhaltsklassen auswählen und speichert
sie in einem Attribut.

## Beispiel: eine Kategorie, die ausgewählte Klassen auflistet

1. Fügen Sie der Klasse Category ein Attribut vom Typ `ngclasslist` hinzu, hier mit dem Bezeichner `class_filter_array`.
2. Wählen Sie in der Kategorie die aufzulistenden Klassen aus.
3. Holen Sie im Template der Kategorie die Kinder mit den ausgewählten Klassen als Filter:

```
{def $children = fetch(
    'content', 'list',
    hash(
        'parent_node_id', $node.data_map.parent_node_id.content,
        'class_filter_type', $node.data_map.class_filter_type.content,
        'class_filter_array', $node.data_map.class_filter_array.content.class_identifiers
    )
)}
```

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.0.1, 1.1.1 | 31. August bis 2. September 2026 | Paketname und Lizenz in `composer.json` (se7enxweb); der Installer ist der Installer von se7enxweb; Beschreibung aktualisiert; Version in `ezinfo.php`. |
| 1.1.2 | 27. September 2026 | Die Extension gibt ihre Version, Lizenz und Website an. |
| 1.1.3 | 2. Oktober 2026 | Englische und deutsche Übersetzungen für jeden Text, den die Administrationsoberfläche unübersetzt zeigte. |

## Verwandte Seiten

- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/ngclasslist.md) und [Versionshinweise](../../../../changelogs/extensions/ngclasslist.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ngclasslist.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-08](../../../geschichte/erweiterungen/monate/2026-08.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
