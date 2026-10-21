---
title: Herausfinden, wer eine Seite geändert hat
audience: government, admin, editor
category: public-sector
order: 310
summary: Aufgabe für Administratoren und leitende Redakteure - herausfinden, wer eine Seite geändert, verschoben oder entfernt hat und was sich geändert hat, mit der Seite der Versionen (zwei Versionen vergleichen) und dem Audit-Protokoll (Konsole oder exp:audit).
keywords: wer hat geändert, Versionshistorie, Versionen vergleichen, Audit-Protokoll, Audit-Konsole, Rechenschaft, who changed, version history, compare versions, audit trail, audit console, exp:audit search, content.node.remove, accountability
type: help
---
# Herausfinden, wer eine Seite geändert hat

Diese Aufgabe richtet sich an leitende Redakteure (Versionen) und Administratoren (Audit). Hintergrund:
[Versionsgeschichte als Rechenschaft](../../transparenz-und-informationsfreiheit/versionsgeschichte-als-rechenschaft.md) und
[Der Audit-Trail für wer was geändert hat](../../transparenz-und-informationsfreiheit/der-audit-trail-fuer-wer-was-geaendert-hat.md).

## Der Inhalt: Versionen

1. Öffnen Sie die Seite; im Editor **Versionen verwalten**, oder gehen Sie zu `/content/history/<object ID>`.
2. Jede Versionskarte zeigt ihren Ersteller und ihre Daten.
3. Wählen Sie unter **Zwei Versionen vergleichen** die **Ältere Version**, die **Neuere Version** und die
   **Übersetzung**: Die Felder, die sich unterscheiden, werden angezeigt.

## Die Handlung: Audit-Protokoll

Für Verschiebungen, Entfernungen, das Verstecken, Änderungen von Sektion oder Zustand und wenn die Seite weg ist:

1. Reiter **Audit** (`/audit/dashboard`), **Konsole**; filtern Sie nach **Kanal** `content`, **Ereignisname**
   `content.*` oder `content.node.remove`, **Objekt**, **Von** und **Bis**.
2. Oder auf der Kommandozeile:

   ```bash
   ./console exp:audit search --channel=content --name='content.node.*' --from=2026-10-01
   ./console exp:audit show <event id>
   ```

Das Audit braucht die Richtlinie `audit/read`; Administratoren haben sie.

## Prüfen

Sie können die Version, ihren Ersteller und den Zeitpunkt nennen, und das passende Audit-Ereignis.

## Probleme

| Problem | Hinweis |
|---|---|
| Alte Versionen fehlen | die Versionsgrenze hat sie entfernt (siehe „Die Grenze der Versionshistorie für Schriftgut anheben“) |
| Kein Audit-Eintrag | Lesezugriffe werden nur aufgezeichnet, wenn das eingeschaltet ist; Inhaltsänderungen schon |

## Verwandte Seiten

- [Inhaltshistorie](../../../../anleitungen/inhaltsverlauf.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
