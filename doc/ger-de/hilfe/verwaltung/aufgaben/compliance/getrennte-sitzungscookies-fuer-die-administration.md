---
title: Getrennte Sitzungscookies für die Administration
audience: government, admin
category: public-sector
order: 60
summary: Schritt für Schritt - jedem Siteaccess in Exponential ein eigenes Sitzungscookie geben (SessionNameHandler=custom), sodass eine Anmeldung auf der öffentlichen Site keine Anmeldung in der Administration ist, und das Ergebnis prüfen.
keywords: Sitzungscookie, SessionNameHandler, custom, SessionNamePrefix, SessionNamePerSiteAccess, Administration, Mitgliederbereich, Siteaccess, Trennung, Velocity SkipCookies, öffentlicher Sektor, session cookie, administration, member area, separation, public sector
type: help
---
# Getrennte Sitzungscookies für die Administration

Für Administratoren von Sites mit einem Mitgliederbereich oder einer anderen Anmeldung auf dem öffentlichen
Siteaccess. Mit getrennten Cookies öffnet eine Sitzung auf der öffentlichen Site nicht die Administration, und
umgekehrt ([13.7 Sitzungen und Cookies](../../../../installation/13-sicherheitshaertung.md#137-sessions-und-cookies)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Alle werden einmal abgemeldet, wenn sich die Namen der Cookies ändern; tun Sie es außerhalb der Bürozeiten und
  informieren Sie die Redakteure.
- Unter Velocity leitet der Antwort-Cache sein Cookie für „persönlich, nicht cachen“ mit `custom` automatisch aus
  `SessionNamePrefix` ab.

## Schritte

1. In `settings/override/site.ini.append.php`:

   ```ini
   [Session]
   SessionNameHandler=custom
   SessionNamePrefix=eZSESSID
   SessionNamePerSiteAccess=enabled
   ```

2. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, dann laden Sie PHP-FPM neu oder
   `./console exp:velocity deploy --allow-root-user`.

## Prüfen, ob es funktioniert hat

1. Melden Sie sich auf der öffentlichen Site an (Mitgliederbereich) und öffnen Sie dann im selben Browser die
   Administration: Sie fragt nach einer Anmeldung.
2. In den Entwicklerwerkzeugen des Browsers tragen die beiden Hosts oder Pfade Sitzungscookies mit verschiedenen Namen.
3. Prüfen Sie, dass ein vorgeschalteter Cache (Varnish, CDN) die neuen Cookie-Namen als persönlich behandelt.

## Verwandte Seiten

- [Sitzungen und Cookies](../../../../../eng-us/help/government/security/sessions-and-cookies.md)
- [Sichere Sitzungscookies setzen](sichere-sitzungscookies-setzen.md)
- [Die Administration trennen](../../../../../eng-us/help/government/security/separating-the-administration.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
