---
title: "Ausschreibungsvorlage: Klausel zu Ausstieg und Datenrückgabe"
audience: government, project-manager, partner, admin
category: public-sector
order: 160
summary: Klauseltext für das Ende eines Vertrags über ein CMS - Ausstiegsplan, vollständige Datenrückgabe in offenen Formaten, Übergabe von Quellcode und Dokumentation, Unterstützung beim Übergang, sichere Übergabe von Zugangsdaten und Löschung - mit den Exportwerkzeugen von Exponential, die jeden Schritt konkret machen.
keywords: Ausstiegsklausel, Datenrückgabe, Übergang, Übergabe, Export, offene Formate, Datenbank-Dump, Speicher, Umleitungen, Zugangsdaten, Löschung, Unterstützung bei Vertragsende, Ausschreibungsvorlage, exit clause, data return, transition, handover, open formats, database dump, storage, redirects, credentials, deletion, termination assistance, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zu Ausstieg und Datenrückgabe

Diese Seite richtet sich an Beschaffungsstellen, die wollen, dass das Ende eines Vertrags Routine ist. Die Klausel gilt
unabhängig davon, ob Sie mit derselben Software zu einem anderen Lieferanten wechseln oder das Produkt verlassen. Die
unten genannten Exportwerkzeuge sind in [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)
dokumentiert. Hintergrund: [Anforderungen an Ausstieg und Portabilität](../anforderungen/anforderungen-an-ausstieg-und-portabilitaet.md).

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Das Rückgabepaket](#das-rückgabepaket)
3. [Die Rückgabe prüfen](#die-rückgabe-prüfen)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
E. EXIT AND DATA RETURN

E.1 Exit plan. Within <period> of the start of the contract the Supplier delivers
    an exit plan describing the return package, the steps, the time needed and
    the roles. The plan is updated <yearly>.

E.2 Trigger. The exit starts on expiry, on termination for any reason, or on the
    Customer's request for a return package during the contract.

E.3 Return package. Within <time> of the trigger the Supplier delivers the return
    package defined in <annex>, by <secure transfer method>.

E.4 Formats. Content exports are in open, documented formats with a manifest.
    Database dumps are in the database's standard dump format. Files are in
    their original form.

E.5 Transition support. The Supplier answers the Customer's and the successor's
    questions and attends <number> transition meetings during <period>, at
    <rates / no charge>.

E.6 Continuity. The Supplier keeps the service running at the agreed service
    levels until the Customer confirms the transition, up to <period>.

E.7 Access and secrets. The Supplier hands over all credentials and keys
    securely, after which the Customer rotates them; the Supplier removes its
    accounts and confirms in writing.

E.8 Deletion. After the Customer confirms receipt and completeness, the Supplier
    deletes the Customer's data from all systems and backups within <time> and
    confirms in writing.

E.9 Costs. The return package and E.7 are included in the contract price.
```

## Das Rückgabepaket

| Element | Werkzeug oder Ort (Exponential) |
|---|---|
| Vollständiger Datenbank-Dump | das eigene Werkzeug der Datenbank oder `./console exp:ezsqldumpschema --output-types=all ...` ([Datenbanksicherungen je Engine](../../../hosting/betrieb/datenbank-backups-je-engine.md)) |
| Alle hochgeladenen Dateien | `var/storage/` und `var/<site>/storage/` |
| Konfiguration | `settings/override/`, `settings/siteaccess/`, `config.php` und zugehörige Dateien; die Signaturschlüssel des Audits in `settings/override/audit.ini.append.php` |
| Quellcode | das Repository mit vollständiger Historie: Erweiterungen, Designs, `composer.json`, `composer.lock` |
| Export der Inhalte | Website-Archiv von xrowextract: `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>` (CSV je Klasse, `manifest.json`) |
| Layouts | `./console exp:layouts export --all --output=<file.zip>` |
| Formulareingaben | `./console ext:bccie:export --object=<id> --format=csv` |
| Einwilligungsnachweise | `./console exp:mailconsent` (CSV) |
| Audit-Protokoll | `var/<site>/log/audit/` mit signierten Manifesten; `./console exp:audit export` |
| Adressliste für Umleitungen | Spalte mit dem URL-Alias im Website-Archiv, dazu die Tabellen `ezurlalias_ml` und `ezurlwildcard` |
| Dokumentation | gemäß der Klausel zur Dokumentation |

Zwei Fakten für den Plan: Das Website-Archiv enthält nie Passwort-Hashes, Benutzer eines neuen Produkts setzen also neue
Passwörter; Rich Text wird als XML gespeichert und muss für ein anderes Produkt gerendert oder in HTML umgewandelt werden.

## Die Rückgabe prüfen

1. Stellen Sie Datenbank und Dateien in einer separaten Umgebung wieder her und melden Sie sich in der Administration an.
2. Führen Sie `./console exp:audit verify` aus; es endet mit 1, wenn eine Kette gebrochen ist.
3. Vergleichen Sie die Zeilen je Klasse im Website-Archiv mit **Setup > Klassen**.
4. Öffnen Sie eine Stichprobe von Dateien und prüfen Sie, ob Bilder angezeigt werden.
5. Halten Sie die alte Website schreibgeschützt, bis die neue abgenommen ist.

## Hinweise für die bewertende Person

- E.1 bringt Bieter dazu, vor der Unterzeichnung über den Ausstieg nachzudenken; bewerten Sie die Qualität des Plans.
- E.9 verhindert einen Preis für Ihre eigenen Daten in dem Moment, in dem Sie am wenigsten Verhandlungsmacht haben.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zur Zusicherung ohne Lock-in](klausel-zusicherung-ohne-lock-in.md)
- [Klausel zu Hosting und Datenstandort](klausel-hosting-und-datenstandort.md)
- [Kunden-Offboarding (für Hoster)](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
