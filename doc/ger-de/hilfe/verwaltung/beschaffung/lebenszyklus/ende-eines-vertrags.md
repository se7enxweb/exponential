---
title: Das Ende eines Vertrags
audience: government, project-manager, admin, partner
category: public-sector
order: 90
summary: Was eine öffentliche Stelle tut, wenn ein Vertrag für eine Exponential-Website endet - verlängern, mit derselben Software neu ausschreiben oder sie verlassen - und die Übergabe von Code, Daten, Schlüsseln, Dokumentation und Wissen, die jede Wahl braucht.
keywords: Vertragsende, Verlängerung, Neuausschreibung, Übergabe, Übergang, Ausstieg, Schlüssel, Dokumentation, Wissenstransfer, Offboarding des Lieferanten, öffentlicher Sektor, end of contract, renewal, re-tender, handover, transition, exit, keys, documentation, knowledge transfer, offboarding supplier, public sector
type: help
---
# Das Ende eines Vertrags

Diese Seite richtet sich an das Vertragsmanagement einer öffentlichen Stelle, deren Vertrag für eine Exponential-Website,
ihr Hosting oder ihren Support endet. Sie stellt die drei Möglichkeiten und die Übergabe dar, die jede braucht. Die
ausführliche Planung des Ausstiegs steht im Teil zu Ausstiegsplänen dieses Abschnitts zur Beschaffung; diese Seite ist
die Sicht des Lebenszyklus.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Drei Möglichkeiten](#drei-möglichkeiten)
2. [Früh beginnen](#früh-beginnen)
3. [Was in jedem Fall zu übergeben ist](#was-in-jedem-fall-zu-übergeben-ist)
4. [Mit derselben Software neu ausschreiben](#mit-derselben-software-neu-ausschreiben)
5. [Exponential verlassen](#exponential-verlassen)
6. [Den Zugang des bisherigen Lieferanten schließen](#den-zugang-des-bisherigen-lieferanten-schließen)

## Drei Möglichkeiten

| Möglichkeit | Was sich ändert | Aufwand |
|---|---|---|
| Mit demselben Lieferanten verlängern | nur die Bedingungen | am geringsten |
| Neu ausschreiben, Exponential behalten | der Lieferant, vielleicht der Host | mittel: eine Übergabe |
| Exponential verlassen | das System | am höchsten: eine Migration ([Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)) |

Weil Exponential freie Software unter der GPL ist, braucht die zweite Möglichkeit von niemandem eine Erlaubnis: Ein
neuer Lieferant kann denselben Code übernehmen ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)).

## Früh beginnen

Ein Übergang braucht Zeit: eine Ausschreibung, eine Übergabezeit, vielleicht einen Hosting-Umzug. Beginnen Sie die
Entscheidung lange vor dem Enddatum und prüfen Sie die Kündigungsfristen in Ihrem Vertrag. In den letzten Monaten muss
der ausscheidende Lieferant weiterhin Sicherheitsupdates einspielen ([Sicherheitsupdates und Patch-Rhythmus](sicherheitsupdates-und-patch-rhythmus.md)).

## Was in jedem Fall zu übergeben ist

| Element | Warum | Lesen |
|---|---|---|
| Der Code: Ihre eigenen Erweiterungen, Designs und Einstellungen, in einem Repository, das Sie kontrollieren | der neue Lieferant muss die Website neu aufbauen können | [Mit Git entwickeln](../../../mit-git-entwickeln.md) |
| Eine vollständige Sicherung: Datenbank und Dateien zusammen erstellt | die Daten gehören Ihnen | [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |
| Schlüssel, die sich nicht neu erzeugen lassen: Signaturschlüssel des Audits, das Geheimnis der E-Mail-Einstellungen, TLS-Schlüssel | ihr Verlust beschädigt die Verifikation des Audits und Abmeldelinks | [Sicherungen: Schlüssel und Geheimnisse](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen) |
| Administratorzugang, übertragen an benannte Personen Ihrer Organisation | damit niemand ausgesperrt ist | [Das Admin-Passwort ändern](../../../administratorpasswort-aendern.md) |
| Dokumentation: Inhaltsmodell, Rollen, Einstellungen, Betriebshandbücher, Deployment | Wissen geht mit den Menschen | [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md) |
| Die Liste der verwendeten Versionen | der Ausgangspunkt des nächsten Lieferanten | Setup > Systeminformationen ([Systeminformationen](../../../../anleitungen/systeminformationen.md)) |
| Offene Punkte und bekannte Risiken | beim Wechsel soll nichts verloren gehen | Ihr Ticketsystem |

Bewahren Sie Zugangsdaten und Schlüssel mit derselben Sorgfalt auf wie den Server: Die Einstellungen in
`settings/override/` enthalten Datenbankpasswörter ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md#die-zu-sichernden-dateien)).

## Mit derselben Software neu ausschreiben

- Geben Sie den Bietern die Dokumentation und eine anonymisierte Beschreibung der Website; nie echte personenbezogene
  Daten.
- Planen Sie eine Überschneidung, in der beide Lieferanten Zugang haben.
- Wechselt auch der Host, folgen Sie [Eine Website zwischen Hostern umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md).
- Prüfen Sie nach dem Umzug: **Setup > Upgrade-Prüfung** auf geänderte Dateien, die Zustandsprüfungen der
  Systeminformationen, einen Test jedes Formulars und der Anmeldung.

## Exponential verlassen

Die Exporte sind dokumentiert: ein Website-Archiv von xrowextract je Klasse mit Manifest, Pakete, Layouts, das
Speicherverzeichnis, ein Datenbank-Dump, Formulareingaben, Einwilligungsnachweise und das Audit-Protokoll
([Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten)). Halten Sie die alte
Website schreibgeschützt, bis die neue abgenommen ist, und bauen Sie aus den URL-Aliasen eine Umleitungstabelle, damit
alte Adressen weiter funktionieren ([Abschnitt 4](../../../../installation/migration/exponential-verlassen.md#4-adressen-für-die-weiterleitungen)).

## Den Zugang des bisherigen Lieferanten schließen

- Entfernen oder deaktivieren Sie seine Konten; erwägen Sie **Deaktivieren und verschieben**, wo es aktiviert ist
  ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)).
- Widerrufen Sie seine persönlichen API-Schlüssel ([Persönliche API-Schlüssel](../../../../anleitungen/api-schluessel.md)) und
  beenden Sie seine Sitzungen (**Setup > Sitzungen**, [Sitzungen](../../../../anleitungen/sitzungen.md)).
- Ändern Sie gemeinsam genutzte Passwörter und Serverzugangsdaten; erneuern Sie Schlüssel, die er kannte.
- Prüfen Sie im Audit-Protokoll seine letzten Aktionen ([Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)).

Die Checkliste „Offboarding eines Auftragnehmers“ im Abschnitt mit den Checklisten für Verwaltungen führt diese Schritte
auf.

## Verwandte Seiten

- [Der Lebenszyklus des Produkts](README.md)
- [Eine Website über einen mehrjährigen Vertrag aktuell halten](eine-website-aktuell-halten.md)
- [Kunden-Offboarding (für Hoster)](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
