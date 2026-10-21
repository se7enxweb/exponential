---
title: Downloads und Berechtigungen
audience: government, admin, website-creator
category: public-sector
order: 90
summary: Wer welches Dokument auf einer Exponential-Site herunterladen darf - content/download prüft den Lesezugriff, Sektionen und Rollen für eingeschränkte Dokumente, gespeicherte PDF-Exporte, die öffentlich sind, direkt ausgelieferte Bilder, das Verstecken und Entfernen von Dokumenten, und wie man testet.
keywords: Downloads, Berechtigungen, Lesezugriff, eingeschränkte Dokumente, Sektionen, Rollen, Mitgliederbereich, gespeichertes PDF, Speicher, Bilder, verstecken, abgemeldet testen, downloads, permissions, content/download, read access, restricted documents, sections, roles, members area, stored PDF, storage, images, hide, test logged out
type: help
---
# Downloads und Berechtigungen

Diese Seite richtet sich an Administratoren und Site-Ersteller, die Dokumente veröffentlichen, die nicht für alle
bestimmt sind, oder die sicher sein müssen, dass ein zurückgezogenes Dokument nicht mehr heruntergeladen werden kann.
Quellen: [Dateien und Downloads](../../../../../eng-us/help/creators/website-creators/files-and-downloads.md),
[Sektionen](../../../../anleitungen/sektionen.md) und [PDF-Exporte](../../../../anleitungen/pdf-exporte.md).

## Inhalt

1. [Wie ein Download geprüft wird](#wie-ein-download-geprüft-wird)
2. [Eingeschränkte Dokumente](#eingeschränkte-dokumente)
3. [Ausnahmen, die ohne Prüfung ausgeliefert werden](#ausnahmen-die-ohne-prüfung-ausgeliefert-werden)
4. [Ein Dokument zurückziehen](#ein-dokument-zurückziehen)
5. [Testen](#testen)

## Wie ein Download geprüft wird

Der Link eines Dokuments ist `content/download/<object ID>/<attribute ID>`. Die View prüft, dass der Besucher das
Objekt lesen darf (`content/read`) und dass mindestens ein Ort davon sichtbar ist (sofern versteckte Knoten nicht
angezeigt werden), und sendet dann die Datei; eine andere Version als die veröffentlichte braucht
`content/versionread` (`kernel/private/classes/views/content/download.php`). Ein Dokument ist also genau so öffentlich
wie das Objekt, das es enthält: Seine Sektion, sein Ort und die Rollen entscheiden.

## Eingeschränkte Dokumente

| Ziel | Wie |
|---|---|
| Dokumente nur für Mitarbeiter oder Mitglieder | eine Sektion, die Anonymous nicht lesen darf; eine Rolle mit `content/read` für diese Sektion, der Gruppe zugewiesen ([Sektionen, 13.1](../../../../anleitungen/sektionen.md#131-ein-mitgliederbereich)) |
| Dokumente für eine Partnergruppe | eine Einschränkung auf einen Teilbaum bei `content/read` für diese Gruppe |
| Ein Dokument, das bis zu einem Datum verfügbar ist | `hide.php` mit einem Datum zum Verstecken, oder verschieben Sie es in eine geschlossene Sektion ([Archiv und Ablauf](../nachrichten-und-warnmeldungen/archiv-und-ablauf.md)) |

Verlinken Sie nie direkt einen gespeicherten Dateipfad (`var/.../storage/...`); das umgeht die Prüfung.

## Ausnahmen, die ohne Prüfung ausgeliefert werden

| Element | Warum | Vorsicht |
|---|---|---|
| Gespeicherte PDF-Exporte (einmal erzeugt) | vom Webserver aus `var/<site>/storage/pdf/<file name>` ausgeliefert, öffentlich | verwenden Sie für eingeschränkte Inhalte Exporte, die bei Bedarf erzeugt werden ([PDF-Export](pdf-export.md#öffentlich-oder-geschützt)) |
| Bilder (Bildaliase) | die mitgelieferten Rewrite-Regeln liefern gespeicherte Bilder aus Geschwindigkeitsgründen direkt aus | legen Sie keine vertraulichen Inhalte in Bilder |
| Seiten des statischen Caches | Dateien, die ausgeliefert werden, ohne Exponential zu starten | nur öffentliche Seiten werden zwischengespeichert; Seiten für angemeldete Benutzer nicht |
| Kopien in Caches, CDNs, Suchmaschinen, auf Computern von Menschen | außerhalb des CMS | denken Sie vor dem Veröffentlichen nach |

## Ein Dokument zurückziehen

| Bedarf | Aktion | Dann |
|---|---|---|
| Downloads sofort stoppen | **Verstecken / Zeigen** am File-Objekt (und an seiner Publikationsseite) | der Download wird Besuchern verweigert |
| Es entfernen | **Entfernen** (Papierkorb) | wiederherstellbar, bis der Papierkorb geleert wird |
| Die alte Datei aus einem Objekt entfernen, das bleibt | ersetzen Sie die Datei, dann entfernen Sie die archivierten Versionen, die die alte enthalten ([Versionen von Dokumenten](versionen-von-dokumenten.md#eine-datei-entfernen-die-nicht-bleiben-darf)) | |
| Ein gespeicherter PDF-Export enthält es | **Neu erzeugen** Sie den Export oder entfernen Sie ihn | |

## Testen

- [ ] Melden Sie sich ab (oder verwenden Sie ein privates Browserfenster) und öffnen Sie den Link des Dokuments: Ein
  eingeschränktes Dokument muss eine Anmeldung verlangen oder verweigert werden.
- [ ] Melden Sie sich als Mitglied der Gruppe an: Es lädt herunter.
- [ ] Nach dem Verstecken: Der Link wird abgemeldet verweigert.
- [ ] Prüfen Sie die Adressen der gespeicherten PDF-Exporte unter **Setup > PDF-Export** auf alles, was nicht
  öffentlich sein sollte.

## Verwandte Seiten

- [Dokumente und Publikationen](README.md)
- [Berechtigungen für Besucher](../../../../../eng-us/help/creators/website-creators/visitor-permissions.md)
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
