---
title: Dokumentation beitragen
audience: supporter
category: supporting
order: 40
summary: Wie Sie die Dokumentation von Exponential und diese Hilfe verbessern – eine Seite über „Edit on GitHub“ korrigieren, eine neue Hilfeseite mit Front Matter schreiben, Stilregeln, Quellen, die GFDL und weitere Orte zum Schreiben (eZpedia, share.exponential.earth).
keywords: Dokumentation, Doku, Hilfe, schreiben, Edit on GitHub, Front Matter, Markdown, GFDL, eZpedia, Tutorial, Anleitung, Stil, documentation, docs, help, write, edit on GitHub, front matter, tutorial, how-to, style
type: help
---
# Dokumentation beitragen

Diese Seite ist für alle, die die Dokumentation verbessern möchten. Sie brauchen ein GitHub-Konto, aber keine
Programmierkenntnisse.

## Eine Seite in zwei Minuten korrigieren

Jede Hilfeseite hat einen Link „Edit on GitHub“. Öffnen Sie ihn, ändern Sie den Text im Browser und schlagen Sie die
Änderung als Pull-Request vor. Tippfehler, veraltete Schritte, unklare Sätze: Alles ist willkommen.

## Eine neue Hilfeseite schreiben

Die Hilfe zeigt die von Git verwalteten Dateien in `doc/`. Eine Hilfeseite ist eine Markdown-Datei unter
`doc/eng-us/help/`, die mit Front Matter beginnt:

```text
---
title: <Title>
audience: editor, admin
category: editing
order: 50
summary: <One or two sentences: who it is for and what it answers.>
keywords: <comma separated>
type: help
---
# <Title>

<First paragraph: who the page is for and what it answers.>
```

Die Werte für Zielgruppe (`audience`) und Kategorie (`category`), die die Hilfe versteht, sind in
`settings/exphelp.ini` konfiguriert.

## Ein Stil, der Seiten vertrauenswürdig macht

- Amerikanisches Englisch; „Exponential“ für das Produkt; den alten Namen eZ Publish nur für die Geschichte.
- Jede Tatsache mit ihrer Quelle: eine Datei, eine Seite oder ein Befehl mit seiner erwarteten Ausgabe.
- Schritte, die auf einer aktuellen Installation funktionieren; nennen Sie die Version, mit der Sie getestet haben.
- Keine Geheimnisse, keine privaten Hostnamen, keine personenbezogenen Daten in Beispielen.
- Tabellen und Checklisten, wo sie helfen; kurze Sätze.

## Lizenz

Die Dokumentation ist freie Dokumentation unter der GNU Free Documentation License 1.3 oder höher
([Die Lizenz dieser Dokumentation](../../README-LIZENZ.md)). Ihr Beitrag wird unter derselben Lizenz veröffentlicht,
damit andere ihn kopieren, übersetzen und verbessern dürfen ([Freie Dokumentation](../freie-dokumentation.md)).

## Weitere Orte zum Schreiben

| Ort | Was | Lizenz |
|---|---|---|
| eZpedia <https://ezpedia.exponential.earth/> | „Die freie eZ-Publish-Enzyklopädie, die jeder bearbeiten kann“, seit 2006 | GFDL |
| share.exponential.earth <https://share.exponential.earth/> | Foren, Blogs, Tutorials | Wie auf der Site angegeben |
| Ihr eigenes Blog | Tutorials und Erfahrungsberichte | Ihre Wahl; eine freie Lizenz hilft anderen, sie weiterzuverwenden |

## Was die Dokumentation am dringendsten braucht

- Anleitungen zu Aufgaben, nach denen in den Foren gefragt wird.
- Bildschirmfotos der aktuellen Administration in Tutorials, die noch alte zeigen.
- Übersetzungen von Hilfeseiten ([Übersetzungen beitragen](uebersetzungen-beitragen.md)).

## Verwandte Seiten

- [Übersetzungen beitragen](uebersetzungen-beitragen.md)
- [Freie Dokumentation](../freie-dokumentation.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
