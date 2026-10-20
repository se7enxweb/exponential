---
title: Verträge und SLAs
audience: customer, businesses, partner
category: customer-success
order: 5
summary: Der Leitfaden des Betreibers zu Verträgen und Service Level Agreements mit einem Hoster oder Partner für eine Exponential-Website - was abzudecken ist, welche Zusagen von wem abhängen, wie jede gemessen wird, die Punkte zu Open Source und wie die Vorlagen in diesem Ordner dazu passen. Keine Rechtsberatung.
keywords: Vertrag, SLA, Service Level Agreement, Verfügbarkeit, Reaktionszeit, RPO, RTO, Sicherheitsupdates, Ausstieg, GPL, keine Rechtsberatung, contract, service level agreement, availability, response time, security updates, exit, not legal advice
type: help
---
# Verträge und SLAs

Diese Seite hilft einem Betreiber, einen Vertrag mit einem Hoster oder Partner vorzubereiten und zu lesen. Sie ersetzt
keine Rechtsberatung: Lassen Sie jeden Vertrag von Ihrer Rechtsberatung prüfen. Die ausführliche Referenz für
Unternehmen ist [Grundlagen zu Verträgen und SLAs](../../../../eng-us/help/businesses/choosing/contracts-and-slas-basics.md); diese Seite
ist die Arbeitszusammenfassung des Betreibers und verweist auf die Vorlagen in diesem Ordner.

> **Warnung**
> Keine Rechtsberatung. Das Exponential-Projekt veröffentlicht kein SLA. Die einzige Zusage zur Reaktionszeit in seinen
> eigenen Dateien ist eine erste Antwort auf eine vertrauliche Sicherheitsmeldung „innerhalb eines Werktags“
> (`SECURITY.md`). Jedes andere Versprechen kommt aus Ihrem Vertrag.

## Was abzudecken ist

Aus [Grundlagen zu Verträgen und SLAs](../../../../eng-us/help/businesses/choosing/contracts-and-slas-basics.md):

| Thema | Die Frage, die es beantwortet |
|---|---|
| Umfang | Welche Websites, Umgebungen und Leistungen? |
| Rollen | Wer macht was ([Wer was macht](wer-was-macht.md))? |
| Service-Level | Was wird versprochen, wie gemessen, was geschieht bei Verfehlung? |
| Änderungen | Wie werden Änderungen angefragt, bepreist und genehmigt ([Änderungsanfragen](aenderungsanfragen.md))? |
| Sicherheit | Wie schnell werden Sicherheits-Releases eingespielt; wer wird über Vorfälle informiert? |
| Datenschutz | Welche personenbezogenen Daten, wo, unter welchen Bedingungen? |
| Eigentum | Wem gehören individuelle Templates, Erweiterungen, Designs und Inhalte? |
| Gewährleistung und Haftung | Wofür steht der Lieferant ein? |
| Laufzeit und Ausstieg | Wie lange, wie man ihn beendet, wie die Übergabe abläuft |

## Zusagen und wovon sie abhängen

| Zusage | Hängt ab von | Gemessen durch |
|---|---|---|
| Verfügbarkeit | Rechner, Netzwerk, Engine, Datenbank | Eine externe Prüfung einer Seite, die die Datenbank anspricht |
| Support-Reaktion | Seiner Personalausstattung | Sein Ticketsystem; verlangen Sie Berichte |
| Wiederherstellungspunkt (RPO) | Backup-Häufigkeit | Zeitstempel der Backups |
| Wiederherstellungszeit (RTO) | Backup-Verfahren, Datenmenge, getestete Wiederherstellungen | Die Dauer des letzten Wiederherstellungstests |
| Sicherheitsupdates | Seinem Update-Verfahren | Die Version der Website und die Deployment-Einträge des Prüfprotokolls |

Quelle: [Grundlagen zu Verträgen und SLAs](../../../../eng-us/help/businesses/choosing/contracts-and-slas-basics.md), aus
[SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md).

> **Tipp**
> Verlangen Sie „Eine veröffentlichte Korrektur wird innerhalb einer bestimmten Zeit eingespielt“, nicht „Eine
> Sicherheitslücke wird innerhalb einer bestimmten Zeit behoben“: Das Zweite hängt vom Projekt ab, das Erste vom
> Lieferanten (dieselben Seiten).

## Die Level wählen

Strengere Level kosten mehr. Passen Sie sie an das an, was Ausfallzeit Ihre Organisation kostet; das
[Arbeitsblatt zu den Kosten von Ausfällen](../../../../eng-us/help/businesses/costs/cost-of-downtime-worksheet.md) hilft dabei. Ein Level,
das Sie nie brauchen, ist verschwendetes Geld, und ein Level, für das Ihr Anbieter kein Personal hat, ist nur ein
Versprechen auf Papier. Partnern wird geraten, einem Kunden nie „eine kürzere Reaktion zu versprechen, als Sie mit
Ihrem eigenen Personal leisten können“ ([Support-Stufen und Reaktionszeiten](../../partner/support-stufen-und-reaktionszeiten.md));
es ist fair zu fragen, wie sie das Versprochene personell abdecken.

## Punkte zu Open Source

- **Keine Lizenzgebühr** für Exponential selbst; ein Vertrag sollte dafür keine berechnen.
- **Die GPL enthält keine Gewährleistung**; eine Gewährleistung, eine garantierte Reaktion oder eine Haftungsabdeckung
  kommt nur aus Ihrem Vertrag (`LICENSE.md`; [Grundlagen zu Verträgen und SLAs](../../../../eng-us/help/businesses/choosing/contracts-and-slas-basics.md)).
- **Vereinbaren Sie das Eigentum an individueller Arbeit schriftlich**, und fragen Sie Ihre Beratung, was die GPL für
  Ihre Pläne bedeutet.
- **Erweiterungen** haben jeweils ihre eigene Lizenz, angezeigt unter **Info > Über**.

## Die Vorlagen

| Vorlage | Verwenden Sie sie für |
|---|---|
| [SLA-Vorlage](sla-vorlage.md) | Verfügbarkeit, Wartungsfenster, Backups, Wiederherstellung, Sicherheitsupdates, Berichte |
| [Vorlage für eine Support-Vereinbarung](vorlage-support-vereinbarung.md) | Kanäle, Zeiten, Schweregrade, erste Reaktionen, was enthalten ist |
| [Vorlage für eine Wartungsvereinbarung](vorlage-wartungsvereinbarung.md) | Updates, PHP-Upgrades, Prüfungen, Berichte, die jährliche Überprüfung |

Alle drei sind Vorlagen mit auszufüllenden Feldern, keine Rechtsberatung, und enthalten keine Zahlen: Die Werte
vereinbaren Sie.

## Vor der Unterschrift

- [ ] Umfang und Rollen als Tabelle festgehalten.
- [ ] Jedes Service-Level hat eine Messung und einen Bericht.
- [ ] Ausschlüsse gelesen und akzeptiert: meist Ihr eigener Code, Erweiterungen, die er nicht geliefert hat, Ausfälle
      durch Ihre Inhalte und angekündigte Wartung ([SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).
- [ ] Zeit für Sicherheitsupdates angegeben.
- [ ] Datenschutzbedingungen vereinbart ([DSGVO-Grundlagen](../../../../eng-us/help/businesses/security/gdpr-basics.md), keine Rechtsberatung).
- [ ] Ausstieg und Übergabe beschrieben ([Checkliste für die Übergabe](checkliste-uebergabe.md)).
- [ ] Von Ihrer Rechtsberatung geprüft.

## Verwandte Seiten

- [Ihren Anbieter jedes Jahr überprüfen](anbieter-jedes-jahr-ueberpruefen.md)
- [Support-Stufen (für Hoster)](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)
- [Die Partnerschaftsvereinbarung (für Partner)](../../partner/die-partnerschaftsvereinbarung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
