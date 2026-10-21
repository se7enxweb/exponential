---
title: Prüfen, wer eine Seite geändert hat
audience: government, editor
category: public-sector
order: 450
summary: Für Redakteure und Teamleitungen - herausfinden, wer eine Seite wann geändert hat, über die Versionsseite (Ersteller und Daten jeder Version) und den Reiter Audit des Knotens (jede aufgezeichnete Aktion), und was jede Quelle sagen kann und was nicht.
keywords: wer hat geändert, Verlauf, Versionen, Versionen verwalten, Ersteller, Audit-Protokoll, Reiter Audit, audit/read, Rechenschaft, Änderungen, Zeitleiste, who changed, history, versions, Manage versions, creator, audit trail, Audit tab, accountability, changes, timeline
type: help
---
# Prüfen, wer eine Seite geändert hat

Diese Seite ist für Redakteure und Teamleitungen, die wissen müssen, wer eine Seite wann geändert hat: um eine Frage zu
stellen, eine Beschwerde zu beantworten oder für die Rechenschaft. Es gibt zwei Quellen: die Versionen der Seite und
das Audit-Protokoll ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md),
[Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte mit der Versionsseite](#schritte-mit-der-versionsseite)
3. [Schritte mit dem Reiter Audit](#schritte-mit-dem-reiter-audit)
4. [Was jede Quelle Ihnen sagt](#was-jede-quelle-ihnen-sagt)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Um die Versionsseite zu öffnen, brauchen Sie Bearbeitungs- oder Leserechte für das Objekt.
- Das Audit braucht die Richtlinie `audit/read`; ohne sie gibt es keinen Reiter Audit. Fragen Sie das Web-Team, wenn
  Sie sie brauchen.

## Schritte mit der Versionsseite

1. Öffnen Sie die Seite, **Versionen verwalten** (in der linken Spalte des Bearbeitungsformulars oder im Menü des
   Elements).
2. Jede Karte zeigt die Versionsnummer, den Status (*Veröffentlicht*, *Archiviert*, *Entwurf* ...), den Ersteller,
   die Zeiten der Erstellung und der Änderung sowie die geänderte Übersetzung.
3. Filtern Sie nach **Ersteller** oder **Status**; **Vergleichen** zeigt, was eine Version geändert hat
   ([Zwei Versionen vergleichen](zwei-versionen-vergleichen.md)).

## Schritte mit dem Reiter Audit

1. Öffnen Sie die Seite in der Administration; wählen Sie am Knoten den Reiter **Audit**.
2. Er listet die aufgezeichneten Ereignisse zu diesem Inhalt: Veröffentlichen, Verschieben, Verstecken, Entfernen,
   Zustandsänderungen, mit Benutzer und Zeit.
3. Für mehr hat der Bereich **Audit** der Administration eine Zeitleiste mit Filtern und Suche (`/audit/dashboard`).

## Was jede Quelle Ihnen sagt

| Frage | Versionen | Audit |
|---|---|---|
| Wer hat diesen Text geschrieben | ja (Ersteller der Version) | ja (wer veröffentlicht hat) |
| Wer hat ihn verschoben, versteckt oder entfernt | nein | ja |
| Wer hat ihn freigegeben | das Element der Zusammenarbeit | das Veröffentlichungsereignis |
| Was sich im Text geändert hat | ja (**Vergleichen**) | nein (zeichnet Aktionen auf, keine Texte) |
| Älter als das Versionslimit | nein (alte Versionen werden am Limit entfernt) | ja, innerhalb der Aufbewahrungsfrist des Audits |

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Text im Audit erwarten | das Audit zeichnet Aktionen auf, niemals Inhaltswerte |
| Persönliche Schlüsse über eine Kollegin oder einen Kollegen veröffentlichen | verwenden Sie die Informationen nur für den Zweck, für den Sie sie erhalten haben |
| Entfernte Versionen | das Entfernen ist endgültig; das Audit zeichnet trotzdem auf, dass es geschehen ist |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Zwei Versionen vergleichen](zwei-versionen-vergleichen.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
