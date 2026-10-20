---
title: Die Administration abtrennen
audience: government, admin, hosting
category: public-sector
order: 330
summary: Für öffentliche Stellen, die die Bearbeitungsoberfläche außer Reichweite der Öffentlichkeit halten wollen - dem Administrations-Siteaccess einen eigenen Host geben, ihn per Netzwerk, VPN oder zweiter Authentifizierung einschränken, Anfrageregeln für System-URLs und was Velocity braucht.
keywords: Administrations-Siteaccess, Administrationshost, Netzwerktrennung, IP-Beschränkung, VPN, Basic-Authentifizierung, Anfrageregeln, Angriffsfläche, öffentlicher Sektor, administration siteaccess, admin host, network separation, IP restriction, basic authentication, Require ip, nginx allow deny, request rules, RequireUserLogin, CheckValidity, Velocity, attack surface, public sector
type: help
---
# Die Administration abtrennen

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams. In der Administration melden
sich Konten mit weitreichenden Rechten an; die Öffentlichkeit muss sie nicht erreichen. Die Administration von
Exponential ist ein eigener Siteaccess, wodurch sie sich als Einheit einschränken lässt
([13.5 Der Administrations-Siteaccess](../../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess)).

## Inhalt

1. [Was bereits vorhanden ist](#was-bereits-vorhanden-ist)
2. [Möglichkeiten, die stärkste zuerst](#möglichkeiten-die-stärkste-zuerst)
3. [System-URLs von der öffentlichen Website fernhalten](#system-urls-von-der-öffentlichen-website-fernhalten)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Was bereits vorhanden ist

- Das Installationsprogramm schreibt `[SiteAccessSettings] RequireUserLogin=true` für den Administrations-Siteaccess:
  Jede Seite verlangt eine Anmeldung.
- Über den Zugriff auf jede Funktion entscheiden Rollen (`setup/*`, `content/*`, `audit/*` ...).
- Der Einrichtungsassistent ist auf einer installierten Website geschlossen.

## Möglichkeiten, die stärkste zuerst

1. **Ein eigener Hostname**, zum Beispiel `admin.example.gov`, zugeordnet über `[SiteAccessSettings] HostMatchType`,
   statt `/admin` unterhalb der öffentlichen Website.
2. **Das Netzwerk einschränken** auf diesem Host. Apache:

   ```apache
   <Location />
       Require ip 203.0.113.0/24 2001:db8::/48
   </Location>
   ```

   oder eine vorgeschaltete HTTP-Basic-Authentifizierung; nginx: `allow` und `deny`.
3. **Ein VPN** für Beschäftigte unterwegs, wobei die Administration nur darüber erreichbar ist.
4. **request-shield** mit der Browserprüfung vor der Anmeldung und optional nur dem Büronetz (`EXP-ADMIN-NET` in den
   Beispielregeln).
5. **Velocity** hat keine eigene HTTP-Authentifizierung: Beschränken Sie den Administrationshost mit der Firewall, einem
   VPN oder einem vorgeschalteten Reverse Proxy, oder betreiben Sie ein separates Velocity, das an eine interne Adresse
   gebunden ist.

Kombinieren Sie das mit der zweistufigen Anmeldung: Die Netzwerkbeschränkung hält Fremde fern, die zweistufige
Anmeldung schützt von innen gegen gestohlene Passwörter.

## System-URLs von der öffentlichen Website fernhalten

Die Anfrageregeln (`settings/requestrules.ini`) entscheiden, bevor eine Modulansicht läuft: Halten Sie anonyme Besucher
von System-URLs wie `/content/view/full/123` fern, verlangen Sie für eine Sektion eine Anmeldung, weisen Sie Anfragen
aus einem Netz ab ([Anfrageregeln](../../../../eng-us/bc/6.0/view_full_security.md)). Die Beispielregeln von request-shield
beantworten auf den öffentlichen Siteaccesses außerdem Administrationsmodule (`setup`, `class`, `role`, `workflow`,
`package`) mit 404.

## Prüfen

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://admin.example.gov/user/login    # from outside: refused (403) or unreachable
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/setup/info     # public siteaccess: no admin module
```

Testen Sie aus einem Netz außerhalb des erlaubten Bereichs, zum Beispiel über eine Mobilfunkverbindung.

## Verwandte Seiten

- [13.5 Der Administrations-Siteaccess](../../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess)
- [Die Administration einschränken (Hoster)](../../hosting/sicherheit/die-administration-einschraenken.md)
- [request-shield vor einer öffentlichen Website](request-shield.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
