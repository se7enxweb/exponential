---
title: Überblick über die Überwachung
audience: hosting, admin
category: hosting
order: 90
summary: Alles, was Exponential zur Beobachtung einer gehosteten Website bietet (Statusbefehle mit Exit-Codes, die Zustandsprüfungen der Systeminformationen, Zustand der Logs und die Fehlerübersicht, Alarme des Audits, die Endpunkte für Zustand und Kennzahlen von Velocity), und wie Sie sie zu einer Überwachung für viele Kunden zusammenfügen.
keywords: Überwachung, Zustandsprüfungen, Status, Exit-Code, JSON, Systeminformationen, Zustand der Logs, Fehlerübersicht, Alarme des Audits, Prometheus, Kennzahlen, Dashboard von Velocity, Benchmark, Benachrichtigungen, monitoring, health checks, exit code, system information, log health, error digest, audit alerts, metrics, notifications
type: help
---
# Überblick über die Überwachung

Diese Seite ist für Systemadministratoren, die die Überwachung für Exponential-Websites aufbauen, und für Support-Mitarbeitende,
die wissen müssen, wo sie zuerst nachsehen. Sie beantwortet: welche Signale Exponential gibt, welche davon ein
Überwachungssystem lesen kann (Exit-Codes, JSON, HTTP-Endpunkte) und welche in die Hände eines Menschen gehören, der einmal pro
Woche auf eine Seite schaut.

![Job-Warteschlange: wartende, laufende und gescheiterte Hintergrundjobs](../../bilder/hosting/job-queue.webp)

Es gibt keine einzelne Zustands-URL; kombinieren Sie eine Prüfung von außen mit den Statusbefehlen im Inneren
([10.11 Überwachung und Zustandsprüfungen](../../../installation/10-nach-der-installation.md#1011-überwachung-und-zustandsprüfungen)). Die
externen Prüfungen stehen auf [Zustandsprüfungen und Verfügbarkeit](zustandspruefungen-und-verfuegbarkeit.md).

## Inhalt

1. [Vier Schichten der Überwachung](#vier-schichten-der-überwachung)
2. [Statusbefehle für ein Überwachungssystem](#statusbefehle-für-ein-überwachungssystem)
3. [Seiten für einen Menschen](#seiten-für-einen-menschen)
4. [Die eigenen Endpunkte von Velocity](#die-eigenen-endpunkte-von-velocity)
5. [Alarme, die die Website selbst sendet](#alarme-die-die-website-selbst-sendet)
6. [Viele Kunden-Websites überwachen](#viele-kunden-websites-überwachen)
7. [Checkliste](#checkliste)

## Vier Schichten der Überwachung

| Schicht | Frage | Die Werkzeuge von Exponential |
|---|---|---|
| Erreichbarkeit | Antwortet die Website, schnell genug? | eine HTTP-Prüfung von außen; `/Q/health` von Velocity |
| Plattform | Sind PHP, OPcache, die Datenbank, die Festplatte, Cron in Ordnung? | `exp:systeminfo`, `exp:cache status`, `crontab:list` |
| Anwendung | Gibt es neue Fehler, Angriffe, gescheiterte Jobs, Probleme mit Mail? | die Logs, die Fehlerübersicht, `exp:mail:status`, `exp:notificationstatus` |
| Integrität | Hat jemand etwas geändert, was er nicht sollte? | das Prüfprotokoll und `exp:audit verify` |

## Statusbefehle für ein Überwachungssystem

Alle laufen aus dem Wurzelverzeichnis der Installation als Benutzer der Website. Die meisten haben `--json` für eine Maschine
und enden mit einem erkennbaren Status oder einem Exit-Code.

| Prüfung | Befehl | Gesund |
|---|---|---|
| Zustandsprüfungen der Plattform | `./console exp:systeminfo --json` | Exit-Status 0; er endet mit 1, wenn eine Prüfung scheitert |
| Caches | `php bin/php/cache.php status --json --allow-root-user` | endet mit `PASS` |
| Velocity | `php bin/php/console exp:velocity status --json --allow-root-user` | läuft, Worker wie konfiguriert |
| Benachrichtigungen | `php bin/php/console exp:notificationstatus` | keine wartenden Ereignisse ohne kürzlichen Lauf |
| Prüfprotokoll | `php bin/php/audit.php verify --allow-root-user` | Exit-Status 0 (1 bedeutet, dass eine Kette gebrochen ist) |
| E-Mail-Einstellungen | `./console exp:mail:status` | fünf Tabellen `ok`, keine Probleme aufgeführt |
| Cron | `php bin/php/console crontab:list` und die Daten der Cron-Logs | Einträge vorhanden, Logs aktuell |
| Festplatte | `df -h /path/to/installation` | Platz für Caches, Logs und ein Backup |
| Leistung | `php bin/php/console exp:benchmark --baseline=var/benchmark/base.json` | Exit-Status 0 (keine Verschlechterung) |
| Datenbank | SQLite: [Eine SQLite-Website überwachen](../../../installation/09-datenbanken.md#überwachung-einer-sqlite-site); Oracle: `ext:ezoracle:health` | |

Quellen: [10.11](../../../installation/10-nach-der-installation.md#1011-überwachung-und-zustandsprüfungen),
[Systeminformationen](../../../anleitungen/systeminformationen.md),
[E-Mail-Einstellungen](../../../anleitungen/mail-einstellungen-administrator.md#3-die-statusseite),
[Benchmark](../../../funktionen/6.0/benchmark.md).

Für einen einzelnen Server genügt ein kleines Skript in der Crontab des Benutzers der Website, das diese ausführt und bei einem
Exit-Status ungleich null eine Mail schickt. Für viele Server führen Sie dieselben Befehle aus Ihrem Überwachungsagenten aus und
sammeln die Exit-Codes.

> **Hinweis**
> `exp:systeminfo` auf der Kommandozeile meldet das PHP der Kommandozeile, nicht den PHP-FPM-Pool oder Velocity, der die Website
> ausliefert. Die Seite **Setup > Systeminformationen** der Administration zeigt den Server, der die Anfrage beantwortet hat;
> öffnen Sie sie über jeden Server, den Sie betreiben
> ([Systeminformationen, welcher Server geantwortet hat](../../../anleitungen/systeminformationen.md#2-welcher-server-geantwortet-hat)).

## Seiten für einen Menschen

| Seite | Was sie zeigt | Wer sie öffnen darf |
|---|---|---|
| **Setup > Systeminformationen** (`/setup/info`) | welcher Server geantwortet hat, Zustandsprüfungen mit dem, was zu tun ist, Versionen, OPcache, Datenbank, Speicher, Cron, Mail | `setup/system_info` |
| **Setup > Logdateien > Zustand der Logs** (`/setup/logshealth`) | von Logs belegter Platz gegenüber einem Budget, am schnellsten wachsende Logs, Dateien, die der Webserver nicht schreiben kann, der neueste Fehler jedes Logs | `setup/logs` |
| **Setup > Logdateien > Fehlerübersicht** (`/setup/logsdigest`) | die verschiedenen Fehler und Warnungen der letzten 24 Stunden, gruppiert und gezählt | `setup/logs` |
| Reiter **Audit** (`/audit/dashboard`) | der Zustand der Kette jedes Kanals, aktuelle Ereignisse, Alarme | `audit/read` |
| **Setup > Cronjobs** | Teile, Zeitpläne, letzte Läufe, Teile, die Aufmerksamkeit brauchen | `setup/managecronjobs` |
| **Setup > E-Mail-Einstellungen** | Zustand der Mail-Sperre, Probleme, Zahlen | `mailpreferences/administrate` |

Zustand der Logs warnt ab 80 % von `logviewer.ini [HealthSettings] DiskBudget` (mitgeliefert 256 MB) und kennzeichnet Logs mit
gemischten Eigentümern, die die Rotation aufhalten ([Zustand der Logs](../../../funktionen/6.0/log-betrachter.md#zustand-der-logs)). Siehe
[Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md).

> **Tipp**
> Ein wöchentlicher Blick auf die Fehlerübersicht und `security.log` steht in der eigenen Checkliste des Projekts: Ein plötzlicher
> Anstieg einer Regel ist einen Blick wert
> ([Sicherheitshärtung](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll)).

## Die eigenen Endpunkte von Velocity

Wenn Exponential Velocity eine Website ausliefert, hat der Server eigene Ansichten
([Kontrollzentrum von Velocity](../../../funktionen/6.0/velocity-control-panel.md)):

| Endpunkt | Format | Nutzung |
|---|---|---|
| `/Q/health` | JSON, für Monitore | antwortet jedem mit `{"status":"ok"}`; Kennzahlen nur für einen Administrator |
| `/Q/metrics` | Text für Prometheus | Abruf in ein Kennzahlensystem |
| `/Q/stats` | JSON, die volle Nutzlast | Dashboards wie Grafana |
| `/Q/dashboard` | HTML | eine Ansicht für Menschen auf Worker, Speicher und Verkehr |

Wer sie öffnen darf, wird in `settings/velocity.ini [DashboardSettings]` festgelegt: standardmäßig nur von derselben Maschine;
`Token=` setzt ein Geheimnis, das sie von außen öffnet (`?token=...` oder ein Header `Authorization: Bearer`); `Remote=enabled`
öffnet sie ohne Token, nur für einen Port, den eine Firewall bereits auf vertrauenswürdige Adressen beschränkt.

Der **Watchdog** von Velocity (`exp:velocity watchdog install`, ein systemd-Timer) startet die Standard-Engine wieder, wenn sie
nicht läuft und niemand das wollte, und schreibt in `var/log/velocity-watchdog.log`, was er getan hat
([Der Watchdog](../../../bc/6.0/velocity-engines.md#der-watchdog-expvelocity-watchdog)). Er rührt nie einen laufenden Server
an; einen Server, der läuft, aber nicht antwortet, muss Ihre Überwachung erkennen.

## Alarme, die die Website selbst sendet

Das Prüfprotokoll wertet Alarmregeln im Cronjob-Teil `audit` aus (Teil von `frequent`): Brute-Force-Anmeldungen, die Vergabe einer
Administratorrolle, außerhalb der Arbeitszeit geschriebene Einstellungen, Massenlöschungen, ein ausgeschaltetes Audit und eine
gebrochene Kette ([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)). Alarme gehen an die Empfänger einer Regel, sonst an
die konfigurierten, sonst an die `AdminEmail` der Website:

```bash
./console exp:audit alerts recipients --allow-root-user
```

Entscheiden Sie auf einer gehosteten Website, ob Alarme an den Kunden, an Ihr Support-Team oder an beide gehen, und stellen Sie
sicher, dass der Teil `frequent` läuft ([Cronjobs](cronjobs.md)). Senken leiten das Protokoll an syslog oder einen Webhook für
ein zentrales System weiter ([Sicherheitslog](sicherheitslog.md)).

## Viele Kunden-Websites überwachen

- **Überall dieselben Befehle.** Jede Installation hat die Befehle oben; führen Sie sie pro Installationsverzeichnis aus, als
  Benutzer dieser Installation.
- **Exit-Codes, nicht Text.** Werten Sie den Exit-Status und das JSON aus; der Text für Menschen kann sich zwischen Releases ändern.
- **Eine externe Prüfung pro Adresse einer Website**, dazu eine pro Knoten oder Engine-Port, wo Sie mehrere betreiben (Apache und
  Velocity auf verschiedenen Ports zeigen verschiedene Zahlen).
- **Schwellen pro Tarif.** Antwortzeit und Festplattennutzung unterscheiden sich je Kunde; halten Sie nach dem Go-Live eine
  Basislinie pro Website fest (`exp:benchmark --save`) und vergleichen Sie damit.

## Checkliste

- [ ] Eine externe HTTP-Prüfung deckt die Website und die Anmeldeseite der Administration ab.
- [ ] `exp:systeminfo`, der Cache-Status, die Prüfung des Audits und die Festplatte werden nach Zeitplan per Exit-Code geprüft.
- [ ] Die Cron-Logs sind aktuell, und `crontab:list` zeigt die erwarteten Zeilen.
- [ ] Die Dashboard-Ansichten von Velocity sind nur lokal oder durch ein Token geschützt.
- [ ] Die Empfänger von Alarmen des Audits sind mit dem Kunden vereinbart.
- [ ] Jemand liest wöchentlich die Fehlerübersicht und `security.log`.

## Verwandte Seiten

- [Zustandsprüfungen und Verfügbarkeit](zustandspruefungen-und-verfuegbarkeit.md)
- [Log-Viewer und Log-Rotation](log-viewer-und-log-rotation.md)
- [Anfrage-Logs](anfrage-logs.md), [Sicherheitslog](sicherheitslog.md)
- [Benchmark einer gehosteten Website](../leistung/benchmark-einer-gehosteten-website.md)
- [Langsame Website](../runbooks/langsame-website.md), [Website nicht erreichbar](../runbooks/website-nicht-erreichbar.md) (Runbooks)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
