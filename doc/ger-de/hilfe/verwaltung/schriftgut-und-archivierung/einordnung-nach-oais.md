---
title: OAIS und die Abgabe von Inhalten an ein Archiv
audience: government, project-manager, admin
category: public-sector
order: 180
summary: Für öffentliche Stellen, die Website-Inhalte an ein digitales Archiv abgeben - das Referenzmodell OAIS (ISO 14721) allgemein eingeordnet, wo Exponential steht (eine abgebende Stelle, kein Archiv) und was eine Abgabe aus Exponential enthalten kann (Inhalte, Metadaten, Dateien, Angaben zur Integrität, Audit-Nachweise).
keywords: OAIS, ISO 14721, Open Archival Information System, digitale Langzeitarchivierung, Übergabepaket, SIP, Produzent, abgebende Stelle, Archiv, Übergabe, Metadaten, Integrität, Prüfsummen, Export, öffentlicher Sektor, Staatsarchiv, Landesarchiv, digital preservation, submission information package, producer, archive, transfer, metadata, fixity, checksums, national archives
type: help
---
# OAIS und die Abgabe von Inhalten an ein Archiv

Diese Seite richtet sich an Zuständige für Schriftgut und Projektleitungen, die Abgaben von einer Website an ein
digitales Archiv vorbereiten, etwa an ein Staats- oder Landesarchiv. Das Referenzmodell Open Archival Information
System (OAIS), ISO 14721:2012 (<https://www.iso.org/standard/57284.html>), ist das gemeinsame Vokabular solcher
Archive. Diese Seite ordnet es allgemein ein und zitiert die Norm nicht.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Rollen im Modell](#rollen-im-modell)
2. [Was eine Abgabe aus Exponential enthalten kann](#was-eine-abgabe-aus-exponential-enthalten-kann)
3. [Eine Abgabe vorbereiten](#eine-abgabe-vorbereiten)
4. [Verwandte Seiten](#verwandte-seiten)

## Rollen im Modell

Allgemein gesprochen unterscheidet OAIS den **Produzenten** (die abgebende Stelle), der Informationen übergibt, das
**Archiv**, das sie erhält, und die **Nutzer**, die sie später verwenden. Eine mit Exponential betriebene Website steht
auf der Seite des Produzenten: Sie ist kein Archiv und erhebt keinen Anspruch auf OAIS-Konformität. Die
Übergabevereinbarung Ihres Archivs sagt, was es annimmt.

## Was eine Abgabe aus Exponential enthalten kann

| Teil | Quelle in Exponential |
|---|---|
| Inhalte | die Objekte: über die REST-API (JSON), Pakete (`.ezpkg`) oder den PDF-Export (menschenlesbar) |
| Dateien | die hochgeladenen Dateien aus Datei- und Bildfeldern, aus `var/storage` oder über Downloads |
| Beschreibende Metadaten | die Felder Ihrer Klassen (Titel, Daten, Autorenfelder), Sektion, Objektzustände, Orte im Baum |
| Herkunft | Versionen mit Erstellern und Daten; Audit-Einträge (wer veröffentlicht hat, wenn `content.object.publish` eingeschaltet war) |
| Integrität | Prüfsummen, die Sie beim Export berechnen; der Audit-Export gibt einen sha256 aus; Audit-Archive tragen signierte Manifeste |
| Kontext | die Klassendefinitionen (Setup > Klassen), um die Felder zu erklären |

## Eine Abgabe vorbereiten

1. Vereinbaren Sie die Formate mit dem Archiv (siehe „Dateiformate für die Langzeiterhaltung“).
2. Legen Sie nach Ihrem Aufbewahrungsplan fest, was abgegeben wird: nach Klasse, Sektion, Zustand oder Teilbaum.
3. Exportieren Sie auf dem gewählten Weg und berechnen Sie Prüfsummen für jede Datei.
4. Fügen Sie die Metadaten hinzu, die das Archiv verlangt; ordnen Sie Ihre Klassenfelder dessen Schema zu.
5. Legen Sie ein Exportprotokoll bei: wer, wann, welche Version jedes Objekts, welches Werkzeug mit welchen
   Einstellungen.
6. Nachdem das Archiv den Empfang bestätigt hat, wenden Sie Ihren Plan auf der Website an (Archivordner, Verstecken
   oder Entfernen).

## Verwandte Seiten

- [Inhalte exportieren](inhalte-exportieren.md)
- [Schriftgutverwaltung und ein Web-CMS](einordnung-der-schriftgutverwaltung.md)
- [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
