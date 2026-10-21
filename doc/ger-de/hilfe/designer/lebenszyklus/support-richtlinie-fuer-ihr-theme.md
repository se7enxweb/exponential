---
title: "Eine Support-Richtlinie für Ihr Theme"
audience: designer
category: theme-lifecycle
order: 30
summary: Aufschreiben, welche Versionen eines Themes Sie pflegen, mit welchen Versionen von Exponential und PHP, und was eine ältere Version bekommt - nach dem Vorbild der Tabelle unterstützter Versionen in der eigenen Sicherheitsrichtlinie von Exponential.
keywords: Support-Richtlinie, unterstützte Versionen, gepflegte Versionen, Versionstabelle, SECURITY.md, ältere Versionen, Upgrade-Pfad, support policy, supported versions, maintained versions, version table, older versions, upgrade path
type: help
---
# Eine Support-Richtlinie für Ihr Theme

Diese Seite ist für Designer, die möchten, dass Nutzer wissen, welche Versionen ihres Themes Korrekturen bekommen.

## Das Vorbild

Die `SECURITY.md` von Exponential hat eine Tabelle der unterstützten Versionen: Die aktuelle Linie 6.0 auf `main` und der
nächste daraus geschnittene Tag werden unterstützt; veröffentlichte Tags werden „durch ein Upgrade auf das neueste 6.0.x
behoben“; ältere Major-Linien werden nicht unterstützt. Sie sagt auch, dass ein veröffentlichter Tag nie geändert wird:
Eine Korrektur kommt als nächstes Release.

## Eine Tabelle für Ihr Theme

| Theme-Version | Exponential | PHP | Status |
|---|---|---|---|
| 1.0.x (neueste) | 6.0 | 8.0 bis 8.5 | Gepflegt |
| 0.9.x | 6.0 | 8.0 bis 8.4 | Behoben durch ein Upgrade auf 1.0.x |
| älter | - | - | Nicht unterstützt |

Füllen Sie sie mit dem, was Sie tatsächlich testen und beheben.

## Schritt für Schritt

1. Entscheiden Sie, wie viele Linien Sie gleichzeitig pflegen. Für eine Person ist eine Linie realistisch.
2. Schreiben Sie die Tabelle in `SECURITY.md` und das README.
3. Nennen Sie den Upgrade-Pfad für ältere Versionen („Upgrade auf 1.0.x; siehe das Changelog für Änderungen“).
4. Aktualisieren Sie die Tabelle bei jedem Release, das sie ändert.

## Bewährte Praxis

- Pflegen Sie die neueste Linie; bitten Sie Nutzer älterer Linien um ein Upgrade.
- Beheben Sie eine alte Version nie, indem Sie ihren Tag verschieben; eine Korrektur für eine alte Linie ist ein neues
  Release dieser Linie ([Ein gepushter Tag ist endgültig](../veroeffentlichung/ein-gepushter-tag-ist-endgueltig.md)).

## Verwandte Seiten

- [Eine Kompatibilitätsmatrix](kompatibilitaetsmatrix.md)
- [Eine Sicherheitsrichtlinie für ein Theme](../veroeffentlichung/sicherheitsrichtlinie-fuer-ein-theme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
