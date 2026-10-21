---
title: Einen Freigabe-Workflow für Übersetzungen einrichten
audience: government, admin
category: public-sector
order: 380
summary: Aufgabe für Administratoren - Übersetzungen in eine Sprache vor dem Veröffentlichen von den Prüfern dieser Sprache freigeben lassen, mit einem Approve-Ereignis, das auf die betroffenen Sprachen beschränkt und mit Vor dem Veröffentlichen von Inhalten verbunden ist.
keywords: Freigabe von Übersetzungen, Approve-Ereignis, Betroffene Sprachen, Workflow, Auslöser, Prüfer, Vor dem Veröffentlichen von Inhalten, Multiplexer, translation approval, Approve event, Affected languages, trigger, reviewers, Before publishing content
type: help
---
# Einen Freigabe-Workflow für Übersetzungen einrichten

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Übersetzungs-Workflow und Freigabe](../../sprachen/uebersetzungsablauf-und-freigabe.md) und
[Workflows](../../../../anleitungen/workflows.md).

## Bevor Sie beginnen

- Eine Benutzergruppe von Prüfern für die Sprache.
- Ob bereits ein anderer Workflow den Auslöser **Vor dem Veröffentlichen von Inhalten** verwendet.

## Schritte

1. **Setup > Workflows**: Öffnen Sie eine Gruppe, **Neuer Workflow** „Freigabe französischer Übersetzungen“.
2. Wählen Sie **Event / Approve**, **Ereignis hinzufügen**; setzen Sie **Betroffene Sprachen** auf Französisch,
   **Betroffene Sektionen** nach Bedarf, **Gruppen, die Content freigeben** auf die französischen Prüfer; **OK**.
3. **Setup > Auslöser**: Zeile **Vor dem Veröffentlichen von Inhalten**, wählen Sie den Workflow, **Änderungen
   anwenden**. Ist dort bereits ein Workflow, kombinieren Sie beide in einem Workflow mit einem Ereignis
   **Multiplexer** und verbinden Sie diesen.
4. Prüfen Sie, dass die Cronjob-Gruppe `frequent` läuft (**Setup > Workflow Prozesse** zeigt es in seiner
   Statusleiste).

## Prüfen

Die französische Version eines Übersetzers wartet als ausstehend; ein französischer Prüfer findet sie unter
**Zusammenarbeit** und drückt **Freigeben**; nach dem nächsten Lauf des Workflow-Cronjobs ist sie veröffentlicht.
Englische Versionen werden ohne Warten veröffentlicht.

## Probleme

| Problem | Lösung |
|---|---|
| Freigegebene Versionen werden nicht veröffentlicht | der Workflow-Cronjob läuft nicht |
| Nichts wartet | die Sprache der Version steht nicht in **Betroffene Sprachen**, oder der Übersetzer ist selbst Freigebender |

## Verwandte Seiten

- [Freigabe vor dem Veröffentlichen](../../transparenz-und-informationsfreiheit/freigabe-vor-dem-veroeffentlichen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
