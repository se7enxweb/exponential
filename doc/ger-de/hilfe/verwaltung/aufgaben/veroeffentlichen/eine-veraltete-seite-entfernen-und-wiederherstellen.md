---
title: Eine veraltete Seite entfernen und wiederherstellen
audience: government, editor
category: public-sector
order: 190
summary: Für Redakteure - eine veraltete Seite mit Entfernen entfernen (sie kommt in den Papierkorb), ihren Besuchern ein anderes Ziel geben und sie aus dem Papierkorb wiederherstellen, wenn sie versehentlich entfernt wurde; und warum das Leeren des Papierkorbs endgültig ist.
keywords: Seite entfernen, Papierkorb, wiederherstellen, Papierkorb leeren, veraltete Inhalte, Weiterleitung, Rückwärtsverknüpfungen, versehentlich entfernt, content/restore, remove page, trash, restore, Empty trash, outdated content, redirect, reverse relations, removed by mistake
type: help
---
# Eine veraltete Seite entfernen und wiederherstellen

Diese Seite ist für Redakteure, die veraltete Seiten außer Dienst stellen. **Entfernen** verschiebt ein Element in den
Papierkorb, aus dem es wiederhergestellt werden kann, bis der Papierkorb geleert wird
([Inhaltsmodell und Bearbeitung, 7](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#7-der-papierkorb)). Entfernen ist etwas
anderes als Verstecken: Die Adresse funktioniert nicht mehr.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte zum Entfernen](#schritte-zum-entfernen)
3. [Schritte zum Wiederherstellen](#schritte-zum-wiederherstellen)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Bestätigen Sie, dass die Seite nicht für die Akten gebraucht wird (fragen Sie bei förmlichen Dokumenten die
  Verantwortlichen für die Schriftgutverwaltung); wenn doch, verstecken Sie sie stattdessen
  ([Eine Seite vorübergehend verstecken](eine-seite-voruebergehend-verstecken.md)).
- Entscheiden Sie, wohin ihre Besucher stattdessen gehen sollen (eine neuere Seite, das übergeordnete Element).

## Schritte zum Entfernen

1. Öffnen Sie die Seite und klicken Sie auf **Entfernen** (oder markieren Sie mehrere in einer Liste
   **Unterelemente**, **Weitere Aktionen** > **Ausgewählte entfernen**).
2. Lesen Sie die Bestätigung: Sie listet andere Inhalte, die auf die Seite verweisen (Einbettungen, Links,
   Relationsfelder). Korrigieren Sie diese Seiten zuerst oder nehmen Sie in Kauf, dass sie ins Leere verweisen.
3. Bestätigen Sie. Das Element und alles darunter kommen in den Papierkorb.
4. Geben Sie der alten Adresse ein Ziel: einen Alias an der Ersatzseite oder einen URL-Platzhalter
   ([Alte Adressen nach einer Umstrukturierung weiterleiten](alte-adressen-nach-einer-umstrukturierung-weiterleiten.md)). Sonst
   antwortet die Adresse einer entfernten Seite mit „nicht gefunden“.

## Schritte zum Wiederherstellen

1. Öffnen Sie den **Papierkorb** (in der linken Spalte der Inhaltsstruktur oder unter `/content/trash`).
2. Finden Sie das Element: Filtern Sie nach **In den Papierkorb verschoben von**, **Typ** oder Datum. Jede Zeile zeigt,
   wer es in den Papierkorb verschoben hat und wo es war.
3. Markieren Sie es und klicken Sie auf **Wiederherstellen** (oder öffnen Sie seine Seite zum Wiederherstellen) und
   bestätigen Sie die Stelle, an die es zurückkehrt.
4. Das Element und alles, was darunter lag, kommen zurück.

## Das Ergebnis prüfen

- [ ] Nach dem Entfernen: Die alte Adresse leitet zur Ersatzseite weiter (abgemeldet).
- [ ] Nach dem Wiederherstellen: Die Seite öffnet sich an ihrer Stelle; die Menüs zeigen sie.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| **Papierkorb leeren** | löscht alles im Papierkorb endgültig, unabhängig vom Filter; überlassen Sie das den Administratoren |
| Benutzerkonten | Benutzerobjekte kommen nie in den Papierkorb; das Entfernen eines Benutzers ist endgültig |
| Einen Ordner entfernen | alles darunter geht mit; prüfen Sie zuerst |
| Akten | das Entfernen eines förmlichen Dokuments kann gegen Ihre Regeln zur Schriftgutverwaltung verstoßen |
| Große Entfernungen | können als Hintergrundauftrag laufen; verfolgen Sie ihn unter `/content/jobs` |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md#entfernen-und-wiederherstellen)
- [Papierkorb: wer und wo](../../../../funktionen/6.0/papierkorb-wer-und-wo.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
