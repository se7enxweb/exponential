---
title: "Lektion 13.2: Velocity für Entwickler"
audience: trainer, developer
category: training
order: 132
summary: Die Teilnehmenden wählen eine Velocity-Engine, erklären, warum eine geänderte PHP-Klasse persistente Worker erst nach einem Neustart erreicht, deployen eine PHP-Änderung mit exp:velocity deploy in der richtigen Reihenfolge und leeren den Antwort-Cache. 45 Minuten.
keywords: entwickler, Velocity, exp:velocity, engine, php, qbix, frankenphp, start, status, neustart, restart, deploy, dry-run, kernel, packer, antwort-cache, persistente worker, OPcache, reihenfolge beim cache-leeren, developers course, response cache, persistent workers, order of cache clearing
type: help
---
# Lektion 13.2: Velocity für Entwickler

Exponential Velocity ist der Anwendungsserver, der mit Exponential ausgeliefert wird. Seine Worker halten PHP-Code
zwischen Anfragen im Speicher. Das macht ihn schnell und ändert eine Gewohnheit aller Entwickler: Eine geänderte Klasse
ist erst live, wenn die Worker den neuen Code haben. Diese Lektion erklärt die Engines, den Deploy-Befehl, der jeden
Schritt in der richtigen Reihenfolge ausführt, und warum die Reihenfolge wichtig ist.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 13: RAD, Velocity und Mitwirken](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Betreiber |
| Voraussetzungen | [Lektion 1.2](../modul-01-die-entwicklungsinstallation/eine-entwicklungsinstallation-mit-git.md) und [Lektion 3.5](../modul-03-designs-overrides-und-debugging/caches-fuer-template-entwickler.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Engines (`php`, `qbix`, `frankenphp`), ihre Rollen und Standardports nennen;
- Velocity mit `exp:velocity` starten, prüfen und neu starten;
- erklären, warum persistente Worker nach einer Änderung an einer Klasse oder einer INI-Datei einen Neustart brauchen;
- eine PHP-Änderung mit `exp:velocity deploy` deployen, zuerst mit `--dry-run`, und wissen, wann `--kernel` und
  `--packer` hinzukommen;
- erklären, warum Caches für gerenderte Ausgabe erst geleert werden, nachdem der neue Code läuft.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Velocity-Engines](../../../../bc/6.0/velocity-engines.md) und
[8. Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md).

1. **Engines** (4 Minuten). `velocity.ini [ServerSettings] Engine` legt den Standard fest: `php` (mitgeliefert; der
   eingebaute Server von PHP, Entwicklung, Port 8087), `qbix` (die empfohlene Engine für jede Stufe, Port 8088),
   `frankenphp` (eine produktionsreife Alternative, Port 8089). `--engine=` wählt eine für einen einzelnen Befehl.
2. **Persistente Worker** (4 Minuten). Qbix-Worker laden Klassen einmal und behalten sie; eine geänderte Klasse, ein
   geänderter Operator oder eine geänderte INI-Datei erreicht sie nach `exp:velocity restart`. Die Engine `php`
   startet für jede Anfrage neu, darum fällt es auf Entwicklungsinstallationen selten auf.
3. **Deploy** (6 Minuten). `exp:velocity deploy` führt der Reihe nach aus: die Autoloads der Erweiterungen
   (`--kernel`: auch die des Kernels), die INI-Caches, die Template-, Override-, Übersetzungs- und Design-Caches, das
   Engine-Archiv, wenn sich Kernel-Dateien geändert haben, PHP-FPM, den Neustart von Velocity, dann die Content-,
   HTTP- und Template-Block-Caches (mit `--packer` unmittelbar davor die gepackten Skripte und Stylesheets), dann den
   Antwort-Cache von Velocity und zuletzt das Aufwärmen. Jeder Schritt meldet PASS, FAIL oder SKIP; der erste Fehler
   beendet den Lauf.
4. **Auf die Reihenfolge kommt es an** (3 Minuten). Gerenderte Ausgabe, die zwischengespeichert wird, bevor die
   Worker den neuen Code ausführen, wird vom alten Code gerendert und ausgeliefert, als hätte die Änderung nicht
   funktioniert. Leeren Sie Ausgabe-Caches zuletzt.
5. **Status** (2 Minuten). `exp:velocity status` zeigt, was läuft; `exp:velocity cache clear` leert den Antwort-Cache.

### Häufige Fragen

**Wann brauche ich `--kernel`?** Wenn eine Kernel-Klasse hinzugefügt oder umbenannt wurde (die Autoloads des Kernels
ändern sich).

**Wann brauche ich `--packer`?** Wenn sich eine Serverfunktion von ezjscore oder ihre Einstellungen geändert haben;
eine bearbeitete `.js`- oder `.css`-Datei erhält von selbst eine neue gepackte Datei.

**Lädt Deploy PHP-FPM jedes Mal neu?** Nur wenn es nötig ist; sonst wartet es, bis OPcache die geänderten Dateien
erneut geprüft hat (`[DeploySettings] PhpFpmReload=auto`).

### Stolperfallen

- **Caches vor dem Neustart leeren.** Der alte Code füllt sie wieder. Verwenden Sie `deploy`, das die Reihenfolge
  einhält.
- **Eine Klassenänderung auf Qbix ohne Neustart testen.** Die alte Klasse läuft weiter; die Änderung „funktioniert
  nicht“.
- **Die eingebaute Engine auf einer öffentlichen Adresse betreiben.** Lassen Sie die Engine `php` auf 127.0.0.1; sie
  ist ein Entwicklungsserver.

## Praktische Übung

Auf der Entwicklungsinstallation.

1. Zeigen Sie die Engines und was läuft: `php bin/php/console exp:velocity status`.
2. Lesen Sie die Standard-Engine: `php bin/php/console exp:ini where velocity.ini/ServerSettings/Engine`.
3. Sehen Sie nach, was ein Deploy tun würde: `php bin/php/console exp:velocity deploy --dry-run`.
4. Ändern Sie den Text, den Ihr Operator `mysite_initials` bei leerer Eingabe zurückgibt (zum Beispiel `-` statt einer
   leeren Zeichenkette), und führen Sie den Deploy wirklich aus: `php bin/php/console exp:velocity deploy`.
5. Lesen Sie die Zeilen PASS, FAIL und SKIP und die Gesamtsumme.
6. Öffnen Sie das Übungs-Template oder eine Seite, die den Operator verwendet, und prüfen Sie die Änderung.
7. Wenn Ihr Trainer auf dem Schulungsserver eine Qbix-Engine betreibt, vergleichen Sie: Ändern Sie den Operator
   erneut, laden Sie eine von Qbix ausgelieferte Seite ohne Neustart neu und dann nach `exp:velocity restart`.
8. Machen Sie die Änderung rückgängig, deployen Sie erneut und führen Sie den Test aus Lektion 12.2 aus.

## Erwartetes Ergebnis

- Schritt 1 listet die verwendete Engine, ihre Adresse und ihren Port.
- Schritt 3 listet die Schritte, die er ausführen würde, und führt keinen aus.
- Schritt 5 endet mit `velocity deploy: PASS` und der Zahl der Schritte und Sekunden.
- Schritt 7: auf Qbix das alte Verhalten bis zum Neustart.
- Schritt 8: Die Tests laufen wieder durch.
- Der Trainer fragt alle Teilnehmenden, warum die Ausgabe-Caches zuletzt geleert werden.

## Quiz

1. Welche Engine wird als Standard mitgeliefert, und welche wird für echte Websites empfohlen?
2. Eine Klasse hat sich geändert. Was muss geschehen, bevor Qbix-Worker sie ausführen?
   A. Nichts B. `exp:velocity restart` (oder ein Deploy) C. Ein Neuladen im Browser D. Nur das Leeren des INI-Caches
3. Welche Option zeigt, was `deploy` tun würde, ohne es zu tun?
4. Wann wird `--kernel` gebraucht?
5. Warum werden die Caches für gerenderte Ausgabe nach dem Neustart geleert?

<details>
<summary>Antworten</summary>

1. `php` ist der mitgelieferte Standard; `qbix` wird für jede Stufe empfohlen.
2. **B.** Die Worker behalten die Klassen ihres Aufwärmens bis zu einem Neustart.
3. `--dry-run`.
4. Wenn eine Kernel-Klasse hinzugefügt oder umbenannt wurde.
5. Seiten, die in der Zwischenzeit vom alten Code gerendert werden, würden erneut zwischengespeichert und
   ausgeliefert, als hätte die Änderung nicht funktioniert.

</details>

## Weiterführende Literatur

- [Velocity-Engines](../../../../bc/6.0/velocity-engines.md): Engines, Deploy, Neustart und der Watchdog.
- [Der Velocity-Server mit persistenten Workern](../../../../../eng-us/features/6.0/velocity-persistent-worker-server.md).
- [Der Antwort-Cache von Velocity](../../../../../eng-us/features/6.0/velocity-response-cache.md).

## Verwandte Seiten

- [Modul 13: RAD, Velocity und Mitwirken](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
