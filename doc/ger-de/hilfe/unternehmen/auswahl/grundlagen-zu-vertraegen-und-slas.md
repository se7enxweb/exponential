---
title: Grundlagen zu Verträgen und SLAs
audience: businesses, project-manager, hosting, partner
category: business-solutions
order: 60
summary: Was ein Unternehmen in einem Hosting-, Entwicklungs- oder Supportvertrag für eine Exponential-Website abdecken sollte - Umfang, Service-Levels (Verfügbarkeit, Reaktion, Wiederherstellung, Updates), wie jeder gemessen wird, Ausschlüsse, Datenschutz, Eigentum unter der GPL, Gewährleistung und Ausstieg - mit einer Checkliste. Keine Rechtsberatung.
keywords: Vertrag, SLA, Service Level Agreement, Verfügbarkeit, Reaktionszeit, RPO, RTO, Wiederherstellung, Wartungsfenster, Sicherheitsupdates, Ausschlüsse, Gewährleistung, GPL, Eigentum, Ausstieg, Auftragsverarbeitung, keine Rechtsberatung, contract, service level agreement, availability, response time, recovery, maintenance window, security updates, exclusions, warranty, ownership, exit, data processing, not legal advice
type: help
---
# Grundlagen zu Verträgen und SLAs

Diese Seite ist für Inhaber, Führungskräfte und Mitarbeiter der Beschaffung, die mit einem Hoster oder Partner einen
Vertrag für eine Exponential-Website aushandeln. Sie listet auf, was ein solcher Vertrag üblicherweise abdeckt, wovon
jede Zusage abhängt, und die Punkte, die speziell für Open-Source-Software unter der GPL gelten.

> **Warnung**
> Diese Seite ist keine Rechtsberatung. Lassen Sie jeden Vertrag von Ihrem Rechtsberater prüfen. Das Projekt
> Exponential selbst veröffentlicht kein SLA; die einzige Reaktionszusage in seinen eigenen Dateien ist eine erste
> Antwort auf eine vertrauliche Sicherheitsmeldung „innerhalb eines Werktags“ (`SECURITY.md`;
> [SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).

## Inhalt

1. [Was der Vertrag abdecken sollte](#was-der-vertrag-abdecken-sollte)
2. [Service-Levels](#service-levels)
3. [Messen, was versprochen wird](#messen-was-versprochen-wird)
4. [Ausschlüsse](#ausschlüsse)
5. [Open-Source-Punkte: GPL, Eigentum und Gewährleistung](#open-source-punkte-gpl-eigentum-und-gewährleistung)
6. [Datenschutz](#datenschutz)
7. [Ausstieg](#ausstieg)
8. [Checkliste vor der Unterschrift](#checkliste-vor-der-unterschrift)

## Was der Vertrag abdecken sollte

| Thema | Welche Fragen es beantwortet |
|---|---|
| Umfang | Welche Websites, Umgebungen (live, Staging) und Dienstleistungen sind enthalten? |
| Rollen | Wer tut was: Updates, Backups, Wiederherstellungen, Monitoring, Inhalte, eigener Code? |
| Service-Levels | Was wird versprochen, wie wird es gemessen, was geschieht, wenn es verfehlt wird? |
| Änderungen | Wie werden Änderungen beantragt, bepreist und genehmigt? |
| Sicherheit | Wie schnell werden Sicherheits-Releases eingespielt? Wer wird über Vorfälle informiert? |
| Datenschutz | Welche personenbezogenen Daten werden verarbeitet, wo und zu welchen Bedingungen? |
| Eigentum | Wem gehören eigene Templates, Erweiterungen, Designs und Inhalte? |
| Gewährleistung und Haftung | Wofür steht der Lieferant ein? |
| Laufzeit und Ausstieg | Wie lange, wie man ihn beendet und wie die Übergabe abläuft |

## Service-Levels

Aus [SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#was-ein-sla-abdecken-kann), die Zusagen,
die ein Hoster oder Partner anbieten kann:

| Zusage | Der Lieferant verspricht | Es hängt ab von |
|---|---|---|
| Verfügbarkeit | die Website antwortet, von außen gemessen | Maschine, Netzwerk, Engine, Datenbank |
| Support-Reaktion | eine erste Antwort innerhalb einer Frist, je nach Schweregrad | seiner Personalausstattung |
| Wiederherstellungspunkt (RPO) | höchstens so viele Daten können verloren gehen | der Häufigkeit der Backups |
| Wiederherstellungszeit (RTO) | eine Wiederherstellung ist innerhalb dieser Zeit abgeschlossen | Backup-Verfahren, Datenmenge, getesteten Wiederherstellungen |
| Wartungsfenster | geplante Arbeiten nur in angekündigten Fenstern | seinen Abläufen |
| Sicherheitsupdates | eine veröffentlichte Sicherheitskorrektur von Exponential wird innerhalb einer Frist eingespielt | seinem Update-Verfahren |
| Plattform-Updates | PHP- und Betriebssystem-Updates werden innerhalb einer Frist eingespielt | seinem Betrieb |

> **Tipp**
> Verlangen Sie „eine veröffentlichte Korrektur wird innerhalb einer Frist eingespielt“, nicht „eine Schwachstelle wird
> innerhalb einer Frist behoben“: Das Zweite hängt vom Projekt ab, das Erste vom Lieferanten
> ([SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#sicherheitsupdates)).

Wählen Sie Stufen, die zu dem passen, was Sie ein Ausfall kostet
([Arbeitsblatt: Kosten eines Ausfalls](../kosten/arbeitsblatt-kosten-eines-ausfalls.md)). Strengere Stufen kosten mehr; eine
Stufe, die Sie nie brauchen, ist Geld für nichts.

## Messen, was versprochen wird

| Zusage | Gemessen durch | Quelle |
|---|---|---|
| Verfügbarkeit | eine externe Prüfung einer Seite, die die Datenbank anspricht | [Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md) |
| Reaktionszeiten | das Ticketsystem des Lieferanten; verlangen Sie Berichte | |
| RPO | Zeitstempel der Backups | [SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#messen-was-sie-versprechen) |
| RTO | die Dauer des letzten Wiederherstellungstests | [Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) |
| Eingespielte Updates | die Version der Website und die Deployment-Einträge des Audit-Trails | [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |

Verlangen Sie einen monatlichen oder vierteljährlichen Bericht mit diesen Werten.

## Ausschlüsse

Lieferanten schließen üblicherweise aus
([SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#ausschlüsse-die-sie-nennen-sollten)):

- Ihren eigenen Code, Ihre Templates und Erweiterungen sowie Konfiguration, die Sie geändert haben;
- Erweiterungen, die sie nicht geliefert haben;
- Ausfälle, die Ihre Inhalte verursachen (zum Beispiel ein Import, der die Festplatte füllt);
- geplante Wartung in angekündigten Fenstern.

Lesen Sie die Ausschlüsse so sorgfältig wie die Zusagen; sie entscheiden, was Sie wirklich bekommen.

## Open-Source-Punkte: GPL, Eigentum und Gewährleistung

Aus [Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation](../../geschaeft/lizenzen-fuer-kaeufer.md):

- **Keine Lizenzgebühr** für Exponential; der Vertrag sollte keine für die Software selbst verlangen.
- **Eine Website zu betreiben ist kein Verbreiten.** Wenn eine Agentur Ihre Website baut und Ihnen den Code übergibt,
  verbreitet sie ihn an Sie, und Sie erhalten die Rechte der GPL, einschließlich des Quellcodes.
- **Gewährleistung**: Die GPL kommt ohne Gewährleistung (`LICENSE.md`, Abschnitte 11 und 12). Eine Gewährleistung,
  garantierte Reaktionszeiten oder eine Haftungsdeckung kommen nur aus Ihrem Vertrag; die GPL erlaubt ausdrücklich,
  „warranty protection in exchange for a fee“ (Gewährleistungsschutz gegen eine Gebühr) anzubieten.
- **Ihre eigene Arbeit**: Vereinbaren Sie schriftlich, wem Designs, Templates, Erweiterungen und Inhalte gehören, die
  für Sie erstellt werden. Beachten Sie, dass auf Exponential aufgebauter und verbreiteter Code unter die Bedingungen
  der GPL fällt; fragen Sie Ihren Berater, was das für Ihre Pläne bedeutet.
- **Erweiterungen** tragen jeweils ihre eigene Lizenz, angezeigt unter **Info > Über**
  ([Die Seiten „About“ und „Copyright“](../../../../eng-us/features/6.0/about-and-package-pages.md)).

## Datenschutz

- Wenn der Lieferant personenbezogene Daten für Sie verarbeitet, ist meist eine schriftliche Vereinbarung über diese
  Verarbeitung nötig; Ihr Berater weiß, in welcher Form ([DSGVO-Grundlagen](../sicherheit/dsgvo-grundlagen.md)).
- Nennen Sie die Standorte von Servern und Backups.
- Vereinbaren Sie, wie der Lieferant bei Anfragen auf Einsicht, Export oder Löschung von Daten hilft
  ([Aufbewahrung und Löschung von Daten](../sicherheit/aufbewahrung-und-loeschung-von-daten.md)).
- Vereinbaren Sie, wer wen nach einem Vorfall wie schnell informiert.

## Ausstieg

- [ ] Administratorzugang und Hosting-Konten gehören Ihnen oder werden auf Anfrage übergeben.
- [ ] Der Code Ihrer Website, die Datenbank und die hochgeladenen Dateien werden in einem festgelegten Format und
      innerhalb einer festgelegten Frist geliefert.
- [ ] Der Lieferant arbeitet während einer Übergabe mit einem neuen Anbieter zusammen.
- [ ] Personenbezogene Daten werden nach der Übergabe vom alten Lieferanten gelöscht, mit Bestätigung.

Die [Checkliste für das Offboarding eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md) zeigt, was ein gut
geführter Hoster am Ende eines Vertrags tut.

## Checkliste vor der Unterschrift

- [ ] Umfang und Rollen als Tabelle festgehalten.
- [ ] Service-Levels passen zu Ihren Kosten eines Ausfalls.
- [ ] Jede Stufe hat eine Messung und einen Bericht.
- [ ] Ausschlüsse gelesen und akzeptiert.
- [ ] Frist für Sicherheitsupdates festgelegt.
- [ ] Bedingungen zum Datenschutz vereinbart.
- [ ] Eigentum an eigener Arbeit festgelegt.
- [ ] Bedingungen zu Gewährleistung und Haftung verstanden.
- [ ] Ausstieg und Übergabe beschrieben.
- [ ] Von Ihrem Rechtsberater geprüft.

Eine SLA-Vorlage mit Platzhaltern, geschrieben für Hoster, steht in
[SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#eine-sla-vorlage).

## Verwandte Seiten

- [Support-Stufen für Exponential-Hosting](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)
- [Fragen an einen Partner](fragen-an-einen-partner.md)
- [Die Partnerschaftsvereinbarung in einfachen Worten (für Partner)](../../partner/die-partnerschaftsvereinbarung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
