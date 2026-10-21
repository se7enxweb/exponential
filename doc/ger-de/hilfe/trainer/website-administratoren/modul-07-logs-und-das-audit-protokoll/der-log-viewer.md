---
title: "Lektion 7.2: Der Log-Viewer"
audience: trainer, admin
category: training
order: 72
summary: Die Teilnehmenden öffnen Setup > Logdateien, verfolgen ein Log live, durchsuchen es nach Text, Stufe und Zeit, teilen einen maskierten Permalink und lesen die Seiten Fehlerübersicht und Zustand der Logs. 45 Minuten.
keywords: website-administration, log-viewer, Setup Logdateien, verfolgen, suchen, permalink, maskierung, unmaskiert zeigen, fehlerübersicht, zustand der logs, logs verwalten, setup/logs, site administration, log viewer, follow, search, masking, Show unmasked, Error digest, Log health, Manage logs
type: help
---
# Lektion 7.2: Der Log-Viewer

**Setup > Logdateien** liest jedes Log der Installation im Browser: Es verfolgt eine Datei, während sie geschrieben
wird, durchsucht sie und ihre Rotationen, gruppiert die Zeilen einer Anfrage und maskiert personenbezogene Daten
standardmäßig. Dazu kommen zwei Übersichtsseiten, **Fehlerübersicht** und **Zustand der Logs**, die einem
Administrator in einer Minute sagen, was nicht stimmt. Diese Lektion macht den Viewer zum täglichen Werkzeug, mit der
Kommandozeile aus Lektion 7.1 als Ausweichweg.

> **Hinweis**
> Machen Sie auf einer Live-Website Screenshots und Berichte mit eingeschalteter Maskierung. Die Übungen verwenden Ihre
> eigene Schulungsinstallation.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 7: Logs und das Audit-Protokoll](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter |
| Voraussetzungen | [Lektion 7.1](die-logdateien.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ein Log aus dem Navigator von **Setup > Logdateien** öffnen und es mit **Anhalten** und **Neueste** live verfolgen;
- eine Datei, alle Kernel-Logs oder jede Datei nach Text, Stufe und Zeit durchsuchen;
- einen Permalink einer Ansicht kopieren und erklären, warum er sich gefahrlos teilen lässt;
- die Seiten **Fehlerübersicht** und **Zustand der Logs** lesen und sagen, wovor jede warnt;
- die drei Richtlinien des Viewers nennen und sagen, wer sie auf einer neuen Installation hat.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Der Log-Viewer](../../../../funktionen/6.0/log-betrachter.md). Beschrieben wird die Seite des Designs admin4.

1. **Der Weg hinein** (2 Minuten). **Setup**, dann **Logdateien** in der linken Spalte. Die Seite öffnet `error.log`
   an seinem Ende. Über den Zeilen liegen die Reiter **Logs**, **Fehlerübersicht**, **Zustand der Logs** und
   **Logs verwalten**.
2. **Der Navigator** (3 Minuten). Die Dateien sind gruppiert in **Kernel**, **Site**, **Extensions**, **Webserver**
   und **Rotierte Archive**, jede mit Größe, letztem Schreibvorgang und Zeilen pro Minute. Logs des Webservers
   erscheinen nur, wenn `logviewer.ini` sie nennt.
3. **Lesen und verfolgen** (3 Minuten). Jeder Eintrag zeigt Zeit, Stufe (farbig, mit Beschriftung und Symbol),
   Quelle, Siteaccess, maskierten Client und Anfrage. Einträge derselben Anfrage tragen dieselbe Markierung. Das
   Verfolgen arbeitet mit kurzen Anfragen: **Anhalten** stoppt, **Neueste** springt ans Ende.
4. **Suchen** (3 Minuten). Suchen Sie in **Diese Datei**, **Alle Kernel-Logs** oder **Alle Logdateien**, mit
   **Rotierte Dateien einbeziehen**; filtern Sie nach Stufen, Zeitraum, **Siteaccess**, **Anfrageadresse enthält**,
   **Client-IP beginnt mit** und **Fehlerreferenz**. Eine lange Suche wird mit **Weitere Ergebnisse** fortgesetzt.
5. **Datenschutz und Teilen** (2 Minuten). Adressen, IPs, Tokens und Sitzungs-IDs werden standardmäßig maskiert;
   geheime Einstellungen werden nie angezeigt. **Permalink kopieren** liefert eine Adresse ohne Query-String, die
   immer maskiert öffnet.
6. **Übersicht und Zustand** (2 Minuten). **Fehlerübersicht** gruppiert die verschiedenen Fehler und Warnungen der
   letzten 24 Stunden, die häufigsten zuerst. **Zustand der Logs** zeigt die Speicherbelegung, die am schnellsten
   wachsenden Logs, Logs über ihrer Grenze und Dateien, in die der Webserver nicht schreiben kann.

### Häufige Fragen

**Wer darf den Viewer verwenden?** `setup/logs` zum Lesen, `setup/managelogs` zum Rotieren, Leeren oder Entfernen von
Archiven (jeweils nur, wenn in `logviewer.ini [ActionSettings]` eingeschaltet) und `setup/unmasklogs` für
**Unmaskiert zeigen**. Eine neue Installation gibt alle drei nur der Rolle Administrator.

**Kann ich ein Log über die Seite rotieren oder leeren?** Nur wenn die Aktion eingeschaltet ist; standardmäßig ist der
Viewer bis auf Downloads nur lesend. Jede Ansicht und jede Aktion wird im Audit-Protokoll erfasst.

**Warum findet eine Suche eine Adresse nicht, von der ich weiß, dass sie im Log steht?** Eine Suche findet nur, was
Ihnen angezeigt wird. Suchen Sie bei eingeschalteter Maskierung nach der maskierten Form (`203.0.`), oder schalten Sie
**Unmaskiert zeigen** ein, wenn Sie `setup/unmasklogs` haben.

### Stolperfallen

- **Unmaskiert zeigen am Beamer.** Im Kurs nie mit echten Daten; das Audit-Protokoll hält fest, dass die Datei
  unmaskiert gelesen wurde.
- **Live-Zeilen erwarten, obwohl nichts passiert.** Das Verfolgen wird langsamer, solange nichts ankommt. Öffnen Sie in
  einem anderen Tab eine Seite der Schulungs-Website, damit Zeilen erscheinen.
- **Gemischte Eigentümer.** Warnt **Zustand der Logs**, dass Logs gemischte Eigentümer haben (manche von root
  geschrieben), können Rotation und neue Zeilen ausbleiben. Korrigieren Sie die Eigentümer auf dem Server; Lektion 7.1
  hat erklärt, warum.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, als Administrator in der Administration angemeldet.

1. Öffnen Sie **Setup**, dann **Logdateien**. Lesen Sie oben den Namen der geöffneten Datei.
2. Öffnen Sie im Navigator `404.log` (in der Gruppe **Kernel**). Notieren Sie die Adresse in Ihrem Browser: Sie ist der
   Permalink des Logs.
3. Öffnen Sie in einem zweiten Tab zwei Adressen Ihrer Schulungs-Website, die es nicht gibt. Beobachten Sie zurück im
   Viewer, wie die neuen Zeilen ankommen; klicken Sie auf **Anhalten**, dann auf **Neueste**.
4. Öffnen Sie wieder `error.log`. Wählen Sie im Suchformular **Alle Kernel-Logs**, setzen Sie das Häkchen bei
   **Rotierte Dateien einbeziehen** und suchen Sie nach dem Wort `not found` (oder einem Wort, das Ihnen Ihr Trainer
   nennt).
5. Grenzen Sie die Suche auf die Stufen **Fehler** und **Warnung** und einen Zeitraum ein, der die letzte Stunde
   abdeckt.
6. Klicken Sie auf **Permalink kopieren** und fügen Sie die Adresse in einen neuen Tab ein. Dieselbe Suche öffnet sich.
7. Öffnen Sie **Fehlerübersicht**. Schalten Sie das Zeitfenster zwischen **1 h**, **24 h** und **168 h** um und lesen
   Sie die häufigste Meldung.
8. Öffnen Sie **Zustand der Logs**. Lesen Sie **Speicherbelegung**, **Am schnellsten wachsend** und
   **Vom Webserver nicht beschreibbar**.
9. Öffnen Sie **Logs verwalten** und lesen Sie die Grenzen, die für `debug.log` gelten. Drücken Sie keine
   Aktionsschaltfläche.
10. Vergleichen Sie in einem Terminal mit der Kommandozeile: `php bin/php/console exp:logs --search="not found" --scope=kernel --limit=5`.

## Erwartetes Ergebnis

- Schritt 3: Die neuen 404-Zeilen erscheinen, ohne dass die Seite neu geladen wird, jede mit der maskierten
  Client-Adresse.
- Schritt 6 öffnet allein über den Permalink dieselbe gefilterte Ansicht.
- Schritt 7 zeigt Gruppen von Meldungen mit Anzahl und Zeitpunkt des letzten Auftretens; Zahlen und IDs in den
  Meldungen werden ausgeklammert und als `#` oder `?` gezeigt.
- Schritt 8 zeigt eine Speicherbelegung im Verhältnis zum Budget (standardmäßig 256 MB) und im Idealfall keine Datei
  unter **Vom Webserver nicht beschreibbar**.
- Der Trainer prüft das, indem er eine Teilnehmerin oder einen Teilnehmer den Permalink aus Schritt 6 im Kurs-Chat
  teilen lässt; er öffnet sich für alle mit `setup/logs` maskiert.

## Quiz

1. Welche Richtlinie wird für **Unmaskiert zeigen** gebraucht?
   A. `setup/logs` B. `setup/managelogs` C. `setup/unmasklogs` D. `audit/read`
2. Was zeigt die **Fehlerübersicht**?
3. Ein Kollege soll die Zeilen sehen, die Sie gefunden haben. Was schicken Sie?
4. Richtig oder falsch: Auf einer neuen Installation ist **Jetzt rotieren** bei jedem Log verfügbar.
5. Welche Seite warnt, dass manche Logs von root geschrieben wurden und für den Webserver nicht beschreibbar sind?

<details>
<summary>Antworten</summary>

1. **C.** ([Der Log-Viewer, Wer ihn verwenden darf](../../../../funktionen/6.0/log-betrachter.md#wer-ihn-verwenden-darf)).
2. Die verschiedenen Fehler und Warnungen der letzten 24 Stunden (oder eines anderen Zeitfensters), gruppiert und
   gezählt, die häufigsten zuerst ([Fehlerübersicht](../../../../funktionen/6.0/log-betrachter.md#fehlerübersicht)).
3. Den Permalink aus **Permalink kopieren**; er öffnet dieselbe Ansicht, immer maskiert.
4. **Falsch.** Rotieren, Leeren und Entfernen sind aus, bis `logviewer.ini [ActionSettings]` sie einschaltet.
5. **Zustand der Logs** ([Zustand der Logs](../../../../funktionen/6.0/log-betrachter.md#zustand-der-logs)).

</details>

## Weiterführende Literatur

- [Der Log-Viewer](../../../../funktionen/6.0/log-betrachter.md): jeder Bereich, jede Einstellung und jedes
  Tastenkürzel.
- [Log-Rotation und Bereinigung](../../../../funktionen/6.0/log-rotation.md): was **Logs verwalten** tut.
- [Einen Fehler über seine Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md): die Anleitung der
  nächsten Lektion.

## Verwandte Seiten

- [Modul 7: Logs und das Audit-Protokoll](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
