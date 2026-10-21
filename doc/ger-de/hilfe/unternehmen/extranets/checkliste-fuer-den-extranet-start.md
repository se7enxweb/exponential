---
title: Checkliste für den Extranet-Start
audience: businesses, project-manager, admin
category: business-solutions
order: 80
summary: Alles, was abzuhaken ist, bevor der erste Partner, Kunde, Händler oder Lieferant in ein Exponential-Extranet eingeladen wird - Adresse und HTTPS, Trennungstests, Registrierung und Freigabe, Anmeldung, Dateien, Bestellen, Mail, Betrieb und der Termin der ersten Prüfung.
keywords: Extranet-Start, Go-live des Partnerportals, Checkliste, Trennungstest, Registrierungstest, Freigabetest, Zwei-Faktor, Downloads, Ermäßigungsgruppen, Mailzustellung, Backups, Prüftermin, extranet launch, partner portal go-live, checklist, separation test, registration test, approval test, two-factor, downloads, discount groups, mail delivery, backups, review date
type: help
---
# Checkliste für den Extranet-Start

Diese Seite ist für die Projektleitung, die ein Portal für externe Organisationen öffnet. Einen Fehler in einem Extranet
sehen Menschen außerhalb des Unternehmens; testen Sie also mehr als bei einem Intranet. Kopieren Sie die Liste, tragen
Sie bei jeder Zeile einen Namen ein, und laden Sie einen ersten Partner erst ein, wenn jeder Block abgehakt ist.

## Inhalt

1. [Adresse und Grundlagen](#adresse-und-grundlagen)
2. [Trennung](#trennung)
3. [Konten](#konten)
4. [Anmeldung](#anmeldung)
5. [Dateien](#dateien)
6. [Bestellen, falls angeboten](#bestellen-falls-angeboten)
7. [Mail und Benachrichtigungen](#mail-und-benachrichtigungen)
8. [Betrieb](#betrieb)
9. [Pilotpartner](#pilotpartner)
10. [Nach dem Start](#nach-dem-start)
11. [Verwandte Seiten](#verwandte-seiten)

## Adresse und Grundlagen

- [ ] Der Hostname des Portals antwortet mit HTTPS und dem richtigen Siteaccess
  ([Getrennte Hosts und Siteaccesses](getrennte-hosts-und-siteaccesses.md)).
- [ ] `SiteURL` ist die Adresse, die Partner verwenden; Links in Mails werden daraus gebaut.
- [ ] Der Siteaccess des Portals hat `RequireUserLogin=true`; die öffentliche Website hat `false`.
- [ ] Das Standardpasswort des Administrators wurde geändert
  ([Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
- [ ] Die Administration hat einen eigenen Hostnamen oder Netzwerkbeschränkungen
  ([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md)).
- [ ] Das Design des Portals zeigt, wer angemeldet ist, und einen Link zum Abmelden.

## Trennung

- [ ] Es gibt Testbenutzer für mindestens zwei Partnerorganisationen und zwei Stufen.
- [ ] Jeder Testbenutzer sieht seinen eigenen Bereich und nicht den des anderen, in Seiten, Menüs, Suche und Downloads
  ([Kunden- und Partnerbereiche, Die Trennung testen](kunden-und-partnerbereiche.md#die-trennung-testen)).
- [ ] Abgemeldet zeigt der öffentliche Host nichts vom Portal; der Host des Portals zeigt nur die Anmeldeseite und die
  Seiten in `AnonymousAccessList[]`.
- [ ] Keine Partnerrolle zeigt *Voller Zugriff* oder *Kann Rollen ändern*
  ([Rollen und Richtlinien, 1](../../../anleitungen/rollen-und-richtlinien.md#1-die-rollenliste-lesen)).
- [ ] `user/login` ist in jeder Rolle auf die richtigen Siteaccesses beschränkt.

## Konten

- [ ] Wie Konten angelegt werden, ist entschieden und dokumentiert
  ([Registrierung und Kontofreigabe](registrierung-und-kontofreigabe.md)).
- [ ] Wenn die Registrierung offen ist: `DefaultUserPlacement` zeigt auf eine Gruppe für Bewerber ohne Rechte.
- [ ] Die Mail „Neuer Benutzer registriert“ erreicht ein überwachtes Postfach; Links zum Freigeben und Ablehnen
  getestet.
- [ ] Wenn Konten bis zur Freigabe zurückgehalten werden: in einem Test eines freigegeben und eines abgelehnt, bei
  laufendem Cronjob des Workflows.
- [ ] CAPTCHA im Registrierungsformular
  ([Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)).
- [ ] Das Registrierungsformular und der Datenschutzhinweis sagen, welche Daten aufbewahrt werden und warum
  ([Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md)).

## Anmeldung

- [ ] Passwortregeln festgelegt ([Zwei-Faktor und Anmeldung](../intranets/zwei-faktor-und-anmeldung.md#passwortregeln)).
- [ ] `MaxNumberOfFailedLogin` auf einen anderen Wert als `0` gesetzt.
- [ ] Zweistufige Anmeldung entschieden: freiwillig, für alle vorgeschrieben oder für sensible Stufen vorgeschrieben
  ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Zurücksetzen des Passworts von der Anmeldeseite aus getestet
  ([Das Passwort eines Benutzers zurücksetzen](../../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md)).
- [ ] Ansprechpartner im Support für ausgesperrte Partner benannt.

## Dateien

- [ ] Jeder Dateibereich in der richtigen Sektion oder im richtigen Partner-Teilbaum
  ([Dateien mit Partnern teilen](dateien-mit-partnern-teilen.md)).
- [ ] Ein geschützter Download als Benutzer ohne Zugriff versucht: abgelehnt.
- [ ] Upload-Rollen der Partner auf die Klasse File und den Ordner des Partners beschränkt; kein Entfernen.
- [ ] Mitarbeiter folgen den Upload-Ordnern.

## Bestellen, falls angeboten

- [ ] `shop/buy` nur in der Händlerrolle ([Händler- und Lieferantenportale](haendler-und-lieferantenportale.md)).
- [ ] Ermäßigungsgruppen und -regeln entsprechen der Preispolitik.
- [ ] USt-Regeln und Währungen geprüft.
- [ ] Eine vollständige Testbestellung aufgegeben und im Tab Shop bearbeitet
  ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)).

## Mail und Benachrichtigungen

- [ ] Absenderadresse und Zustellung an ein externes Postfach getestet
  ([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).
- [ ] Die Fußzeile der E-Mail-Einstellungen enthält den Namen der Organisation und die Postanschrift
  ([E-Mail-Einstellungen: einrichten](../../../anleitungen/mail-einstellungen-administrator.md)).
- [ ] Lauf der Benachrichtigungen unter **Setup > Benachrichtigungen** sichtbar
  ([Benachrichtigungen: die Anleitung für Administratoren](../../../anleitungen/benachrichtigungen-administrator.md)).

## Betrieb

- [ ] Cronjobs eingeplant, einschließlich der Gruppe `frequent` ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Backups eingeplant und eine Wiederherstellung getestet
  ([Backups, die sich wiederherstellen lassen](../zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)).
- [ ] Prüfprotokoll läuft: `./console exp:audit status`
  ([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md#in-zwei-minuten-ausprobieren)).
- [ ] Monitoring der Portaladresse ([Health-Checks und Monitoring](../zuverlaessigkeit/health-checks-und-monitoring.md)).
- [ ] Plan für Aktualisierungen vereinbart
  ([Aktualisierungen ohne Überraschungen](../zuverlaessigkeit/aktualisierungen-ohne-ueberraschungen.md)).

## Pilotpartner

- [ ] Zuerst einen wohlgesinnten Partner eingeladen.
- [ ] Der Partner meldet sich an, findet drei benannte Dokumente, lädt eine Datei hoch und gibt (falls angeboten) eine
  Testbestellung auf.
- [ ] Rückmeldungen gesammelt und behoben, bevor weitere Partner eingeladen werden.

## Nach dem Start

- [ ] Termin der ersten Berechtigungsprüfung festgelegt
  ([Berechtigungen im Extranet prüfen](berechtigungen-im-extranet-pruefen.md)).
- [ ] Verfahren für beendete Partnerschaften mit den Verantwortlichen für die Beziehungen vereinbart.
- [ ] Nicht aktivierte Registrierungen werden regelmäßig bereinigt
  ([Rollen und Richtlinien, 7](../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen)).

## Verwandte Seiten

- [Extranets und Partnerportale](README.md)
- [Checkliste für die Intranet-Einführung](../intranets/checkliste-fuer-die-intranet-einfuehrung.md)
- [Vorlage Checkliste Go-live](../vorlagen/checkliste-go-live.md)
- [Checkliste zur Sicherheitsprüfung für Hoster](../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
