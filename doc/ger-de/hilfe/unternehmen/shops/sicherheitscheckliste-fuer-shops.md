---
title: Sicherheitscheckliste für Shops
audience: businesses, admin, hosting
category: business-solutions
order: 210
summary: Die Sicherheitsaufgaben, die ein Shop-Inhaber prüfen sollte, in einfacher Sprache - Konten und zweistufige Anmeldung, Shop-Berechtigungen, Zahlungen, HTTPS, Updates, Backups, Logs und das Prüfprotokoll - mit Links zu den ausführlichen Seiten zur Härtung.
keywords: Shop-Sicherheit, Sicherheit des Web-Shops, Checkliste, zweistufige Anmeldung, Rollen, Berechtigungen, HTTPS, Updates, Backups, Prüfprotokoll, Formular-Token, Content Security Policy, Meldung von Schwachstellen, store security, webshop security, checklist, two-factor, roles, permissions, updates, backups, audit trail, form tokens, vulnerability report
type: help
---
# Sicherheitscheckliste für Shops

Diese Seite richtet sich an Shop-Inhaber und Shop-Verantwortliche, die für die Sicherheit ihres Shops zuständig, aber
keine Sicherheitsfachleute sind. Sie macht aus den Härtungsempfehlungen des Projekts eine Checkliste für einen Shop,
sagt, wer jeden Punkt üblicherweise erledigt, und verweist auf die ausführliche Seite. Geben Sie sie Ihrem Hoster oder
Partner und bitten Sie ihn, jede Zeile zu bestätigen.

> **Hinweis**
> Dies sind Grundlagen, keine Rechtsberatung. Pflichten zur Kartensicherheit, zum Datenschutz und zum
> Verbraucherrecht hängen davon ab, wo Sie verkaufen, und von Ihren Verträgen. Lesen Sie die für Sie geltenden Regeln
> und fragen Sie Ihren Anwalt, Datenschutzbeauftragten oder Zahlungsanbieter.

## Inhalt

1. [Warum ein Shop ein Ziel ist](#warum-ein-shop-ein-ziel-ist)
2. [Konten und Anmeldung](#konten-und-anmeldung)
3. [Shop-Berechtigungen](#shop-berechtigungen)
4. [Zahlungen](#zahlungen)
5. [Die Website und der Server](#die-website-und-der-server)
6. [Updates](#updates)
7. [Backups](#backups)
8. [Überwachen](#überwachen)
9. [Kundendaten](#kundendaten)
10. [Wenn etwas passiert](#wenn-etwas-passiert)

## Warum ein Shop ein Ziel ist

Ein Shop hält Namen, Anschriften und Bestellverläufe von Kunden und kann Preise und Bestellstatus ändern. Angreifer
suchen nach schwachen Administratorpasswörtern, veralteter Software und Zahlungsschritten, die sie umgehen können. Die
Sicht des Projekts auf die Bedrohungen steht unter [Sicherheitshärtung](../../sicherheitshaertung.md#wer-ein-cms-angreift-und-wie).

## Konten und Anmeldung

| Prüfung | Wer | Lesen Sie |
|---|---|---|
| Das anfängliche Administratorpasswort der Installation wurde geändert | Hoster oder Partner | [Das Administratorpasswort ändern](../../administratorpasswort-aendern.md) |
| Jede Person hat ihr eigenes Konto; niemand teilt eines | Inhaber | [Sicherheitshärtung](../../sicherheitshaertung.md#checkliste) |
| Für Administratoren und Shop-Mitarbeiter ist die zweistufige Anmeldung Pflicht | Hoster oder Partner | [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md), [sevenx_authentication_2fa](../../../funktionen/6.0/erweiterungen/sevenx_authentication_2fa.md) |
| Konten ausgeschiedener Personen werden am selben Tag deaktiviert | Inhaber | [Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md) |
| Die Administration läuft auf einem eigenen Host und ist eingeschränkt | Hoster | [Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md) |

## Shop-Berechtigungen

Die Funktionen des Shops sind `buy`, `administrate`, `setup`, `edit_status` und `setstatus` (`kernel/shop/module.php`).

- [ ] Besucher und Kunden haben nur `shop/buy`.
- [ ] Die Bestellbearbeitung hat `shop/administrate` und `shop/setstatus`, wo es passt beschränkt durch `FromStatus`
  und `ToStatus` ([Bestellungen und Bestellstatus](bestellungen-und-bestellstatus.md#wer-einen-status-ändern-darf)).
- [ ] Nur der Inhaber oder die Buchhaltung hat `shop/setup` (USt, Währungen, Rabatte).
- [ ] Redakteure, die Produkte pflegen, haben nur Inhaltsberechtigungen in den Produktordnern.
- [ ] Rollen werden zweimal im Jahr überprüft ([Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md)).

## Zahlungen

- [ ] An der Kasse gibt es einen Zahlungsschritt; das Shop-Dashboard warnt nicht vor einem fehlenden Zahlungsschritt
  ([Shop-Dashboard und Berichte](shop-uebersicht-und-berichte.md#nächste-schritte)).
- [ ] Wo möglich wird ein Anbieter mit Weiterleitung verwendet, sodass Kartendaten Ihren Server nie erreichen
  ([Kartenzahlungen und PCI-Grundlagen](kartenzahlungen-und-pci-grundlagen.md)).
- [ ] Mit ezauthorize: die Speicherung von Kartendaten vor dem Start von einem Entwickler geprüft (dieselbe Seite).
- [ ] Zahlungsbenachrichtigungen werden gegen Betrag und Währung geprüft, wie ezpaypal es tut
  ([Zahlungen mit PayPal](zahlungen-mit-paypal.md#was-die-erweiterung-prüft)).
- [ ] Testmodus und Sandbox-Adressen des Anbieters am Starttag entfernt.
- [ ] Geheimnisse (Schlüssel der Zahlungsanbindung, das Beleg-Geheimnis) liegen in Override-Dateien, nie in
  ausgelieferten Dateien oder in der Versionsverwaltung
  ([Umgebungseinstellungen und Geheimnisse](../../ersteller/anwendungsentwickler/umgebungseinstellungen-und-geheimnisse.md)).

## Die Website und der Server

Aus der Härtungs-Checkliste des Projekts ([Sicherheitshärtung](../../sicherheitshaertung.md#checkliste)):

- [ ] Überall HTTPS, mit HSTS und sicheren Cookies ([TLS und HTTPS](../../hosting/sicherheit/tls-und-https.md)).
- [ ] `ezformtoken` aktiv, sodass Formulare ein Token pro Sitzung gegen Cross-Site-Request-Forgery tragen.
- [ ] Content Security Policy durchgesetzt, zuerst in der Administration, dann auf den Websites
  ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).
- [ ] Debug-Ausgabe aus, und PHP-Fehler werden nicht angezeigt.
- [ ] Der Installationsassistent kann auf der installierten Website nicht laufen.
- [ ] Der Code ist für den Benutzer des Webservers nur lesbar.
- [ ] Ein request-shield oder Request-Regeln davor ([Request-Regeln](../../../funktionen/6.0/anfrageregeln.md)).

## Updates

- [ ] Jemand verfolgt die Releases von Exponential und spielt Sicherheitskorrekturen ein. Exponential 6.0 ist die
  gepflegte Linie, und Korrekturen erscheinen mit dem nächsten Release 6.0.x ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- [ ] Erweiterungen (auch Zahlungsanbindungen) werden mit dem System aktualisiert.
- [ ] Updates werden vor dem Live-Shop auf einer Kopie getestet ([Aktualisierungen ohne Überraschungen](../zuverlaessigkeit/aktualisierungen-ohne-ueberraschungen.md)).

## Backups

- [ ] Die Datenbank wird mindestens täglich gesichert; dort liegen die Bestellungen.
- [ ] Backups werden außerhalb des Servers und geschützt aufbewahrt, da sie Kundendaten enthalten.
- [ ] Eine Wiederherstellung wurde getestet ([Backups, die sich wiederherstellen lassen](../zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md),
  [Backups und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)).

## Überwachen

- [ ] Das Prüfprotokoll ist eingeschaltet (das ist es standardmäßig), und jemand liest es. Es zeichnet in seiner
  Kategorie `commerce` Bestellungen, Zahlungen (nie Kartendaten), USt, Währungen und Rabatte auf ([Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).
- [ ] Audit-Warnungen erreichen eine Person, und das Prüfprotokoll wird von der Maschine weg weitergeleitet (Härtungs-Checkliste).
- [ ] Die Logs werden wöchentlich gelesen ([Log-Viewer](../../../funktionen/6.0/log-betrachter.md)).

## Kundendaten

- [ ] Nur Mitarbeiter, die es brauchen, können die Kundenliste und die Bestellungen öffnen.
- [ ] Bestellunterlagen werden so lange aufbewahrt, wie Ihre Buchhaltung es verlangt, und nicht länger
  ([Aufbewahrung und Löschung von Daten](../sicherheit/aufbewahrung-und-loeschung-von-daten.md)).
- [ ] Werbemails nur mit Einwilligung ([Opt-in und Einwilligung](../newsletters/opt-in-und-einwilligung.md)).
- [ ] Ihr Datenschutzhinweis beschreibt, was der Shop speichert ([DSGVO-Grundlagen](../sicherheit/dsgvo-grundlagen.md)).

## Wenn etwas passiert

- Folgen Sie dem Runbook des Hosters für eine kompromittierte Website ([Runbook: kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md)).
- Melden Sie eine Schwachstelle in Exponential vertraulich an die Adresse in [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md),
  nicht in einem öffentlichen Issue.
- Informieren Sie sofort Ihren Zahlungsanbieter, wenn Zahlungsdaten betroffen sein könnten.
- Schreiben Sie auf, was passiert ist ([Störungsbehandlung für kleine Teams](../zuverlaessigkeit/stoerungsbehandlung-fuer-kleine-teams.md)).

## Verwandte Seiten

- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Sicherheitsgrundlagen für Unternehmen](../sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)
- [Checkliste für den Shop-Start](checkliste-fuer-den-shop-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
