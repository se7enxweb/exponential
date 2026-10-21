---
title: "Lektion 13.1: Die RAD-Werkzeuge"
audience: trainer, developer
category: training
order: 131
summary: Die Teilnehmenden finden mit Setup > RAD den richtigen Erweiterungspunkt, erzeugen mit einem Assistenten eine kleine Erweiterung und lesen jede Datei, die er schreibt, lesen die Übersicht der Erweiterungspunkte und prüfen Klassen mit der Zustandsprüfung und checkclasses.php. 45 Minuten.
keywords: entwickler, RAD, werkzeuge zur schnellen anwendungs-entwicklung, setup/rad, erweiterungspunkte, katalog, assistent, einstellungs-erweiterung, assistent für template-operatoren, übersicht der erweiterungspunkte, radsurvey, zustandsprüfung, checkclasses.php, developers course, Rapid Application Development Tools, extension points, catalogue, wizard, extension point survey, health check
type: help
---
# Lektion 13.1: Die RAD-Werkzeuge

Nach zwölf Modulen haben die Teilnehmenden die meisten Arten von Erweiterungscode von Hand geschrieben. Die
RAD-Werkzeuge schreiben denselben Code in Minuten, und sie sind zugleich die Landkarte aller Stellen, an denen sich der
Kernel erweitern lässt. In dieser Lektion wählen die Teilnehmenden im Katalog einen Erweiterungspunkt, lassen einen
Assistenten eine Erweiterung schreiben, lesen, was er geschrieben hat, und sehen mit der Übersicht und der
Zustandsprüfung, was eine Installation tatsächlich anbietet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 13: RAD, Velocity und Mitwirken](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Modul 7](../modul-07-erweiterungen/README.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **Setup** > **RAD** öffnen und einen Erweiterungspunkt in seinen sieben Gruppen finden;
- mit einem Assistenten eine Erweiterung erzeugen und die Liste der Dateien lesen, bevor etwas geschrieben wird;
- die erzeugte Erweiterung einschalten und prüfen;
- die **Übersicht der Erweiterungspunkte** und die Befunde der Zustandsprüfung lesen;
- `php bin/php/checkclasses.php` ausführen, um Klassen zu finden, die PHP nicht laden will.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [RAD-Werkzeuge: Erweiterungsassistenten, Katalog, Übersicht und Zustandsprüfung](../../../../../eng-us/features/6.0/rad-tools.md).

1. **Der Katalog** (4 Minuten). **Setup** > **RAD** (`/setup/rad`, Überschrift „Werkzeuge zur schnellen
   Anwendungs-Entwicklung“) listet die Erweiterungspunkte in Gruppen: Inhalt, Templates und Design, Module und Views,
   Workflow, Ereignisse und Jobs, Speicherung und Infrastruktur, Pakete und Shop, Benutzer, Zugriff und Sprache. Jeder
   Eintrag sagt, wie er registriert wird, und verlinkt seinen Assistenten. Zählen Sie die Einträge auf Ihrer
   Installation; die Zahl wächst mit dem Kernel.
2. **Ein Assistent** (4 Minuten). Jeder Assistent fragt nach einem Namen und danach, was erzeugt werden soll, und
   listet dann jede Datei, die er unterhalb von `extension/<name>` schreiben würde, bevor er irgendeine schreibt. Er
   schreibt niemals außerhalb des Erweiterungsordners und überschreibt niemals eine vorhandene Erweiterung.
3. **Nach dem Schreiben** (2 Minuten). Aktivieren Sie die Erweiterung, führen Sie
   `php bin/php/ezpgenerateautoloads.php -e` aus, leeren Sie die Caches.
4. **Die Übersicht** (2 Minuten). Die **Übersicht der Erweiterungspunkte** (`/setup/radsurvey`) liest von der
   Festplatte, was diese Installation anbietet: Einstellungen, die eine Klasse nennen, Template-Operatoren,
   Modul-Views, Ereignisse, Overrides.
5. **Zustand** (3 Minuten). Die Zustandsprüfung meldet Einstellungen, die eine Klasse nennen, die sich nicht laden
   lässt, durchsuchte und fehlende Verzeichnisse, Views ohne Skript sowie angebotene und nicht gefundene Datentypen.
   `checkclasses.php` lädt jede deklarierte Klasse und listet diejenigen, die PHP ablehnt.

### Häufige Fragen

**Sollte ich immer einen Assistenten verwenden?** Verwenden Sie ihn, um schnell zu beginnen und die Konventionen zu
sehen; verstehen Sie jede Datei, die er geschrieben hat, wie in den früheren Modulen, bevor Sie sie ändern.

**Wer darf die RAD-Werkzeuge verwenden?** Benutzer mit der Richtlinie `setup/setup`.

**Ist erzeugter Code sicher?** Erzeugte Templates maskieren, was sie ausgeben; die Assistenten folgen der
[Sicherheit der RAD-Werkzeuge](../../../../bc/6.0/rad-sicherheit.md).

### Stolperfallen

- **Auf einer Live-Website erzeugen.** Assistenten schreiben Dateien; verwenden Sie sie nur auf
  Entwicklungsinstallationen.
- **Ein Name, den es schon gibt.** Der Assistent lehnt ab; wählen Sie einen anderen Namen, statt die alte Erweiterung
  zu löschen.
- **Die Autoloads vergessen.** Eine erzeugte Klasse wird erst gefunden, wenn `-e` gelaufen ist.

## Praktische Übung

Auf der Schulungsinstallation, als Administrator an der Administration angemeldet.

1. Öffnen Sie **Setup** > **RAD** und lesen Sie die Gruppenüberschriften. Zählen Sie die Einträge von **Templates und
   Design**.
2. Suchen Sie die Einträge, die Sie in diesem Kurs von Hand gebaut haben: **Template-Operator**,
   **Template-Fetch-Funktion**, **Modul**, **Datentyp**, **Workflow-Ereignistyp**, **REST-Provider**.
3. Wählen Sie **Template-Operator**. Geben Sie den Erweiterungsnamen `mysite_wizard` und einen Operatornamen Ihrer
   Wahl ein.
4. Lesen Sie die Liste der Dateien, die der Assistent schreiben würde; vergleichen Sie sie mit Ihrem
   `mysite_hello/autoloads/`.
5. Schreiben Sie die Erweiterung, dann in einer Shell: `find extension/mysite_wizard -type f | sort`.
6. Aktivieren Sie sie, erzeugen Sie die Autoloads neu und leeren Sie die Caches:

   ```bash
   php bin/php/console exp:ini add site.ini/ExtensionSettings/ActiveExtensions mysite_wizard global
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   ```

7. Verwenden Sie den erzeugten Operator in Ihrem Übungs-Template.
8. Öffnen Sie von der RAD-Seite aus die **Übersicht der Erweiterungspunkte** und suchen Sie Ihren Operator in der
   Liste der Template-Operatoren.
9. Führen Sie `php bin/php/checkclasses.php --quiet-ok` aus.

## Erwartetes Ergebnis

- Schritt 4: die Dateiliste mit der Operator-Klasse, `autoloads/eztemplateautoload.php`, den Einstellungen,
  `ezinfo.php`, `extension.xml` und der Lizenz, bevor etwas geschrieben wird.
- Schritt 7: Der Operator funktioniert im Übungs-Template.
- Schritt 8: Die Übersicht listet den neuen Operator.
- Schritt 9: keine Ausgabe, wenn sich jede Klasse laden lässt.
- Der Trainer bittet alle Teilnehmenden, eine Datei zu nennen, die der Assistent geschrieben hat und die ihrer von
  Hand geschriebenen Fassung fehlt.

## Quiz

1. Wo finden Sie die RAD-Werkzeuge?
2. Was zeigt ein Assistent, bevor er schreibt?
   A. Nichts B. Die Liste aller Dateien, die er schreiben würde C. Nur den Klassennamen D. Einen Diff des Kernels
3. Was muss geschehen, nachdem eine erzeugte Erweiterung geschrieben wurde?
4. Was liest die Übersicht der Erweiterungspunkte?
5. Welcher Befehl listet Klassen, die PHP nicht laden will?

<details>
<summary>Antworten</summary>

1. **Setup** > **RAD** (`/setup/rad`).
2. **B.** Jede Datei, die er schreiben würde, bevor er irgendeine schreibt.
3. Sie aktivieren, `php bin/php/ezpgenerateautoloads.php -e` ausführen und die Caches leeren.
4. Was die Installation anbietet, bei jeder Anfrage von der Festplatte gelesen: Einstellungen, die eine Klasse nennen,
   Operatoren, Views, Ereignisse, Overrides.
5. `php bin/php/checkclasses.php`.

</details>

## Weiterführende Literatur

- [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md).
- [Erweiterungspunkte](../../../../bc/6.0/rad-erweiterungspunkte.md) und [Die Erweiterungsoberfläche](../../../../bc/6.0/rad-erweiterungsoberflaeche.md).
- [Sicherheit der RAD-Werkzeuge](../../../../bc/6.0/rad-sicherheit.md).

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
