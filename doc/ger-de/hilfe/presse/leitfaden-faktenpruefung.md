---
title: "Leitfaden: Faktenprüfung"
audience: press, advocate
category: press-media
order: 130
summary: Eine Methode, um jeden Beitrag, jedes Skript und jeden Post über Exponential auf Fakten zu prüfen - jede Aussage markieren, einordnen, ihre Primärquelle finden, an der Quelle prüfen, das Datum festhalten und entscheiden, was mit dem geschieht, was sich nicht prüfen lässt - mit einer Rangfolge der Quellen und einem durchgerechneten Beispiel.
keywords: Faktenprüfung, Faktencheck, Verifizierung, prüfen, Quellen, Primärquelle, Aussagen, Genauigkeit, einen Beitrag prüfen, Methode, durchgerechnetes Beispiel, fact-checking, fact check, verification, verify, sources, primary source, claims, accuracy, checking a story, method, worked example
featured: true
type: help
---
# Leitfaden: Faktenprüfung

Diese Seite ist für Faktenprüfer, Redakteure, Reporter und Creator. Exponential lässt sich leichter prüfen als die meiste
Software, weil fast alles in seinem Git-Repository öffentlich ist. Dieser Leitfaden gibt Ihnen eine Methode, die für
einen Nachrichtenbeitrag, ein Videoskript oder einen Social-Media-Post funktioniert.

## Inhalt

1. [Die Methode in sechs Schritten](#die-methode-in-sechs-schritten)
2. [Arten von Aussagen und wo Sie sie prüfen](#arten-von-aussagen-und-wo-sie-sie-prüfen)
3. [Ein durchgerechnetes Beispiel](#ein-durchgerechnetes-beispiel)
4. [Was sich nicht prüfen lässt](#was-sich-nicht-prüfen-lässt)
5. [Ein Protokoll führen](#ein-protokoll-führen)

## Die Methode in sechs Schritten

1. **Markieren Sie jede Aussage.** Namen, Zahlen, Daten, Versionen, „erste“, „einzige“, „schneller“, „kostenlos“,
   Zitate.
2. **Ordnen Sie jede ein**: Fakt, Selbstbeschreibung, Meinung oder Zitat ([Aussagen zuordnen](aussagen-zuordnen.md)).
3. **Finden Sie die Primärquelle** mit [Wo die Fakten stehen, für die Presse](wo-die-fakten-stehen-fuer-die-presse.md).
4. **Prüfen Sie an der Quelle**, nicht in einer Zusammenfassung: Öffnen Sie die Datei, das Tag oder die Seite.
5. **Halten Sie Quelle und Datum fest**, an dem Sie geprüft haben.
6. **Entscheiden Sie**: behalten, korrigieren, zuordnen oder streichen. Eine Aussage ohne Quelle wird gestrichen oder als
   ungeprüft berichtet.

## Arten von Aussagen und wo Sie sie prüfen

| Aussage | Prüfen an | Seite |
|---|---|---|
| Eine Version oder ein Veröffentlichungsdatum | Den Git-Tags | [Daten prüfen](daten-pruefen.md), [Ein Release selbst prüfen](ein-release-selbst-pruefen.md) |
| Eine Zahl | Der dazu genannten Methode, oder neu zählen | [Zahlen prüfen](zahlen-pruefen.md) |
| Ein Zitat | Dem Originaltext oder Ihrer Aufnahme | [Zitate prüfen](zitate-pruefen.md) |
| Ein Produktname | Den Namensregeln | [Namens-Kurzreferenz für Redaktionen](namens-kurzreferenz-fuer-redaktionen.md) |
| Was eine Funktion tut | Ihrer Funktionsseite in `doc/eng-us/features/6.0/` und dem Changelog | [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md) |
| Ein Fakt zur Geschichte | Den Geschichtsseiten und den Zeitleisten | [Zeitleiste für Reporter](zeitleiste-fuer-reporter.md) |
| Ein Fakt zu einer Organisation | Der eigenen Website der Organisation und den beiden Profilseiten | [7x für Reporter](7x-fuer-reporter.md), [Die Exponential Foundation für Reporter](die-foundation-fuer-reporter.md) |
| Eine Lizenz | LICENSE, COPYRIGHT.md, der composer.json des Pakets | [Lizenzen für Reporter erklärt](lizenzen-fuer-reporter-erklaert.md) |
| Ein Vergleich | Der eigenen Dokumentation beider Produkte, mit gleichen Kriterien | [Faire Vergleiche](faire-vergleiche.md) |

## Ein durchgerechnetes Beispiel

Satz im Entwurf:

```text
„Das letzten Monat veröffentlichte eZ Publish 6.0.15 bringt eine KI, die Artikel schreibt und veröffentlicht,
und macht Websites zehnmal schneller, sagt 7x, das das CMS seit 1998 pflegt.“
```

| Aussage | Prüfung | Ergebnis |
|---|---|---|
| „letzten Monat veröffentlicht“ | `git tag -l 'v6.0.*' --sort=version:refname` | Kein Tag v6.0.15: in Entwicklung |
| „eZ Publish 6.0.15“ | Namensregeln | Das Produkt heißt Exponential |
| „eine KI, die schreibt und veröffentlicht“ | [Funktionsseite expai](../../../eng-us/features/6.0/extensions/expai.md) | Schreibt nur Entwürfe, veröffentlicht nie |
| „zehnmal schneller“ | README, Abschnitt Velocity | Kein Vergleich veröffentlicht; nur die dokumentierten Zahlen mit ihren Bedingungen |
| „sagt 7x“ | Gibt es eine Aussage? | Nein; die Fakten stammen aus der Dokumentation |
| „pflegt seit 1998“ | [7x in drei Etappen](7x-in-drei-etappen.md) | Gegründet 2001 als Brookins Consulting; hat die Codebasis 2023 übernommen |

Korrigierter Satz:

```text
„Exponential 6.0.15, das sich in Entwicklung befindet, bringt laut dem Changelog des Projekts Exponential
Intelligence, einen Schreibassistenten, der Entwürfe schreibt, sie aber nie veröffentlicht, und Velocity,
einen in PHP geschriebenen Webserver. 7x leitet das Projekt seit 2023.“
```

## Was sich nicht prüfen lässt

Manche Fragen haben keine öffentliche Antwort: Nutzerzahlen, Kunden, Budgets, Einzelheiten der Leitungsstruktur,
künftige Termine ([Was die Quellen nicht sagen](was-die-quellen-nicht-sagen.md)). Fragen Sie die Organisation, ordnen
Sie die Antwort zu, und wenn es keine gibt, sagen Sie, dass die Information nicht veröffentlicht ist. Füllen Sie die
Lücke nie mit einer Schätzung.

## Ein Protokoll führen

Führen Sie pro Beitrag ein einfaches Protokoll; es erleichtert Korrekturen ([Korrekturen und Aktualisierungen](korrekturen-und-aktualisierungen.md)):

```text
AUSSAGE                              QUELLE                                   GEPRÜFT      ERGEBNIS
6.0.14 veröffentlicht 7. Juni 2026   git tag v6.0.14                          2026-10-18   ok
Foundation gegründet 19. März 2026   foundation.exponential.earth             2026-10-18   ok
„schreibt nur Entwürfe“              doc/eng-us/features/6.0/extensions/expai.md     2026-10-18   ok, zugeordnet
```

## Verwandte Seiten

- [Checkliste: Faktenprüfung](checkliste-faktenpruefung.md)
- [Häufige Fehler in Berichten](haeufige-fehler-in-berichten.md)
- [Ehrlichkeit und Genauigkeit](../fuersprecher/ehrlichkeit-und-genauigkeit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
