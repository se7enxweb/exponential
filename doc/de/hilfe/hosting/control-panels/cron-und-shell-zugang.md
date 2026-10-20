---
title: Cron und Shell-Zugang
audience: hosting, admin
category: hosting
order: 80
summary: Wie Kunden auf Servern mit Panel runcronjobs.php von Exponential planen (Teile, Zeitpläne, die richtige PHP-Binärdatei, die Crontab des Benutzers der Website), was die Seite Cronjobs der Administration von PHP braucht und was eine Website ohne Shell-Zugang tun kann und was nicht.
keywords: Cron, Crontab, runcronjobs.php, Cronjob-Teile, frequent, infrequent, PhpCliPath, Setup > Cronjobs, proc_open, exec, posix_kill, Shell-Zugang, SSH, Konsole, Composer, Setup-Assistent, crontab:edit, cron:frequent, cronjob parts, shell access, console, setup wizard
type: help
---
# Cron und Shell-Zugang

Diese Seite ist für Support-Mitarbeitende und Administratoren bei Hosting-Anbietern, die Kunden helfen, die Jobs von Exponential
über die Cron-Seite eines Panels zu planen, und die entscheiden, wie viel Shell-Zugang ein Tarif enthält. Sie beantwortet, welche
Jobs laufen müssen und wie oft, welche PHP-Binärdatei eine Cron-Zeile nutzen muss, was die eigene Seite Cronjobs der
Administration braucht und was ein Kunde ohne Shell tun kann.

![Setup, Cache-Verwaltung: was Sie nutzen, wenn ein Hosting-Panel keine Shell bietet](../../../../en/us/help/images/hosting/cache-from-panel.webp)

## Inhalt

1. [Warum Exponential Cron braucht](#warum-exponential-cron-braucht)
2. [Die Cron-Zeilen](#die-cron-zeilen)
3. [Die richtige PHP-Binärdatei](#die-richtige-php-binärdatei)
4. [Der richtige Benutzer](#der-richtige-benutzer)
5. [Die Seite Cronjobs in der Administration](#die-seite-cronjobs-in-der-administration)
6. [Mit und ohne Shell-Zugang](#mit-und-ohne-shell-zugang)
7. [Fehlersuche](#fehlersuche)
8. [Checkliste](#checkliste)

## Warum Exponential Cron braucht

Cronjobs erledigen die Arbeit, auf die kein Besucher wartet: Sie veröffentlichen und verbergen Inhalte nach Zeitplan, versenden
Benachrichtigungen, setzen Inhaltsjobs fort, führen die Wartung des Prüfprotokolls aus, importieren Feeds, indizieren Inhalte und
räumen auf. Ohne sie funktioniert eine Website, aber geplante Inhalte erscheinen nie, Benachrichtigungen gehen nie hinaus, und
Caches und Entwürfe häufen sich auf der Festplatte ([10.3 Cronjobs](../../../installation/10-nach-der-installation.md#103-cronjobs)).

`runcronjobs.php` führt **Teile** aus, benannte Listen von Skripten in `settings/cronjob.ini`; ohne Namen eines Teils führt es den
globalen Teil aus. `php runcronjobs.php --list` zeigt jeden Teil und seine Skripte.

## Die Cron-Zeilen

Die Crontab, die Kapitel 10 für eine Website nennt, einzurichten in der Crontab des Benutzers der Website:

```text
# m   h  dom mon dow  command
*/5   *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
*/15  *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site >> var/log/cron-default.log 2>&1
17    *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site infrequent >> var/log/cron-infrequent.log 2>&1
40    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cache_cleanup >> var/log/cron-cleanup.log 2>&1
50    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site --script=session_gc.php >> var/log/cron-cleanup.log 2>&1
```

- `-s` wählt den Siteaccess, dessen Einstellungen die Skripte lesen; nutzen Sie den öffentlichen Siteaccess, es sei denn, ein Teil
  sagt etwas anderes. `-q` unterdrückt die Ausgabe außer Fehlern.
- `frequent` alle paar Minuten ist, was Benachrichtigungen und Inhaltsjobs erwarten; `infrequent` stündlich oder täglich.
- Rotieren Sie die Dateien `cron-*.log` mit logrotate des Systems.

Wenn die Cron-Seite eines Panels ein Feld pro Zeile hat, geben Sie Zeitplan und Befehl jeder Zeile getrennt ein; der Befehl ist alles
nach den fünf Zeitfeldern.

> **Hinweis**
> Die Seite Cronjobs erkennt eine Zeile dieser Installation nur, wenn sie `runcronjobs.php` und den **echten** Wurzelpfad der
> Installation enthält; ein Pfad über einen symbolischen Link zeigt den Teil als *Nicht geplant*. Verwenden Sie in der Zeile
> `cd <real root> &&` ([Cronjobs, Fehlersuche](../../../anleitungen/cronjobs.md#17-fehlerbehebung)).

## Die richtige PHP-Binärdatei

Verwenden Sie dieselbe PHP-Binärdatei, die der Webserver nutzt (`/opt/.../php` auf Hosts mit mehreren PHP-Versionen); `php -v` in
der Cron-Umgebung kann sich von dem Ihrer Shell unterscheiden. Auf Plesk ist das zum Beispiel `/opt/plesk/php/8.5/bin/php`:

```text
*/5 * * * * cd /var/www/vhosts/<domain>/<docroot> && /opt/plesk/php/8.5/bin/php runcronjobs.php -q -s site frequent >/dev/null 2>&1
```

Wenn das Panel die PHP-Version der Website ändert, aktualisieren Sie auch die Cron-Zeilen
([PHP-Auswahl und FPM-Pools](php-auswahl-und-fpm-pools.md)).

## Der richtige Benutzer

Richten Sie die Zeilen in der Crontab des Benutzers der Website ein (der, als der der Webserver läuft, damit die Dateien, die die
Jobs schreiben, ihm gehören): `crontab -e -u <site user>` oder `php bin/php/console crontab:edit` als dieser Benutzer
([Cronjobs, Planen](../../../anleitungen/cronjobs.md#12-planen-mit-der-crontab)). Die Cron-Seite eines Panels pro Konto führt Jobs
meist als Benutzer des Kontos aus, was Exponential braucht; lesen Sie in der Dokumentation Ihres Panels nach.

> **Warnung**
> Cron-Zeilen in der Crontab von root schreiben Cache- und Logdateien als root, die der Benutzer der Website danach nicht ersetzen
> kann, und die Website versagt an seltsamen Stellen ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)).
> Als root braucht jedes Skript `--allow-root-user`; besser, Sie führen sie nicht als root aus.

## Die Seite Cronjobs in der Administration

**Setup > Cronjobs** führt jeden Teil oder jedes Skript aus, folgt seiner Ausgabe, zählt Probleme, hält Teile an und schlägt
Crontab-Zeilen für die noch nicht geplanten Teile vor ([Anleitung zu Cronjobs](../../../anleitungen/cronjobs.md)). Was sie vom Server
braucht:

| Bedarf | Warum | Wenn es fehlt |
|---|---|---|
| Eine PHP-Binärdatei für die Kommandozeile: `cronjob.ini [AdminSettings] PhpCliPath`, sonst `PHP_BINDIR/php`, `/usr/bin/php`, `/usr/local/bin/php` | Jobs sind getrennte Prozesse; sie nutzt nie die Binärdatei, die die Web-Anfrage beantwortet | rote Meldung, jedes Starten deaktiviert |
| `proc_open` | um einen Job zu starten | die Seite sagt es |
| `exec` | um die Crontab zu lesen (`crontab -l`) | die Seite sagt es |
| `posix_kill` | **Laufenden Job anhalten** | die Seite sagt es |
| Richtlinie `setup/managecronjobs` | Zugang | *Zugriff verweigert* |

Setzen Sie `PhpCliPath` auf Servern mit mehreren PHP-Versionen:

```bash
php bin/php/console exp:ini set cronjob.ini/AdminSettings/PhpCliPath /opt/plesk/php/8.5/bin/php global
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Die Seite liest nur die Crontab des Benutzers, als der der Webserver läuft. Für Kunden ohne Shell sind ihre **Vorgeschlagenen
Einträge** die Zeilen, die in die Cron-Seite des Panels eingefügt werden.

Teile können aus der Seite angehalten werden, ohne die Crontab anzurühren; einen angehaltenen Teil lässt `runcronjobs.php` aus, wer
ihn auch startet ([Cronjob-Teile anhalten](../../../../en/us/features/6.0/cronjob-pause.md)). Das ist während einer Migration oder einer
Wiederherstellung nützlich.

## Mit und ohne Shell-Zugang

| Aufgabe | Mit Shell | Ohne Shell |
|---|---|---|
| Den Code und `vendor/` holen | `git clone`, `composer install` | einen anderswo vorbereiteten Baum hochladen, oder ein Installer des Hosters |
| Installieren | `exp:install`, der Kickstarter oder der Assistent | der Setup-Assistent (er braucht nur einen Browser und FTP-Zugang) |
| Updates | Composer oder ezupdate | ezupdate in der Administration, wenn Composer erreichbar ist und Updates erlaubt sind |
| Caches leeren | `php bin/php/ezcache.php --clear-all` | **Setup > Caches** (`/setup/cache`) in der Administration |
| Einen Cron-Teil einmal ausführen | `php runcronjobs.php -s site frequent` oder `php bin/php/console cron:frequent` | **Setup > Cronjobs**, wenn `proc_open` erlaubt ist |
| Wartungsmodus | `php bin/php/maintenance.php on` | **Setup > Wartung** |
| Logs lesen | `tail var/log/error.log` | **Setup > Logdateien** |
| Velocity | möglich, wenn lang laufende Prozesse erlaubt sind | nicht möglich |

Quellen: [Kapitel 4](../../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation),
[Wartungsmodus](../../../anleitungen/wartungsmodus.md), [Log-Viewer](../../../../en/us/features/6.0/log-viewer.md),
[ezupdate](../../../../en/us/features/6.0/extensions/ezupdate.md).

Shell-Zugang als eigener Benutzer der Website (nicht als root) ist die sicherste Form, die Sie anbieten können: Alles, was der
Kunde ausführt, schreibt dann Dateien, die die Website weiterhin ändern kann.

## Fehlersuche

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Geplante Inhalte erscheinen nie | keine Cron-Zeile, oder die Zeile nutzt ein PHP ohne die Erweiterungen der Website | fügen Sie die Zeilen hinzu; nutzen Sie die PHP-Binärdatei der Website |
| Von Cron geschriebene Dateien kann die Website nicht ändern | Cron läuft als anderer Benutzer | verlegen Sie die Zeilen in die Crontab des Benutzers der Website |
| *Cronjob part locked by other process* | dasselbe Skript läuft bereits | erwartet: die Sperre funktioniert |
| Ein neuer Teil erscheint nicht auf der Seite | INI-Cache | `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` |

Mehr in [Cronjobs, Fehlersuche](../../../anleitungen/cronjobs.md#17-fehlerbehebung) und [Cronjobs für Hoster](../betrieb/cronjobs.md).

## Checkliste

- [ ] `frequent`, der globale Teil und `infrequent` geplant; Bereinigungsteile nächtlich.
- [ ] Die Zeilen nutzen die PHP-Binärdatei der Website und den echten Pfad der Installation.
- [ ] Die Zeilen stehen in der Crontab des Benutzers der Website, nicht in der von root.
- [ ] `PhpCliPath` auf Servern mit mehreren PHP-Versionen gesetzt.
- [ ] Die Entscheidung zu `proc_open`, `exec`, `posix_kill` für die Seite Cronjobs ist dokumentiert.
- [ ] Die Richtlinie zum Shell-Zugang pro Tarif ist aufgeschrieben.

**Cron auf einer öffentlichen Demo.** Die Cronjobs der Demo brauchen dasselbe `EXP_DEMO_MODE=1` wie ihre Web-Anfragen: eine Zeile oben in der Crontab (oder in der Cron-Umgebung des Panels), wie in [`crontab-demo`](../../../../en/us/examples/demo-mode/crontab-demo). [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Control Panels: Überblick](README.md)
- [Cronjobs](../betrieb/cronjobs.md), [Cronjob-Konsole](../betrieb/die-cronjob-konsole.md)
- [Anleitung zu Cronjobs](../../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
