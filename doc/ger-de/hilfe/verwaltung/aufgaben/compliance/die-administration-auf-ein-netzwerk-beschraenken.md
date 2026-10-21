---
title: Die Administration auf ein Netzwerk beschränken
audience: government, admin, hosting
category: public-sector
order: 140
summary: Schritt für Schritt - den Siteaccess der Administration einer Exponential-Site mit Apache Require ip oder nginx allow/deny auf Ihr Büronetz oder VPN beschränken, nachdem er einen eigenen Hostnamen erhalten hat, und von außen prüfen.
keywords: Administration, Admin-Host, IP-Beschränkung, Require ip, nginx allow deny, VPN, Netztrennung, HostMatchType, Velocity-Firewall, request-shield EXP-ADMIN-NET, öffentlicher Sektor, administration, admin host, IP restriction, network separation, Velocity firewall, public sector
type: help
---
# Die Administration auf ein Netzwerk beschränken

Für Administratoren und Hosting-Teams. Die Öffentlichkeit muss die Administration nicht erreichen. Wenn Sie sie auf Ihr
Netzwerk beschränken, fällt das meiste Erraten von Passwörtern aus dem Internet weg
([Die Administration trennen](../../../../../eng-us/help/government/security/separating-the-administration.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Administration hat einen eigenen Hostnamen (zum Beispiel `admin.example.gov`, zugeordnet über
  `[SiteAccessSettings] HostMatchType`); `/admin` unter dem öffentlichen Host zu beschränken ist schwerer richtig zu
  machen.
- Die Adressbereiche Ihrer Büros und Ihres VPN; Mitarbeiter unterwegs ohne VPN brauchen einen anderen Weg (VPN oder die
  Browserprüfung von request-shield statt einer Netzwerkregel).
- Ein Weg zurück hinein, falls Sie sich aussperren (Konsolenzugang zum Server).

## Schritte

**Apache**, im virtuellen Host der Administration:

```apache
<Location />
    Require ip 203.0.113.0/24 2001:db8::/48
</Location>
```

**nginx**, im Server-Block der Administration:

```nginx
allow 203.0.113.0/24;
allow 2001:db8::/48;
deny all;
```

**Velocity** hat hierfür weder HTTP-Authentifizierung noch Adressregeln; verwenden Sie die Firewall, ein VPN, einen
vorgeschalteten Reverse Proxy oder ein eigenes Velocity, das an eine interne Adresse gebunden ist
([13.5](../../../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess)).

Laden Sie den Webserver neu.

## Prüfen, ob es funktioniert hat

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://admin.example.gov/user/login
```

Aus einer Mobilfunkverbindung (außerhalb der Bereiche): `403` oder keine Verbindung. Aus dem Büro: `200`.

## Wenn etwas schiefgeht

- Hinter einem Proxy sieht der Webserver die Adresse des Proxys: Beschränken Sie am Proxy oder verwenden Sie die
  weitergeleitete Adresse nur von vertrauenswürdigen Proxys (Apache `mod_remoteip`, nginx `realip`).
- Ausgesperrt: Stellen Sie die vorherige Konfiguration über die Konsole des Servers wieder her.

## Verwandte Seiten

- [Die Administration trennen](../../../../../eng-us/help/government/security/separating-the-administration.md)
- [Die Administration beschränken (Hoster)](../../../hosting/sicherheit/die-administration-einschraenken.md)
- [request-shield vor einer öffentlichen Site](../../../../../eng-us/help/government/security/request-shield.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
