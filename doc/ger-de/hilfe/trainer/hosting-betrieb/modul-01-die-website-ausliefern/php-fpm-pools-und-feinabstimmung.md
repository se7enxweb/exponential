---
title: "Lektion 1.3: PHP-FPM-Pools und Feinabstimmung"
audience: trainer, hosting, admin
category: training
order: 13
summary: Die Teilnehmenden setzen in einem PHP-FPM-Pool die PHP-Werte, die Exponential braucht, stimmen die Zeitlimits aufeinander ab, prüfen APCu und finden und laden den Master neu, der die Website wirklich bedient. 45 Minuten.
keywords: hosting und betrieb, PHP-FPM, pool, memory_limit, max_execution_time, request_terminate_timeout, APCu, ApcuReserve, neu laden, PhpFpmService, PhpFpmReload, bemessung, hosting and operations, reload, sizing
type: help
---
# Lektion 1.3: PHP-FPM-Pools und Feinabstimmung

Unter Apache oder nginx ist der PHP-FPM-Pool der Ort, an dem ein Hoster jede Website isoliert und abstimmt. Diese
Lektion behandelt die PHP-Werte, die Exponential braucht, die Zeitlimits, die zueinander passen müssen, APCu und den
häufigsten Betriebsfehler: einen PHP-FPM-Master neu zu laden, der die Website gar nicht bedient.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 1: Die Website ausliefern](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 1.2](apache-und-nginx-mit-php-fpm.md); root auf der eigenen virtuellen Maschine |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `memory_limit`, `max_execution_time`, die Upload-Grenzen und `date.timezone` für einen Pool setzen;
- erklären, warum `request_terminate_timeout` unter PHP-FPM das eigentliche Zeitlimit ist;
- APCu prüfen und erklären, warum `ApcuReserve` wichtig ist, wenn sich Websites einen Master teilen;
- den Master finden, der eine Website bedient, und das Neuladen einer Bereitstellung mit `--dry-run` vorab ansehen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Feinabstimmung von PHP-FPM](../../../hosting/leistung/abstimmung-von-php-fpm.md) und
[Kapitel 8, PHP-FPM](../../../../installation/08-die-website-ausliefern.md#852-php-fpm).

1. **Ein Pool pro Website** (3 Minuten). Ein Pool führt PHP als Benutzer der Website aus; er ist die Einheit für
   Isolation und Abstimmung. Panels wie Plesk legen einen Pool pro Domain an.
2. **PHP-Werte** (5 Minuten). Zeigen Sie die Tabelle der Seite zur Feinabstimmung: `memory_limit` im Produktivbetrieb
   mindestens 256M, `max_execution_time` mindestens 30 (60 im Produktivbeispiel aus Kapitel 2), `date.timezone`,
   `file_uploads=On`, Upload-Grenzen für den größten Upload des Kunden, `allow_url_fopen=On`, `variables_order` mit
   `E`, leeres `open_basedir`, OPcache an. Das Web-PHP und das PHP der Kommandozeile lesen unterschiedliche Dateien.
3. **Zeitlimits** (4 Minuten). `max_execution_time` ist der eigene Zeitgeber des Skripts; `request_terminate_timeout`
   setzt der Master durch, egal was das Skript tut. Halten Sie auf SQLite `SQLiteTransactionWait` (standardmäßig
   25 s) unter dem Zeitlimit der Anfrage. Lange Aufträge gehören auf die Kommandozeile.
4. **APCu** (3 Minuten). APCu ist ein Speicher für alle Websites des Pools; wenn er voll ist, leert er sich vollständig.
   `[QueryCacheSettings] ApcuReserve=25` hält ein Viertel frei; lassen Sie den Wert über 0.
5. **Neu laden** (5 Minuten). Ein Neuladen beschränkt sich nicht auf eine Website: Der Master wartet auf jedes Kind
   jedes Pools. `exp:velocity deploy` lädt nur neu, wenn es muss (`[DeploySettings] PhpFpmReload=auto`), und
   `PhpFpmService=auto` findet den richtigen Master. Zeigen Sie `deploy --no-velocity --dry-run`.

### Häufige Fragen

**Wie viele Kinder sollte ein Pool haben?** Die Quellen nennen keine Zahl. Sie nennen eine Methode: Messen Sie mit
`exp:benchmark --cold` bei steigender Parallelität und wählen Sie den Punkt, an dem p90 zu steigen beginnt (Modul 7).

**Ich habe `php.ini` geändert, und nichts ist passiert.** Meist ging die Änderung in die Datei, die das andere PHP
liest (CLI gegenüber FPM), oder der Master, der die Website bedient, wurde nicht neu geladen.

**Warum sind Sitzungen hier wichtig?** Der rollenbewusste HTTP-Cache braucht Sitzungen in Dateien, die jeder Server der
Website lesen kann; anderswo abgelegt, speichert er für angemeldete Besucher nichts.

**Wann muss ich ein Neuladen erzwingen?** Nach einer Änderung an `php.ini` oder am Pool, nach einer neuen
PHP-Erweiterung oder wenn PHP-Dateien an Ort und Stelle entfernt oder umbenannt wurden (`deploy --fpm-reload`).

### Stolperfallen

- **Mehrere PHP-Versionen.** Der Master, der die Website bedient, ist oft nicht der, der `php-fpm` heißt. Finden Sie
  ihn wie in Schritt 6 über seine Pool-Datei.
- **Teilnehmende ohne root.** Der Schritt zum Neuladen braucht root; als jemand anderes ausgeführt, wird er mit einem
  Hinweis übersprungen. Achten Sie darauf, dass die Teilnehmenden ihre eigene virtuelle Maschine verwenden.
- **Die falsche `php.ini` bearbeiten.** Lassen Sie die Teilnehmenden `php --ini` ausgeben und mit den Zeilen
  `php_admin_value` des Pools vergleichen.

## Praktische Übung

Verwenden Sie Ihre eigene virtuelle Linux-Maschine aus Lektion 1.2.

1. Öffnen Sie die Pool-Datei Ihrer Website (zum Beispiel unter `/etc/php-fpm.d/` oder
   `/etc/php/<version>/fpm/pool.d/`).
2. Notieren Sie `user`, `listen` und etwaige Zeilen `php_admin_value` des Pools.
3. Fügen Sie dem Pool diese Zeilen hinzu oder prüfen Sie sie:

   ```ini
   php_admin_value[memory_limit] = 256M
   php_admin_value[max_execution_time] = 60
   php_admin_value[date.timezone] = Europe/Berlin
   php_admin_value[upload_max_filesize] = 64M
   php_admin_value[post_max_size] = 64M
   ```

4. Suchen Sie im Pool `request_terminate_timeout` und notieren Sie seinen Wert oder dass er nicht gesetzt ist.
5. Laden Sie den PHP-FPM-Dienst dieses Pools mit `systemctl reload <service>` neu.
6. Sehen Sie sich im Wurzelverzeichnis der Installation eine Bereitstellung vorab an und lesen Sie, welchen Master sie
   gefunden hat und ob sie neu laden oder warten würde:

   ```bash
   php bin/php/console exp:velocity deploy --no-velocity --dry-run --allow-root-user
   ```

7. Melden Sie sich in der Administration Ihrer Schulungs-Website an und öffnen Sie **Setup**, dann
   **Systeminformationen**. Prüfen Sie im Abschnitt **PHP extensions and settings** `memory_limit`; prüfen Sie unter
   **OPcache and APCu**, dass APCu aufgeführt ist.
8. Führen Sie `php -m | grep -i apcu` aus und vergleichen Sie das PHP der Kommandozeile mit dem, was die Webseite
   gezeigt hat.
9. Notieren Sie einen Unterschied zwischen den Einstellungen von CLI und Pool, falls es einen gibt.

## Erwartetes Ergebnis

- Nach dem Neuladen zeigt **Systeminformationen** für das Web-PHP `memory_limit` 256M.
- Die Ausgabe von `--dry-run` listet jeden Bereitstellungsschritt als `DRY` auf und nennt den gefundenen PHP-FPM-Dienst
  oder erklärt, warum sie das Neuladen überspringen würde.
- Die Person kann sagen, ob sich das CLI-PHP und der Pool unterscheiden und warum das wichtig ist.
- Der Trainer prüft das, indem er jede Person fragt, welchen Dienstnamen der Probelauf gefunden hat.

## Quiz

1. Welches Limit ist unter PHP-FPM das eigentliche Zeitlimit einer Anfrage?
   A. `max_execution_time` B. `request_terminate_timeout` C. `memory_limit` D. `post_max_size`
2. Was geschieht, wenn ein gemeinsam genutzter APCu voll ist?
3. Warum kann ein Neuladen von PHP-FPM die Websites anderer Kunden verlangsamen?
4. Was bewirkt `[DeploySettings] PhpFpmService=auto`?
5. Wohin gehören lang laufende Importe und das Vorwärmen von Caches, und warum?

<details>
<summary>Antworten</summary>

1. **B.** Der Master setzt es durch, egal was das Skript tut ([Feinabstimmung von PHP-FPM](../../../hosting/leistung/abstimmung-von-php-fpm.md#zeitlimits-die-zusammenpassen-müssen)).
2. Er leert sich vollständig, und jeder andere Cache und Zähler darin geht verloren; `ApcuReserve` hält Platz frei.
3. Der Master bittet jedes Kind jedes Pools, seine Anfrage zu beenden, und startet kein neues Kind, bevor das letzte
   fertig ist.
4. Es findet den Master, der die Website bedient, über seine Pool-Datei (Benennung von Plesk, der Distribution und
   Remi).
5. Auf die Kommandozeile, wo das PHP-CLI standardmäßig kein Ausführungslimit hat; die Web-Zeitlimits bleiben für
   Seiten bemessen.

</details>

## Weiterführende Informationen

- [Feinabstimmung von PHP-FPM](../../../hosting/leistung/abstimmung-von-php-fpm.md): jeder Wert und die Methode zur
  Bemessung.
- [PHP-Einstellungen für Exponential](../../../hosting/grundlagen/php-einstellungen-fuer-exponential.md): die Referenz zu
  php.ini.
- [PHP-Auswahl und FPM-Pools](../../../hosting/control-panels/php-auswahl-und-fpm-pools.md): Pools in Hosting-Panels.
- [Benutzer und Pools pro Kunde](../../../hosting/sicherheit/benutzer-und-pools-pro-kunde.md): Isolation zwischen
  Kunden.

## Verwandte Seiten

- [Modul 1: Die Website ausliefern](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
