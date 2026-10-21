---
title: Exponential Projects (projects.exponential.earth)
audience: user, developer, designer, app-creator
category: extension-development
order: 120
summary: Exponential Projects, die Projektplattform, die projects.ez.no ersetzt hat - Tausende Projekte nach Typ, wie Sie einen Eintrag lesen und wie Sie wiederverwenden, was Sie finden, ohne das Urheberrecht zu verletzen.
keywords: Projektplattform, Erweiterungsverzeichnis, Template-Operator, Datentyp, Modul, Workflow-Ereignis, Login-Handler, Downloads, Geschichte, projects.exponential.earth, project forge, projects.ez.no, extensions directory, template operator, datatype, module, workflow event, login handler, downloads, history
type: help
---
# Exponential Projects (projects.exponential.earth)

Teil 1, Kapitel 12. [Exponential Projects](https://projects.exponential.earth/) ist eine Projektplattform: ein
Verzeichnis von Erweiterungen und anderen Projekten mit ihren Neuigkeiten, Downloads, Foren und Quellcode-Links. Sie
bezeichnet sich selbst als „a responsive projects.ez.no replacement portal“, und ein großer Teil ihres Verzeichnisses
stammt von dieser älteren Plattform, was sie zum größten einzelnen Index historischer Erweiterungen für diese Codebasis
macht. Die Fakten auf dieser Seite haben den Stand Oktober 2026.

## Was es dort gibt

Als diese Seite geschrieben wurde, zählte das Portal **3.934 registrierte Projekte**. Sie lassen sich auf mehrere Arten
durchsuchen:

| Durchsuchen | Adresse | Zeigt |
|---|---|---|
| Nach Typ | [/types](https://projects.exponential.earth/types) | Projekte, gruppiert nach dem, was sie sind |
| Nach Organisation | [/org](https://projects.exponential.earth/org) | Projekte, gruppiert danach, wer sie veröffentlicht |
| Als Baum | [/tree](https://projects.exponential.earth/tree) | Das ganze Verzeichnis |
| Neueste Änderungen am Quellcode | [/changes](https://projects.exponential.earth/changes) | Die neuesten Git-Commits von Projekten mit verbundenem Repository |
| Neuigkeiten | [/news](https://projects.exponential.earth/news) | Ankündigungen |

Die Adresse eines Projekts hat die Form `https://projects.exponential.earth/org/<organization>/<project>`.

## Die Typen, die ein Entwickler ansieht

Der Typ „eZ publish (Via Projects.ez.no)“ ([/types/ez_publish_via_projects_ez_no](https://projects.exponential.earth/types/ez_publish_via_projects_ez_no))
umfasste 338 Projekte in Untertypen, die eins zu eins den Kapiteln dieses Buchs entsprechen:

| Untertyp | Projekte | Kapitel im Buch |
|---|---|---|
| Module | 121 | [Module und Views](../teil-3-erweiterungsentwickler/module-und-views.md) |
| Datatype | 73 | [Datentypen](../teil-3-erweiterungsentwickler/datentypen.md) |
| Template operator | 71 | [Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md) |
| Command line script | 61 | [CLI-Skripte und Konsolenbefehle](../teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md) |
| Workflow event type | 38 | [Workflow-Ereignistypen](../teil-3-erweiterungsentwickler/workflow-ereignistypen.md) |
| Template function | 27 | [Template-Funktionen](../teil-3-erweiterungsentwickler/template-funktionen.md) |
| Admin interface integration | 26 | [Administrationsseiten im admin4-Stil](../teil-3-erweiterungsentwickler/admin-seiten-im-admin4-stil.md) |
| Content classes | 20 | [Inhaltsklassen für Builder](../teil-2-builder/inhaltsklassen-fuer-builder.md) |
| Translation | 14 | [Übersetzungen in einer Erweiterung](../teil-3-erweiterungsentwickler/uebersetzungen-in-einer-erweiterung.md) |
| Extended attribute filter | 11 | [Fetch-Funktionen](../teil-3-erweiterungsentwickler/fetch-funktionen.md) |
| Content action handler | 10 | [Handler, die über Einstellungen benannt werden](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |
| Payment gateway | 9 | [Handler, die über Einstellungen benannt werden](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |
| Search plugin | 9 | [Handler, die über Einstellungen benannt werden](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |
| Login handler | 8 | [Handler, die über Einstellungen benannt werden](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |
| Binary file handler | 7 | [Handler, die über Einstellungen benannt werden](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |

Kleinere Untertypen umfassen Single-Sign-on-Handler, Collaboration-Handler, Benachrichtigungsereignisse und -Handler,
XML-Custom-Tags, Datenbank-Plugins, SOAP-Funktionen sowie Handler für Shop-Konten, Mehrwertsteuer und Versand. Ein
zweiter Typ, „3rd party (via Projects.ez.no)“, umfasste 110 weitere Projekte; die Typen „Exponential“, „Exponential
Platform“ und „Exponential Platform Legacy“ enthalten die aktuellen Distributionen und eine Design-Erweiterung.

## Einen Projekteintrag lesen

Jeder Eintrag in einer Liste zeigt den Namen des Projekts, seinen Status (zum Beispiel Alpha oder Stable), seinen
„unix name“ (meist der Ordnername der Erweiterung), die letzte Aktualisierung, die Version und die Versionen, für die es
geschrieben wurde, sowie eine kurze Beschreibung. Eine Projektseite fügt Neuigkeiten, Downloads, ein Forum, Bilder und,
wo verbunden, den Quellcode auf Git oder GitHub hinzu.

Das Feld „compatible versions“ ist für Sie das wichtigste: Die meisten Projekte in den historischen Typen wurden für
PHP 5 und ältere Releases des Kernels geschrieben. Behandeln Sie sie als **Ideen und Ausgangspunkte**, und folgen Sie
dann [Teil 4](../teil-4-aktualisieren/README.md), um eines auf den neuesten Stand zu bringen, oder suchen Sie zuerst auf
GitHub nach einem gepflegten Fork ([Ideen auf GitHub finden](ideen-auf-github-finden.md)).

## Urheberrecht: verlinken, nicht kopieren

Die Fußzeile des Portals lautet „Copyright © 1998 - 2026 7x (except where otherwise noted). All rights reserved.“ Der
eigene Text des Portals darf also nicht frei kopiert werden: **Verlinken Sie auf Projektseiten**, statt ihre
Beschreibungen wiederzugeben.

Die **Projekte selbst** tragen ihre eigenen Lizenzen, fast immer die GNU GPL, angegeben in ihren Downloads (eine Datei
`LICENSE`, der Kopf der PHP-Dateien, `ezinfo.php`). Für den Code gilt die Lizenz des Projekts, nicht die Fußzeile des
Portals. Lesen Sie sie, bevor Sie etwas wiederverwenden:
[Lizenzen lesen, bevor Sie etwas wiederverwenden](lizenzen-vor-der-wiederverwendung-lesen.md).

## Ein Ablauf, um einen Baustein zu finden

1. Entscheiden Sie, welche Art von Ding Sie brauchen (einen Datentyp, einen Operator, ein Modul).
2. Öffnen Sie seinen Untertyp unter [/types](https://projects.exponential.earth/types) und überfliegen Sie die
   Beschreibungen.
3. Öffnen Sie bei einem vielversprechenden Projekt seine Seite: Status, kompatible Versionen, letzte Aktualisierung,
   Download.
4. Suchen Sie auf GitHub nach demselben unix name; vielleicht gibt es einen gepflegten Fork unter se7enxweb oder
   anderswo.
5. Lesen Sie die Lizenz, dann den Code. Entscheiden Sie: so verwenden, wie es ist, auf den neuesten Stand bringen oder
   nur die Idee übernehmen.

## Verwandte Seiten

- Zurück: [Share Exponential (share.exponential.earth)](share-exponential-earth.md)
- Weiter: [eZpedia (ezpedia.exponential.earth)](ezpedia-exponential-earth.md)
- [Eine alte Erweiterung bewerten, bevor Sie sie verwenden](eine-alte-erweiterung-bewerten.md)
- [Eine verwaiste Erweiterung übernehmen](../teil-5-erweitern-forken-zurueckgeben/eine-verwaiste-erweiterung-uebernehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
