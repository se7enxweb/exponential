---
title: Eine lokale Testinstallation
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 820
summary: Exponential für die App-Entwicklung mit einem Befehl lokal auf SQLite installieren, prüfen, dass die REST-API und die Remote-Dienste aktiv sind (und ezprestapi andernfalls aktivieren), einen Testbenutzer, eine Rolle, einen Schlüssel und einen OAuth-Client vorbereiten, die Installation von einem Emulator oder einem Smartphone aus erreichen und sie zwischen Testläufen zurücksetzen.
keywords: lokale Installation, exp:install, SQLite, Testinstallation, ezprestapi, ActiveExtensions, Testbenutzer, API-Schlüssel, OAuth-Client, Emulator, 10.0.2.2, Smartphone, zurücksetzen, Entwicklung, Mobile App, local installation, test installation, test user, API key, OAuth client, emulator, phone, reset, development, mobile app
type: help
---
# Eine lokale Testinstallation

Diese Seite richtet sich an App-Entwickler, die eine eigene Exponential-Installation möchten, gegen die sie entwickeln
und testen. Mit SQLite braucht sie keinen Datenbankserver und ist in wenigen Minuten installiert.

## Inhalt

1. [Installieren](#installieren)
2. [Die APIs prüfen](#die-apis-prüfen)
3. [Für die App vorbereiten](#für-die-app-vorbereiten)
4. [Von einem Gerät aus erreichen](#von-einem-gerät-aus-erreichen)
5. [Zwischen Testläufen zurücksetzen](#zwischen-testläufen-zurücksetzen)

## Installieren

Aus einem Checkout von Exponential, dessen Voraussetzungen erfüllt sind
([Voraussetzungen](../../../installation/02-voraussetzungen.md)):

```bash
./console exp:install --apply
```

Das installiert das Multisite-Paket auf SQLite in `eng-US` mit den Siteaccesses `site` und `admin` und gibt die
Adressen und die Anmeldedaten aus ([Exponential mit einem Befehl installieren](../../../funktionen/6.0/installation-mit-einem-befehl.md)).
Eine umfassendere Entwicklungsumgebung mit Webserver und Entwicklungseinstellungen beschreibt
[Eine Entwicklungsumgebung einrichten](../../ersteller/anwendungsentwickler/entwicklungsumgebung.md).

## Die APIs prüfen

Führen Sie die drei Prüfungen aus [Gegen eine Demo testen](gegen-eine-demo-testen.md#erst-prüfen-dann-bauen) gegen Ihre
lokale Adresse aus. Antwortet REST mit `404`, aktivieren Sie `ezprestapi` so, wie es der Leitfaden beschreibt
([Migration von ezprestapiprovider](../../../anleitungen/rest-api-v1-und-v2.md#6-migration-von-ezprestapiprovider)):

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=ezprestapi
```

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezpgenerateautoloads.php -e
```

Laden Sie danach PHP-FPM neu oder starten Sie Velocity neu, wenn Velocity die Website ausliefert. Fehlen die
Remote-Dienste, gilt dasselbe Vorgehen mit `ActiveExtensions[]=expservices`
([Remote-Dienste und Apps, Abschnitt 1](../../../anleitungen/entfernte-dienste-und-apps.md#1-prüfen-ob-es-antwortet-1-minute)).

## Für die App vorbereiten

1. **Inhalte:** ein Ordner für die App mit einigen Einträgen jeder Klasse, die die App anzeigt; geben Sie dem Ordner
   eine Remote-ID, die die Konfiguration der App verwendet ([Knoten-IDs, Remote-IDs und Deep Links](../inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md)).
2. **Eine Rolle und ein Benutzer**, genau wie der Integrationsbenutzer der Produktion
   ([Berechtigungen für App-Benutzer](../veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md)).
3. **Ein persönlicher API-Schlüssel** dieses Benutzers für Tests (vorher `apikey/create` in einer Rolle)
   ([Persönliche API-Schlüssel für Werkzeuge](../anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md)).
4. **Ein OAuth-Client** unter **Setup > oAuth-Verwaltung** mit der Weiterleitungsadresse Ihres Entwicklungs-Backends
   und `oauth/authorize` für die Testbenutzer ([OAuth2-Autorisierungscode-Ablauf](../anmeldung/oauth2-autorisierungscode-ablauf.md)).
5. **Ein expservices-Token** des Testbenutzers, wenn die App die Remote-Dienste verwendet
   ([expservices-Tokens in Apps](../anmeldung/expservices-tokens-in-apps.md)).

## Von einem Gerät aus erreichen

- Der Android-Emulator erreicht den Host-Rechner unter `10.0.2.2`; der iOS-Simulator teilt das Netzwerk des Hosts,
  dort funktioniert also `localhost`.
- Ein echtes Smartphone braucht die Adresse des Rechners im lokalen Netz und einen Server, der darauf lauscht.
- Verwenden Sie wie in der Produktion HTTPS, wo die Plattform es für App-Verkehr verlangt; ein lokales Zertifikat oder
  ein Tunnel sind die üblichen Wege. Halten Sie Ausnahmen für unverschlüsseltes HTTP aus Release-Builds heraus.

## Zwischen Testläufen zurücksetzen

- Bewahren Sie nach der Vorbereitung eine Kopie der SQLite-Datenbankdatei auf und kopieren Sie sie vor einem Testlauf
  zurück, damit jeder Lauf mit denselben Inhalten und IDs beginnt.
- Oder schreiben Sie die Vorbereitung als Skript mit den Remote-Diensten (`expnode::create` mit festen `remote_id`s)
  und führen Sie es nach einer frischen Installation aus.

## Verwandte Seiten

- [Apps gegen Exponential testen](README.md)
- [Anwendungen testen](../../ersteller/anwendungsentwickler/anwendungen-testen.md)
- [Erste Schritte](../../erste-schritte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
