---
title: Eine Liste verwandter Dokumente hinzufügen
audience: government, editor
category: public-sector
order: 200
summary: Für Redakteure - einer Seite eine Liste verwandter Dokumente oder Seiten hinzufügen, mit einem Relationsfeld (Vorhandenes hinzufügen, Neu hochladen) oder Einbettungen im Text, sodass jedes Dokument nur einmal existiert und jede Seite, die es braucht, darauf verweist.
keywords: verwandte Dokumente, Verknüpfungen, Objektrelationen, Vorhandenes hinzufügen, Neu hochladen, einbetten, verwandte Dateien, Rückwärtsverknüpfungen, Dokumentenliste, Wiederverwendung, related documents, relations, Object relations, Add existing, Upload new, embed, related files, reverse relations, documents list, reuse
type: help
---
# Eine Liste verwandter Dokumente hinzufügen

Diese Seite ist für Redakteure, die auf einer Seite eine Liste „Dokumente“ oder „Siehe auch“ haben möchten. Exponential
verwendet Inhalte über Relationen wieder: Ein Dokument ist ein einziges Datei-Objekt, und jede Seite, die es braucht,
verweist darauf ([Relationen und Wiederverwendung](../../../ersteller/website-ersteller/relationen-und-wiederverwendung.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte mit einem Relationsfeld](#schritte-mit-einem-relationsfeld)
3. [Schritte mit Einbettungen im Text](#schritte-mit-einbettungen-im-text)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Prüfen Sie, ob die Klasse der Seite ein Relationsfeld hat (zum Beispiel **Related documents**, **Documents** oder
  **Attachments**); der Feldtyp ist *Relation*. Wenn nicht, verwenden Sie Einbettungen im Rich Text oder bitten Sie das
  Web-Team, ein Feld hinzuzufügen.
- Wissen Sie, ob die Dokumente schon auf der Site existieren (suchen Sie zuerst).

## Schritte mit einem Relationsfeld

1. Öffnen Sie die Seite, **Bearbeiten**.
2. Im Relationsfeld:
   - **Vorhandenes hinzufügen**: Durchsuchen oder suchen Sie das Dokument, wählen Sie es aus, bestätigen Sie;
   - **Neu hochladen**: Wählen Sie eine Datei von Ihrem Computer; sie wird zu einem Datei-Objekt und verknüpft.
3. Wiederholen Sie das für jedes Dokument; die Reihenfolge im Feld ist die Reihenfolge auf der Seite (verschieben Sie
   Einträge, wenn das Feld es erlaubt).
4. **Zur Veröffentlichung schicken**.

## Schritte mit Einbettungen im Text

1. Setzen Sie in einem Rich-Text-Feld den Cursor an die Stelle, an der der Link stehen soll.
2. Verwenden Sie **Embed object** (oder den Link-Dialog) und wählen Sie das Dokument; wählen Sie eine Ansicht, die es
   als Link zeigt.
3. Auch Einbettungen erzeugen Relationen, sodass die Rückwärtsverknüpfungen des Dokuments diese Seite auflisten.

## Das Ergebnis prüfen

- [ ] Abgemeldet zeigt die Liste jedes Dokument mit Name, Format und Größe.
- [ ] Bei einem Dokument listet der Reiter **Verknüpfungen**, **Rückwärts verknüpfte Objekte**, die Seiten auf, die
  es verwenden.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Dasselbe PDF für jede Seite erneut hochladen | suchen Sie und verwenden Sie **Vorhandenes hinzufügen** |
| Ein Dokument entfernen, das noch anderswo verknüpft ist | die Bestätigung des Entfernens listet die Seiten; korrigieren Sie sie zuerst |
| Linktext „Dokument 1“ | geben Sie Datei-Objekten echte Titel |
| Relationen zu verstecktem oder eingeschränktem Inhalt | Besucher sehen ein fehlendes Element; verknüpfen Sie öffentliche Inhalte |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Einen PDF-Bericht mit barrierefreier Zusammenfassung hochladen](einen-pdf-bericht-mit-barrierefreier-zusammenfassung-hochladen.md)
- [Inhalte erstellen und bearbeiten: Relationen und Bilder](../../../inhalte-bearbeiten.md#verknüpfungen-und-bilder)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
