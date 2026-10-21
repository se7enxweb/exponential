---
title: Ansprechpartner und Dienststellen
audience: government, website-creator, editor
category: public-sector
order: 60
summary: Wie Sie Ansprechpartner und Dienststellen in Exponential als eigene Objekte modellieren, sie von jeder Leistung verknüpfen, die sie bearbeiten, Öffnungszeiten und Barrierefreiheit zeigen und sie an einer Stelle ändern.
keywords: Ansprechpartner, Dienststelle, Schalter, Öffnungszeiten, Adresse, Telefon, gemeinsames Postfach, Beziehung, umgekehrte Beziehungen, Barrierefreiheit von Gebäuden, contact point, office, desk, opening hours, address, phone, shared mailbox, relation, ezobjectrelation, reverse relations, accessibility of buildings
type: help
---
# Ansprechpartner und Dienststellen

Diese Seite richtet sich an Site-Ersteller und Redakteure, die Kontaktinformationen zu Leistungen verwalten. Wenn jede
Leistungsseite ihre eigene Telefonnummer und ihre eigenen Öffnungszeiten eintippt, bedeutet eine Änderung der Zeiten
das Bearbeiten von Dutzenden Seiten. Mit einem Objekt je Ansprechpartner, von jeder Leistung verknüpft, wird die
Änderung einmal gemacht. Beziehungen erklärt
[Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md).

## Inhalt

1. [Die Klasse für Ansprechpartner](#die-klasse-für-ansprechpartner)
2. [Leistungen mit Ansprechpartnern verknüpfen](#leistungen-mit-ansprechpartnern-verknüpfen)
3. [Den Kontakt auf der Leistungsseite zeigen](#den-kontakt-auf-der-leistungsseite-zeigen)
4. [Öffnungszeiten und Schließungen ändern](#öffnungszeiten-und-schließungen-ändern)
5. [Personenbezogene Daten](#personenbezogene-daten)

## Die Klasse für Ansprechpartner

Legen Sie sie unter **Setup > Klassen** an
([Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)):

| Attribut | Bezeichner | Datentyp | Erforderlich |
|---|---|---|---|
| Name | `name` | Textzeile (`ezstring`) | ja |
| Art | `kind` | Auswahlliste (`ezselection`): Dienststelle, Telefonleitung, nur online | ja |
| Adresse | `address` | Textabschnitt (`eztext`) | nein |
| Öffnungszeiten | `opening_hours` | XML Block (`ezxmltext`) | nein |
| Vorübergehende Änderungen | `changes` | XML Block | nein |
| Telefon | `phone` | Textzeile | nein |
| E-Mail | `email` | E-Mail (`ezemail`) | nein |
| Kontaktformular | `form` | Objekt Verknüpfung (`ezobjectrelation`) zu einem Feedback-Formular | nein |
| Barrierefreiheit | `access` | XML Block | nein |
| Standort auf einer Karte | `location` | Google Maps Location (`ezgmaplocation`), optional | nein |

Halten Sie Ansprechpartner in einem Ordner „Ansprechpartner“ (oder unter der Seite jeder Dienststelle). Sie müssen
nicht in Menüs erscheinen.

Der Datentyp für Kartenstandorte verwendet Google Maps, einen Dienst eines Drittanbieters, und braucht einen Schlüssel
([ezgmaplocation](../../../../../eng-us/features/6.0/extensions/ezgmaplocation.md)); eine Adresse als Text plus ein Link zu Ihrem
eigenen Kartendienst passt vielleicht besser zu Ihrer Datenschutzrichtlinie.

## Leistungen mit Ansprechpartnern verknüpfen

In der Klasse Service ist das Attribut **Kontakt** eine Objekt Verknüpfung, eingeschränkt auf die Klasse für
Ansprechpartner. Redakteure wählen den Kontakt mit **Vorhandenes hinzufügen**
([Inhalte anlegen und bearbeiten](../../../inhalte-bearbeiten.md#verknüpfungen-und-bilder)). Mehrere Leistungen verknüpfen
denselben Ansprechpartner.

Um zu sehen, welche Leistungen einen Ansprechpartner verwenden, öffnen Sie ihn und sehen Sie sich seinen Reiter
**Verknüpfungen** an: **Rückwärts verknüpfte Objekte** listet die Objekte, die auf ihn verweisen (umgekehrte
Beziehungen). Prüfen Sie das, bevor Sie einen Ansprechpartner entfernen; die Prüfung umgekehrter Beziehungen warnt vor
Inhalten, die noch auf ein zu entfernendes Objekt zeigen
([Prüfung umgekehrter Beziehungen](../../../../../eng-us/features/6.0/reverse-relation-check.md)).

## Den Kontakt auf der Leistungsseite zeigen

Im Template der Leistung:

```text
{if $node.data_map.contact.has_content}
  {def $contact = $node.data_map.contact.content}
  <h2>{'Contact'|i18n( 'design/<your design>' )}</h2>
  <p><strong>{$contact.name|wash}</strong></p>
  {attribute_view_gui attribute=$contact.data_map.opening_hours}
  {if $contact.data_map.changes.has_content}{attribute_view_gui attribute=$contact.data_map.changes}{/if}
  {undef $contact}
{/if}
```

Der View-Cache einer Leistungsseite wird geleert, wenn die Leistung veröffentlicht wird. Ändert sich nur der
Ansprechpartner, müssen auch die Leistungsseiten geleert werden, die ihn zeigen; der Content-Cache-Manager leert die
Caches verknüpfter Objekte gemäß den Einstellungen in `viewcache.ini`, also testen Sie es: Ändern Sie die Zeiten und
öffnen Sie dann abgemeldet eine Leistungsseite ([Caches](../../../../anleitungen/caches.md)).

## Öffnungszeiten und Schließungen ändern

| Änderung | Tun Sie |
|---|---|
| Dauerhaft neue Zeiten | bearbeiten Sie **Öffnungszeiten**, veröffentlichen Sie |
| Ein Feiertag oder eine kurze Schließung | füllen Sie **Vorübergehende Änderungen** („Geschlossen am <Datum>“), veröffentlichen Sie; leeren Sie es danach |
| Eine Dienststelle schließt endgültig | verknüpfen Sie die Leistungen mit dem neuen Ansprechpartner, dann verstecken Sie den alten |

## Personenbezogene Daten

Ansprechpartner nennen Schalter, Teams und gemeinsame Postfächer, nicht einzelne Bedienstete. Gemeinsame Kontaktdaten
überdauern Personalwechsel und vermeiden es, unnötig personenbezogene Daten zu veröffentlichen.

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](README.md)
- [Leistungen modellieren](leistungen-modellieren.md)
- [Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
