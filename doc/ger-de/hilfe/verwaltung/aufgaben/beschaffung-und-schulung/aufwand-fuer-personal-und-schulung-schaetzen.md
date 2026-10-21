---
title: Aufwand für Personal und Schulung schätzen
audience: government, project-manager, trainer
category: public-sector
order: 140
summary: Schritt für Schritt die interne Arbeitszeit und den Schulungsaufwand schätzen, den eine Exponential-Site in einer Behörde braucht - Rollen, Stunden je Routineaufgabe, Schulungsstunden aus dem Kurskatalog und Fluktuation - mit Ihren eigenen Zahlen.
keywords: Personalaufwand, Schulungsaufwand, interne Kosten, Rollen, Site-Administrator, Redakteure, Stunden, Kursdauer, Fluktuation, Budget, Aufgabe zur Beschaffung, staff effort, training effort, internal cost, roles, site administrator, editors, hours, course lengths, turnover, budget, procurement task
type: help
---
# Aufwand für Personal und Schulung schätzen

**Wer:** der Projektleiter mit dem Schulungskoordinator. **Wann:** beim Aufstellen des Budgets. Die interne Zeit ist
oft der größte Kostenblock, und der einzige, der nicht in Rechnung gestellt wird. Hintergrund:
[Personal und Fähigkeiten](../../beschaffung/gesamtbetriebskosten/personal-und-kompetenzen.md),
[Schulungsaufwand budgetieren](../../beschaffung/schulung/schulungsaufwand-budgetieren.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Arbeitsblatt](#arbeitsblatt)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Die Liste der Menschen, die mit der Site arbeiten werden, nach Rolle
  ([Rollen und Kurse](../../beschaffung/schulung/rollen-und-kurse.md)).
- Ihre internen Stundenkosten je Rolle (von der Finanzabteilung).

## Schritte

1. **Listen Sie die Rollen** und die Zahl der Personen in jeder auf: Redakteure, Freigebende, Site-Administratoren,
   Systemadministratoren, Entwickler.
2. **Schätzen Sie die Routinestunden je Rolle und Monat** aus den Aufgaben, die sie erledigen. Für Administratoren
   nehmen Sie die Routine aus
   [Eine Site über einen mehrjährigen Vertrag aktuell halten](../../beschaffung/lebenszyklus/eine-website-aktuell-halten.md)
   und der [Vierteljährlichen Überprüfung des Zugangs](../../checklisten/vierteljaehrliche-zugriffspruefung.md).
3. **Nehmen Sie die Schulungsstunden aus dem Katalog**: Redakteure 101 etwa 19,5 Stunden, Redakteure 201 etwa 16,
   Site-Administration etwa 30, Hosting und Betrieb etwa 20 ([Kurskatalog](../../../trainer/kurskatalog.md)).
4. **Rechnen Sie die Fluktuation hinzu**: wie viele neue Personen je Jahr, von denen jede eine Einarbeitung braucht
   ([Neue Mitarbeiter einarbeiten](../../beschaffung/schulung/einarbeitung-neuer-beschaeftigter.md)).
5. **Rechnen Sie einmalige Arbeit hinzu**: Migration der Inhalte durch Redakteure, Abnahmetests, die Ausstiegsübung.
6. **Multiplizieren Sie** mit den internen Sätzen und übernehmen Sie die Summe in das Kostenmodell.

## Arbeitsblatt

```text
Staff and training effort - <site>, per year            all values: your own estimates

Role                People  Routine h/month  Training h (yr 1)  New people/yr  Rate
Editors             <n>     <h>              19.5 (Ed. 101)     <n>            <r>
Approvers           <n>     <h>              +16 (Ed. 201)      <n>            <r>
Site administrators <n>     <h>              30 (Site admin)    <n>            <r>
System admins       <n>     <h>              20 (Hosting & ops) <n>            <r>

Routine cost   = sum(People x h/month x 12 x Rate)
Training cost  = sum(People x training h x Rate) + new people x training h x Rate
One-off work   = migration + acceptance + exit drill hours x Rate
```

## Ergebnis

Eine Position für Arbeitszeit und eine für Schulung im Budget, mit Annahmen, die Sie überarbeiten können.

## Fallstricke

- Die Zeit der Teilnehmer in der Schulung auslassen.
- Vergessen, dass die Arbeit eines Lieferanten weiterhin die Zeit Ihrer Mitarbeiter braucht: Prüfungen, Freigaben,
  Abnahmetests.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Schulungskosten](../../beschaffung/gesamtbetriebskosten/schulungskosten.md)
- [Ein Schulungsplan für eine Behörde](../../beschaffung/schulung/schulungsplan-fuer-eine-oeffentliche-stelle.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
