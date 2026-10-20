---
title: Öffnungszeiten oder Kontaktdaten aktualisieren
audience: government, editor
category: public-sector
order: 290
summary: Für Redakteure - Öffnungszeiten, eine Schließung an Feiertagen oder eine Telefonnummer einmal im Objekt der Kontaktstelle oder Dienststelle ändern, sodass jede Dienstleistungsseite, die es verknüpft, die Änderung zeigt, und Seiten finden, in denen die alten Angaben noch eingetippt sind.
keywords: Öffnungszeiten, Kontaktdaten, Schließung an Feiertagen, Telefonnummer, Kontaktstelle, Dienststelle, Relationen, Rückwärts verknüpfte Objekte, Suche, einmal aktualisieren, opening hours, contact details, holiday closure, phone number, contact point, office, relations, reverse related objects, search, update once
type: help
---
# Öffnungszeiten oder Kontaktdaten aktualisieren

Diese Seite ist für Redakteure, die Öffnungszeiten, Schließungen oder Kontaktdaten ändern. Wenn die Site
Kontaktinformationen in Objekten für Kontaktstellen hält, die von Dienstleistungsseiten aus verknüpft sind, ändern Sie
sie einmal ([Kontaktstellen und Dienststellen](../../../../../eng-us/help/government/building/service-catalog/contact-points-and-offices.md)). Wo die
Angaben in Seiten eingetippt wurden, müssen Sie jede finden und ändern.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Sie kennen die neuen Zeiten oder Angaben und ab wann sie gelten.
- Sie wissen, wo die Kontaktstellen auf Ihrer Site liegen (ein Ordner „Contact points“ oder Seiten der Dienststellen).

## Schritte

**Dauerhafte Änderung:**

1. Öffnen Sie die Kontaktstelle (oder die Seite der Dienststelle), **Bearbeiten**.
2. Ändern Sie **Opening hours**, **Phone** oder **E-mail**.
3. **Zur Veröffentlichung schicken**.

**Vorübergehende Änderung (Feiertag, Schließung):**

1. **Bearbeiten** Sie die Kontaktstelle.
2. Schreiben Sie unter **Temporary changes** (wenn die Klasse es hat) „Geschlossen am <Datum>“ oder „Geöffnet von 10:00
   bis 14:00 Uhr am <Datum>“.
3. **Zur Veröffentlichung schicken**; leeren Sie das Feld nach dem Datum.

**Seiten finden, in denen die alten Angaben noch eingetippt sind:**

1. Suchen Sie mit der Suche der Administration (`/content/search`) nach der alten Telefonnummer oder E-Mail-Adresse.
2. Bearbeiten Sie jedes Ergebnis; ersetzen Sie die eingetippten Angaben durch eine Relation zur Kontaktstelle, wo die
   Klasse es erlaubt.

## Das Ergebnis prüfen

- [ ] Die Seite der Kontaktstelle zeigt abgemeldet die neuen Angaben.
- [ ] Eine Dienstleistungsseite, die sie verknüpft, zeigt die neuen Angaben. Zeigt sie noch die alten, wurde ihre
  Ansicht im Cache noch nicht erneuert; fragen Sie das Web-Team oder siehe
  [Eine Seite aktualisieren, die alte Inhalte zeigt](eine-seite-aktualisieren-die-alte-inhalte-zeigt.md).
- [ ] An der Kontaktstelle listet der Reiter **Verknüpfungen**, **Rückwärts verknüpfte Objekte**, die Dienstleistungen,
  die sie verwenden.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Die Zeiten in jede Dienstleistungsseite eintippen | verknüpfen Sie stattdessen die Kontaktstelle |
| Eine persönliche Telefonnummer oder ein Name | verwenden Sie Team-Nummern und Funktionspostfächer |
| Das Entfernen eines vorübergehenden Hinweises vergessen | tragen Sie eine Erinnerung in Ihren Kalender ein |
| Zeiten in einem Bild | nur Text; Bilder sind nicht für alle lesbar |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Dienstleistungsseite veröffentlichen](eine-dienstleistungsseite-veroeffentlichen.md)
- [Suchen](../../../suchen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
