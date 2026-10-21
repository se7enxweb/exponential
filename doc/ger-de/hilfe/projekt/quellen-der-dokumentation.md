---
title: "Quellen der Dokumentation: wo Sie über Exponential lesen können"
audience: user, editor, admin, developer, designer
category: project
order: 90
summary: Jede Dokumentationsquelle, die die Seite „Documentation“ von exponential.earth auflistet, mit ihrem Inhalt, ihrer Aktualität und ihrer Nutzung neben dieser Hilfe - die Dokumentations-Website, die freien Bücher, die Artikel auf Share Exponential, eZpedia, die älteren Core- und SDK-Handbücher, die Kernkonzepte von Martin Bauer, die Dokumentation von Mugo, die Dokumentation der Zeta Components, Schulungsmaterial und Videos.
keywords: Dokumentation, Handbuch, technisches Handbuch, Installationsdokumentation, Bücher, Artikel, Schulung, Videos, ausgewählte Artikel, documentation, docs, manual, technical manual, installation documentation, exponential.doc.exponential.earth, doc.exponential.earth, books, Learning eZ Publish, PDF, Share Exponential, articles, eZpedia, doc.core.exponential.earth, doc.sdk.exponential.earth, Martin Bauer, Mugo, Zeta Components, training, YouTube, ExponentialCMSInsights, featured articles, RSS
type: help
---
# Quellen der Dokumentation: wo Sie über Exponential lesen können

Die Seite [Documentation](https://exponential.earth/doc/) der Projektwebsite listet die Orte auf, an denen
Exponential dokumentiert ist. Manche sind aktuell, manche beschreiben Releases von vor vielen Jahren und werden
aufbewahrt, weil ihre Konzepte weiterhin gelten. Diese Seite listet sie alle auf, gelesen im Oktober 2026, sagt,
wofür jede gut ist und wie Sie sie neben dieser Hilfe nutzen.

## Beginnen Sie mit dieser Hilfe

Diese Hilfe ist die Dokumentation des Releases, das Sie betreiben: Sie ist der Ordner `doc/` der Installation, wird
in der Administration angezeigt und ändert sich mit jeder Änderung der Software. Wo eine ältere Quelle und diese
Hilfe in Bezug auf die Software voneinander abweichen, vertrauen Sie dieser Hilfe und dem
[Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md). Sie ist freie Dokumentation
([Freie Dokumentation](../freie-dokumentation.md)).

## Aktuelle Quellen

| Quelle | Was sie behandelt | Adresse |
|---|---|---|
| **Die Dokumentations-Website** | Die Installationsdokumentation von Exponential 6 und das technische Handbuch (Konzepte, Templates, Konfiguration), zusammen mit der Dokumentation der Linie Exponential Platform 5. `doc.exponential.earth` führt zur selben Site. | <https://exponential.doc.exponential.earth/> |
| **Installation, 6.x** | Der neueste Bereich der Website, die Installation von Exponential 6 | [Installation](https://exponential.doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html) |
| **Artikel auf Share Exponential** | Über viele Jahre gesammelte Artikel zu zentralen Themen | <https://share.exponential.earth/learn> |
| **eZpedia** | Das Wiki der Community, das jeder nach der Anmeldung bearbeiten kann | <https://ezpedia.exponential.earth/> |
| **ExponentialCMSInsights** | Videos über Exponential auf YouTube | <https://www.youtube.com/@ExponentialCMSInsights> |
| **Schulung** | Die Sammlung der Website mit Schulungsmaterial und Wissensaustausch unter Entwicklern | <https://exponential.earth/doc/training> |
| **Ausgewählte Artikel** | Artikel und Blogbeiträge über Exponential aus dem ganzen Web, mit einem RSS-Feed | <https://exponential.earth/doc/exponential-featured> |

## Die freien Bücher

Die Website bietet auf ihrer [Bücherseite](https://exponential.earth/doc/books) freie Bücher als PDF-Downloads an,
beginnend mit „Learning eZ Publish“, geschrieben, als die Software noch ihren alten Namen trug. Drei Bücher werden in
Repositories von se7enxweb aufbewahrt, zwei davon unter der GNU Free Documentation License; das Kapitel
[Aus den freien Büchern lernen](../../../eng-us/help/extension-development/part-1-from-user-to-developer/learning-from-the-books.md)
listet sie auf und enthält eine Tabelle, die das in den Büchern Beschriebene der heutigen Umsetzung in
Exponential 6 gegenüberstellt.

## Ältere Handbücher, für die Konzepte weiterhin nützlich

Diese beschreiben deutlich ältere Releases. Ihre Konzepte (Inhaltsklassen und -objekte, der Knotenbaum, Templates,
Überschreibungen, Siteaccesses, Erweiterungen) gelten weiterhin; ihre Installationsschritte, PHP-Versionen und
Versionsnummern nicht.

| Quelle | Was sie ist | Adresse |
|---|---|---|
| **Exponential Core Documentation** | Eine überarbeitete Kopie der Benutzungsdokumentation aus der Zeit von 3.0 bis 3.4 | <https://doc.core.exponential.earth/> |
| **Exponential SDK Documentation** | Eine kurze Einführung in die Konzepte und APIs, mit Anwendungsbeispielen, aus der Zeit von 3.0 bis 3.2 | <https://doc.sdk.exponential.earth/> |
| **Kernkonzepte von Martin Bauer** | Ein Überblick über die Kernkonzepte des Systems von einem frühen Autor | [martinbauer.com](http://www.martinbauer.com/eZ-publish-Overview.html) |
| **Share Exponential: Artikel zum alten Namen** | Artikel aus der Zeit, als die Software noch eZ Publish hieß | <https://share.exponential.earth/learn/ez-publish> |
| **Dokumentation von Mugo** | Dokumentation der Software unter ihrem alten Namen, gepflegt von Mugo Web, einem langjährigen Mitwirkenden | <https://ezpublishdoc.mugo.ca/> |
| **Die ältere FAQ** | Die FAQ der archivierten Dokumentation, die die Website „schon etwas älter“ nennt | <https://doc.ezpublishlegacy.se7enx.com/FAQ.html> |

Die Website merkt an, dass Exponential mit der Dokumentation und den Softwarebeispielen kompatibel ist, die für
eZ Publish geschrieben wurden. In der Praxis gilt das für Templates, das Inhaltsmodell und die Erweiterungs-API;
prüfen Sie PHP-Code gegen die PHP-Versionen, die Sie betreiben, und Installationsschritte gegen das
[Installationsbuch](../../installation/README.md).

## Bibliotheken, die Exponential verwendet

Exponential verwendet die Zeta Components, eine Sammlung von PHP-Bibliotheken für Aufgaben wie E-Mail, Archive, die
Datenbank und die Konfiguration. Ihre Dokumentation mit vielen Beispielen finden Sie unter
[zetacomponents.org](https://zetacomponents.org/documentation/overview.html).

## Wie Sie sie zusammen nutzen

1. Durchsuchen Sie zuerst diese Hilfe: Sie passt zu Ihrem Release.
2. Für ein ausführlich erklärtes Konzept lesen Sie die Bücher oder die älteren Handbücher.
3. Für ein praktisches Rezept durchsuchen Sie eZpedia und die Artikel auf Share und vergleichen dann mit dieser
   Hilfe.
4. Ist danach noch etwas unklar, fragen Sie ([Unterstützung erhalten](unterstuetzung-erhalten.md)), und wenn Sie die Antwort
   finden, ergänzen Sie sie in eZpedia oder in dieser Hilfe, damit die nächste Person sie findet
   ([Zum Projekt beitragen](zum-projekt-beitragen.md#dokumentation)).

## Verwandte Seiten

- [Das Exponential-Projekt: Hier beginnen](README.md)
- [Die Hilfe für Redakteure und Benutzer](../README.md)
- [Aus den freien Büchern lernen](../../../eng-us/help/extension-development/part-1-from-user-to-developer/learning-from-the-books.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt
  und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
