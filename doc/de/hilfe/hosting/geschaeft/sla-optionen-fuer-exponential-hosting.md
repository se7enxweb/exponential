---
title: SLA-Optionen für Exponential-Hosting
audience: hosting
category: hosting
order: 30
summary: Die Zusagen zum Service-Level, die ein Hoster für Exponential-Websites anbieten kann (Verfügbarkeit, Reaktion, Wiederherstellung, Updates, Sicherheitskorrekturen), wovon jede technisch abhängt, wie Sie sie messen, und eine SLA-Vorlage mit Platzhaltern. Keine Rechtsberatung; es werden keine Zahlen genannt.
keywords: SLA, Service-Level-Vereinbarung, Verfügbarkeit, Uptime, Reaktionszeit, Wiederherstellungszeit, Wiederherstellungspunkt, RTO, RPO, Wiederherstellung, Wartungsfenster, Sicherheitskorrekturen, Updates, Gutschriften, Ausschlüsse, Vorlage, service level agreement, availability, uptime, response time, recovery time, recovery point, restore, maintenance window, security fixes, credits, exclusions, template
type: help
---
# SLA-Optionen für Exponential-Hosting

Diese Seite ist für Produktmanager und Führungskräfte bei einem Hosting-Anbieter, die die Service-Level-Vereinbarung (SLA)
eines Angebots für Exponential-Hosting schreiben. Sie beantwortet: welche Zusagen möglich sind, wovon jede abhängt, wie
Sie sie messen und was Sie ausschließen.

> **Warnung**
> Das Exponential-Projekt veröffentlicht kein SLA. Die kaufmännischen Bedingungen von 7x stehen auf der Website von 7x,
> nicht hier ([Wer ist 7x?](../../wer-ist-7x.md#dienstleistungen-und-hosting)). Die einzige Zusage zur Reaktion in den eigenen
> Dateien des Projekts steht in `SECURITY.md`: eine erste Antwort auf eine vertrauliche Sicherheitsmeldung innerhalb eines
> Werktags. Alles auf dieser Seite ist eine Option für **Ihren** Vertrag. Lassen Sie den endgültigen Text von Ihrem
> rechtlichen Berater prüfen; diese Seite ist keine Rechtsberatung.

## Inhalt

1. [Was ein SLA abdecken kann](#was-ein-sla-abdecken-kann)
2. [Jede Zusage und wovon sie abhängt](#jede-zusage-und-wovon-sie-abhängt)
3. [Messen, was Sie versprechen](#messen-was-sie-versprechen)
4. [Ausschlüsse, die Sie nennen sollten](#ausschlüsse-die-sie-nennen-sollten)
5. [Eine SLA-Vorlage](#eine-sla-vorlage)

## Was ein SLA abdecken kann

| Zusage | Sie versprechen | Hoster bieten sie meist an in |
|---|---|---|
| Verfügbarkeit | die Website antwortet, von außen gemessen | jedem Tarif oberhalb des günstigsten |
| Reaktion des Supports | eine erste Antwort innerhalb einer Zeit, je Schweregrad | jedem Tarif, mit unterschiedlichen Zeiten |
| Wiederherstellungspunkt (RPO) | höchstens so viele Daten können verloren gehen | Tarifen mit Backups |
| Wiederherstellungszeit (RTO) | eine Wiederherstellung ist innerhalb dieser Zeit abgeschlossen | verwalteten Tarifen |
| Wartungsfenster | geplante Arbeiten finden nur in angekündigten Fenstern statt | jedem Tarif |
| Sicherheitsupdates | eine veröffentlichte Sicherheitskorrektur von Exponential wird innerhalb einer Zeit eingespielt | verwalteten Tarifen |
| Plattform-Updates | Updates von PHP und Betriebssystem werden innerhalb einer Zeit eingespielt | jedem Tarif, den der Hoster betreibt |

## Jede Zusage und wovon sie abhängt

### Verfügbarkeit

Hängt von der Maschine, dem Netzwerk, der Engine und der Datenbank ab. Was in Exponential Ihnen hilft, sie zu halten:

- Velocity kann von einem optionalen Watchdog überwacht werden, der die Engine nach einem Absturz wieder startet
  (`exp:velocity watchdog install`, `[WatchdogSettings]` in `settings/velocity.ini`); siehe
  [Velocity für Hoster](../server/velocity-fuer-hoster.md).
- Mehrere Web-Knoten hinter einem Load Balancer brauchen einen Datenbankserver und gemeinsamen Speicher
  ([Mehrere Web-Knoten](../betrieb/mehrere-web-knoten.md)).
- Caches verringern die Last, die eine Verkehrsspitze verursacht ([Cache-Schichten](../leistung/cache-schichten.md)).

### Reaktion des Supports

Hängt von Ihrer Personalausstattung ab, nicht von der Software. Legen Sie zuerst Schweregrade fest
([Support-Stufen](support-stufen-fuer-exponential-hosting.md)), dann Zeiten je Schweregrad.

### Wiederherstellungspunkt und Wiederherstellungszeit

Hängen von der Häufigkeit der Backups, der Backup-Methode je Datenbank und der Größe von `var/storage` ab. Die Dauer einer
Wiederherstellung kennen Sie erst, wenn Sie eine durchgeführt haben: Testen Sie sie
([Wiederherstellung und Wiederherstellungstests](../betrieb/wiederherstellung-und-wiederherstellungstests.md)).

### Sicherheitsupdates

Hängen davon ab, wie schnell das Projekt veröffentlicht und wie schnell Sie einspielen. Die Seite des Projekts, aus
`SECURITY.md`: Sicherheitskorrekturen werden auf dem Branch `main` gemacht und mit dem nächsten Release 6.0.x
veröffentlicht, und ein veröffentlichter Tag wird nie geändert. Ihre Seite: Releases verfolgen
([Mit dem Projekt und 7x zusammenarbeiten](mit-dem-projekt-und-7x-zusammenarbeiten.md)) und ein Update-Verfahren
([Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md)).

> **Hinweis**
> Versprechen Sie, eine veröffentlichte Korrektur innerhalb einer Zeit einzuspielen, nicht, dass eine Schwachstelle
> innerhalb einer Zeit behoben wird: Das Zweite hängt vom Projekt ab, das Erste von Ihnen.

## Messen, was Sie versprechen

| Zusage | Messen mit |
|---|---|
| Verfügbarkeit | einer externen Prüfung einer Seite, die die Datenbank berührt, siehe [Zustandsprüfungen und Verfügbarkeit](../betrieb/zustandspruefungen-und-verfuegbarkeit.md) |
| Reaktionszeiten | Ihrem Ticketsystem |
| Fehler, die Besucher sehen | den Anfrage-Logs und dem Log-Viewer ([Anfrage-Logs](../betrieb/anfrage-logs.md), [Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md)) |
| RPO | den Zeitstempeln Ihrer Backups |
| RTO | der Dauer Ihres letzten Wiederherstellungstests |

## Ausschlüsse, die Sie nennen sollten

- der eigene Code, die Templates und Erweiterungen des Kunden und Änderungen, die der Kunde an der Konfiguration
  vorgenommen hat;
- Erweiterungen, die nicht vom Hoster geliefert wurden;
- Ausfälle, die durch Inhalte eines Kunden verursacht werden (zum Beispiel ein Import, der die Festplatte füllt);
- geplante Wartung in angekündigten Fenstern;
- Dienste Dritter, die die Website aufruft (Zahlungsanbieter, Mail, externe APIs);
- höhere Gewalt, so wie Ihr rechtlicher Berater es formuliert.

## Eine SLA-Vorlage

```text
Service level agreement — <plan name>                 version <x>, valid from <date>

1. Availability        <percentage> per <month>, measured by <external check> on <URL>,
                       excluding announced maintenance windows.
2. Maintenance         windows <day/time, time zone>, announced <n> days ahead by <channel>.
3. Support             severities and first-response times as in the support tier sheet <tier name>.
4. Backups             <what> every <interval>; kept <n> days; stored <where>.
   RPO                 <time>.   RTO <time>, restore last tested on <date>.
5. Security updates    released Exponential security fixes applied within <time> of release;
                       PHP and OS security updates within <time>.
6. Exclusions          <list from this page>.
7. Credits             <your credit model, if any>.
8. Reporting           <monthly report: availability, incidents, restore tests>.
```

## Verwandte Seiten

- [Support-Stufen](support-stufen-fuer-exponential-hosting.md)
- [Hosting-Tarife gestalten](hosting-tarife-gestalten.md)
- [Zustandsprüfungen und Verfügbarkeit](../betrieb/zustandspruefungen-und-verfuegbarkeit.md)
- [Backup-Zeitpläne und Aufbewahrung](../betrieb/backup-zeitplaene-und-aufbewahrung.md)
- [Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
