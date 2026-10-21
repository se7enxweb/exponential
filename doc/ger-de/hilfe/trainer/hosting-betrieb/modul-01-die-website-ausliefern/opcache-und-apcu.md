---
title: "Lektion 1.4: OPcache und APCu"
audience: trainer, hosting, admin
category: training
order: 14
summary: Die Teilnehmenden konfigurieren OPcache für PHP-FPM, erklären, warum Velocity andere OPcache-Optionen mitliefert, und lesen das Profil des Opcode-Caches, um zu sehen, ob Dateien wirklich zwischengespeichert werden. 35 Minuten.
keywords: hosting und betrieb, OPcache, validate_timestamps, revalidate_freq, file_update_protection, JIT, APCu, profil des opcode-caches, Velocity, PHP-FPM, hosting and operations, opcode cache profile
type: help
---
# Lektion 1.4: OPcache und APCu

Der Opcode-Cache ist der größte einzelne Leistungsgewinn für PHP, und eine falsche Einstellung beschädigt eine
Exponential-Website auf schwer zu diagnostizierende Weise. Diese Lektion vermittelt die sicheren Einstellungen unter
PHP-FPM, warum die von Velocity anders sind und wie man mit dem Profil des Opcode-Caches nachweist, dass der Cache
funktioniert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 1: Die Website ausliefern](README.md) |
| Dauer | 35 Minuten (15 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 1.3](php-fpm-pools-und-feinabstimmung.md); eine Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die OPcache-Einstellungen schreiben, die Kapitel 10 für einen PHP-FPM-Pool empfiehlt;
- erklären, warum `opcache.validate_timestamps=0` auf einer Exponential-Website nie verwendet werden darf;
- erklären, warum Velocity `revalidate_freq=0` und `file_update_protection=0` mitliefert;
- das Profil des Opcode-Caches einschalten, `included_not_cached` lesen und es wieder ausschalten.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [OPcache](../../../hosting/leistung/opcache.md) und
[Velocity und der Opcode-Cache](../../../../funktionen/6.0/velocity-opcode-cache-und-profil.md).

1. **Warum es wichtig ist** (2 Minuten). Die in `settings/velocity.ini` festgehaltene Messung: Eine Inhaltsseite
   brauchte ohne Opcode-Cache 526,6 ms und mit OPcache 390,8 ms. Der Tracing-JIT war langsamer (416,7 ms) und fehlt
   absichtlich.
2. **Unter PHP-FPM** (4 Minuten). Zeigen Sie den Einstellungsblock: `opcache.enable=1`, `memory_consumption=256`,
   `max_accelerated_files=20000`, `interned_strings_buffer=32`, `validate_timestamps=1`, `revalidate_freq=2`.
   Exponential schreibt PHP-Cache-Dateien (INI-Caches, kompilierte Templates) unter demselben Namen neu; mit
   ausgeschalteter Zeitstempelprüfung wird eine neu geschriebene Datei in ihrer alten Form ausgeliefert, bis PHP neu
   startet.
3. **Unter Velocity** (5 Minuten). OPcache vergleicht die Zeit einer Datei mit der Startzeit der Anfrage, und ein
   Velocity-Worker ist eine lange Anfrage. Mit den PHP-Standardwerten hat ein Worker eine Datei nie wieder geprüft, und
   jede nach dem Start des Workers geschriebene Datei galt als zu neu und wurde bei jedem Include neu kompiliert.
   Velocity übergibt deshalb eigene Optionen (`[PHPSettings] IniOptions[]`). Nichts muss geändert werden; ein Neustart
   wendet sie an.
4. **Das Profil** (4 Minuten). `touch var/tmp/opcache_profile.on` lässt jede Anfrage eine JSON-Zeile an
   `var/tmp/opcache_profile.log` anhängen. Lesen Sie `included_not_cached`: Nahe 0 bei späteren Anfragen ist gesund.

### Häufige Fragen

**Sollte ich JIT einschalten?** Nur nachdem Sie die Website des Kunden mit und ohne gemessen haben; bei der Last des
Projekts war er langsamer.

**Wo setze ich OPcache zurück?** **Setup**, **Cache-Verwaltung** (die Seite **Caches**) hat **OPcache zurücksetzen**
und **APCu leeren** für den Serverprozess, der die Seite beantwortet hat; ein anderer Server behält seinen eigenen
Cache.

**Liest FrankenPHP meine `php.ini`?** Nein. Es bekommt die OPcache-Einstellungen aus
`[FrankenPHPSettings] IniOptions[]` und `[PHPSettings] IniOptions[]`.

**Was ist ein guter erster Verdacht, wenn etwas unter PHP-FPM funktioniert, aber nicht unter Velocity?** Jede
PHP-Einstellung, die mit der Anfragezeit vergleicht; sie verhält sich in einem Velocity-Worker wie eingefroren.

### Stolperfallen

- **Das Profil eingeschaltet lassen.** Das Log wächst mit jeder Anfrage. Beenden Sie die Übung, indem Sie die Datei
  `.on` aus `var/tmp/` verschieben.
- **Die erste Anfrage lesen.** Die erste Anfrage eines Prozesses kompiliert alles; lesen Sie die zweite und spätere
  Zeilen.
- **Den falschen Prozess zurücksetzen.** Die Schaltflächen der Administration wirken auf den Prozess, der geantwortet
  hat; laufen Apache und Velocity nebeneinander, hat jeder seinen eigenen Cache.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert von Velocity oder von Apache mit PHP-FPM.

1. Listen Sie im Wurzelverzeichnis der Installation die mitgelieferten Optionen von Velocity auf:

   ```bash
   grep -n "opcache\." settings/velocity.ini
   ```

2. Suchen Sie `revalidate_freq` und `file_update_protection` in der Ausgabe und notieren Sie ihre Werte.
3. Wenn Ihre Website unter PHP-FPM läuft, öffnen Sie die `php.ini` des Pools und vergleichen Sie ihre OPcache-Zeilen
   mit dem PHP-FPM-Block aus [OPcache, Unter PHP-FPM](../../../hosting/leistung/opcache.md#unter-php-fpm).
4. Zeigen Sie den Cache-Status des Kommandozeilenprozesses:

   ```bash
   php bin/php/cache.php opcache status --allow-root-user
   ```

5. Schalten Sie das Profil ein: `touch var/tmp/opcache_profile.on`.
6. Fragen Sie die Startseite dreimal mit `curl -s -o /dev/null <your site address>` an.
7. Lesen Sie die letzten Zeilen: `tail -n 3 var/tmp/opcache_profile.log`. Suchen Sie in jeder Zeile
   `included_not_cached`.
8. Schalten Sie das Profil aus, indem Sie die Markierung aus `var/tmp/` verschieben, zum Beispiel
   `mv var/tmp/opcache_profile.on var/opcache_profile.off`.
9. Fragen Sie die Seite noch einmal an und prüfen Sie, dass das Log nicht gewachsen ist.

## Erwartetes Ergebnis

- Schritt 2: `revalidate_freq` ist `0` und `file_update_protection` ist `0` in den mitgelieferten Optionen von
  Velocity.
- Schritt 7: Die erste Zeile zeigt möglicherweise viele nicht zwischengespeicherte Dateien; spätere Zeilen zeigen einen
  Wert nahe 0.
- Schritt 9: keine neue Zeile in `var/tmp/opcache_profile.log`.
- Der Trainer prüft das, indem er nach dem Wert `included_not_cached` der dritten Anfrage fragt.

## Quiz

1. Warum muss `opcache.validate_timestamps` für eine Exponential-Website eingeschaltet bleiben?
2. Was hat die festgehaltene Messung für den Tracing-JIT gezeigt?
   A. 25 % schneller B. langsamer als OPcache allein C. gleich D. er wurde nicht gemessen
3. Warum braucht ein Velocity-Worker `revalidate_freq=0`?
4. Welcher Wert im Profil des Opcode-Caches zeigt, ob Dateien bei jedem Include neu kompiliert werden?
5. Richtig oder falsch: **OPcache zurücksetzen** in der Administration setzt den Cache jedes Serverprozesses auf dem
   Rechner zurück.

<details>
<summary>Antworten</summary>

1. Exponential schreibt PHP-Cache-Dateien unter demselben Namen neu; ohne Zeitstempelprüfung wird die alte Form
   ausgeliefert, bis PHP neu startet ([OPcache](../../../hosting/leistung/opcache.md#unter-php-fpm)).
2. **B.** 416,7 ms gegenüber 390,8 ms für OPcache allein.
3. Ein Worker ist eine lange Anfrage, die mit dem Worker begonnen hat; mit dem Standardwert prüft er eine Datei nie
   wieder.
4. `included_not_cached`.
5. **Falsch.** Es wirkt auf den Prozess, der die Seite beantwortet hat; der andere Server behält seinen eigenen Cache.

</details>

## Weiterführende Informationen

- [OPcache](../../../hosting/leistung/opcache.md): jede Einstellung für jeden Server.
- [Velocity und der Opcode-Cache](../../../../funktionen/6.0/velocity-opcode-cache-und-profil.md): die Messungen und das
  Profil.
- [Cache-Schichten](../../../hosting/leistung/cache-schichten.md): wie der Opcode-Cache zu den anderen Caches passt.
- [Caches](../../../../anleitungen/caches.md): die Cache-Seite der Administration.

## Verwandte Seiten

- [Modul 1: Die Website ausliefern](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
