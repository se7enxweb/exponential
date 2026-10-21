# Rollen: Richtlinien-IDs, Sortierung und Schaltflächen für die Reihenfolge

Diese Seite ist für Administratoren, die Rollen mit vielen Richtlinien pflegen. Seit dem 30. September 2026 zeigen der
Rolleneditor und die Rollenansicht die IDs der Richtlinien und haben sortierbare Überschriften, und der Editor hat
Schaltflächen, die die Reihenfolge der Richtlinien ändern.

## Richtlinien im Rolleneditor umordnen

1. Öffnen Sie den Reiter **Benutzer**, dann **Rollen und Richtlinien** und dann **Bearbeiten** bei einer Rolle.
2. Jede Richtlinie zeigt ihre **ID**. Behalten Sie die Standardsortierung bei (ID aufsteigend, die eigene Reihenfolge
   der Rolle).
3. Verwenden Sie die Schaltflächen **Nach oben** und **Nach unten** neben einer Richtlinie. Sie funktionieren auch über
   eine Seitengrenze hinweg.
4. Klicken Sie auf **Speichern**, um die neue Reihenfolge zu behalten, oder auf **Abbrechen**, um sie zu verwerfen. Wie
   jede Änderung im Editor geschieht eine Verschiebung in der temporären Version der Rolle.

Was eine Verschiebung tut: Sie tauscht die Inhalte einer Richtlinie und ihrer Nachbarin (Modul, Funktion,
Einschränkungen und jede temporäre Kopie, die der Richtlinieneditor von einer der beiden hält) und lässt beide IDs, wo
sie sind.

Die Eingabetaste im Namensfeld drückt eine verborgene Schaltfläche, die nur den Namen speichert, nicht die erste
Schaltfläche für die Reihenfolge.

## Die Liste sortieren

Klicken Sie auf die Überschriften **ID**, **Modul**, **Funktion** oder **Einschränkungen**.

- Sortiert wird in der Datenbank, weil die Liste seitenweise angezeigt wird.
- Einschränkungen werden nach dem ersten Bezeichner einer Einschränkung sortiert, dann nach der Anzahl der
  Einschränkungen (ein Join auf SQL-Engines, PHP auf MongoDB).
- Die Sortierung steht in den Ansichtsparametern `(policy_sort)` und `(policy_dir)`, neben `(policy_offset)`. Die
  Formularadresse und die Seitennavigation behalten alle drei, sodass eine Schaltfläche, die auf Seite drei einer
  sortierten Liste gedrückt wird, zu Seite drei zurückführt, weiterhin sortiert.

Die Rollenansicht zeigt die ID jeder Richtlinie und sortiert mit denselben Überschriften. Sie liest nur und hat daher
keine Schaltflächen für die Reihenfolge. Die Benutzer und Gruppen, denen die Rolle zugewiesen ist, bleiben unverändert.

## Ändert die Reihenfolge, wer was darf?

Nein. Jede Richtlinie gewährt Zugriff für sich; das Berechtigungssystem hängt nicht von der Reihenfolge ab. Das
Zugriffsarray der Rolle ist, ohne die Richtlinien-IDs, vor und nach einem Umordnen mit Speichern identisch.

Die Reihenfolge wird in den Richtlinien-IDs gehalten, sodass das Datenbankschema unverändert bleibt. Das Öffnen einer
Rolle zum Bearbeiten kopiert ihre Richtlinien in Listenreihenfolge, Speichern behält diese Kopien und Abbrechen löscht
sie, sodass die IDs die Reihenfolge *sind*. Damit das gilt, geben `eZRole::policyList()` und `policyPage()` die
Richtlinien jetzt in der Reihenfolge ihrer IDs zurück statt nach Modul und Funktion.

## Verwandte Seiten

- [Blättern in Rollen und Richtlinien](../../bc/6.0/seitenweise-anzeige-von-rollen-und-richtlinien.md)
- [Template-Operatoren für Rollen und Richtlinien](template-operatoren-fuer-rollen-und-richtlinien.md)
- [Sektionen](../../anleitungen/sektionen.md): die Sektions-Einschränkung, für eine Sektion zugewiesene Rollen und wer Sektionen zuweisen darf (`section/assign` mit `NewSection`)
- [Objektzustände](../../anleitungen/objektzustaende.md): die Einschränkungen `StateGroup_<identifier>` und `NewState`, mit einem ausgearbeiteten Prüf-Workflow
- [Blättern in Admin-Listen](seitenweise-listen-im-admin.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Januar 2026](../../geschichte/2026/2026-01.md), [16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
