---
title: Länder und Regionen
audience: government, project-manager, admin, website-creator
category: public-sector
order: 10
summary: Verzeichnis für Bundesländer, Bundesstaaten, Provinzen, Regionen und Kreise, die eine Exponential-Website planen - was sie veröffentlichen, das Inhaltsmodell, Struktur und Rollen, Anforderungen, ein Plan für 90 Tage und gemeinsame Dienste für die Kommunen einer Region.
keywords: Landesverwaltung, Bundesland, Bundesstaat, Provinz, Region, Landkreis, Kreis, regionale Behörde, gemeinsame Dienste, Kommunen, regionales Portal, regional government, state government, province, region, county, regional authority, shared services, municipalities, regional portal
type: help
---
# Länder und Regionen

Dieser Ordner richtet sich an Projektverantwortliche, Administratorinnen und Administratoren und Website-Ersteller
eines Bundeslands, eines Bundesstaats, einer Provinz, einer Region oder eines Kreises. Solche Organisationen stehen
zwischen nationaler und kommunaler Ebene: Sie erbringen eigene Dienstleistungen (je nach Land oft Verkehr, Gesundheit,
Bildung, Umwelt oder Planung), veröffentlichen eigene Beschlüsse und unterstützen oft die Kommunen ihres Gebiets. Die
Seiten beantworten, was sie veröffentlichen, wie Sie es in Exponential bauen, welche Anforderungen am meisten wiegen,
wie Sie die ersten 90 Tage gestalten und wie Sie den Kommunen gemeinsame Dienste anbieten.

Die Fakten zum Produkt stammen aus der Dokumentation dieses Repositorys; die Beschreibungen der regionalen Ebene geben
allgemeine Praxis im öffentlichen Sektor wieder, und die Aufteilung der Zuständigkeiten zwischen den Ebenen unterscheidet
sich von Land zu Land.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Was die regionale Ebene besonders macht](#was-die-regionale-ebene-besonders-macht)

## Die Seiten

| Seite | Die Antwort in einer Zeile |
|---|---|
| [Was ein Land oder eine Region veröffentlicht](was-veroeffentlicht-wird.md) | Dienstleistungen nach Thema, Beschlüsse der regionalen Volksvertretung, Pläne und Karten, Förderprogramme, Nachrichten und Informationen für Kommunen |
| [Inhaltsmodell für Länder und Regionen](inhaltsmodell.md) | Mitgelieferte Klassen für Seiten, Dateien und Veranstaltungen; ergänzen Sie Klassen für Plan, Förderprogramm und Beschluss mit echten Datentypen |
| [Struktur, Sektionen und Rollen für Länder und Regionen](struktur-sektionen-und-rollen.md) | Ein Baum nach Thema und Ort, Sektionen je Fachbereich, Rollen je Fachbereich und für das Sekretariat der Volksvertretung |
| [Schwerpunkte der Anforderungen für Länder und Regionen](schwerpunkte-der-anforderungen.md) | Barrierefreiheit, Regional- und Minderheitensprachen, Sicherheit, Schriftgut der Beschlüsse |
| [Die ersten 90 Tage für Länder und Regionen](die-ersten-90-tage.md) | Ein Plan in Phasen mit Prüfungen, einschließlich der Kommunen als Beteiligte |
| [Gemeinsame Dienste für die Kommunen einer Region](gemeinsame-dienste-fuer-kommunen.md) | Websites kleinerer Kommunen auf einer Installation hosten, mit Trennung, gemeinsamem Design und gemeinsamen Inhalten |

## Lesereihenfolge

1. **Was veröffentlicht wird**, um den Umfang mit den Fachbereichen zu vereinbaren.
2. **Inhaltsmodell** und **Struktur, Sektionen und Rollen** mit denjenigen, die die Website bauen.
3. **Schwerpunkte der Anforderungen** mit den Beauftragten für Barrierefreiheit, Sicherheit, Datenschutz und Schriftgut.
4. **Die ersten 90 Tage** als Projektplan.
5. **Gemeinsame Dienste für die Kommunen einer Region**, wenn Sie kommunale Websites hosten oder das planen.

Nach oben: [Verwaltungsebenen](../README.md). Die Einstiegsseite des Bereichs für Verwaltungen,
[Verwaltungen: hier beginnen](../../verwaltungen-hier-beginnen.md), ist der Ausgangspunkt für alle Leserinnen und Leser.

## Was die regionale Ebene besonders macht

| Thema | Warum es wichtig ist |
|---|---|
| Ort | viele Inhalte betreffen Orte: Straßen, Standorte, Gebiete, Dienststellen; Besucher suchen „in meiner Nähe“ |
| Zwei Zielgruppen | Bürgerinnen und Bürger und Unternehmen sowie die Kommunen und Partner der Region |
| Gewählte Volksvertretung | Beschlüsse, Tagesordnungen und Niederschriften der regionalen Volksvertretung brauchen eine dauerhafte, zitierfähige Heimat |
| Sprachen | Regional- und Minderheitensprachen haben neben der Landessprache oft einen rechtlichen Status |
| Förderung | Förder- und Zuwendungsprogramme mit Aufrufen, Fristen und veröffentlichten Ergebnissen |

Exponential hat kein geografisches Informationssystem. Für Orte bietet es einen Datentyp für Kartenorte
(`ezgmaplocation`, der Google Maps verwendet, einen Dienst eines Dritten) und einfache Anschriften in Textfeldern
([ezgmaplocation](../../../../../eng-us/features/6.0/extensions/ezgmaplocation.md)). Verlinken Sie für alles über einen Punkt
auf einer Karte hinaus Ihr bestehendes GIS oder Kartenportal.

## Verwandte Seiten

- [Verwaltungsebenen](../README.md)
- [Kommunen](../kommunen/README.md)
- [Multisite einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
