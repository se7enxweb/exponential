---
title: "Oracle: persistente Verbindungen, DRCP und Tuning"
audience: dba
category: database-administration
order: 20
summary: Die Verbindungs- und Leistungseinstellungen der Erweiterung ezoracle - persistente Verbindungen, Database Resident Connection Pooling, Keep-alive für langlebige Worker, Failover über mehrere Listener, Wiederholungen und Aufruf-Timeouts, Prefetch, Cursor Sharing, Session-Tracing in V$SESSION und das Log langsamer Abfragen - und die Regel zur Reihenfolge der Erweiterungen, die entscheidet, welche Einstellungen gewinnen.
keywords: Oracle, ezoracle.ini, ConnectionSettings, Persistent, DRCP, DBMS_CONNECTION_POOL, START_POOL, ConnectionClass, KeepAliveInterval, Hosts, ServiceName, Failover, LoadBalance, RetryCount, CallTimeout, Prefetch, LobPrefetch, CursorSharing, TraceSettings, V$SESSION, SlowQueryThreshold, oracle-slow.log, Reihenfolge von ActiveExtensions, Verbindungspooling, Tuning, connection pooling, tuning, ActiveExtensions order
type: help
---
# Oracle: persistente Verbindungen, DRCP und Tuning

Diese Seite richtet sich an Oracle-Datenbankadministratoren, die die Verbindungen einer Exponential-Website abstimmen. Eine
neue dedizierte Oracle-Session kostet Dutzende Millisekunden, und eine Website mit vielen PHP-Workern kann viele Sessions
öffnen. Diese Seite beantwortet: wie Sie Sessions wiederverwenden und bündeln, wie langlebige Worker verbunden bleiben, wie
Sie ein Failover einrichten, welche Leistungs- und Tracing-Einstellungen es gibt und wie Sie sicherstellen, dass Ihre
Einstellungen die wirksamen sind. Jede Funktion ist standardmäßig aus. Quelle:
[Persistente Verbindungen und DRCP](../../installation/09-datenbanken.md#persistente-verbindungen-und-drcp) und
`extension/ezoracle/INSTALL`.

## Inhalt

1. [Wo die Einstellungen liegen](#wo-die-einstellungen-liegen)
2. [Persistente Verbindungen](#persistente-verbindungen)
3. [DRCP](#drcp)
4. [Keep-alive für Worker](#keep-alive-für-worker)
5. [Mehrere Listener, Wiederholungen und Timeouts](#mehrere-listener-wiederholungen-und-timeouts)
6. [Leistungseinstellungen](#leistungseinstellungen)
7. [Tracing und das Log langsamer Abfragen](#tracing-und-das-log-langsamer-abfragen)
8. [Welche Einstellungen gewinnen](#welche-einstellungen-gewinnen)
9. [Checkliste](#checkliste)

## Wo die Einstellungen liegen

Die einfache Verbindung steht in `site.ini [DatabaseSettings]` ([Oracle mit ezoracle einrichten](oracle-einrichten.md)). Alles
andere steht in `ezoracle.ini` (mitgeliefert in `extension/ezoracle/settings/ezoracle.ini`), überschrieben in
`settings/override/ezoracle.ini.append.php`.

## Persistente Verbindungen

```ini
# settings/override/ezoracle.ini.append.php
[ConnectionSettings]
# oci_pconnect: inherit (site.ini UsePersistentConnection, the shipped value), enabled or disabled
Persistent=enabled
```

Persistente Verbindungen leben so lange wie der PHP-Prozess: ein PHP-FPM-Worker, ein Velocity-Worker, ein CLI-Skript.
`oci8.persistent_timeout`, `oci8.max_persistent` und `oci8.ping_interval` in `php.ini` steuern sie und lassen sich zur
Laufzeit nicht setzen.

## DRCP

Database Resident Connection Pooling teilt einen Pool von Serverprozessen zwischen allen PHP-Prozessen, sodass Hunderte von
Workern nicht Hunderte dedizierter Serverprozesse brauchen:

```ini
[ConnectionSettings]
DRCP=enabled
# oci8.connection_class: sessions of one class are shared
ConnectionClass=EXPONENTIAL
```

Bei einem Easy-Connect-String hängt der Treiber `:POOLED` an; bei einem aus `Hosts[]` gebauten Deskriptor fügt er
`SERVER=POOLED` hinzu; für einen TNS-Alias tragen Sie `(SERVER=POOLED)` selbst in `tnsnames.ora` ein. Starten Sie den Pool
einmal in der Datenbank, als DBA:

```sql
EXECUTE DBMS_CONNECTION_POOL.START_POOL;
```

## Keep-alive für Worker

```ini
[ConnectionSettings]
# after this many idle seconds a round trip checks the connection first and replaces a dead one (shipped: 0, never)
KeepAliveInterval=60
```

Velocity-Worker und Cronjobs halten Verbindungen lange. Das Keep-alive lässt sie einen Neustart der Datenbank oder ein
Leerlauf-Timeout einer Firewall überstehen.

## Mehrere Listener, Wiederholungen und Timeouts

| Schlüssel | Was er tut |
|---|---|
| `ConnectString`, `TnsAdmin` | Ein Connect-String, der `site.ini Database` ersetzt (leer: `Database` verwenden); das Verzeichnis von `tnsnames.ora`. `env[TNS_ADMIN]` des PHP-FPM-Pools oder die Umgebung von Velocity sind der sicherere Ort, weil der Client ihn einmal je Prozess liest. |
| `Hosts[]`, `ServiceName`, `Failover`, `LoadBalance`, `ConnectTimeout` | Einen Deskriptor über mehrere Listener bauen (wenn `ConnectString` leer ist). |
| `RetryCount`, `RetryDelay`, `RetryBackoff` | Verbindungswiederholungen mit Back-off (Standard: `site.ini ConnectRetries`). |
| `ReconnectErrors[]`, `RetryReads` | Bei einer verlorenen Verbindung außerhalb einer Transaktion neu verbinden und einen Lesezugriff wiederholen; ein Schreibzugriff wird gemeldet. |
| `CallTimeout` | Millisekunden, die ein Roundtrip dauern darf (Client 18c oder neuer); 0 bedeutet keine Grenze. |

## Leistungseinstellungen

| Schlüssel in `[PerformanceSettings]` | Was er tut |
|---|---|
| `Prefetch` | Zeilen je Roundtrip. |
| `LobPrefetch` | Mit der Zeile geholte LOB-Bytes; mitgeliefert 2000 und vom Treiber dort begrenzt, der einen Hinweis schreibt, wenn mehr gesetzt ist (eine Client-Version schnitt längere CLOBs über 2000 still ab). |
| `CursorSharing` | `CURSOR_SHARING` für die Session; mitgeliefert `FORCE`. |

Sammeln Sie regelmäßig Optimizer-Statistiken mit `ext:ezoracle:gather-stats` oder dem nächtlichen Cronjob
([Wartungsbefehle und Cronjobs für Oracle](oracle-wartungsbefehle.md)).

## Tracing und das Log langsamer Abfragen

`[TraceSettings]` legt mit Mustern fest, was ein DBA in `V$SESSION` und in AWR- oder ASH-Berichten sieht:

```ini
[TraceSettings]
ClientIdentifier=%siteaccess%:%user_id%
ModuleName=Exponential %siteaccess%
Action=%module%/%view%
ClientInfo=pid %pid%
```

`[LogSettings] SlowQueryThreshold`, `SlowQueryLog` und `MaskLiterals` schreiben langsame Anweisungen nach
`var/log/oracle-slow.log`, mit maskierten Zeichenkettenliteralen ([Langsame Abfragen finden](langsame-abfragen-finden.md)).

## Welche Einstellungen gewinnen

Liefern zwei aktive Erweiterungen beide Einstellungen für `ezoracle.ini`, gewinnt die, die in `ActiveExtensions[]`
**früher** steht. `ezoracle` liefert eine vollständige `ezoracle.ini`; halten Sie Ihre Einstellungen statt in
`settings/override/` in einer eigenen Website-Erweiterung, listen Sie diese **vor** `ezoracle`:

```ini
[ExtensionSettings]
ActiveExtensions[]=mysite_settings
ActiveExtensions[]=ezoracle
```

`settings/override/` gewinnt immer gegen jede Erweiterung. Prüfen Sie den wirksamen Wert:

```bash
php bin/php/console exp:ini get ezoracle.ini/ConnectionSettings/DRCP --allow-root-user
```

Die Reihenfolge lässt sich auch unter **Setup > Extensions** ändern
([Ladereihenfolge der Erweiterungen](../../funktionen/6.0/ladereihenfolge-der-extensions.md)).

## Checkliste

- [ ] `Persistent=enabled` und, bei vielen Workern, `DRCP=enabled` mit gestartetem Pool.
- [ ] `KeepAliveInterval` gesetzt, wenn Velocity die Website ausliefert.
- [ ] `TNS_ADMIN` und `LD_LIBRARY_PATH`, falls nötig, für PHP-FPM, Velocity und Cron gleichermaßen gesetzt.
- [ ] Tracing gesetzt, sodass sich Sessions in `V$SESSION` unterscheiden lassen.
- [ ] `exp:ini get` zeigt die Werte, die Sie geschrieben haben.

## Verwandte Seiten

- [Oracle mit ezoracle einrichten](oracle-einrichten.md)
- [Leistung und Skalierung](leistung-und-skalierung.md)
- [Oracle für Hoster](../hosting/grundlagen/oracle-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
