---
title: Reviews geben und erhalten
audience: community, developer, supporter
category: community
order: 30
summary: Wie Reviews in der Exponential-Community funktionieren - warum 7x viele Review-Runden durchführt, wie Sie sie durchlaufen, Squash und Rebase, wie Community-Mitglieder die Pull Requests anderer prüfen können und wie Sie im Rahmen des Verhaltenskodex Rückmeldungen geben und annehmen.
keywords: Code-Review, Review-Runden, Pull Request, Rückmeldung, Feedback, Squash, Rebase, force-with-lease, andere prüfen, Umgangsformen, Verhaltenskodex, code review, review rounds, feedback, reviewing others, etiquette, code of conduct
type: help
---
# Reviews geben und erhalten

Im Review geschieht das meiste Lernen in einem Open-Source-Projekt, und zwar in beide Richtungen.

## Inhalt

1. [Ein Review erhalten](#ein-review-erhalten)
2. [Squash und Rebase](#squash-und-rebase)
3. [Die Arbeit anderer prüfen](#die-arbeit-anderer-prüfen)
4. [Rückmeldungen im Rahmen des Verhaltenskodex](#rückmeldungen-im-rahmen-des-verhaltenskodex)

## Ein Review erhalten

> „**Stellen Sie sich auf viele Review-Runden ein.** 7x führt deutlich mehr Review-Runden als üblich durch, bevor es
> einen Commit oder Pull Request annimmt, manchmal Hunderte von Überarbeitungen. [...] So bleibt der Kernel wartbar;
> es ist kein Urteil über Sie.“ (`CONTRIBUTING.md`, „Code Reviews“)

So kommen Sie schneller durch das Review (gleiche Quelle):

- Halten Sie Pull Requests klein und fokussiert: eine Änderung pro Pull Request.
- Folgen Sie den Coding-Standards und dem Stil der Dateien rund um Ihre Änderung.
- Fügen Sie Tests hinzu, die ohne Ihre Änderung fehlschlagen und mit ihr bestehen.
- Beantworten Sie jeden Review-Kommentar im Pull Request, mit einer Änderung oder einer Begründung.
- Halten Sie Ihren Branch aktuell: Rebasen Sie ihn auf `main`, bevor Sie um die nächste Runde bitten.
- Nehmen Sie viele Runden nicht persönlich.
- Wenn Ihre Änderung eine [negative Funktion](../../projekt/unterstuetzung-erhalten.md#negative-funktionen) verändert, also
  Verhalten, das gewollt oder auslegungsfähig ist und von dem andere Websites abhängen, rechnen Sie mit noch mehr
  Runden: Begründen Sie den Anwendungsfall und bieten Sie das neue Verhalten als Option statt als neuen Standard an.

Jeder Pull Request wird am Ende gemergt: Sein Autor überarbeitet ihn so, wie es die Reviews verlangen
([Wie das Projekt wächst](../../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst)).

## Squash und Rebase

7x verlangt, dass Sie „Ihren Pull Request squashen und auf `main` rebasen, solange er offen ist“:

```bash
git fetch origin
git rebase -i origin/main          # fold fixups into logical commits, usually one
git push --force-with-lease        # only to your own pull request branch, never to main
```

Nachdem ein Pull Request gemergt ist, wird die Historie von `main` nie umgeschrieben; eine spätere Korrektur kommt als
neuer Pull Request.

## Die Arbeit anderer prüfen

Auch Community-Mitglieder können Pull Requests prüfen, und ein sorgfältiges Review von jemandem, der die betroffene
Funktion nutzt, hilft den Maintainern:

| Prüfung | Wie |
|---|---|
| Tut er, was er verspricht? | Den Branch auf einer Testinstallation auschecken und ausprobieren |
| Macht er etwas in der Nähe kaputt? | Die zugehörigen Bildschirme und Ihre eigenen Erweiterungen ausprobieren |
| Ist er getestet? | Nach einem Test suchen, der ohne die Änderung fehlschlägt |
| Folgt er den Regeln? | Präfix der Commit-Nachricht, Manifestzeile, Namenspräfixe, keine Inline-Styles in Administrations-Templates |
| Ist er dokumentiert? | Eine Hilfe- oder Funktionsseite für geändertes Verhalten |

Schreiben Sie, was Sie getestet haben und auf welcher Einrichtung. „Getestet auf PHP 8.3, SQLite, Velocity:
funktioniert“ ist nützlich; „Sieht gut aus“ nicht. Mehr dazu:
[Die Arbeit anderer prüfen](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/die-arbeit-anderer-pruefen.md).

## Rückmeldungen im Rahmen des Verhaltenskodex

- Kommentieren Sie den Code, nie die Person.
- Erklären Sie das Warum und schlagen Sie eine Alternative vor.
- Widersprechen Sie „fachlich, öffentlich und höflich“ (`CODE_OF_CONDUCT.md`, „The contributor agreement (CLA)“).
- Danken Sie Menschen für ihre Arbeit, auch wenn Sie um Änderungen bitten.

## Verwandte Seiten

- [Code beitragen](code-beitragen.md)
- [Umgangsformen bei Pull Requests](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/pull-request-etikette.md)
- [Mentoring](../anderen-helfen/mentoring.md)
- [Der Verhaltenskodex](../beitreten/der-verhaltenskodex.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
