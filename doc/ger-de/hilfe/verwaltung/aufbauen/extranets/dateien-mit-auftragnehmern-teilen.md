---
title: Dateien mit Auftragnehmern teilen
audience: government, editor, admin
category: public-sector
order: 50
summary: Wie Sie Dokumente über ein Exponential-Extranet so mit Auftragnehmern und Partnern teilen, dass nur sie sie herunterladen können - Dateien in der Sektion der Partei, von content/download geprüfte Downloads, Uploads durch Auftragnehmer und was Sie nicht tun sollten.
keywords: Dateien teilen, Auftragnehmer, geschützte Downloads, Extranet-Dateien, Uploads, Vertraulichkeit, Dokumentenaustausch, file sharing, contractors, protected downloads, content/download, extranet files, uploads, confidentiality, document exchange
type: help
---
# Dateien mit Auftragnehmern teilen

Diese Seite richtet sich an Redakteure und Administratoren. Dateien sind Inhaltsobjekte mit einem Dateifeld; Downloads
laufen über `content/download`, das prüft, ob der Besucher das Objekt lesen darf, bevor es die Datei sendet. Eine Datei
in einer Sektion, die nur die Gruppe eines Auftragnehmers lesen darf, kann also nur von dieser Gruppe heruntergeladen
werden ([Dateien und Downloads](../../../../../eng-us/help/creators/website-creators/files-and-downloads.md#protected-downloads)).

## Inhalt

1. [Eine Datei für eine Partei veröffentlichen](#eine-datei-für-eine-partei-veröffentlichen)
2. [Uploads durch Auftragnehmer](#uploads-durch-auftragnehmer)
3. [Was Sie nicht tun sollten](#was-sie-nicht-tun-sollten)
4. [Große und sensible Dateien](#große-und-sensible-dateien)

## Eine Datei für eine Partei veröffentlichen

1. Öffnen Sie den Ordner der Partei in der **Inhaltsstruktur** (er liegt in der Sektion der Partei).
2. Unter **Unterelemente** **Neu erstellen**, **File**; hochladen; **Zur Veröffentlichung schicken**.
3. Testen Sie den Download mit einem Testkonto dieser Partei und abgemeldet (er muss verweigert werden).

## Uploads durch Auftragnehmer

Eine Rolle mit `content/create` für die Klasse File, eingeschränkt auf den Teilbaum der Partei, lässt Auftragnehmer
Lieferungen hochladen. Fügen Sie für diese Sektion einen Freigabe-Workflow hinzu, wenn ein Projektleiter Dateien
annehmen muss, bevor andere in der Gruppe sie sehen.

## Was Sie nicht tun sollten

- Verlinken Sie Dateien nicht direkt aus `var/.../storage/`; das umgeht die Prüfung der Berechtigungen.
- Legen Sie keine Dateien für verschiedene Parteien in denselben Ordner.
- Verwenden Sie für vertrauliche Dateien nicht die Medienbibliothek der öffentlichen Site: Sie ist meist für Besucher
  lesbar.

## Große und sensible Dateien

Upload-Grenzen kommen aus PHP (`upload_max_filesize`, `post_max_size`). Für eingestuftes oder sehr großes Material
verwenden Sie den Kanal, den Ihre Sicherheitsregeln vorschreiben; ein mit Exponential gebautes Extranet ist ein System
zum Veröffentlichen im Web, kein Dienst zur sicheren Dateiübertragung.

## Verwandte Seiten

- [Dateien und Downloads](../../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Benutzergruppen und Sektionen für Extranets](benutzergruppen-und-sektionen-fuer-extranets.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
