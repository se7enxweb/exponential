---
title: Eine Übersetzung sicher entfernen
audience: government, admin, editor
category: public-sector
order: 280
summary: Der Unterschied zwischen dem Entfernen einer Sprache und dem Entfernen der Übersetzung eines Eintrags in Exponential, warum das Entfernen einer Übersetzung Inhalte in jeder Version löscht, die sichere Reihenfolge der Schritte, um Inhalte in die richtige Sprache zu verschieben, und wer das Recht dazu haben sollte.
keywords: Übersetzung entfernen, Sprache entfernen, Übersetzung löschen, falsche Sprache, Übersetzung verschieben, Als Haupt setzen, Ausgewählte entfernen, content/translations, Schriftgut, remove translation, remove language, delete translation, wrong language, move translation, Set main, Remove selected, record keeping
type: help
---
# Eine Übersetzung sicher entfernen

Diese Seite richtet sich an Administratoren und leitende Redakteure. Zwei Arten des Entfernens sehen ähnlich aus und
sind sehr verschieden
([Inhaltssprachen, Abschnitt 9](../../../anleitungen/inhaltssprachen.md#9-übersetzungen-und-sprachen-sicher-entfernen)).

## Inhalt

1. [Sprache oder Übersetzung](#sprache-oder-übersetzung)
2. [Inhalte in der falschen Sprache](#inhalte-in-der-falschen-sprache)
3. [Eine Sprache entfernen, die niemand nutzt](#eine-sprache-entfernen-die-niemand-nutzt)
4. [Wer es dürfen sollte](#wer-es-dürfen-sollte)

## Sprache oder Übersetzung

| Aktion | Wo | Löscht | Rückgängig machen |
|---|---|---|---|
| Eine **Sprache** entfernen | Setup > Sprachen | nur die Zeile der Sprache; verweigert, solange irgendein Objekt oder eine Klasse eine Übersetzung darin hat | sie erneut hinzufügen |
| Die **Übersetzung** eines Eintrags entfernen | der Reiter Übersetzungen des Eintrags | alle Felder und Namen des Eintrags in dieser Sprache, **in jeder Version** | nur aus einer Datensicherung |
| Die Übersetzung einer Klasse entfernen | das Fenster Übersetzungen der Klasse | Name und Beschreibung der Klasse in dieser Sprache | die Klasse erneut übersetzen |

Für eine öffentliche Stelle entfernt das Entfernen einer Übersetzung auch den Nachweis dessen, was in dieser Sprache
veröffentlicht wurde. Prüfen Sie vor dem Entfernen, ob Verbergen oder ein Hinweis besser geeignet ist (siehe
„Veröffentlichte Informationen zurücknehmen und berichtigen“ im Bereich Transparenz).

## Inhalte in der falschen Sprache

Wenn Inhalte in der falschen Sprache angelegt wurden (zum Beispiel britisches statt amerikanisches Englisch):

1. **Übersetzen Sie sie in die richtige Sprache**: Bearbeiten, **Neue Übersetzung** (richtige Sprache), **Übersetzung
   basiert auf** (falsche Sprache), **Zur Veröffentlichung schicken**. Jetzt gibt es beide Übersetzungen.
2. **Machen Sie die richtige Sprache zur Hauptsprache**: Reiter Übersetzungen, Spalte **Haupt**, **Als Haupt
   setzen**.
3. **Entfernen Sie die falsche Übersetzung**: Wählen Sie sie aus, **Ausgewählte entfernen**, bestätigen.

Kein mitgelieferter Konsolenbefehl verschiebt Übersetzungen in großer Zahl; bei vielen Einträgen ist ein Skript gegen
die Kernel-API, erprobt an einer Kopie der Datenbank, der richtige Weg.

## Eine Sprache entfernen, die niemand nutzt

1. Unter **Setup > Sprachen** muss die Karte *Unbenutzt* anzeigen und angeben, dass sie entfernt werden kann.
2. Setzen Sie das Häkchen bei **Zum Entfernen auswählen**, klicken Sie auf **Ausgewählte entfernen** und bestätigen
   Sie.
3. Nehmen Sie die Locale aus der `SiteLanguageList[]` jedes Siteaccess heraus; leeren Sie den INI-Cache und den Cache
   `content_language` (`php bin/php/ezcache.php --clear-tag=ini`, `--clear-id=content_language`) sowie den
   Antwort-Cache von Velocity.

## Wer es dürfen sollte

Geben Sie das Recht, Übersetzungen zu entfernen, nur Administratoren oder leitenden Redakteuren. Entfernungen werden
im Audit-Trail festgehalten ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)); der entfernte Text selbst ist aus
der Datenbank verschwunden.

## Verwandte Seiten

- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Hauptsprache eines Eintrags](hauptsprache-eines-eintrags.md)
- [Rollen und Rechte für Übersetzer](rollen-und-rechte-fuer-uebersetzer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
