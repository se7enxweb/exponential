---
title: Bewertungsbogen Zuverlässigkeit
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 220
summary: Eine Checkliste, mit der Sie bewerten, wie zuverlässig Ihre Exponential-Website betrieben wird, in zehn Bereichen mit je drei Stufen, wie Sie die Bewertung jedes Quartal nutzen, und Links zu der Seite, die jeden Punkt erklärt.
keywords: Bewertungsbogen, Zuverlässigkeitsbewertung, Checkliste, Einschätzung, Reifegrad, Quartalsrückblick, Betriebsprüfung, Selbsteinschätzung, Audit, scorecard, reliability score, checklist, assessment, maturity, quarterly review, operations review, self-assessment, audit
type: help
---
# Bewertungsbogen Zuverlässigkeit

Diese Seite richtet sich an Inhaber und Führungskräfte, die in einer Sitzung wissen wollen, wie gut ihre Website
geschützt ist, und an die Person im Betrieb oder den Partner, der den Bogen mit ihnen ausfüllt. Er hat zehn Bereiche,
die jeweils mit 0, 1 oder 2 bewertet werden. Er ist keine Zertifizierung und gibt keinen Zielwert vor: Er zeigt, wo Sie
stehen und was als Nächstes zu tun ist. Jeder Punkt verweist auf die Seite, die ihn erklärt.

## Inhalt

1. [So verwenden Sie ihn](#so-verwenden-sie-ihn)
2. [Der Bewertungsbogen](#der-bewertungsbogen)
3. [Ihre Bewertung lesen](#ihre-bewertung-lesen)
4. [Nachweise, die Sie aufbewahren](#nachweise-die-sie-aufbewahren)
5. [Ein Protokoll zum Kopieren](#ein-protokoll-zum-kopieren)

## So verwenden Sie ihn

1. Setzen Sie sich einmal im Quartal mit der Person zusammen, die die Website betreibt, oder mit Ihrem Hosting-Partner.
2. Bewerten Sie jeden Bereich ehrlich: **0** = nicht vorhanden, **1** = vorhanden, aber nicht getestet oder nicht
   regelmäßig, **2** = vorhanden, regelmäßig und bewährt.
3. Bewerten Sie nur, was Sie zeigen können: einen Log-Eintrag, einen Bericht, einen Kalendereintrag. „Wir würden es
   tun“ ergibt 0.
4. Wählen Sie höchstens drei Bereiche, die Sie bis zur nächsten Prüfung verbessern, jeweils mit einer verantwortlichen
   Person.
5. Bewahren Sie die Bewertungen auf; die Entwicklung zählt mehr als die Zahl.

## Der Bewertungsbogen

| # | Bereich | 0 | 1 | 2 | Lesen |
|---|---|---|---|---|---|
| 1 | Release-Linie | Version unbekannt oder älter als 6.0.x | Auf 6.0.x, Aktualisierungen unregelmäßig | Auf der neuesten 6.0.x oder höchstens ein bis zwei Releases zurück, Aktualisierungen im Kalender | [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md) |
| 2 | PHP | Nicht unterstütztes PHP | Unterstütztes PHP, Abkündigungsdatum unbekannt | Unterstütztes PHP, Abkündigungsdatum bekannt und eingeplant | [Release-Linie und Support-Richtlinie](release-linie-und-support-richtlinie.md#php-versionen) |
| 3 | Monitoring von außen | Keines | Startseite wird geprüft | Startseite und Administration werden über HTTPS geprüft, Alarme erreichen eine benannte Person und eine Vertretung | [Health-Checks und Monitoring](health-checks-und-monitoring.md) |
| 4 | Prüfungen im Inneren | Keine | Ab und zu angesehen | Systeminformationen, Cache-Status und Audit-Prüfung laufen nach Zeitplan und werden per Exit-Code ausgewertet | [Health-Checks und Monitoring](health-checks-und-monitoring.md#prüfungen-auf-dem-server) |
| 5 | Backups | Keine oder nur die Datenbank | Datenbank und Dateien, nicht wiederhergestellt | Datenbank und Dateien, abseits des Servers, mehrere Generationen, Fehlschläge alarmieren jemanden | [Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md) |
| 6 | Wiederherstellungsübungen | Nie | Einmal, vor langer Zeit | Nach Zeitplan, mit Zeitmessung, protokolliert, Mail auf der Kopie abgeschaltet | [Wiederherstellungsübungen](wiederherstellungsuebungen.md) |
| 7 | Änderungen und Aktualisierungen | Direkt auf der Live-Website | Zuerst ein Backup, keine Probe | An einer Kopie geprobt, Upgrade-Prüfung vorher und nachher, Wartungsfenster, Verantwortlicher für das Zurückrollen | [Aktualisierungen ohne Überraschungen](aktualisierungen-ohne-ueberraschungen.md) |
| 8 | Deployments | Caches von Hand geleert, Server hart neu gestartet | Dokumentierte Schritte | `exp:velocity deploy` oder ein gleichwertiges geordnetes Vorgehen, zuerst ein Probelauf, danach geprüft | [Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md) |
| 9 | Hintergrundarbeit | Cron nicht eingerichtet oder unbekannt | Cron eingerichtet, nicht beobachtet | Cron auf einem Server, wöchentlich beobachtet, während geplanter Arbeiten angehalten | [Cronjobs und Hintergrundjobs](cronjobs-und-hintergrundjobs.md) |
| 10 | Bereitschaft für Störungen | Kein Plan | Ein Plan, den niemand geprobt hat | Ausgedruckter Plan, benannte Rollen, bekannte Runbooks, in diesem Jahr geprobt, Nachbesprechungen geschrieben | [Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md) |

Optionale Bereiche, für Websites, auf die sie zutreffen:

| # | Bereich | 2 bedeutet | Lesen |
|---|---|---|---|
| 11 | Caching | View-Caching und Template-Caches eingeschaltet, Seiten-Caches in der richtigen Reihenfolge geleert, Vorwärmen nach dem Leeren | [Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md) |
| 12 | Spitzen | Termine der Spitzen Wochen vorher bekannt, gemessene Ausgangsbasis, Änderungsstopp | [Lastspitzen planen](lastspitzen-planen.md) |
| 13 | Audit-Trail | Durch Cron indiziert, nach Zeitplan geprüft, Alarme erreichen ein gemeinsames Postfach, Schlüssel gesichert | [Audit-Trail für den Betrieb](audit-trail-fuer-den-betrieb.md) |
| 14 | Redaktionelle Sicherheit | Vorschau, Freigabe dort, wo Fehler teuer sind, Redakteure kennen Versionen und den Papierkorb | [Vorschau vor dem Veröffentlichen](vorschau-vor-dem-veroeffentlichen.md) |
| 15 | Logs | Fehlerübersicht wöchentlich gelesen, der Support fragt nach Referenzen `ERR-`, Aufbewahrung entschieden | [Logs und Fehlerreferenzen](logs-und-fehlerreferenzen.md) |

## Ihre Bewertung lesen

Die zehn Kernbereiche ergeben höchstens 20 Punkte. Die Bewertung dient Ihrem eigenen Vergleich über die Zeit, sie ist
keine Bestehensgrenze:

- **Jeder Bereich mit 0** ist das Erste, was Sie beheben, gleich wie hoch die Summe ist. Eine Website mit perfektem
  Monitoring und ohne Backup ist einen Festplattenausfall davon entfernt, alles zu verlieren.
- **Bereiche mit 1** brauchen meist eine Gewohnheit, keine Anschaffung: einen Kalendereintrag, eine benannte
  verantwortliche Person, eine erste Probe.
- **Bereiche mit 2** müssen dort bleiben: Prüfen Sie sie jedes Quartal erneut, denn Menschen, Server und Websites
  ändern sich.

Die Bereiche 5, 6 und 10 (Backups, Wiederherstellungsübungen, Bereitschaft für Störungen) entscheiden, wie schlimm ein
schlechter Tag wird. Wenn Sie in diesem Quartal nur an einer Sache arbeiten können, dann dort.

## Nachweise, die Sie aufbewahren

Eine 2 sollte auf etwas verweisen, das Sie einer Führungskraft, einem Prüfer oder einem Kunden zeigen können:

| Bereich | Nachweis |
|---|---|
| Release-Linie, PHP | Die unter **Setup > Systeminformationen** angezeigte Version und der Aktualisierungsplan |
| Monitoring | Die Alarmeinstellungen des Monitors und der Bericht des letzten Monats |
| Prüfungen im Inneren | Der geplante Job und seine letzten Ergebnisse |
| Backups | Das Log des Backup-Jobs und der Speicherort |
| Wiederherstellungsübungen | Das Übungsprotokoll: Datum, Backup, Minuten, Ergebnis ([Wiederherstellungsübungen](wiederherstellungsuebungen.md#das-ergebnis-festhalten)) |
| Aktualisierungen | Die Berichte der Upgrade-Prüfung vorher und nachher, die abgenommene Probe |
| Deployments | Der Kanal `system` des Audit-Trails und Ihr Änderungsprotokoll |
| Hintergrundarbeit | **Letzte Ausführungen** unter **Setup > Cronjobs** |
| Störungen | Der ausgedruckte Plan, das Datum der Probe, die Nachbesprechungen |

Zur Nutzung des Audit-Trails für Compliance siehe
[Compliance-Nachweise aus dem Audit-Trail](../sicherheit/compliance-nachweise-aus-dem-audit-trail.md).

## Ein Protokoll zum Kopieren

```text
Reliability review of <site>          Date: <yyyy-mm-dd>        Present: <roles>

 1 Release line          0 1 2     6 Restore drills       0 1 2
 2 PHP                   0 1 2     7 Changes, upgrades    0 1 2
 3 Outside monitoring    0 1 2     8 Deploys              0 1 2
 4 Inside checks         0 1 2     9 Background work      0 1 2
 5 Backups               0 1 2    10 Incident readiness   0 1 2

Total (of 20): ____      Last quarter: ____

Actions before the next review (at most three):
1. <area> - <what> - <owner> - <by when>
2.
3.
```

Der Bereich [Vorlagen für Unternehmen](../vorlagen/README.md) enthält weitere Checklisten zum Kopieren, darunter die
[Checkliste: Jahresrückblick](../vorlagen/checkliste-jahresrueckblick.md) und die
[Monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md).

## Verwandte Seiten

- [Zuverlässigkeit für Unternehmen](README.md)
- [Verfügbarkeit in der Praxis](verfuegbarkeit-in-der-praxis.md)
- [Risikomanagement für Exponential-Projekte](../../fuehrung/risikomanagement.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
