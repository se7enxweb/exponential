---
title: Checkliste für die Intranet-Einführung
audience: businesses, project-manager, admin
category: business-solutions
order: 100
summary: Eine Checkliste für die Einführung eines Exponential-Intranets, von der ersten Testinstallation über Berechtigungen, Anmeldung, Migration der Inhalte, Benachrichtigungen und Betrieb bis zu dem Tag, an dem die Mitarbeiter eingeladen werden, und der ersten Prüfung nach dem Start.
keywords: Intranet-Einführung, Checkliste für den Start, Go-live, Intranet-Start, Berechtigungstest, Anmeldetest, Migration der Inhalte, Schulung, Backups, Cronjobs, Zugriffsprüfung, Pilotgruppe, intranet rollout, launch checklist, go-live, intranet launch, permissions test, sign-in test, content migration, training, backups, cronjobs, access review, pilot group
type: help
---
# Checkliste für die Intranet-Einführung

Diese Seite ist für die Projektleitung, die ein Intranet vom Plan in den täglichen Gebrauch bringt. Jeder Block listet
auf, was zu prüfen ist, und verlinkt die Seite, die es erklärt. Kopieren Sie die Liste in Ihr Projektwerkzeug, tragen
Sie bei jeder Zeile einen Namen ein, und laden Sie nicht alle Mitarbeiter ein, bevor jeder Block abgehakt ist.

## Inhalt

1. [Phase 1: Grundlagen](#phase-1-grundlagen)
2. [Phase 2: Struktur und Berechtigungen](#phase-2-struktur-und-berechtigungen)
3. [Phase 3: Anmeldung](#phase-3-anmeldung)
4. [Phase 4: Inhalte](#phase-4-inhalte)
5. [Phase 5: Kommunikation und Freigabe](#phase-5-kommunikation-und-freigabe)
6. [Phase 6: Betrieb](#phase-6-betrieb)
7. [Phase 7: Pilotgruppe](#phase-7-pilotgruppe)
8. [Tag des Starts](#tag-des-starts)
9. [Nach dem Start](#nach-dem-start)
10. [Verwandte Seiten](#verwandte-seiten)

## Phase 1: Grundlagen

- [ ] Zweck, verantwortliche Person und Umfang schriftlich festgehalten ([Intranet planen](intranet-planen.md)).
- [ ] Hosting gewählt und die Installation läuft ([Hosting auswählen](../auswahl/hosting-auswaehlen.md)).
- [ ] Hostname und HTTPS-Zertifikat vorhanden
  ([Live gehen: Hosts und HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md)).
- [ ] Der Siteaccess des Intranets hat `RequireUserLogin=true`
  ([Was Besucher sehen dürfen, Siteaccess settings](../../ersteller/website-ersteller/rechte-fuer-besucher.md#einstellungen-des-siteaccess)).
- [ ] Netzwerkbeschränkung entschieden und, falls gewünscht, im Webserver eingerichtet
  ([Network restrictions](../../hosting/sicherheit/die-administration-einschraenken.md#einschränkungen-im-netzwerk)).
- [ ] Das Standardpasswort des Administrators geändert
  ([Standard-Administratorpasswort](../../standard-administratorpasswort.md)).

## Phase 2: Struktur und Berechtigungen

- [ ] Eine Sektion pro Bereich, jede mit einer Kennung ([Sektionen](../../../anleitungen/sektionen.md#5-eine-sektion-anlegen)).
- [ ] Benutzergruppen pro Abteilung ([Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md)).
- [ ] Rolle Mitarbeiter: `content/read` für die Mitarbeitersektionen, `user/login` für den Siteaccess des Intranets,
  `notification/use`.
- [ ] Rolle Redakteur pro Abteilung mit einer Sektions-Einschränkung zugeordnet.
- [ ] Keine Rolle außer Administrator zeigt *Voller Zugriff* oder *Kann Rollen ändern*
  ([Rollen und Richtlinien, 1](../../../anleitungen/rollen-und-richtlinien.md#1-die-rollenliste-lesen)).
- [ ] Wenn das Intranet eine Installation mit einer öffentlichen Website teilt: Die öffentliche Rolle Anonymous kann
  keine Sektion des Intranets lesen; in einem privaten Browserfenster getestet
  ([Als Besucher testen](../../ersteller/website-ersteller/rechte-fuer-besucher.md#als-besucher-testen)).

## Phase 3: Anmeldung

- [ ] Quelle der Konten entschieden: lokal, LDAP oder beides
  ([Verzeichnisanmeldung und LDAP](verzeichnisanmeldung-und-ldap.md)).
- [ ] Passwortregeln in `[PasswordSettings]` gesetzt
  ([Zwei-Faktor und Anmeldung](zwei-faktor-und-anmeldung.md#passwortregeln)).
- [ ] `MaxNumberOfFailedLogin` auf einen anderen Wert als `0` gesetzt.
- [ ] Zweistufige Anmeldung aktiv; Administratoren und Redakteure zuerst eingerichtet
  ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Der Helpdesk weiß, wie man ein Passwort zurücksetzt und mit einem verlorenen zweiten Schritt umgeht
  ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md#was-ein-benutzer-sieht),
  [Das Passwort eines Benutzers zurücksetzen](../../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md)).
- [ ] Offene Registrierung geschlossen: `user/register` im Intranet nicht an Anonymous vergeben
  ([Rollen und Richtlinien, 8](../../../anleitungen/rollen-und-richtlinien.md#8-festlegen-wer-sich-registrieren-und-wer-knotendaten-lesen-darf)).

## Phase 4: Inhalte

- [ ] Bestandsaufnahme der Dokumente erledigt; Verantwortliche benannt
  ([Dokumente und Dateibibliotheken](dokumente-und-dateibibliotheken.md)).
- [ ] Dokumente migriert; Duplikate entfernt.
- [ ] Geschützte Downloads als Benutzer ohne Zugriff getestet.
- [ ] Suche als Mitglieder zweier Abteilungen getestet ([Suche im Intranet](suche-im-intranet.md)).
- [ ] Suchindex nach der Migration neu aufgebaut.
- [ ] Startseite mit den neuesten Neuigkeiten ([Neuigkeiten und Ankündigungen](neuigkeiten-und-ankuendigungen.md)).
- [ ] Redakteure geschult ([Die Website an Redakteure übergeben](../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md)).

## Phase 5: Kommunikation und Freigabe

- [ ] Freigabe-Workflow, wo nötig, eingerichtet, ein Test freigegeben und einer abgelehnt
  ([Freigabe-Workflows](freigabe-workflows.md)).
- [ ] Freigebende haben die Benachrichtigung zur Zusammenarbeit Freigabe markiert.
- [ ] Lauf der Benachrichtigungen getestet; die Statusseite zeigt einen kürzlichen Lauf
  ([Benachrichtigungen für Mitarbeiter](benachrichtigungen-fuer-mitarbeiter.md)).
- [ ] Absender und Zustellung der Mails getestet ([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).

## Phase 6: Betrieb

- [ ] Cronjobs eingeplant, einschließlich der Gruppe `frequent` (Workflow, Benachrichtigungen, Audit)
  ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Backups eingeplant und eine Wiederherstellung getestet
  ([Backups, die sich wiederherstellen lassen](../zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)).
- [ ] Status des Prüfprotokolls geprüft: `./console exp:audit status`
  ([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md#in-zwei-minuten-ausprobieren)).
- [ ] Plan für Aktualisierungen vereinbart
  ([Aktualisierungen ohne Überraschungen](../zuverlaessigkeit/aktualisierungen-ohne-ueberraschungen.md)).
- [ ] Monitoring der Adresse des Intranets eingerichtet
  ([Health-Checks und Monitoring](../zuverlaessigkeit/health-checks-und-monitoring.md)).

## Phase 7: Pilotgruppe

- [ ] Zuerst eine kleine Pilotgruppe mit Personen aus verschiedenen Abteilungen eingeladen.
- [ ] Jeder Pilotbenutzer meldet sich an, richtet den zweiten Schritt ein, findet drei benannte Dokumente und folgt
  einem Ordner.
- [ ] Rückmeldungen gesammelt; Suchstatistiken ausgewertet, wenn eingeschaltet.
- [ ] Probleme vor der breiteren Einführung behoben.

## Tag des Starts

- [ ] Konten für alle Mitarbeiter existieren oder werden bei der ersten Verzeichnisanmeldung angelegt.
- [ ] Einladungsmail mit der Adresse, wie man sich anmeldet und wo es Hilfe gibt.
- [ ] Helpdesk für die ersten Tage besetzt.
- [ ] Altes gemeinsames Laufwerk oder altes Intranet auf nur lesbar gesetzt, mit einem Datum zum Abschalten.

## Nach dem Start

- [ ] Prüfung nach 30 Tagen: wonach Mitarbeiter gesucht und was sie nicht gefunden haben, welche Bereiche nicht
  aktualisiert werden.
- [ ] Erste Zugriffsprüfung nach 90 Tagen ([Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md)).
- [ ] Ungenutzte Konten unter **Benutzer > Benutzerkonten prüfen** geprüft
  ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).
- [ ] Monatliche Betriebsroutine eingerichtet
  ([Monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md)).

## Verwandte Seiten

- [Intranets](README.md)
- [Vorlage Checkliste Go-live](../vorlagen/checkliste-go-live.md)
- [Qualitätsschwellen und Abnahme](../../fuehrung/qualitaetsschranken-und-abnahme.md)
- [Checkliste für den Start für Website-Ersteller](../../ersteller/website-ersteller/checkliste-fuer-den-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
