---
title: "Beobachtbarkeit: Logs, Debug-Leiste und Audit-Trail"
audience: software-engineer, admin, developer
category: software-engineering
order: 400
summary: Was eine Exponential-Installation über sich selbst verrät und wo - die Kernel-Logs und ihre Rotation, die Anfrage-Logs 404.log und security.log, der Log-Viewer und exp:logs, Fehlerreferenzen, die Debug-Leiste, der manipulationssichere Audit-Trail, Systeminformationen, Zustandsprüfungen und der Status von Velocity - und wie Sie sie bei einer Untersuchung kombinieren.
keywords: Beobachtbarkeit, Logs, error.log, 404.log, security.log, Log-Viewer, exp:logs, Fehlerreferenz, Debug-Leiste, Audit-Trail, exp:audit, Systeminformationen, Überwachung, Zustandsprüfung, Metriken, Softwareingenieur, observability, log viewer, error reference, debug bar, audit trail, monitoring, health check, metrics
type: help
---
# Beobachtbarkeit: Logs, Debug-Leiste und Audit-Trail

Diese Seite richtet sich an Ingenieure, die sehen müssen, was eine laufende Installation tut: um eine Seite zu debuggen,
einen Vorfall zu untersuchen oder das System an eine Überwachung anzubinden. Exponential hat keinen eigenen
Metrik-Endpunkt; es hat strukturierte Logs, einen Log-Viewer mit Suche, eine Debug-Leiste, einen manipulationssicheren
Audit-Trail und Statusbefehle.

## Inhalt

1. [Die Signale auf einen Blick](#die-signale-auf-einen-blick)
2. [Logs](#logs)
3. [Der Log-Viewer und exp:logs](#der-log-viewer-und-explogs)
4. [Die Debug-Leiste](#die-debug-leiste)
5. [Der Audit-Trail](#der-audit-trail)
6. [Zustand und Status](#zustand-und-status)
7. [Eine Untersuchung von Anfang bis Ende](#eine-untersuchung-von-anfang-bis-ende)
8. [Datenschutz in den Signalen](#datenschutz-in-den-signalen)

## Die Signale auf einen Blick

| Signal | Wo | Frage, die es beantwortet |
|---|---|---|
| Kernel-Logs | `var/log/error.log`, `warning.log`, `notice.log`, `debug.log`, `strict.log`, `deprecated.log`, `auth.log` | was im Code schiefging |
| Anfrage-Logs | `var/log/404.log`, `var/log/security.log` | was nicht gefunden wurde, was wie eine Sondierung oder ein Angriff aussah |
| Logs je Website | `var/<site>/log/*.log` | dasselbe je Website, wenn Websites ein eigenes Log-Verzeichnis haben |
| Fehlerreferenz | die Fehlerseite und `error.log` (`ERR-...`) | welche Log-Zeilen zur Meldung eines Besuchers gehören |
| Debug-Leiste | die Seite selbst, für aufgeführte Adressen | warum diese Seite langsam oder falsch ist |
| Audit-Trail | Reiter Audit, `./console exp:audit` | wer was getan hat, und Beweis, dass die Aufzeichnung unversehrt ist |
| Systeminformationen | Setup > Systeminformationen | Versionen, Engine, Datenbank, Caches |
| Status von Velocity | `./console exp:velocity status` | ob der Server läuft, welche Engine, welche Worker |

## Logs

- Die Debug-Logs und `eZLog` rotieren, wenn eine Datei `site.ini [FileSettings] LogFileMaxSize` (2 MB) übersteigt, und
  behalten `LogFileRotateCount` (3) Archive ([Log-Rotation und Bereinigung](../../funktionen/6.0/log-rotation.md)).
- Nicht-gefunden-Antworten gehen mit dem Grund, den der Kernel fand, nach `404.log`; was der Anfrageklassifizierer für
  eine Sondierung, einen Pfaddurchlauf, eine Injektion, ein Angriffswerkzeug oder eine Häufung von Nicht-gefunden-Antworten
  hält, geht nach `security.log`. Jede Anfrage wird nur in ein Log geschrieben, und die Klassifizierung blockiert nichts
  ([Anfrage-Logs](../../funktionen/6.0/anfrageprotokolle.md)).
- Bei mehreren Websites können Logs in das Verzeichnis jeder Website oder auf ein anderes Dateisystem gehen
  ([Logs, INI-Cache und Ablaufzeitstempel je Website](../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)).
- Code schreibt über `eZDebug::writeError()`, `writeWarning()`, `writeNotice()` und `eZLog`, nie mit `echo` oder
  `var_dump` ([eZDebug und Protokollierung](../erweiterungsentwicklung/teil-8-referenz/ezdebug-und-protokollierung.md)).

## Der Log-Viewer und exp:logs

**Setup > Logdateien** (`setup/logs`) listet jedes Log der Installation, verfolgt eines live, durchsucht eine Datei, jedes
Kernel-Log oder jede Datei mit ihren Rotationen, zerlegt Zeilen in Zeit, Client, Siteaccess, Anfrage und Nachricht,
gruppiert Zeilen nach Anfrage und verknüpft sie mit den Audit-Einträgen derselben Minute. Standardmäßig ist er nur
lesend. Dasselbe in einer Shell:

```bash
./console exp:logs --help
./console exp:logs --reference=ERR-0123456789
```

Siehe [Der Log-Viewer](../../funktionen/6.0/log-betrachter.md) und
[Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md).

## Die Debug-Leiste

Die Exp-Debug-Leiste ersetzt den klassischen Debug-Bericht durch eine angeheftete Leiste: Seitenzeit, SQL-Anzahl und
-Zeit, Spitzenspeicher, verwendete Templates, Warnungen und Fehler sowie Reiter für Meldungen, Einstellungen, Cache,
Zeitmessung, SQL, Templates, eingebundene Dateien, Speicher und Velocity. Zeigen Sie sie nur Ihrer eigenen Adresse:

```ini
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10
```

Hinter einem Proxy stimmt die Adresse nur, wenn der Proxy in `TrustedProxies[]` aufgeführt ist
([Die Exp-Debug-Leiste](../../funktionen/6.0/exp-debug-leiste.md), [Anleitung zur Debug-Leiste](../../bc/6.0/debug-leiste.md)).

## Der Audit-Trail

Sicherheitsrelevante Ereignisse (Anmeldungen und Ablehnungen, Inhaltsänderungen, Schreibvorgänge an Einstellungen, das
Leeren von Caches, Bereitstellungen, Bestellungen, Änderungen der Mehrwertsteuer) werden in einem hashverketteten Log mit
signierten Manifesten aufgezeichnet, standardmäßig eingeschaltet. Warnregeln achten auf Brute-Force-Anmeldungen, eine
vergebene Administratorrolle, Einstellungen, die außerhalb der Arbeitszeit geschrieben werden, Massenlöschungen und eine
gebrochene Kette.

```bash
./console exp:audit status --allow-root-user
./console exp:audit tail --channel=access --name='access.session.*' --lines=3 --allow-root-user
./console exp:audit verify --allow-root-user        # exit 1 when a chain is broken
```

Die Cronjob-Gruppe `frequent` muss für Indexierung, Warnungen und Rotation laufen
([Der Audit-Trail](../../funktionen/6.0/pruefprotokoll.md), [Anleitung zum Audit](../../bc/6.0/audit.md)).

## Zustand und Status

Exponential hat keine einzelne URL für den Zustand. Kombinieren Sie eine Prüfung von außen (die Startseite und die
Anmeldung der Administration, weil die Startseite aus einem Cache beantwortet werden kann, während die Datenbank ausgefallen
ist) mit Statusbefehlen auf dem Server ([Zustandsprüfungen und Verfügbarkeit](../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md),
[Überwachung im Überblick](../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md)). Der Wartungsmodus antwortet mit `503` und
`Retry-After`, sodass sich ein geplantes Fenster von einem Ausfall unterscheiden lässt.

## Eine Untersuchung von Anfang bis Ende

1. Ein Besucher meldet eine Fehlerseite mit einer Referenz: Suchen Sie sie im Log-Viewer oder mit
   `exp:logs --reference`.
2. Lesen Sie jede Zeile derselben Anfrage, dann die Audit-Einträge derselben Minute: Was hat sich kurz davor geändert?
3. Reproduzieren Sie mit eingeschalteter Debug-Leiste für Ihre Adresse.
4. Prüfen Sie `security.log`, wenn die Anfrage ungewöhnlich aussieht.
5. Halten Sie fest, was Sie gefunden haben ([Umgang mit Störungen](umgang-mit-stoerungen.md)).

## Datenschutz in den Signalen

Log-Viewer, Anfrage-Logs und Audit-Trail maskieren standardmäßig E-Mail- und IP-Adressen, Tokens, Schlüssel, Session-IDs
und Cookies; Passwörter und Geheimnisse werden nie aufgezeichnet. Das Aufheben der Maskierung im Log-Viewer braucht die
Richtlinie `setup/unmasklogs`. Belassen Sie es so, wenn Sie Logs in ein anderes System exportieren.

## Verwandte Seiten

- [Debugging und Logs](../ersteller/anwendungsentwickler/fehlersuche-und-logs.md)
- [Log-Viewer und Rotation, für Hoster](../hosting/betrieb/log-viewer-und-log-rotation.md)
- [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
