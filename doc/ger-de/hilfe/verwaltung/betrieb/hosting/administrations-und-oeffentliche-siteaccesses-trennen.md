---
title: Die Administration von der öffentlichen Website trennen
audience: government, hosting, admin
category: public-sector
order: 90
summary: Für IT-Teams, die die Bearbeitungsoberfläche einer Website des öffentlichen Sektors vom öffentlichen Internet fernhalten müssen. Wie Siteaccesses die Administration von der öffentlichen Website trennen, wie Sie ihr einen eigenen Hostnamen geben, sie auf Ihr Netzwerk beschränken und begrenzen, wer sich wo anmelden darf.
keywords: Siteaccess, Administration, Administrations-Siteaccess, Redaktions-Siteaccess, Hostname, HostMatchType, HostMatchMapItems, internes Netzwerk, VPN, Require ip, RequireUserLogin, user/login, Einschränkung auf Siteaccess, Intranet, siteaccess, administration, admin siteaccess, editor siteaccess, host name, internal network, SiteAccess limitation, intranet
type: help
---
# Die Administration von der öffentlichen Website trennen

Diese Seite richtet sich an IT- und Sicherheitsteams in öffentlichen Stellen, deren Richtlinie vorschreibt, dass
Bearbeitungsoberflächen nicht aus dem öffentlichen Internet erreichbar sein dürfen oder nur über das Netzwerk der
Organisation. Sie beantwortet, wie Exponential die Administration von der öffentlichen Website trennt, wie Sie der
Administration einen eigenen Hostnamen und eine Netzwerkbeschränkung geben und wie Rollen entscheiden, wer sich wo
anmelden darf. Die Referenzen sind
[13.5 Der Siteaccess der Administration](../../../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess),
[10.2 Siteaccesses und Adressen der Website](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)
und die Hosting-Seite [Die Administration beschränken](../../../hosting/sicherheit/die-administration-einschraenken.md).

## Inhalt

1. [Siteaccesses in einem Absatz](#siteaccesses-in-einem-absatz)
2. [Der Administration einen eigenen Hostnamen geben](#der-administration-einen-eigenen-hostnamen-geben)
3. [Sie auf Ihr Netzwerk beschränken](#sie-auf-ihr-netzwerk-beschränken)
4. [Entscheiden, wer sich wo anmelden darf](#entscheiden-wer-sich-wo-anmelden-darf)
5. [Hinter einem zentralen Proxy](#hinter-einem-zentralen-proxy)
6. [Prüfen](#prüfen)
7. [Checkliste](#checkliste)

## Siteaccesses in einem Absatz

Ein Siteaccess ist ein Zugang zur Installation: ein Name, seine Einstellungen in `settings/siteaccess/<name>/` und eine
Regel, auf welche Anfragen er antwortet. Das Installationsprogramm legt mindestens den öffentlichen Siteaccess und die
Administration an (meist `admin`) und schreibt für die Administration `[SiteAccessSettings] RequireUserLogin=true`,
sodass jede ihrer Seiten eine Anmeldung verlangt. Beide teilen dieselbe Datenbank; bei der Trennung geht es um
Adressen, Netzwerke und Berechtigungen, nicht um Daten.

## Der Administration einen eigenen Hostnamen geben

Das Installationsprogramm bietet drei Aufteilungen an: URL (`/admin`), Host (`admin.example.gov`) und Port. Für die
Produktion ist die Aufteilung nach Host die richtige: Die Administration lässt sich dann als Einheit beschränken, mit
eigenem virtuellem Host, eigenem Zertifikat und eigener Firewall-Regel
([10.2](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.gov;site
HostMatchMapItems[]=admin.example.gov;admin
```

Setzen Sie `[SiteSettings] SiteURL` in jedem Siteaccess auf dessen Hostnamen, tragen Sie die Namen im DNS und im
Webserver ein und leeren Sie den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Viele öffentliche Stellen veröffentlichen den Host der Administration nur im internen DNS. Das hält ihn aus der
öffentlichen Sicht heraus, ist aber als Ergänzung zur Netzwerkbeschränkung zu sehen, nicht als Ersatz.

## Sie auf Ihr Netzwerk beschränken

Von der stärksten bis zur einfachsten Möglichkeit ([Die Administration beschränken](../../../hosting/sicherheit/die-administration-einschraenken.md)):

| Server | Wie |
|---|---|
| Apache | auf dem virtuellen Host der Administration `Require ip` mit Ihren Netzwerken oder HTTP-Basisauthentifizierung |
| nginx | `allow` und `deny` im Server-Block der Administration |
| Velocity | keine eigene HTTP-Authentifizierung: die Firewall, ein VPN, ein vorgeschalteter Reverse Proxy oder ein separates Velocity, das an eine interne Adresse gebunden ist |

```apache
<Location />
    Require ip 203.0.113.0/24 2001:db8::/48
</Location>
```

Die Adressen oben sind Beispiele für Dokumentationszwecke; verwenden Sie die Netzwerke Ihrer Organisation.
Redakteurinnen und Redakteure, die von zu Hause arbeiten, erreichen die Administration dann über Ihr VPN.

## Entscheiden, wer sich wo anmelden darf

Die Anmeldung ist selbst eine Berechtigung: Die Richtlinie `user/login` lässt sich auf Siteaccesses einschränken.
Eine Rollenseite zeigt sie als Satz wie *Darf sich anmelden, in den Siteaccesses site und bold*
([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)). Nutzen Sie das, um sicherzustellen, dass:

- öffentliche Konten (zum Beispiel von Personen, die sich für einen Newsletter registriert haben) sich nur im
  öffentlichen Siteaccess anmelden dürfen;
- Rollen der Beschäftigten sich in der Administration anmelden dürfen;
- externe Dienstleister die Administration nur so lange erhalten, wie ihr Vertrag läuft (siehe die Seiten zu Personen
  und Berechtigungen in diesem Bereich, zum Beispiel
  [Rollenentwurf für externe Dienstleister](../personen-und-berechtigungen/rollenentwurf-externe-dienstleister.md)).

Exponential hat außerdem einen **Redaktions-Siteaccess**, eine vereinfachte Bearbeitungsoberfläche für die Redaktion
([Einrichtungsassistent und Redaktions-Siteaccess](../../../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md)). Er
verweigert das Rollenmodul, sodass Rollen immer im Siteaccess der Administration verwaltet werden. Geben Sie ihm
dieselbe Netzwerkbeschränkung wie der Administration.

## Hinter einem zentralen Proxy

Wenn ein zentraler Reverse Proxy oder Load Balancer TLS terminiert, vertraut Exponential weitergeleiteten Headern nur
von den Proxys, die in `site.ini [HTTPHeaderSettings] TrustedProxies[]` aufgeführt sind. Ohne diese Einstellung sehen
HTTPS-Anfragen wie reines HTTP aus, und jeder Besucher hat die Adresse des Proxys, was netzwerkbasierte Regeln und die
Adressen im Audit-Trail unbrauchbar macht. Siehe
[Mehrere Webserver hinter einem Load Balancer](../verfuegbarkeit/mehrere-webserver-hinter-einem-load-balancer.md).

## Prüfen

```bash
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' https://admin.example.gov/
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/
```

Aus Ihrem Netzwerk heraus erwarten Sie vom Host der Administration eine Weiterleitung zur Anmeldung und vom
öffentlichen Host `200`. Von außen darf der Host der Administration gar nicht antworten oder nur mit 403.

## Checkliste

- [ ] Die Administration hat einen eigenen Hostnamen, der über `HostMatchMapItems[]` zugeordnet wird.
- [ ] Webserver, Firewall oder Proxy beschränken diesen Host auf Ihre Netzwerke oder Ihr VPN.
- [ ] `RequireUserLogin=true` ist für den Siteaccess der Administration gesetzt.
- [ ] Richtlinien für `user/login` beschränken öffentliche Konten auf den öffentlichen Siteaccess.
- [ ] `TrustedProxies[]` führt den zentralen Proxy auf, falls es einen gibt.
- [ ] Ein Test von außerhalb Ihres Netzwerks zeigt keine Administration.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Den Host härten](den-host-haerten.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
