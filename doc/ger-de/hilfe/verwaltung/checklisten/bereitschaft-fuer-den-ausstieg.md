---
title: Bereitschaft für den Ausstieg
audience: government, project-manager, admin, hosting
category: public-sector
order: 230
summary: Eine Checkliste, die eine öffentliche Stelle jederzeit bereit hält, ihren Dienstleister, ihren Hoster oder Exponential selbst zu verlassen - Code und Zugänge, die Sie kontrollieren, dokumentierte und getestete Exporte, Schlüssel und Datensicherungen in Ihren Händen, Vertragsbedingungen und ein Übergangsplan.
keywords: Ausstieg, Bereitschaft für den Ausstieg, Abhängigkeit, Export, xrowextract, Datenbank-Dump, Übergabe, Schlüssel, Repository, Übergang, Wechsel des Dienstleisters, öffentlicher Sektor, exit, exit readiness, lock-in, export, database dump, handover, keys, repository, transition, supplier change, public sector
type: help
---
# Bereitschaft für den Ausstieg

Diese Checkliste richtet sich an das Vertragsmanagement und die Website-Administration einer öffentlichen Stelle.
Aussteigen zu können ist es, was eine Beziehung zu einem Dienstleister fair hält. Die Lizenz und die Exporte von
Exponential machen den Ausstieg möglich; diese Checkliste hält ihn praktikabel. Die Exportwerkzeuge stammen aus
[Von Exponential weg migrieren](../../../installation/migration/exponential-verlassen.md).

![Datensicherungen: eine vollständige Sicherung der Website oder Datenbank und Dateien, die Grundlage eines Ausstiegsplans](../../../../eng-us/help/images/government/gov-backups.webp)

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie Ihren
> Vertrag mit Ihrer Rechtsberatung.

## Inhalt

1. [Was Sie kontrollieren](#was-sie-kontrollieren)
2. [Getestete Exporte](#getestete-exporte)
3. [Schlüssel und Datensicherungen](#schlüssel-und-datensicherungen)
4. [Vertrag](#vertrag)
5. [Plan](#plan)

## Was Sie kontrollieren

- [ ] Die Software steht unter der GPL; Sie dürfen sie ohne Erlaubnis an einen anderen Dienstleister geben ([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md)).
- [ ] Ihre eigenen Erweiterungen, Designs und Einstellungen liegen in einem Repository, das Ihrer Organisation gehört ([Mit Git entwickeln](../../mit-git-entwickeln.md)).
- [ ] Benannte Mitarbeitende Ihrer Organisation haben Administratorkonten auf der Website und Zugang zum Hosting.
- [ ] Die Dokumentation von Inhaltsmodell, Rollen, Einstellungen und Bereitstellung liegt in Ihren Händen.
- [ ] Keine Kernel-Dateien direkt geändert; **Setup > Upgrade-Prüfung** bestätigt das ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).

## Getestete Exporte

Führen Sie mindestens einmal im Jahr einen Exporttest auf einer Kopie durch:

- [ ] Archiv der Website mit jeder Klasse: `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>`; Manifest geöffnet, Zeilenanzahlen mit den Anzahlen in der Administration verglichen ([Von Exponential weg migrieren](../../../installation/migration/exponential-verlassen.md#2-der-vollständige-export-schritt-für-schritt)).
- [ ] Das Speicherverzeichnis damit zusammen kopiert.
- [ ] Ein Datenbank-Dump mit dem Werkzeug der Datenbank ([Datenbanksicherungen je Datenbank](../../hosting/betrieb/datenbank-backups-je-engine.md)).
- [ ] Layouts, falls verwendet: `./console exp:layouts export --all --output=<file.zip>`.
- [ ] Formulareinsendungen, Einwilligungsnachweise und das Audit-Protokoll exportiert, wo nötig.
- [ ] Weiterleitungsliste aus URL-Aliassen und Platzhaltern ([Abschnitt 4](../../../installation/migration/exponential-verlassen.md#4-adressen-für-die-weiterleitungen)).
- [ ] Hinweis: Das Archiv der Website enthält nie Passwort-Hashes; Benutzer setzen auf einem neuen System neue Passwörter, es sei denn, es kann Standard-PHP-Hashes prüfen ([Abschnitt 5](../../../installation/migration/exponential-verlassen.md#5-benutzer-und-personenbezogene-daten)).

## Schlüssel und Datensicherungen

- [ ] Datensicherungen gelangen an einen Ort, den Ihre Organisation kontrolliert, nicht nur an den des Hosters ([Bereitschaft für Datensicherungen](bereitschaft-fuer-datensicherungen.md)).
- [ ] Schlüssel, die sich nicht neu erzeugen lassen, sind in Ihren Händen: Signaturschlüssel des Audits, das Geheimnis der E-Mail-Einstellungen, TLS-Schlüssel ([Schlüssel und Geheimnisse](../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)).
- [ ] Domainnamen und DNS sind auf Ihre Organisation registriert.

## Vertrag

- [ ] Ausstiegsklauseln: Übergabezeitraum, Zusammenarbeit mit einem Nachfolger, Rückgabe und Löschung der Daten ([Ausstiegspläne](../beschaffung/ausstiegsplaene/README.md)).
- [ ] Kündigungsfristen und Enddaten bekannt ([Das Ende eines Vertrags](../beschaffung/lebenszyklus/ende-eines-vertrags.md)).
- [ ] Keine Klausel, die das Eigentum an dem Code beansprucht, den Sie bezahlt haben, oder verbietet, ihn an einen anderen Dienstleister zu geben.

## Plan

- [ ] Ein schriftlicher Ausstiegsplan, jedes Jahr aktualisiert ([Jährliche Überprüfung](jaehrliche-ueberpruefung.md)).
- [ ] Nur für einen Wechsel des Hosters: [Eine Website zwischen Hosts umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md).
- [ ] Für einen Wechsel des Systems: den Importweg des Zielsystems gewählt ([Von Exponential weg migrieren](../../../installation/migration/exponential-verlassen.md)).
- [ ] Die alte Website schreibgeschützt behalten, bis die neue abgenommen ist.
- [ ] Zugänge von Auftragnehmern am Ende geschlossen ([Einen Auftragnehmer verabschieden](einen-auftragnehmer-verabschieden.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Vor der Unterzeichnung eines Vertrags](vor-der-unterzeichnung.md)
- [Exponential verlassen (Lektion für Trainer)](../../trainer/migration/modul-08-wechsel-weg-und-abschlussprojekt/von-exponential-wechseln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
