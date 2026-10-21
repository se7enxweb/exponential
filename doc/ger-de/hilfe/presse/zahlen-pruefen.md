---
title: Zahlen prüfen
audience: press
category: press-media
order: 132
summary: Wie Sie jede Zahl zu Exponential prüfen, bevor Sie sie drucken - die Methode dahinter finden, sie im Repository oder auf Packagist und GitHub nachzählen, Datum und Bedingungen festhalten und die Zahlen erkennen, die überhaupt keine Quelle haben.
keywords: Zahlen, Werte, Statistik, prüfen, nachzählen, Commits, Releases, Pakete, Packagist, GitHub, Benchmark, Anfragen pro Sekunde, Sprachen, Datenjournalismus, numbers, figures, statistics, verify, recount, releases, packages, benchmark, requests per second, languages, data journalism
type: help
---
# Zahlen prüfen

Diese Seite ist für Reporter und Datenjournalisten. Eine Zahl zu Exponential ist erst druckbar, wenn Sie wissen, wie
sie gezählt wurde, wann und unter welchen Bedingungen. Die meisten Zahlen in dieser Dokumentation kommen mit einer
Methode, die Sie wiederholen können.

## Inhalt

1. [Drei Fragen zu jeder Zahl](#drei-fragen-zu-jeder-zahl)
2. [Die gängigen Werte nachzählen](#die-gängigen-werte-nachzählen)
3. [Leistungswerte](#leistungswerte)
4. [Zahlen ohne Methode](#zahlen-ohne-methode)
5. [Die Zahl schreiben](#die-zahl-schreiben)

## Drei Fragen zu jeder Zahl

1. **Wie wurde gezählt?** Ein Befehl, eine Seite, ein Messskript. Ohne Methode kein Druck.
2. **Wann?** Zählungen ändern sich mit jedem Commit und jedem Release. Halten Sie das Datum fest.
3. **Unter welchen Bedingungen?** Vor allem bei Geschwindigkeit: welcher Rechner, welche Seite, gecacht oder nicht.

Die gezählten Werte, mit Methoden und Daten, stehen unter [Exponential in Zahlen](../../../eng-us/help/press/exponential-by-the-numbers.md).

## Die gängigen Werte nachzählen

| Wert | Nachzählen mit |
|---|---|
| Commits auf main | `git rev-list --count HEAD` in einem frischen Klon von `se7enxweb/exponential` |
| Releases 6.0.x | `git tag -l 'v6.0.*' --sort=version:refname` (zählen Sie die Zeilen) |
| Übernommene Pull Requests in der Geschichte | `git log --grep="Merge pull request" --oneline \| wc -l` |
| Verlangte Pakete von se7enxweb | Zählen Sie die Zeilen mit `se7enxweb/` im Abschnitt `require` der `composer.json` |
| Pakete von se7enxweb auf Packagist | <https://packagist.org/packages/list.json?vendor=se7enxweb> |
| Öffentliche Repositorys von se7enxweb | Die Zahl der Repositorys auf <https://github.com/se7enxweb> |
| Locale-Dateien und Übersetzungen | [Sprachen in Exponential](../../../eng-us/languages.md) |

Wenn Ihre Zählung von der dokumentierten abweicht, hat sich das Repository wahrscheinlich weiterentwickelt. Drucken
Sie Ihre, mit Ihrem Datum.

## Leistungswerte

Das README dokumentiert Werte für Exponential Velocity: 0,36 ms pro gecachter Seite, 8.941 Anfragen pro Sekunde
dauerhaft auf einem Rechner mit 12 Kernen, null Fehler bis zu 384 Verbindungen, mit niedergeschriebener Methode und
Skripten im Projekt ([Thema: wie die Velocity-Zahlen gemessen werden](thema-wie-die-velocity-zahlen-gemessen-werden.md)).

- Drucken Sie sie mit ihren Bedingungen: „auf einem Rechner mit 12 Kernen, für eine gecachte Seite“.
- Machen Sie daraus keinen Vergleich mit einem anderen Produkt; es ist keiner veröffentlicht.
- Wenn Sie selbst messen, veröffentlichen Sie Hardware, Versionen, Seite, Methode und Daten.

## Zahlen ohne Methode

| Zahl | Status |
|---|---|
| Installationen, Websites, Benutzer | In den Quellen nirgends gezählt |
| Kunden, Umsatz, Mitarbeiter | Nicht veröffentlicht |
| Marktanteil, Rang | Nicht veröffentlicht |
| „Active Properties“, „Forge Projects“ im Pressekit | Ohne Methode aufgeführt ([Das Pressekit](das-pressekit.md#fakten-im-kit-die-veraltet-sind)); fragen Sie vor dem Druck nach |
| Downloadzahlen | Packagist zeigt pro Paket Installationsstatistiken von Composer; sie sind keine Zählung von Websites |

## Die Zahl schreiben

```text
Good:  "more than 27,000 commits (count of 18 October 2026)"
Good:  "15 releases of the 6.0 line between January 2024 and June 2026"
Good:  "8,941 requests per second, measured by the project on a 12-core machine"
Avoid: "about 30,000 commits" when you counted 27,242 (round down, not up)
Avoid: "thousands of users"
```

## Verwandte Seiten

- [Exponential in Zahlen](../../../eng-us/help/press/exponential-by-the-numbers.md)
- [Leitfaden zum Faktencheck](../../../eng-us/help/press/fact-checking-guide.md)
- [Was die Quellen nicht sagen](was-die-quellen-nicht-sagen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
