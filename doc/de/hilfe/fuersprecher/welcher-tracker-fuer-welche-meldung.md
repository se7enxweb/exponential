---
title: Welcher Tracker für welche Meldung
audience: advocate
category: advocacy
order: 74
summary: Eine Entscheidungstabelle, die jede Art von Meldung an die richtige Stelle schickt - den Issue-Tracker von Exponential, den Tracker von Velocity, das Repository einer Erweiterung, den Community-Tracker, die vertrauliche Sicherheitsadresse, die Foren - mit Beispielen und der Seite Fehler melden.
keywords: Tracker, Issues, Fehlerbericht, wo melden, Entscheidungstabelle, Issues von Exponential, Issues von Velocity, Repository einer Erweiterung, Community-Tracker, Sicherheit, Foren, Fehler melden, tracker, issues, bug report, where to report, decision table, extension repository, community tracker, security, forums, Report an error
type: help
---
# Welcher Tracker für welche Meldung

Diese Seite ist für Fürsprecher, die Menschen beim Melden helfen. Eine Meldung an der richtigen Stelle wird bearbeitet;
eine Meldung an der falschen Stelle wartet oder geht verloren. Die Liste der Tracker stammt aus
[Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden).

## Inhalt

1. [Die Entscheidungstabelle](#die-entscheidungstabelle)
2. [Beispiele](#beispiele)
3. [Die Seite Fehler melden](#die-seite-fehler-melden)
4. [Wenn Sie unsicher sind](#wenn-sie-unsicher-sind)

## Die Entscheidungstabelle

| Es geht um | Wohin | Öffentlich? |
|---|---|---|
| Ein Sicherheitsproblem: etwas sehen oder ändern, was man nicht sollte | security@se7enx.com oder die vertrauliche Meldung von Sicherheitslücken bei GitHub ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) | **Nein, vertraulich** |
| Einen Fehler im Kernel, in der Administration, in einer mitgelieferten Erweiterung oder in der Dokumentation | [Issues von Exponential](https://github.com/se7enxweb/exponential/issues) | Ja |
| Einen Fehler des Velocity-Servers: Start, Worker, sein Antwort-Cache, HTTPS | [Issues von Velocity](https://github.com/se7enxweb/exponential-velocity/issues) | Ja |
| Einen Fehler in einer Erweiterung mit eigenem Repository | Die Issues dieses Repositorys; die Seite **Fehler melden** listet sie auf | Ja |
| Das Projekt, die Website, das Ökosystem, alles, was kein Softwarefehler ist | [Issues der Community](https://github.com/se7enxweb/exponential-community/issues) | Ja |
| Eine Frage „Wie mache ich ...?“ | [Foren](https://share.exponential.earth/forums) oder [Telegram](https://t.me/exponentialcms) | Ja |
| Ein Problem der Einrichtung einer Website (ihre eigenen Templates, ihr Server, ihr Hosting) | Zuerst der Administrator oder Anbieter der Website | Kommt darauf an |
| Eine falsche oder unklare Hilfeseite | Ein Pull Request mit **Auf GitHub bearbeiten** oder ein Issue bei Exponential | Ja |

## Beispiele

| Meldung | Gehört zu |
|---|---|
| „Die Upload-Seite scheitert bei einer ZIP-Datei mit einem Ordner.“ | Issues von Exponential |
| „exp:velocity start hängt auf meinem Rechner.“ | Issues von Velocity |
| „Die Newsletter-Erweiterung sendet ein falsches Datum.“ | Das Repository dieser Erweiterung |
| „Die Website des Projekts hat einen defekten Link.“ | Issues der Community |
| „Ich kann die Entwürfe anderer Benutzer sehen, wenn ich die URL ändere.“ | **Vertraulich**: SECURITY.md |
| „Wie füge ich Artikeln ein Feld hinzu?“ | Foren |
| „Die Hilfeseite zu Sprachen nennt eine Schaltfläche, die es nicht mehr gibt.“ | Pull Request oder Issues von Exponential |
| „Unsere Website ist langsam, seit unser Hoster uns umgezogen hat.“ | Der Administrator oder Hoster der Website |

## Die Seite Fehler melden

In der Administration führt die Seite **Fehler melden** der Hilfe (`exphelp/report`) durch eine Meldung: Sie wählt den
Tracker, füllt eine Zusammenfassung der Installation aus und öffnet ein neues Issue mit vorbereitetem Titel und Text.
Die Zusammenfassung lässt Passwörter, Schlüssel und Serverpfade weg ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden)).
Empfehlen Sie sie allen, die sich anmelden können.

## Wenn Sie unsicher sind

- Wenn es ein Sicherheitsproblem sein könnte, behandeln Sie es als solches: zuerst vertraulich.
- Wenn Sie nicht sagen können, ob es der Kernel oder eine Erweiterung ist, melden Sie es bei den Issues von Exponential
  und sagen Sie das; die Betreuer können es verschieben.
- Wenn es kein Softwarefehler ist, ist der Community-Tracker der richtige Ort.
- Suchen Sie zuerst: Ergänzen Sie ein bestehendes Issue, statt ein Duplikat zu eröffnen.

## Verwandte Seiten

- [Fehler gut melden](fehler-gut-melden.md)
- [Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md)
- [GitHub für Fürsprecher](github-fuer-fuersprecher.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
