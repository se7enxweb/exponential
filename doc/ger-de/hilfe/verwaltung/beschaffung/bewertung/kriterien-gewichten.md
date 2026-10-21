---
title: Die Bewertungskriterien gewichten
audience: government, project-manager
category: public-sector
order: 40
summary: Wie eine öffentliche Stelle die Gewichtung ihrer Bewertungskriterien für ein CMS festlegt - Qualität gegen Preis, Mindestanforderungen gegen bewertete Anforderungen und welche Kriterien für öffentliche Websites mehr zählen - mit einem Arbeitsblatt, das die Gründe festhält. Es werden keine Gewichtungen vorgegeben.
keywords: Gewichtung, Gewichtung der Kriterien, Zuschlagskriterien, Preis-Leistungs-Verhältnis, wirtschaftlichstes Angebot, Mindestanforderungen, bewertete Anforderungen, Bewertungsmodell, weighting, criteria weights, award criteria, quality price ratio, most economically advantageous tender, MEAT, mandatory requirements, scored requirements, evaluation model
type: help
---
# Die Bewertungskriterien gewichten

Diese Seite richtet sich an die Projektleitung und die Beschaffungsstelle, die die Zuschlagskriterien und ihre
Gewichtungen veröffentlichen müssen, bevor Angebote eingehen. Sie erklärt die Entscheidungen hinter einem
Gewichtungsmodell für ein Content-Management-System und gibt ein Arbeitsblatt, das festhält, warum jede Gewichtung
gewählt wurde. Sie gibt keine Zahlen vor: Die Gewichtungen sind Ihre politische Entscheidung und müssen Ihren
Vergaberegeln folgen.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Mindestanforderung oder bewertet](#mindestanforderung-oder-bewertet)
2. [Qualität und Preis](#qualität-und-preis)
3. [Kriterien, die bei öffentlichen Websites üblicherweise gewichtet werden](#kriterien-die-bei-öffentlichen-websites-üblicherweise-gewichtet-werden)
4. [Was die Dokumentation belegen kann und was nicht](#was-die-dokumentation-belegen-kann-und-was-nicht)
5. [Ein Arbeitsblatt zur Gewichtung](#ein-arbeitsblatt-zur-gewichtung)
6. [Häufige Fehler](#häufige-fehler)

## Mindestanforderung oder bewertet

Entscheiden Sie für jede Anforderung, ob sie eine **Mindestanforderung** ist (ein Angebot, das sie nicht erfüllt, wird
ausgeschlossen) oder **bewertet** wird (bessere Antworten erhalten mehr Punkte).

| Machen Sie sie zur Mindestanforderung, wenn | Bewerten Sie sie, wenn |
|---|---|
| das Gesetz sie von Ihnen verlangt (zum Beispiel die Barrierefreiheit öffentlicher Websites in vielen Rechtsordnungen) | mehrere Qualitätsstufen möglich und nützlich sind |
| das System ohne sie gar nicht nutzbar ist | der Markt sie vielleicht noch nicht vollständig erfüllt |
| sie sich mit Ja oder Nein prüfen lässt | Sie vergleichen wollen, wie gut Bieter sie erfüllen |

Eine Mindestanforderung, die niemand belegen kann, schließt jeden Bieter aus. Ein Beispiel: „Das Produkt hat ein
veröffentlichtes VPAT“ würde Exponential ausschließen, weil es keines gibt ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md#section-508)).
„Der Bieter liefert vor der Abnahme einen Accessibility Conformance Report für die gelieferte Website“ kann ein Bieter
erfüllen, der die Arbeit leistet, unabhängig vom Produkt.

## Qualität und Preis

- Viele öffentliche Stellen erteilen den Zuschlag auf das wirtschaftlichste Angebot: eine Mischung aus Qualität und
  Preis. In der EU legt die [Richtlinie 2014/24/EU über die öffentliche Auftragsvergabe](https://eur-lex.europa.eu/eli/dir/2014/24/oj) diesen Rahmen fest; prüfen Sie Ihre nationalen Regeln für die genaue Methode.
- Der Anteil des Preises gegenüber der Qualität ist eine politische Entscheidung. Schreiben Sie auf, warum Sie ihn
  gewählt haben.
- Vergleichen Sie den Preis als **Gesamtbetriebskosten** über die Vertragslaufzeit, nicht als Lizenzpreis. Exponential
  hat unter der GNU GPL keine Lizenzgebühr ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)), aber
  Umsetzung, Hosting, Support und Schulung kosten trotzdem Geld; siehe den Ordner total-cost-of-ownership.

## Kriterien, die bei öffentlichen Websites üblicherweise gewichtet werden

| Kriterium | Warum es im öffentlichen Sektor zählt | Nachweise zu Exponential, die in einem Angebot zu erwarten sind |
|---|---|---|
| Barrierefreiheit | öffentliche Stellen sind oft gesetzlich verpflichtet, barrierefreie Websites zu veröffentlichen | [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md) und der Plan des Bieters für das öffentliche Design |
| Sicherheit | öffentliche Websites sind Ziele; oft gelten Sicherheitspflichten | [Sicherheitsstandards](../../../../../eng-us/standards/security.md), [Sicherheitshärtung](../../../sicherheitshaertung.md) |
| Datenschutz | personenbezogene Daten von Bürgerinnen und Bürgern | [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) |
| Sprachen | Amts- oder Minderheitensprachen, Schriften von rechts nach links | [Internationalisierung](../../../../../eng-us/standards/internationalization.md), [Rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md) |
| Redaktioneller Workflow | Freigaben, Versionen, Rechenschaft | [Workflows](../../../../anleitungen/workflows.md), [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| Freiheit beim Hosting und Speicherort der Daten | wo Daten gespeichert werden | [Hosting: ein Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md) |
| Ausstieg und Portabilität | die Möglichkeit, den Lieferanten zu wechseln | [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md) |
| Support und Kontinuität | eine Website lebt jahrelang | [Support erhalten](../../../projekt/unterstuetzung-erhalten.md), `SECURITY.md` |
| Schulung | Redaktion über Abteilungen hinweg | [Kurskatalog](../../../../../eng-us/help/trainers/course-catalogue.md) |

## Was die Dokumentation belegen kann und was nicht

Gewichten Sie Kriterien in dem Wissen, welche ein Bieter belegen kann:

- **Durch das Repository belegt**: Funktionen, Einstellungen, Tests, Release-Richtlinie, Exportwerkzeuge, der Status jedes
  Standards (Complies, Partly complies, Unverified), wie in [Standards: wie der Status zu lesen ist](../../../../../eng-us/standards/README.md#how-to-read-the-status) festgelegt.
- **Nicht durch das Repository belegt**: ein VPAT oder Accessibility Conformance Report, eine Bewertung nach OWASP ASVS,
  ein externer Penetrationstest, eine Bewertung nach EN 301 549, veröffentlichte SLAs des Projekts ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)
  hält fest, dass das Projekt keine veröffentlicht). Diese müssen vom Bieter kommen oder Teil der Vertragsleistung sein.

## Ein Arbeitsblatt zur Gewichtung

```text
Criterion                    Mandatory parts                 Scored parts          Weight   Reason for the weight
---------------------------  ------------------------------  --------------------  -------  ---------------------
Accessibility                <e.g. ACR for delivered site>   <...>                 <...>    <policy, law, users>
Security                     <...>                           <...>                 <...>    <...>
Privacy                      <...>                           <...>                 <...>    <...>
Languages                    <...>                           <...>                 <...>    <...>
Editorial functions          <...>                           <...>                 <...>    <...>
Hosting and data location    <...>                           <...>                 <...>    <...>
Support and maintenance      <...>                           <...>                 <...>    <...>
Training and documentation   <...>                           <...>                 <...>    <...>
Exit and portability         <...>                           <...>                 <...>    <...>
Price (total cost)           -                               formula: <...>        <...>    <...>
Approved by: <name, role, date>
```

## Häufige Fehler

- Gewichtungen, die sich nicht zu 100 addieren, oder Unterkriterien, die nicht veröffentlicht wurden.
- Ein hohes Gewicht auf einem Kriterium, das niemand belegen kann: Die Bewertenden bewerten dann Behauptungen.
- Nur den Lizenzpreis gewichten, was Produkte ohne Lizenzgebühr bevorzugt, ohne die Arbeit zu zählen, die sie brauchen,
  oder umgekehrt.
- Den Ausstieg vergessen: ein niedriger Preis mit hohen Wechselkosten später.

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
