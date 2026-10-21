---
title: Lizenzkosten - was die GPL für Ihr Budget bedeutet
audience: government, project-manager, sales-marketing
category: public-sector
order: 40
summary: Was eine öffentliche Stelle für die Lizenz von Exponential unter der GNU GPL Version 2 oder neuer bezahlt und was nicht - keine Lizenzgebühr, keine Gebühr je Benutzer oder Website - und welche Kosten bleiben, mit einer Anleitung, wie Sie Zeilen zu Lizenz und Abonnement in Angeboten lesen.
keywords: Lizenzkosten, Lizenzgebühr, GPL, GNU General Public License, Open Source, freie Software, Abonnement, Lizenz je Benutzer, keine Lizenzgebühr, Budget, Erweiterungen, Dienste Dritter, license cost, license fee, open source, free software, subscription, per-user license, no license fee, budget, extensions, third-party services
type: help
---
# Lizenzkosten - was die GPL für Ihr Budget bedeutet

Diese Seite richtet sich an Personen mit Budgetverantwortung und Beschaffungsstellen, die in einem Angebot „Lizenz:
keine“ lesen und wissen müssen, was das bedeutet. Exponential ist freie Software unter der GNU General Public License,
Version 2 oder (nach Ihrer Wahl) jeder späteren Version. Die Fakten stammen aus den [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md) und der
[GNU GPL Version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) selbst.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Was Sie nicht bezahlen](#was-sie-nicht-bezahlen)
2. [Was Sie trotzdem bezahlen](#was-sie-trotzdem-bezahlen)
3. [Lizenzzeilen in Angeboten lesen](#lizenzzeilen-in-angeboten-lesen)
4. [Erweiterungen und Dienste Dritter](#erweiterungen-und-dienste-dritter)
5. [Was Ihnen die Lizenz gibt](#was-ihnen-die-lizenz-gibt)
6. [Die Lizenz der Dokumentation](#die-lizenz-der-dokumentation)

## Was Sie nicht bezahlen

- **Keine Lizenzgebühr** für den Kernel: `"license": "GPL-2.0-or-later"` in `composer.json`.
- **Keine Gebühr je Benutzer, Redaktionsmitglied, Website oder Server.** Die GPL erlaubt jede Nutzung, einschließlich
  so vieler Websites, wie Sie brauchen, und der Installation auf so vielen Servern, wie Sie möchten.
- **Keine Gebühr, um den Quellcode zu sehen oder zu ändern.** Der Code ist öffentlich auf GitHub ([Kanäle der Community](../../../projekt/community-kanaele.md)).
- **Keine Gebühr für die Dokumentation**, die freie Dokumentation unter der GNU FDL 1.3 oder neuer ist.

## Was Sie trotzdem bezahlen

| Kosten | Warum sie bleiben |
|---|---|
| Umsetzung | jemand baut Ihr Inhaltsmodell, Ihr Design und Ihre Integrationen |
| Hosting | Server oder ein Hosting-Dienst ([Hostingkosten](hostingkosten.md)) |
| Support und Wartung | kostenlose Hilfe der Community verspricht keine Reaktionszeit ([Support erhalten](../../../projekt/unterstuetzung-erhalten.md)) |
| Upgrades und Sicherheitsreleases | jemand spielt sie ein ([Kosten für Upgrades und Wartung](kosten-fuer-upgrades-und-wartung.md)) |
| Personal | Redaktion, Administratoren, Webteam ([Personal und Kompetenzen](personal-und-kompetenzen.md)) |
| Schulung | das Kursmaterial ist kostenlos; die Zeit der Trainerinnen und Trainer nicht ([Schulungskosten](schulungskosten.md)) |
| Compliance-Arbeit | Audit der Barrierefreiheit, Sicherheitstests, Datenschutz |

Ein Angebot ohne Lizenzgebühr ist also kein Angebot ohne Kosten. Vergleichen Sie die Gesamtkosten wie in
[Der Aufbau eines TCO-Modells](aufbau-des-tco-modells.md).

## Lizenzzeilen in Angeboten lesen

| Das Angebot sagt | Fragen Sie |
|---|---|
| „Lizenz: kostenlos / keine“ | für den Kernel richtig; sind kostenpflichtige Erweiterungen oder Dienste enthalten? |
| „Jahresabonnement“ für Exponential | wofür genau ist das Abonnement: Support, Updates, Hosting? Die Software selbst hat keine Lizenzgebühr |
| „Lizenz für die Enterprise Edition“ | welcher Code ist darin, und unter welcher Lizenz? Code, der an Sie weitergegeben wird und auf Exponential aufbaut, wird unter der GPL weitergegeben |
| „Proprietäres Modul, Quellcode wird nicht geliefert“ | die Fragen zur Lizenzierung erklären, dass Erweiterungen abgeleitete Werke sind; wer eine erhält, erhält auch ihren Quellcode |

Die GPL erlaubt einem Lieferanten, für Kopien, Installation, Support und Updates Geld zu verlangen; sie legt nur die
Lizenz fest, unter der Sie den Code erhalten ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md#darf-ich-exponential-oder-eine-erweiterung-oder-ein-design-dafür-verkaufen)).

## Erweiterungen und Dienste Dritter

- Vom Projekt veröffentlichte Erweiterungen sind freie Software; prüfen Sie die Lizenz jeder einzelnen auf ihrer Seite
  ([Lizenzen und Versionen der Pakete](../../../../funktionen/6.0/paketlizenzen-und-versionen.md)).
- Manche Erweiterungen rufen bei Aktivierung Dienste Dritter auf (Analyse, Captcha, Anmeldung über soziale Netzwerke,
  Karten). Die Erweiterung ist kostenlos; der Dienst kann eigene Bedingungen und Kosten haben ([Datenschutzstandards](../../../../standards/datenschutz.md#drittanbieterdienste-in-mitgelieferten-extensions)).
- Ein KI-Schreibassistent braucht ein Konto bei einem Anbieter; seine Nutzung wird an den Anbieter bezahlt (siehe den
  Kurs Exponential Intelligence, [Exponential Intelligence](../../../trainer/exponential-intelligence/README.md)).

## Was Ihnen die Lizenz gibt

- Das Recht, die Software auszuführen, zu untersuchen, zu ändern und weiterzugeben, was einen Lieferantenwechsel möglich
  macht.
- Keine Pflicht, den Code Ihrer eigenen Website zu veröffentlichen: Der Besuch einer Website ist keine Weitergabe im
  Sinne der GPL.
- Ihre eigenen Erweiterungen müssen nicht geteilt werden, es sei denn, Sie geben sie weiter.

Diese Rechte senken das langfristige Kostenrisiko: Kein Lieferant kann die Lizenz entziehen oder ihren Preis erhöhen.

## Die Lizenz der Dokumentation

Die Dokumentation, einschließlich dieser Hilfe, ist freie Dokumentation unter der
[GNU FDL 1.3](https://www.gnu.org/licenses/fdl-1.3.html) oder neuer: Sie dürfen sie in Ihre eigenen Handbücher und
Schulungsmaterialien kopieren ([Die Lizenz dieser Dokumentation](../../../../README-LIZENZ.md)).

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
