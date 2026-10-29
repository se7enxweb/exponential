---
title: "Bewährte Praktiken der Erweiterungsentwicklung"
audience: developer
category: books
order: 540
summary: Die Planungsratschläge, mit denen das Kapitel des Buchs über Erweiterungsentwicklung beginnt (Ziele, Tests, Zeitrahmen, Lernkurve, Anforderungen, Werkzeuge, Teilen, Aktualisieren, Dokumentation), mit den Werkzeugen und Gewohnheiten von heute.
keywords: Erweiterungsentwicklung, Planung, Ziele, Tests, Versionsverwaltung, Lizenz, Teilen, Aktualisieren, Dokumentation, Buch Learning, extension development, planning, goals, testing, version control, git, license, sharing, upgrading, documentation, Learning book
type: help
---
# Bewährte Praktiken der Erweiterungsentwicklung

Kapitel 6 des Buchs baut zwei Erweiterungen im Detail, ein Zahlungsmodul für einen Anbieter seiner Zeit und einen
Kategorie-Datentyp. Diese anbieterspezifischen Teile sind veraltet, und der Shop von Exponential 6 hat ein eigenes
Zahlungsgateway-Ereignis ([Zahlungsgateway-Ereignis](../e-commerce/kasse/das-ereignis-zahlungs-gateway.md)). Nicht veraltet
ist der erste Abschnitt, die Praxis der Erweiterungsentwicklung. Jeder seiner Punkte ist immer noch eine gute Regel.

## Zuerst entwerfen

- **Ziele und Vorgaben.** Schreiben Sie auf, was die Erweiterung tun muss und was sie nicht tun darf. Ein klares Ziel
  verhindert, dass der Umfang ausufert, was Zeit und Geld kostet, und lässt Sie das Notwendige vom Wünschenswerten
  trennen.
- **Die Tests planen.** Schreiben Sie zu jedem Ziel, wie Sie es testen werden. Am Ende sagt Ihnen die Liste, ob Sie
  gebaut haben, was Sie brauchten. Heute ist das außerdem eine Testsuite: [Tests für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tests-fuer-erweiterungen.md).
- **Zeitrahmen.** Bringen Sie die Termine, die Entwicklungszeit und die Veröffentlichungstermine dessen, was Sie bauen,
  früh zu Papier. Rechnen Sie bei den ersten Projekten Zeit ein, in der Sie die Bibliotheken lernen.
- **Die Lernkurve.** Rechnen Sie damit, Hilfe zu brauchen. [Die Community fragen](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/die-community-fragen.md)
  sagt, wo und wie Sie fragen, damit Sie eine Antwort bekommen.

## Anforderungen und Umgebung

Verwenden Sie dieselben Versionen wie Ihr Produktivserver: Eine Funktion, die es nur in einem neueren PHP oder einer
neueren Datenbank gibt, scheitert bei der Bereitstellung. Exponential 6 läuft auf PHP 8.0 bis 8.5 (siehe
[Erste Schritte](../../anleitungen/erste-schritte.md)), und jede Erweiterung nennt ihre eigenen Anforderungen in
`composer.json`. Testen Sie früh auf der Plattform, auf der Sie bereitstellen werden. Eine Entwicklungsinstallation, die
eine Kopie des Produktivsystems ist, ist der richtige Weg ([Eine Entwicklungsinstallation einrichten](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md)).

## Werkzeuge

Das Buch empfiehlt Versionsverwaltung (damals CVS und Subversion) und einen Editor, der HTML, PHP, SQL und
Template-Dateien versteht. Versionsverwaltung heißt heute Git, und jede Erweiterung ist ein Git-Repository
([Mit Git entwickeln](../mit-git-entwickeln.md)). Verwenden Sie `php -l` für jede PHP-Datei, die Sie ändern, und die
Debug-Ausgabe während der Arbeit ([Erweiterungen debuggen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/erweiterungen-debuggen.md)).

## Mit der Community teilen

Das Buch beschreibt eine Doppellizenz, die Sie verpflichtete, Ihren Code zu veröffentlichen, sofern Sie nicht zahlten.
Die Lizenz von Exponential ist die GNU General Public License 2.0 oder später; das heißt, Sie dürfen es nach Belieben
verwenden und ändern, und wenn Sie Ihre Änderungen weitergeben, erhält der Empfänger dieselbe Freiheit.
[Lizenzen vor der Wiederverwendung lesen](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md)
und [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md) beschreiben, was zu tun ist. Geteilt wird über ein
Repository und ein Paket auf Packagist, nicht über eine Beitragsseite und einen Subversion-Server.

## Aktualisieren

Notieren Sie die wichtigsten Anforderungen Ihrer Erweiterung (Kernel-Version, PHP-Version, andere Erweiterungen), damit
Sie sie bei jedem Release von Exponential prüfen können. Die Richtlinien für Aktualisierungen, die das Buch verspricht,
gibt es: [Teil 4: Aktualisieren](../erweiterungsentwicklung/teil-4-aktualisieren/README.md).

## Dokumentieren

Wenn Sie jemals nach Monaten zu Ihrer eigenen Arbeit zurückgekehrt sind und sie nicht lesen konnten, wissen Sie, warum.
Schreiben Sie auf, wofür die Erweiterung da ist, wie man sie installiert und einrichtet, was jede Einstellung bedeutet
und was die Templates erwarten. [Ihre Erweiterung dokumentieren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ihre-erweiterung-dokumentieren.md).

## Vorhandenen Code einbinden

Das Buch fragt: „Eine neue Anwendung schreiben oder eine vorhandene umbauen?“ und warnt: Eine andere Anwendung
anzubinden verdoppelt die Komplexität und den Aufwand für die Unterstützung. Prüfen Sie, ob eingebaute Funktionen oder
eine kleine Erweiterung günstiger sind als eine Brücke zu einem separaten Paket. Wenn Sie eine Brücke bauen, beschreiben
die beiden Strategien des Buchs weiterhin die Wahl: die Daten in Ihre eigene Datenbank **importieren** (einfach und
schnell, kann aber veralten) oder das andere System bei Bedarf **aufrufen** (aktuell, aber davon abhängig, dass es
läuft). Bauen Sie die Brücke über eine Schnittstelle, heute die REST-API oder einen Feed, nicht durch Zugriff auf die
Tabellen des anderen Systems. Siehe [Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md).

Weiter: [Bereitstellen](bereitstellen-planen-und-pruefen.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 6, „Extension Development“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: die allgemeinen Praktiken beibehalten, Werkzeuge und Lizenzierung durch die heutigen ersetzt; die anbieterspezifischen Beispiele sind als veraltet weggelassen.
