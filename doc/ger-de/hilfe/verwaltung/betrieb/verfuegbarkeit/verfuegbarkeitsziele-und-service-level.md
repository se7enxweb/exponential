---
title: Verfügbarkeitsziele und Service-Level
audience: government, project-manager, hosting, admin
category: public-sector
order: 130
summary: Für öffentliche Stellen, die Verfügbarkeitsziele für eine Website festlegen, intern oder in einem Vertrag mit einem Dienstleister. Welche Zusagen möglich sind, wovon jede abhängt, wie Sie sie messen und warum die Ziele eine Entscheidung Ihrer Organisation sind und keine Eigenschaft der Software.
keywords: Verfügbarkeitsziel, Service-Level, SLA, OLA, Verfügbarkeit, Wartungsfenster, Reaktionszeit des Supports, Wiederherstellungspunkt, Wiederherstellungszeit, Vertrag, Dienstleister, Messung, Pflicht zur Aufrechterhaltung des Betriebs, availability target, service level, uptime, maintenance windows, support response, recovery point, recovery time, contract, provider, measurement, continuity duty
type: help
---
# Verfügbarkeitsziele und Service-Level

Diese Seite richtet sich an Projektleitungen, Verantwortliche für Dienste und Vertragsverantwortliche in öffentlichen
Stellen, die festlegen, wie verfügbar eine Website sein muss, entweder als internes Ziel oder in einem Vertrag mit
einem Hoster. Sie beantwortet, welche Zusagen möglich sind, wovon jede abhängt, wie Sie sie messen und welche
Ausschlüsse Sie benennen sollten. Die Hosting-Seite [SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md) behandelt
dasselbe Thema aus der Sicht eines Dienstleisters.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Software veröffentlicht kein Service-Level](#die-software-veröffentlicht-kein-service-level)
2. [Zusagen, die Sie festlegen können](#zusagen-die-sie-festlegen-können)
3. [Wovon jede Zusage abhängt](#wovon-jede-zusage-abhängt)
4. [Messen](#messen)
5. [Zu benennende Ausschlüsse](#zu-benennende-ausschlüsse)
6. [Checkliste](#checkliste)

## Die Software veröffentlicht kein Service-Level

Das Exponential-Projekt veröffentlicht keine Service-Level-Vereinbarung. Die einzige Reaktionszusage in den eigenen
Dateien des Projekts steht in `SECURITY.md`: eine erste Antwort auf eine vertrauliche Sicherheitsmeldung „innerhalb
eines Werktags“ ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)). Jedes Ziel auf dieser Seite ist daher
eine Entscheidung Ihrer Organisation, die durch Ihre Architektur, Ihre Beschäftigten und Ihre Lieferanten erfüllt
wird.

## Zusagen, die Sie festlegen können

| Zusage | Bedeutet | Entschieden von |
|---|---|---|
| Verfügbarkeit | die Website antwortet, von außen gemessen, zu festgelegten Zeiten | Verantwortliche für den Dienst |
| Wartungsfenster | geplante Arbeiten nur in angekündigten Fenstern | Verantwortliche für den Dienst und Änderungsbeirat |
| Reaktionszeit des Supports | eine erste Antwort innerhalb einer Frist, je nach Schweregrad | IT-Servicedesk |
| Wiederherstellungspunkt (RPO) | höchstens so viel aktueller Inhalt darf verloren gehen | Verantwortliche für den Dienst, mit der Schriftgutverwaltung |
| Wiederherstellungszeit (RTO) | die Website ist innerhalb dieser Zeit nach einem Ausfall wieder da | Verantwortliche für den Dienst, mit der IT |
| Sicherheitsupdates | eine veröffentlichte Sicherheitskorrektur wird innerhalb einer Frist eingespielt | Sicherheitsbeauftragte und IT |

RPO und RTO werden unter [RPO und RTO als Entscheidungen](../../../../../eng-us/help/government/operations/backups-and-recovery/rpo-and-rto-as-decisions.md) behandelt.

## Wovon jede Zusage abhängt

| Zusage | Hängt ab von | Seiten |
|---|---|---|
| Verfügbarkeit | Maschinen, Netzwerk, Engine, Datenbank und wie viele einzelne Ausfallpunkte bleiben | [Überblick über Hochverfügbarkeit](hochverfuegbarkeit-im-ueberblick.md) |
| Wartungsfenster | dem Änderungskalender; dem Wartungsmodus | [Wartungsmodus und geplante Ausfallzeiten](wartungsmodus-und-geplante-ausfallzeiten.md) |
| Reaktionszeit des Supports | Personalausstattung und Bereitschaftsregelungen, nicht der Software | Ihr Servicedesk |
| Wiederherstellungspunkt | Häufigkeit der Datensicherung, Datenbankreplikation | [Sicherungspläne und Aufbewahrung](../../../../../eng-us/help/government/operations/backups-and-recovery/backup-schedules-and-retention.md) |
| Wiederherstellungszeit | einer getesteten Wiederherstellung und ihrer gemessenen Dauer | [Wiederherstellung testen](../../../../../eng-us/help/government/operations/backups-and-recovery/restore-testing.md) |
| Sicherheitsupdates | dem Verfolgen der Releases; einem getesteten Weg zur Aktualisierung | [Sicherheitsupdates und Offenlegung](../../../../../eng-us/help/government/operations/upgrades-and-change/security-updates-and-disclosure.md) |

Versprechen Sie nie eine Wiederherstellungszeit, die Sie nicht in einem Test gemessen haben: „Nennen Sie die
Wiederherstellungszeit, die Sie in einem Test gemessen haben, nicht eine, die Sie erhoffen“
([Sicherungspläne und Aufbewahrung](../../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)).

## Messen

- **Von außen**: Ein Verfügbarkeitsmonitor ruft die Startseite und die Anmeldeseite der Administration ab; die
  Startseite allein kann aus einem Cache ausgeliefert werden, während die Datenbank ausgefallen ist
  ([Zustandsprüfungen und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
- **Geplante Wartung**: ein 503 mit `Retry-After` während eines angekündigten Fensters; konfigurieren Sie den Monitor
  so, dass dies nicht als Ausfall zählt.
- **Von innen**: `exp:velocity status` endet mit einem Wert ungleich null, wenn der Server nicht antwortet;
  **Setup > Systeminformationen** führt Zustandsprüfungen aus ([Systeminformationen](../../../../anleitungen/systeminformationen.md)).
- **Wiederherstellungen**: Halten Sie die Dauer jedes Wiederherstellungstests fest.

## Zu benennende Ausschlüsse

Typische Ausschlüsse in einem internen Ziel oder einem Vertrag, zur Anpassung:

- angekündigte Wartungsfenster;
- Ausfälle von Diensten außerhalb des Geltungsbereichs (das Netzwerk der Organisation, DNS, ein zentraler Proxy,
  auf der Website aktivierte Dienste Dritter);
- inhaltliche Fehler von Redakteurinnen und Redakteuren (behandelt durch Wiederherstellung oder Korrektur, nicht über
  die Verfügbarkeit);
- höhere Gewalt, wie Ihre Vertragsvorlagen sie definieren.

## Checkliste

- [ ] Jede Zusage hat eine verantwortliche Person und einen von der Organisation festgelegten Wert.
- [ ] Die Architektur und die getestete Wiederherstellungszeit tragen die Werte.
- [ ] Eine externe Überwachung misst die Verfügbarkeit und kennt die Wartungsfenster.
- [ ] Die Ausschlüsse sind schriftlich festgehalten.
- [ ] Der Vertrag eines Dienstleisters entspricht den internen Zielen und wurde von der Rechtsberatung geprüft.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Support-Stufen](../../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)
- [Notfallwiederherstellungsplan](../../../../../eng-us/help/government/operations/backups-and-recovery/disaster-recovery-plan.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
