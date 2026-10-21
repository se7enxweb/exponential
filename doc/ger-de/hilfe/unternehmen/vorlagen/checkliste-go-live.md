---
title: "Checkliste: Go-live"
audience: businesses, project-manager, admin, website-creator
category: business-solutions
order: 50
summary: Eine kopierfertige Go-live-Checkliste für die geschäftliche Seite beim Start einer Exponential-Website - Inhalte, rechtliche Seiten, Konten, Shop und Formulare, Sicherheit, Backups, Monitoring, Kommunikation und die Entscheidung zum Start - mit Links auf die technischen Go-live-Listen für Hoster und Website-Ersteller.
keywords: Go-live, Start, Livegang, Checkliste, Starttag, Umstellung, DNS, HTTPS, Backups, Monitoring, Konten, zweistufige Anmeldung, Datenschutzhinweis, Shop, Formulare, Abnahme, Rückfall, launch, checklist, launch day, cut-over, accounts, two-step sign-in, privacy notice, store, forms, acceptance, rollback
type: help
---
# Checkliste: Go-live

Diese Seite ist für Inhaber und Projektleiter, die „ja, wir starten“ sagen müssen. Sie deckt ab, was das Unternehmen
bestätigen muss; die technischen Listen für diejenigen, die die Arbeit machen, sind die
[Start-Checkliste für Website-Ersteller](../../../../eng-us/help/creators/website-creators/launch-checklist.md), die
[Checkliste für den Start einer Kunden-Website für Hoster](../../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md) und die
Start-Checkliste in den [Projektvorlagen](../../fuehrung/projektvorlagen.md#livegang-checkliste). Lassen Sie diese
ausfüllen und unterschreiben; verwenden Sie dann diese hier.

## Inhalt

1. [So verwenden Sie sie](#so-verwenden-sie-sie)
2. [Die Checkliste](#die-checkliste)
3. [Plan für den Starttag](#plan-für-den-starttag)
4. [Die ersten zwei Wochen](#die-ersten-zwei-wochen)

## So verwenden Sie sie

- Gehen Sie sie mit den Verantwortlichen durch, eine Woche und noch einmal einen Tag vor dem Start.
- Jede Zeile hat einen Verantwortlichen und einen Nachweis (ein Ticket, einen Screenshot, ein Testergebnis).
- Ein „Nein“ in einer Zeile mit der Markierung **(Blocker)** bedeutet, dass der Start verschoben wird.

## Die Checkliste

```text
GO-LIVE-CHECKLISTE - <Website>   Geplanter Start: <Datum, Uhrzeit, Zeitzone>   Entscheidung durch: <Name>

INHALTE                                                        Verantw.  Erl.  Nachweis
[ ] Alle geplanten Inhalte veröffentlicht und Korrektur gelesen ______    [ ]   ______
[ ] Kontaktdaten, Öffnungszeiten, Preise korrekt               ______    [ ]   ______
[ ] Alte URLs leiten auf neue weiter (bei Ablösung einer Site) ______    [ ]   ______
[ ] Die Suche findet die Hauptseiten                           ______    [ ]   ______

RECHT UND DATEN (fragen Sie Ihren Berater)
[ ] Datenschutzhinweis und Impressum veröffentlicht (Blocker)  ______    [ ]   ______
[ ] Formulare fragen nur nötige Daten ab und verlinken Hinweis ______    [ ]   ______
[ ] Mail-Fußzeile: Name und Postanschrift der Organisation     ______    [ ]   ______
[ ] Shop-AGB, USt- und Versandtexte geprüft                    ______    [ ]   ______
[ ] Aufbewahrungsfristen beschlossen und eingestellt           ______    [ ]   ______

KONTEN UND ZUGÄNGE
[ ] Jede Person hat ein persönliches Konto; kein geteilter Admin ______  [ ]   ______
[ ] Anfängliches Administratorpasswort geändert (Blocker)      ______    [ ]   ______
[ ] Zweistufige Anmeldung für Administratoren eingeschaltet    ______    [ ]   ______
[ ] Test- und Agenturkonten entfernt oder deaktiviert          ______    [ ]   ______

SHOP UND FORMULARE (falls genutzt)
[ ] Testbestellung bezahlt, bestätigt, per Mail erhalten (Blocker) ____  [ ]   ______
[ ] Zahlungsanbieter vom Test- in den Live-Modus geschaltet    ______    [ ]   ______
[ ] Jedes Formular getestet; Einsendungen erreichen die Richtigen ____   [ ]   ______
[ ] Newsletter-Anmeldung und -Abmeldung getestet               ______    [ ]   ______

SICHERHEITSNETZ
[ ] HTTPS auf jeder Adresse; Zertifikat erneuert sich selbst   ______    [ ]   ______
[ ] Backup erstellt und eine Wiederherstellung getestet (Blocker) _____  [ ]   ______
[ ] Externes Monitoring eingerichtet; Warnungen erreichen eine Person __ [ ]   ______
[ ] Empfänger der Audit-Warnungen eingetragen                  ______    [ ]   ______
[ ] Rückfallplan geschrieben (alte Site oder Backup bereit)    ______    [ ]   ______

MENSCHEN UND KOMMUNIKATION
[ ] Redakteure geschult; Übergabedokument geliefert            ______    [ ]   ______
[ ] Support-Kontakt und Reaktionszeiten den Mitarbeitern bekannt ______  [ ]   ______
[ ] Ankündigung an Kunden, Mitglieder oder Mitarbeiter vorbereitet _____ [ ]   ______

ENTSCHEIDUNG
Technische Checklisten unterschrieben von: ______________   am: ________
Go / No-Go: ________   Entschieden von: ______________   am: ________
```

Worauf jede Zeile beruht: Datenschutz und Mail-Fußzeile unter [Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md);
Konten unter [Benutzerkonten und Passwörter](../sicherheit/benutzerkonten-und-passwoerter.md); der Shop in der
[Checkliste für den Shop-Start](../shops/checkliste-fuer-den-shop-start.md); Backups unter
[Backups, die sich wiederherstellen lassen](../zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md); Monitoring unter
[Zustandsprüfungen und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md); Audit-Warnungen unter
[Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#try-it-in-two-minutes).

## Plan für den Starttag

```text
STARTTAG - <Datum>
<Uhrzeit>  Inhaltsstopp auf der alten Site (falls vorhanden)
<Uhrzeit>  Letztes Backup der alten und der neuen Site
<Uhrzeit>  Wartungsmodus an, falls die Umstellung ihn braucht
<Uhrzeit>  Umstellung (DNS- oder Serverwechsel) - durch <Name>
<Uhrzeit>  Kurztest: Startseite, Anmeldung, ein Formular, eine Testbestellung - durch <Name>
<Uhrzeit>  Wartungsmodus aus
<Uhrzeit>  Ankündigung versendet
<Uhrzeit>  Logs und Monitoring prüfen - durch <Name>
Entscheidungspunkt für den Rückfall: <Uhrzeit>; Rückfall entscheidet <Name>
```

Der Wartungsmodus zeigt Besuchern eine freundliche Seite, während gearbeitet wird
([Wartungsmodus](../../../../eng-us/features/6.0/maintenance-mode.md)). Wählen Sie eine ruhige Stunde
([Arbeitsblatt: Kosten eines Ausfalls](../kosten/arbeitsblatt-kosten-eines-ausfalls.md)).

## Die ersten zwei Wochen

- [ ] Sehen Sie sich die Fehler „nicht gefunden“ an (`404.log` im Log-Viewer) und legen Sie bei Bedarf Weiterleitungen an
      ([Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md)).
- [ ] Lesen Sie Fehlerreferenzen, die Besucher melden ([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).
- [ ] Sammeln Sie die Fragen der Redakteure und aktualisieren Sie das Übergabedokument.
- [ ] Bestätigen Sie, dass das erste geplante Backup gelaufen ist und die ersten Cronjobs ihre Arbeit getan haben.
- [ ] Halten Sie eine kurze Rückschau; notieren Sie, was Sie beim nächsten Start besser machen.

## Verwandte Seiten

- [Monatliche Betriebscheckliste](monatliche-betriebscheckliste.md)
- [Checkliste für den Shop-Start](../shops/checkliste-fuer-den-shop-start.md)
- [Checkliste für die Intranet-Einführung](../intranets/checkliste-fuer-die-intranet-einfuehrung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
