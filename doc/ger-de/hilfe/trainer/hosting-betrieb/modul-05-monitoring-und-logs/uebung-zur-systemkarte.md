---
title: "Lektion 5.5 (optional): Die Systemkarte"
audience: trainer, hosting, admin
category: training
order: 55
summary: Eine optionale Lektion von 25 Minuten - die Teilnehmenden lesen einen Server mit ./console exp:expfetch, handeln nach seinen Zustandshinweisen, verfolgen Last und Velocity-Worker live mit --watch und vergleichen zwei Installationen mit exp:info --json. Funktioniert auf jeder Schulungsinstallation.
keywords: hosting und betrieb, systemkarte, exp:expfetch, exp:info, exp:about, zustandshinweise, --watch, --json, jq, bestandsaufnahme, NO_COLOR, schulung, übung, hosting and operations, system card, health hints, inventory, training, exercise
type: help
---
# Lektion 5.5 (optional): Die Systemkarte

Wer als Hoster auf einem Server ein Terminal öffnet, möchte einen Bildschirm, der sagt, was dort läuft und was nicht
stimmt. Diese Lektion vermittelt die Systemkarte, `./console exp:expfetch`, und ihre zwei größeren Formen. Sie passt
nach Lektion 5.1 oder als Aufwärmübung zu Beginn eines Tages. Referenz: [Die Systemkarte](../../../../../eng-us/features/6.0/system-card.md).

## Auf einen Blick

| | |
|---|---|
| Modul | [Modul 5: Monitoring und Logs](README.md) |
| Dauer | 25 Minuten |
| Niveau | Einsteiger bis Fortgeschrittene |
| Form | Praxisorientiert, allein; der letzte Schritt in Paaren |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

1. die Karte lesen und sagen, aus welchem Abschnitt eine Angabe stammt;
2. jeden Hinweis auf ihrem Rechner erklären und sagen, was ihn beheben würde;
3. mit `--watch` Last, Speicher und Velocity-Worker verfolgen, während etwas läuft;
4. `exp:info --json` erzeugen und mit `jq` Werte daraus entnehmen;
5. erklären, warum die Ausgabe gefahrlos in ein Ticket eingefügt werden kann.

## Hinweise für Trainer

- Zeigen Sie die Karte einmal in Farbe auf einem breiten Terminal (das „e“ mit dem Schriftzug) und verkleinern Sie das
  Fenster dann auf 60 und 40 Spalten: Das Logo schrumpft und wird schließlich übereinander angeordnet. Führen Sie sie
  dann mit `NO_COLOR=1` und über `| cat` aus, um zu zeigen, dass Farben nur in einem Terminal erscheinen.
- Auf einer Schulungsinstallation ist der Block mit Hinweisen selten leer (der Cronjob fehlt oft, der Hilfeindex ist
  veraltet): Das ist gutes Material.
- Häufige Frage: „Warum steht dort, OPcache sei an, wenn die Website langsam ist?“ Die Karte zeigt das PHP der
  Kommandozeile; das PHP des Webservers steht unter Setup > Systeminformationen.
- Stolperfalle: Als root braucht der Befehl wie jeder Konsolenbefehl `--allow-root-user`.

## Praktische Übung

1. Führen Sie `./console exp:expfetch` aus. Notieren Sie die Version von Exponential, die PHP-Version und ob Velocity
   läuft.
2. Lesen Sie den Block mit Hinweisen. Schreiben Sie für jeden Hinweis einen Satz: was er bedeutet und was Sie tun
   würden.
3. Beheben Sie einen Hinweis, der sich auf einem Schulungsrechner gefahrlos beheben lässt (zum Beispiel
   `./console exp:help index` für einen veralteten Hilfeindex), und führen Sie die Karte erneut aus: Der Hinweis ist
   weg.
4. Starten Sie in einem zweiten Terminal `./console exp:expfetch --watch=1 --section=system,engine`. Führen Sie im
   ersten `./console exp:benchmark` aus (oder laden Sie die Website ein paarmal im Browser neu). Beobachten Sie, wie sich
   Last und Worker bewegen. Beenden Sie mit Strg-C.
5. Führen Sie `./console exp:info --json --no-logo > me.json` aus, dann `jq '.sections.php' me.json` und
   `jq '[.hints[] | .severity] | group_by(.) | map({(.[0]): length}) | add' me.json`.
6. In Paaren: Tauschen Sie die JSON-Dateien aus und listen Sie drei Unterschiede zwischen den beiden Installationen auf
   (`diff <(jq -S . me.json) <(jq -S . them.json)`).
7. Durchsuchen Sie beide Dateien nach dem Datenbankbenutzer oder dem Passwort. Sie werden sie nicht finden: Erklären
   Sie, warum.

## Erwartetes Ergebnis

- Jede Person hat eine Liste von Hinweisen mit einer Behebung für jeden, und ein Hinweis ist behoben.
- Die Teilnehmenden haben gesehen, wie `--watch` an Ort und Stelle aktualisiert, und wissen, wie man es beendet.
- Jedes Paar hat drei Unterschiede und kann sagen, welche davon wichtig sind.

## Quiz

1. Welcher Befehl zeigt die Versionen aller aktiven Erweiterungen? (`exp:info`)
2. Was passiert, wenn die Datenbank nicht antwortet? (Die Angabe zur Datenbank ist ein Fehler mit einem Hinweis; mit
   Isolation zeigt ein Anbieter, der hängt, `n/a (timeout)`, und die Karte erscheint trotzdem.)
3. Wie bekommen Sie in einem Terminal keine Farben? (`NO_COLOR=1`, `--no-color` oder `--plain`.)
4. Welche Option gibt ein Dokument für ein Monitoring-System aus? (`--json`)

## Weiterführende Informationen

- [Die Systemkarte](../../../../../eng-us/features/6.0/system-card.md): jede Option, die Hinweise und die Einstellungen.
- [Die Systemkarte für Hoster](../../../../../eng-us/help/hosting/operations/system-card-for-hosts.md)

## Verwandte Seiten

- [Modul 5: Monitoring und Logs](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
