---
title: Sicherheitspflichten von Wiederverkäufern
audience: reseller, partner, hosting
category: reselling
order: 420
summary: Wofür ein Wiederverkäufer bei der Sicherheit der Exponential-Websites, die er verkauft, verantwortlich ist - die geteilte Verantwortung mit dem Projekt, Hostern und Kunden, eine Sicherheits-Checkliste je Website, Zugangsdaten, Updates, vertrauliche Meldung an security@se7enx.com, Demo-Websites und ein kurzer Notfallplan.
keywords: Sicherheit, Verantwortung, geteilte Verantwortung, Härtung, Updates, Zugangsdaten, Passwörter, Anmeldung in zwei Schritten, Content Security Policy, Audit-Protokoll, Sicherheitslücke, security@se7enx.com, Vorfall, Demomodus, security, responsibilities, shared responsibility, hardening, credentials, passwords, two-step sign-in, audit trail, vulnerability, incident, demo mode
type: help
---
# Sicherheitspflichten von Wiederverkäufern

Diese Seite ist für Inhaber und technische Leiter bei Wiederverkäufern. Wenn Sie eine Website, einen Tarif oder ein
Produkt verkaufen, vertraut der Kunde **Ihnen** seine Sicherheit an, welche Rolle das Projekt, ein Hoster oder 7x
hinter Ihnen auch spielt. Die Fassung für Partner ist
[Sicherheitspflichten von Partnern](../../partner/sicherheitspflichten.md).

## Inhalt

1. [Geteilte Verantwortung](#geteilte-verantwortung)
2. [Checkliste je Website](#checkliste-je-website)
3. [Zugangsdaten](#zugangsdaten)
4. [Sicherheitslücken und Updates](#sicherheitslücken-und-updates)
5. [Vorfälle](#vorfälle)
6. [Demo- und Schulungs-Websites](#demo--und-schulungs-websites)

## Geteilte Verantwortung

| Wer | Verantwortlich für |
|---|---|
| Das Projekt | Sicheren Code im Kernel und in seinen Erweiterungen; in Releases veröffentlichte Fehlerbehebungen (`SECURITY.md`) |
| Der Hoster (Sie oder Ihr Lieferant) | Server, PHP, Datenbank, TLS, Netzwerk, Datensicherungen |
| Sie als Wiederverkäufer | Konfiguration, gewählte Erweiterungen, eigenen Code, eingespielte Updates, Überwachung, Reaktion auf Vorfälle, wie Ihr Vertrag es festlegt |
| Der Kunde | Konten und Passwörter seiner Redakteure, die Inhalte, wem er Zugang gibt |

Schreiben Sie diese Aufteilung in jeden Vertrag ([Verträge mit Kunden](../kunden/vertraege-mit-kunden.md)). Wenn
Sie Hosting oder White-Label-Dienstleistungen einkaufen, bleiben Sie gegenüber dem Kunden verantwortlich.

## Checkliste je Website

- [ ] Gehärtet wie in [Sicherheitshärtung](../../sicherheitshaertung.md) und Kapitel 13 des Installationshandbuchs
      ([Kapitel Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md)).
- [ ] Standard- und gemeinsam genutzte Passwörter geändert ([Standardpasswort des Administrators](../../standard-administratorpasswort.md)).
- [ ] Setup-Assistent und Installer-Dateien nicht erreichbar.
- [ ] TLS überall ([TLS und HTTPS](../../hosting/sicherheit/tls-und-https.md)); die Content Security Policy der
      Administration nicht abgeschwächt ([Content Security Policy](../../hosting/sicherheit/content-security-policy.md)).
- [ ] Debug-Ausgabe in der Produktion aus.
- [ ] Anmeldung in zwei Schritten für Administratoren angeboten ([Anmeldung in zwei Schritten](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Die Administration, wo möglich, eingeschränkt ([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md)).
- [ ] Audit-Protokoll eingeschaltet und geprüft ([Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md)).
- [ ] Eigener Code auf SQL-Injection, XSS und CSRF geprüft.
- [ ] Updates nach Plan eingespielt ([Kunden aktuell halten](../kunden/kunden-aktuell-halten.md)).
- [ ] Datensicherungen durch eine Wiederherstellung getestet.
- [ ] Jeder Kunde von den anderen isoliert ([Mandantenisolation](../../hosting/sicherheit/trennung-von-mandanten.md)).

## Zugangsdaten

- Nie in Tickets, Chats, E-Mails oder Git-Repositorys.
- Übergeben Sie Kunden Zugangsdaten über einen sicheren Kanal; lassen Sie sie die Passwörter bei der ersten
  Anmeldung ändern.
- Persönliche Konten für Ihr Personal, nie ein gemeinsames Administratorkonto.
- Entfernen Sie die Konten ausscheidender Mitarbeiter, auf Ihrer Seite und in der Administration jedes Kunden.

## Sicherheitslücken und Updates

- Eine Sicherheitslücke in Exponential wird vertraulich an **security@se7enx.com** gemeldet; „You can expect a first
  answer within one business day“ (Sie können eine erste Antwort innerhalb eines Werktags erwarten, `SECURITY.md`).
  Melden Sie sie nie öffentlich ([Eskalation für Wiederverkäufer](../kunden/eskalation.md#sicherheitslücken)).
- Fehlerbehebungen werden auf `main` vorgenommen und mit dem nächsten Release 6.0.x veröffentlicht; „a published tag
  is never changed“ (ein veröffentlichter Tag wird nie geändert). Ihre Kunden sind erst geschützt, wenn Sie das
  Release einspielen.
- Versprechen Sie eine Frist für das Einspielen von Sicherheits-Releases erst, nachdem Sie gemessen haben, wie lange
  ein getestetes Update über alle Ihre Websites dauert ([Service-Levels gestalten](../angebote/service-levels-gestalten.md#sicherheitskorrekturen)).

## Vorfälle

Halten Sie für jeden Kunden einen kurzen Plan bereit:

1. **Eindämmen**: Nehmen Sie den betroffenen Teil vom Netz oder blockieren Sie den Angriffsweg (der Wartungsmodus
   hilft: [Wartungsmodus](../../../anleitungen/wartungsmodus.md)).
2. **Sichern**: Bewahren Sie Logs und Kopien für die Analyse auf ([Sicherheitslog](../../hosting/betrieb/sicherheitslog.md)).
3. **Informieren**: den Kunden sofort; Behörden, wenn das Gesetz es verlangt ([Datenschutz für Wiederverkäufer](datenschutz.md)).
4. **Beheben**: Spielen Sie die Fehlerbehebung ein, tauschen Sie Zugangsdaten aus, stellen Sie saubere
   Datensicherungen wieder her.
5. **Lernen**: Schreiben Sie auf, was geschehen ist, und ändern Sie den Prozess.

Das Runbook Schritt für Schritt ist [Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md).

## Demo- und Schulungs-Websites

Eine öffentliche Demo läuft im Demomodus (`EXP_DEMO_MODE=1`), der nur die Passwörter von Administratoren schützt:
Ein als Administrator angemeldeter Besucher kann weiterhin Einstellungen ändern und Dateien hochladen. Halten Sie
Demos auf einem eigenen Systembenutzer, Pool und einer eigenen Datenbank, nie mit echten Daten, und stellen Sie
sicher, dass die Variable nie einen Produktivprozess erreicht ([Demo-Websites für Wiederverkäufer](../angebote/demo-websites.md)).

## Verwandte Seiten

- [Sicherheitspflichten von Partnern](../../partner/sicherheitspflichten.md)
- [Härtung für Hoster](../../hosting/sicherheit/haertung-fuer-hoster.md)
- [Checkliste für die Sicherheitsüberprüfung](../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
