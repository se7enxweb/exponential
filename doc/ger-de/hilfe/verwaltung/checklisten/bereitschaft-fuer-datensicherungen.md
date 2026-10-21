---
title: Bereitschaft für Datensicherungen
audience: government, admin, hosting
category: public-sector
order: 150
summary: Eine Checkliste, die beweist, dass sich eine Exponential-Website einer öffentlichen Stelle wiederherstellen lässt - Datenbank und Dateien gemeinsam gesichert, Schlüssel, die sich nicht neu erzeugen lassen, Speicherort und Aufbewahrung, Wiederherstellungstests, Wiederherstellungsziele und wer was tut.
keywords: Datensicherung, Backup, Wiederherstellung, Wiederherstellungstest, RPO, RTO, Datenbank-Dump, Speicher, Schlüssel, Audit-Schlüssel, Aufbewahrung, Verschlüsselung, Notfallwiederherstellung, öffentlicher Sektor, backup, restore, restore test, database dump, storage, keys, audit keys, retention, encryption, disaster recovery, public sector
type: help
---
# Bereitschaft für Datensicherungen

Diese Checkliste richtet sich an die Website-Administration und den Hoster einer öffentlichen Stelle. Eine
Datensicherung ist erst durch eine Wiederherstellung bewiesen. Die Angaben stammen aus
[Datensicherungen: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md),
[Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) und
[10. Nach der Installation](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung).

## Inhalt

1. [Was gesichert wird](#was-gesichert-wird)
2. [Wie](#wie)
3. [Wo und wie lange](#wo-und-wie-lange)
4. [Wiederherstellung](#wiederherstellung)
5. [Vereinbarungen](#vereinbarungen)

## Was gesichert wird

- [ ] Die Datenbank, mit dem Werkzeug ihrer Datenbank ([Datenbanksicherungen je Datenbank](../../hosting/betrieb/datenbank-backups-je-engine.md)).
- [ ] `var/storage/` und jedes `var/<site>/storage/`: hochgeladene Dateien.
- [ ] `settings/override/` und `settings/siteaccess/`: Einstellungen und Zugangsdaten.
- [ ] Schlüssel, die sich nicht neu erzeugen lassen: `settings/override/audit.ini.append.php` (Signaturschlüssel des Audits), `settings/override/mailpreferences.ini.append.php` (das Geheimnis der E-Mail-Einstellungen), die TLS-Schlüssel von Velocity, falls verwendet ([Schlüssel und Geheimnisse](../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)).
- [ ] Das Audit-Verzeichnis `var/<site>/log/audit/`.
- [ ] Ihre eigenen Erweiterungen und Designs, sofern sie nicht in einem Repository liegen, das Sie kontrollieren; `composer.json` und `composer.lock`.
- [ ] Caches, `var/autoload/` und `vendor/` ausgelassen: Sie werden neu aufgebaut.

## Wie

- [ ] Datenbank und Dateien im selben Moment gesichert, mit Wartungsmodus oder mit der akzeptierten kleinen Lücke ([Datenbank und Dateien gemeinsam sichern](../../hosting/betrieb/backups-was-gesichert-werden-muss.md#datenbank-und-dateien-gemeinsam-sichern)).
- [ ] Eine SQLite-Datenbank wird mit der Online-Sicherung von SQLite kopiert, nie durch Kopieren einer Datei in Benutzung.
- [ ] Automatisiert, mit einem Fehleralarm an eine Person.

## Wo und wie lange

- [ ] Außerhalb des Servers gespeichert, an einem zweiten Ort.
- [ ] Verschlüsselt; nur für benannte Personen lesbar (die Datensicherung enthält Datenbankpasswörter).
- [ ] Die Aufbewahrung entspricht Ihren Regeln zu Schriftgut und Datenschutz ([Sicherungspläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)).
- [ ] Der Datenstandort der Datensicherungen ist im Hostingvertrag angegeben.

## Wiederherstellung

- [ ] Ein Wiederherstellungstest mindestens einmal im Jahr auf einem anderen Rechner ([Ein Wiederherstellungstest auf einem anderen Rechner](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#ein-wiederherstellungstest-auf-einer-anderen-maschine)).
- [ ] Nach der Wiederherstellung: Anmeldung, eine Seite mit Bildern, ein Formular, die Audit-Kette (`./console exp:audit verify`).
- [ ] Die benötigte Zeit festgehalten: Das ist Ihre tatsächliche Wiederherstellungszeit.
- [ ] Dem Runbook [Aus einer Datensicherung wiederherstellen](../../hosting/runbooks/aus-einem-backup-wiederherstellen.md) gefolgt und es verbessert.

## Vereinbarungen

- [ ] RPO (wie viele Daten verloren gehen dürfen) und RTO (wie lange eine Wiederherstellung dauern darf) mit Ihren eigenen Zahlen vereinbart ([SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md#wiederherstellungspunkt-und-wiederherstellungszeit)).
- [ ] Wer eine Wiederherstellung startet und wer darüber entscheidet.
- [ ] Sicherungskopien am Ende des Hostingvertrags zurückgegeben oder gelöscht ([Bereitschaft für den Ausstieg](bereitschaft-fuer-den-ausstieg.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Datensicherungen und Wiederherstellungen (für Websiteersteller)](../../../../eng-us/help/creators/website-creators/backups-and-restores.md)
- [Bereitschaft für Vorfälle](bereitschaft-fuer-vorfaelle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
