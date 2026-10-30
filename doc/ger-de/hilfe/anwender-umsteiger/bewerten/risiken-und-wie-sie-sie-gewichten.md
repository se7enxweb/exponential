---
title: Risiken der Einführung und wie Sie sie gewichten
audience: adopter, businesses, project-manager
category: adoption
order: 17
summary: Die Risiken, die ein Anwender von Exponential bedenken sollte - Kenntnisse, Abhängigkeit von Anbietern, Migration, individueller Code, Upgrades, Sicherheit, Finanzierung des Projekts -, wie wahrscheinlich und schwerwiegend jedes für Sie ist und wie Sie es verringern.
keywords: Risiken, Risikoregister, Kenntnisse, Anbieter, Migration, individueller Code, Upgrades, Sicherheit, Projektfinanzierung, Risikominderung, risks, risk register, skills, provider, migration, custom code, upgrades, security, project funding, mitigation
type: help
---
# Risiken der Einführung und wie Sie sie gewichten

Jede Einführung hat Risiken. Sie aufzuschreiben und für jedes zu entscheiden, wie Sie es verringern, gehört zu einer
guten Entscheidung.

## Inhalt

1. [Ein Risikoregister](#ein-risikoregister)
2. [Gewichten](#gewichten)
3. [Was die Quellen über das Projekt selbst sagen](#was-die-quellen-über-das-projekt-selbst-sagen)

## Ein Risikoregister

| Risiko | Wie Sie es verringern | Seite |
|---|---|---|
| Zu wenige Menschen kennen Exponential | Schulen Sie Ihre Mitarbeitenden; wählen Sie einen Partner; tragen Sie bei, damit Wissen öffentlich bleibt | [Kenntnisse und Schulung](../planung/kenntnisse-und-schulung.md), [Einwand: Finden wir Leute?](../../fuersprecher/einwand-fachkraefte.md) |
| Abhängigkeit von einem Anbieter | Behalten Sie Code und Konten in Ihrer Hand; halten Sie den Code in Git; dokumentieren Sie die Übergabe | [Was Ihnen gehört](../../kunden/was-ihnen-gehoert.md), [Checkliste zur Übergabe](../../kunden/partner-und-hoster/checkliste-uebergabe.md) |
| Die Migration dauert länger als geplant | Proben und messen; in Etappen migrieren | [Umziehen](../umziehen/README.md), [Einführung in Etappen](../planung/einfuehrung-in-etappen.md) |
| Individueller Code erschwert Upgrades | Halten Sie individuellen Code in Erweiterungen; vermeiden Sie Patches am Kernel | [Erweitern ohne Fork](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/erweitern-ohne-zu-forken.md) |
| Aktualisierungen werden aufgeschoben | Eine Routine ab dem ersten Monat | [Auf dem Laufenden bleiben](../nach-der-einfuehrung/auf-dem-laufenden-bleiben.md) |
| Sicherheitsvorfälle | Härtung, Aktualisierungen, vertrauliche Meldung | [Sicherheit nach der Einführung](../nach-der-einfuehrung/sicherheit-nach-der-einfuehrung.md) |
| Eine benötigte Funktion fehlt | Im Machbarkeitsnachweis prüfen; eine Erweiterung planen oder sie sponsern | [Ein Machbarkeitsnachweis](machbarkeitsnachweis.md) |

## Gewichten

Notieren Sie für jedes Risiko, wie wahrscheinlich es für Sie ist (niedrig, mittel, hoch) und wie schwer es wiegen würde.
Handeln Sie zuerst bei Risiken, die sowohl wahrscheinlich als auch schwerwiegend sind. Die Seite für Projektleitungen
hat eine ausführlichere Methode: [Risikomanagement](../../fuehrung/risikomanagement.md).

## Was die Quellen über das Projekt selbst sagen

- **Kontinuität.** Die Codebasis wurde nach dem Ende der Lebensdauer beim ursprünglichen Hersteller 2021 am Leben
  erhalten, und die Linie 6.0 erscheint seit dem 1. Januar 2024
  ([Eine kurze Geschichte des Ökosystems](../../oekosystem/eine-kurze-geschichte-des-oekosystems.md)).
- **Zwei Organisationen.** Ein Unternehmen (7x) und eine gemeinnützige Organisation (die Exponential Foundation)
  ([Organisationen](../../oekosystem/organisationen/README.md)).
- **Offener Code.** Selbst wenn alle Anbieter verschwänden, gehörte der Code unter der GPL weiter Ihnen.
- **Nicht veröffentlicht.** Vorstand, Steuerung und Budget der Foundation sind nicht veröffentlicht
  ([Was die Quellen nicht sagen](../../wer-ist-die-exponential-foundation.md#was-die-quellen-nicht-sagen)). Fragen Sie,
  wenn es für Sie wichtig ist.
- **Finanzierung.** Das Projekt lebt von geschenkter Zeit und Sponsoring; Anwender, die sich darauf verlassen, können
  helfen, es abzusichern ([Die Community finanzieren](../../gemeinschaft/so-funktioniert-es/die-community-finanzieren.md)).

## Verwandte Seiten

- [Exponential bewerten](README.md)
- [Risikomanagement](../../fuehrung/risikomanagement.md)
- [Einwand: Lebt es noch?](../../fuersprecher/einwand-lebt-es-noch.md)
- [Die Entscheidung festhalten](../entscheiden/die-entscheidung-festhalten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
