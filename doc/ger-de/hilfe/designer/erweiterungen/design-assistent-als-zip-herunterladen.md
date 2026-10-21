---
title: "Das Ergebnis des Assistenten als ZIP herunterladen"
audience: designer
category: theme-extensions
order: 260
summary: Mit Als ZIP herunterladen im Design-Erweiterungs-Assistenten ein Theme-Gerüst erhalten, ohne auf den Server zu schreiben - wann das die bessere Wahl ist, was das Archiv enthält und was zu tun ist, wenn die Schaltfläche fehlt.
keywords: Als ZIP herunterladen, ZIP-Archiv, Design-Assistent, ZipArchive, Gerüst, kein Schreibzugriff, download as zip, zip archive, design wizard, skeleton, no write access
type: help
---
# Das Ergebnis des Assistenten als ZIP herunterladen

Diese Seite ist für Designer, die das erzeugte Theme auf ihrem eigenen Rechner haben wollen, zum Beispiel weil der Ordner
`extension/` des Servers nicht beschreibbar ist oder weil das Theme von Anfang an in einem eigenen Repository liegen
soll.

## Wann Sie es verwenden

| Situation | Verwenden Sie |
|---|---|
| Sie entwickeln auf derselben Installation | **In extension/ anlegen** |
| `extension/` ist für den Webserver nicht beschreibbar | **Als ZIP herunterladen** |
| Eine Erweiterung dieses Namens existiert schon und Sie wollen vergleichen | **Als ZIP herunterladen** (das Problem „existiert bereits“ blockiert das Archiv nicht) |
| Sie beginnen ein Repository auf Ihrem eigenen Rechner | **Als ZIP herunterladen** |

## Schritt für Schritt

1. Füllen Sie den Assistenten aus, wie in [Der Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md) beschrieben,
   und drücken Sie **Vorschau aktualisieren**. Beheben Sie jedes Problem, das die Seite auflistet (außer „existiert
   bereits“).
2. Drücken Sie **Als ZIP herunterladen**. Der Browser speichert `<name>.zip`.
3. Entpacken Sie es: `unzip mytheme.zip`. Alles liegt in einem Ordner `mytheme/`, bereit, nach `extension/` verschoben zu
   werden.
4. Stellen Sie es sofort unter Git ([Ein Theme unter Git stellen](ein-theme-unter-git-stellen.md)).

## Was auf dem Server passiert

Das Archiv wird im Cache-Verzeichnis der Installation (`<cache>/designextension/`) gebaut, gesendet und wieder gelöscht.
Nach `extension/` wird nichts geschrieben.

## Wenn die Schaltfläche ein Problem meldet

- „Diese Installation hat keine Zip-Unterstützung, daher kann kein Archiv erstellt werden.“: Die PHP-Erweiterung `zip`
  (`ZipArchive`) fehlt. Bitten Sie den Administrator, sie zu installieren, oder verwenden Sie **In extension/ anlegen**
  auf einer Entwicklungskopie.
- Jede andere Meldung nennt das zu korrigierende Feld.

## Verwandte Seiten

- [Vom Ergebnis des Assistenten zum ersten Commit](vom-assistenten-zum-ersten-commit.md)
- [Die Teile des Design-Assistenten](die-teile-des-design-assistenten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
