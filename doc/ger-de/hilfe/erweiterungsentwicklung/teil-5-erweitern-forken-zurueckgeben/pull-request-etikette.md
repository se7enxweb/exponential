---
title: Pull-Request-Etikette
audience: developer, designer, app-creator
category: extension-development
order: 90
summary: Die Gewohnheiten, mit denen Pull Requests übernommen werden und Maintainer sich freuen, Ihren Namen zu sehen - klein und fokussiert, erklärt, getestet, geduldig, offen für Änderungen - und was Sie vermeiden sollten.
keywords: Pull Request, Etikette, Maintainer, Review, kleine Änderungen, Geduld, Respekt, Kommunikation, Open Source, pull request, etiquette, small changes, patience, respect, communication, open source
type: help
---
# Pull-Request-Etikette

Teil 5, Kapitel 9. Die Mechanik eines Pull Requests steht im vorigen Kapitel. In diesem geht es um die Menschen am
anderen Ende. Ein Maintainer liest Ihren Pull Request in der Zeit, die ihm nach seiner eigenen Arbeit bleibt, oft
unbezahlt. Diese Gewohnheiten sorgen dafür, dass diese Zeit etwas bringt, für ihn und für Sie.

## Tun Sie das

- **Halten Sie ihn klein.** Ein Pull Request, der eine Sache in fünfzig Zeilen ändert, wird heute geprüft. Einer, der
  fünf Dinge in zweitausend Zeilen ändert, wartet auf ein freies Wochenende, das vielleicht nie kommt.
- **Erklären Sie das Warum.** Der Diff zeigt, was sich geändert hat. Nur Sie können sagen, warum, und was Sie
  versucht haben, das nicht funktioniert hat.
- **Zeigen Sie, wie Sie es geprüft haben.** „Getestet“ bedeutet nichts; „ein neuer Test, der ohne die Änderung
  fehlschlägt, die vollständige Suite mit demselben Ergebnis wie main und die Anmeldung von Hand in zwei Browsern
  ausprobiert“ bedeutet viel.
- **Folgen Sie dem Stil des Projekts**, auch wo Sie anderer Meinung sind. Schlagen Sie eine Stiländerung getrennt vor,
  wenn überhaupt.
- **Beantworten Sie jeden Review-Kommentar**, mit einer Änderung oder einer Begründung. „Erledigt“ ist eine gute
  Antwort.
- **Bedanken Sie sich.** Für das Review, für das Übernehmen, für das Projekt.
- **Erwähnen Sie, was nicht von Ihnen stammt.** Ein Test, der schon auf main fehlschlägt, eine Warnung, die schon
  vorher da war. Nennen Sie es, damit niemand denkt, Sie hätten es verursacht.
- **Rechnen Sie beim Kernel mit vielen Review-Runden.** 7x prüft einen Commit oder Pull Request in viel mehr Runden als
  üblich, manchmal in Hunderten von Überarbeitungen; seien Sie bereit, Ihre Änderung so lange zu überarbeiten, bis sie
  einfach anzunehmen ist ([Code reviews](../../../../../CONTRIBUTING.md#code-reviews), auf Englisch).
- **Squashen und rebasen Sie, solange der Pull Request offen ist.** 7x verlangt, dass ein Pull Request an den Kernel
  vor dem Mergen in logische Commits zusammengefasst (meist einen) und auf `main` rebast wird: `git fetch origin`,
  `git rebase -i origin/main`, `git push --force-with-lease` nur auf den Branch Ihres Pull Requests. Nach dem Mergen
  wird `main` nie umgeschrieben; eine spätere Korrektur ist ein neuer Pull Request.
- **Wissen Sie vor einem Pull Request an den Kernel, was Sie mit dem CLA abgeben.** Der Kernel
  (`se7enxweb/exponential`) übernimmt einen Code-Beitrag erst, wenn das unterzeichnete Contributor Licensing Agreement
  seines Autors eingegangen ist. Mit der Unterzeichnung und der Einreichung übertragen Sie das Urheberrecht an Ihrem
  Beitrag auf 7x, das damit sein Inhaber wird, und geben die Kontrolle darüber auf: 7x darf ihn auf jede Weise
  verwenden, ändern, neu lizenzieren und verbreiten, ohne Sie zu fragen oder zu bezahlen, und Sie können ihn nicht
  zurückziehen. Ihre eigenen Erweiterungen sind davon nicht berührt. Haken Sie das Kästchen in der Vorlage des Pull
  Requests an ([Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla), auf Englisch).

## Tun Sie das nicht

- **Mischen Sie keine fremden Änderungen hinein.** Kein Neuformatieren von Dateien, die Sie sonst nicht angefasst
  haben, kein Umbenennen „bei der Gelegenheit“. Sie verdecken die eigentliche Änderung.
- **Streiten Sie in einem Review nicht über Geschmack.** Wenn der Maintainer einen anderen Namen bevorzugt, verwenden
  Sie ihn. Es ist sein Projekt.
- **Erinnern Sie nicht täglich.** Eine freundliche Erinnerung nach zwei oder drei Wochen genügt. Maintainer haben auch
  Urlaub.
- **Eröffnen Sie denselben Pull Request nicht noch einmal**, nachdem er geschlossen wurde. Fragen Sie in einem
  Kommentar dazu, warum er geschlossen wurde.
- **Nehmen Sie eine Ablehnung nicht persönlich.** Ein Projekt kann eine gute Änderung ablehnen, weil sie nicht zu
  seiner Richtung passt. Das ist ein legitimer Grund für einen Fork ([Wann Sie forken sollten und wann nicht](wann-forken.md)),
  kein Grund, verärgert zu sein.
- **Schreiben Sie keine Nennung Ihrer selbst in den Code** (einen Kommentar „fixed by ...“). Ihr Name steht in der
  Git-Historie.

## Wenn Sie der Maintainer sind

Dieselben Gewohnheiten gelten umgekehrt:

- Antworten Sie in angemessener Zeit, und sei es nur mit „Ich sehe mir das nächste Woche an“.
- Erklären Sie, was sich ändern muss, wo möglich mit einem Beispiel.
- Korrigieren Sie Kleinigkeiten selbst auf dem Branch des Beitragenden, wenn er Bearbeitungen erlaubt, statt einer
  Runde Kommentare über einen Tippfehler.
- Bedanken Sie sich, besonders bei Menschen, die zum ersten Mal beitragen.

Siehe auch [Die Arbeit anderer prüfen](die-arbeit-anderer-pruefen.md).

## Sprache

Viele Maintainer und Beitragende schreiben in einer Zweitsprache. Schreiben Sie kurze, einfache Sätze. Vermeiden Sie
Ironie; sie übersteht keine Übersetzung. Unterstellen Sie bei dem, was Sie lesen, gute Absichten.

## Verwandte Seiten

- Zurück: [Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md)
- Weiter: [Gute Issues schreiben](gute-issues-schreiben.md)
- [Fallstudie: ein Pull Request an einen Upstream-Autor](fallstudie-ein-pull-request-an-einen-upstream-autor.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
