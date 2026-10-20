---
title: Fehler gut melden
audience: advocate
category: advocacy
order: 102
summary: Wie Fürsprecher Fehlerberichte schreiben, die behoben werden, und es anderen beibringen - zuerst suchen, ein Problem pro Issue, Schritte zum Nachstellen, erwartetes und tatsächliches Ergebnis, Umgebung von der Seite Fehler melden, sichere Logs - mit einer Vorlage und einer Checkliste zur Prüfung.
keywords: Fehlerbericht, Issue, nachstellen, Schritte, erwartet, tatsächlich, Umgebung, Fehler melden, Systeminformationen, Logs, Vorlage, GitHub-Issues, bug report, issue, reproduce, steps, expected, actual, environment, Report an error, System information, logs, template, GitHub issues
type: help
---
# Fehler gut melden

Diese Seite ist für Fürsprecher, die Fehler melden und anderen beim Melden helfen. Ein guter Bericht ist der schnellste
Weg, ein Problem für alle zu beheben. Die eigene Anleitung des Projekts steht in
[Wenn etwas schiefgeht, Eine gute Fehlerbeschreibung](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung); diese
Seite macht daraus eine Routine.

## Inhalt

1. [Bevor Sie melden](#bevor-sie-melden)
2. [Die Teile eines guten Berichts](#die-teile-eines-guten-berichts)
3. [Die Vorlage](#die-vorlage)
4. [Die Umgebung sicher ermitteln](#die-umgebung-sicher-ermitteln)
5. [Den Bericht eines anderen prüfen](#den-bericht-eines-anderen-prüfen)
6. [Nach der Meldung](#nach-der-meldung)

## Bevor Sie melden

1. Ist es ein Fehler oder eine Frage? Fragen gehören in die Foren ([Welcher Tracker für welche Meldung](welcher-tracker-fuer-welche-meldung.md)).
2. Ist es ein Sicherheitsproblem? Dann vertraulich ([Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md)).
3. Durchsuchen Sie den Tracker; wenn es ihn schon gibt, ergänzen Sie dort, was Sie wissen.
4. Können Sie ihn auf einer frischen Installation nachstellen? Wenn nicht, liegt es vielleicht an der Einrichtung der Website.

## Die Teile eines guten Berichts

| Teil | Beispiel |
|---|---|
| Kurzer Titel, der das Problem nennt | *Upload page: ZIP file with a folder fails with "temp_dir"* |
| Schritte zum Nachstellen | 1. Die Medienbibliothek öffnen. 2. Eine ZIP-Datei mit einem Ordner hochladen. 3. ... |
| Erwartetes Ergebnis | Die Dateien werden importiert |
| Tatsächliches Ergebnis, genaue Meldung | Der genaue Fehlertext |
| Umgebung | Exponential-Version, PHP, Engine, Datenbank, Design |
| Logzeilen | Um den Zeitpunkt herum, ohne private Teile |

Das Beispiel für den Titel stammt aus [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung).

## Die Vorlage

```text
Title: <Where>: <what goes wrong>

Steps to reproduce
1. ...
2. ...

Expected
...

Actual
... (exact message, screenshot with invented data)

Environment
Exponential <version or commit>, PHP <version>, <Velocity | FrankenPHP | Apache/nginx + PHP-FPM>,
<database>, <admin4 | classic | AdminUI>

Logs (private data removed)
...

Notes
Reproduced on a fresh installation: <yes/no>. Related: <links>.
```

## Die Umgebung sicher ermitteln

- Die Seite **Fehler melden** der Hilfe (`exphelp/report`) füllt eine Zusammenfassung der Installation aus.
- **Setup > Systeminformationen**, **Bericht als Text kopieren**, liefert dieselbe Zusammenfassung.
- Beide lassen Passwörter, Schlüssel und Serverpfade weg ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung)).
- Entfernen Sie IP-Adressen, E-Mail-Adressen und Namen aus Logs, bevor Sie sie veröffentlichen.

## Den Bericht eines anderen prüfen

Helfen Sie der meldenden Person mit einem freundlichen Kommentar, ihr Issue zu verbessern:

```text
[ ] Can I follow the steps? If not, ask for the missing one
[ ] Is the exact message there?
[ ] Is the environment there?
[ ] Is there anything private in it? (then ask them to edit it out at once)
[ ] Can I reproduce it? (then say so, with my environment)
```

Siehe [Issues nachstellen und bestätigen](issues-nachstellen-und-bestaetigen.md).

## Nach der Meldung

- Beantworten Sie Fragen im Issue zügig.
- Wenn Sie die Ursache finden, überlegen Sie, die Korrektur zu senden ([Noch besser: die Korrektur senden](../wenn-etwas-schiefgeht.md#noch-besser-die-korrektur-mitschicken)).
- Wenn es behoben ist, danken Sie den Beteiligten.

## Verwandte Seiten

- [Welcher Tracker für welche Meldung](welcher-tracker-fuer-welche-meldung.md)
- [Issues nachstellen und bestätigen](issues-nachstellen-und-bestaetigen.md)
- [GitHub für Fürsprecher](github-fuer-fuersprecher.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
