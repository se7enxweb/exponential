---
title: Wiederherstellung testen
audience: government, hosting, admin, project-manager
category: public-sector
order: 80
summary: Für öffentliche Stellen, die nachweisen müssen, dass ihre Datensicherungen funktionieren. Warum eine nie wiederhergestellte Datensicherung eine Hoffnung ist, wie oft Sie testen, was ein Wiederherstellungstest prüft, wie Sie das Ergebnis als Nachweis für Prüfende und Kontinuitätspläne festhalten und die gemessene Wiederherstellungsdauer als Grundlage Ihrer Wiederherstellungszeit.
keywords: Wiederherstellungstest, Test der Datensicherung, Wiederanlauftest, Nachweis, Audit, Kontinuität, Wiederherstellungszeit, gemessene Wiederherstellung, Testbericht, Prüfung, Integritätsprüfung, restore test, backup test, recovery test, evidence, continuity, recovery time, measured restore, test report, verification, integrity check
type: help
---
# Wiederherstellung testen

Diese Seite richtet sich an Betriebsteams, Kontinuitätsplanende und Prüfende in öffentlichen Stellen, die zeigen
müssen, dass sich Datensicherungen wiederherstellen lassen. Sie beantwortet, wie oft Sie testen, was ein
Wiederherstellungstest prüft und wie Sie das Ergebnis so festhalten, dass es als Nachweis dient. Das Verfahren selbst
steht unter [Wiederherstellung auf einem Testserver](wiederherstellung-auf-einem-testserver.md); die Hosting-Seite ist
[Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md).

## Inhalt

1. [Warum testen](#warum-testen)
2. [Wie oft](#wie-oft)
3. [Was ein Test prüft](#was-ein-test-prüft)
4. [Das Ergebnis festhalten](#das-ergebnis-festhalten)
5. [Was üblicherweise schiefgeht](#was-üblicherweise-schiefgeht)
6. [Checkliste](#checkliste)

## Warum testen

„Eine Datensicherung, die nie wiederhergestellt wurde, ist eine Hoffnung, keine Datensicherung“
([10.9 Datensicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)). Ein
Job-Log, das „erfolgreich“ meldet, beweist nur, dass etwas geschrieben wurde. Ein Wiederherstellungstest beweist die
Datensicherung, das Verfahren und die Zeit, die es braucht, und auf dieser Zeit muss Ihr Wiederherstellungsziel
beruhen ([RPO und RTO als Entscheidungen](rpo-und-rto-als-entscheidungen.md)).

## Wie oft

Die Quellen verlangen eine Wiederherstellung „mindestens einmal auf einer anderen Maschine und nach jeder größeren
Aktualisierung“, und die wöchentliche Checkliste verlangt, dass eine aktuelle Datensicherung „mindestens einmal auf
einer anderen Maschine wiederhergestellt wurde“
([Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste)). Darüber hinaus wählen Sie
einen Rhythmus, der zu Ihren Pflichten zur Aufrechterhaltung des Betriebs passt, zum Beispiel:

| Anlass | Test |
|---|---|
| Nach dem Einrichten oder Ändern der Datensicherung | vollständige Wiederherstellung |
| Nach jeder größeren Aktualisierung | vollständige Wiederherstellung |
| Nach einem festen Plan, den Sie festlegen | vollständige Wiederherstellung, abwechselnd über Engines und Websites |
| Nach einem Wechsel der Datenbank-Engine oder des Speichers | vollständige Wiederherstellung |

## Was ein Test prüft

| Prüfung | Wie | Gutes Ergebnis |
|---|---|---|
| Die Website antwortet | `curl -s -o /dev/null -w '%{http_code}\n' https://<test host>/` | `200` |
| Die Administration | `/user/login` der Administration öffnen, anmelden | das Dashboard |
| Version | **Setup > Systeminformationen** oder `./console exp:system:info` | das erwartete Release, keine fehlgeschlagene Zustandsprüfung |
| Caches | `php bin/php/cache.php status --allow-root-user` | endet mit `PASS` |
| Prüfprotokoll | `./console exp:audit verify --allow-root-user` | Exit-Status 0 |
| Inhalte | aktuelle Seiten und Bilder öffnen; ein Testelement veröffentlichen | Bilder laden, Veröffentlichen funktioniert |
| SQLite | `sqlite3 <db file> "PRAGMA integrity_check"` | `ok` |
| Fehler | `tail -n 50 var/log/error.log` | nichts Unerklärtes |

Aus [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md).

## Das Ergebnis festhalten

Führen Sie je Test einen kurzen Bericht; er ist der Nachweis, nach dem Prüfende oder Kontinuitätsprüfende fragen
werden:

| Feld | Beispielinhalt |
|---|---|
| Datum und testende Person | Datum; Teamname |
| Verwendete Datensicherung | Datum und Uhrzeit der Datensicherung, Speicherort |
| Ziel | Name der Testmaschine |
| Schritte | das befolgte Verfahren, mit Abweichungen |
| Dauer | vom Start bis „Website antwortet und Prüfungen bestanden“ |
| Ergebnis | jede Prüfung oben, bestanden oder nicht bestanden |
| Nacharbeit | Korrekturen an der Datensicherung oder am Verfahren, mit Verantwortlichen |

Bewahren Sie die Berichte zusammen mit dem Kontinuitätsplan auf. Eine im Lauf der Zeit wachsende Dauer ist ein
Signal, vor einem Vorfall zu handeln.

## Was üblicherweise schiefgeht

- Als root wiederhergestellte Dateien, die der Webserver nicht beschreiben kann.
- Eine fehlende Schlüsseldatei, sodass sich das Prüfprotokoll nicht prüfen lässt.
- Eine SQLite-Datei, die im laufenden Betrieb kopiert statt mit `.backup` erstellt wurde.
- Eine Testkopie, die echte E-Mails versendet oder echte Cronjobs ausführt: siehe
  [Wiederherstellung auf einem Testserver](wiederherstellung-auf-einem-testserver.md).
- Eine Datensicherung aus einem anderen Release als der Code auf der Testmaschine; stellen Sie auf demselben Release
  und derselben `composer.lock` wieder her.

## Checkliste

- [ ] Ein Wiederherstellungstest ist eingeplant, und jemand ist dafür verantwortlich.
- [ ] Jeder Test erzeugt einen Bericht mit seiner Dauer und seinen Ergebnissen.
- [ ] Fehlschläge führen zu Korrekturen mit Verantwortlichen und zu einem erneuten Test.
- [ ] Die Wiederherstellungszeit in Ihren Plänen ist die gemessene.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- Runbook [Aus einer Datensicherung wiederherstellen](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Notfallwiederherstellungsplan](notfallwiederherstellungsplan.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
