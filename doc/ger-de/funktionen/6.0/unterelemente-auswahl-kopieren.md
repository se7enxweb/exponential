# Ausgewählte Unterelemente kopieren

Diese Seite ist für Redakteure, die mehrere Elemente auf einmal kopieren. **Auswahl kopieren** kopiert mehrere Elemente
mit allem, was darunter liegt, in einem Schritt an eine neue Stelle im Baum. Vor Januar 2025 konnte die
Unterelementliste die ausgewählten Elemente entfernen oder verschieben, aber nicht kopieren.

## Verwenden

1. Öffnen Sie einen Knoten in der Administrationsoberfläche. Haken Sie in der Liste **Unterelemente** die
   Kontrollkästchen der zu kopierenden Elemente an.
2. Öffnen Sie das Menü **Weitere Aktionen** über der Liste und wählen Sie **Auswahl kopieren**.
3. Ein Auswahldialog fragt nach dem neuen übergeordneten Knoten. Wählen Sie ihn und bestätigen Sie.
4. Exponential kopiert jeden ausgewählten Knoten und seinen Teilbaum unter den neuen übergeordneten Knoten und zeigt,
   wenn Benachrichtigungen eingeschaltet sind, eine Zusammenfassung dessen, was kopiert wurde.

Dieselbe Aktion steht im Kontextmenü eines einzelnen Elements und in der Ansicht `content/copysubtree`.

## Was zuerst geprüft wird

- Der Benutzer muss jeden ausgewählten Knoten lesen können und für die Klasse jeder Kopie unter dem gewählten
  übergeordneten Knoten die Berechtigung **create** haben.
- Ein Knoten kann nicht in sich selbst oder einen seiner eigenen Nachfahren kopiert werden.
- Ist die Kopie größer als `MaxNodesCopySubtree`, lehnen aktuelle Releases sie nicht ab: Die Kopieransicht bietet an,
  sie als Inhaltsauftrag im Hintergrund auszuführen, der an kein Zeitlimit einer Anfrage stoßen kann (siehe
  [Inhaltsaufträge](inhaltsauftraege.md)). Die erste Version der Funktion (Januar 2025) kopierte höchstens so viele Knoten.

## Einstellungen

Alle Schlüssel stehen in `settings/content.ini`, Block `CopySettings`. Geltungsbereich: Override oder Siteaccess.

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `MaxNodesCopySubtree` | `30` | Höchstzahl der Knoten, die eine Anfrage zum Kopieren eines Teilbaums verarbeitet |
| `VersionHandling` | `all` | `user-defined`, `last-published` oder `all`: welche Versionen jedes Objekts kopiert werden |
| `CreatorHandling` | `current` | `user-defined`, `keep-unchanged` oder `current`: wer als Ersteller der Kopien erfasst wird |
| `TimeHandling` | `current` | `user-defined`, `keep-unchanged` oder `current`: welche Zeitstempel die Kopien erhalten |
| `ShowCopySubtreeNotification` | `enabled` | Nach dem Kopieren die Ergebnisseite zeigen |

## Wo es im Code liegt

| Teil | Datei |
|---|---|
| Menüeintrag (Wert 2, Formularfeld `CopyButton`, Beschriftung „Copy selected“) | Beschriftung in `design/admin/templates/children_detailed.tpl` und `design/admin4/templates/children_detailed.tpl`; das Menü und sein Absenden in `design/admin4/javascript/ezajaxsubitems_expdatatable.js` (das Skript `ezajaxsubitems_datatable.js` von 2025 gibt es nicht mehr) |
| Aktionen `CopyButton` und `CopyNode` (Auswahl des Ziels) | `kernel/private/classes/views/content/action.php` |
| Operation | `eZContentOperationCollection::copyNode()` in `kernel/content/ezcontentoperationcollection.php` und `eZContentObjectTreeNodeOperations::copySubtree()` in `kernel/classes/ezcontentobjecttreenodeoperations.php` |
| Ergebnisseite | `design/standard/templates/content/copy_subtrees_notification.tpl` |
| Ansicht | `kernel/content/module.php`, Ansicht `copysubtree` (braucht die Funktion `create`) |

## Verwandte Seiten

- [Inhaltsaufträge](inhaltsauftraege.md) für große Kopien
- [Ausgewählte ausblenden und einblenden](../../../eng-us/bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [Optionen der Unterelementtabelle](optionen-der-unterelementtabelle.md)
- [Das responsive Admin-Design (admin3)](admin3-responsives-admin.md), [das Design admin4](../../../eng-us/features/6.0/admin4-design.md), [Breite und Schriftgröße der linken Seitenleiste](admin3-seitenleistenbreite-und-schriftgroesse.md)
- [Changelog 6.0.7](../../../changelogs/6.0/6.0.7.md)
- Chronik: [August 2024](../../../eng-us/history/2024/2024-08.md), [Oktober 2024](../../../eng-us/history/2024/2024-10.md), [November 2024](../../../eng-us/history/2024/2024-11.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md), [Juni 2025](../../../eng-us/history/2025/2025-06.md), [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
