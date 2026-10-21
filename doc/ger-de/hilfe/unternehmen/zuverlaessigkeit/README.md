---
title: Zuverlässigkeit für Unternehmen
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 1
summary: Die Einstiegsseite des Bereichs Zuverlässigkeit. Sie erklärt, warum sich ein Unternehmen darauf verlassen kann, dass Exponential stabil bleibt, und wie Sie eine Website so betreiben, dass Kunden nie abgewiesen werden, mit einer Tabelle aller Seiten des Bereichs.
keywords: Zuverlässigkeit, Stabilität, Verfügbarkeit, Backups, Monitoring, Aktualisierungen, Wartung, Velocity, Caching, Störungsbehandlung, Geschäftskontinuität, reliability, stability, uptime, availability, backups, monitoring, upgrades, maintenance, caching, incident response, business continuity
type: help
---
# Zuverlässigkeit für Unternehmen

Dieser Bereich richtet sich an Inhaber, Führungskräfte und Entscheider, die mit einer Website, einem Shop, einem
Intranet oder einem App-Backend auf Basis von Exponential Geld verdienen oder Mitglieder betreuen, und an die Person,
die diese Website am Laufen hält. Er beantwortet zwei Fragen: warum Sie sich darauf verlassen können, dass Exponential
über viele Jahre stabil bleibt, und was Sie tun müssen, damit die Website Ihre Kunden nie im Stich lässt.

Die Seiten sind zuerst in einfacher Sprache geschrieben. Jede nennt danach die Befehle, Einstellungen und
Administrationsseiten, die die Arbeit erledigen, und verweist auf die technische Anleitung mit den Einzelheiten, sodass
nichts doppelt beschrieben ist.

## Inhalt

1. [Was dieser Bereich abdeckt](#was-dieser-bereich-abdeckt)
2. [Für wen er gedacht ist](#für-wen-er-gedacht-ist)
3. [Alle Seiten dieses Bereichs](#alle-seiten-dieses-bereichs)
4. [Eine empfohlene Reihenfolge](#eine-empfohlene-reihenfolge)
5. [Die fünf Gewohnheiten in einer Liste](#die-fünf-gewohnheiten-in-einer-liste)
6. [Benachbarte Bereiche](#benachbarte-bereiche)

## Was dieser Bereich abdeckt

Zuverlässigkeit ist keine einzelne Funktion. Sie ist die Summe aus einem stabilen Produkt, einer sorgfältigen
Release-Linie und einigen Gewohnheiten, die das Team, das die Website betreibt, jede Woche pflegt. Dieser Bereich deckt
alle drei ab:

- **Das Produkt**: eine langlebige Release-Linie, veröffentlichte Versionen, die sich nie ändern, ein unterstützter
  PHP-Bereich und eine gepflegte Sicherheitsrichtlinie
  ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- **Die mitgelieferten Werkzeuge**: Health-Checks, Logs mit Fehlerreferenzen, ein manipulationssicherer Audit-Trail,
  Wartungsmodus, eine Upgrade-Prüfung, Caches, der Server Exponential Velocity und sein Watchdog.
- **Die Gewohnheiten**: Backups, die gezielt wiederhergestellt werden, Monitoring, das jemand liest, Aktualisierungen,
  die geprobt werden, und ein Notfallplan, der zu einem kleinen Team passt.

> **Hinweis**
> Keine Seite in diesem Bereich verspricht einen Verfügbarkeitswert in Prozent. Das Projekt veröffentlicht keine solche
> Zahl, und die Verfügbarkeit hängt ebenso von Ihrem Hosting, Ihrem Netzwerk und Ihren Mitarbeitern ab wie von der
> Software ([Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#verfügbarkeit-in-einem-hosting-tarif)).
> Die Seiten geben Ihnen die Mittel, ein Versprechen zu halten, das Sie selbst geben.

## Für wen er gedacht ist

| Sie sind | Beginnen Sie mit |
|---|---|
| Inhaber oder Führungskraft und entscheiden, ob Sie sich auf Exponential verlassen | [Warum Stabilität wichtig ist](../../../../eng-us/help/businesses/reliability/why-stability-matters.md), dann [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md) |
| Führungskraft, die den Betrieb budgetieren muss | [Verfügbarkeit in der Praxis](../../../../eng-us/help/businesses/reliability/uptime-practice.md) und dem [Bewertungsbogen Zuverlässigkeit](bewertungsbogen-zuverlaessigkeit.md) |
| Die Person, die die Website im Alltag betreibt | [Health-Checks und Monitoring](health-checks-und-monitoring.md), [Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md), [Cronjobs und Hintergrundjobs](cronjobs-und-hintergrundjobs.md) |
| Ein kleines Team, das reagieren muss, wenn etwas ausfällt | [Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md) |
| Shop-Inhaber, der einen Sale oder eine Kampagne erwartet | [Lastspitzen planen](lastspitzen-planen.md) und [Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md) |

## Alle Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Warum Stabilität wichtig ist](../../../../eng-us/help/businesses/reliability/why-stability-matters.md) | Was eine instabile Website ein Unternehmen kostet und was Exponential dagegen tut |
| [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md) | Welche Versionen gepflegt werden, wie Releases funktionieren, unter welchen PHP-Versionen es läuft |
| [Verfügbarkeit in der Praxis](../../../../eng-us/help/businesses/reliability/uptime-practice.md) | Die Routine, die eine Website Woche für Woche verfügbar hält |
| [Health-Checks und Monitoring](health-checks-und-monitoring.md) | Was Sie von außen und von innen beobachten und mit welchen Befehlen |
| [Logs und Fehlerreferenzen](logs-und-fehlerreferenzen.md) | Wie aus dem „Etwas ist schiefgelaufen“ eines Kunden eine Log-Zeile wird, mit der Sie arbeiten können |
| [Audit-Trail für den Betrieb](audit-trail-fuer-den-betrieb.md) | Wer was geändert hat, und der Nachweis, dass die Aufzeichnung nicht bearbeitet wurde |
| [Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md) | Was Sie sichern, wie und wie oft |
| [Wiederherstellungsübungen](wiederherstellungsuebungen.md) | Wie Sie beweisen, dass ein Backup funktioniert, bevor Sie es brauchen |
| [Velocity für Unternehmen](../../../../eng-us/help/businesses/reliability/velocity-for-businesses.md) | Was der mitgelieferte Anwendungsserver einem Unternehmen bringt, in einfachen Worten |
| [Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md) | Änderungen ausliefern, ohne Anfragen von Besuchern zu verlieren |
| [Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md) | Wie Caches eine Website unter Last antwortfähig halten |
| [Statischer Cache und Antwortcache](../../../../eng-us/help/businesses/reliability/static-and-response-cache.md) | Die beiden Caches, die antworten, ohne die Anwendung auszuführen |
| [Lastspitzen planen](lastspitzen-planen.md) | Vorbereitung auf einen Sale, eine Kampagne oder einen Newsletter-Versand |
| [Clustering und Hochverfügbarkeit](clustering-und-hochverfuegbarkeit.md) | Wann ein Server nicht reicht und was weitere Server kosten |
| [Datenbankwahl für Zuverlässigkeit](datenbankwahl-fuer-zuverlaessigkeit.md) | Welche Datenbank zu welchem Unternehmen passt, in der offiziellen Reihenfolge des Projekts |
| [Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md) | Die Website sauber vom Netz nehmen und die Kunden informieren |
| [Aktualisierungen ohne Überraschungen](../../../../eng-us/help/businesses/reliability/upgrades-without-surprises.md) | Eine Aktualisierung planen und proben |
| [Upgrade-Prüfung und Dateikonsistenz](../../../../eng-us/help/businesses/reliability/upgrade-check-and-file-consistency.md) | Nachweisen, dass die Installation vor und nach einer Aktualisierung dem Release entspricht |
| [Vorschau vor dem Veröffentlichen](vorschau-vor-dem-veroeffentlichen.md) | Entwürfe, Versionen, Freigabe und Vorschau, damit ein Fehler nie bei den Kunden ankommt |
| [Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md) | Ein Plan für den schlechten Tag, der zu zwei oder drei Personen passt |
| [Cronjobs und Hintergrundjobs](cronjobs-und-hintergrundjobs.md) | Die Arbeit, auf die niemand wartet, und wie Sie wissen, dass sie gelaufen ist |
| [Bewertungsbogen Zuverlässigkeit](bewertungsbogen-zuverlaessigkeit.md) | Eine Checkliste, mit der Sie Ihren eigenen Betrieb bewerten |
| [FAQ zur Zuverlässigkeit](faq-zur-zuverlaessigkeit.md) | Kurze Antworten auf die Fragen, die Führungskräfte am häufigsten stellen |

## Eine empfohlene Reihenfolge

1. Lesen Sie [Warum Stabilität wichtig ist](../../../../eng-us/help/businesses/reliability/why-stability-matters.md) und
   [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md), um zu verstehen, was Ihnen das Produkt
   bietet.
2. Lesen Sie [Verfügbarkeit in der Praxis](../../../../eng-us/help/businesses/reliability/uptime-practice.md), um die ganze Routine auf einer Seite zu sehen.
3. Richten Sie die Grundlagen ein: [Health-Checks und Monitoring](health-checks-und-monitoring.md),
   [Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md),
   [Cronjobs und Hintergrundjobs](cronjobs-und-hintergrundjobs.md).
4. Beweisen Sie, dass sie funktionieren: [Wiederherstellungsübungen](wiederherstellungsuebungen.md) und
   [Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md).
5. Planen Sie Änderungen: [Aktualisierungen ohne Überraschungen](../../../../eng-us/help/businesses/reliability/upgrades-without-surprises.md),
   [Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md),
   [Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md).
6. Planen Sie Wachstum: [Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md),
   [Lastspitzen planen](lastspitzen-planen.md),
   [Clustering und Hochverfügbarkeit](clustering-und-hochverfuegbarkeit.md).
7. Bewerten Sie sich jedes Quartal mit dem [Bewertungsbogen Zuverlässigkeit](bewertungsbogen-zuverlaessigkeit.md).

## Die fünf Gewohnheiten in einer Liste

- [ ] Etwas außerhalb Ihres Servers prüft die Website und die Anmeldeseite der Administration und alarmiert eine Person.
- [ ] Ein Backup der Datenbank und der Dateien aus den letzten sieben Tagen existiert und wurde mindestens einmal auf
      einem anderen Rechner wiederhergestellt ([Betrieb einer Site, die wöchentliche Checkliste](../../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste)).
- [ ] Die Cronjobs laufen, und **Setup > Systeminformationen** zeigt keine fehlgeschlagene Zustandsprüfung.
- [ ] Jede Aktualisierung beginnt mit einem Backup und der Upgrade-Prüfung, innerhalb eines Wartungsfensters.
- [ ] Zwei Personen wissen, wo die Logs, der Audit-Trail und die Backups liegen.

## Benachbarte Bereiche

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md) und
  [Warum Unternehmen sich auf Exponential verlassen](../warum-unternehmen-sich-auf-exponential-verlassen.md): die Einstiegsseiten des
  Bereichs.
- [Kosten und Rendite](../kosten/README.md): die Kosten eines Ausfalls und die Rendite der Stabilität, in Zahlen, die Sie
  selbst eintragen.
- [Sicherheit](../../../../eng-us/help/businesses/security/README.md): Grundlagen von Sicherheit und Datenschutz für Unternehmen.
- [Hosting und Partner auswählen](../auswahl/README.md): wer die Server für Sie betreibt.
- [Vorlagen für Unternehmen](../vorlagen/README.md): Checklisten zum Kopieren, darunter eine monatliche
  Betriebs-Checkliste.
- Behörden haben einen eigenen Bereich: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).
- Die technische Seite für Hosting-Mitarbeiter: [Betrieb](../../hosting/betrieb/README.md) und
  [Runbooks für Hoster: Überblick](../../hosting/runbooks/README.md).
- Für Führungskräfte: [Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md) und
  [Risikomanagement für Exponential-Projekte](../../fuehrung/risikomanagement.md).

## Verwandte Seiten

- [Betrieb einer Site](../../../anleitungen/betrieb-einer-website.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
