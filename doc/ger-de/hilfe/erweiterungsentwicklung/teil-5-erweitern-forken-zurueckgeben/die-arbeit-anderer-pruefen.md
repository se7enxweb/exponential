---
title: Die Arbeit anderer prüfen
audience: developer, designer, app-creator
category: extension-development
order: 120
summary: Wie Sie einen Pull Request an eine Exponential-Erweiterung sinnvoll prüfen - was Sie in welcher Reihenfolge prüfen (Zweck, Sicherheit, Verhalten, Tests, Stil), wie Sie ihn lokal ausprobieren und wie Sie Kommentare schreiben, die helfen.
keywords: Code-Review, Review, Pull Request, Checkliste, Sicherheit, CSP, Tests, Kommentare, Feedback, Maintainer, code review, review, pull request, checklist, security, tests, comments, feedback, maintainer
type: help
---
# Die Arbeit anderer prüfen

Teil 5, Kapitel 12. Prüfen (Review) ist ein eigener Beitrag, und einer, an dem es jedem Projekt fehlt. Sie müssen kein
Maintainer sein, um zu prüfen: Ein sorgfältiger Kommentar eines beliebigen Benutzers, der die Änderung ausprobiert
hat, hilft dem Maintainer bei der Entscheidung. Dieses Kapitel gibt eine Reihenfolge für das Prüfen vor, eine
Checkliste für Exponential-Erweiterungen und eine Art, Kommentare zu schreiben.

## Prüfen Sie in dieser Reihenfolge

Beginnen Sie mit den Fragen, deren Antwort den Rest überflüssig machen kann.

1. **Zweck.** Sagt die Beschreibung, welches Problem dies löst? Ist es ein Problem, das das Projekt gelöst haben
   möchte?
2. **Sicherheit.** Könnte es Inhalte beschädigen, Daten preisgeben oder jemandem erlauben, was er nicht tun sollte?
3. **Verhalten.** Tut es, was es sagt, in den Fällen, auf die es ankommt?
4. **Tests.** Gibt es einen Test, der ohne die Änderung fehlschlägt?
5. **Passung.** Folgt es den Konventionen des Projekts?
6. **Details.** Namen, Kommentare, Tippfehler.

Kommentare zu Schritt 6 bei einer Änderung, die an Schritt 2 scheitert, verschwenden die Zeit aller.

## Eine Checkliste für Exponential-Erweiterungen

| Bereich | Prüfen |
|---|---|
| Eingaben | Werte aus der Anfrage laufen über `eZHTTPTool`; Ganzzahlen werden gecastet; Zeichenketten in SQL laufen über `escapeString()` ([Sicherheit in Erweiterungen](../teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)) |
| Ausgaben | Template-Werte werden gewaschen (`\|wash`), Attribute ebenfalls |
| Zugriff | neue Views deklarieren Richtlinienfunktionen; Menüeinträge prüfen `has_access_to` |
| Formulare | Views, die den Zustand ändern, verlangen POST und das Formular-Token |
| Inhalte | Schreibvorgänge, die zusammengehören, laufen in einer Transaktion |
| Browser | kein Inline-Script, kein `on...=`-Attribut, kein `style=""` in Templates |
| Admin-Seiten | gemeinsame `.a4-*`-Komponenten, nur Tokens, funktioniert im dunklen Modus |
| PHP | läuft auf der ältesten PHP-Version, die die Erweiterung unterstützt; keine neuen veralteten Aufrufe (Deprecations) |
| Einstellungen | neue Einstellungen haben Voreinstellungen und einen Kommentar |
| Metadaten | Versionssprünge nur in Release-Commits, in allen Dateien |
| Texte | Oberflächentexte sind Übersetzungs-Strings mit dem Kontext der Erweiterung |
| Commits | das Betreff-Format, ein Thema je Commit ([Commit-Nachrichten](commit-nachrichten.md)) |

Der Kernel hat eine eigene Checkliste für Code-Reviews von Templates der Administration in
[Styling the administration](../../../bc/6.0/admin4-gestaltung.md) (auf Englisch).

## Probieren Sie es aus

Das Lesen eines Diffs findet manche Probleme; das Ausführen findet andere. Um einen Pull Request lokal auszuprobieren:

```bash
git fetch origin pull/123/head:pr-123
git switch pr-123
```

(`123` ist die Nummer des Pull Requests; auf GitHub ist `origin` hier das ursprüngliche Repository.) Installieren Sie
ihn in einer Entwicklungs-Website, leeren Sie die Caches und verwenden Sie die Funktion, einschließlich der
Fehlerfälle: falsche Eingabe, ein Benutzer ohne die Richtlinie, eine fehlende Einstellung.

## Kommentare schreiben

- **Sagen Sie, was und warum.** „Dieser Wert wird ohne `wash` ausgegeben, daher macht ein Titel mit `<` die Seite
  kaputt“ hilft. „Escape this“ lässt den Autor raten.
- **Trennen Sie Muss von Kann.** Kennzeichnen Sie optionale Vorschläge als solche („Optional: ...“). Dann weiß der
  Autor, was das Übernehmen blockiert.
- **Schlagen Sie vor, statt zu befehlen.** Mit den „suggestion“-Blöcken von GitHub kann der Autor eine kleine Änderung
  mit einem Klick übernehmen.
- **Loben Sie, was gut ist.** Ein konkretes „Der Test für den leeren Fall ist eine gute Idee“ lehrt so viel wie eine
  Kritik.
- **Fragen Sie, wenn Sie etwas nicht verstehen.** „Warum ist das nötig?“ ist ein guter Review-Kommentar; die Antwort
  gehört oft in einen Code-Kommentar.
- **Bleiben Sie beim Code.** Nie bei der Person.

## Freigeben

Geben Sie frei, wenn Sie sich wohl dabei fühlen würden, den Code selbst zu pflegen. Wenn Sie nur einen Teil geprüft
haben, sagen Sie, welchen. Wenn Sie kein Maintainer sind, ist ein Kommentar „Auf PHP 8.0 und 8.5 mit den Schritten aus
der Beschreibung ausprobiert: funktioniert“ ein wertvolles Review, auch ohne Freigabe.

## Verwandte Seiten

- Zurück: [Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann](commit-nachrichten.md)
- Weiter: [Eine verwaiste Erweiterung übernehmen](eine-verwaiste-erweiterung-uebernehmen.md)
- [Pull-Request-Etikette](pull-request-etikette.md)
- [About pull request reviews](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/reviewing-changes-in-pull-requests/about-pull-request-reviews) in der GitHub-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
