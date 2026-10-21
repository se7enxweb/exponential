---
title: Open-Source-Beschaffung
audience: government, project-manager
category: public-sector
order: 100
summary: Wie eine öffentliche Stelle Leistungen rund um freie Software wie Exponential einkauft, wenn es keine Lizenz zu kaufen gibt - was zu beschaffen ist (Hosting, Entwicklung, Support, Schulung), Anforderungen, die Sie unabhängig halten, wie Sie Angebote bewerten und was in den Vertrag gehört.
keywords: Beschaffung, Ausschreibung, Vergabe, Open-Source-Beschaffung, Leistungen, Supportvertrag, Hostingvertrag, Anforderungen, Bewertung, Ausstiegsklausel, Lieferung des Quellcodes, Souveränität, procurement, tender, open source procurement, support contract, hosting contract, evaluation, exit clause, source code delivery, sovereignty
type: help
---
# Open-Source-Beschaffung

Diese Seite richtet sich an Projektleitungen und Beschaffungsverantwortliche. Für Exponential fallen keine
Lizenzgebühren an: „Using Exponential under the terms of the GNU GPL is free of charge“ (die Nutzung unter den
Bedingungen der GNU GPL ist kostenlos; [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)). Was eine
öffentliche Stelle beschafft, sind Leistungen. Das Vergaberecht unterscheidet sich je nach Rechtsordnung; der Bereich
dieser Hilfe zur Beschaffung behandelt das Verfahren ausführlicher.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was es zu kaufen gibt](#was-es-zu-kaufen-gibt)
2. [Anforderungen, die Sie unabhängig halten](#anforderungen-die-sie-unabhängig-halten)
3. [Angebote bewerten](#angebote-bewerten)
4. [Vertragsklauseln](#vertragsklauseln)
5. [Vermeiden Sie Folgendes](#vermeiden-sie-folgendes)

## Was es zu kaufen gibt

| Los | Typischer Inhalt | Wer es anbietet |
|---|---|---|
| Hosting und Betrieb | Server, Datenbank, Datensicherungen, Updates, Überwachung | Hosting-Unternehmen, Partner ([Hosting-Partner](../../partner/hosting-partner.md)) |
| Aufbau | Inhaltsmodell, Design, Templates, Migration | Implementierungspartner ([Implementierungspartner](../../partner/implementierungspartner.md)) |
| Support | Reaktionszeiten, Fehlerbehebungen, Hilfe bei Aktualisierungen | Partner, 7x ([Supportstufen und Reaktionszeiten](../../partner/support-stufen-und-reaktionszeiten.md)) |
| Schulung | Redaktion, Administration, Entwicklung | Schulungspartner ([Kurskatalog](../../../../eng-us/help/trainers/course-catalogue.md)) |
| Entwicklung von Funktionen | neue Funktionen, an das Projekt zurückgegeben | jede qualifizierte Entwicklerin, jeder qualifizierte Entwickler ([Funktionswünsche und gesponserte Funktionen](../../partner/funktionswuensche-und-gesponserte-funktionen.md)) |

Getrennte Lose ermöglichen es, einen Dienstleister zu wechseln, ohne alle zu wechseln.

## Anforderungen, die Sie unabhängig halten

- Sämtlicher eigens entwickelter Code (Erweiterungen, Designs, Einstellungen) wird in einem Git-Repository geliefert,
  das der Stelle gehört, unter der GPL oder einer Lizenz, die die Stelle im Rahmen der GPL wählt.
- Keine eigenen Änderungen am Kernel; Anpassungen gehören in Erweiterungen und Overrides, damit Aktualisierungen
  möglich bleiben.
- Eine Dokumentation der Installation (Siteaccesses, Erweiterungen, Cronjobs, Overrides der Einstellungen) wird
  geliefert und aktuell gehalten.
- Inhalte und Dateien lassen sich jederzeit in dokumentierten Formaten exportieren (siehe „Inhalte und Pakete
  exportieren“).
- Hosting in den Regionen, die die Stelle verlangt; Datensicherungen werden auf Anfrage ausgehändigt.
- Optionale Dienste von Drittanbietern sind aufgelistet und von der Stelle genehmigt (siehe „Zu prüfende Dienste von
  Drittanbietern“).

## Angebote bewerten

| Kriterium | Nachweis, den Sie verlangen sollten |
|---|---|
| Erfahrung mit Exponential | Referenzen, die der Bieter nennen darf, Beispielcode auf GitHub |
| Beiträge zum Projekt | veröffentlichte Erweiterungen, angenommene Pull Requests |
| Kompetenz im Betrieb | Runbooks, Tests von Datensicherung und Wiederherstellung |
| Umgang mit Sicherheit | Verfahren für Updates und Vorfälle ([Verantwortlichkeiten für die Sicherheit](../../partner/sicherheitspflichten.md)) |
| Unterstützung beim Ausstieg | wie die Übergabe an einen Nachfolger erfolgt |

## Vertragsklauseln

- Lieferung von Quellcode und Dokumentation zu jedem Meilenstein und am Ende.
- Eine Ausstiegsklausel: Unterstützung bei der Übergabe, Datenexport, Fristen.
- Service-Level für Support und Sicherheitsupdates.
- Wem die Hosting-Konten und Domainnamen gehören (der Stelle selbst).
- Rückgabe allgemeiner Verbesserungen an das Projekt, wenn die Stelle das wünscht (siehe „Beiträge als öffentliche
  Stelle“).

## Vermeiden Sie Folgendes

- Anforderungen, die das proprietäre Zusatzprodukt eines einzelnen Anbieters als Pflicht vorschreiben.
- Verträge, bei denen der Dienstleister die einzige Kopie des Codes oder das einzige Administratorkonto hält.
- Eigene Kernel-Patches, die nur ein einziger Anbieter versteht.

## Verwandte Seiten

- [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
- [Partner: hier beginnen](../../partner/partner-hier-beginnen.md)
- [Ein Exponential-Projekt planen](../../fuehrung/ein-exponential-projekt-planen.md)
- [Beschaffung](../../../../eng-us/help/government/procurement/README.md): der Bereich Beschaffung der Hilfe für Verwaltungen
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
