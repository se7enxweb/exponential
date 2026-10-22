---
title: Mentoring in der Community
audience: community, advocate, developer, trainer
category: community
order: 43
summary: Wie erfahrene Community-Mitglieder Neulinge durch ihre ersten Beiträge zu Exponential begleiten - eine Aufgabe wählen, einrichten, der erste Pull Request, Review-Runden und die Übergabe -, mit einem Vierwochenplan und Grenzen, die das Ganze tragfähig halten.
keywords: Mentoring, Mentor, Mentee, erster Beitrag, Pull Request, Review, Begleitung, Vierwochenplan, Grenzen, Wachstum der Community, mentoring, mentor, mentee, first contribution, guidance, four-week plan, boundaries, community growth
type: help
---
# Mentoring in der Community

Das Projekt wächst, wenn Mitwirkende neue Mitwirkende mitbringen. Mentoring braucht kein Programm: Es ist ein
erfahrenes Mitglied, das einem Neuling durch seine ersten Beiträge hilft.

## Inhalt

1. [Was eine Mentorin oder ein Mentor tut](#was-eine-mentorin-oder-ein-mentor-tut)
2. [Die erste Aufgabe wählen](#die-erste-aufgabe-wählen)
3. [Ein Vierwochenplan](#ein-vierwochenplan)
4. [Grenzen](#grenzen)

## Was eine Mentorin oder ein Mentor tut

- Hilft, eine erste Aufgabe zu wählen, die sich abschließen lässt.
- Beantwortet „Wo ist ...“-Fragen, die die Dokumentation nicht beantwortet.
- Erklärt die Review-Runden, bevor sie kommen, damit sie nicht entmutigen
  ([Reviews geben und erhalten](../mitwirken/reviews-geben-und-erhalten.md)).
- Erledigt die Arbeit nicht anstelle der betreuten Person.

## Die erste Aufgabe wählen

| Gute erste Aufgaben | Warum |
|---|---|
| Eine Korrektur einer Hilfeseite | Klein, sichtbar, kein Code |
| Ein fehlender Übersetzungstext | Klein, lehrt den Ablauf |
| Ein Test für einen behobenen Fehler | Lehrt die Testsuite |
| Ein Fehler, den die betreute Person selbst gefunden hat | Sie versteht ihn bereits |

Vermeiden Sie als erste Aufgaben große Funktionen, Refactorings und alles, was die Sicherheit berührt.

## Ein Vierwochenplan

| Woche | Betreute Person | Mentor |
|---|---|---|
| 1 | Richtet eine Testinstallation aus einem Git-Klon ein, führt `make hooks` aus, liest `CONTRIBUTING.md` | Beantwortet Fragen zur Einrichtung; vereinbart die erste Aufgabe |
| 2 | Nimmt die Änderung vor, committet mit einer Nachricht mit Präfix, aktualisiert das Manifest | Prüft sie vor dem Pull Request unter vier Augen |
| 3 | Eröffnet den Pull Request, beantwortet Review-Kommentare | Erklärt unklare Kommentare; antwortet nicht anstelle der betreuten Person |
| 4 | Squasht, rebased, sieht den Merge; wählt allein eine zweite Aufgabe | Schlägt den nächsten Bereich vor; tritt zurück |

Die Schritt-für-Schritt-Anleitung für den ersten Pull Request ist
[Einen ersten Beitrag betreuen](../../fuersprecher/einen-ersten-beitrag-begleiten.md).

## Grenzen

- Vereinbaren Sie, wie und wann Sie erreichbar sind; Sie sind ehrenamtlich tätig.
- Führen Sie Gespräche über die Arbeit möglichst an öffentlichen Orten (im Pull Request, in den Foren), damit auch
  andere lernen.
- Halten Sie sich an den Verhaltenskodex; ein Treffen unter vier Augen ändert daran nichts.
- Wenn die betreute Person Sie bittet, einen Sicherheitsbefund zu prüfen, verweisen Sie sie an den vertraulichen
  Kanal (`SECURITY.md`).

## Verwandte Seiten

- [Anderen in der Community helfen](README.md)
- [Einen ersten Beitrag betreuen](../../fuersprecher/einen-ersten-beitrag-begleiten.md)
- [Der Weg zum Maintainer](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/der-weg-zum-maintainer.md)
- [Vom Mitwirkenden zum Maintainer](../lernpfade/vom-mitwirkenden-zum-maintainer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
