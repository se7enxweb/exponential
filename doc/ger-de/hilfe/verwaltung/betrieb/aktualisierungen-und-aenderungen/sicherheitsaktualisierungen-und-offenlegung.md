---
title: Sicherheitsupdates und Offenlegung
audience: government, admin, project-manager
category: public-sector
order: 130
summary: Welche Exponential-Versionen Sicherheitskorrekturen erhalten, wie Korrekturen eine öffentliche Stelle erreichen (das nächste Release, nie ein geändertes Tag), wie Sie sie unter Änderungsmanagement verfolgen und einspielen und wie Sie eine Schwachstelle vertraulich melden.
keywords: Sicherheitsupdates, Patches, Schwachstelle, unterstützte Versionen, SECURITY.md, Offenlegung, security@se7enx.com, Release, Changelog, Patch-Management, NIS2, beschleunigte Änderung, Schwachstellenmanagement, security updates, patches, vulnerability, supported versions, disclosure, changelog, patch management, fast-track change, vulnerability management
type: help
---
# Sicherheitsupdates und Offenlegung

Diese Seite richtet sich an Sicherheitsbeauftragte, Verantwortliche für Dienste und Administratoren, die eine
öffentliche Website auf dem neuesten Stand halten und Schwachstellen verantwortungsvoll melden müssen. Die Richtlinie
steht in der Datei `SECURITY.md` im Wurzelverzeichnis des Repositorys; die Sicht des Hostings ist
[Sicherheitsupdates und Offenlegung (Hosting)](../../../hosting/sicherheit/sicherheitsupdates-und-offenlegung.md).

## Inhalt
1. [Was unterstützt wird](#was-unterstützt-wird)
2. [Wie eine Korrektur Sie erreicht](#wie-eine-korrektur-sie-erreicht)
3. [Verfolgen, was Sie brauchen](#verfolgen-was-sie-brauchen)
4. [Eine Sicherheitskorrektur einspielen](#eine-sicherheitskorrektur-einspielen)
5. [Eine Schwachstelle melden](#eine-schwachstelle-melden)
6. [Verwandte Seiten](#verwandte-seiten)

## Was unterstützt wird

Aus `SECURITY.md`:

| Version | Unterstützt |
|---|---|
| 6.0.x, die aktuelle Linie (`main` und das nächste daraus erstellte Tag) | ja |
| Veröffentlichte Tags von 6.0.x (`v6.0.0` bis `v6.0.14`) | durch Aktualisierung auf das neueste 6.0.x behoben |
| 5.x, 4.x, 3.x und älter | nein |

Eine Website auf einer nicht unterstützten Linie braucht vor allem anderen einen Aktualisierungsplan
([Überblick über Aktualisierungen](aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md)).

## Wie eine Korrektur Sie erreicht

Sicherheitskorrekturen werden auf `main` vorgenommen und mit dem nächsten Release 6.0.x veröffentlicht. Ein
veröffentlichtes Tag wird nie geändert: Eine Korrektur für `v6.0.14` erreicht Sie als nächstes Release, nicht als
korrigierte Kopie des alten ([Unveränderliche Releases und Versionen](unveraenderliche-releases-und-versionen.md)).
Erweiterungen folgen in ihren eigenen Repositorys derselben Regel.

## Verfolgen, was Sie brauchen

| Quelle | Was sie Ihnen sagt |
|---|---|
| Der Changelog der Linie ([6.0.15](../../../../../changelogs/6.0/6.0.15.md)) | jede Änderung, einschließlich sicherheitsrelevanter Änderungen |
| [Sicherheitsstandard](../../../../standards/sicherheit.md) | die Zuordnung zu OWASP und die bekannten Lücken |
| **Setup > Updates und Pakete** (ezupdate), **Nach Updates suchen** | welche installierten Pakete neuere Versionen haben (`composer outdated`); ändert nichts |
| **Setup > Upgrade-Prüfung** | ob Dateien von dem Release abweichen, das Sie betreiben |
| Die eigenen Sicherheitshinweise Ihres Betriebssystems und von PHP | die Plattform unter Exponential |

Der Sicherheitsstandard hält fest, dass die CI des Projekts keine Prüfung der Abhängigkeiten auf Schwachstellen
ausführt; verlangt Ihre Richtlinie eine solche, führen Sie Ihre eigene aus (zum Beispiel `composer audit` in Ihrer
Pipeline) ([Sicherheitsstandard, „Bekannte Lücken“](../../../../standards/sicherheit.md#bekannte-lücken)).

Einige öffentliche Stellen haben gesetzliche Pflichten zum Umgang mit Schwachstellen und zur Meldung von Vorfällen,
zum Beispiel nach der [NIS2-Richtlinie](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) der EU, soweit sie für sie gilt;
klären Sie das mit Ihren Teams für Informationssicherheit und Recht.

## Eine Sicherheitskorrektur einspielen

1. Lesen Sie die Versionshinweise; entscheiden Sie mit Ihrem Change-Manager die Art der Änderung (normal im
   beschleunigten Verfahren oder Notfall) ([Änderungsmanagement und Änderungsbeiräte](aenderungsmanagement-und-aenderungsbeiraete.md)).
2. Spielen Sie sie zuerst auf dem Staging ein ([Staging und Freigabe](staging-und-freigabe.md)), es sei denn, das
   Risiko des Wartens ist höher.
3. Sichern Sie, öffnen Sie ein [Wartungsfenster](wartungsfenster.md), wenn sich die Datenbank ändert,
   aktualisieren Sie und stellen Sie bereit:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ./console exp:velocity deploy --dry-run --allow-root-user
   ```

4. Prüfen Sie mit **Setup > Upgrade-Prüfung** und der Version unter **Setup > Systeminformationen**.
5. Halten Sie die Änderung und die nun laufende Version fest.

## Eine Schwachstelle melden

Melden Sie vertraulich per E-Mail an die Adresse in `SECURITY.md` (security@se7enx.com), nicht im öffentlichen
Issue-Tracker; eine erste Antwort wird innerhalb eines Werktags zugesagt. Geben Sie die Version, die PHP-Version, die
Engine und die Datenbank an, was ein Angreifer tun kann und wie es sich nachstellen lässt. Lassen Sie Passwörter,
Geheimnisse und personenbezogene Daten weg. Die Installation liefert keine Datei `security.txt` aus
([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)); eine öffentliche Stelle, die eine für ihre eigene Website möchte,
legt sie selbst in ihrem Web-Wurzelverzeichnis ab.

## Verwandte Seiten
- [Aktualisierungen von Erweiterungen](aktualisierung-von-erweiterungen.md)
- [Versionshinweise lesen](versionshinweise-lesen.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Den Host härten](../hosting/den-host-haerten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
