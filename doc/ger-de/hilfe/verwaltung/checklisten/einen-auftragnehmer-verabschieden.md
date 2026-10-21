---
title: Einen Auftragnehmer verabschieden
audience: government, admin, project-manager, hosting
category: public-sector
order: 130
summary: Wie eine öffentliche Stelle die Zugänge eines Auftragnehmers oder Dienstleisters zu einer Exponential-Website und ihren Servern entfernt, wenn dessen Arbeit endet - Konten, Rollen, API-Schlüssel, Sitzungen, Server-Zugangsdaten, Schlüssel und Geheimnisse, Repositorys - und behält, was übergeben wurde.
keywords: Verabschiedung, Offboarding, Auftragnehmer, Dienstleister, Ausscheiden, Entzug von Zugängen, Konten, API-Schlüssel, Sitzungen, Zugangsdaten, Passwortwechsel, Audit, Übergabe, öffentlicher Sektor, offboarding, contractor, supplier, leaver, access removal, accounts, API keys, sessions, credentials, password rotation, handover, public sector
type: help
---
# Einen Auftragnehmer verabschieden

Diese Checkliste richtet sich an die Website-Administration und das Vertragsmanagement einer öffentlichen Stelle, wenn
ein Auftragnehmer, eine Agentur oder eine einzelne Beraterin ihre Arbeit an der Website beendet. Erledigen Sie das am
letzten Tag, nicht Wochen später.

## Inhalt

1. [Vor dem letzten Tag](#vor-dem-letzten-tag)
2. [In Exponential](#in-exponential)
3. [Server und Dienste](#server-und-dienste)
4. [Prüfen und festhalten](#prüfen-und-festhalten)

## Vor dem letzten Tag

- [ ] Listen Sie jeden Zugang des Auftragnehmers auf: Konten in Exponential, Server-Logins, Datenbankbenutzer, Repositorys, Hosting-Panel, DNS, Überwachung, E-Mail.
- [ ] Nehmen Sie die Übergabe entgegen: Code in Ihrem Repository, Dokumentation, offene Punkte, angelegte Zugangsdaten ([Das Ende eines Vertrags](../beschaffung/lebenszyklus/ende-eines-vertrags.md#was-in-jedem-fall-zu-übergeben-ist)).
- [ ] Stellen Sie sicher, dass benannte Mitarbeitende Ihrer Organisation Administratorzugang haben, bevor der des Auftragnehmers entfernt wird.

## In Exponential

- [ ] Deaktivieren oder entfernen Sie die Konten des Auftragnehmers; bei eingeschaltetem **Deaktivieren und verschieben** wandern Konten in eine verborgene Gruppe ohne Rollen und können wiederhergestellt werden ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).
- [ ] Inhalte, deren Eigentümer er ist, können einer Mitarbeiterin oder einem Mitarbeiter übertragen werden ([Den Eigentümer von Inhalten ändern](../../../anleitungen/eigentuemer-aendern.md)).
- [ ] Entfernen Sie seine Rollenzuweisungen; prüfen Sie Rollen, die er möglicherweise angelegt hat ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
- [ ] Widerrufen Sie seine persönlichen API-Schlüssel ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)) und alle OAuth-Clients (**Setup > oAuth-Verwaltung**).
- [ ] Melden Sie seine Sitzungen unter **Setup > Sitzungen** ab ([Sitzungen](../../../anleitungen/sitzungen.md)).
- [ ] Seine Entwürfe und ausstehenden Einträge neu zugewiesen oder entfernt ([Meine Entwürfe und meine Warteliste](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).
- [ ] Workflows, in denen er freigebende Person war, aktualisiert ([Workflows](../../../anleitungen/workflows.md#102-aufräumen-nach-einem-entfernten-freigebenden)).
- [ ] Entfernen Sie seine Adressen aus den Freigabelisten des Wartungsmodus und den Listen für die Debug-Ausgabe ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).

## Server und Dienste

- [ ] Entfernen Sie seine SSH-Schlüssel und Serverkonten ([Benutzer und Pools pro Kunde](../../hosting/sicherheit/benutzer-und-pools-pro-kunde.md)).
- [ ] Ändern Sie Passwörter, die er kannte: Datenbank, Hosting-Panel, gemeinsame Postfächer.
- [ ] Wechseln Sie Geheimnisse, auf die er Zugriff hatte, nachdem Sie geprüft haben, was jeder Wechsel bricht: Ein Wechsel des Geheimnisses der E-Mail-Einstellungen macht jeden bereits versendeten Abmeldelink ungültig ([Datensicherungen: Schlüssel und Geheimnisse](../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)).
- [ ] Entfernen Sie seinen Zugang zu Repositorys, Überwachung und DNS.
- [ ] Entfernen Sie seine E-Mail-Adresse aus den Empfängern von Alarmen: `./console exp:audit alerts recipients` ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).

## Prüfen und festhalten

- [ ] Prüfen Sie seine letzten Aktionen im Prüfprotokoll (`/audit/console`).
- [ ] Versuchen Sie, sich in einem Testbrowser mit seinem alten Konto anzumelden: Es muss scheitern.
- [ ] Halten Sie fest, was von wem wann entfernt wurde.
- [ ] Lassen Sie sich schriftlich bestätigen, dass der Auftragnehmer alle Kopien Ihrer Daten zurückgegeben oder gelöscht hat, wie der Vertrag es verlangt.

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Vierteljährliche Zugriffsprüfung](vierteljaehrliche-zugriffspruefung.md)
- [Abschied von Kunden (für Hoster)](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
