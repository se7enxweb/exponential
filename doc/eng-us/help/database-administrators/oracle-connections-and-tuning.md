---
title: "Oracle: persistent connections, DRCP and tuning"
audience: dba
category: database-administration
order: 20
summary: The connection and performance settings of the ezoracle extension - persistent connections, Database Resident Connection Pooling, keep-alive for long-running workers, failover over several listeners, retries and call timeouts, prefetch, cursor sharing, session tracing in V$SESSION and the slow query log - and the extension-order rule that decides which settings win.
keywords: Oracle, ezoracle.ini, ConnectionSettings, Persistent, DRCP, DBMS_CONNECTION_POOL, START_POOL, ConnectionClass, KeepAliveInterval, Hosts, ServiceName, Failover, LoadBalance, RetryCount, CallTimeout, Prefetch, LobPrefetch, CursorSharing, TraceSettings, V$SESSION, SlowQueryThreshold, oracle-slow.log, ActiveExtensions order
type: help
---
# Oracle: persistent connections, DRCP and tuning

This page is for Oracle database administrators who tune the connections of an Exponential site. A new dedicated
Oracle session costs tens of milliseconds, and a site with many PHP workers can open many sessions. This page answers:
how to reuse and pool sessions, how to keep long-running workers connected, how to fail over, which performance and
tracing settings exist, and how to make sure your settings are the ones in effect. Every feature is off by default.
Source: [Persistent connections and DRCP](../../install/09-databases.md#persistent-connections-and-drcp) and
`extension/ezoracle/INSTALL`.

## Contents

1. [Where the settings live](#where-the-settings-live)
2. [Persistent connections](#persistent-connections)
3. [DRCP](#drcp)
4. [Keep-alive for workers](#keep-alive-for-workers)
5. [Several listeners, retries and timeouts](#several-listeners-retries-and-timeouts)
6. [Performance settings](#performance-settings)
7. [Tracing and the slow query log](#tracing-and-the-slow-query-log)
8. [Which settings win](#which-settings-win)
9. [Checklist](#checklist)

## Where the settings live

The plain connection is in `site.ini [DatabaseSettings]` ([Setting up Oracle](oracle-setup.md)). Everything else is
in `ezoracle.ini` (shipped in `extension/ezoracle/settings/ezoracle.ini`), overridden in
`settings/override/ezoracle.ini.append.php`.

## Persistent connections

```ini
# settings/override/ezoracle.ini.append.php
[ConnectionSettings]
# oci_pconnect: inherit (site.ini UsePersistentConnection, the shipped value), enabled or disabled
Persistent=enabled
```

Persistent connections live as long as the PHP process: a PHP-FPM worker, a Velocity worker, a CLI script.
`oci8.persistent_timeout`, `oci8.max_persistent` and `oci8.ping_interval` in `php.ini` govern them and cannot be set
at run time.

## DRCP

Database Resident Connection Pooling shares a pool of server processes between all PHP processes, so hundreds of
workers do not need hundreds of dedicated server processes:

```ini
[ConnectionSettings]
DRCP=enabled
# oci8.connection_class: sessions of one class are shared
ConnectionClass=EXPONENTIAL
```

With an Easy Connect string the driver appends `:POOLED`; with a descriptor built from `Hosts[]` it adds
`SERVER=POOLED`; for a TNS alias put `(SERVER=POOLED)` into `tnsnames.ora` yourself. Start the pool once in the
database, as a DBA:

```sql
EXECUTE DBMS_CONNECTION_POOL.START_POOL;
```

## Keep-alive for workers

```ini
[ConnectionSettings]
# after this many idle seconds a round trip checks the connection first and replaces a dead one (shipped: 0, never)
KeepAliveInterval=60
```

Velocity workers and cronjobs keep connections for a long time. The keep-alive lets them survive a database restart
or an idle timeout on a firewall.

## Several listeners, retries and timeouts

| Key | What it does |
|---|---|
| `ConnectString`, `TnsAdmin` | A connect string that replaces `site.ini Database` (empty: use `Database`); the directory of `tnsnames.ora`. The PHP-FPM pool's `env[TNS_ADMIN]` or Velocity's environment is the surer place, because the client reads it once per process. |
| `Hosts[]`, `ServiceName`, `Failover`, `LoadBalance`, `ConnectTimeout` | Build a descriptor over several listeners (when `ConnectString` is empty). |
| `RetryCount`, `RetryDelay`, `RetryBackoff` | Connection retries with back-off (default: `site.ini ConnectRetries`). |
| `ReconnectErrors[]`, `RetryReads` | On a lost connection outside a transaction, reconnect and run a read again; a write is reported. |
| `CallTimeout` | Milliseconds a round trip may take (client 18c or later); 0 means no limit. |

## Performance settings

| `[PerformanceSettings]` key | What it does |
|---|---|
| `Prefetch` | Rows per round trip. |
| `LobPrefetch` | LOB bytes fetched with the row; shipped 2000 and capped there by the driver, which writes a notice when more is set (a client version cut longer CLOBs silently above 2000). |
| `CursorSharing` | `CURSOR_SHARING` for the session; shipped `FORCE`. |

Gather optimizer statistics regularly with `ext:ezoracle:gather-stats` or the nightly cronjob
([Oracle maintenance commands and cronjobs](oracle-maintenance-commands.md)).

## Tracing and the slow query log

`[TraceSettings]` sets what a DBA sees in `V$SESSION` and in AWR or ASH reports, with patterns:

```ini
[TraceSettings]
ClientIdentifier=%siteaccess%:%user_id%
ModuleName=Exponential %siteaccess%
Action=%module%/%view%
ClientInfo=pid %pid%
```

`[LogSettings] SlowQueryThreshold`, `SlowQueryLog` and `MaskLiterals` write slow statements to
`var/log/oracle-slow.log`, with string literals masked ([Finding slow queries](finding-slow-queries.md)).

## Which settings win

When two active extensions both provide `ezoracle.ini` settings, the one listed **earlier** in `ActiveExtensions[]`
wins. `ezoracle` ships a complete `ezoracle.ini`; if you keep your settings in a site extension of your own instead of
`settings/override/`, list it **before** `ezoracle`:

```ini
[ExtensionSettings]
ActiveExtensions[]=mysite_settings
ActiveExtensions[]=ezoracle
```

`settings/override/` always wins over every extension. Check the value in effect:

```bash
php bin/php/console exp:ini get ezoracle.ini/ConnectionSettings/DRCP --allow-root-user
```

The order can also be changed under **Setup > Extensions** ([Extension loading order](../../features/6.0/extension-loading-order.md)).

## Checklist

- [ ] `Persistent=enabled` and, with many workers, `DRCP=enabled` with the pool started.
- [ ] `KeepAliveInterval` set when Velocity serves the site.
- [ ] `TNS_ADMIN` and `LD_LIBRARY_PATH`, if needed, set for PHP-FPM, Velocity and cron alike.
- [ ] Tracing set, so sessions can be told apart in `V$SESSION`.
- [ ] `exp:ini get` shows the values you wrote.

## Related pages

- [Setting up Oracle with ezoracle](oracle-setup.md)
- [Performance and scaling](performance-and-scaling.md)
- [Oracle for hosts](../hosting/basics/oracle-for-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
