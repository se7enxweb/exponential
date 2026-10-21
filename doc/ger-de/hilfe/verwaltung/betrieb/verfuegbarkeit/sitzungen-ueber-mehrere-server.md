---
title: Sitzungen über mehrere Server
audience: government, hosting, admin
category: public-sector
order: 110
summary: Für IT-Teams, die eine Exponential-Website auf mehreren Webservern betreiben. Wo Sitzungen gespeichert werden (PHP-Dateien oder die Datenbank), wie Redakteurinnen und Redakteure angemeldet bleiben, wenn ein Load Balancer sie zwischen Servern verschiebt, was ein ausgefallener Knoten für angemeldete Beschäftigte bedeutet und welche Sitzungseinstellungen wichtig sind.
keywords: Sitzungen, Session-Handler, ezpSessionHandlerDB, ezpSessionHandlerPHP, Sticky Sessions, Sitzungsaffinität, CookieAlwaysAddToHttpResponse, SessionTimeout, Setup > Sitzungen, mehrere Webserver, angemeldete Redakteure, sessions, session handler, sticky sessions, session affinity, Setup > Sitzungen, several web servers, signed-in editors
type: help
---
# Sitzungen über mehrere Server

Diese Seite richtet sich an Infrastrukturteams in öffentlichen Stellen, die eine Exponential-Website auf mehr als
einem Webserver betreiben. Sie beantwortet, wo Sitzungen gespeichert werden, welche zwei Wege es gibt, angemeldete
Redakteurinnen und Redakteure über Server hinweg angemeldet zu halten, was mit ihnen geschieht, wenn ein Server
ausfällt, und welche Sitzungseinstellungen wichtig sind. Die Referenzen sind
[Sitzungen](../../../../anleitungen/sitzungen.md) und [Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md).

## Inhalt

1. [Wo Sitzungen gespeichert werden](#wo-sitzungen-gespeichert-werden)
2. [Zwei Wege für den Betrieb über mehrere Server](#zwei-wege-für-den-betrieb-über-mehrere-server)
3. [Was ein ausgefallener Server bedeutet](#was-ein-ausgefallener-server-bedeutet)
4. [Wichtige Einstellungen](#wichtige-einstellungen)
5. [Checkliste](#checkliste)

## Wo Sitzungen gespeichert werden

Eine Sitzung hält einen Browser angemeldet. `site.ini [Session] Handler` entscheidet, wo sie gespeichert wird:

| Handler | Wo | Mehrere Server |
|---|---|---|
| leer (Standard): `ezpSessionHandlerPHP` | der eigene Sitzungsspeicher von PHP, meist Dateien auf dem Server | jeder Server kennt nur seine eigenen Sitzungen |
| `ezpSessionHandlerDB` | die Tabelle `ezsession` der Datenbank der Website | jeder Server sieht jede Sitzung |

Anonyme Besucher haben meist gar keine Sitzung; das Thema betrifft Redakteurinnen und Redakteure und andere
angemeldete Benutzer.

## Zwei Wege für den Betrieb über mehrere Server

**Sitzungen in der Datenbank.** Setzen Sie den Handler in `settings/override/site.ini.append.php`:

```ini
[Session]
Handler=ezpSessionHandlerDB
```

Jeder Knoten liest dieselbe Tabelle, sodass der Load Balancer einen Benutzer an jeden Knoten schicken darf. Ein
weiterer Vorteil für öffentliche Stellen: **Setup > Sitzungen** kann dann Sitzungen auflisten und entfernen, zum
Beispiel um ein vermutlich kompromittiertes Konto sofort abzumelden ([Sitzungen](../../../../anleitungen/sitzungen.md)). Mit
dem Standard-Handler kann diese Seite Sitzungen nicht einzeln auflisten oder entfernen; sie zeigt stattdessen, wer
sich kürzlich angemeldet hat.

**Jeden Benutzer auf einem Knoten halten.** Ein Load Balancer, der anhand des Sitzungscookies weiterleitet, hält
einen Benutzer auf dem Knoten, der die Sitzung hat. `[Session] CookieAlwaysAddToHttpResponse=enabled` sendet für einen
solchen Load Balancer das Sitzungscookie mit jeder Antwort, sobald eine Sitzung läuft; diese Antworten gehen mit
`Cache-Control: private` hinaus ([Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)).

| | Sitzungen in der Datenbank | Sitzungsaffinität |
|---|---|---|
| Konfiguration des Load Balancers | keine | Weiterleitung anhand des Cookies |
| Ein Knoten fällt aus | Benutzer bleiben angemeldet | seine Benutzer werden abgemeldet |
| Setup > Sitzungen listet und entfernt Sitzungen | ja | nein |
| Last auf der Datenbank | eine weitere Tabelle in Gebrauch | keine zusätzliche |

## Was ein ausgefallener Server bedeutet

Mit Affinität meldet ein ausgefallener Knoten seine Benutzer ab: Redakteurinnen und Redakteure verlieren die
ungespeicherten Formulareingaben des Augenblicks, aber gespeicherte Entwürfe bleiben in der Datenbank erhalten
([Meine Entwürfe und meine ausstehenden Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)). Mit Sitzungen in der
Datenbank geht die nächste Anfrage an einen anderen Knoten, und der Benutzer arbeitet weiter. Wählen Sie für eine
Website, auf der die Redaktion während eines Vorfalls weiterarbeiten muss, den Datenbank-Handler.

## Wichtige Einstellungen

| Einstellung (`site.ini [Session]`) | Standard | Bedeutung |
|---|---|---|
| `SessionTimeout` | `259200` (drei Tage) | eine Sitzung endet so viele Sekunden nach ihrer letzten Nutzung |
| `CookieSecure` | `auto` | `Secure`-Cookie über HTTPS; hinter einem Load Balancer, der TLS terminiert, braucht es `X-Forwarded-Proto` von einem vertrauenswürdigen Proxy |
| `CookieHttponly` | `true` | das Cookie ist für Skripte nicht lesbar |
| `CookieSameSite` | `Lax` | wird bei Anfragen von fremden Websites nicht mitgesendet |

Ein kürzeres `SessionTimeout` für die Administration kann eine Anforderung Ihrer Sicherheitsrichtlinie sein; setzen
Sie es in den Einstellungen des Administrations-Siteaccess. Eine Abmeldung beendet die Sitzung unter jedem Handler
endgültig, und eine Passwortänderung beendet andere Sitzungen (`[PasswordSettings] EndOtherSessions=enabled`)
([Sicherheitsstandards](../../../../../eng-us/standards/security.md)).

## Checkliste

- [ ] Bei mehreren Servern: `ezpSessionHandlerDB` oder eine mit dem Netzwerkteam vereinbarte Sitzungsaffinität.
- [ ] Die Abwägung für einen ausgefallenen Knoten ist dokumentiert.
- [ ] `TrustedProxies[]` führt den Load Balancer auf, damit das `Secure`-Flag hinter der TLS-Terminierung stimmt.
- [ ] `SessionTimeout` entspricht Ihrer Sicherheitsrichtlinie.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Mehrere Webserver hinter einem Load Balancer](mehrere-webserver-hinter-einem-load-balancer.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
