---
title: Korrekturen und Aktualisierungen
audience: press
category: press-media
order: 136
summary: Wie Redaktionen Berichte über Exponential korrigieren und aktualisieren - der Unterschied zwischen einer Korrektur und einer Aktualisierung, wann ein Release, ein umbenanntes Produkt oder eine neuere Zahl einen Beitrag veralten lässt, Korrekturhinweise, die die Quelle nennen, und wie Sie dem Projekt mitteilen, dass seine eigene Dokumentation falsch ist.
keywords: Korrekturen, Aktualisierungen, Errata, Korrekturhinweis, veraltet, Aktualisierungshinweis, Genauigkeit, Redaktion, redaktionelle Standards, Archiv, corrections, updates, correction note, outdated, update note, accuracy, newsroom, editorial standards, archive
type: help
---
# Korrekturen und Aktualisierungen

Diese Seite ist für Redakteure und Reporter, die veröffentlichte Berichte betreuen. Beiträge über Software altern
schnell: Versionen ändern sich, Zahlen wachsen, Funktionen wandern von „in Entwicklung“ zu „veröffentlicht“. Diese
Seite trennt Korrekturen von Aktualisierungen und gibt Ihnen Hinweise, die Sie an Ihren Hausstil anpassen können.

## Inhalt

1. [Korrektur oder Aktualisierung](#korrektur-oder-aktualisierung)
2. [Fehler, die eine Korrektur brauchen](#fehler-die-eine-korrektur-brauchen)
3. [Änderungen, die eine Aktualisierung brauchen](#änderungen-die-eine-aktualisierung-brauchen)
4. [Hinweise](#hinweise)
5. [Wenn die Dokumentation falsch war](#wenn-die-dokumentation-falsch-war)

## Korrektur oder Aktualisierung

| | Korrektur | Aktualisierung |
|---|---|---|
| Wann | Der Beitrag war bei der Veröffentlichung falsch | Der Beitrag war richtig, aber die Dinge haben sich geändert |
| Beispiel | „eZ Publish 6.0.14“, „die 7x Foundation“ | 6.0.15 wurde nach der Veröffentlichung freigegeben |
| Hinweis | Pflicht, sagt, was falsch war | Empfohlen, sagt, was sich wann geändert hat |

## Fehler, die eine Korrektur brauchen

- Ein falscher Produkt- oder Organisationsname ([Häufige Fehler in Berichten](haeufige-fehler-in-berichten.md)).
- Eine Version, die als veröffentlicht bezeichnet wurde, aber in Entwicklung war.
- Eine Zahl ohne Quelle oder mit den falschen Bedingungen ([Zahlen prüfen](zahlen-pruefen.md)).
- Ein Zitat, das die Person nicht gesagt oder geschrieben hat ([Richtlinie für Zitate](richtlinie-fuer-zitate.md)).
- Eine angedeutete Verbindung zu eZ Systems oder Ibexa ([Nicht verbunden mit eZ Systems oder Ibexa](nicht-verbunden-mit-ez-systems-oder-ibexa.md)).
- Eine Einzelheit zur Sicherheit, die vor ihrer Behebung veröffentlicht wurde ([Über Sicherheitskorrekturen berichten](ueber-sicherheitskorrekturen-berichten.md)).

## Änderungen, die eine Aktualisierung brauchen

| Änderung | Wo Sie sie sehen |
|---|---|
| Ein neues Release wird getaggt | `git tag -l 'v6.0.*' --sort=version:refname`; [Wo Changelogs und Release Notes stehen](wo-changelogs-und-release-notes-stehen.md) |
| Eine als kommend beschriebene Funktion ist jetzt veröffentlicht | Das Changelog des Releases |
| Eine Zahl ist gewachsen | [Exponential in Zahlen](../../../eng-us/help/press/exponential-by-the-numbers.md) |
| Eine Organisation ändert ihre Selbstbeschreibung | Ihre Website |

Sie müssen nicht jeden alten Beitrag aktualisieren; ergänzen Sie einen Aktualisierungshinweis dort, wo Leser sich
wahrscheinlich darauf verlassen, etwa bei einer Anleitung oder einem Kaufratgeber.

## Hinweise

```text
Korrektur, <Datum>: In einer früheren Fassung dieses Artikels wurde die Software „eZ Publish 6.0.14“
genannt. Das Produkt heißt seit August 2025 Exponential; das Release ist Exponential 6.0.14.

Korrektur, <Datum>: In einer früheren Fassung hieß es, Exponential Intelligence veröffentliche Artikel. Laut
der Dokumentation des Projekts schreibt es Entwürfe und veröffentlicht nie von sich aus.

Aktualisierung, <Datum>: Exponential 6.0.15, hier als in Entwicklung beschrieben, wurde am <Datum des Tags>
veröffentlicht (Quelle: <Link>).
```

## Wenn die Dokumentation falsch war

Wenn Sie sich auf diese Dokumentation verlassen haben und sie falsch war, stehen Sie mit dem Fehler nicht allein. Sagen
Sie es dem Projekt im [Issue-Tracker der Community](https://github.com/se7enxweb/exponential-community/issues) oder mit
**Auf GitHub bearbeiten** auf der Seite; die Korrektur erscheint im Verlauf der Seite, den Sie in Ihrem Korrekturhinweis
zitieren können.

## Verwandte Seiten

- [Ein Video oder einen Beitrag korrigieren](ein-video-oder-einen-beitrag-korrigieren.md)
- [Leitfaden: Faktenprüfung](../../../eng-us/help/press/fact-checking-guide.md)
- [Daten prüfen](daten-pruefen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
