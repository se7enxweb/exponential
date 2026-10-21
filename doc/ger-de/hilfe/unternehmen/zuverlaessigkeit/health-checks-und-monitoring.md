---
title: Health-Checks und Monitoring
audience: businesses, admin, hosting
category: business-solutions
order: 40
summary: Was ein Unternehmen auf einer Exponential-Website überwachen sollte, von außen und von innen, welche mitgelieferten Befehle und Seiten eine klare Antwort „gesund“ oder „nicht gesund“ geben und wie Sie Alarme an eine Person leiten.
keywords: Monitoring, Überwachung, Health-Check, Zustandsprüfung, Verfügbarkeitsmonitor, Systeminformationen, Watchdog, Exit-Code, Alarme, monitoring, health check, uptime monitor, exp:system:info, exp:velocity status --json, system information, /Q/health, watchdog, exit code, alerts
type: help
---
# Health-Checks und Monitoring

Diese Seite richtet sich an Führungskräfte, die sicher sein wollen, dass jemand bemerkt, wenn die Website ein Problem
hat, und an die Person im Betrieb, die die Prüfungen einrichtet. Sie erklärt die vier Ebenen, die Sie beobachten, die
Befehle, die mit einem klaren Exit-Code oder JSON antworten, und die Seiten, die eine Person einmal pro Woche liest. Die
technische Referenz ist [Überblick über die Überwachung](../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md) und
[Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md).

## Inhalt

1. [Keine einzelne Health-URL](#keine-einzelne-health-url)
2. [Die vier Ebenen](#die-vier-ebenen)
3. [Prüfungen von außen](#prüfungen-von-außen)
4. [Prüfungen auf dem Server](#prüfungen-auf-dem-server)
5. [Status und Health-Endpunkt von Velocity](#status-und-health-endpunkt-von-velocity)
6. [Seiten für einen Menschen](#seiten-für-einen-menschen)
7. [Alarme, die jemanden erreichen](#alarme-die-jemanden-erreichen)
8. [Checkliste](#checkliste)

## Keine einzelne Health-URL

Das Installationsbuch sagt es direkt: „Es gibt keine einzelne Health-URL; kombinieren Sie eine Prüfung von außen mit den
Statusbefehlen im Inneren“
([10.11 Überwachung und Zustandsprüfungen](../../../installation/10-nach-der-installation.md#1011-überwachung-und-zustandsprüfungen)).
Ein Unternehmen braucht beide Hälften: Die Prüfung von außen sieht, was Kunden sehen, die Prüfungen im Inneren erklären,
warum.

## Die vier Ebenen

Aus dem [Überblick über die Überwachung](../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md#vier-schichten-der-überwachung):

| Ebene | Die geschäftliche Frage | Werkzeuge |
|---|---|---|
| Erreichbarkeit | Erreichen Kunden die Website, und zwar schnell genug? | eine HTTP-Prüfung von außen; `/Q/health` von Velocity |
| Plattform | Sind PHP, Datenbank, Festplatte und Cron in Ordnung? | `exp:system:info`, der Cache-Status, `crontab:list` |
| Anwendung | Gibt es neue Fehler, Angriffe, fehlgeschlagene Jobs, Mailprobleme? | die Logs, die Fehlerübersicht, der Status von Mail und Benachrichtigungen |
| Integrität | Hat jemand etwas geändert, was er nicht sollte? | der Audit-Trail und sein Prüfbefehl |

## Prüfungen von außen

Verwenden Sie einen externen Dienst oder ein Werkzeug zur Verfügbarkeitsüberwachung, dem Sie vertrauen. Richten Sie es
auf zwei Adressen ([Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#prüfungen-von-außen)):

| Prüfung | Gesunde Antwort |
|---|---|
| Die Startseite, über HTTPS, unter der Adresse, die Kunden verwenden | `200` und eine Antwortzeit, die Sie kennen |
| Die Anmeldeseite der Administration (`/admin/user/login` bei einer typischen Installation) | `200` |

Warum beide: Die Startseite wird oft aus einem Cache beantwortet und kann daher grün bleiben, während die Datenbank
ausgefallen ist. Die Anmeldeseite läuft durch die Anwendung. Eine Prüfung, die nach einem Wort sucht, das nur die echte
Seite enthält, ist stärker als ein Statuscode allein.

Während geplanter Wartung antwortet die Website mit `503` und einem Header `Retry-After`. Halten Sie den Monitor für das
Wartungsfenster an oder lassen Sie ihn diese Antwort akzeptieren, solange die Wartung läuft, und alarmieren Sie, wenn das
Fenster länger dauert als angekündigt ([Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md)).

## Prüfungen auf dem Server

Führen Sie diese nach Zeitplan als Benutzer der Website aus und alarmieren Sie bei einem Exit-Status ungleich null. Alle
wurden am Code geprüft; führen Sie sie im Wurzelverzeichnis der Installation aus.

| Prüfung | Befehl | Gesund |
|---|---|---|
| Zustand der Plattform | `php bin/php/console exp:system:info --json --allow-root-user` | Exit-Status 0; er endet mit 1, wenn eine Zustandsprüfung fehlschlägt |
| Caches | `php bin/php/cache.php status --json --allow-root-user` | endet mit `PASS` |
| Velocity, falls verwendet | `php bin/php/console exp:velocity status --json --allow-root-user` | läuft, Worker wie konfiguriert |
| Audit-Trail | `php bin/php/audit.php verify --allow-root-user` | Exit-Status 0; 1 bedeutet, dass eine Kette gebrochen ist |
| Cron | `php bin/php/console crontab:list` und die Datumsangaben der Cron-Logs | Einträge vorhanden, Logs aktuell |
| Festplatte | `df -h` auf dem Dateisystem der Installation | Platz für Caches, Logs und ein Backup |

Quellen: [Systeminformationen, von der Kommandozeile](../../../anleitungen/systeminformationen.md#8-von-der-kommandozeile),
[Überblick über die Überwachung](../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md#statusbefehle-für-ein-überwachungssystem).

> **Hinweis**
> Der Kommandozeilenbericht beschreibt das Kommandozeilen-PHP, nicht den PHP-FPM-Pool oder Velocity, die Besucher
> bedienen. Öffnen Sie **Setup > Systeminformationen** über jeden Server, den Sie betreiben, um das PHP zu sehen, das
> Kunden tatsächlich erreichen
> ([Systeminformationen, welcher Server geantwortet hat](../../../anleitungen/systeminformationen.md#2-welcher-server-geantwortet-hat)).

Der Rat des Installationsbuchs für einen einzelnen Server: „Ein kleines Skript in der Crontab des Website-Benutzers, das
diese ausführt und bei einem Exit-Status ungleich null eine Mail schickt, genügt“
([10.11](../../../installation/10-nach-der-installation.md#1011-überwachung-und-zustandsprüfungen)).

## Status und Health-Endpunkt von Velocity

Wenn die Website von Exponential Velocity ausgeliefert wird, gibt es zwei weitere Signale
([Zustandsprüfungen und Verfügbarkeit, Velocity](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#velocity-zustandsendpunkt-und-watchdog)):

- `exp:velocity status` meldet `running` nur für einen Server, der auf eine HTTP-Anfrage antwortet; Prozesse, die nicht
  antworten, werden als `not answering` gemeldet, und der Befehl endet mit einem Exit-Status ungleich null
  ([Velocity-Engines](../../../bc/6.0/velocity-engines.md#stopp-neustart-und-status-der-qbix-engine)). Die README
  merkt an, dass „`status --json` für das Monitoring da ist“.
- `/Q/health` auf dem Server „antwortet jedem mit `{"status":"ok"}`“. Das sagt Ihnen, dass der Serverprozess antwortet;
  es rendert keine Seite, behalten Sie also auch die Prüfungen von außen bei.
- Der **Watchdog**, ein systemd-Timer, der mit `exp:velocity watchdog install` installiert wird, startet den Server
  wieder, wenn er ausgefallen ist, ohne dass das jemand beabsichtigt hat. Standardmäßig prüft er alle 45 Sekunden
  (`[WatchdogSettings] Interval` in `settings/velocity.ini`).

## Seiten für einen Menschen

Manche Signale sind für einen Menschen gedacht, der einmal pro Woche liest, nicht für einen Alarm
([Überblick über die Überwachung, Seiten für einen Menschen](../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md#seiten-für-einen-menschen)):

| Seite | Was sie zeigt |
|---|---|
| **Setup > Systeminformationen** | Welcher Server geantwortet hat, Zustandsprüfungen mit dem, was zu tun ist, Versionen, Datenbank, Speicher, Cron, Mail |
| **Setup > Logdateien > Zustand der Logs** | Von Logs belegter Plattenplatz, am schnellsten wachsende Logs, der neueste Fehler jedes Logs |
| **Setup > Logdateien > Fehlerübersicht** | Die verschiedenen Fehler und Warnungen der letzten 24 Stunden, gruppiert und gezählt |
| Reiter **Audit** | Der Zustand jedes Audit-Kanals, neueste Ereignisse, Alarme |
| **Setup > Cronjobs** | Teile, Zeitpläne, letzte Läufe, Teile, die Aufmerksamkeit brauchen |

Die Zustandsprüfungen unter **Setup > Systeminformationen** umfassen PHP-Version und -Erweiterungen, OPcache, Speicher-
und Zeitlimits, beschreibbare Verzeichnisse, freien Plattenplatz, eingeschaltet gelassene Debug-Ausgabe,
Entwicklungseinstellungen, ausgeschaltete Caches, seit zwei Tagen nicht gelaufene Cronjobs, Mail und Datenbank
([Systeminformationen, Zustandsprüfungen](../../../anleitungen/systeminformationen.md#3-zustandsprüfungen)). Jede fehlgeschlagene
Prüfung sagt, was zu tun ist.

## Alarme, die jemanden erreichen

Eine Prüfung, die niemand liest, ist kein Monitoring. Legen Sie im Voraus fest:

- **Wer einen Alarm erhält**, während und außerhalb der Bürozeiten, und wer die Vertretung ist.
- **Wie schnell** die Person nachsehen soll und was sie zuerst tut
  ([Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md)).
- **Wohin Alarme gehen**: Mail, ein Chat-Kanal, ein Telefon. Der Audit-Trail verschickt außerdem eigene Alarm-Mails bei
  Ereignissen wie Brute-Force-Anmeldungen oder der Vergabe einer Administratorrolle; prüfen Sie die Empfänger mit
  `php bin/php/console exp:audit alerts recipients` ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#try-it-in-two-minutes)).
- **Was kein Alarm ist**: ein geplantes Wartungsfenster, eine einzelne langsame Anfrage. Zu viele Fehlalarme gewöhnen
  Menschen daran, echte zu übersehen.

## Checkliste

- [ ] Eine externe Prüfung ruft die Startseite und die Anmeldeseite der Administration über HTTPS ab.
- [ ] Die oben genannten Serverprüfungen laufen nach Zeitplan und alarmieren bei einem Exit-Status ungleich null.
- [ ] Geplante Wartung wird erkannt und alarmiert niemanden; ein überzogenes Wartungsfenster schon.
- [ ] Mit Velocity: Der Watchdog ist installiert, und sein Log (`var/log/velocity-watchdog.log`) wird beobachtet.
- [ ] Eine benannte Person und eine Vertretung erhalten Alarme und kennen die ersten Schritte.
- [ ] Jemand liest jede Woche **Setup > Systeminformationen** und die Fehlerübersicht.

## Verwandte Seiten

- [Logs und Fehlerreferenzen](logs-und-fehlerreferenzen.md)
- [Verfügbarkeit in der Praxis](verfuegbarkeit-in-der-praxis.md)
- [Überblick über die Überwachung](../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
