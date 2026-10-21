---
title: Eine Seite vorübergehend verstecken
audience: government, editor
category: public-sector
order: 180
summary: Für Redakteure - eine Seite für eine Weile von der Site nehmen, ohne sie zu entfernen, mit Verstecken / Zeigen im Menü des Baums oder Auswahl verstecken in einer Liste; wissen, was das Verstecken betrifft (alles darunter, Menüs, Suche, Downloads), und die Seite zurückholen.
keywords: verstecken, einblenden, zeigen, Verstecken / Zeigen, Auswahl verstecken, Auswahl einblenden, versteckt, unsichtbar, vorübergehend, Rückwärtsverknüpfungen, offline nehmen, Seite, hide, unhide, Hide / unhide, Hide selected, Unhide selected, hidden, reverse relations
type: help
---
# Eine Seite vorübergehend verstecken

Diese Seite ist für Redakteure, die eine Seite für eine Weile von der Site nehmen müssen: Informationen, die gerade
geprüft werden, eine saisonale Seite, eine Dienstleistung, die ausgesetzt ist. Ein verstecktes Element bleibt in der
Administration, wird aber auf der Site nicht angezeigt, ebenso wenig wie alles darunter
([Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md#ein-objekt-verstecken)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was das Verstecken betrifft](#was-das-verstecken-betrifft)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Ihre Rolle hat `content/hide` für das Element.
- Entscheiden Sie, ob Verstecken das Richtige ist: Für eine Seite, die endgültig verschwinden soll, siehe
  [Eine veraltete Seite entfernen und wiederherstellen](eine-veraltete-seite-entfernen-und-wiederherstellen.md); für eine Seite mit
  einem bekannten Enddatum lässt sich ein automatisches Versteckdatum einrichten
  ([Eine öffentliche Bekanntmachung mit Ablaufdatum veröffentlichen](eine-oeffentliche-bekanntmachung-mit-ablaufdatum-veroeffentlichen.md)).

## Schritte

1. Öffnen Sie im Inhaltsbaum das Menü des Elements (klicken Sie auf das Symbol vor seinem Namen) und wählen Sie
   **Fortgeschritten** > **Verstecken / Zeigen**.
2. Für mehrere Elemente: Markieren Sie sie in der Liste **Unterelemente** des übergeordneten Elements und wählen Sie
   **Weitere Aktionen** > **Auswahl verstecken**.
3. Nur ein Ort: Markieren Sie auf dem Reiter **Orte** des Elements den Ort und klicken Sie auf **Verstecken**; die
   anderen Orte bleiben sichtbar.
4. Wenn andere Inhalte auf das Element verweisen, listet eine Warnung sie auf (die Prüfung der Rückwärtsverknüpfungen):
   Diese Seiten verweisen dann auf etwas, das Besucher nicht sehen können
   ([Prüfung der Rückwärtsverknüpfungen](../../../../funktionen/6.0/pruefung-umgekehrter-beziehungen.md)).

Zum Zurückholen: noch einmal **Verstecken / Zeigen** oder **Auswahl einblenden**.

## Was das Verstecken betrifft

| Was | Solange es versteckt ist |
|---|---|
| Die Seite und alles darunter | wird Besuchern nicht angezeigt |
| Menüs und Listen | ausgelassen (Fetches zeigen, was Besucher sehen) |
| Suche | versteckte Inhalte werden von Besuchern nicht gefunden; mit eZ Find wird der Index benachrichtigt |
| Downloads von Dateien darin | für Besucher verweigert (ein sichtbarer Ort ist nötig) |
| Die Administration | angezeigt, als versteckt markiert; Redakteure können weiter bearbeiten |

## Das Ergebnis prüfen

- [ ] Abgemeldet zeigt die Adresse der Seite die Seite nicht mehr an.
- [ ] Das Menü des übergeordneten Elements listet sie nicht mehr auf.
- [ ] Seiten, die auf sie verlinkt haben: Korrigieren oder entfernen Sie die Links, solange sie versteckt ist.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Einen Ordner zu verstecken versteckt alle seine Unterelemente | prüfen Sie vor dem Verstecken, was darunter liegt |
| Das Einblenden vergessen | tragen Sie das Datum in Ihren Kalender ein |
| Erwarten, dass Suchmaschinen sofort vergessen | Kopien an anderen Stellen bleiben noch eine Weile erhalten |
| Ein Ort, der durch eine Bearbeitung sichtbar wird | im Fenster **Orte** des Bearbeitungsformulars beginnen bestehende Orte bei **Unverändert**; wer **Sichtbar** wählt, zeigt diesen Ort an |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine veraltete Seite entfernen und wiederherstellen](eine-veraltete-seite-entfernen-und-wiederherstellen.md)
- [Inhalte in Prüfung mit Objektzuständen markieren](inhalte-in-pruefung-mit-objektzustaenden-markieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
