---
title: Velocity für Teams im öffentlichen Sektor
audience: government, hosting, admin
category: public-sector
order: 70
summary: Für interne IT-Teams, die Exponential Velocity betreiben, die empfohlene Engine. Wie sie gestartet, nach einem Absturz am Laufen gehalten, mit Bereitstellungen versorgt, beobachtet und abgesichert wird, mit den Punkten, nach denen eine interne Sicherheitsbewertung üblicherweise fragt.
keywords: Velocity, Exponential Velocity, qbix, exp:velocity, Watchdog, Startdienst, systemd, Bereitstellung, graceful, Bedienfeld, Dashboard, /Q/health, Logs, Worker, Sicherheitsbewertung, watchdog, boot service, deploy, control panel, dashboard, logs, workers, security assessment
type: help
---
# Velocity für Teams im öffentlichen Sektor

Diese Seite richtet sich an das Betriebsteam einer öffentlichen Stelle, das Exponential auf Velocity betreibt, dem
PHP-Anwendungsserver, der mit Exponential ausgeliefert wird und dessen empfohlene Engine ist. Sie beantwortet, wie
Velocity startet, wie er nach einem Absturz zurückkommt, wie eine Änderung ihn erreicht, wo er protokolliert, wie Sie ihn
beobachten und welche seiner Teile eine Sicherheitsbewertung betrachten wird. Die vollständige Seite für den Betrieb
ist [Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md); die Funktionsseiten sind
[Velocity-Webserver](../../../../funktionen/6.0/velocity-webserver.md) und
[Velocity-Server mit dauerhaften Workern](../../../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md).

## Inhalt

1. [Was Velocity ist](#was-velocity-ist)
2. [Starten und Stoppen](#starten-und-stoppen)
3. [Rückkehr nach einem Absturz oder Neustart](#rückkehr-nach-einem-absturz-oder-neustart)
4. [Eine Änderung bereitstellen](#eine-änderung-bereitstellen)
5. [Logs und Status](#logs-und-status)
6. [Punkte für eine Sicherheitsbewertung](#punkte-für-eine-sicherheitsbewertung)
7. [Checkliste](#checkliste)

## Was Velocity ist

Velocity ist ein PHP-Anwendungsserver. Worker halten die Anwendung zwischen Anfragen im Arbeitsspeicher, ein
Antwort-Cache beantwortet anonyme Besucher, bevor PHP läuft, und HTTPS mit Zertifikaten (einschließlich Let's Encrypt
und anderer ACME-Zertifizierungsstellen) ist eingebaut. Es braucht kein Apache, nginx oder PHP-FPM
([Bereitstellen](../../../../anleitungen/bereitstellung.md)). Es braucht PHP 8.0 oder neuer (ab seinem Release 0.0.4.54; davor
8.1), daher läuft es mit dem Standard-PHP von RHEL 9.

## Starten und Stoppen

```bash
./console exp:velocity config set --apply ServerSettings Engine qbix --allow-root-user
./console exp:velocity start --allow-root-user
./console exp:velocity status --allow-root-user
./console exp:velocity graceful --allow-root-user    # re-exec, listening socket kept, no request dropped
./console exp:velocity restart --allow-root-user
./console exp:velocity stop --allow-root-user
```

`config set` schreibt `settings/override/velocity.ini.append.php`, die eine Datei für die Werte dieser Installation;
die ausgelieferten Standardwerte bleiben in `settings/velocity.ini`. Reines HTTP liegt auf `Port` (ausgeliefert 8088),
HTTPS auf `HTTPSPort` (ausgeliefert 8080), beide an `Host` gebunden (ausgeliefert `127.0.0.1`).

## Rückkehr nach einem Absturz oder Neustart

`exp:velocity` hat keinen Befehl, der einen Startdienst installiert. Verwenden Sie eine eigene systemd-Unit, die
`exp:velocity start`, `stop` und `graceful` ausführt, oder die Unit-Dateien der Engine; die Wege und ihre Unterschiede
stehen unter [Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md). Unterziehen Sie die Unit derselben
Prüfung wie jede andere Service-Unit.

Der optionale **Watchdog** startet die Engine nach einem Absturz, einem Abbruch wegen Speichermangels oder einem Stopp
durch ein anderes Werkzeug erneut. Er stoppt oder startet niemals einen laufenden Server neu und ist ausgeschaltet, bis
er installiert wird:

```bash
./console exp:velocity watchdog install --apply --allow-root-user
./console exp:velocity watchdog status --allow-root-user
```

Jeder Start und jedes Zurückweichen wird in `var/log/velocity-watchdog.log` geschrieben und im Prüfprotokoll als
`system.velocity.watchdog.*` aufgezeichnet. Für die Kontinuitätsplanung ist das wichtig: Ein automatischer Neustart
wird protokolliert und kann einer Prüferin oder einem Prüfer nachträglich gezeigt werden.

## Eine Änderung bereitstellen

Worker halten Klassen, Einstellungen und kompilierte Templates im Arbeitsspeicher, daher braucht eine Änderung die
richtigen Schritte in der richtigen Reihenfolge. Ein Befehl erledigt sie, mit PASS, FAIL oder SKIP für jeden Schritt
und einem Halt beim ersten Fehler:

```bash
./console exp:velocity deploy --dry-run --allow-root-user
./console exp:velocity deploy --allow-root-user
```

Hängen Sie die Ausgabe des Probelaufs an den Änderungsantrag an; sie listet genau auf, was der echte Lauf tun wird.
Einzelheiten: [Bereitstellen](../../../../anleitungen/bereitstellung.md) und die Seiten zu Änderungen in diesem Bereich, zum
Beispiel [Staging und Freigabe](../aktualisierungen-und-aenderungen/staging-und-freigabe.md).

## Logs und Status

| Was | Wo |
|---|---|
| Zugriffs- und Fehler-Logs | `var/vc/qbix/log/<siteaccess>-access.log` und `-error.log` |
| Prozessausgabe | `var/vc/qbix/run/console.log` |
| Die eigenen Logs des Kernels | `var/log/` oder `var/<site>/log/`, wie unter jedem Server |

`exp:velocity status` meldet `running` nur für einen Server, der auf eine HTTP-Anfrage antwortet, und endet sonst mit
einem Wert ungleich null; `status --json` ist für die Überwachung gedacht. `/Q/health` antwortet jedem mit „ok“ und
eignet sich für eine externe Verfügbarkeitsprüfung ([Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md)).

## Punkte für eine Sicherheitsbewertung

| Thema | Was die Quellen sagen |
|---|---|
| Benutzer der Worker | `[ServerSettings] User` und `Group`; Worker dürfen niemals als root laufen ([Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md)) |
| Bedienfeld `/Q/panel` | nur diese Maschine, danach ein Passwort mit mindestens 16 Zeichen; aus der Ferne über einen SSH-Tunnel erreichbar ([Velocity-Bedienfeld](../../../../funktionen/6.0/velocity-control-panel.md)) |
| Dashboard `/Q/dashboard` | diese Maschine, anderswo nur mit `[DashboardSettings] Token`; lassen Sie `Remote` ausgeschaltet, sofern keine Firewall den Port beschränkt |
| Dateimodi der Logs | ausgeliefert `0640` und `0750`, weil ein Zugriffs-Log Besucher nennt |
| Antwort-Cache | liefert niemals an angemeldete Besucher aus; braucht Engine 0.0.4.26 oder neuer ([Antwort-Cache von Velocity](../../../hosting/leistung/antwort-cache-von-velocity.md)) |
| HTTPS | Zertifikate werden vor der Verwendung geprüft; ein selbstsignierter Platzhalter, bis ein echtes nutzbar ist ([HTTPS und Zertifikate in Velocity](../../../../funktionen/6.0/velocity-https-zertifikate.md)) |
| Trennung von Websites | ein Server für mehrere Websites „ist eine Bequemlichkeit beim Routing, keine Sicherheitsgrenze“; betreiben Sie einen Velocity-Server pro getrennter Organisation ([Isolation von Mandanten](../../../hosting/sicherheit/trennung-von-mandanten.md)) |

## Checkliste

- [ ] `exp:velocity status` zeigt den Server als laufend; die Website antwortet.
- [ ] Eine Start-Unit startet Velocity nach einem Neustart; der Watchdog ist installiert, falls gewünscht.
- [ ] Worker laufen als Benutzer der Website, nicht als root.
- [ ] Das Passwort des Bedienfelds ist gesetzt; `Remote` bleibt ausgeschaltet; das Token des Dashboards liegt in Ihrem Speicher für Geheimnisse.
- [ ] Bereitstellungen verwenden `exp:velocity deploy`, und sein Probelauf ist dem Änderungsantrag beigefügt.
- [ ] Ein echtes Zertifikat wird ausgeliefert, nicht der selbstsignierte Platzhalter.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Wahl des Webservers](wahl-des-webservers.md)
- [Worker und Pools von Velocity](../../../hosting/server/velocity-worker-und-pools.md)
- Runbook [Velocity startet nicht](../../../hosting/runbooks/velocity-startet-nicht.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
