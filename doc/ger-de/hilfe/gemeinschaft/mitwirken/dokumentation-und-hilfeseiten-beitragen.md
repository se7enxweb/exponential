---
title: Dokumentation und Hilfeseiten beitragen
audience: community, supporter, editor, advocate, trainer
category: community
order: 26
summary: Wie Community-Mitglieder die Exponential-Hilfe und -Dokumentation verbessern - eine Seite über „Auf GitHub bearbeiten“ korrigieren, eine neue Hilfeseite mit dem richtigen Front Matter schreiben, die Stilregeln, Links mit exp:help check prüfen, die GNU FDL und andere Orte zum Schreiben.
keywords: Dokumentation, Hilfeseiten, Auf GitHub bearbeiten, Front Matter, Markdown, exp:help check, GNU FDL, Stil, amerikanisches Englisch, eZpedia, Share, schreiben, documentation, help pages, Edit on GitHub, style, American English, writing
type: help
---
# Dokumentation und Hilfeseiten beitragen

Die Hilfe, die Sie gerade lesen, ist freie Dokumentation, in Markdown geschrieben und im Repository unter
`doc/eng-us/` abgelegt. Alle können sie verbessern, und Programmierkenntnisse sind nicht nötig.

## Inhalt

1. [Eine Seite korrigieren](#eine-seite-korrigieren)
2. [Eine neue Seite schreiben](#eine-neue-seite-schreiben)
3. [Stilregeln](#stilregeln)
4. [Ihre Seite prüfen](#ihre-seite-prüfen)
5. [Die Lizenz](#die-lizenz)
6. [Andere Orte zum Schreiben](#andere-orte-zum-schreiben)

## Eine Seite korrigieren

Jede Hilfeseite hat einen Link „Auf GitHub bearbeiten“. Öffnen Sie ihn, ändern Sie den Text im Browser und schlagen
Sie die Änderung als Pull Request vor ([Dokumentation beitragen](../../unterstuetzer/dokumentation-beitragen.md)).
Tippfehler, veraltete Schritte und unklare Sätze sind alle willkommen.

## Eine neue Seite schreiben

Eine Hilfeseite ist eine Markdown-Datei unter `doc/eng-us/help/`, die mit Front Matter beginnt:

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
```

Die Werte für audience und category, die die Hilfe versteht, sind in `settings/exphelp.ini` konfiguriert
([Dokumentation beitragen](../../unterstuetzer/dokumentation-beitragen.md)). Hilfeseiten enden mit einer Liste
„Verwandte Seiten“. Die Hilfe zeigt die Dateien, die Git verfolgt; eine neue Seite erscheint also, sobald sie
committet ist.

## Stilregeln

Aus [Dokumentation beitragen](../../unterstuetzer/dokumentation-beitragen.md):

- Amerikanisches Englisch; „Exponential“ für das Produkt; der alte Name nur für die Geschichte.
- Jede Tatsache mit ihrer Quelle: eine Datei, eine Seite oder ein Befehl mit seiner erwarteten Ausgabe.
- Schritte, die auf einer aktuellen Installation funktionieren; nennen Sie die Version, mit der Sie getestet haben.
- Keine Geheimnisse, keine privaten Hostnamen, keine personenbezogenen Daten in Beispielen.
- Tabellen und Checklisten, wo sie helfen; kurze Sätze.

## Ihre Seite prüfen

In einem Klon prüft `./console exp:help check` „die Links und Bilder der Seiten“ (`bin/php/exphelp.php`); beheben Sie
jeden aufgeführten Fehler, bevor Sie den Pull Request eröffnen. `./console exp:help show <page id>` zeigt die
Metadaten und Überschriften einer Seite. Das Hilfesystem beschreibt [Das Hilfesystem](../../../funktionen/6.0/hilfesystem.md).

## Die Lizenz

Die Dokumentation steht unter der GNU Free Documentation License 1.3 oder neuer; Ihr Beitrag wird unter derselben
Lizenz veröffentlicht, sodass andere ihn kopieren, übersetzen und verbessern dürfen
([Freie Dokumentation](../../freie-dokumentation.md)).

## Andere Orte zum Schreiben

| Ort | Was | Seite |
|---|---|---|
| [eZpedia](https://ezpedia.exponential.earth/) | Eine Community-Enzyklopädie unter der GFDL, für Kniffe und Anleitungen | [Zu eZpedia beitragen](../../fuersprecher/zu-ezpedia-beitragen.md) |
| [Share](https://share.exponential.earth/) | Blogs, Artikel, Tutorials | [Auf Share und Projects veröffentlichen](../../fuersprecher/auf-share-und-projects-veroeffentlichen.md) |

## Verwandte Seiten

- [Als Community-Mitglied mitwirken](README.md)
- [Die Hilfe verbessern](../../fuersprecher/die-hilfe-verbessern.md)
- [Die Hilfe aktuell halten](../../hilfethemen-pflegen.md)
- [Die Hilfe korrekt halten](../anderen-helfen/die-hilfe-korrekt-halten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
