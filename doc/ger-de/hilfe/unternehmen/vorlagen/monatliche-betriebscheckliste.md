---
title: Monatliche Betriebscheckliste
audience: businesses, admin, hosting
category: business-solutions
order: 60
summary: Eine kopierfertige monatliche Checkliste, damit eine laufende Exponential-Website gesund und sicher bleibt - Releases und Updates, Backups, Logs und Fehler, Sicherheit, Konten, Jobs, Inhalte und Formulare - mit dem Ort, an dem jede Prüfung stattfindet, und einem kurzen Bericht für die Geschäftsleitung.
keywords: monatlicher Betrieb, Checkliste, Wartung, Updates, Backups, Logs, Fehler, Sicherheit, Prüfprotokoll, Konten, Cronjobs, Festplatte, Bericht, Betrieb, monthly operations, checklist, maintenance, logs, errors, security, audit trail, accounts, cron jobs, disk, report, operations
type: help
---
# Monatliche Betriebscheckliste

Diese Seite ist für den Administrator, Partner oder Hoster, der sich jeden Monat um eine laufende Website kümmert, und
für den Inhaber, der wissen will, dass es erledigt wurde. Sie ist das geschäftliche Gegenstück zur
[Checkliste für den monatlichen Betrieb für Hoster](../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md), die tiefer in die
Serverarbeit geht. Ein Monat ohne diese Prüfungen ist der Weg, auf dem aus kleinen Problemen Ausfälle werden.

## Inhalt

1. [Wie lange es dauert](#wie-lange-es-dauert)
2. [Die Checkliste](#die-checkliste)
3. [Wo jede Prüfung stattfindet](#wo-jede-prüfung-stattfindet)
4. [Der Bericht an die Geschäftsleitung](#der-bericht-an-die-geschäftsleitung)

## Wie lange es dauert

Auf einer gut geführten Website etwa eine Stunde im Monat, zuzüglich der Zeit, um ein Update einzuspielen. Die
[Seite zur Budgetplanung](../kosten/budget-fuer-aktualisierungen-und-wartung.md#ein-durchgerechnetes-jahresbudget) verwendet das als
Beispielannahme; messen Sie Ihre eigenen Werte.

## Die Checkliste

```text
MONATLICHER BETRIEB - <Website>   Monat: <Monat Jahr>   Erledigt von: <Name>   Datum: <Datum>

UPDATES                                                                     Erl.  Notiz
[ ] Neues Exponential-6.0.x-Release? Changelog lesen; Update planen         [ ]   ____
[ ] Updates der Erweiterungen geprüft (Setup > Updates und Pakete)          [ ]   ____
[ ] PHP- / Betriebssystem-Updates vom Hoster eingespielt                   [ ]   ____
[ ] Upgrade-Prüfung nach jedem Update sauber                               [ ]   ____

BACKUPS
[ ] Backups liefen in diesem Monat jeden Tag                               [ ]   ____
[ ] Backup umfasst Datenbank, Dateien, Einstellungen, Schlüssel, Prüfprotokoll [ ] ____
[ ] Wiederherstellungstest in diesem Quartal? (Datum: ______)              [ ]   ____

LOGS UND FEHLER
[ ] Fehlerlog gelesen; wiederkehrende Fehler als Ticket erfasst            [ ]   ____
[ ] Liste „nicht gefunden“ (404) gelesen; Weiterleitungen angelegt         [ ]   ____
[ ] Speicherplatz in Ordnung (Zustandsprüfungen in Systeminformationen)    [ ]   ____

SICHERHEIT
[ ] Audit-Dashboard angesehen; Warnungen des Monats beantwortet            [ ]   ____
[ ] Audit-Kette verifiziert                                                [ ]   ____
[ ] Fehlgeschlagene Anmeldungen: etwas Ungewöhnliches?                     [ ]   ____
[ ] Zertifikat noch mehr als 30 Tage gültig / erneuert sich automatisch    [ ]   ____

KONTEN
[ ] Konten der Ausgeschiedenen des Monats deaktiviert                      [ ]   ____
[ ] Neue Administratoren beabsichtigt? (Rollenzuweisungen im Prüfprotokoll) [ ]   ____

JOBS UND INHALTE
[ ] Cronjobs gelaufen (Cronjob-Seite)                                      [ ]   ____
[ ] Defekte Links und Inhaltsprüfungen durchgesehen                        [ ]   ____
[ ] Formulareinsendungen bearbeitet; alte gemäß Aufbewahrung entfernt      [ ]   ____
[ ] Mail-Status sauber (exp:mail:status)                                   [ ]   ____

FÜR DEN BERICHT
Vorfälle in diesem Monat: ____   Ausfallzeit (Stunden): ____   Eingespielte Updates: ____
Offene Risiken: ______________________________________________
```

## Wo jede Prüfung stattfindet

| Prüfung | Wo | Lesen |
|---|---|---|
| Releases | GitHub-Releases und der in `README.md` genannte Issue-Tracker | [Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md) |
| Updates der Erweiterungen | **Setup > Updates und Pakete** (`ezupdate`), die Prüfung ändert nichts | [Automatische Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md) |
| Upgrade-Prüfung | **Setup > Upgrade-Prüfung** | [Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) |
| Backups | der Backup-Bericht Ihres Hosters | [Backups: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |
| Logs und 404-Fehler | der Log-Viewer | [Log-Viewer](../../../funktionen/6.0/log-betrachter.md), [Request-Logs](../../../funktionen/6.0/anfrageprotokolle.md) |
| Speicherplatz und Zustand | **Systeminformationen** | [Systeminformationen](../../../anleitungen/systeminformationen.md) |
| Audit | der Tab **Audit**; `exp:audit verify` | [Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md) |
| Konten | **Benutzer > Benutzerkonten prüfen** | [Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md) |
| Cronjobs | die Cronjob-Seite | [Cronjob-Konsole](../../../funktionen/6.0/cronjobs-in-der-konsole.md) |
| Inhaltsprüfungen | Link- und Inhaltsprüfungen | [Link- und Inhaltsprüfungen](../../ersteller/website-ersteller/link-und-inhaltspruefungen.md) |
| Formulare | **Setup > Gesammelte Informationen** | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| Mail | `exp:mail:status` | [E-Mail-Einstellungen: Leitfaden für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md) |

## Der Bericht an die Geschäftsleitung

```text
WEBSITE-BERICHT - <Website> - <Monat Jahr>
Status: grün / gelb / rot
Verfügbarkeit: <Ausfallstunden, geplant und ungeplant>
Updates: <eingespielte Releases und Erweiterungen>
Sicherheit: <Warnungen, Maßnahmen>
Backups: <alle gelaufen ja/nein>, letzter Wiederherstellungstest <Datum>
Offene Risiken und Vorschläge: <...>
Nächster Monat: <geplante Arbeiten und Zeitfenster>
```

Bewahren Sie die Berichte auf; sie sind die Grundlage für die [Checkliste: Jahresrückblick](checkliste-jahresrueckblick.md)
und der Nachweis für Ihre [ROI](../kosten/rendite-der-stabilitaet.md)-Zahlen.

## Verwandte Seiten

- [Ihre Website pflegen](../../ersteller/website-ersteller/ihre-website-pflegen.md)
- [Verfügbarkeit in der Praxis](../zuverlaessigkeit/verfuegbarkeit-in-der-praxis.md)
- [Betrieb einer Website](../../../anleitungen/betrieb-einer-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
