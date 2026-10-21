---
title: Dateien und digitale Sammlungen
audience: businesses, editor, website-creator
category: business-solutions
order: 50
summary: Wie eine Bibliothek oder ein Archiv digitalisierte Objekte auf Exponential anbietet - File-Objekte und Downloads mit Berechtigungsprüfung, offene und eingeschränkte Sammlungen, die Lizenz jedes Objekts angeben, von Besuchern geschickte Dateien mit enhancedezbinaryfile, große Dateien, Writer-Importe und das Finden fehlender oder ungenutzter Dateien.
keywords: digitale Sammlung, digitalisiert, Downloads, Dateien, ezbinaryfile, enhancedezbinaryfile, geschützter Download, Lizenz, Creative Commons, gemeinfrei, xrowmetadata, fehlende Dateien, Speicher, digital collection, digitized, downloads, files, protected download, license, public domain, missing files, storage
type: help
---
# Dateien und digitale Sammlungen

Diese Seite ist für Bibliothekare, Archivare und Verantwortliche von Sammlungen, die digitalisiertes Material
veröffentlichen, und für den Website-Ersteller, der es einrichtet. Sie erklärt, wie Dateien gespeichert und
ausgeliefert werden, wie manches Material eingeschränkt bleibt, wie Sie die Lizenz jedes Objekts angeben und wie Sie
den Dateispeicher in Ordnung halten.

## Inhalt

1. [Wie Dateien gespeichert und ausgeliefert werden](#wie-dateien-gespeichert-und-ausgeliefert-werden)
2. [Offenes und eingeschränktes Material](#offenes-und-eingeschränktes-material)
3. [Die Lizenz jedes Objekts angeben](#die-lizenz-jedes-objekts-angeben)
4. [Von Besuchern geschickte Dateien](#von-besuchern-geschickte-dateien)
5. [Große Dateien und Formate](#große-dateien-und-formate)
6. [In Writer geschriebene Dokumente](#in-writer-geschriebene-dokumente)
7. [Den Speicher in Ordnung halten](#den-speicher-in-ordnung-halten)
8. [Checkliste für Sammlungen](#checkliste-für-sammlungen)

## Wie Dateien gespeichert und ausgeliefert werden

- Ein **File**-Objekt (Klasse `file`) enthält ein Dokument in seinem Attribut `file` (Datentyp `ezbinaryfile`).
  Uploads von Dateien, die keine Bilder sind, werden zu File-Objekten (`DefaultClass=file` in `settings/upload.ini`).
- Eine Eintragsklasse des Katalogs kann ein eigenes Dateifeld haben; Eintrag und digitalisierte Kopie sind dann ein
  Objekt ([Katalogeinträge modellieren](katalogeintraege-modellieren.md#ein-eintrag-feld-für-feld)).
- Die Download-Adresse ist `content/download/<object ID>/<attribute ID>`. Die Ansicht prüft, ob der Besucher das Objekt
  lesen darf, und schickt dann die Datei.

Quelle: [Dateien und Downloads, How a download works](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#how-a-download-works).
Nennen Sie Lesern Format und Größe, bevor sie klicken: „it is also an accessibility requirement for many sites“ (für
viele Websites ist das auch eine Anforderung der Barrierefreiheit, dieselbe Seite).

## Offenes und eingeschränktes Material

| Ziel | Wie |
|---|---|
| Für alle offen | Eine Sektion, die der anonyme Benutzer lesen darf |
| Nur für Mitglieder oder registrierte Leser | Eine Sektion, die nur Mitglieder lesen dürfen |
| Nur für Mitarbeiter (interne Kopien, Master-Scans) | Eine Sektion, die nur Mitarbeiterrollen lesen dürfen |
| Für eine begrenzte Zeit verfügbar | Verbergen Sie es oder verschieben Sie es in eine Sektion, die Besucher nicht lesen können, wenn die Zeit vorbei ist |

Quelle: [Dateien und Downloads, Protected downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#protected-downloads).

> **Warnung**
> „Do not link files directly from `var/.../storage/`: that bypasses the permission check“ (Verlinken Sie Dateien nicht
> direkt aus `var/.../storage/`, das umgeht die Berechtigungsprüfung, dieselbe Seite). Verlinken Sie immer über
> `content/download`, wie es das eigene Template des Dateifelds tut.

Leserkonten und Bereiche nur für Mitglieder erklären [Mitglieder](../mitglieder/README.md) und
[Mitgliederregistrierung und Bereiche](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md).

## Die Lizenz jedes Objekts angeben

Die Erweiterung `xrowmetadata`, die `composer.json` verlangt, lässt einen Redakteur angeben, unter welcher Lizenz die
Inhalte einer Seite veröffentlicht sind. Ihre Anleitung (`extension/xrowmetadata/doc/licenses.md`) beschreibt:

- einen Katalog von Lizenzen, einschließlich „every Creative Commons license ever published“ (jeder je veröffentlichten
  Creative-Commons-Lizenz), Werkzeugen für Gemeinfreiheit und unfreien Bedingungen wie „all rights reserved“;
- drei Ebenen: pro Objekt, als Standard pro Klasse und als Standard der Installation; die erste, die zutrifft, gilt;
- die Ausgabe im Seitenkopf als `rel="license"`, als schema.org `license` in JSON-LD und als Dublin Core
  `dcterms.license`, das „is read by library and repository harvesters“ (von Harvestern für Bibliotheken und
  Repositorien gelesen wird);
- Kennzeichnungen wie die Public Domain Mark werden als Etiketten behandelt, nicht als Lizenzen.

> **Hinweis**
> Der Standard der Installation in `xrowmetadata.ini [LicenseSettings] DefaultLicense` wird als `GPL-2.0-or-later`
> ausgeliefert, die Lizenz der Software. Setzen Sie für eine Sammlung Ihren eigenen Standard in
> `settings/override/xrowmetadata.ini.append.php` und einen Standard pro Eintragsklasse, wo er abweicht (dieselbe
> Anleitung). Welche Lizenz für ein Objekt gilt, entscheidet der Rechteinhaber; diese Seite ist keine Rechtsberatung.

## Von Besuchern geschickte Dateien

Wenn Leser oder Spender Dateien über ein Formular schicken (einen Scan zur Bestimmung, ein dem Archiv angebotenes
Dokument), verwenden Sie den Datentyp `enhancedezbinaryfile` („Enhanced File“ im Klasseneditor), der für die
Informationssammlung gebaut ist. Er legt hochgeladene Dateien mit einer Begrenzung in einem Download-Ordner ab
(`[RemoveFiles] MaxFiles`, Standard 25, „0 keeps an unlimited number, which is not advisable for a site with anonymous
uploads“ – 0 behält eine unbegrenzte Zahl, was für eine Website mit anonymen Uploads nicht ratsam ist) und verschickt
gesammelte Informationen als mehrteilige Mail, wenn eine Datei angehängt ist
([enhancedezbinaryfile](../../../../eng-us/features/6.0/extensions/enhancedezbinaryfile.md)). Schützen Sie solche Formulare gegen
Spam ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).

## Große Dateien und Formate

- `upload_max_filesize` und `post_max_size` von PHP begrenzen, was Redakteure hochladen können; sie werden in der
  `php.ini` des Web-PHP gesetzt. Ein Dateifeld kann in der Klasse außerdem eine Höchstgröße haben.
- „Very large files (videos) are better hosted on a service built for them and embedded“: Sehr große Dateien (Videos)
  liegen besser bei einem Dienst, der dafür gebaut ist, und werden eingebettet.

Quelle: [Dateien und Downloads, Large files](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#large-files). Zu
Bildern und ihren Größen siehe [Bilder und Bildvarianten](../../../../eng-us/help/creators/website-creators/images-and-image-variations.md);
zu den funktionierenden Bildformaten [SVG und Bildformate](../../../../eng-us/help/creators/website-creators/svg-and-image-formats.md).
Planen Sie Speicherplatz und Backups mit Ihrem Hoster
([Speicher, CPU und Festplatte dimensionieren](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)).

## In Writer geschriebene Dokumente

Wenn Mitarbeiter Berichte in LibreOffice oder OpenOffice Writer schreiben, importiert die Erweiterung `ezodf` eine Datei
`.odt` als Inhaltsobjekt (Titel, Überschriften, Absätze, Listen, Tabellen und Bilder werden auf Klassenattribute
abgebildet) und exportiert Objekte zurück nach Writer. Für Konvertierungen braucht sie OpenOffice oder LibreOffice 2.4
oder neuer ([ezodf](../../../../eng-us/features/6.0/extensions/ezodf.md)).

## Den Speicher in Ordnung halten

Die **Inhaltsprüfungen** (Menü Setup) helfen einer Sammlung, in Ordnung zu bleiben
([Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)):

| Prüfung | Findet |
|---|---|
| Nicht verknüpfte Objekte | Veröffentlichte Bilder, Dateien und Medien unterhalb eines Knotens, die nichts verwendet |
| Ungültige Relationen | Veröffentlichte Inhalte, die auf gelöschte, im Papierkorb liegende, verborgene oder unveröffentlichte Objekte verweisen |
| Speicherdateien | Dateien im Speicher, die kein Inhalt verwendet, und Probleme im Dateisystem bei Dateien, die Inhalte verwenden |
| Fehlende Dateien | Dateien, deren gespeicherte Datei fehlt, in veröffentlichten Versionen und Entwürfen |

Verwaiste Speicherdateien können in eine Quarantäne verschoben und wiederhergestellt werden; „nothing is ever deleted
from the storage itself“ (aus dem Speicher selbst wird nie etwas gelöscht, dieselbe Seite). Führen Sie diese Prüfungen
bei einer digitalen Sammlung nach Migrationen und vor dem Archivieren eines Backups aus. Linkprüfungen für externe
Links beschreibt [Link- und Inhaltsprüfungen](../../../../eng-us/help/creators/website-creators/link-and-content-checks.md).

## Checkliste für Sammlungen

- [ ] Jede Eintragsklasse, die eine digitalisierte Kopie trägt, hat ein Dateifeld.
- [ ] Material für alle, für Mitglieder und für Mitarbeiter durch Sektionen getrennt; Downloads abgemeldet getestet.
- [ ] Download-Links zeigen Format und Größe.
- [ ] Eine Standardlizenz für die Installation und für jede Eintragsklasse gesetzt; Lizenzen pro Objekt, wo sie
      abweichen.
- [ ] Upload-Grenzen passen zur größten Datei; sehr große Medien anderswo gehostet und eingebettet.
- [ ] Upload-Formulare für Besucher verwenden `enhancedezbinaryfile` mit einem vernünftigen `MaxFiles` und Spamschutz.
- [ ] Inhaltsprüfungen nach jeder Migration ausgeführt; fehlenden Dateien nachgegangen.
- [ ] Speicher in Backups enthalten und eine Wiederherstellung getestet
      ([Backups und Wiederherstellungen](../../../../eng-us/help/creators/website-creators/backups-and-restores.md)).

## Verwandte Seiten

- [Bibliotheken und Kataloge](README.md)
- [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Die Medien-Bibliothek ordnen](../../../../eng-us/help/creators/website-creators/organizing-the-media-library.md)
- [Medien und Uploads (für Redakteure)](../../medien-und-uploads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
