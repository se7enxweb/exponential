---
title: GitHub-Issues
audience: customer, admin, partner
category: customer-success
order: 19
summary: Wie ein Website-Betreiber Issues in den öffentlichen GitHub-Trackern von Exponential verfolgt und anlegt - welcher Tracker wofür da ist, wie man sucht, was ein gutes Issue enthält, was man nie veröffentlicht, was zu erwarten ist und wie eine Korrektur Ihre Website erreicht.
keywords: GitHub, Issues, Issue-Tracker, Fehlerbericht, Funktionswunsch, se7enxweb, exponential, velocity, Community, ein Issue verfolgen, issue tracker, bug report, feature request, community, follow an issue
type: help
---
# GitHub-Issues

Exponential wird offen auf GitHub entwickelt. Fehler und Wünsche werden in öffentlichen Issue-Trackern verfolgt, die
jeder lesen und in die jeder mit einem kostenlosen GitHub-Konto schreiben kann. Diese Seite erklärt, wie Ihr Team sie
nutzt: um zu prüfen, ob ein Problem bekannt ist, um eine Korrektur zu verfolgen und um einen neuen Fehler zu melden.

## Die Tracker

| Tracker | Für |
|---|---|
| [Exponential (die Software)](https://github.com/se7enxweb/exponential/issues) | Fehler im Kernel, in der Administration, in den mitgelieferten Erweiterungen und in der Dokumentation |
| [Exponential Velocity](https://github.com/se7enxweb/exponential-velocity/issues) | Fehler des Anwendungsservers Velocity: Start, Worker, sein Response-Cache, HTTPS |
| [Exponential-Projekt und Community](https://github.com/se7enxweb/exponential-community/issues) | Das Projekt, die Website, das Ökosystem und alles, was kein Fehler in Software ist |
| Das eigene Repository einer Erweiterung | Fehler in einer Erweiterung, die eines hat; die Meldeseite der Hilfe listet sie auf |

Quelle: `settings/exphelp.ini [IssueTrackers]` und [Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md).

## Vor dem Anlegen: suchen

Durchsuchen Sie den Tracker nach Wörtern aus der Fehlermeldung oder der Funktion. „Wenn das Problem bereits bekannt
ist, ergänzen Sie, was Sie wissen, in diesem Issue, statt ein neues zu eröffnen“ ([Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md)).
Wenn Sie ein bestehendes Issue auf GitHub verfolgen, sehen Sie, wann es behoben ist.

## Was ein gutes Issue enthält

Aus [Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md):

- **Einen kurzen Titel**, der das Problem benennt.
- **Was Sie getan haben**, Schritt für Schritt, damit jemand anderes es nachstellen kann.
- **Was Sie erwartet haben** und **was stattdessen geschah**, mit der genauen Meldung.
- **Die Umgebung**: Version von Exponential, PHP-Version, Webserver, Datenbank, Design der Administration. Die
  [Meldeseite der Hilfe](die-meldeseite-der-hilfe.md) und **Setup > Systeminformationen** (**Bericht als Text kopieren**)
  liefern eine Zusammenfassung ohne Passwörter, Schlüssel und Serverpfade.
- **Logzeilen** um den Zeitpunkt herum, ohne alles Vertrauliche.

Die Meldeseite der Hilfe entwirft ein solches Issue für Sie ([Die Meldeseite der Hilfe](die-meldeseite-der-hilfe.md)).

## Nie veröffentlichen

- Passwörter, Schlüssel, Sitzungs-Cookies, Datenbankadressen.
- Personenbezogene Daten von Kunden oder Benutzern, auch nicht in Screenshots und Logzeilen.
- Ein Sicherheitsproblem. Solche gehen vertraulich an security@se7enx.com ([Vertrauliche Sicherheitsmeldungen](vertrauliche-sicherheitsmeldungen.md)).

## Was zu erwarten ist

- **Keine zugesagte Antwortzeit.** Kostenlose Hilfe „hängt von der Zeit ab, die Menschen geben, daher verspricht
  niemand eine Antwortzeit“ ([Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md)). Wenn Sie nicht warten
  können, ist bezahlter Support von 7x der Weg, den die Support-Seite des Projekts nennt ([Support erhalten](../../../../en/us/help/project/getting-support.md)).
- **Eine Korrektur erreicht Sie in einem Release.** Korrekturen werden in der gepflegten Linie vorgenommen und mit dem
  nächsten Release veröffentlicht; eine veröffentlichte Version wird nie geändert (`SECURITY.md`). Ihr Anbieter spielt
  dann dieses Release ein ([Wie Updates funktionieren](wie-updates-funktionieren.md)).
- **Sie können helfen.** Eine genaue Reproduktion, ein Test auf der Entwicklungslinie oder eine als Pull Request
  eingereichte Korrektur beschleunigen die Sache; „Erwähnen Sie das Issue im Pull Request (zum Beispiel `Fixes #123`),
  damit beide verknüpft sind“ ([Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md), [Mit Git entwickeln](../../../../en/us/help/develop-with-git.md)).

## Wer für Ihre Organisation meldet

Meist legt Ihr Administrator oder Partner das Issue an, nachdem er bestätigt hat, dass das Problem in der Software und
nicht in der Einrichtung Ihrer Website liegt. Bitten Sie ihn, Ihnen den Link zu schicken, und bewahren Sie ihn in
Ihren Notizen zum Problem auf. Ein Partner des Programms kann auch Support-Tickets für Partner nutzen
([Fehler als Partner melden](../../../../en/us/help/partners/filing-bugs-as-a-partner.md)).

## Funktionswünsche

Auch der Wunsch nach einer neuen Funktion ist ein Issue, im Software-Tracker oder, bei allem, was das Projekt betrifft,
im Community-Tracker. Beschreiben Sie den Bedarf, nicht nur die Lösung. Wenn die Funktion für Ihr Geschäft wichtig ist,
ist ihr Sponsoring ein dokumentierter Weg ([Funktionswünsche und gesponserte Funktionen](../../../../en/us/help/partners/feature-requests-and-sponsored-features.md)).

## Verwandte Seiten

- [Probleme melden](probleme-melden.md)
- [Mit 7x und dem Projekt zusammenarbeiten](../partner-und-hoster/mit-7x-und-dem-projekt-zusammenarbeiten.md)
- [Zum Projekt beitragen](../../../../en/us/help/project/contribute-to-the-project.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
