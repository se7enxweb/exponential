---
title: "Lektion 2.1: Was Velocity ist und ein erster Start"
audience: trainer, hosting, admin
category: training
order: 21
summary: Die Teilnehmenden erklären, wie Velocity eine Website mit einem Elternprozess und persistenten Workern ausliefert, wählen die Engine qbix, ändern Einstellungen mit exp:velocity config und starten und prüfen den Server. 45 Minuten.
keywords: hosting und betrieb, Velocity, qbix, engine, exp:velocity, status, start, config set, velocity.ini, persistente worker, elternprozess, ports 8088 8080, hosting and operations, persistent workers, parent process
type: help
---
# Lektion 2.1: Was Velocity ist und ein erster Start

Exponential Velocity ist der Anwendungsserver, der mit Exponential ausgeliefert wird, und in jeder Phase der empfohlene
Weg, eine Website auszuliefern. Er braucht keinen separaten Webserver und keinen TLS-Terminator. Diese Lektion erklärt
das Modell in einfachen Worten und führt alle Teilnehmenden durch einen ersten Start auf ihrer eigenen Installation.

> **Hinweis**
> Die Teilnehmenden starten und stoppen Velocity nur auf ihrer eigenen Schulungsinstallation. Üben Sie nie auf einem
> Server, der andere Websites hostet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 2: Velocity](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Modul 1](../modul-01-die-website-ausliefern/README.md); eine Schulungsinstallation mit PHP 8.0 oder neuer und den Erweiterungen `sockets`, `pcntl`, `posix` und `openssl` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Elternprozess, die Worker und das beschreiben, was der Elternprozess ohne Worker beantwortet;
- die drei Engines nennen, die `exp:velocity` steuert, und welche davon echte Websites ausliefert;
- eine Einstellung mit `exp:velocity config` lesen und ändern, ohne `settings/velocity.ini` zu bearbeiten;
- den Server starten, `exp:velocity status` lesen und die Website mit `curl` prüfen;
- die Dateien nennen, die sagen, warum ein erster Start fehlgeschlagen ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Kapitel 8, Abschnitt 8.3](../../../../installation/08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg)
und [Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md).

1. **Das Modell** (5 Minuten). Zeichnen Sie das Diagramm aus Abschnitt 8.3: Ein Elternprozess lädt die Anwendung einmal
   und forkt Worker, die eine Anfrage nach der anderen beantworten; zwischen den Anfragen wird jeder Worker
   zurückgesetzt. Der Elternprozess nimmt jede Verbindung an, beantwortet statische Dateien, Treffer im Antwort-Cache
   und seine eigenen `/Q/`-Seiten und terminiert TLS.
2. **Was sich für den Betrieb ändert** (4 Minuten). Verwenden Sie die Tabelle in *Velocity für Hoster*: ein lang
   laufender Server pro Website; eine geänderte Klasse oder INI-Datei erreicht die Worker erst nach einem Neustart;
   Speicher wird nach PSS beurteilt, nie nach RSS.
3. **Engines** (3 Minuten). `qbix` (der eigene Server von Velocity, empfohlen), `frankenphp` (produktionsreife
   Alternative), `php` (der eingebaute Entwicklungsserver von PHP, nur deshalb der ausgelieferte Standard, weil er immer
   funktioniert). Mitgelieferte Ports: `qbix` 8088 und 8080, `frankenphp` 8089 und 8444, `php` 8087.
4. **Einstellungen** (4 Minuten). `settings/velocity.ini` enthält die kommentierten Standardwerte und wird nie
   bearbeitet; `exp:velocity config set` schreibt `settings/override/velocity.ini.append.php`; der
   Konfigurationsbaum wird bei jedem Start aus beiden erzeugt. Eine geänderte Einstellung erreicht den Server beim
   nächsten `restart` oder `graceful`.
5. **Erster Start** (4 Minuten). Führen Sie am Beamer `start` und `status` aus. Weisen Sie darauf hin, dass `status`
   jede Adresse als vollständige URL auflistet und nur dann `running` sagt, wenn der Server eine Anfrage beantwortet.

### Häufige Fragen

**Brauche ich Apache davor?** Nein. Velocity liefert HTTP und HTTPS selbst aus. Ein Proxy davor ist weiterhin richtig,
wenn ein Load Balancer oder CDN TLS terminiert (Abschnitt 8.8).

**Ist das Engine-Paket schon installiert?** Ja: Die `composer.json` von Exponential verlangt es mit `~0.0.4.54`, dem
ersten Release, das auf PHP 8.0 läuft. Davor brauchte es PHP 8.1 und wurde nur vorgeschlagen; eine ältere Installation
fügt es mit `composer require se7enxweb/exponential-velocity:~0.0.4.54` hinzu.

**Was macht `ext check`?** Es listet die PHP-Erweiterungen auf, die diesem PHP fehlen, mit dem Installationsbefehl für
das Betriebssystem (`ext install-hint <extension>`).

**Können zwei Engines gleichzeitig laufen?** Ja, jede hat ihren eigenen Port, ihre eigene PID-Datei und eigene Logs;
eine Website wird von einer ausgeliefert.

### Stolperfallen

- **Die Website ist von einem anderen Rechner aus nicht erreichbar.** `Host` ist noch `127.0.0.1`. Das ist der sichere
  Standard; testen Sie im Kurs mit `curl` auf dem Rechner selbst oder über einen SSH-Tunnel.
- **Port schon belegt.** Eine andere Engine oder ein anderes Programm hält ihn. `exp:velocity status --all` zeigt jede
  Engine.
- **Eine Wartungsseite nach der Installation.** Ein Installationslauf hat den Wartungsmodus eingeschaltet gelassen:
  `php bin/php/maintenance.php status`, dann `off`.
- **Composer im Kurs nicht erlaubt.** Bereiten Sie Installationen vor, in denen das Engine-Paket bereits eingebunden
  ist.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Führen Sie jeden Befehl im Wurzelverzeichnis der Installation aus.

1. Prüfen Sie die PHP-Erweiterungen, die die Engine erwartet:

   ```bash
   php bin/php/console exp:velocity ext check
   ```

2. Lesen Sie einmal die vollständige Befehlsliste: `php bin/php/console exp:velocity --help | less`.
3. Zeigen Sie, welche Engine der Standard ist und welche Einstellungen Sie überschrieben haben:

   ```bash
   php bin/php/console exp:velocity config get ServerSettings Engine
   php bin/php/console exp:velocity config list ServerSettings
   ```

4. Wenn die Engine nicht `qbix` ist, setzen Sie sie:
   `php bin/php/console exp:velocity config set ServerSettings Engine qbix`.
5. Zeigen Sie, welche Datei geschrieben wurde: `php bin/php/console exp:velocity config paths`.
6. Starten Sie den Server: `php bin/php/console exp:velocity start`.
7. Lesen Sie den Status: `php bin/php/console exp:velocity status`. Notieren Sie die Adresse der Website, die Adresse der
   Administration und die Adresse des Dashboards.
8. Prüfen Sie die Website: `curl -sI http://127.0.0.1:8088/ | head -1` (verwenden Sie den Port, den der Status zeigt).
9. Lesen Sie die Startzeilen: `tail -n 20 var/vc/qbix/run/console.log`. Suchen Sie die Zeile, die mit `Workers as:`
   beginnt.
10. Geben Sie den maschinenlesbaren Status aus: `php bin/php/console exp:velocity status --json | head -30`.

## Erwartetes Ergebnis

- Schritt 1 meldet keine fehlende Erweiterung oder nennt die Erweiterungen mit ihren Installationsbefehlen.
- Schritt 5 zeigt, dass die Override-Datei `settings/override/velocity.ini.append.php` ist.
- Schritt 7 zeigt die laufende Engine `qbix` mit vollständigen URLs für die Website und die Administration.
- Schritt 8 gibt `HTTP/1.1 200 OK` oder eine Weiterleitung aus.
- Schritt 9 nennt den Benutzer, unter dem die Worker laufen.
- Der Trainer prüft das, indem er jede Person nach dem Benutzer bei `Workers as:` fragt und ob es root ist.

## Quiz

1. Was beantwortet der Elternprozess von Velocity ohne Worker?
2. Welche Datei bearbeiten Sie nie, um eine Velocity-Einstellung zu ändern?
   A. `settings/override/velocity.ini.append.php` B. `settings/velocity.ini` C. keine, beide werden erzeugt
   D. `/etc/vc/vc.conf`
3. Sie haben `[ServerSettings] Workers` geändert. Wann verwendet der Server den neuen Wert?
4. Warum ist der mitgelieferte `Host` `127.0.0.1`?
5. Richtig oder falsch: `exp:velocity status` sagt `running`, sobald die PID-Datei existiert.

<details>
<summary>Antworten</summary>

1. Statische Dateien, Treffer im Antwort-Cache und seine eigenen `/Q/`-Seiten; außerdem terminiert er TLS
   (Abschnitt 8.3).
2. **B.** Sie enthält die mitgelieferten Standardwerte; `config set` schreibt die Override-Datei.
3. Beim nächsten `restart` oder `graceful`.
4. Damit der Server nur vom Rechner selbst, über einen Tunnel oder einen Reverse Proxy erreichbar ist, bis Sie etwas
   anderes entscheiden.
5. **Falsch.** Es sagt `running` nur bei einem Server, der eine HTTP-Anfrage beantwortet, und endet sonst mit einem Wert
   ungleich null.

</details>

## Weiterführende Informationen

- [Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md): Anforderungen, Einstellungen und Betrieb.
- [Kapitel 8, Befehlsreferenz](../../../../installation/08-die-website-ausliefern.md#8316-befehlsreferenz): jedes Verb.
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md):
  die Funktionsseite.
- [Velocity startet nicht](../../../hosting/runbooks/velocity-startet-nicht.md): das Runbook für einen
  fehlgeschlagenen Start.

## Verwandte Seiten

- [Modul 2: Velocity](README.md)
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
