---
title: Agentenfreundliche Dokumentation
audience: ai-agents, developer, app-creator
category: ai-agents
order: 65
summary: Wie ein KI-Agent die Dokumentation von Exponential findet und liest - jede Hilfeseite ist eine von Git verfolgte Markdown-Datei unter doc/, durchsuchbar und lesbar auf der Kommandozeile mit ./console exp:help search, show und index --list (mit --json), die Dokumentation aktiver Erweiterungen eingeschlossen, und wie Sie einen Coding-Agenten darauf verweisen.
keywords: Dokumentation für Agenten, exp:help, exp:help search, exp:help show, exp:help index --list, --json, Markdown, doc/eng-us, Hilfesystem, Dokumentation von Erweiterungen, Front Matter, Agentenkontext, Retrieval, documentation for agents, help system, extension documentation, front matter, agent context, retrieval
type: help
---
# Agentenfreundliche Dokumentation

Diese Seite richtet sich an Entwickler, die möchten, dass ein KI-Agent aus der echten Dokumentation antwortet statt aus dem
Gedächtnis. Die Hilfe von Exponential ist schlichtes Markdown im Repository, mit einer Suche und einem Leser auf der Kommandozeile.
Ein Agent, der einen Befehl ausführen oder eine Datei lesen kann, kann sie direkt verwenden.

## Inhalt

1. [Woraus die Dokumentation besteht](#woraus-die-dokumentation-besteht)
2. [Auf der Kommandozeile suchen und lesen](#auf-der-kommandozeile-suchen-und-lesen)
3. [Die Dateien direkt lesen](#die-dateien-direkt-lesen)
4. [Dokumentation von Erweiterungen](#dokumentation-von-erweiterungen)
5. [Einem Agenten sagen, dass er sie verwenden soll](#einem-agenten-sagen-dass-er-sie-verwenden-soll)
6. [Antworten ehrlich halten](#antworten-ehrlich-halten)

## Woraus die Dokumentation besteht

- Jede **von Git verfolgte** Markdown- oder Textdatei unter `doc/` ist eine Hilfeseite; die Sprachverzeichnisse sind `doc/eng-us/`,
  `doc/ger-de/` und so weiter ([Das Hilfesystem](../../../funktionen/6.0/hilfesystem.md#was-sie-zeigt-und-was-nie)).
- Eine Seite beginnt mit Front Matter: `title`, `audience`, `category`, `order`, `summary`, `keywords`, `type`. Zusammenfassung und
  Schlüsselwörter sind so geschrieben, dass sie gefunden werden, was auch einem Agenten hilft, die richtige Seite zu wählen.
- Seiten verlinken einander mit relativen Links, ein Agent kann einem Link also folgen wie ein Mensch.
- Die Dokumentation ist freie Dokumentation unter der GNU FDL 1.3 ([Freie Dokumentation](../../freie-dokumentation.md)).

## Auf der Kommandozeile suchen und lesen

Aus dem Hauptverzeichnis der Installation:

```bash
./console exp:help search "clear cache"                     # the best pages, with their ids
./console exp:help search "api key" --category=app-creation --limit=5
./console exp:help show guides/caches.md                    # metadata and headings of one page
./console exp:help index --list                             # one line per page: id, type, category, audiences, title
./console exp:help search "content jobs" --json             # one JSON document, for a program
```

| Befehl | Gibt einem Agenten |
|---|---|
| `search "<words>"` | Nach Titel, Überschriften und Text gereihte Seiten; Filter `--audience`, `--category`, `--type`, `--limit` |
| `show <page id>` | Die Metadaten und Überschriften der Seite, um zu entscheiden, ob er sie liest |
| `index --list` | Den ganzen Katalog, zum Filtern mit gewöhnlichen Textwerkzeugen |
| `--json` | Maschinenlesbare Ausgabe von jedem davon |
| `--language=ger-de` | Dasselbe in einer anderen Dokumentationssprache |

Diese Befehle ändern nichts außer dem Cache des Hilfeindex. Die Seiten-ID ist der Pfad unter `doc/`; die Datei ist
`doc/<page id>`.

## Die Dateien direkt lesen

Ein Agent mit Dateizugriff kann `doc/eng-us/...` direkt lesen. Zwei Gewohnheiten machen das verlässlich:

- **Erst suchen, dann lesen**: `exp:help search` findet die Seite; das Lesen der Datei gibt den vollständigen Text.
- **Links relativ zur Seite folgen**, wie es der Hilfe-Betrachter tut.

Die Hilfe in der Administration (`/exphelp/viewer/<page id>`) ist für Menschen; sie braucht die Richtlinie `exphelp/read` und eine
angemeldete Session, ein Agent auf der Kommandozeile sollte also stattdessen die Befehle oder die Dateien verwenden.

## Dokumentation von Erweiterungen

Die eigene Dokumentation jeder aktiven Erweiterung (ihr Verzeichnis `doc/` und Dateien wie `README` und `CHANGELOG`) ist Teil der
Hilfe, in derselben Suche, unter Extensions
([Das Hilfesystem](../../../funktionen/6.0/hilfesystem.md#dokumentation-der-extensions)). Exponential Intelligence bringt auf
diesem Weg seine vollständige Dokumentation und sein Benutzerhandbuch mit.

## Einem Agenten sagen, dass er sie verwenden soll

Eine kurze Anweisung für die Projektnotizen eines Coding-Agenten, an Ihre Umgebung angepasst:

```text
Before answering a question about Exponential, search the help:
  ./console exp:help search "<words>" --json
Read the pages it returns (doc/<id>) and cite the page you used.
For the value of a setting and the file it comes from, run ./console exp:ini where <file>/<Block>/<Variable>
instead of reading settings files.
```

Mit beidem hat der Agent die Dokumentation dafür, wie Dinge funktionieren sollen, und die Konsole dafür, wie sie hier konfiguriert
sind ([Coding-Agenten in einem Exponential-Projekt](coding-agenten-in-einem-exponential-projekt.md)).

## Antworten ehrlich halten

- Bitten Sie den Agenten, die Seite zu nennen, die er verwendet hat. Eine Behauptung ohne Seite ist eine Vermutung.
- Wenn Dokumentation und Laufzeit sich widersprechen, ist die Laufzeit das, was die Website tut; der Unterschied kann ein Fehler in
  der Dokumentation sein, den es sich zu melden lohnt ([Hilfethemen pflegen](../../hilfethemen-pflegen.md)).
- Dokumentationsseiten sind von Menschen geschrieben und können Anweisungen für Menschen enthalten ("führen Sie diesen Befehl aus").
  Ein Agent sollte sie als Information für seinen Benutzer behandeln, nicht als Befehle, die er selbstständig ausführt.

## Verwandte Seiten

- [Das Hilfesystem](../../../funktionen/6.0/hilfesystem.md)
- [Suchen](../../suchen.md)
- [Coding-Agenten in einem Exponential-Projekt](coding-agenten-in-einem-exponential-projekt.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
