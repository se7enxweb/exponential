---
title: Versionen von Dokumenten
audience: government, website-creator, editor, admin
category: public-sector
order: 40
summary: Wie Versionen von Dokumenten in Exponential funktionieren - das Ersetzen der Datei eines File-Objekts behält die Adresse und speichert die alte Datei in der Versionshistorie, die Grenze der Versionshistorie, wann stattdessen ein neues Objekt veröffentlicht wird und wie eine Datei entfernt wird, die nicht bleiben darf.
keywords: Versionen von Dokumenten, Datei ersetzen, neue Version, Versionshistorie, archivierte Version, ersetztes Dokument, Versionen entfernen, stabile Adresse, document versions, replace file, new version, version history, archived version, DefaultVersionHistoryLimit, VersionHistoryClass, superseded document, remove versions, stable address
type: help
---
# Versionen von Dokumenten

Diese Seite richtet sich an Site-Ersteller, Redakteure und Administratoren, die Dokumente aktualisieren. Jedes
Speichern eines Objekts ist eine Version; ein File-Objekt ist keine Ausnahme. Das Ersetzen einer Datei an Ort und Stelle
behält ihre Adresse, und die vorherige Datei bleibt in der Versionshistorie. Quellen:
[Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) und
[Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md).

## Inhalt

1. [Eine Datei ersetzen](#eine-datei-ersetzen)
2. [Wie lange alte Versionen aufbewahrt werden](#wie-lange-alte-versionen-aufbewahrt-werden)
3. [An Ort und Stelle ersetzen oder ein neues Dokument veröffentlichen?](#an-ort-und-stelle-ersetzen-oder-ein-neues-dokument-veröffentlichen)
4. [Eine Datei entfernen, die nicht bleiben darf](#eine-datei-entfernen-die-nicht-bleiben-darf)

## Eine Datei ersetzen

1. Öffnen Sie das File-Objekt (oder die verknüpfte Datei der Publikation), **Bearbeiten**.
2. Wählen Sie im Feld **Datei** die neue Datei; aktualisieren Sie bei Bedarf die Beschreibung (zum Beispiel
   „Aktualisiert am <Datum>: Tabelle 3 korrigiert“).
3. **Zur Veröffentlichung schicken**. Links auf das Objekt funktionieren weiter; der Download liefert nun die neue
   Datei.
4. Die vorherige Version wird mit ihrer Datei archiviert; **Versionen verwalten** listet sie, und **Ansehen** öffnet
   sie.

## Wie lange alte Versionen aufbewahrt werden

| Einstellung (`settings/content.ini [VersionManagement]`) | Mitgeliefert | Bedeutung |
|---|---|---|
| `DefaultVersionHistoryLimit` | 45 | die meisten Versionen, die ein Objekt behält; an der Grenze wird die älteste archivierte Version entfernt, um Platz zu schaffen |
| `VersionHistoryClass[<class id>]` | `VersionHistoryClass[1]=75` (Klasse 1 ist Folder) | eine Grenze für eine Klasse |

Setzen Sie eigene Grenzen in `settings/override/content.ini.append.php`, wenn Dokumente jede Version behalten müssen,
zum Beispiel für die Klasse File oder eine Klasse Publication, und leeren Sie den INI-Cache. Der Aufräumbefehl
`./console exp:cleanupversions` entfernt alte Versionen; lesen Sie zuerst seine `--help`
([Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#3-ein-objekt-erstellen-und-bearbeiten)).

## An Ort und Stelle ersetzen oder ein neues Dokument veröffentlichen?

| Situation | Tun Sie |
|---|---|
| Eine Korrektur desselben Dokuments | an Ort und Stelle ersetzen; die Korrektur vermerken |
| Eine neue Ausgabe (Bericht 2026 nach dem Bericht 2025) | ein neues Objekt; das alte bleibt veröffentlicht |
| Eine neue Fassung eines Leitfadens, die den alten ersetzt | ein neues Objekt, das alte als ersetzt markiert (ein Objektzustand), mit einem Link auf das neue |
| Eine Übersetzung | ein verknüpftes File-Objekt je Sprache, oder eine Übersetzung des Objekts mit eigener Datei |

Ein neues Objekt für eine neue Ausgabe hält frühere Ausgaben zitierbar; das Ersetzen an Ort und Stelle behält eine
einzige Adresse für ein lebendes Dokument.

## Eine Datei entfernen, die nicht bleiben darf

Wenn eine Datei versehentlich veröffentlicht wurde (falsches Dokument, personenbezogene Daten):

1. Ersetzen Sie sie sofort durch die richtige Datei, oder verstecken Sie das Objekt (**Verstecken / Zeigen**).
2. Entfernen Sie die Versionen, die die falsche Datei noch enthalten: **Versionen verwalten**, Filter **Archiviert**,
   markieren Sie sie, **Ausgewählte Versionen entfernen**, **Endgültig entfernen**. Die veröffentlichte Version lässt
   sich so nicht entfernen, deshalb ersetzen Sie zuerst. Es braucht `content/versionremove`.
3. Leeren Sie Caches, die eine Kopie enthalten können; ein gespeicherter PDF-Export wird getrennt neu erzeugt oder
   entfernt ([PDF-Export](pdf-export.md)).
4. Halten Sie fest, was getan wurde; informieren Sie Ihren Datenschutzbeauftragten, wenn personenbezogene Daten offengelegt
   wurden.

Das Entfernen von Versionen ist endgültig und wird im Audit-Protokoll festgehalten.

## Verwandte Seiten

- [Dokumente und Publikationen](README.md)
- [Metadaten und Verschlagwortung](metadaten-und-verschlagwortung.md)
- [Entwürfe und ausstehende Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
