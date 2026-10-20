---
title: Cronjobs
audience: hosting, admin
category: hosting
order: 140
summary: Was die Cronjob-Teile von Exponential tun, welche eine gehostete Website braucht und wie oft, die Crontab-Zeilen, die Sie für jeden Kunden einrichten (als welcher Benutzer, mit welchem PHP), was auf Hosts zu tun ist, die Cron begrenzen, und wie sich Cron während der Wartung und auf mehreren Knoten verhält.
keywords: Cron, Cronjobs, Crontab, runcronjobs.php, Cronjob-Teile, frequent, infrequent, cache_cleanup, session_gc, PhpCliPath, exponential.cron, cron:frequent, Cron auf Shared Hosting, geplante Aufgaben, cronjob parts, shared hosting cron, scheduled tasks
type: help
---
# Cronjobs

Diese Seite ist für Systemadministratoren, die Exponential-Websites bereitstellen, und für Support-Mitarbeitende, die die
Frage beantworten, warum die Benachrichtigung nie verschickt wurde. Sie beantwortet: welche geplanten Jobs eine
Exponential-Website braucht, was jeder Teil tut, wie Sie sie in der Crontab eines Kunden einrichten und was schiefgeht, wenn
sie nicht laufen.

Die vollständige Anleitung ist [Cronjobs: ausführen, planen und verfolgen](../../../../en/us/guides/cronjobs.md); die Fassung des
Installationsbuchs ist [10.3 Cronjobs](../../../../en/us/install/10-after-installing.md#103-cronjobs). Die Seite **Setup > Cronjobs** der
Administration wird auf [Die Cronjob-Konsole](die-cronjob-konsole.md) beschrieben.

## Inhalt

1. [Teile und Skripte](#teile-und-skripte)
2. [Was eine gehostete Website braucht](#was-eine-gehostete-website-braucht)
3. [Eine Crontab für eine Kunden-Website](#eine-crontab-für-eine-kunden-website)
4. [Welcher Benutzer, welches PHP](#welcher-benutzer-welches-php)
5. [Hosts, die Cron begrenzen](#hosts-die-cron-begrenzen)
6. [Cron während der Wartung und auf mehreren Knoten](#cron-während-der-wartung-und-auf-mehreren-knoten)
7. [Wenn Jobs nicht laufen](#wenn-jobs-nicht-laufen)
8. [Checkliste](#checkliste)

## Teile und Skripte

Ein **Skript** ist eine PHP-Datei in einem Cronjob-Verzeichnis (`cronjobs/` oder dem einer Erweiterung). Ein **Teil** ist eine
benannte Liste von Skripten in `settings/cronjob.ini` (`[CronjobPart-<name>] Scripts[]`), die gemeinsam laufen, in einem Prozess,
nach einem Zeitplan. `php runcronjobs.php <part>` führt einen Teil aus; ohne Namen eines Teils führt es die Skripte aus
`[CronjobSettings] Scripts[]` aus, den Standard-Teil (die Seite der Administration nennt ihn **global**).

| Teil | Skripte | Was sie tun |
|---|---|---|
| Standard (global) | `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php` | geplantes Zurückziehen und Verbergen, RSS-Importe, verzögerte Suchindizierung, alte interne Entwürfe |
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | Benachrichtigungen, ausstehende Workflows, Inhaltsjobs, die Senken und Alarme des Prüfprotokolls, das Bounce-Postfach |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | verwaiste Warenkörbe, Linkprüfung, Aufbewahrung der E-Mail-Einstellungen |
| `cache_cleanup` | `cachecleanup.php`, `httpcache_cleanup.php`, `querycache_cleanup.php` | Caches, die nie wieder ausgeliefert werden können |
| `logs` | `explogrotate.php` | Logs rotieren, bereinigen und komprimieren (in keinem Standard-Teil) |
| `cleanuprss` | `cleanuprss.php` | kürzt per RSS importierte Inhalte; tut nichts, bis er konfiguriert ist |
| `cluster_maintenance` | `clusterpurge.php` | entfernt abgelaufene Cluster-Dateien |
| `unlock` | `unlock.php` | gibt Objekte frei, die in einem gesperrten Zustand hängen |

Einzelne Teile gibt es auch für `contentjobs`, `audit`, `notification`, `mailpreferences`, `mailbounces` und `contentcheck`.
Erweiterungen fügen eigene Teile hinzu (Sitemaps, Newsletter, Oracle-Wartung); `./console list cron` zeigt jeden Teil mit einer
Beschreibung. `php runcronjobs.php --list` listet jeden Teil und seine Skripte auf.

## Was eine gehostete Website braucht

| Teil | Nötig, wenn | Zeitplan, den die Quellen vorschlagen |
|---|---|---|
| `frequent` | immer: Benachrichtigungen, Workflows, Inhaltsjobs und das Prüfprotokoll hängen davon ab | alle paar Minuten (`*/5 * * * *` in den Beispielen) |
| Standard (global) | immer: geplantes Veröffentlichen und verzögerte Indizierung | `*/15 * * * *` in den Beispielen |
| `infrequent` | immer: Linkprüfung, Aufbewahrung der E-Mail-Einstellungen | stündlich oder täglich |
| `cache_cleanup` | ausgelastete Websites, damit Cache-Verzeichnisse nicht wachsen | einmal pro Nacht |
| `session_gc.php` | wo die Sitzungsbereinigung von PHP durch das Betriebssystem nicht läuft | einmal pro Nacht, mit `--script=` |
| `logs` | wenn der Kernel und nicht logrotate die Logs verwaltet | einmal pro Nacht |

Die Alarmregeln, die Rotation und die Archivierung des Prüfprotokolls laufen innerhalb von `frequent`; eine Installation, die nur
die Gruppe `frequent` ausführt, braucht dafür keine zusätzliche Crontab-Zeile (`settings/cronjob.ini`). Ohne `frequent` werden
keine Benachrichtigungen verschickt, und die Alarme des Audits lösen nie aus.

## Eine Crontab für eine Kunden-Website

Aus [10.3, Eine Crontab](../../../../en/us/install/10-after-installing.md#a-crontab), in der Crontab des Benutzers der Website:

```text
# m   h  dom mon dow  command
*/5   *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
*/15  *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site >> var/log/cron-default.log 2>&1
17    *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site infrequent >> var/log/cron-infrequent.log 2>&1
40    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cache_cleanup >> var/log/cron-cleanup.log 2>&1
50    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site --script=session_gc.php >> var/log/cron-cleanup.log 2>&1
```

- `-s site` nennt den Siteaccess, in dem die Jobs laufen; Indizierung, Sitemaps und Benachrichtigungen wirken auf die Inhalte
  und URLs dieser Website. Nutzen Sie den eigenen öffentlichen Siteaccess des Kunden.
- `-q` gibt nur Fehler aus; die Umleitung bewahrt auf, was ausgegeben wird. Rotieren Sie `cron-*.log` mit logrotate des
  Systems, da der Kernel sie nicht schreibt.
- Das Repository liefert eine Beispieldatei mit, `exponential.cron` (der alte Name `ezpublish.cron` ist eine erhaltene Kopie),
  mit einer Variablen für das Wurzelverzeichnis der Installation und die PHP-Binärdatei
  ([ExponentialSDK und exponential.cron](../../../../en/us/bc/6.0/exponentialsdk-and-exponential-cron.md)).
- **Setup > Cronjobs** schlägt eine Crontab-Zeile für jeden Teil vor, den noch nichts ausführt, geschrieben mit dem
  Wurzelverzeichnis und der PHP-Binärdatei der Installation; der Zeitplan kommt aus
  `cronjob.ini [AdminSettings] CrontabSchedule_<part>` ([Cronjobs, Abschnitt 12](../../../../en/us/guides/cronjobs.md#12-scheduling-with-the-crontab)).

## Welcher Benutzer, welches PHP

- **Der Benutzer der Website.** Richten Sie die Zeilen in der Crontab des Benutzers ein, als der der Webserver die Website
  betreibt (`crontab -e -u <site user>` oder `./console crontab:edit` als dieser Benutzer), damit die Dateien, die die Jobs
  schreiben, ihm gehören. Als `root` verweigern die Skripte den Start ohne `--allow-root-user`, und die Dateien, die sie anlegen,
  gehören danach root.
- **Dasselbe PHP wie die Website.** Verwenden Sie dieselbe PHP-Binärdatei, die der Webserver nutzt (`/opt/.../php` auf Hosts mit
  mehreren PHP-Versionen); `php -v` in der Cron-Umgebung kann sich von dem Ihrer Shell unterscheiden. Schreiben Sie den
  vollständigen Pfad der Binärdatei in die Zeile. Für Jobs, die aus der Seite der Administration gestartet werden, setzen Sie
  `cronjob.ini [AdminSettings] PhpCliPath` auf dieselbe Binärdatei.
- **Sperren.** Jedes Skript nimmt eine Sperre, während es läuft; ein zweiter Lauf desselben Skripts gibt
  `Cronjob part locked by other process` aus und überspringt es. Eine Sperre, die älter ist als
  `[CronjobSettings] MaxScriptExecutionTime` (mitgeliefert `43200` Sekunden), wird übernommen.

## Hosts, die Cron begrenzen

Manche Hosting-Tarife erlauben wenige Cron-Einträge oder ein Mindestintervall. Möglichkeiten:

- **Eine Zeile für `frequent`** ist das Minimum für Benachrichtigungen und das Audit; fügen Sie den Standard-Teil demselben
  Zeitplan hinzu, wenn nur eine Zeile pro Intervall erlaubt ist (zwei Befehle mit `&&` verbunden).
- **Die Cron-Funktion eines Panels** funktioniert genauso, solange sie den Befehl im Wurzelverzeichnis der Installation als
  Benutzer der Website ausführt; siehe [Cron und Shell-Zugang](../control-panels/cron-und-shell-zugang.md).
- **Der Planer von Velocity** ist kein Ersatz: Er führt kein beliebiges PHP-Skript der Kommandozeile aus; dafür nutzen Sie
  weiter den Cron des Systems ([Planer von Velocity](../../../../en/us/features/6.0/velocity-scheduler.md)).

## Cron während der Wartung und auf mehreren Knoten

- **Der Wartungsmodus hält Cron nicht an.** Cronjobs laufen in einer Shell und sind nicht betroffen. Pausieren Sie die Teile, die
  während eines Fensters nicht laufen dürfen (Importe, Neuaufbau von Indizes), auf **Setup > Cronjobs**, oder entfernen Sie die
  Zeilen für die Dauer des Fensters
  ([Anleitung zum Wartungsmodus](../../../../en/us/guides/maintenance-mode.md#10-installations-cronjobs-and-deploys)).
- **Mehrere Knoten: eine Crontab.** Führen Sie die Jobs nur auf einem Knoten aus; die Sperren der Skripte sind auf einem Knoten
  lokal. Ein gemeinsames `[PauseSettings] Directory` lässt eine Pause für jeden Knoten gelten
  ([Mehrere Web-Knoten](mehrere-web-knoten.md)).

## Wenn Jobs nicht laufen

| Symptom | Sehen Sie nach |
|---|---|
| Benachrichtigungen werden nicht verschickt | steht `frequent` in der Crontab? `./console exp:notificationstatus` |
| Geplantes Zurückziehen fand nicht statt | der Standard-Teil und der Siteaccess bei `-s` |
| Ein Job läuft, scheitert aber | sein Log (`cron-*.log`) und `var/log/error.log` |
| Jedes Mal `Cronjob part locked by other process` | ein vorheriger Lauf läuft noch oder wurde beendet; siehe die Regeln zu Sperren oben |
| Ein Teil wird mit `is paused` übersprungen | er wurde auf **Setup > Cronjobs** pausiert; übersprungene Läufe werden in `cronjob_skipped.log` festgehalten |
| Jobs schreiben Dateien, die die Website nicht ersetzen kann | die Crontab läuft als root; verlegen Sie sie zum Benutzer der Website |

Der Abschnitt zur Fehlersuche der Anleitung behandelt weitere Fälle: [Cronjobs, Abschnitt 17](../../../../en/us/guides/cronjobs.md#17-troubleshooting).

## Checkliste

- [ ] `frequent`, der Standard-Teil und `infrequent` sind für jede Website geplant.
- [ ] Die Crontab gehört dem Benutzer der Website und nennt die PHP-Binärdatei, auf der die Website läuft.
- [ ] `-s` nennt den öffentlichen Siteaccess des Kunden.
- [ ] Die Ausgabe geht in Logdateien, die logrotate rotiert, oder bewusst nach `/dev/null`.
- [ ] Auf mehreren Knoten führt nur ein Knoten die Crontab aus.
- [ ] Runbooks für Wartungsfenster sagen, welche Teile pausiert werden.

**Cron auf einer öffentlichen Demo.** Eine Demo im Demomodus braucht `EXP_DEMO_MODE=1` auch in ihrer Crontab, sonst laufen ihre Cronjobs mit ausgeschaltetem Demomodus. Jeder Cronjob-Prozess schreibt dann einmal `demo/mode ... sapi=cli` in `security.log`. [`crontab-demo`](../../../../en/us/examples/demo-mode/crontab-demo), [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Die Cronjob-Konsole](die-cronjob-konsole.md)
- [Cron und Shell-Zugang](../control-panels/cron-und-shell-zugang.md)
- [Wartungsmodus](wartungsmodus.md)
- [Einwilligung und Bounces bei Mails](einwilligung-und-bounces-bei-mails.md)
- [Checkliste für den Start einer Kunden-Website](../checklisten/checkliste-fuer-den-start-einer-kunden-website.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
