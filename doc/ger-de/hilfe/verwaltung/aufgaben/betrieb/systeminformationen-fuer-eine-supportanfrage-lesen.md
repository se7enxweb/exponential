---
title: Systeminformationen für eine Supportanfrage lesen
audience: government, admin, hosting
category: public-sector
order: 230
summary: Schritt für Schritt - eine Supportanfrage an Ihren Partner oder das Projekt vorbereiten - den Bericht der Systeminformationen herunterladen (Geheimnisse ausgelassen), die Fehlerreferenz und Log-Zeilen hinzufügen und sagen, welcher Server geantwortet hat.
keywords: Supportanfrage, Systeminformationen, Bericht, Bericht herunterladen, exp:system:info, Zustandsprüfungen, Supportpartner, Ticket, Fehlerreferenz, support request, system information, report, Download report, health checks, support partner, ticket, error reference
type: help
---
# Systeminformationen für eine Supportanfrage lesen

Diese Aufgabe ist für Administratoren, die einen Supportpartner oder die Community um Hilfe bitten. Am Ende enthält die
Anfrage alles, was ein Unterstützer braucht, und nichts, was privat bleiben sollte. Referenz:
[Systeminformationen, Abschnitt 6](../../../../anleitungen/systeminformationen.md#6-der-bericht-für-eine-supportanfrage).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was der Bericht niemals enthält](#was-der-bericht-niemals-enthält)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `setup/system_info` (Administratoren) oder Shell-Zugang.

## Schritte

1. Öffnen Sie **Setup > Systeminformationen** (`/setup/info`) über den Server, auf dem das Problem auftritt (Apache,
   ein Port von Velocity): Das Banner nennt, welcher Server geantwortet hat, und jeder hat sein eigenes PHP und seine
   eigenen Caches.
2. Lesen Sie die **Zustandsprüfungen**: Ein Fehler dort kann schon die Antwort sein.
3. **Bericht herunterladen (.txt)** (oder **Als JSON herunterladen**, **Bericht als Text kopieren**). **Größen messen**
   fügt bei Bedarf die Größen von Verzeichnissen und Datenbank hinzu.
4. Fügen Sie die Fehlerreferenz und die passenden Log-Zeilen hinzu
   ([Einen Fehler anhand seiner Referenz finden](einen-fehler-anhand-seiner-referenz-finden.md)), kopiert aus dem Log-Viewer, der
   personenbezogene Daten maskiert.
5. Beschreiben Sie: was Sie getan haben, was Sie erwartet haben, was geschah, seit wann, was sich kürzlich geändert hat.

In der Shell: `./console exp:system:info --allow-root-user` (das PHP der Kommandozeile, nicht das des Webservers;
dass OPcache dort ausgeschaltet ist, ist normal).

## Was der Bericht niemals enthält

Passwörter, Schlüssel, Token, Sitzungs-IDs, Zugangsdaten in Adressen und vollständige Serverpfade werden ausgelassen
([Systeminformationen, Abschnitt 7](../../../../anleitungen/systeminformationen.md#7-was-niemals-angezeigt-wird)). Prüfen Sie den
Text trotzdem vor dem Senden und fügen Sie niemals Daten von Bürgern in ein Support-Ticket ein; schicken Sie Screenshots
nur von Testinhalten.

Wer Exponential unterstützt: [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wer ist 7x](../../../wer-ist-7x.md).

## Verwandte Seiten
- [Systeminformationen und Zustandsprüfungen](../../../../../eng-us/help/government/operations/monitoring/system-information-and-health-checks.md)
- [Wenn etwas schiefgeht (Hilfe)](../../../wenn-etwas-schiefgeht.md)
- [Anleitung zu den Systeminformationen](../../../../anleitungen/systeminformationen.md)
- [Support-Stufen (Hosting)](../../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
