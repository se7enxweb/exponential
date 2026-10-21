---
title: "Lektion 2.4: Die Weiterleitung zur Sprache des Besuchers"
audience: trainer, admin, developer
category: training
order: 24
summary: Die Teilnehmenden schicken Besucher einer Adresse ohne Sprachsegment mit DefaultHostUriMatchMapItems zur richtigen Sprache, prüfen die 302-Antwort und ihre Cache-Header mit curl und schalten die Weiterleitung aus. 50 Minuten.
keywords: multisite und seitenpakete, sprachweiterleitung, weiterleitung, cache-header, multisite and site packages, DefaultHostUriMatchMapItems, DefaultHostUriRedirect, Accept-Language, 302, language redirect, Vary, Cache-Control private, DefaultAccess, curl
type: help
---
# Lektion 2.4: Die Weiterleitung zur Sprache des Besuchers

Wenn eine Website einen Siteaccess pro Sprache hat, gehört die bloße Adresse `example.com/` zu keinem von ihnen.
Exponential kann den Siteaccess und das Sprachsegment für diese Adresse nach Host und nach den Sprachen wählen, die der
Browser akzeptiert, und den Browser weiter zu `example.com/de` schicken. Diese Lektion richtet die Weiterleitung für
den Host der Übungsumgebung aus Lektion 2.3 ein, liest die Antwort mit `curl` und erklärt, was sie für Caches
bedeutet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 2: Anfragen Siteaccesses zuordnen](README.md) |
| Dauer | 50 Minuten (20 Präsentation, 30 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 2.3](zuordnung-nach-host-und-pfad-und-port.md): `club.localhost/de` erreicht `site_b_ger` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Einträge in `DefaultHostUriMatchMapItems[]` schreiben (`host;uri;siteaccess[;method[;language]]`);
- vorhersagen, welcher Eintrag bei einem bestimmten `Accept-Language`-Header gilt;
- die Weiterleitung mit `curl` prüfen und ihre Header `Location`, `Vary` und `Cache-Control` lesen;
- die Weiterleitung mit `DefaultHostUriRedirect=disabled` ausschalten und sagen, was stattdessen geschieht.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md#the-redirect-to-the-language).

1. **Wann sie greift** (4 Minuten). Nur dort, wo keine Prüfung aus `MatchOrder` gepasst hat, also wo `DefaultAccess`
   antworten würde: typischerweise die Adresse ohne Siteaccess- oder Sprachsegment. Eine Adresse, die eine Prüfung
   trifft (`/de/...`, `/admin/...`), wird nie weitergeschickt.
2. **Der Eintrag** (5 Minuten). `DefaultHostUriMatchMapItems[]=host;uri;siteaccess[;method[;language]]`. Der erste
   Eintrag für den Host, dessen Sprache der Browser akzeptiert, gewinnt, wobei die Sprachen aus `Accept-Language` ab
   der meistgewünschten versucht werden; will der Browser keine davon, gilt der erste Eintrag für den Host ohne
   Sprache; gibt es keinen, antwortet `DefaultAccess`. `de` nimmt auch `de-CH`. Ein Eintrag, dessen Siteaccess nicht
   in `AvailableSiteAccessList[]` steht, wird ignoriert und protokolliert.
3. **Die Antwort** (4 Minuten). Eine GET- oder HEAD-Anfrage bekommt einmal eine 302 auf dieselbe Adresse mit dem
   Segment, wobei die Query erhalten bleibt: `example.com/news/an-article?page=2` geht zu
   `example.com/de/news/an-article?page=2`. Die `Location` ist immer ein Pfad auf dem angefragten Host, nie eine
   vollständige URL. Ein POST wird an der Adresse selbst beantwortet.
4. **Caches** (4 Minuten). Hängt die Antwort vom Browser ab, wird sie mit `Vary: Accept-Language` und
   `Cache-Control: private` gesendet, sodass weder ein Proxy noch der Antwort-Cache von Velocity sie aufbewahrt. Jede
   Seite hinter der Weiterleitung hat eine Adresse pro Sprache und wird wie üblich zwischengespeichert. Seiten, die der
   statische Cache oder der HTTP-Cache aufbewahrt hat, bevor ein Eintrag hinzugefügt wurde, werden weiterhin ohne die
   Weiterleitung ausgeliefert: Leeren Sie diese Caches nach einer Änderung der Einstellung.
5. **Ohne die Weiterleitung** (3 Minuten). `DefaultHostUriRedirect=disabled` oder keine Prüfung, die die Zieladresse
   nehmen würde: Die Seite wird an der Adresse selbst im gewählten Siteaccess angezeigt, und ihre Links tragen das
   Segment.

### Häufige Fragen

**Gibt es ein Cookie, das sich die Wahl merkt?** Nein. Ein Besucher, der die Sprache wechselt, folgt Links, die das
andere Segment tragen; diese Adressen werden von einer Prüfung zugeordnet und nie wieder weitergeschickt.

**Leitet auch die REST-Schnittstelle weiter?** Nein. Nur die Anfrage einer Webseite fragt nach der
Sprachvoreinstellung; die REST-Schnittstelle, Skripte und Erweiterungen bekommen wie bisher die Prüfungen und dann
`DefaultAccess`.

**Funktioniert es unter Velocity?** Ja; die Weiterleitung wird wie eine Modul-Weiterleitung zurückgegeben und
funktioniert hinter Apache und Velocity gleich, auf jedem Port.

**Was, wenn nichts gesetzt ist?** Dann ändert sich nichts: `settings/site.ini` liefert keinen Eintrag mit.

### Stolperfallen

- **Nur im Browser testen.** Browser merken sich Weiterleitungen und senden ihre eigenen Sprachen. Testen Sie mit
  `curl` und einem ausdrücklichen `Accept-Language`-Header.
- **Ein Eintrag für einen Host, den eine Prüfung bereits trifft.** Wenn ein Eintrag mit leerem Pfad in
  `HostUriMatchMapItems[]` für den Host `/` nimmt (wie `club.localhost;;site_b` in Lektion 2.3), greift die
  Voreinstellung dort nie. Die Übung verwendet aus diesem Grund einen zweiten Host.
- **Ein Segment, das keine Prüfung nimmt.** Die Weiterleitung braucht eine Prüfung, die das Ziel erreicht; sonst wird
  die Seite an Ort und Stelle angezeigt. Prüfen Sie zuerst die Zieladresse für sich.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Lektion 2.3. Ersetzen Sie `http://127.0.0.1:8087` durch die Adresse
Ihrer Übungsumgebung.

1. Geben Sie der deutschen und der englischen Website je einen Pfad auf einem neuen Host, `lang.localhost`, ohne
   Eintrag für `/` selbst:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostUriMatchMapItems[]' 'lang.localhost;de;site_b_ger' global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostUriMatchMapItems[]' 'lang.localhost;en;site_b' global
   ```

2. Fügen Sie die Voreinstellungen für diesen Host hinzu, Deutsch für deutsche Browser und Englisch für alle anderen:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/DefaultHostUriMatchMapItems[]' 'lang.localhost;de;site_b_ger;;de' global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/DefaultHostUriMatchMapItems[]' 'lang.localhost;en;site_b' global
   php bin/php/ezcache.php --clear-tag=ini
   ```

3. Prüfen Sie zuerst die Zieladressen:
   `curl -s -H 'Host: lang.localhost' http://127.0.0.1:8087/de | grep -i '<title>'` (und dasselbe für `/en`).
4. Fragen Sie als deutscher Browser an:

   ```bash
   curl -sI -H 'Host: lang.localhost' -H 'Accept-Language: de-DE,de;q=0.9,en;q=0.8' http://127.0.0.1:8087/ \
     | grep -iE '^(HTTP|location|vary|cache-control)'
   ```

5. Fragen Sie als englischer und als französischer Browser an (`Accept-Language: en` und `fr`). Notieren Sie jedes Mal
   die `Location`.
6. Fragen Sie mit dem deutschen Header eine tiefere Adresse mit Query an: `.../News?x=1`. Notieren Sie die `Location`.
7. Fragen Sie mit dem deutschen Header `/de` selbst an und bestätigen Sie, dass keine Weiterleitung kommt.
8. Schalten Sie die Weiterleitung aus:
   `php bin/php/console exp:ini set site.ini/SiteAccessSettings/DefaultHostUriRedirect disabled global`, leeren Sie
   den INI-Cache und wiederholen Sie Schritt 4. Notieren Sie den Status und den Seitentitel.
9. Schalten Sie sie wieder ein (`enabled`) und leeren Sie den INI-Cache.

## Erwartetes Ergebnis

- Schritt 4: `302` mit einem `Location`-Pfad, der auf demselben Host auf `/de` endet, dazu `Vary: Accept-Language` und
  `Cache-Control: private`, weil die Antwort vom Browser abhängt.
- Schritt 5: Der englische und der französische Browser werden beide zu `/en` geschickt (der Eintrag ohne Sprache).
- Schritt 6: Die `Location` behält Pfad und Query nach dem Segment (`/de/News?x=1`).
- Schritt 7: `200` und die deutsche Seite, keine Weiterleitung.
- Schritt 8: `200` an `/` selbst, mit der deutschen Website.
- Der Trainer prüft Schritt 4 und Schritt 8 am Beamer in der Übungsumgebung einer Person.

## Quiz

1. In welcher Situation greift `DefaultHostUriMatchMapItems[]` überhaupt?
2. Einträge: `example.com;de;site_de;;de`, dann `example.com;en;site_en`. Ein Browser sendet
   `Accept-Language: fr, en;q=0.5`. Wohin wird er geschickt? A. `/de` B. `/en` C. nirgendwohin D. `/fr`
3. Warum wird die Weiterleitung mit `Cache-Control: private` gesendet?
4. Richtig oder falsch: Die `Location` der Weiterleitung kann auf einen anderen Host zeigen.
5. Was geschieht bei `DefaultHostUriRedirect=disabled`?

<details>
<summary>Antworten</summary>

1. Wenn keine Prüfung aus `MatchOrder` die Adresse getroffen hat, wo sonst `DefaultAccess` antworten würde.
2. **B.** Es gibt keinen Eintrag für Französisch, und der Eintrag `de` wird nicht akzeptiert, deshalb gilt der erste
   Eintrag für den Host ohne Sprache.
3. Weil die Antwort von der Sprache des Browsers abhängt; Proxys und der Antwort-Cache von Velocity dürfen sie nicht
   für andere Besucher aufbewahren.
4. **Falsch.** Sie ist immer ein Pfad auf dem angefragten Host, nie eine vollständige URL.
5. Die Seite wird an der Adresse selbst im gewählten Siteaccess angezeigt, und ihre Links tragen das Segment.

</details>

## Weiterführende Informationen

- [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md):
  die vollständigen Regeln, die Caches und wie es funktioniert.
- [Antwort-Cache von Velocity](../../../../../eng-us/features/6.0/velocity-response-cache.md).
- [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md).
- [Sprachumschalter und Adressen pro Sprache](../../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md).

## Verwandte Seiten

- [Modul 2: Anfragen Siteaccesses zuordnen](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
