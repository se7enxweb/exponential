---
title: Steuerung des Stilleitfadens
audience: government, editor, project-manager, designer
category: public-sector
order: 130
summary: Wie eine öffentliche Stelle ihren redaktionellen und gestalterischen Stilleitfaden auf einer Exponential-Website wirksam hält - was das System durchsetzt (Klassen, Felder, der Rich-Text-Editor, Templates, Freigaben) und was der Stilleitfaden und seine Verantwortlichen selbst durchsetzen müssen.
keywords: Stilleitfaden, redaktionelle Standards, leichte Sprache, verständliche Sprache, Inhaltsgestaltung, Steuerung, Inhaltsklassen, Rich Text, Templates, Barrierefreiheit, Freigabe, Schulung, Webstandards, style guide, editorial standards, plain language, content design, governance, content classes, rich text, templates, accessibility, approval, training, web standards
type: help
---
# Steuerung des Stilleitfadens

Diese Seite richtet sich an die Verantwortlichen für den redaktionellen und gestalterischen Stilleitfaden einer öffentlichen Stelle. Ein
Stilleitfaden ist nur so gut wie seine Durchsetzung; diese Seite trennt, was Exponential über seine Struktur durchsetzen kann, von dem, was
Menschen und Prüfungen durchsetzen müssen. Leitlinien für verständliche Sprache in öffentlichen Stellen werden zum Beispiel unter
[plainlanguage.gov](https://www.plainlanguage.gov/) veröffentlicht; Regeln zur Barrierefreiheit stehen in [WCAG 2.2](https://www.w3.org/TR/WCAG22/).

## Inhalt
1. [Was das System durchsetzt](#was-das-system-durchsetzt)
2. [Was Menschen durchsetzen](#was-menschen-durchsetzen)
3. [Wer wofür verantwortlich ist](#wer-wofür-verantwortlich-ist)
4. [Den Stilleitfaden ändern](#den-stilleitfaden-ändern)
5. [Verwandte Seiten](#verwandte-seiten)

## Was das System durchsetzt

| Stilregel | Mechanismus | Wer sie ändert |
|---|---|---|
| Jede Seite hat einen Titel, eine Zusammenfassung, einen Kontakt | Pflichtfelder an der Inhaltsklasse | die Website-Entwicklung, über die Änderungssteuerung |
| Nur freigegebene Inhaltstypen | die Klassen, die Redaktionen anlegen dürfen (`content/create` mit einer Einschränkung `Class`) | Administratoren |
| Einheitliches Layout und einheitliche Typografie | Templates und Design, nicht die Formatierung durch die Redaktion | Gestaltung und Entwicklung |
| Begrenzte Formatierung im Text | die Konfiguration des Rich-Text-Editors | Entwicklung |
| Alternativtext für Bilder | ein Feld der Bildklasse; Pflicht, wenn Sie es dazu machen | die Website-Entwicklung |
| Jede Seite geprüft | der Freigabe-Workflow ([Überblick über Freigabe-Workflows](freigabe-workflows-im-ueberblick.md)) | Administratoren |

Änderungen an Klassen, Templates und Editoreinstellungen sind Konfiguration und Code; auf einer Live-Website mit
[ManagedByDeployment](../aktualisierungen-und-aenderungen/verwaltet-durch-bereitstellung.md) kommen sie nur über die Versionsverwaltung, was die stilistischen
Entscheidungen unter Änderungssteuerung hält.

Die mit Exponential ausgelieferten öffentlichen Designs wurden nicht auf Barrierefreiheit geprüft
([Standard zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)); ein Stilleitfaden, der WCAG-Konformität verlangt, braucht eine eigene
Prüfung Ihres Designs.

## Was Menschen durchsetzen

- Verständliche Sprache, Tonfall, Lesbarkeitsniveau und Terminologie.
- Richtige, aktuelle Informationen und benannte Verantwortung ([Verantwortung für Inhalte](verantwortung-fuer-inhalte.md)).
- Aussagekräftige Linktexte, Überschriften in der richtigen Reihenfolge, Tabellen nur für Daten.
- Alternativtexte, die den Zweck des Bildes beschreiben.

Freigebende sind der natürliche Kontrollpunkt: Eine Stil-Checkliste neben der Freigabeanfrage hilft ihnen, einheitlich zu entscheiden, und ein
Kommentar bei **Ablehnen**, der die Regel nennt, schult die Autorin oder den Autor.

## Wer wofür verantwortlich ist

| Rolle | Verantwortlich für |
|---|---|
| Verantwortliche Stelle für den Stilleitfaden (eine Funktion, zum Beispiel die Leitung der digitalen Kommunikation) | den Text des Leitfadens, Ausnahmen |
| Web-Team | Klassen, Pflichtfelder, Vorschläge für Editoreinstellungen |
| Freigebende | die Anwendung des Leitfadens vor der Veröffentlichung |
| Schulende | die Einarbeitung neuer Redaktionsmitglieder ([Kurs Redaktion 101](../../../../../eng-us/help/trainers/editors-101/README.md)) |

## Den Stilleitfaden ändern

1. Schlagen Sie die Änderung mit ihrer Begründung und den betroffenen Inhalten vor.
2. Wenn sie eine Klasse, ein Template oder den Editor ändert, reichen Sie eine Änderung bei Ihrem Änderungsbeirat ein
   ([Änderungsmanagement und Änderungsbeiräte](../aktualisierungen-und-aenderungen/aenderungsmanagement-und-aenderungsbeiraete.md)).
3. Informieren Sie Freigebende und Redaktionen; aktualisieren Sie die Checkliste.
4. Passen Sie bestehende Inhalte im nächsten Prüfzyklus an ([Prüftermine mit Objektzuständen](pruefdaten-mit-objektzustaenden.md)).

## Verwandte Seiten
- [Redaktionskalender](redaktionskalender.md)
- [Schritt der rechtlichen Prüfung](rechtliche-pruefung-als-schritt.md)
- [Standard zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)
- [Standard für Dokumentation und Veröffentlichung](../../../../../eng-us/standards/documentation-and-publishing.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
