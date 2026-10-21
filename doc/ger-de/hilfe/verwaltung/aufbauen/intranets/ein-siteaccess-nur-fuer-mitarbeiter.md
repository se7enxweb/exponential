---
title: Ein Siteaccess nur für Mitarbeiter
audience: government, admin
category: public-sector
order: 30
summary: Wie Sie in Exponential einen Intranet-Siteaccess einrichten, der für jede Seite eine Anmeldung verlangt (RequireUserLogin), mit eigenem Wurzelordner, Design und eigener Adresse, welche Views offen bleiben und wie Sie ihn aus Suchmaschinen heraushalten.
keywords: Intranet-Siteaccess, nur für Mitarbeiter, Anmeldung erforderlich, Wurzelknoten, interner Host, robots, intranet siteaccess, RequireUserLogin, AnonymousAccessList, staff only, sign-in required, root node, internal host, siteaccess
type: help
---
# Ein Siteaccess nur für Mitarbeiter

Diese Seite richtet sich an Administratoren. Ein ganzer Siteaccess kann eine Anmeldung verlangen, was zu einem
Intranet passt
([Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md#ein-bereich-nur-für-mitglieder)).

## Inhalt

1. [Die Einstellungen](#die-einstellungen)
2. [Views, die offen bleiben](#views-die-offen-bleiben)
3. [Adresse und Netz](#adresse-und-netz)
4. [Aus Suchmaschinen heraushalten](#aus-suchmaschinen-heraushalten)
5. [Prüfungen](#prüfungen)

## Die Einstellungen

Richten Sie den Siteaccess wie eine Abteilungs-Site ein (siehe „Eine Abteilungs-Site hinzufügen“ im Bereich
Multisite), mit Wurzelordner und Design, und fügen Sie hinzu:

```ini
# settings/siteaccess/intranet/site.ini.append.php
[SiteAccessSettings]
RequireUserLogin=true
```

Mit `RequireUserLogin=true` wird ein nicht angemeldeter Besucher vor jeder Seite zur Anmeldung aufgefordert. Prüfen Sie
den Wert, den die globalen Einstellungen und die öffentlichen Siteaccesses Ihrer Installation verwenden, damit nur das
Intranet die Anmeldung verlangt.

## Views, die offen bleiben

`[SiteAccessSettings] AnonymousAccessList[]` listet die Module oder Views, die keine Anmeldung verlangen. Die
mitgelieferte Liste enthält `user/register`, `user/success`, `user/activate` und `user/forgotpassword`; entfernen Sie
für ein Intranet `user/register` aus dem Intranet-Siteaccess, damit dort niemand ein Konto anlegen kann:

```ini
AnonymousAccessList[]
AnonymousAccessList[]=user/forgotpassword
AnonymousAccessList[]=csp/report
```

Behalten Sie die Einträge, die Ihre Installation braucht (prüfen Sie die Kommentare in `settings/site.ini`).

## Adresse und Netz

Geben Sie dem Intranet einen eigenen Host, idealerweise nur aus dem internen Netz oder über ein VPN erreichbar,
festgelegt im Webserver oder in der Firewall. Die Anmeldung ist die zweite Verteidigungslinie, nicht die einzige.

## Aus Suchmaschinen heraushalten

Seiten, die eine Anmeldung verlangen, können Crawler nicht lesen. Fügen Sie für den Intranet-Host eine `robots.txt`
hinzu, die alles verbietet ([robots.txt](../../../ersteller/website-ersteller/robots-txt.md)).

## Prüfungen

- Öffnen Sie die Intranet-Adresse in einem privaten Fenster: Für jede Seite erscheint die Anmeldeseite.
- Melden Sie sich mit einem Testkonto für Mitarbeiter an: Interne Seiten öffnen sich.
- Die öffentliche Site kann keine internen Inhalte zeigen (siehe „Sektionen und Rollen für Intranet-Inhalte“).

## Verwandte Seiten

- [Sektionen und Rollen für Intranet-Inhalte](sektionen-und-rollen-fuer-intranet-inhalte.md)
- [Live gehen: Hosts, HTTPS und Siteaccesses](../../../ersteller/website-ersteller/livegang-hosts-und-https.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
