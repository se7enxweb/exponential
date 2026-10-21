---
title: Sicherheitslog
audience: hosting, admin
category: hosting
order: 110
summary: Die Sicherheitssignale, die eine Exponential-Website schreibt (security.log aus dem Request-Klassifizierer, auth.log, das Log der CSP-Meldungen und das Prüfprotokoll), wie Sie sie lesen, welche Einstellungen sie abstimmen und wie ein Hoster sie an ein zentrales System weiterleitet.
keywords: security.log, Request-Klassifizierer, Abtastungen, Injection, Traversal, Scanner, Schwelle, auth.log, csp.log, Prüfprotokoll, syslog, journald, Webhook, SIEM, request-shield, Listener, request classifier, probes, threshold, audit trail, listeners
type: help
---
# Sicherheitslog

Diese Seite ist für Systemadministratoren und Sicherheitsverantwortliche bei einem Hoster, die viele Exponential-Websites auf
Angriffe und Missbrauch beobachten. Sie beantwortet: welche Logs Sicherheitssignale enthalten, was eine Zeile in `security.log`
bedeutet, wie der Klassifizierer abgestimmt wird, was er nicht tut (er blockiert nie) und wie Sie die Signale von der Maschine
in Ihre eigene Überwachung bekommen.

Die Details stehen in [Anfrage-Logs](../../../../eng-us/features/6.0/request-logs.md), [Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)
und [Sicherheitshärtung: beobachten](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll).

## Inhalt

1. [Die Sicherheitsquellen auf einen Blick](#die-sicherheitsquellen-auf-einen-blick)
2. [security.log: der Request-Klassifizierer](#securitylog-der-request-klassifizierer)
3. [Den Klassifizierer abstimmen](#den-klassifizierer-abstimmen)
4. [Blockieren ist nicht die Aufgabe des Klassifizierers](#blockieren-ist-nicht-die-aufgabe-des-klassifizierers)
5. [auth.log und csp.log](#authlog-und-csplog)
6. [Das Prüfprotokoll und seine Senken](#das-prüfprotokoll-und-seine-senken)
7. [Eine wöchentliche Routine](#eine-wöchentliche-routine)
8. [Checkliste](#checkliste)

## Die Sicherheitsquellen auf einen Blick

| Quelle | Was sie festhält | Wo Sie sie lesen |
|---|---|---|
| `var/log/security.log` | die Urteile des Request-Klassifizierers: Abtastungen, Traversal, Injection, Angriffswerkzeuge, Häufungen von 404 | **Setup > Logdateien**, `./console exp:logs` |
| das Prüfprotokoll | Anmeldungen und Fehlschläge, Passwortänderungen, Änderungen an Rollen und Einstellungen, Entfernungen, ausgeführte Befehle; als Hash-Kette | Reiter **Audit**, `./console exp:audit tail` |
| `var/log/auth.log` | jeden Schritt der zweistufigen Anmeldung (nie einen Code oder ein Geheimnis) | **Setup > Logdateien** |
| `var/log/csp.log` | Meldungen über Verstöße gegen die Content Security Policy | **Setup > Logdateien** |
| `error.log`, die Logs des Webservers | Fehler mit einer Referenz-ID | **Setup > Logdateien** |
| das Log von request-shield | seine Entscheidungen, wenn der Schutz installiert ist | seine eigene Live-Ansicht |

Quelle: [Sicherheitshärtung](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll).

## security.log: der Request-Klassifizierer

Jede Anfrage wird einmal eingestuft, an ihrem Ende, was auch immer ihr Status ist. Eine Anfrage, die eine Regel erkennt, wird statt
in `404.log` oder `notice.log` in `security.log` geschrieben:

```text
[ Oct 17 2026 09:00:00 ] [203.0.x.x] probe/wp-login confidence=high 404 GET /wp-login.php siteaccess=site namespace=/wp-login.php ua=3f2a9c1b "curl/8.5.0"
```

| Feld | Bedeutung |
|---|---|
| `probe/wp-login` | `<category>/<rule>`: eingebaute Kategorien sind `probe`, `traversal`, `injection`, `scanner` und `threshold`; Erweiterungen können weitere hinzufügen |
| `confidence=` | `low`, `medium` oder `high` |
| `404` | der endgültige HTTP-Status (ein 200 ist möglich: ein Injection-Versuch gegen eine Suchseite wird normal beantwortet) |
| `GET /wp-login.php` | Methode und URI; anders als in `404.log` bleiben die Werte der Query als Beleg erhalten, mit maskierten Geheimnissen, E-Mail- und IP-Adressen |
| `namespace=` | der Pfad unterhalb des Siteaccess, sodass eine Regel denselben Pfad unter jedem Siteaccess sieht |

Die Regel **threshold** kennzeichnet einen Client, der innerhalb von `WindowSeconds=60` mehr als `Max404PerMinute=30` Antworten
„nicht gefunden“ erhält (`requestclassifier.ini [ThresholdSettings]`); ihr Zähler wird über einen gesalzenen Hash der Adresse
geführt, nie über die Adresse selbst.

Die **Fehlerübersicht** (**Setup > Logdateien > Fehlerübersicht**) gruppiert `security.log` nach `<category>/<rule>`, sodass eine
Woche voller Abtastungen sich als wenige Zeilen wie `probe/wp-login 312` liest.

## Den Klassifizierer abstimmen

`settings/requestclassifier.ini`, überschrieben in `settings/override/requestclassifier.ini.append.php`, einem Siteaccess oder einer
Erweiterung:

| Block und Schlüssel | Mitgeliefert | Bedeutung |
|---|---|---|
| `[ClassifierSettings] Classification` | `enabled` | `disabled` stuft nichts ein |
| `[ClassifierSettings] LogFile` | `security.log` | die Datei im Log-Verzeichnis |
| `[ClassifierSettings] RuleSets[]` | `probes`, `traversal`, `injection`, `scanner`, `threshold` | einen weglassen, um ihn auszuschalten |
| `[ClassifierSettings] Listeners[]` | keine | Klassen, die über jedes Urteil informiert werden |
| `[ThresholdSettings] Max404PerMinute` | `30` | `0` schaltet die Regel threshold aus |
| `[RuleSet-<id>] Disabled[]`, `Excludes[]`, `Patterns[]`, `Confidence[]` | pro Satz | eine Regel ausschalten, einen Pfad ausnehmen, eine Regel hinzufügen, eine Konfidenz ändern |

Ein Kunde, dessen eigene Anwendung einen Pfad nutzt, den eine Regel kennzeichnet (zum Beispiel einen legitimen Pfad
`/administrator/`), erhält einen Eintrag in `Excludes[]` statt eines ausgeschalteten Regelsatzes. Die Regelnamen jedes Satzes stehen
in der mitgelieferten Datei.

## Blockieren ist nicht die Aufgabe des Klassifizierers

Nichts wird blockiert. Der Klassifizierer stuft nur ein: Die Anfrage bekommt die Antwort, die sie ohnehin bekäme. Das Blockieren
gehört zu:

- **einem Schutz vor dem Kernel**, request-shield, der läuft, bevor Exponential startet, und eine Anfrage in seinem eigenen Log
  ablehnen oder festhalten kann ([Ein Schutz davor](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield),
  [request-shield und Request-Regeln](../sicherheit/request-shield-und-request-regeln.md));
- **dem Webserver oder einer Firewall** des Hosters;
- **einem Listener**, der in `[ClassifierSettings] Listeners[]` registriert ist (eine Klasse, die
  `expRequestVerdictListenerInterface` implementiert), der Stelle, an der später eine Sperrliste, eine Ratenbegrenzung oder eine
  Benachrichtigung hinzugefügt werden kann.

> **Hinweis**
> Eine Anfrage, die der Schutz ablehnt, erreicht den Kernel nie und steht daher nicht in `security.log`. Im Beobachtungsmodus des
> Schutzes kann dieselbe Abtastung in beiden Logs stehen. Zählen Sie sie nicht doppelt, wenn Sie einem Kunden Angriffszahlen
> berichten.

## auth.log und csp.log

- **`auth.log`** hält jeden Schritt der zweistufigen Anmeldung fest, nie einen Code oder ein Geheimnis. Eine Häufung gescheiterter
  zweiter Schritte bei einem Konto ist einen Blick wert ([Zweistufige Anmeldung](../sicherheit/zweistufige-anmeldung.md)).
- **`csp.log`** enthält die Meldungen der Browser zur Content Security Policy, in der Übersicht nach Direktive und blockierter
  Quelle gruppiert. Eine Flut von Meldungen ergibt eine Zeile `abuse/csp-report-flood` pro Minute in security.log
  ([Anfrage-Logs](../../../../eng-us/features/6.0/request-logs.md#the-log-viewer-rotation-and-the-error-digest),
  [Content Security Policy](../sicherheit/content-security-policy.md)).

## Das Prüfprotokoll und seine Senken

Das Prüfprotokoll ist eine signierte, manipulationssichtbare Aufzeichnung darüber, wer was getan hat, ab 6.0.15 standardmäßig an,
in `var/<site>/log/audit/` ([Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)):

```bash
./console exp:audit status --allow-root-user
./console exp:audit tail --channel=access --lines=20 --allow-root-user
./console exp:audit verify --allow-root-user     # exit 1 when a chain is broken
```

Eine Kopie auf derselben Maschine kann zusammen mit ihr zerstört werden. Leiten Sie das Protokoll mit einer **Senke** weiter: syslog
(RFC 5424; mit `Transport=local` der native Socket von journald, sodass `journalctl -t exponential` die Einträge findet), ein
signierter Webhook (HMAC-SHA-256, mit Warteschlange und Wiederholungen) oder E-Mail bei kritischen Ereignissen. Die Senken werden in
`settings/audit.ini` konfiguriert (`[AuditSink_syslog]`, `[AuditSink_webhook]`, `[AuditSink_mail]`); die vollständige Referenz ist
[die Anleitung zum Audit](../../../../eng-us/bc/6.0/audit.md). Die Sicht des Hosters, einschließlich der Frage, wer das Protokoll lesen darf,
steht auf [Prüfprotokoll](../sicherheit/pruefprotokoll.md).

> **Warnung**
> `settings/override/audit.ini.append.php` enthält die Signatur- und Pseudonymschlüssel. Sichern Sie sie mit jedem Backup, und
> halten Sie sie so vertraulich wie das Datenbankpasswort ([Was gesichert werden muss](backups-was-gesichert-werden-muss.md)).

## Eine wöchentliche Routine

Aus der eigenen Checkliste des Projekts ([Sicherheitshärtung](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll)):

1. Führen Sie die Cronjob-Gruppe `frequent` aus, damit die Alarmregeln des Audits laufen, und prüfen Sie, wer Alarm-Mails erhält
   (`./console exp:audit alerts recipients`).
2. Leiten Sie das Prüfprotokoll mit einer Senke an ein zentrales Log oder SIEM weiter, und führen Sie regelmäßig `exp:audit verify` aus.
3. Sehen Sie sich wöchentlich die Fehlerübersicht und `security.log` an; ein plötzlicher Anstieg einer Regel ist einen Blick wert.
4. Geben Sie `audit/read` und den Log-Viewer nur Administratoren.

## Checkliste

- [ ] `security.log` ist in **Setup > Logdateien** aufgeführt und wird rotiert (mitgeliefert: 5 Archive, nach 90 Tagen bereinigt).
- [ ] Ausnahmen von Regeln für Anwendungen von Kunden werden mit `Excludes[]` gemacht und pro Kunde dokumentiert.
- [ ] Blockiert wird vor dem Kernel (Schutz, Webserver, Firewall), nicht vom Klassifizierer erwartet.
- [ ] Das Prüfprotokoll wird von der Maschine weitergeleitet und nach Zeitplan geprüft.
- [ ] Die Schlüsseldatei des Audits ist in jedem Backup.
- [ ] Jemand sieht sich wöchentlich die Übersicht von `security.log` an.

**Zeilen `demo/mode`.** Jeder Prozess, der mit der Umgebungsvariablen `EXP_DEMO_MODE=1` startet, schreibt eine Zeile: `demo/mode on pid=<pid> sapi=<sapi> EXP_DEMO_MODE=1: ...`. Auf einer öffentlichen Demo ist das erwartet; auf jeder anderen Website bedeutet es, dass der Demomodus sie erreicht hat: Lösen Sie darauf einen Alarm aus. [Probleme mit dem Demomodus](../runbooks/probleme-mit-dem-demomodus.md).

## Verwandte Seiten

- [Anfrage-Logs](anfrage-logs.md)
- [Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md)
- [Härtung für Hoster](../sicherheit/haertung-fuer-hoster.md)
- [Kompromittierte Website](../runbooks/kompromittierte-website.md) (Runbook)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
