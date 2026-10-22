---
title: "Bereitstellen: das Hosting planen und den Server prüfen"
audience: admin, builder, developer
category: books
order: 550
summary: Die Fragen zum Hosting, die das Buch vor dem Livegang stellt (Besucher, Systemgröße, Sicherheit, Berichte, Budget, Zeit, geteilt oder dediziert), ein Lasttest mit siege, die Dokumentation, die Sie führen sollten, und wo die heutigen Anleitungen zur Bereitstellung stehen.
keywords: Bereitstellung, Hosting-Anforderungen, Besucher, gleichzeitige Zugriffe, Bandbreite, Sicherheit, Berichte, Lasttest, Benchmark, Dokumentation, Notfallwiederherstellung, Livegang, Buch Learning, deploy, hosting requirements, visitors, concurrency, bandwidth, security, reporting, load test, siege, benchmark, documentation, disaster recovery, go live, Learning book
type: help
---
# Bereitstellen: das Hosting planen und den Server prüfen

Kapitel 7 des Buchs handelt nicht davon, einen Server zu installieren. Es handelt davon, was das System braucht, um dort,
wo Sie es bereitstellen, gut zu funktionieren, und davon, die Antworten zu bekommen, bevor sie Geld kosten. Die
Checkliste unten ist die des Buchs, auf den aktuellen Stand gebracht. Das Handwerkliche des Livegangs steht in
[Exponential bereitstellen](../../anleitungen/bereitstellung.md).

## Zuerst die Hosting-Anforderungen festlegen

| Frage | Was zu entscheiden ist |
|---|---|
| **Zahl der Besucher** | Benutzer pro Stunde, gleichzeitige Benutzer, Benutzer insgesamt. Daraus ergeben sich die Größe des Servers und die Frage, ob Sie vorgeschaltete Caches brauchen. |
| **Systemgröße** | Speicherplatz für die Dateien (`var/`) und die Datenbank; die Bandbreite, die mit Bildern und Downloads wächst. |
| **Sicherheit** | Was die Website enthält (Zahlungs- oder medizinische Daten, personenbezogene Daten) und wer sie erreichen darf: das Internet, ein Extranet für angemeldete Benutzer oder ein geschlossenes Intranet. Siehe [Sicherheitshärtung](../sicherheitshaertung.md). |
| **Berichte** | Wer Besucherzahlen und Bandbreite braucht und aus welchem Statistikwerkzeug (das Buch nennt drei Log-Analyseprogramme; verwenden Sie, was Ihr Hoster bereitstellt, oder einen Analysedienst). |
| **Budget, Fristen** | Der Termin des Livegangs, der Termin, an dem die Umgebung bereit ist, und die Zeit für die Bereitstellung. |
| **Geteilt oder dediziert** | Geschwindigkeit des Dienstes, die Zahl der Websites, die sich den Server teilen, und der Preis zusätzlicher Bandbreite. |

Unvorhergesehene Probleme bei der Bereitstellung werden direkt zu Kosten. Die letzte Zeile der Liste im Buch gilt
weiterhin: **Im Zweifel gehen Sie nicht live.**

## Ist der Server leistungsfähig genug?

Testen Sie vor dem Start. Das Buch verwendet `siege`, ein Lasttestwerkzeug, mit einer Zahl gleichzeitiger Benutzer eine
Stunde lang gegen Ihre eigene Website, und prüft die fehlgeschlagenen Anfragen und die Antwortzeiten. Exponential hat
einen eingebauten Messbefehl, der dasselbe für Seiten tut, mit und ohne Cache, und Durchläufe mit einer Basislinie
vergleicht: [Exponential messen](../../anleitungen/benchmarking.md). Testen Sie mit den Caches in dem Zustand, den sie im
Produktivbetrieb haben werden, und testen Sie eine Seite, die Arbeit verrichtet (Suche, eine lange Liste), nicht nur
die Startseite.

## Die Dokumentation führen

Die Liste des Buchs, was ein Hosting-Dokument enthält, lohnt sich für jede Website: Kontaktdaten (wen man wann anruft),
die Hardware oder die virtuelle Maschine, das Betriebssystem und die Softwareversionen, der Ablauf für Patches, DNS- und
Netzwerkinformationen, Zugriffskontrolle (wer welche Anmeldung hat), ein **Fahrplan für Aktualisierungen** und der Plan
für die **Notfallwiederherstellung** (was gesichert wird, wie oft und wer es mindestens einmal aus der Sicherung
wiederhergestellt hat). [Eine Website betreiben](../../anleitungen/betrieb-einer-website.md) und
[Aktualisieren](../../anleitungen/aktualisieren.md) sind die Anleitungen, aus denen Sie es aufbauen.

## Die Linux-Umgebung vorbereiten

Die Linux-Besonderheiten des Buchs (die Pakete einer Distribution seiner Zeit) sind durch [Voraussetzungen](../../installation/02-voraussetzungen.md)
und [Einen Installationsweg wählen](../../installation/04-installationsweg-waehlen.md) ersetzt. Der Kern des Abschnitts
bleibt: Richten Sie den Server nach den Anforderungen der Installation ein, mit den richtigen PHP-Erweiterungen und einem
`memory_limit`, das den Systemtest besteht ([Installationsprobleme](installationsprobleme-damals-und-heute.md)). Führen Sie
dann den Setup-Assistenten oder die Installation über die Konsole aus ([Der Setup-Assistent](der-setup-assistent-damals-und-heute.md))
und lassen Sie die Cronjobs laufen.

Weiter: [Eine standardkonforme Website](eine-standardkonforme-website.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 7, „Deploying eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: die Checkliste beibehalten, die Werkzeuge und die Vorbereitung des Servers durch Anleitungen für Exponential 6 ersetzt.
