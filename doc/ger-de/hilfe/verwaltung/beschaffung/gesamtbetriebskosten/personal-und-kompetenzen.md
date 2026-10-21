---
title: Personal und Kompetenzen in den Kosten einer Exponential-Website
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Die internen Rollen, die eine öffentliche Stelle für den Betrieb einer Website oder eines Intranets auf Exponential braucht - Redaktion, Webteam, Administration der Website, Betrieb, Sicherheit und Datenschutz -, die Kompetenzen, die jede braucht, die Kurse, die sie vermitteln, und wie Sie ihre Zeit in den Gesamtbetriebskosten schätzen.
keywords: Personalkosten, interne Kosten, Rollen, Kompetenzen, Redaktion, Webmanagement, Administration der Website, Systemadministration, Entwicklung, Personalausstattung, VZÄ, Beschaffung, TCO, staff cost, internal cost, roles, skills, editors, web manager, site administrator, system administrator, developer, staffing, FTE, sourcing
type: help
---
# Personal und Kompetenzen in den Kosten einer Exponential-Website

Diese Seite richtet sich an Führungskräfte, die die interne Personalzeit zählen müssen, die eine Website braucht, und
entscheiden, was sie im Haus erledigen und was sie einkaufen. Interne Zeit erscheint selten in einem Angebot, ist aber
oft ein großer Teil der Gesamtbetriebskosten. Jede Rolle unten verlinkt den Kurs der Hilfe, der ihre Kompetenzen
vermittelt ([Kurskatalog](../../../trainer/kurskatalog.md)).

## Inhalt
1. [Rollen und ihre Kompetenzen](#rollen-und-ihre-kompetenzen)
2. [Im Haus oder eingekauft](#im-haus-oder-eingekauft)
3. [Personalzeit schätzen](#personalzeit-schätzen)
4. [Kompetenzen über die Jahre erhalten](#kompetenzen-über-die-jahre-erhalten)
5. [Fallstricke](#fallstricke)

## Rollen und ihre Kompetenzen

| Rolle | Tut | Kompetenzen | Kurs |
|---|---|---|---|
| Redaktion (in den Abteilungen) | schreibt und aktualisiert Seiten | die Administration, Entwürfe, Versionen, Bilder, barrierefreie Inhalte | [Editors 101](../../../trainer/redakteure-101/README.md), [Editors 201](../../../trainer/redakteure-201/README.md) |
| Webmanagement | redaktionelle Standards, Freigaben, Struktur | Inhaltsmodell, Workflows, Objektzustände, Sprachen | Editors 201, [Exponential Layouts](../../../trainer/layouts/README.md) |
| Administration der Website | Benutzer, Rollen, Sektionen, Caches, Cronjobs, Protokolle, Audit | Administration einer Installation, grundlegende Kommandozeile | [Administration der Website](../../../trainer/website-administratoren/README.md) |
| Systemadministration | Server, PHP, Datenbank, Sicherungen, Überwachung, Updates | Linux, Webserver, Datenbanken | [Hosting und Betrieb](../../../trainer/hosting-betrieb/README.md) |
| Entwicklung (im Haus oder beim Lieferanten) | Templates, Erweiterungen, Integrationen | PHP, Git, die Template-Sprache | [Entwickler](../../../trainer/entwickler/README.md), [Theme-Design](../../../trainer/theme-designer/README.md) |
| Sicherheitsbeauftragte | Härtung, Überprüfungen, Sicherheitsvorfälle | Sicherheitspraxis | [Leitfaden zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md) |
| Datenschutzbeauftragte | Einwilligung, Aufbewahrung, Anfragen | Datenschutzrecht, die Datenschutzfunktionen | [Datenschutzstandards](../../../../standards/datenschutz.md) |
| Verantwortliche für Barrierefreiheit | Tests, Erklärung, Coaching der Redaktion | WCAG, Testmethoden | [Die Barrierefreiheit testen](../../../ersteller/website-ersteller/barrierefreiheit-testen.md) |

## Im Haus oder eingekauft

| Meist im Haus | Oft eingekauft | Beides |
|---|---|---|
| Redaktion, Webmanagement | Umsetzungsprojekt, Design, Penetrationstest | Administration der Website |
| Entscheidungen zum Datenschutz | Hosting und Systemadministration | Entwicklung von Templates und Erweiterungen |
| Barrierefreiheit Ihrer eigenen Inhalte | Audit der Barrierefreiheit der gelieferten Website | Support und Wartung |

Behalten Sie mindestens eine Person im Haus, die die Website administrieren und die Arbeit des Lieferanten beurteilen
kann. Ohne sie wird jede Frage zu einem Support-Ticket, und der Ausstieg wird schwieriger.

## Personalzeit schätzen

Schätzen Sie je Rolle, in Stunden je Zeitraum, aus Ihrer eigenen Erfahrung oder einem Pilotprojekt:

```text
Role                 People   Hours per month (each)   Months   Internal rate   Cost
Editor               <n>      <h>                       <m>      <rate>          <...>
Web manager          <n>      <h>                       <m>      <rate>          <...>
Site administrator   <n>      <h>                       <m>      <rate>          <...>
System administrator <n>      <h>                       <m>      <rate>          <...>
...
```

Messen Sie Routineaufgaben während des Proof of Concept: Wie lange dauert es, eine Seite mit Freigabe zu
veröffentlichen, einen Benutzer mit einer Rolle anzulegen, ein Release auf Staging einzuspielen? Wiederkehrende
Betriebsaufgaben sind in [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md) und [Monatlicher Betrieb](../../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md) beschrieben.

Zählen Sie nur die zusätzliche Zeit, die die Website verursacht. Eine Redaktion, die vorher Inhalte geschrieben hat,
schreibt auch danach Inhalte; zählen Sie den Unterschied.

## Kompetenzen über die Jahre erhalten

- Personalwechsel: Planen Sie die Einarbeitung neuer Redaktionsmitglieder (siehe den Ordner zur Schulung der
  Beschaffung).
- Dokumentation in der Administration: Die Hilfe öffnet sich über die Schaltfläche „?“ und ist auf einer neuen
  Installation für die Redaktion lesbar ([Hilfesystem](../../../../funktionen/6.0/hilfesystem.md)).
- Das Kursmaterial ist freie Dokumentation; Sie dürfen es in Ihre eigene Schulung übernehmen und anpassen
  ([Freie Dokumentation](../../../freie-dokumentation.md)).

## Fallstricke

- Annehmen, ein gehosteter oder verwalteter Dienst brauche keine interne Administration.
- Die Zeit der Redaktion in den Abteilungen vergessen, weil sie sich auf viele Personen verteilt.
- Einmal beim Start schulen und nie wieder.

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Schulungskosten](schulungskosten.md)
- [Kurskatalog](../../../trainer/kurskatalog.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
