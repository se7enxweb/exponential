---
title: An einen Kunden oder einen neuen Anbieter übergeben
audience: reseller, partner, hosting
category: reselling
order: 370
summary: Wie ein Wiederverkäufer eine Website am Ende eines Projekts oder eines Vertrags übergibt - ein vollständiger Export (Datenbank, var/storage, Einstellungen), Konten, Domain, eigener Code mit den Pflichten aus der GPL, Dokumentation, die Reihenfolge der Schritte, die Löschung danach - und warum ein sauberer Ausstieg Teil eines guten Angebots ist.
keywords: Übergabe, übergeben, Ausstieg, Offboarding, Export, Datenbank-Dump, var/storage, settings/override, settings/siteaccess, Domain, Konten, eigener Code, GPL, Löschung, neuer Anbieter, handover, hand over, exit, offboarding, export, database dump, domain, accounts, custom code, deletion, new provider
type: help
---
# An einen Kunden oder einen neuen Anbieter übergeben

Diese Seite ist für technische Leiter und Kundenbetreuer bei Wiederverkäufern. Übergaben finden am Ende eines
Aufbauprojekts statt, wenn ein Kunde die Website selbst übernimmt oder wenn er zu einem anderen Anbieter wechselt.
Sie gut durchzuführen gehört zu Ihrem Ruf, und die GPL bedeutet, dass ein Kunde immer gehen kann; ein sauberer
Ausstieg ist das, was ihn Ihnen genug vertrauen lässt, um zu bleiben.

## Inhalt

1. [Wann eine Übergabe stattfindet](#wann-eine-übergabe-stattfindet)
2. [Was eine vollständige Übergabe enthält](#was-eine-vollständige-übergabe-enthält)
3. [Die Reihenfolge der Schritte](#die-reihenfolge-der-schritte)
4. [Eigener Code und die GPL](#eigener-code-und-die-gpl)
5. [Nach der Übergabe](#nach-der-übergabe)
6. [Checkliste für die Übergabe](#checkliste-für-die-übergabe)

## Wann eine Übergabe stattfindet

| Situation | Wer übernimmt |
|---|---|
| Ende eines Aufbauprojekts | Der Kunde oder der Anbieter, der die Website betreiben wird |
| Der Kunde übernimmt die Website selbst | Das eigene Team des Kunden |
| Der Kunde wechselt den Anbieter | Der neue Anbieter |
| Sie stellen eine Dienstleistung ein | Der Kunde und ein Anbieter, den Sie empfehlen |

## Was eine vollständige Übergabe enthält

Eine vollständige Website besteht aus der Datenbank und den Dateien:

| Position | Detail | Quelle |
|---|---|---|
| Datenbank | Ein Dump, erstellt mit dem eigenen Werkzeug der Engine | [Datenbanksicherungen je Engine](../../hosting/betrieb/datenbank-backups-je-engine.md) |
| Dateien | `var/storage` (der Speicher jeder Website, wo getrennt) | [Was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |
| Einstellungen | `settings/override` und `settings/siteaccess`, vertraulich übermittelt | ebenda |
| Code | Die Exponential-Version und die Erweiterungen mit Versionen; eigene Erweiterungen und Designs wie vereinbart | [Kunden aktuell halten](kunden-aktuell-halten.md) |
| Konten | Die Liste der Konten, die Ihr Personal hatte, damit sie deaktiviert werden können | [Kunden-Offboarding](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md) |
| Domain und DNS | Auf den Namen des Kunden oder zum vereinbarten Datum übertragen | |
| Dokumentation | Inventar der Website, Runbooks, die Sie geschrieben haben, das Kundenhandbuch | [Dokumentation, die Sie Ihren Kunden geben können](dokumentation-fuer-ihre-kunden.md) |

## Die Reihenfolge der Schritte

1. Vereinbaren Sie das Datum, wer die Website bis dahin betreibt und wie Geheimnisse übermittelt werden (nie per
   einfacher E-Mail).
2. Kündigen Sie einen kurzen Inhaltsstopp an oder nutzen Sie für den letzten Export den Wartungsmodus
   ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).
3. Erstellen Sie den letzten Export und prüfen Sie, dass er sich auf einem Testrechner wiederherstellen lässt.
4. Übermitteln Sie ihn vertraulich; der Empfänger bestätigt, dass die Website am neuen Ort funktioniert.
5. Lassen Sie den Empfänger das Datenbankpasswort und jedes Geheimnis in `settings/override` ändern.
6. Deaktivieren Sie die Konten Ihres Personals in der Administration des Kunden und bestätigen Sie das schriftlich.
7. Schließen oder übertragen Sie Hosting- und Panel-Konten zum vereinbarten Datum.

## Eigener Code und die GPL

- Code, den Sie übergeben, wird weitergegeben: Der Empfänger erhält für die GPL-Teile die Rechte aus der GPL, mit der
  Lizenz, den Urheberrechtshinweisen und dem Quellcode ([Checkliste der Pflichten bei der Weitergabe](../verantwortlichkeiten/checkliste-der-pflichten-bei-der-weitergabe.md)).
- Was Sie sonst übergeben (Ihre eigenen Erweiterungen, Designdateien), richtet sich nach Ihrem Vertrag
  ([Verträge mit Kunden](vertraege-mit-kunden.md#ausstieg-und-übergabe)).
- Prüfen Sie die Lizenzen von Schriften und Stockbildern: Sie lassen sich oft nicht übertragen.

## Nach der Übergabe

- Löschen Sie die Daten des Kunden nach dem vereinbarten Zeitplan von Ihren Servern und aus Ihren Datensicherungen
  und bestätigen Sie es schriftlich ([Datenschutz für Wiederverkäufer](../verantwortlichkeiten/datenschutz.md)).
- Behalten Sie nur, was Sie gesetzlich aufbewahren müssen (zum Beispiel Rechnungen).
- Bitten Sie um Rückmeldung: Warum ist der Kunde gegangen? Halten Sie es fest ([Verlängerungen und Abwanderung](verlaengerungen-und-abwanderung.md)).
- Bleiben Sie höflich: Kunden kommen zurück, und sie reden.

## Checkliste für die Übergabe

- [ ] Datum und Plan schriftlich vereinbart.
- [ ] Letzter Export: Datenbank-Dump, `var/storage`, `settings/override`, `settings/siteaccess`.
- [ ] Wiederherstellung des Exports getestet.
- [ ] Code und Versionen aufgelistet; eigener Code wie vereinbart übergeben, mit Hinweisen und Lizenz.
- [ ] Geheimnisse vertraulich übermittelt; der Empfänger hat Passwörter geändert.
- [ ] Domain und DNS auf den Namen des Kunden.
- [ ] Ihre Konten deaktiviert und bestätigt.
- [ ] Dokumentation übergeben.
- [ ] Daten nach Zeitplan gelöscht; Löschung bestätigt.

## Verwandte Seiten

- [Checkliste für die Übergabe (für Website-Betreiber)](../../kunden/partner-und-hoster/checkliste-uebergabe.md)
- [Kunden-Offboarding](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)
- [Eine Website zwischen Hostern umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
