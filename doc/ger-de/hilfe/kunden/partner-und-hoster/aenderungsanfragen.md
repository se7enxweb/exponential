---
title: Änderungsanfragen
audience: customer, partner, project-manager
category: customer-success
order: 13
summary: Wie der Betreiber einer Exponential-Website einen Partner oder Hoster um eine Änderung bittet - was zu schreiben ist, wie sie geschätzt und genehmigt wird, Testen auf einer Kopie, der Probelauf, wann sie live geht und wie sie festgehalten wird - mit einem Anfrageformular zum Kopieren.
keywords: Änderungsanfrage, Änderung anfordern, Schätzung, Genehmigung, Staging, Probelauf, Deployment, Änderungsprotokoll, Umfang, Partner, change request, request a change, estimate, approval, staging, dry run, deploy, change log, scope, partner
type: help
---
# Änderungsanfragen

Eine Änderungsanfrage ist der Weg, auf dem Ihr Team einen Partner oder Hoster um etwas Neues oder Anderes an der
Website bittet: einen neuen Seitentyp, eine Designänderung, eine neue Sprache, eine Einstellungsänderung. Eine klare
Anfrage bekommt eine klare Schätzung und bringt weniger Überraschungen. Diese Seite beschreibt die Seite des
Betreibers; die Vorlagen für Projektleitungen stehen unter [Projektvorlagen](../../fuehrung/projektvorlagen.md).

## Ist es eine Änderung, oder können Sie es selbst erledigen?

Vieles braucht überhaupt keinen Anbieter: Inhalte, Menüs, die aus Inhalten bestehen, Bilder, Übersetzungen, Rollen und
Konten. Die Seiten für Redakteure zeigen, wie ([Inhalte bearbeiten](../../inhalte-bearbeiten.md), [Sprachen](../../sprachen.md),
[Layouts-Editor](../../layout-editor.md)). Eine Änderungsanfrage ist für Templates, Designs, Erweiterungen, das
Inhaltsmodell, Einstellungen und den Server.

## Was zu schreiben ist

| Punkt | Warum |
|---|---|
| Was Sie wollen, in Ihren eigenen Worten | Der Bedarf, nicht nur eine Lösung |
| Warum: der geschäftliche Grund | Hilft dem Anbieter, einen einfacheren Weg vorzuschlagen |
| Wo: betroffene Seiten, Websites, Sprachen | Legt den Umfang fest |
| Wer es nutzt: Besucher, Redaktion, Mitarbeitende | Legt die Tests fest |
| Wann es gebraucht wird, und ein fester Termin, falls vorhanden | Eine Kampagne, ein Gesetz, eine Veranstaltung |
| Woran Sie erkennen, dass es fertig ist | Die Abnahmeprüfung |
| Beispiele oder Skizzen | Ersparen eine Runde Fragen |

## Wie es ablaufen sollte

1. **Schätzung schriftlich**, mit dem, was enthalten ist und was nicht. „Eine Änderungsanfrage ist eine neue Zeile mit
   eigener Schätzung“ ([Schätzen und Abgrenzen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)).
2. **Genehmigung** durch die in Ihrem Vertrag genannte Person.
3. **Gebaut und getestet auf einer Kopie** der Website, nie zuerst auf der Live-Website
   ([Unterbrechungsfreie Deployments](../../../../en/us/help/businesses/reliability/graceful-deploys.md)).
4. **Sie prüfen es** auf der Kopie anhand Ihrer Abnahmeprüfung.
5. **Ein Probelauf des Deployments.** Der Deployment-Befehl kann jeden Schritt ausgeben, ohne etwas zu ändern; der
   Leitfaden für Unternehmen sagt, „verlangen Sie, dass er Teil jeder Änderungsanfrage ist“ (dieselbe Seite).
6. **Live zu einer vereinbarten Zeit**, nicht direkt vor einer Spitze und nicht am Ende der Woche (dieselbe Seite).
7. **Danach geprüft**: Startseite, eine tief liegende Seite, die Administration und das, was die Änderung berührt hat.
8. **Festgehalten.** Das Prüfprotokoll hält Deployments in seinem Kanal `system` fest; führen Sie zusätzlich eine Zeile
   in Ihrem eigenen Änderungsprotokoll (dieselbe Seite).

## Wenn eine Änderung ein Wartungsfenster braucht

Die meisten Änderungen gehen live, ohne dass Besucher es bemerken. Manche, etwa eine Datenbankänderung oder ein großer
Import, brauchen die Website möglicherweise kurz offline; fragen Sie, ob Ihre das tut, und planen Sie es
([Wartungsmodus und geplante Arbeiten](../gesund-halten/wartungsmodus-und-geplante-arbeiten.md)).

## Jede Änderung lässt sich rückgängig machen

Fragen Sie vor der Genehmigung: „Wie kommen wir zurück?“ Die Regel für Unternehmen lautet, dass „die vorherige Version
des Codes in Git liegt und ein Backup existiert, wenn sich die Datenbank ändert“ ([Unterbrechungsfreie Deployments](../../../../en/us/help/businesses/reliability/graceful-deploys.md)).

## Ein Anfrageformular zum Kopieren

```text
CHANGE REQUEST                              Number: ____    Date: ____
Requested by:        ____ (role)
Approver:            ____ (role)
What we want:        ____
Why:                 ____
Where:               ____ (sites, languages, page types)
Who uses it:         ____
Needed by:           ____ (and why that date)
Done when:           ____ (acceptance check)
Attachments:         ____
-- provider --
Estimate:            ____   Included: ____   Not included: ____
Needs a maintenance window: yes / no
Way back:            ____
Approved:            ____ (name, date)
Tested on the copy:  ____ (date, by)
Live:                ____ (date, time)   Checked by: ____
```

## Verwandte Seiten

- [Wer was macht](wer-was-macht.md)
- [Was Sie Ihren Partner fragen sollten](../gesund-halten/was-sie-ihren-partner-fragen-sollten.md)
- [Vorschau vor dem Veröffentlichen](../../../../en/us/help/businesses/reliability/preview-before-you-publish.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
