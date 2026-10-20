---
title: Checkliste für die Übergabe
audience: customer, partner, hosting
category: customer-success
order: 9
summary: Was der Betreiber einer Exponential-Website erhalten muss, wenn ein Partner oder Hoster die Website übergibt - am Ende eines Projekts, am Ende eines Vertrags oder an einen neuen Anbieter - Zugänge, Code, Daten, Einstellungen, Unterlagen und Wissen, mit Prüfungen zur Bestätigung jedes Punkts.
keywords: Übergabe, Checkliste, Ausscheiden, Export, Zugänge, Zugangsdaten, Code, Datenbank, Dokumentation, neuer Anbieter, Ausstieg, handover, checklist, offboarding, export, access, credentials, code, database, documentation, new provider, exit
type: help
---
# Checkliste für die Übergabe

Eine Übergabe findet statt, wenn ein Projekt endet und die Website in Ihre Obhut übergeht, wenn ein Vertrag endet oder
wenn ein Anbieter einen anderen ersetzt. Diese Checkliste nennt, was Sie aus Sicht des Betreibers erhalten müssen.
Hoster arbeiten ihre eigene Liste ab ([Ausscheiden eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)) und
Partner die Übergabephase ihres Leitfadens ([Leitfaden für Implementierungsprojekte](../../partner/leitfaden-fuer-implementierungsprojekte.md)).

## Zuerst den Plan vereinbaren

- [ ] Das Übergabedatum, und wer bis dahin was tut.
- [ ] Ob der alte Anbieter die Website weiterbetreibt, bis der neue bestätigt hat, dass sie funktioniert.
- [ ] Wie vertrauliche Dinge (Einstellungen mit Passwörtern) übertragen werden: nie per einfacher E-Mail.

## Zugänge

- [ ] Administratorzugang für Ihre eigenen Leute, mit persönlichen Konten
      ([Administratorkonten und Zugriff](../gesund-halten/administratorkonten-und-zugriff.md)).
- [ ] Die Konten für Hosting und Panel, oder die Bestätigung, dass sie zum vereinbarten Datum geschlossen werden.
- [ ] Die Domain und das DNS, auf Ihren Namen, oder zum vereinbarten Datum übertragen.
- [ ] Die Liste der Konten, die der alte Anbieter in Ihrer Administration hatte, damit sie deaktiviert werden können;
      ein gut geführter Hoster wird „dem Kunden mitteilen, welche Konten die eigenen waren“ ([Ausscheiden eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).
- [ ] Das Notfallverfahren, schriftlich festgehalten ([Das Administratorpasswort ändern](../../../../en/us/help/change-the-admin-password.md)).

## Ein vollständiger Export

Eine vollständige Website besteht aus der Datenbank und den Dateien ([Ausscheiden eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)):

- [ ] Ein Datenbank-Dump, erstellt mit dem eigenen Werkzeug der Engine.
- [ ] `var/storage` (und der Speicher jeder Website, wo er getrennt ist).
- [ ] `settings/override` und `settings/siteaccess`, vertraulich übertragen; lassen Sie das Datenbankpasswort beim
      neuen Hoster ändern.
- [ ] Eigene Erweiterungen und Designs, oder die Adressen ihrer Git-Repositorys.
- [ ] Die Signaturschlüssel des Prüfprotokolls (`settings/override/audit.ini.append.php`) und, wenn Sie die Historie
      möchten, die Einträge des Prüfprotokolls ([Backups, die sich wiederherstellen lassen](../../../../en/us/help/businesses/reliability/backups-that-restore.md)).
- [ ] Erstellt bei eingeschaltetem Wartungsmodus, damit Datenbank und Dateien zusammenpassen.

## Unterlagen

- [ ] Die Versionen: Exponential, PHP, Datenbank-Engine und -Version, Engine, Erweiterungen.
- [ ] Ein Übergabedokument: Inhaltsmodell, Rollen, Einstellungen, Cronjobs, Backups, Überwachung, bekannte Probleme
      ([Projektvorlagen, Übergabedokument](../../../../en/us/help/leadership/project-templates.md)).
- [ ] Die Cron-Einträge und was sie ausführen.
- [ ] Bekannte Probleme und ihre Links beim Projekt, falls vorhanden.
- [ ] Offene Änderungsanfragen und ihr Stand ([Änderungsanfragen](aenderungsanfragen.md)).

## Wissen

- [ ] Ihre Redakteure wurden geschult und wissen, wo die Hilfe ist: das Dashboard der Hilfe in ihrer Administration.
- [ ] Ihr Team weiß, wie man ein Problem meldet und was hineingehört ([Probleme melden](../gesund-halten/probleme-melden.md)).
- [ ] Der neue Anbieter, falls es einen gibt, hatte einen Durchgang mit dem alten.

## Prüfungen nach der Übergabe

| Prüfung | Gutes Ergebnis |
|---|---|
| Den Export auf einem Testrechner wiederherstellen | Er läuft ([Wiederherstellungstest](../gesund-halten/wiederherstellungstest.md)) |
| **Setup > Systeminformationen** auf der neuen Website | Die erwartete Version; keine fehlgeschlagene Statusprüfung |
| **Setup > Upgrade-Prüfung** | Keine unerwartet geänderten Dateien ([Die Upgrade-Prüfung](../../../../en/us/guides/upgrade-check.md)) |
| Ihre Testliste | Jeder Punkt besteht ([Checkliste für den Upgrade-Tag](../gesund-halten/checkliste-upgrade-tag.md)) |

## Ganz am Ende

- [ ] Die Konten des alten Anbieters in Ihrer Administration sind deaktiviert.
- [ ] Der alte Anbieter löscht Ihre Daten gemäß Vertrag und Gesetz und bestätigt es schriftlich
      (keine Rechtsberatung; [Datenaufbewahrung und Löschung](../../../../en/us/help/businesses/security/data-retention-and-deletion.md)).
- [ ] [Wer was macht](wer-was-macht.md) und Ihre [Eskalationskontakte](vorlage-eskalationskontakte.md) sind aktualisiert.

> **Hinweis**
> Exponential steht unter der GPL. Ein Partner, der eine Website baut und Ihnen den Code übergibt, verbreitet ihn an Sie,
> und Sie erhalten die Rechte der GPL, einschließlich des Quellcodes ([Grundlagen zu Verträgen und SLAs](../../../../en/us/help/businesses/choosing/contracts-and-slas-basics.md)).
> Das Eigentum an Designs und Inhalten, die für Sie erstellt wurden, ist eine Vertragsfrage: Vereinbaren Sie es schriftlich.

## Verwandte Seiten

- [Eine Website übernehmen](eine-website-uebernehmen.md)
- [Einen Hoster auswählen](einen-hoster-auswaehlen.md)
- [Eine Website zwischen Hostern umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
