---
title: Anforderungen an Ausstieg und Portabilität
audience: government, project-manager, admin, partner
category: public-sector
order: 140
summary: Formulierungen, die einer öffentlichen Stelle die Freiheit erhalten, einen Lieferanten oder das Produkt zu verlassen - vollständige Exporte in offenen Formaten, Dateien, Konfiguration, Quellcode, Dokumentation, Umleitungen und ein getesteter Ausstieg - mit den Exportwerkzeugen, die Exponential dokumentiert.
keywords: Ausstieg, Portabilität, Lock-in, Datenexport, Datenrückgabe, offene Formate, CSV, JSON, XML, Datenbank-Dump, Pakete, Umleitungen, Übergang, Übergabe, Ausschreibung, Anforderungen, exit, portability, data export, data return, open formats, database dump, packages, redirects, transition, handover, tender, requirements
type: help
---
# Anforderungen an Ausstieg und Portabilität

Diese Seite richtet sich an Beschaffungsteams, die sicher sein wollen, dass die Organisation aussteigen kann: den
Lieferanten wechseln, das Hosting wechseln oder zu einem anderen Produkt wechseln, ohne Inhalte zu verlieren oder für
die eigenen Daten Lösegeld zu zahlen. Sie gibt Formulierungen für Anforderungen und die Exportwerkzeuge, die Exponential
in [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md) dokumentiert.

## Inhalt
1. [Zwei Arten des Ausstiegs](#zwei-arten-des-ausstiegs)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Was Exponential exportieren kann](#was-exponential-exportieren-kann)
4. [Den Ausstieg testen, bevor Sie ihn brauchen](#den-ausstieg-testen-bevor-sie-ihn-brauchen)
5. [Fallstricke](#fallstricke)

## Zwei Arten des Ausstiegs

| Ausstieg | Was umzieht | Was Sie brauchen |
|---|---|---|
| **Lieferant wechseln, Exponential behalten** | die ganze Installation: Code, Datenbank, Dateien, Einstellungen | Quellcode, einen Datenbank-Dump, das Speicherverzeichnis, `settings/override/`, Dokumentation |
| **Exponential verlassen** | Inhalte, Dateien, Benutzer (ohne Passwörter), Adressen | Exporte der Inhalte in offenen Formaten, eine Umleitungstabelle, Rich Text als HTML |

Beides ist leichter, wenn es zu Beginn in den Vertrag geschrieben wird, statt am Ende verhandelt zu werden.

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| EXI-01 | Auf Anforderung und am Vertragsende liefert der Lieferant innerhalb von <Zeit>: einen vollständigen Datenbank-Dump, das Speicherverzeichnis mit allen hochgeladenen Dateien, die vollständige Konfiguration, den Quellcode allen Codes und die Dokumentation. |
| EXI-02 | Alle Inhalte lassen sich vom Kunden ohne den Lieferanten in offenen, dokumentierten Formaten (zum Beispiel CSV, JSON, XML) exportieren, mit einem Manifest, das die Felder beschreibt. |
| EXI-03 | Der Export enthält für jedes Element seine Kennungen, seine Adresse, seinen Ort in der Struktur, seine Daten und jedes Feld, in jeder Sprache. |
| EXI-04 | Eine Liste aller Seitenadressen, einschließlich eigener Adressen und Platzhalter, wird geliefert, damit sich auf dem neuen System Umleitungen bauen lassen. |
| EXI-05 | Zugangsdaten, Schlüssel und Geheimnisse werden sicher übergeben und dann erneuert; der Zugang des Lieferanten wird entfernt und schriftlich bestätigt. |
| EXI-06 | Der Lieferant arbeitet <Zeitraum> lang zu den Sätzen in <Anlage> mit dem Nachfolger zusammen. |
| EXI-07 | Innerhalb von <Zeitraum> nach dem Livegang wird ein Ausstiegstest durchgeführt: Ein Export wird erstellt und mit den Zahlen in der Administration abgeglichen. |
| EXI-08 | Nach der Übergabe löscht der Lieferant die Daten des Kunden innerhalb von <Zeit> aus seinen Systemen und Sicherungen und bestätigt das. |

## Was Exponential exportieren kann

Aus [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md):

| Daten | Werkzeug | Format |
|---|---|---|
| Alle Inhalte einer Website | `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>` | CSV je Klasse, `manifest.json`, `README.txt` |
| Eine Klasse | `./console ext:xrowextract:csv --class=<identifier> ...` | CSV, XML oder JSON mit einem typisierten Manifest |
| Klassen und Inhalte für ein anderes Exponential | Pakete | `.ezpkg` ([Pakete](../../../../anleitungen/pakete.md)) |
| Layouts | `./console exp:layouts export --all --output=<file.zip>` | ZIP |
| Dateien | `var/<site>/storage/` | die Originaldateien |
| Die ganze Datenbank | `./console exp:ezsqldumpschema --output-types=all ...` oder das eigene Werkzeug der Datenbank | neutrales Format oder SQL |
| Formulareingaben | `./console ext:bccie:export --object=<id> --format=csv` | CSV |
| Nachweise der E-Mail-Einwilligung | `./console exp:mailconsent` | CSV |
| Inhalte über HTTP | REST v1/v2 | JSON |

Zwei Fakten, für die Sie planen sollten: Das Website-Archiv **enthält nie Passwort-Hashes**, Benutzer setzen also auf dem
Zielsystem neue Passwörter, es sei denn, es kann Standardwerte von PHP `password_hash()` prüfen; und Rich Text wird als
XML gespeichert, das für ein anderes Produkt gerendert oder in HTML umgewandelt werden muss.

## Den Ausstieg testen, bevor Sie ihn brauchen

Der Schulungskurs [Migration zu Exponential](../../../trainer/migration/README.md) hat eine Lektion, die den
vollständigen Export auf einer Schulungsinstallation durchführt und prüft
([Lektion 8.1: Von Exponential migrieren](../../../trainer/migration/modul-08-wechsel-weg-und-abschlussprojekt/von-exponential-wechseln.md)).
Verwenden Sie dieselben Schritte für EXI-07: Erstellen Sie das Archiv, vergleichen Sie die Zeilen je Klasse mit
**Setup > Klassen** und bestätigen Sie, dass kein Passwort-Hash darin ist.

## Fallstricke

- „Wir exportieren Ihre Daten“ ohne Formate, Zeitpunkt oder Preis akzeptieren.
- `settings/override/` und die Signaturschlüssel des Audits vergessen; ohne sie lassen sich die Audit-Einträge nicht mehr
  verifizieren ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
- Die alte Website abschalten, bevor die neue abgenommen ist; halten Sie sie bis dahin schreibgeschützt.

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Ausschreibungsvorlage: Klausel zu Ausstieg und Datenrückgabe](../ausschreibungsvorlagen/klausel-ausstieg-und-datenrueckgabe.md)
- [Ausschreibungsvorlage: Zusicherung ohne Lock-in](../ausschreibungsvorlagen/klausel-zusicherung-ohne-lock-in.md)
- [Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
