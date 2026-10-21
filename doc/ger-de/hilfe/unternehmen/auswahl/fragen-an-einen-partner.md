---
title: Fragen an einen Partner
audience: businesses, project-manager, partner, hosting
category: business-solutions
order: 40
summary: Eine kopierfertige Liste von Fragen für Gespräche und Ausschreibungen mit Implementierungs-, Hosting- und Support-Partnern für eine Exponential-Website - Erfahrung, Vorgehen, Hosting, Sicherheit, Datenschutz, Updates, Support, Kosten und Ausstieg - mit dem, was eine gute Antwort enthält.
keywords: Fragen, Partnergespräch, Ausschreibung, RFP, Anbieterfragebogen, Due Diligence, Fragen zum Hosting, Fragen zur Sicherheit, Support, SLA, Ausstieg, Referenzen, questions, partner interview, request for proposal, vendor questionnaire, due diligence, hosting questions, security questions, exit, references
type: help
---
# Fragen an einen Partner

Diese Seite ist für alle, die Gespräche mit Firmen führen, die eine Exponential-Website bauen, hosten oder betreuen
sollen. Kopieren Sie die Listen in Ihre Ausschreibung oder Ihre Gesprächsnotizen. Nach jeder Gruppe sagt „Eine gute
Antwort“, worauf Sie achten sollten, gestützt auf das, was die Dokumentation des Projekts möglich macht.

## Inhalt

1. [Wie Sie die Fragen verwenden](#wie-sie-die-fragen-verwenden)
2. [Erfahrung und Team](#erfahrung-und-team)
3. [Vorgehen und das Inhaltsmodell](#vorgehen-und-das-inhaltsmodell)
4. [Hosting und Betrieb](#hosting-und-betrieb)
5. [Sicherheit und Datenschutz](#sicherheit-und-datenschutz)
6. [Updates und die lange Sicht](#updates-und-die-lange-sicht)
7. [Support und Reaktion](#support-und-reaktion)
8. [Kosten](#kosten)
9. [Eigentum und Ausstieg](#eigentum-und-ausstieg)

## Wie Sie die Fragen verwenden

- Schicken Sie jedem Kandidaten dieselben Fragen, schriftlich.
- Verlangen Sie schriftliche Antworten; vertiefen Sie sie dann im Gespräch.
- Bewerten Sie die Antworten mit dem [Bewertungsbogen für Anbieter](../vorlagen/bewertungsbogen-fuer-anbieter.md).
- Lassen Sie Gruppen weg, die nicht zutreffen (eine reine Designagentur muss keine Fragen zum Hosting beantworten).

## Erfahrung und Team

```text
1. Which Exponential sites have you built or run? May we see them, with the customers' permission?
2. May we call one of those customers?
3. Who exactly will work on our project, and what is their experience with Exponential?
4. Are you part of the Exponential Partner Program? Which profile and level?
5. Have you contributed fixes, extensions or documentation to the project?
```

Eine gute Antwort nennt echte, sichtbare Arbeit und Menschen und bietet ein Referenzgespräch an. Sie zitiert keine
Kunden, die nicht zugestimmt haben
([Fallstudien und Content-Marketing schreiben](../../geschaeft/fallstudien-und-content-marketing.md) erklärt, warum
die Einwilligung wichtig ist).

## Vorgehen und das Inhaltsmodell

```text
6. How do you plan the content model (classes, sections, roles) with us?
7. Which of our needs will you meet with built-in features or existing extensions, and which with custom code?
8. Where will custom code live: in extensions and template overrides, or in changed kernel files?
9. How will you test accessibility?
10. How do you hand over to our editors and administrators?
```

Eine gute Antwort nutzt eingebaute Funktionen wieder
([Schätzen und Abgrenzen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)), hält
eigenen Code in Erweiterungen und plant Workshops
([Workshops zur Inhaltsmodellierung durchführen](../../fuehrung/workshops-zur-inhaltsmodellierung.md)).

## Hosting und Betrieb

```text
11. Which hosting model, PHP version, database and engine (Velocity or a web server) do you propose, and why?
12. What do backups include, how often, how long kept, and when was the last restore test?
13. How is the site monitored from outside, and who is told when it is down?
14. How are cron jobs and outgoing mail set up?
15. How do you handle planned work: maintenance windows, maintenance mode?
```

Eine gute Antwort passt zu [Hosting auswählen](hosting-auswaehlen.md), nennt das Datum eines Wiederherstellungstests und
deckt alles in [Backups: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md) ab.

## Sicherheit und Datenschutz

```text
16. Who applies security updates for Exponential, extensions, PHP and the operating system, and within how long?
17. Will two-step sign-in be required for administrators?
18. How is the administration protected (host name, network limits)?
19. Where are the servers and backups located? Who can access our data?
20. Which contract do you offer for processing personal data on our behalf?
21. How do you help us answer requests to see, export or delete a person's data?
22. What happens, and who tells whom, if there is a security incident?
```

Eine gute Antwort nennt einen Zeitrahmen für das Einspielen veröffentlichter Korrekturen
([Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md)), verwendet die zweistufige
Anmeldung ([Benutzerkonten und Passwörter](../sicherheit/benutzerkonten-und-passwoerter.md)) und kennt die Mail- und
Audit-Werkzeuge für Anfragen zu Daten ([DSGVO-Grundlagen](../sicherheit/dsgvo-grundlagen.md)).

## Updates und die lange Sicht

```text
23. How do you keep a site on the current 6.0.x release? How often do you update?
24. How do you test an update before it reaches the live site?
25. How do you plan PHP upgrades?
26. What happens to our site if your company stops working with Exponential?
```

Eine gute Antwort beschreibt eine Staging-Kopie, die Upgrade-Prüfung und kleine, häufige Updates
([Budget für Aktualisierungen und Wartung](../kosten/budget-fuer-aktualisierungen-und-wartung.md)).

## Support und Reaktion

```text
27. Which response times do you commit to, per severity, in writing?
28. Are they business hours or around the clock? In which time zone?
29. How do we report a problem, and how do we follow it?
30. Do you escalate to the Exponential project or 7x when needed? How?
```

Eine gute Antwort hat Schweregrade und Zeiten ([Support-Stufen für Exponential-Hosting](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md))
und verspricht nichts, was von anderen abhängt
([SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#sicherheitsupdates)).

## Kosten

```text
31. What is the one-time cost, and what is included?
32. What are the monthly or yearly costs, and what is included?
33. How are changes outside the scope priced?
34. Which third-party costs should we expect (payment provider, mail service, AI provider, CAPTCHA)?
```

Eine gute Antwort liegt schriftlich vor und trennt einmalige, wiederkehrende und Drittkosten, sodass Sie die
[Vorlage ROI-Arbeitsblatt](../vorlagen/vorlage-roi-arbeitsblatt.md) ausfüllen können. Beachten Sie, dass für
Exponential selbst keine Lizenzgebühr auftauchen sollte (`README.md`, „License“).

## Eigentum und Ausstieg

```text
35. Who owns the templates, extensions and design you create for us?
36. Do we hold administrator access and the hosting account?
37. How do we get the code, database and files if we leave? In which format and how fast?
38. Will you cooperate with a new provider during a handover?
```

Eine gute Antwort gibt Ihnen jederzeit Zugang und Kopien. Die GPL erlaubt Ihnen, die Software überallhin mitzunehmen
([Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation](../../geschaeft/lizenzen-fuer-kaeufer.md)); sorgen Sie
dafür, dass der Vertrag dasselbe für Ihre eigene Arbeit und Ihre Daten leistet.

## Verwandte Seiten

- [Einen Partner auswählen](einen-partner-auswaehlen.md)
- [Grundlagen zu Verträgen und SLAs](grundlagen-zu-vertraegen-und-slas.md)
- [Pre-Sales-Unterstützung (für Partner)](../../partner/pre-sales-unterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
