---
title: Einen Intranet-Siteaccess für Mitarbeiter einrichten
audience: government, admin
category: public-sector
order: 180
summary: Aufgabe für Administratoren - einen Intranet-Siteaccess anlegen, der für jede Seite eine Anmeldung verlangt, mit eigenem Ordner in einer Sektion, die Besucher nicht lesen können, und einer Rolle für Mitarbeiter, und ihn von außen und innen testen.
keywords: Intranet, Mitarbeiter-Siteaccess, Sektion, Mitarbeiterrolle, interne Site, intranet, RequireUserLogin, staff siteaccess, AnonymousAccessList, section, Staff role, internal site
type: help
---
# Einen Intranet-Siteaccess für Mitarbeiter einrichten

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Ein Siteaccess nur für Mitarbeiter](../../../../../eng-us/help/government/building/intranets/a-staff-only-siteaccess.md) und
[Sektionen und Rollen für Intranet-Inhalte](../../../../../eng-us/help/government/building/intranets/sections-and-roles-for-intranet-content.md).

## Bevor Sie beginnen

- Ein interner Hostname (zum Beispiel `intranet.example.gov`), idealerweise nur aus dem internen Netz erreichbar.
- Eine Benutzergruppe für Mitarbeiter.

## Schritte

1. **Setup > Sektionen**: Legen Sie „Intranet“ an. Legen Sie den Intranet-Ordner an und **Inhalt zuweisen** zur
   Sektion.
2. Legen Sie den Siteaccess `intranet` wie eine Abteilungs-Site an (Ordner als Wurzelknoten) und fügen Sie hinzu:

   ```ini
   [SiteAccessSettings]
   RequireUserLogin=true
   AnonymousAccessList[]
   AnonymousAccessList[]=user/forgotpassword
   AnonymousAccessList[]=csp/report
   ```

3. Registrieren Sie den Siteaccess und ordnen Sie ihn zu (`AvailableSiteAccessList[]`, `HostMatchMapItems[]`).
4. **Benutzer > Rollen und Richtlinien**: eine Rolle „Mitarbeiter“ mit `content/read` auf den Sektionen Intranet und
   Standard und `user/login` für den Intranet-Siteaccess; weisen Sie sie der Gruppe der Mitarbeiter zu.
5. Fügen Sie für den Intranet-Host eine `robots.txt` hinzu, die alles verbietet.
6. Leeren Sie den INI-Cache.

## Prüfen

- Ein privates Browserfenster auf dem Intranet-Host zeigt für jede Adresse die Anmeldeseite.
- Ein Testkonto für Mitarbeiter öffnet Intranet-Seiten.
- Auf der öffentlichen Site öffnen sich Intranet-Seiten nicht und werden von der Suche nicht gefunden.

## Probleme

| Problem | Lösung |
|---|---|
| Besucher können sich registrieren | `user/register` steht für den Siteaccess noch in `AnonymousAccessList[]` |
| Mitarbeiter sehen „Zugriff verweigert“ | der Rolle für Mitarbeiter fehlt die Sektion Intranet oder `user/login` für den Siteaccess |

## Verwandte Seiten

- [Intranets für Mitarbeiter mit Exponential](../../../../../eng-us/help/government/building/intranets/staff-intranets-with-exponential.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
