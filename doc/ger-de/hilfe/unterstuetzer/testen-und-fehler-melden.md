---
title: Testen und Fehler melden
audience: supporter
category: supporting
order: 60
summary: Wie jeder Anwender Exponential unterstützt, indem er testet und gute Fehlerberichte anlegt – welcher Tracker der richtige ist, was eine gute Fehlerbeschreibung enthält, das Testen von Entwicklungsversionen, Sicherheitsprobleme, die vertraulich gemeldet werden müssen, und das Nachverfolgen.
keywords: Testen, Fehler, Fehlerbericht, Issue, GitHub, Tracker, reproduzieren, Entwicklungsversion, Branch main, Sicherheit, nachverfolgen, testing, bug, bug report, reproduce, development version, main branch, security, follow up
type: help
---
# Testen und Fehler melden

Diese Seite ist für alle Anwender von Exponential. Sie müssen keinen Code schreiben, um die Software besser zu machen:
Eine genaue Fehlerbeschreibung ist oft schon die halbe Korrektur.

## Welcher Tracker

| Problem | Wo |
|---|---|
| Ein Fehler in der Software Exponential | <https://github.com/se7enxweb/exponential/issues> |
| Ein Fehler in einer Erweiterung | Das Repository dieser Erweiterung auf GitHub |
| Das Projekt, seine Websites oder die Community | <https://github.com/se7enxweb/exponential-community/issues> |
| Eine Sicherheitslücke | **Vertraulich** an security@se7enx.com; niemals als öffentliches Issue (SECURITY.md) |

## Eine gute Fehlerbeschreibung

Die Anleitung der Hilfe selbst erklärt es ausführlich: [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung).
Das Wichtigste:

- Was Sie getan haben, Schritt für Schritt.
- Was Sie erwartet haben und was stattdessen geschehen ist.
- Die Versionen: Exponential, PHP, Datenbank, Engine, Erweiterungen.
- Fehlermeldungen und Logzeilen, aus denen Passwörter, Schlüssel und personenbezogene Daten entfernt sind.
- Ob es auch auf einer frischen Installation auftritt.

## Vor einem Release testen

Die Entwicklung findet im Branch `main` statt, und Korrekturen werden „mit dem nächsten Release 6.0.x veröffentlicht“
(SECURITY.md). Wer `main` auf einer Testinstallation testet, findet Probleme, bevor sie alle erreichen:

1. Installieren Sie aus Git ([Mit Git entwickeln](../mit-git-entwickeln.md)).
2. Aktualisieren Sie auf den neuesten Stand von `main`.
3. Tun Sie, was Sie sonst auch tun: bearbeiten, veröffentlichen, hochladen, suchen, die Sprache wechseln.
4. Melden Sie alles, was nicht mehr funktioniert, zusammen mit dem Commit, den Sie getestet haben.

Testen Sie Entwicklungsversionen niemals auf einer Produktivsite.

## Nachverfolgen

- Beantworten Sie Rückfragen zu Ihrem Issue; unbeantwortete Rückfragen sind der häufigste Grund, warum ein Fehler
  liegen bleibt.
- Testen Sie die Korrektur, sobald sie da ist, und sagen Sie, ob sie funktioniert.
- Wenn Sie den Fehler selbst beheben können: [Wenn etwas schiefgeht, „Noch besser: die Korrektur schicken“](../wenn-etwas-schiefgeht.md#noch-besser-die-korrektur-mitschicken).

## Verwandte Seiten

- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Code beitragen](code-beitragen.md)
- [Anderen in der Community helfen](in-der-community-helfen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
