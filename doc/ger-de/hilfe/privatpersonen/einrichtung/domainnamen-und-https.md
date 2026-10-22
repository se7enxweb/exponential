---
title: Domainnamen und HTTPS
audience: individual, website-creator, customer
category: personal-sites
order: 1635
summary: Was eine Privatperson außerhalb von Exponential regelt, um einer Website eine eigene Adresse und das Schloss im Browser zu geben - eine Domain bei einem Registrar kaufen, DNS auf den Server richten, Exponential seine Adresse mitteilen, ein Zertifikat vom Hoster oder über die Let's-Encrypt-Unterstützung von Velocity beziehen, E-Mail-Einträge und Verlängerungen.
keywords: Domainname, Registrar, DNS, A-Eintrag, AAAA-Eintrag, CNAME, Nameserver, HTTPS, TLS, Zertifikat, Let's Encrypt, Velocity, SiteURL, Siteaccess, Host, E-Mail, SPF, Verlängerung, Privatperson, domain name, A record, certificate, renewal
type: help
---
# Domainnamen und HTTPS

Diese Seite ist für Privatpersonen, die ihre Website unter einer eigenen Adresse haben möchten, mit dem Schloss im
Browser.

> **Hinweis**
> Domainnamen und DNS liegen **außerhalb von Exponential**. Exponential kann keine Domain kaufen, registrieren oder
> verlängern und verwaltet kein DNS. Sie regeln das mit einem Registrar (und für DNS mit dem, der Ihr DNS hostet).
> Exponential muss nur die Adresse mitgeteilt bekommen, unter der es erreichbar ist.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Eine Domain wählen und kaufen](#eine-domain-wählen-und-kaufen)
3. [Die Domain auf Ihre Website richten](#die-domain-auf-ihre-website-richten)
4. [Exponential seine Adresse mitteilen](#exponential-seine-adresse-mitteilen)
5. [HTTPS](#https)
6. [E-Mail für Ihre Domain](#e-mail-für-ihre-domain)
7. [Verlängerungen](#verlängerungen)
8. [Checkliste](#checkliste)

## Die Bausteine

| Baustein | Wer ihn bereitstellt | Anteil von Exponential |
|---|---|---|
| Der Domainname | Ein Registrar, jährlich gemietet | Keiner |
| DNS-Einträge (wohin der Name zeigt) | Der Registrar, der Hoster oder ein DNS-Dienst | Keiner |
| Das Zertifikat für HTTPS | Der Hoster oder Velocity mit Let's Encrypt | Velocity kann es beziehen und verlängern |
| Die Adresse der Website in den Einstellungen | Sie | `SiteURL` und Host-Einstellungen des Siteaccess |

## Eine Domain wählen und kaufen

- Kurz, leicht zu schreiben und laut auszusprechen.
- Kaufen Sie sie auf Ihren eigenen Namen, mit einer Adresse, die Sie in zehn Jahren noch lesen; für einen Verein auf
  den Namen des Vereins.
- Schützen Sie das Konto beim Registrar: ein starkes Passwort und, falls angeboten, zweistufige Anmeldung. Wer die
  Domain verliert, verliert die Website und jede E-Mail-Adresse darauf.
- Viele Registrare bieten Schutz der Kontaktdaten des Inhabers an; erwägen Sie das für eine persönliche oder eine
  Familienseite.

## Die Domain auf Ihre Website richten

Setzen Sie beim Registrar oder DNS-Dienst:

| Eintrag | Zeigt auf | Für |
|---|---|---|
| `A` | Die IPv4-Adresse Ihres Servers oder Hostings | `example.com` |
| `AAAA` | Die IPv6-Adresse, wenn der Hoster eine hat | `example.com` |
| `CNAME` oder ein weiteres `A` | Denselben Server | `www.example.com` |

Bei Shared Hosting sagt Ihnen der Hoster oft, dass Sie stattdessen seine Nameserver verwenden sollen; dann setzt er
die Einträge. Änderungen können eine Weile brauchen, bis sie überall sichtbar sind.

Entscheiden Sie, welcher Name der Hauptname ist (`www.example.com` oder `example.com`), und leiten Sie den anderen
darauf weiter.

## Exponential seine Adresse mitteilen

Exponential bildet Links, E-Mails und Feeds aus seiner eingestellten Adresse. Wenn Sie online gehen, ordnen Sie die
Hostnamen Siteaccesses zu und setzen die Website-URL
([Livegang: Hosts, HTTPS und Siteaccesses](../../ersteller/website-ersteller/livegang-hosts-und-https.md)). Wenn Sie mit
`exp:install` installiert haben, setzen die Optionen `--url`, `--access=host`, `--host` und `--admin-host` das von
Anfang an ([Eine Installationsmethode wählen](eine-installationsmethode-waehlen.md#optionen-die-für-eine-echte-website-zählen)).

Eine eigene Adresse für die Administration (zum Beispiel `admin.example.com`) hält das Bearbeiten von der
öffentlichen Website getrennt.

## HTTPS

Jede Website mit Anmeldung braucht HTTPS; heute sollte jede Website es haben.

| Ausgeliefert durch | Zertifikat |
|---|---|
| Shared Hosting | Meist vom Hoster enthalten, oft von Let's Encrypt; im Panel einschalten |
| Apache oder nginx auf Ihrem Server | Ein Zertifikatswerkzeug auf dem Server oder eines von Ihrem Anbieter |
| Velocity | Velocity bezieht selbst Zertifikate von Let's Encrypt, wenn Port 80 erreichbar ist und DNS auf den Server zeigt, verlängert sie im Hintergrund und liefert ein selbst signiertes Zertifikat aus, bis ein echtes nutzbar ist ([Velocity: HTTPS und Zertifikate](../../../funktionen/6.0/velocity-https-zertifikate.md)) |

Sobald HTTPS funktioniert, verwenden Sie nur noch ein Schema: Leiten Sie `http://` auf `https://` weiter und setzen
Sie die Website-URL mit `https://` ([Livegang, HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md#https)).

## E-Mail für Ihre Domain

Wenn die Website E-Mails von Ihrer Domain verschickt (Registrierung, Zurücksetzen des Passworts, Newsletter), prüfen
empfangende Server DNS-Einträge wie SPF und DKIM für Ihre Domain. Ihr Hoster oder E-Mail-Anbieter sagt Ihnen, welche
Einträge Sie hinzufügen müssen. Ohne sie landen die E-Mails Ihrer Website oft im Spam
([E-Mail-Zustellung von Newslettern](../../unternehmen/newsletters/zustellung-der-newsletter-mails.md)).

## Verlängerungen

- Domains laufen ab. Schalten Sie die automatische Verlängerung ein und halten Sie das Zahlungsmittel aktuell.
- Zertifikate von Let's Encrypt sind kurzlebig; Ihr Hoster oder Velocity verlängert sie. Prüfen Sie einmal im Monat
  das Schloss.
- Tragen Sie beide Termine in Ihren Kalender ein ([Mit kleinem Budget betreiben](mit-kleinem-budget-betreiben.md#ihr-arbeitsblatt-für-die-jährlichen-kosten)).

## Checkliste

- [ ] Domain auf den richtigen Namen gekauft, Konto beim Registrar geschützt.
- [ ] `A`- (und `AAAA`-)Einträge für den Hauptnamen und `www`.
- [ ] Ein Hauptname; der andere leitet weiter.
- [ ] Die Host-Einstellungen und die Website-URL von Exponential verwenden die echte Adresse.
- [ ] HTTPS funktioniert auf jeder Seite; `http://` wird weitergeleitet.
- [ ] E-Mail-Einträge hinzugefügt, wenn die Website E-Mails von Ihrer Domain verschickt.
- [ ] Verlängerungen automatisch und im Kalender.

## Verwandte Seiten

- [Einrichtung für Privatpersonen](README.md)
- [Livegang: Hosts, HTTPS und Siteaccesses](../../ersteller/website-ersteller/livegang-hosts-und-https.md)
- [Startcheckliste für Privatpersonen](../checklisten/startcheckliste-fuer-privatpersonen.md)
- [Velocity: HTTPS und Zertifikate](../../../funktionen/6.0/velocity-https-zertifikate.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
