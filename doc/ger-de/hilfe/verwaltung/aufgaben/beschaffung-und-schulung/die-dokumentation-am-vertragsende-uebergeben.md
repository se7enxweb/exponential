---
title: Die Dokumentation am Vertragsende übergeben
audience: government, project-manager, admin, partner
category: public-sector
order: 220
summary: Schritt für Schritt die Dokumentation sammeln und prüfen, die ein scheidender Lieferant übergibt, wenn ein Vertrag für eine Exponential-Site endet - Inhaltsmodell, Rollen, Einstellungen, Code, Deployment, Betrieb, offene Punkte - damit ein Nachfolger oder Ihre eigenen Mitarbeiter übernehmen können.
keywords: Übergabe der Dokumentation, Vertragsende, Wechsel des Lieferanten, Inhaltsmodell, Rollen, Einstellungen, Deployment, Runbooks, Wissenstransfer, Aufgabe zur Beschaffung, documentation handover, contract end, supplier change, content model, roles, settings, deployment, runbooks, knowledge transfer, procurement task
type: help
---
# Die Dokumentation am Vertragsende übergeben

**Wer:** der Vertragsverantwortliche, mit dem Site-Administrator, der jeden Punkt prüfen wird. **Wann:** während der
Übergabezeit am Ende eines Vertrags. Hintergrund:
[Übergabe der Dokumentation](../../../../../eng-us/help/government/procurement/exit-plans/documentation-handover.md),
[Das Ende eines Vertrags](../../../../../eng-us/help/government/procurement/lifecycle/end-of-a-contract.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Übergabeliste](#übergabeliste)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Der Ausstiegsplan und die Dokumentationsklausel des Vertrags
  ([Klausel zur Dokumentation](../../../../../eng-us/help/government/procurement/tender-templates/documentation-clause.md)).
- Benannte Personen auf Ihrer Seite, die jeden Teil entgegennehmen und prüfen.

## Schritte

1. **Schicken Sie die Übergabeliste** unten zu Beginn der Übergabezeit an den Lieferanten.
2. **Nehmen Sie jeden Punkt** an einem Ort **entgegen**, den Ihre Organisation kontrolliert (Repository,
   Dokumentensystem), niemals nur als E-Mail-Anhang.
3. **Prüfen Sie jeden Punkt gegen die Live-Site**: Passt die Beschreibung des Inhaltsmodells zu **Setup > Klassen**?
   Passen die Rollen zu **Rollen** (`/role/list`)? Funktioniert die Beschreibung des Deployments auf Staging?
4. **Gehen Sie den Betrieb** mit dem Lieferanten **durch**: ein routinemäßiges Update und eine Wiederherstellung auf
   Staging, nur mit der übergebenen Dokumentation.
5. **Halten Sie Lücken fest** und geben Sie dem Lieferanten ein Datum, sie zu schließen.
6. **Zeichnen Sie ab**, wenn die Prüfungen bestanden sind; schließen Sie dann den Zugang des Lieferanten
   ([Offboarding eines Auftragnehmers](../../checklisten/einen-auftragnehmer-verabschieden.md)).

## Übergabeliste

| Punkt | Prüfen gegen |
|---|---|
| Inhaltsmodell: Klassen, Felder, Zweck | **Setup > Klassen** ([Klassengruppen](../../../../anleitungen/klassengruppen.md)) |
| Sektionen, Rollen, Richtlinien, Benutzergruppen | **Sektionen**, **Rollen** ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)) |
| Workflows, Auslöser, Objektzustände | **Setup > Workflows**, **Auslöser**, **Zustände** |
| Siteaccesses, Designs, von den Voreinstellungen geänderte Einstellungen | die Einstellungsseite ([Die Einstellungsseite](../../../../anleitungen/einstellungsseite.md)) |
| Eigene Erweiterungen und Designs, in Ihrem Repository | **Setup > Upgrade-Prüfung** für geänderte Dateien des Kernels |
| Ablauf des Deployments und Umgebungen | ein Deployment auf Staging ([Bereitstellung](../../../../anleitungen/bereitstellung.md)) |
| Cronjobs und ihr Zeitplan | **Setup > Cronjobs** ([Cronjobs](../../../../anleitungen/cronjobs.md)) |
| Ablauf von Sicherung und Wiederherstellung | ein Test der Wiederherstellung ([Bereitschaft der Sicherungen](../../checklisten/bereitschaft-fuer-datensicherungen.md)) |
| Dienste Dritter und ihre Konten | die Liste der aktiven Erweiterungen |
| Offene Punkte, bekannte Risiken, geplante Arbeit | das Ticketsystem |

## Ergebnis

Dokumentation, die Ihre Organisation besitzt und getestet hat, genug, damit ein Nachfolger übernehmen kann.

## Fallstricke

- Dokumente annehmen, ohne sie gegen die Site zu prüfen.
- Dokumentation, die nur in den Werkzeugen des Lieferanten liegt.
- Den Zugang des Lieferanten vor dem gemeinsamen Durchgang schließen.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Einen Ausstiegsplan schreiben](einen-ausstiegsplan-schreiben.md)
- [Bereitschaft für den Ausstieg](../../checklisten/bereitschaft-fuer-den-ausstieg.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
