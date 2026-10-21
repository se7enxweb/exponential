# Unterelemente, „Weitere Aktionen“: Auswahl verstecken und Auswahl einblenden

Lesen Sie diese Seite, wenn Sie die Liste der Unterelemente von admin3, ihre Templates oder das Modul `content/action`
pflegen. Sie beschreibt zwei neue Sammelaktionen im Menü **Weitere Aktionen**. Die Änderung ist abwärtskompatibel:
Nichts geht kaputt, und es gibt nichts zu migrieren.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Die Liste der Unterelemente von admin3 hat **Auswahl verstecken** und **Auswahl einblenden** im Menü **Weitere Aktionen**. |
| Wer betroffen ist | Redakteure (neue Aktionen). Entwickler, die die Liste der Unterelemente oder die View `content/action` überschreiben. |
| Wie Sie es prüfen | Wählen Sie einige Unterelemente aus, wählen Sie **Weitere Aktionen > Auswahl verstecken** und prüfen Sie, dass die Knoten versteckt sind. |
| Wie Sie es beheben | Nichts zu beheben. Wenn Sie die unter „Halten Sie diese Teile abgestimmt“ aufgeführten Teile überschreiben, fügen Sie dort die neuen Aktionen hinzu. |

## Was sich geändert hat

Das Menü **Weitere Aktionen** der Liste der Unterelemente von admin3 hat zwei neue Einträge:

- **Auswahl verstecken**
- **Auswahl einblenden**

Sie funktionieren wie die anderen Sammelaktionen in diesem Menü. Sie senden die IDs der ausgewählten Knoten über die
Modul-View `content/action` und leiten zum Elternknoten zurück, wenn der Vorgang abgeschlossen ist.

## Wie es funktioniert

- Der tatsächlich verwendete Weg ist das Override des Moduls `content` in `extension/nxc_powercontent`
  (`extension/nxc_powercontent/modules/content/action.php`). Das `content/action.php` des Kernels hat denselben Zweig
  zum Verstecken und Einblenden, aber das Override der Erweiterung ist das, das läuft.
- Der Handler akzeptiert `HideButton` oder `UnhideButton`, liest `SelectedIDArray` und ändert den Versteckt-Zustand
  über die Schicht der Inhaltsoperationen.
- Die Menüeinträge und die gesendete Schaltfläche stammen aus `design/admin/javascript/ezajaxsubitems_expdatatable.js`
  (admin3 verwendet sie über den Design-Fallback; admin4 hat eine eigene Kopie in `design/admin4/javascript/`).

## So prüfen Sie es

1. Öffnen Sie in der Administration einen Ordner mit Unterelementen.
2. Wählen Sie zwei oder drei Unterelemente aus.
3. Wählen Sie **Weitere Aktionen > Auswahl verstecken**. Die Seite lädt auf dem Elternknoten neu, und die
   ausgewählten Knoten werden als versteckt angezeigt.
4. Wählen Sie sie erneut aus und wählen Sie **Weitere Aktionen > Auswahl einblenden**.

Ein Regressionstest deckt den aktiven Weg der Aktion ab:
`tests/tests/kernel/content/ezcontentaction_hide_unhide_regression.php` (Gruppe `database`). Er legt einen echten
Knoten an, sendet die Aktionen zum Verstecken und Einblenden und prüft sowohl die Weiterleitung als auch den
Versteckt-Zustand des Knotens. Er braucht eine Testdatenbank; führen Sie ihn daher in einer Entwicklungskopie aus,
nicht auf einer Live-Website.

## Halten Sie diese Teile abgestimmt

Wenn Sie dieses Menü erneut ändern, ändern Sie all diese Teile gemeinsam:

- die Beschriftungen von admin3 und die Verdrahtung der Menüeinträge im Template;
- das JavaScript, das die Schaltflächen der Sammelaktionen sendet;
- den aktiven Handler von `content/action` in `extension/nxc_powercontent`;
- den Zweig von `content/action` im Kernel, für Installationen ohne diese Erweiterung.

## Verwandte Seiten

- [Das responsive Admin-Design (admin3)](../../funktionen/6.0/admin3-responsives-admin.md)
- [Die Liste der Unterelemente: 129 Spalten, Voreinstellungen und CSV-Export](../../funktionen/6.0/optionen-der-unterelementtabelle.md)
- [Ausgewählte Unterelemente kopieren](../../funktionen/6.0/unterelemente-auswahl-kopieren.md)
- [Breite und Schriftgröße der linken Seitenleiste](../../funktionen/6.0/admin3-seitenleistenbreite-und-schriftgroesse.md)
- [Das Design admin4](../../funktionen/6.0/admin4-design.md)
- Geschichte: [August 2024](../../geschichte/2024/2024-08.md), [Oktober 2024](../../geschichte/2024/2024-10.md),
  [November 2024](../../geschichte/2024/2024-11.md), [Januar 2025](../../geschichte/2025/2025-01.md),
  [Juni 2025](../../geschichte/2025/2025-06.md), [Juni 2026, 16. bis 30.](../../geschichte/2026/2026-06b.md)
