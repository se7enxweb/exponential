---
title: Speicher, CPU und Festplatte dimensionieren
audience: hosting, admin
category: hosting
order: 170
summary: Die Zahlen zu Arbeitsspeicher, CPU und Festplatte, die das Exponential-Repository tatsächlich nennt (PHP-Speichergrenzen, Velocity-Worker, OPcache, APCu, SQLite-Seitencache, MongoDB, Schwellen für die Festplatte, Log-Rotation), was auf der Festplatte wächst und wie Sie eine echte Website messen, statt zu raten.
keywords: Dimensionierung, Kapazitätsplanung, Arbeitsspeicher, RAM, CPU, Festplatte, memory_limit, Velocity-Worker, Workers, SpareWorkers, PSS, RSS, OPcache, APCu, apc.shm_size, SQLite cache_size, MongoDB, var/storage, var/cache, var/log, LogFileMaxSize, exp:benchmark, Systeminformationen, Hosting-Tarife, sizing, capacity planning, memory, disk, System information, hosting plans
type: help
---
# Speicher, CPU und Festplatte dimensionieren

Diese Seite ist für Systemadministratoren und Produktmanager bei einem Hosting-Anbieter, die Server und Tarife für
Exponential-Websites dimensionieren. Sie beantwortet: welche Zahlen die Dokumentation und die Einstellungen des Projekts
tatsächlich nennen, was auf einer laufenden Website Arbeitsspeicher, CPU und Festplatte verbraucht und wie Sie eine echte
Website messen. Sie nennt keine Zahlen, die das Repository nicht nennt; [Kapitel 2.10](../../../installation/02-voraussetzungen.md#210-arbeitsspeicher-cpu-und-festplatte)
sagt dasselbe: Die Dokumentation nennt wenige harte Zahlen, und das Buch erfindet keine weiteren.

## Inhalt

1. [Die Zahlen, die das Repository nennt](#die-zahlen-die-das-repository-nennt)
2. [Arbeitsspeicher](#arbeitsspeicher)
3. [CPU](#cpu)
4. [Festplatte](#festplatte)
5. [Messen, nicht raten](#messen-nicht-raten)
6. [Aus Messungen Tarife machen](#aus-messungen-tarife-machen)
7. [Checkliste](#checkliste)
8. [Verwandte Seiten](#verwandte-seiten)

## Die Zahlen, die das Repository nennt

| Element | Wert | Quelle |
|---|---|---|
| PHP `memory_limit`, Untergrenze des Installers | 64M | `settings/setup.ini`, `[memory_limit] MinMemoryLimit` |
| PHP `memory_limit`, Zustandsprüfung | unter 128M ein Fehler, unter 256M eine Warnung | [Systeminformationen](../../../anleitungen/systeminformationen.md#3-zustandsprüfungen) |
| PHP `memory_limit` unter FrankenPHP | 512M mitgeliefert | `settings/velocity.ini`, `[FrankenPHPSettings] IniOptions[]` |
| Ein Velocity-Worker | etwa 10 MB privater Speicher (gemessen) | `settings/velocity.ini`; [Velocity-Webserver](../../../funktionen/6.0/velocity-webserver.md) |
| Velocity-Pool | `Workers=4` mitgeliefert; der eigene Standard der Engine ist, was in den RAM passt, höchstens 8 pro Kern und 64, nie weniger als 4 | `settings/velocity.ini`, [Kapitel 2.10](../../../installation/02-voraussetzungen.md#210-arbeitsspeicher-cpu-und-festplatte) |
| OPcache | `memory_consumption=256`, `max_accelerated_files=20000`, `interned_strings_buffer=32` | Kapitel 10.12 und `velocity.ini [PHPSettings]` |
| APCu für Velocity | `apc.shm_size=256M` (reserviert, nicht genutzt, bis er sich füllt) | `velocity.ini [PHPSettings]` |
| SQLite-Seitencache | bis zu 64 MB pro Prozess, der die Datenbank geöffnet hat; 256 MB Memory Map | [Datenbankunterstützung](../../datenbankunterstuetzung.md#performance-und-skalierung) |
| SQLite `VACUUM` | etwa die doppelte Größe der Datenbank als freier Speicherplatz | [Kapitel 9.2](../../../installation/09-datenbanken.md#analyze-vacuum-und-die-wal-datei) |
| Eine MongoDB-Installation | RAM mindestens 512 MB, empfohlen 2 GB; Festplatte mindestens 2 GB, empfohlen 10 GB | [Referenz zur Portierung](../../../bc/6.0/mongodb-kernel-unterstuetzung.md), Abschnitt 24.1 |
| Freier Speicherplatz, Zustandsprüfung | unter 1 GB oder 5 % ein Fehler; unter 5 GB oder 10 % eine Warnung | [Systeminformationen](../../../anleitungen/systeminformationen.md#3-zustandsprüfungen) |
| Log-Rotation | jedes Log wird bei `LogFileMaxSize=2097152` (2 MB) rotiert, `LogFileRotateCount=3` werden behalten | `settings/site.ini`, `[FileSettings]` |
| Ein Kernel-Klon mit Historie | etwa 370 MB | [Kapitel 3.2](../../../installation/03-den-code-beschaffen.md#a-git-clone-und-composer-install-empfohlen) |

## Arbeitsspeicher

Was auf einem Server mit Exponential-Websites Arbeitsspeicher verbraucht:

- **PHP-Prozesse.** Jedes PHP-FPM-Kind oder jeder Velocity-Worker in der Spitze bis zu seinem `memory_limit`;
  Paketinstallationen, Importe und Bildarbeit brauchen am meisten. Planen Sie die Pool-Größen so, dass der
  geschäftigste Moment hineinpasst ([Abstimmung von PHP-FPM](../leistung/abstimmung-von-php-fpm.md)).
- **Velocity-Worker** halten ab dem Moment, in dem sie geforkt werden, je etwa 10 MB privaten Speicher, ob untätig oder
  nicht. Mit `SpareWorkers` hält der Pool in ruhigen Zeiten nur so viele und wächst unter Last bis `Workers`, wobei er
  untätige Worker nach `IdleWorkerTimeout` Sekunden beendet.
- **Gemeinsame Caches.** OPcache (`opcache.memory_consumption`) und APCu (`apc.shm_size`) reservieren gemeinsamen
  Speicher. PHP-FPM nimmt sie aus seiner `php.ini`; Velocity setzt seine eigenen in `velocity.ini [PHPSettings] IniOptions[]`,
  weil der Standard der Kommandozeile für APCu 32 MB beträgt, zu wenig für seinen Antwort-Cache und den
  SQL-Abfrage-Cache zusammen.
- **Die Datenbank.** Der Seitencache von SQLite zählt pro Prozess, der sie genutzt hat: `SQLitePragmas[]=cache_size=-16384`
  senkt ihn auf 16 MB, was auf Servern mit vielen kleinen SQLite-Websites hilft. Datenbankserver brauchen ihre eigene
  Dimensionierung.
- **Optionale Dienste.** Solr, Valkey oder Redis, wenn ein Tarif sie enthält.

> **Hinweis**
> Lesen Sie den Speicher von Velocity so, wie sein Kontrollzentrum ihn zeigt: als Proportional Set Size (PSS) des
> Elternprozesses und seiner Worker. Die Resident Set Size (RSS) jedes geforkten Workers zählt den gemeinsam genutzten
> Speicher immer wieder mit und summiert sich zu weit mehr, als die Maschine tatsächlich nutzt
> ([Kapitel 2.10](../../../installation/02-voraussetzungen.md#210-arbeitsspeicher-cpu-und-festplatte)).

## CPU

- **Zwischengespeicherte Seiten kosten wenig.** Der Antwort-Cache von Velocity beantwortet anonyme Besucher im
  Serverprozess ohne Worker. `settings/velocity.ini` hält eine Messung fest: Ein Server beantwortet zwischengespeicherte
  Seiten mit etwa 0,3 ms CPU je Seite, etwa 3.500 pro Sekunde auf einer Maschine mit 12 Kernen; mit `Instances` über 1
  verteilt der Kernel Verbindungen auf mehrere Server (zwei gemessen 6.600 bis 6.900, vier 12.200 bis 13.100).
  Gerenderte Seiten sind durch die Worker begrenzt, nicht dadurch.
- **Gerenderte Seiten** (erste Aufrufe nach dem Leeren, angemeldete Redakteure, Suche) verbrauchen die CPU. Lassen Sie
  den Content-View-Cache und den Template-Block-Cache an (beide mitgeliefert), und lassen Sie den statischen Cache aus
  ([Kapitel 10.12](../../../installation/10-nach-der-installation.md#1012-performance-tuning)).
- **OPcache zählt.** Unter Velocity ließ `opcache.file_update_protection` über 0 ein Rendering etwa das Vierfache der
  CPU kosten, die es unter PHP-FPM braucht; die mitgelieferte `velocity.ini` setzt es auf 0
  ([Opcode-Cache von Velocity](../../../funktionen/6.0/velocity-opcode-cache-und-profil.md)).
- **FrankenPHP**-Threads sind standardmäßig doppelt so viele wie CPU-Kerne, wenn `[FrankenPHPSettings] Workers` leer ist.
- **Cron und Bildarbeit** bringen Lastspitzen: Bildvarianten werden bei der ersten Verwendung erzeugt, und die Cronjobs
  laufen nach Zeitplan.

## Festplatte

| Was wächst | Wo | Wie Sie es im Griff behalten |
|---|---|---|
| Hochgeladene Dateien und Bildvarianten | `var/storage` (pro Website `var/<site>/storage`) | wächst mit den Inhalten; der Hauptfaktor für die Festplatte eines Tarifs |
| Caches | `var/cache` (pro Website `var/<site>/cache`) | werden geleert und neu aufgebaut; können auf einem Dateisystem im Arbeitsspeicher liegen ([Pfade bei mehreren Websites](../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md)) |
| Logs | `var/log`, `var/vc/` für Velocity | Rotation (`LogFileMaxSize`, `LogFileRotateCount`, `LogFileCompress`), dazu logrotate des Systems für Cron-Logs |
| Die SQLite-Datenbank | `var/storage/sqlite3/` | Platz für die doppelte Größe der Datenbank, für `VACUUM` und Backups |
| Backups | idealerweise außerhalb der Maschine | nie nur auf derselben Festplatte |
| Code | die Installation, `vendor/`, Klone von Erweiterungen | Git-Klone mit Historie brauchen mehr als Archive |

Die Festplattenprüfung der Administration (**Setup > Systeminformationen**) schlägt unter 1 GB oder 5 % frei fehl und
warnt unter 5 GB oder 10 %; nutzen Sie dieselben Schwellen in Ihrer Überwachung. Siehe auch das
[Runbook bei voller Festplatte](../runbooks/festplatte-voll.md).

## Messen, nicht raten

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
php bin/php/console exp:benchmark kernel --repeat=30 --allow-root-user
php bin/php/console exp:system:info --json --allow-root-user
du -sh var/storage var/cache var/log
```

`exp:benchmark` ruft die Startseite, Menüseiten, die Suchseite und die Anmeldeseite der Administration ab und gibt
Median, p90 und p99 aus; gegenüber anderen Hosts ist es standardmäßig zurückhaltend ([Benchmarking](../../../anleitungen/benchmarking.md)).
`exp:system:info` gibt die Fakten und Zustandsprüfungen von **Setup > Systeminformationen** aus (Speichergrenze, OPcache
und APCu, Festplatte, Arbeitsspeicher und Last der Maschine). Das [Kontrollzentrum](../../../funktionen/6.0/velocity-control-panel.md)
von Velocity zeigt den Speicher des Pools.

> **Tipp**
> Bauen Sie eine Referenz-Website nach Ihrem häufigsten Tarif, messen Sie sie mit diesen Befehlen auf Ihrer Hardware, und
> dimensionieren Sie danach. Eine auf Ihren eigenen Servern gemessene Zahl ist mehr wert als jede Faustregel.

## Aus Messungen Tarife machen

Das Repository nennt keine Tarifgrößen und keine Preise; dies sind die Dimensionen, für die ein Hoster Grenzen festlegen
kann, jede mit den Befehlen oben messbar:

| Dimension | Gemessen mit |
|---|---|
| Festplatte für `var/storage` | `du -sh var/storage` |
| PHP-Speicher pro Prozess | `memory_limit` und die Spitze des Pools |
| Anzahl der PHP-Worker | die Pool-Größe oder `Workers` von Velocity |
| Datenbankgröße | **Setup > Systeminformationen**, die Karte Datenbank |
| Upload-Größe | `upload_max_filesize`, `post_max_size` |

## Checkliste

- [ ] `memory_limit` 256M oder mehr für Produktions-Pools.
- [ ] `Workers` von Velocity ausdrücklich gesetzt; `SpareWorkers` auf ausgelasteten Servern mit mehreren Websites erwogen.
- [ ] Größen von OPcache und APCu für PHP-FPM und für Velocity getrennt gesetzt.
- [ ] Speicher von Velocity als PSS gelesen.
- [ ] Festplattenüberwachung mit den Schwellen der Systeminformationen.
- [ ] Eine Referenz-Website auf Ihrer eigenen Hardware gemessen.

## Verwandte Seiten

- [Anforderungen auf einen Blick](anforderungen-auf-einen-blick.md)
- [Cache-Schichten](../leistung/cache-schichten.md) und [Benchmark einer gehosteten Website](../leistung/benchmark-einer-gehosteten-website.md)
- [Velocity-Worker und Pools](../server/velocity-worker-und-pools.md)
- [Hosting-Tarife gestalten](../geschaeft/hosting-tarife-gestalten.md)
- [Kapitel 2.10: Arbeitsspeicher, CPU und Festplatte](../../../installation/02-voraussetzungen.md#210-arbeitsspeicher-cpu-und-festplatte)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
