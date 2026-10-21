---
title: Die Hilfe verbessern
audience: advocate
category: advocacy
order: 58
summary: Wie Fürsprecher die Hilfeseiten verbessern, die sie gerade lesen - Auf GitHub bearbeiten, ein kleiner Pull Request, das Format der Commit-Nachricht, die GNU FDL, Front Matter, Linkprüfungen - mit den Korrekturen, die am meisten helfen, und dem, was Sie vermeiden sollten.
keywords: die Hilfe verbessern, Auf GitHub bearbeiten, Pull Request, Korrektur der Dokumentation, Tippfehler, Front Matter, Commit-Nachricht, GFDL, Dokumentation beitragen, Ordner doc, exp:help check, improve the help, Edit on GitHub, pull request, documentation fix, typo, front matter, commit message, contributing documentation, doc folder
type: help
---
# Die Hilfe verbessern

Diese Seite ist für Fürsprecher, die in der Hilfe etwas zu verbessern finden: einen Tippfehler, einen veralteten
Schritt, einen fehlenden Satz, eine ganze fehlende Seite. Die Hilfeseiten sind die Markdown-Dateien im Ordner `doc/`
des Repositorys; sie zu verbessern ist also ein Pull Request wie jeder andere
([Mit Git entwickeln](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen)).

## Inhalt

1. [Der schnelle Weg: Auf GitHub bearbeiten](#der-schnelle-weg-auf-github-bearbeiten)
2. [Der gründliche Weg: ein Klon](#der-gründliche-weg-ein-klon)
3. [Was am meisten hilft](#was-am-meisten-hilft)
4. [Regeln für das Schreiben von Hilfeseiten](#regeln-für-das-schreiben-von-hilfeseiten)
5. [Was Sie vermeiden sollten](#was-sie-vermeiden-sollten)

## Der schnelle Weg: Auf GitHub bearbeiten

1. Drücken Sie auf der Hilfeseite **Auf GitHub bearbeiten**. GitHub öffnet die Datei.
2. Drücken Sie den Stift, um zu bearbeiten. GitHub bietet an, das Repository für Sie zu forken.
3. Nehmen Sie Ihre Änderung vor.
4. Schreiben Sie eine Commit-Nachricht, die damit beginnt, was sie tut, zum Beispiel
   `Fixed: The layouts help names the Discard button` ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).
5. Schlagen Sie die Änderung vor und eröffnen Sie den Pull Request. Sagen Sie in einem Satz, was falsch war und wie Sie
   es geprüft haben.

Jeder Pull Request wird übernommen, und die Commits des Beitragenden bleiben erhalten
([Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst)).

## Der gründliche Weg: ein Klon

Für größere Änderungen arbeiten Sie in einem Klon:

```bash
git clone https://github.com/<you>/exponential.git
cd exponential
git switch -c help-fix-layouts
# edit doc/eng-us/help/...
git commit -am "Fixed: The layouts help names the Discard button"
git push -u origin help-fix-layouts
```

In einer Installation meldet `./console exp:help check` defekte Links und Bilder in `doc/`
([Das Hilfesystem, Auf der Kommandozeile](../../funktionen/6.0/hilfesystem.md#auf-der-kommandozeile)). Für Pull Requests am
Kernel beschreibt CONTRIBUTING.md außerdem den Commit-Hook und das Dateimanifest (`make hooks`, `make manifest-fix`).

## Was am meisten hilft

| Korrektur | Beispiel |
|---|---|
| Ein Schritt, der nicht mehr zum Bildschirm passt | Eine Schaltfläche wurde umbenannt |
| Ein fehlender Schritt | „Zuerst die Erweiterung aktivieren“ |
| Ein Link auf die ausführlichere Seite | Von einer Hilfeseite zur Funktionsseite |
| Ein klarerer Satz | Kürzer, aktiv, das Wort auf dem Bildschirm |
| Eine fehlende Seite für eine häufige Frage | Sie haben sie dreimal in den Foren beantwortet |
| Eine Übersetzung einer Seite | „Eine übersetzte Seite hilft jedem Leser dieser Sprache“ ([Freie Dokumentation](../freie-dokumentation.md#anderen-helfen)) |

## Regeln für das Schreiben von Hilfeseiten

- **Front Matter**: Neue Seiten beginnen mit `title`, `audience`, `category`, `order`, `summary`, `keywords` und `type`,
  wie die bestehenden Seiten ([Das Hilfesystem, Front Matter](../../funktionen/6.0/hilfesystem.md#front-matter)).
- **Wörter auf dem Bildschirm**: „Verwenden Sie den Produktnamen, den der Leser auf dem Bildschirm sieht“, und die Namen
  der Schaltflächen genau so, wie sie angezeigt werden (Stilrichtlinie der Hilfe, zitiert in
  [Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md#sprache-und-schreibweise)).
- **Amerikanisches Englisch** auf den englischen Seiten: license, color, behavior.
- **Quellen**: Verlinken Sie die Seite oder Datei, die eine Tatsache belegt.
- **Sicheres Markdown**: Rohes HTML wird in der Hilfe maskiert; verwenden Sie schlichtes Markdown, Tabellen und
  Codeblöcke ([Was sie zeigt und was nie](../../funktionen/6.0/hilfesystem.md#was-sie-zeigt-und-was-nie)).
- **Lizenz**: Ihre Änderung wird unter der GNU FDL 1.3 oder später angenommen; sagen Sie es, wenn Text aus einer Quelle
  mit einer anderen Lizenz stammt ([Zur Dokumentation beitragen](../../README-LIZENZ.md#zur-dokumentation-beitragen)).

## Was Sie vermeiden sollten

- Viele Seiten in einem Pull Request aus unzusammenhängenden Gründen ändern: Bleiben Sie fokussiert.
- Text von Websites oder aus Büchern mit anderen Lizenzen kopieren.
- Personenbezogene Daten, echte Kundennamen, Schlüssel oder Serveradressen in Beispiele aufnehmen.
- Bezeichner im Code (`eZINI`, `ezcontentobject`) in der Dokumentation von Code umbenennen; nur der Produktname ändert
  sich ([Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md#bezeichner-im-code-bleiben-wie-sie-sind)).
- Werbesprache in Hilfeseiten: Die Hilfe erklärt; sie verkauft nicht.

## Verwandte Seiten

- [Für freie Dokumentation eintreten](fuer-freie-dokumentation-eintreten.md)
- [Einen ersten Beitrag begleiten](einen-ersten-beitrag-begleiten.md)
- [Freie Dokumentation](../freie-dokumentation.md#anderen-helfen)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
