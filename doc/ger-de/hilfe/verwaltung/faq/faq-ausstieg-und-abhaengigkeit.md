---
title: "FAQ: Ausstieg und Abhängigkeit"
audience: government, project-manager, admin
category: public-sector
order: 200
summary: Antworten auf die Fragen öffentlicher Stellen zur Abhängigkeit von Exponential - können wir den Dienstleister wechseln, können wir das Produkt verlassen, was lässt sich in welchen Formaten exportieren, was geschieht mit Benutzern und Adressen, und woher Abhängigkeit wirklich kommt.
keywords: FAQ Abhängigkeit, Ausstieg, Anbieterabhängigkeit, Vendor-Lock-in, Dienstleister wechseln, Export, Datenportabilität, xrowextract, CSV, JSON, Datenbank-Dump, Weiterleitungen, Passwörter, öffentlicher Sektor, lock-in FAQ, exit, vendor lock-in, change supplier, export, data portability, database dump, redirects, passwords, public sector
type: help
---
# FAQ: Ausstieg und Abhängigkeit

Diese Seite beantwortet die Fragen öffentlicher Stellen zur Abhängigkeit von Exponential. Die Angaben zum Export stammen
aus [Von Exponential wegmigrieren](../../../installation/migration/exponential-verlassen.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie Ihren
> Vertrag mit Ihrer Rechtsberatung.

## Inhalt

1. [Können wir den Dienstleister wechseln und Exponential behalten?](#können-wir-den-dienstleister-wechseln-und-exponential-behalten)
2. [Können wir Exponential ganz verlassen?](#können-wir-exponential-ganz-verlassen)
3. [Was lässt sich exportieren, in welchen Formaten?](#was-lässt-sich-exportieren-in-welchen-formaten)
4. [Was geschieht mit den Passwörtern der Benutzer?](#was-geschieht-mit-den-passwörtern-der-benutzer)
5. [Funktionieren alte Adressen weiter?](#funktionieren-alte-adressen-weiter)
6. [Woher kommt Abhängigkeit wirklich?](#woher-kommt-abhängigkeit-wirklich)

## Können wir den Dienstleister wechseln und Exponential behalten?

Ja. Die Software steht unter der GPL; ein neuer Dienstleister kann denselben Code ohne Erlaubnis von irgendjemandem
übernehmen ([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md)). Was eine Übergabe braucht, steht in
[Das Ende eines Vertrags](../beschaffung/lebenszyklus/ende-eines-vertrags.md).

## Können wir Exponential ganz verlassen?

Ja. „Your content is yours“ (Ihre Inhalte gehören Ihnen): Sie liegen in einem offenen Datenbankschema und in gewöhnlichen
Dateien, mit Werkzeugen, die sie in einfache Formate schreiben
([Von Exponential wegmigrieren](../../../installation/migration/exponential-verlassen.md)). Für mehrere Zielsysteme gibt es
Anleitungen.

## Was lässt sich exportieren, in welchen Formaten?

| Daten | Format |
|---|---|
| Alle Inhalte, pro Klasse, selbstbeschreibend | CSV (oder XML, JSON) mit `manifest.json`, in einem ZIP- oder TAR-Archiv |
| Klassen und Inhalte für ein anderes Exponential | `.ezpkg`-Pakete |
| Layouts | ZIP-Übertragungsdatei |
| Bilder und Dateien | die Originaldateien aus dem Speicherverzeichnis |
| Die ganze Datenbank | neutrales Schemaformat oder SQL |
| Formulareinsendungen, Einwilligungsnachweise | CSV |
| Inhalte über HTTP | JSON über REST |

Quelle: [Von Exponential wegmigrieren, Abschnitt 1](../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten).

## Was geschieht mit den Passwörtern der Benutzer?

Das Archiv der Website enthält nie Passwort-Hashes; die Benutzer setzen auf dem Zielsystem neue Passwörter, es sei denn,
das Zielsystem kann Standardwerte von PHP `password_hash()` prüfen, die ein Datenbankexport liefern kann. Entscheiden Sie
das mit Ihrer Datenschutzberatung
([Abschnitt 5](../../../installation/migration/exponential-verlassen.md#5-benutzer-und-personenbezogene-daten)).

## Funktionieren alte Adressen weiter?

Wenn Sie aus den URL-Aliassen und Platzhaltern eine Weiterleitungstabelle erstellen und sie vor dem Umschalten auf dem
neuen System einrichten ([Abschnitt 4](../../../installation/migration/exponential-verlassen.md#4-adressen-für-die-weiterleitungen)).

## Woher kommt Abhängigkeit wirklich?

Nicht von der Lizenz. Sie kommt von Verträgen, die Ihnen Code oder Daten vorenthalten, von eigens entwickeltem Code,
den sonst niemand kennt, von Dokumentation, die beim Dienstleister bleibt, von Zugangsdaten und Schlüsseln, die Sie
nicht besitzen, und von Kompetenzen, die Sie nicht aufgebaut haben. Die Checkliste
[Bereitschaft für den Ausstieg](../checklisten/bereitschaft-fuer-den-ausstieg.md) behandelt jeden dieser Punkte.

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Bereitschaft für den Ausstieg](../checklisten/bereitschaft-fuer-den-ausstieg.md)
- [Exponential verlassen (Lektion für Trainer)](../../trainer/migration/modul-08-wechsel-weg-und-abschlussprojekt/von-exponential-wechseln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
