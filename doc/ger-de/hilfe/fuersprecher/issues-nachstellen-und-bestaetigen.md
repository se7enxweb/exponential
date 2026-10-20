---
title: Issues nachstellen und bestätigen
audience: advocate
category: advocacy
order: 103
summary: Wie Fürsprecher den Betreuern helfen, indem sie gemeldete Fehler auf einer frischen Installation nachstellen, bestätigen oder eingrenzen und mit nützlichen, sicheren Informationen kommentieren - eine Routine Schritt für Schritt mit Vorlagen für Kommentare.
keywords: nachstellen, bestätigen, Sichtung, Issues, Fehler, frische Installation, eingrenzen, Kommentar, Umgebung, GitHub, Betreuer, helfen ohne Code, reproduce, confirm, triage, issues, bug, fresh installation, narrowing down, comment, environment, maintainers, help without code
type: help
---
# Issues nachstellen und bestätigen

Diese Seite ist für Fürsprecher, die beim Beheben von Fehlern helfen möchten, ohne Code zu schreiben. Wer einen
gemeldeten Fehler auf einer anderen Installation nachstellt, sagt den Betreuern, dass er echt ist und wo er liegt. Das
ist einer der nützlichsten Beiträge überhaupt.

## Inhalt

1. [Ein Issue auswählen](#ein-issue-auswählen)
2. [Es nachstellen](#es-nachstellen)
3. [Es eingrenzen](#es-eingrenzen)
4. [Kommentieren](#kommentieren)

## Ein Issue auswählen

Öffnen Sie die [Issues von Exponential](https://github.com/se7enxweb/exponential/issues) oder die
[Issues von Velocity](https://github.com/se7enxweb/exponential-velocity/issues) und wählen Sie eines mit Schritten, denen
Sie auf Ihrer eigenen Installation folgen können. Überspringen Sie alles, was nach einem Sicherheitsproblem aussieht;
das gehört in vertrauliche Kanäle.

## Es nachstellen

1. Legen Sie eine frische Installation mit erfundenen Daten an ([Installation mit einem Befehl](../../../en/us/features/6.0/install-in-one-command.md))
   oder nutzen Sie Ihre Testinstallation. Nie eine Produktiv-Website oder die Website eines anderen.
2. Verwenden Sie wenn möglich die gemeldete Version, dann die aktuelle.
3. Folgen Sie den Schritten genau.
4. Schreiben Sie auf, was Sie sehen, mit der genauen Meldung.

## Es eingrenzen

| Versuchen Sie | Sagt den Betreuern |
|---|---|
| Eine andere Datenbank (SQLite oder ein Server) | Ob es datenbankspezifisch ist |
| Eine andere Engine (Velocity oder PHP-FPM) | Ob es enginespezifisch ist ([Welcher Tracker](welcher-tracker-fuer-welche-meldung.md)) |
| Ein anderes Design (admin4 oder classic) | Ob es ein Template-Problem ist |
| Das neueste `main` | Ob es bereits behoben ist ([Die Entwicklungslinie testen](die-entwicklungslinie-testen.md)) |
| Weniger Schritte | Die kleinste Nachstellung |

## Kommentieren

```text
REPRODUCED
I can reproduce this on Exponential <version/commit>, PHP <version>, <engine>, <database>, <design>,
fresh installation. Steps as described; result: <exact message>.
Not reproducible with <other database/engine>, so it seems specific to <...>.

NOT REPRODUCED
I could not reproduce this on <environment>, following the steps exactly. Could you share <missing detail>?

ALREADY FIXED
On the current main (<commit>) this no longer happens; on <older version> it does.
```

Lassen Sie Passwörter, Schlüssel, Serverpfade und personenbezogene Daten weg
([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung)). Nutzen Sie eine Reaktion statt
Kommentaren mit „+1“ ([GitHub für Fürsprecher](github-fuer-fuersprecher.md)).

## Verwandte Seiten

- [Fehler gut melden](fehler-gut-melden.md)
- [Die Entwicklungslinie testen](die-entwicklungslinie-testen.md)
- [Einen Beitrags-Sprint vorschlagen](einen-beitrags-sprint-vorschlagen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
