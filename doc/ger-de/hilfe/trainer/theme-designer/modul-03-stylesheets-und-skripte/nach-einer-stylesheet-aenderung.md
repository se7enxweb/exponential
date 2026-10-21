---
title: "Lektion 3.3: Nach einer Änderung am Stylesheet"
audience: trainer, designer, developer
category: training
order: 33
summary: Die Teilnehmenden leeren genau die Caches, die eine Änderung an einem Stylesheet oder Skript verlangt (Packer, Template, Template-Block), verwenden ein hartes Neuladen statt eines Versionsparameters und kennen den einen Deploy-Befehl für einen Velocity-Server. 30 Minuten.
keywords: theme-design, stylesheet-änderung, cache leeren, ezjscore-packer, template-block, template-cache, hartes neuladen, versionsparameter, exp:velocity deploy, packer, veraltetes stylesheet, theme design, stylesheet change, cache clear, hard reload, version query, stale stylesheet
type: help
---
# Lektion 3.3: Nach einer Änderung am Stylesheet

„Ich habe das CSS geändert, und nichts ist passiert“ ist der häufigste Satz in einem Theme-Kurs. Die gepackte Datei,
der Cache-Block, der ihren Link enthält, und die Kopie im Browser erinnern sich jeweils an die alte Version. Diese
kurze Lektion gibt den Teilnehmenden eine Routine, die immer funktioniert, und eine Regel, die sie nicht brechen
dürfen: kein Versionsparameter an einem Link.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 3: Stylesheets und Skripte](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 3.2](der-ezjscore-packer.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Stellen nennen, die sich an ein altes Stylesheet erinnern, und den Cache, der jede davon leert;
- nach einer Änderung an einem Stylesheet oder Skript die Routine zum Leeren ausführen;
- den Browser mit einem harten Neuladen zwingen, die neue Datei zu holen;
- erklären, warum kein Parameter `?v=` an einem Link verwendet wird.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Stylesheets und Skripte für Ihre Website, nach einer Änderung](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md#after-a-change)
und [Die Administration gestalten, nach einer Änderung am Stylesheet](../../../../bc/6.0/admin4-gestaltung.md#nach-einer-änderung-an-einem-stylesheet).

1. **Drei Gedächtnisse** (4 Minuten). Zeichnen Sie sie an die Tafel:

   | Erinnert sich an | Geleert durch |
   |---|---|
   | die gepackte Datei | `php bin/php/ezcache.php --clear-id=ezjscore-packer` |
   | den Link darauf, in kompilierten Templates und in Cache-Blöcken des Pagelayouts | `--clear-tag=template` und `--clear-id=template-block` |
   | die Kopie im Browser | ein hartes Neuladen (Strg+Umschalt+R, auf dem Mac Cmd+Umschalt+R) |

   Mit `AppendLastModifiedTime=enabled` bekommt eine neue Datei ohnehin einen neuen Namen; das Leeren stellt sicher,
   dass die Seite diesen neuen Namen verlinkt.
2. **Kein Versionsparameter** (2 Minuten). Hängen Sie kein `?v=...` an einen Stylesheet- oder Skript-Link: Der
   Änderungszeitpunkt im gepackten Namen erledigt das bereits, und die Regel des Projekts verbietet
   Versionsparameter. Ein Browser, der noch das alte Aussehen zeigt, braucht ein hartes Neuladen.
3. **Auf einem Velocity-Server** (2 Minuten). Velocity hat einen eigenen Antwort-Cache. Auf einem Server, auf dem es
   läuft, erledigt nach einer Änderung am Stylesheet ein einziger Befehl die ganze Routine:

   ```bash
   php bin/php/console exp:velocity deploy --packer
   ```

   `--packer` leert die gepackten Skripte und Stile unmittelbar vor dem Template-Block-Cache. Fügen Sie `--dry-run`
   hinzu, um die Schritte vorher zu sehen.
4. **Neue Template-Dateien** (2 Minuten). Eine Änderung am Stylesheet verlangt kein Leeren von `template-override`;
   eine neue Template-Datei schon (Lektion 2.3).

### Häufige Fragen

**Warum sehe ich die Änderung, eine Kollegin aber nicht?** Ihr Browser hat die neue Datei geholt; ihrer hält noch die
alte. Ein hartes Neuladen auf ihrer Seite.

**Brauche ich das alles auch bei ausgeschaltetem Packer?** Auf Stufe 0 wird jede Datei unter ihrer eigenen Adresse
verlinkt; das Haupthindernis ist dann die Kopie im Browser, also genügt meist ein hartes Neuladen. Die Routine
funktioniert trotzdem.

**Warum nicht jedes Mal alle Caches leeren?** Das funktioniert, ist aber auf einer echten Website langsam und leert den
View-Cache der Inhalte für jede Seite. Lernen Sie das gezielte Leeren.

### Stolperfallen

- **Nur den Packer leeren.** Der Cache-Block des Pagelayouts verlinkt noch den alten Namen, den es vielleicht nicht
  mehr gibt: Die Seite verliert ihre Stile. Leeren Sie auch `template-block`.
- **Im falschen Design bearbeiten.** Die Änderung wurde in einer Datei vorgenommen, die die Kette nie erreicht. Prüfen
  Sie im Seitenquelltext, aus welcher Datei das gepackte Bündel stammt (Packer aus, Lektion 3.2).
- **Hinter einem Proxy oder CDN testen.** Deren Caches liegen außerhalb von Exponential; die Übungsumgebung des
  Trainers sollte keinen haben.

## Praktische Übung

Auf Ihrer Schulungsinstallation, Packer eingeschaltet.

1. Ändern Sie in `training.css` Ihres Designs die Schriftgröße des Breadcrumbs von `0.9rem` auf `1.1rem`. Speichern
   Sie.
2. Laden Sie die Seite normal neu. Notieren Sie, ob sich der Breadcrumb geändert hat.
3. Lesen Sie den Stylesheet-Link im Seitenquelltext und notieren Sie den Dateinamen.
4. Führen Sie die Routine aus:

   ```bash
   php bin/php/ezcache.php --clear-id=ezjscore-packer
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=template-block
   ```

   Wenn Ihre Übungsumgebung von Velocity bereitgestellt wird, leeren Sie auch dessen Antwort-Cache:
   `php bin/php/console exp:velocity cache clear`.
5. Laden Sie hart neu. Prüfen Sie den Breadcrumb.
6. Lesen Sie den Stylesheet-Link erneut und vergleichen Sie seinen Namen mit Schritt 3.
7. Wenn Ihre Übungsumgebung auf Velocity läuft, führen Sie `php bin/php/console exp:velocity deploy --packer --dry-run`
   aus und lesen Sie die Liste der Schritte, die er ausführen würde. Führen Sie ihn auf einem gemeinsamen Server nicht
   ohne `--dry-run` aus.
8. Schreiben Sie die Routine aus Schritt 4 auf eine Karte neben Ihrer Tastatur.

## Erwartetes Ergebnis

- Schritt 2: Die Änderung ist je nach Caches und Browser sichtbar oder nicht.
- Schritt 5: Der Breadcrumb zeigt die neue Größe.
- Schritt 6: Die Zahl zwischen den Unterstrichen im Namen ist höher als in Schritt 3.
- Schritt 7: Der Probelauf listet die Schritte auf, darunter die Caches des Packers und der Template-Blöcke, und ändert
  nichts.
- Der Trainer bittet zwei Teilnehmende, die drei Gedächtnisse und ihr Leeren ohne Notizen aufzusagen.

## Quiz

1. Welche drei Caches leert die Routine nach einer Änderung am Stylesheet?
2. Warum kann die Seite alle ihre Stile verlieren, wenn nur der Cache des Packers geleert wird?
3. Eine Kollegin sieht nach Ihrer Routine noch die alte Farbe. Was sollte sie tun?
   A. `?v=2` an den Link hängen B. Erneut alle Caches leeren C. Hart neu laden D. Die Datenbank neu starten
4. Welcher Befehl erledigt auf einem Velocity-Server die ganze Routine?
5. Richtig oder falsch: Eine Änderung am Stylesheet verlangt das Leeren des Caches `template-override`.

<details>
<summary>Antworten</summary>

1. `ezjscore-packer`, den Template-Cache und `template-block`
   ([Stylesheets und Skripte für Ihre Website](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md#after-a-change)).
2. Ein Cache-Block im Pagelayout verlinkt noch den alten Namen der gepackten Datei, die es nicht mehr gibt.
3. **C.** Versionsparameter werden nicht verwendet; der Änderungszeitpunkt im Namen ändert ihn bereits.
4. `php bin/php/console exp:velocity deploy --packer`.
5. **Falsch.** Nur eine neue Template-Datei verlangt es.

</details>

## Weiterführende Literatur

- [Stylesheets und Skripte für Ihre Website](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md): die
  Checkliste.
- [Caches](../../../../anleitungen/caches.md): jeder Cache und seine ID.
- [Antwort-Cache von Velocity](../../../../../eng-us/features/6.0/velocity-response-cache.md): der zusätzliche Cache auf
  Velocity-Servern.

## Verwandte Seiten

- [Modul 3: Stylesheets und Skripte](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
