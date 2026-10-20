---
title: Anfrage-Logs
audience: hosting, admin
category: hosting
order: 100
summary: Wie Exponential echte Fehler (error.log) von Antworten „nicht gefunden“ (404.log) und verdächtigen Anfragen (security.log) trennt, was jede Zeile enthält, wie Adressen maskiert werden, welche Einstellungen das steuern und wie Support-Mitarbeitende die Logs nutzen, um Kunden zu antworten.
keywords: 404.log, security.log, error.log, Anfrage-Logs, nicht gefunden, defekte Links, Request-Klassifizierer, MaskIP, NotFoundLog, Datenschutz, Log-Rotation, Fehlerübersicht, request logs, not found, broken links, request classifier, privacy, log rotation, error digest
type: help
---
# Anfrage-Logs

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende eines Hosters. Sie beantwortet: wo eine Anfrage nach einer
fehlenden Seite und eine Anfrage, die wie ein Angriff aussieht, festgehalten werden, was eine Zeile Ihnen sagt, wie
personenbezogene Daten aus den Dateien herausgehalten werden, wie viel Festplatte sie belegen und wie Sie sie nutzen, wenn ein
Kunde fragt, warum Besucher Fehler bekommen.

Jedes Format und jede Einstellung unten stammt aus [Anfrage-Logs: 404.log und security.log](../../../../eng-us/features/6.0/request-logs.md),
wo auch die Schnittstellen für Entwickler beschrieben sind. Die Sicherheitsseite hat eine eigene Seite: [Sicherheitslog](sicherheitslog.md).

## Inhalt

1. [Drei Logs statt einem](#drei-logs-statt-einem)
2. [Eine Zeile in 404.log lesen](#eine-zeile-in-404log-lesen)
3. [Datenschutz als Standard](#datenschutz-als-standard)
4. [Einstellungen](#einstellungen)
5. [Festplattennutzung und Rotation](#festplattennutzung-und-rotation)
6. [Die Logs im Support nutzen](#die-logs-im-support-nutzen)
7. [Checkliste](#checkliste)

## Drei Logs statt einem

Exponential schreibt eine Antwort „nicht gefunden“ und eine verdächtige Anfrage in eigene Logs, sodass `error.log` nur die
echten Fehler enthält:

| Log (in `var/log/`) | Was es enthält | Stufe im Log-Viewer |
|---|---|---|
| `error.log` | echte Fehler: ein Datenbankfehler, ein Template, das sich nicht kompilieren lässt, ein PHP-Fehler | error |
| `404.log` | Antworten 404 und 410, mit dem Grund, den der Kernel gefunden hat | notice |
| `notice.log` | Antworten 401 und 403 (Antworten zum Zugriff) | notice |
| `security.log` | Anfragen, die der Request-Klassifizierer als Abtastung, Traversal, Injection, Angriffswerkzeug oder Häufung von Antworten „nicht gefunden“ einstuft, mit jedem Status | warning |

Jede Anfrage wird in eines davon geschrieben, nie in zwei: Eine Abtastung, die mit 404 endet, steht nur in `security.log`.
Nichts wird blockiert; der Klassifizierer stuft nur ein ([Anfrage-Logs, kurz gesagt](../../../../eng-us/features/6.0/request-logs.md#in-short)).

> **Hinweis**
> Auf einer Website, auf der der Webserver (oder ein Schutz davor) eine Anfrage ablehnt, bevor Exponential startet, steht diese
> Anfrage überhaupt nicht in diesen Logs. Suchen Sie sie in den eigenen Logs des Webservers.

## Eine Zeile in 404.log lesen

```text
[ Oct 17 2026 09:00:04 ] [2001:db8:x:x] 404 GET /content/view/full/9999 siteaccess=site reason=node:9999 node=9999 referrer=https://example.org/news ua=5d41402a "Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5)"
```

| Feld | Bedeutung |
|---|---|
| `[2001:db8:x:x]` | die Adresse des Clients, maskiert |
| `404` | der Status: 404 oder 410 |
| `GET /content/view/full/9999` | Methode und URI; jeder Wert der Query wird durch `***` ersetzt |
| `siteaccess=` | der Siteaccess, der geantwortet hat |
| `reason=` | warum die Seite fehlt: `module:<name>`, `view:<module>/<view>`, `disabled:<module>`, `node:<id>`, `object:<id>`, `rule:<name>`, `file:<object>/<attribute>` oder `kernel:<type>/<number>` |
| `node=` | die ID des Knotens, wenn bekannt |
| `referrer=` | die Seite, die hierher verlinkt hat, oder `-` |
| `ua=5d41402a "..."` | 8 Hexadezimalziffern des SHA-1 des User Agents und seine ersten 60 Zeichen |

Das Feld `reason=` macht das Log für den Support nützlich: `node:9999` ist ein Link auf entfernte Inhalte, `module:wp-admin` ist
ein Roboter, der nach einem anderen Produkt sucht, `disabled:...` ist eine Ansicht, die der Siteaccess abschaltet.

## Datenschutz als Standard

- **Adressen werden maskiert** (`[RequestLogSettings] MaskIP=enabled`): `203.0.x.x`, `2001:db8:x:x`. Die rohe Adresse wird nie in
  eine Datei geschrieben. `disabled` schreibt sie vollständig; die Seite der Funktion sagt, das nur für eine Website zu tun, die
  einen Grund und eine Rechtsgrundlage dafür hat.
- **Werte der Query** sind in `404.log` `***`; Geheimnisse und E-Mail-Adressen im Pfad werden maskiert.
- **Der User Agent** ist ein kurzer Hash plus seine ersten 60 Zeichen: genug, um ein Werkzeug vom anderen zu unterscheiden, kein
  Fingerabdruck.
- Der Log-Viewer maskiert erneut, was er zeigt, und die Fehlerübersicht ist immer maskiert.

> **Hinweis**
> Die Maskierung verringert personenbezogene Daten in den Logs; sie entscheidet nicht, wie lange Sie sie aufbewahren dürfen. Das
> ist Teil Ihrer Datenschutzrichtlinie und der Ihres Kunden. Diese Seite ist keine Rechtsberatung; siehe
> [Aufbewahrung von Logs und Daten](../sicherheit/aufbewahrung-von-logs-und-daten.md), und holen Sie Ihren eigenen rechtlichen Rat ein.

## Einstellungen

`site.ini [RequestLogSettings]` (überschrieben in `settings/override/site.ini.append.php`):

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `NotFoundLog` | `enabled` | `disabled` schickt Antworten „nicht gefunden“ wie früher an `error.log` |
| `NotFoundLogFile` | `404.log` | der Dateiname, im Log-Verzeichnis |
| `MaskIP` | `enabled` | die Adresse des Clients maskieren |
| `MaxURILength` | `300` | die längste geschriebene URI und der längste Referrer; längere enden auf `...` |

Das Log-Verzeichnis ist `var/log` oder das eigene Log-Verzeichnis der Website mit `[FileSettings] UseGlobalLogDir=disabled`
([Aufbau des Speichers](aufbau-des-speichers.md#mehrere-websites-in-einer-installation)). Der Klassifizierer wird in
`settings/requestclassifier.ini` konfiguriert; siehe [Sicherheitslog](sicherheitslog.md).

## Festplattennutzung und Rotation

Beide Logs sind Kernel-Logs des Log-Viewers, mit eigenen Rotationseinstellungen in `settings/logviewer.ini [RotationSettings]`:
je 5 Archive, komprimiert, eine Größengrenze von 10 MB für den Cronjob-Teil `logs` und eine Bereinigung, die Archive von
`404.log` nach 30 Tagen und von `security.log` nach 90 Tagen entfernt. Während eine Zeile geschrieben wird, rotiert der Schreiber
die Datei außerdem bei `site.ini [FileSettings] LogFileMaxSize` wie jedes andere Log
([Log-Rotation](../../../../eng-us/features/6.0/log-rotation.md)).

```ini
# settings/override/logviewer.ini.append.php: keep security.log longer
[RotationSettings]
Keep[security]=10
MaxAgeDays[security]=365
```

Die Bereinigung läuft, wenn der Cronjob-Teil `logs` läuft (er ist in keinem Standard-Teil) oder wenn `./console exp:logs --prune`
aufgerufen wird; siehe [Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md).

## Die Logs im Support nutzen

| Frage des Kunden | Wo Sie nachsehen |
|---|---|
| „Besucher melden defekte Links“ | **Setup > Logdateien > Fehlerübersicht**, `404.log` nach `reason=` gruppiert; Einträge `node:#` sind entfernte Inhalte, `referrer=` nennt die Seite, die noch darauf verlinkt |
| „Google meldet viele 404“ | dieselbe Übersicht; viele Gründe `module:` für Pfade anderer Produkte sind Roboter, keine defekten Links |
| „Werden wir angegriffen?“ | `security.log`, gruppiert nach `<category>/<rule>` ([Sicherheitslog](sicherheitslog.md)) |
| „Eine Seite zeigte einen Fehler mit einer Referenz“ | `error.log`, über die Referenz gefunden ([Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md#die-zeilen-einer-anfrage-finden)) |

Aus einer Shell lassen sich dieselben Dateien mit dem Befehl des Log-Viewers durchsuchen:

```bash
./console exp:logs --search='reason=node' --file=var/log/404.log --rotations
```

## Checkliste

- [ ] `NotFoundLog=enabled` und `MaskIP=enabled` (die mitgelieferten Werte), es sei denn, der Kunde hat schriftlich anders entschieden.
- [ ] Der Cronjob-Teil `logs` oder ein geplantes `exp:logs --prune` hält Archive innerhalb ihrer Altersgrenzen.
- [ ] Support-Mitarbeitende kennen die Fehlerübersicht und ihre Gruppierung nach `reason=`.
- [ ] Die Aufbewahrung der Anfrage-Logs ist Teil der Datenschutzdokumentation des Kunden.

## Verwandte Seiten

- [Sicherheitslog](sicherheitslog.md)
- [Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md)
- [Überblick über die Überwachung](ueberblick-ueber-die-ueberwachung.md)
- [request-shield und Request-Regeln](../sicherheit/request-shield-und-request-regeln.md)
- [Anfrage-Logs: 404.log und security.log](../../../../eng-us/features/6.0/request-logs.md) (Referenz der Funktion)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
