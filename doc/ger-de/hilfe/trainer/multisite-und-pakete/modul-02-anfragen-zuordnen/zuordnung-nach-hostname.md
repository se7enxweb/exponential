---
title: "Lektion 2.2: Zuordnung nach Hostname"
audience: trainer, admin, developer
category: training
order: 22
summary: Die Teilnehmenden geben der zweiten Website mit der Host-Zuordnung einen eigenen Hostnamen, testen ihn ohne DNS, indem sie den Host-Header senden, und verwenden HostMatchMethod, damit ein Testhost denselben Siteaccess erreicht. 55 Minuten.
keywords: multisite und seitenpakete, host-zuordnung, hostname, zuordnungsmethode, zertifikat, multisite and site packages, host matching, MatchOrder host, HostMatchType map, HostMatchMapItems, HostMatchMethod, strict, start, end, part, SiteURL, DNS, certificate, TrustedProxies
type: help
---
# Lektion 2.2: Zuordnung nach Hostname

Eine echte Multisite gibt jeder Marke einen eigenen Hostnamen: `www.example.com` für die eine Website,
`www.example.org` für die andere. Die Host-Prüfung wählt den Siteaccess nach dem Host, den der Browser anfragt. Diese
Lektion richtet sie auf der Schulungsinstallation ein, ohne DNS anzufassen, und zeigt die Methoden der
Host-Zuordnung, mit denen ein Testhost wie `www.example.com.test.local` den Siteaccess der Live-Website erreicht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 2: Anfragen Siteaccesses zuordnen](README.md) |
| Dauer | 55 Minuten (20 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 2.1](zuordnung-nach-url-pfad.md); `curl` auf dem Rechner der Person oder dem Server der Übungsumgebung |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Host-Prüfung zu `MatchOrder` hinzufügen und mit `HostMatchMapItems[]` einen Host einem Siteaccess zuordnen;
- die Host-Zuordnung mit `curl` und dem `Host`-Header testen, bevor ein DNS-Eintrag existiert;
- eine Methode der Host-Zuordnung (`strict`, `start`, `end`, `part`) für die ganze Zuordnung oder für einen Eintrag
  wählen;
- aufzählen, was ein neuer Host sonst noch braucht: `SiteURL`, DNS, den Webserver oder Velocity, das Zertifikat.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)
und [Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#zuordnung-nach-url-host-oder-port).

1. **Die Host-Zuordnung** (5 Minuten). Zeigen Sie das Beispiel:

   ```ini
   # settings/override/site.ini.append.php
   [SiteAccessSettings]
   MatchOrder=host;uri
   HostMatchType=map
   HostMatchMapItems[]
   HostMatchMapItems[]=www.example.com;site
   HostMatchMapItems[]=www.example.org;site_org
   HostMatchMapItems[]=admin.example.com;admin
   ```

   `HostMatchType` kennt außerdem `element`, `text` und `regexp`; Multisites verwenden die Zuordnung (`map`).
2. **Wie Hosts verglichen werden** (5 Minuten). Jede Methode vergleicht Hostnamen so, wie DNS es tut: ohne Rücksicht
   auf Groß- und Kleinschreibung und einen abschließenden Punkt, ohne den Port der Anfrage, sofern der Eintrag keinen
   nennt, und einen in Unicode geschriebenen Namen in der ASCII-Form, die ein Browser sendet (wenn PHP die
   Erweiterung intl hat).
3. **Die Methoden** (5 Minuten):

   | Methode | Passt, wenn der Host ... | Beispiel für `www.example.com` |
   |---|---|---|
   | `strict` | der aufgeführte Host ist (Voreinstellung) | `www.example.com` |
   | `start` | mit ihm beginnt | `www.example.com.test.local` |
   | `end` | mit ihm endet | `preview.www.example.com` |
   | `part` | ihn enthält | `a.www.example.com.b` |

   `HostMatchMethod` setzt sie für die ganze Zuordnung; ein drittes Feld setzt sie für einen Eintrag
   (`HostMatchMapItems[]=admin.example.com;admin;start`). `start` und `part` nehmen auch Hosts, die Sie nicht gemeint
   haben, verwenden Sie sie deshalb nur dort, wo der Webserver für Ihre eigenen Hosts antwortet. Der statische Cache
   kennt nur `strict`.
4. **Über die Einstellung hinaus** (5 Minuten). Jeder Siteaccess setzt seine eigene `[SiteSettings] SiteURL`. Der Host
   braucht einen DNS-Eintrag, einen Platz in der Konfiguration des Webservers oder von Velocity und im Zertifikat.
   Hinter einem Proxy oder Load Balancer tragen Sie ihn in `site.ini [HTTPHeaderSettings] TrustedProxies[]` ein, sonst
   sieht die Website das Schema und die Adresse des Proxys.

### Häufige Fragen

**Spielt die Reihenfolge von `MatchOrder` hier eine Rolle?** Ja. Bei `uri;host` wird eine Adresse, deren erstes
Element ein Siteaccess-Name ist (`/admin`), auf jedem Host von der URI-Prüfung zugeordnet; alles andere fällt zur
Host-Zuordnung durch. Bei `host;uri` gewinnt ein aufgeführter Host immer.

**Brauche ich einen Zuordnungseintrag pro Sprache?** Mit der Host-Zuordnung allein ja, einen Host pro Siteaccess. Für
einen Host mit einem Pfad pro Sprache verwenden Sie `host_uri` (Lektion 2.3).

**Kann der Port Teil eines Eintrags sein?** Ja, ein Eintrag kann einen nennen (`example.com:8080`); sonst wird der
Port der Anfrage ignoriert.

**Warum `start` für Testsysteme?** Damit `www.example.com.test.local` denselben Siteaccess erreicht wie
`www.example.com`, ohne einen zweiten Eintrag pro Testhost.

### Stolperfallen

- **Gemeinsame Übungsumgebung.** Die Host-Zuordnung und `MatchOrder` stehen im globalen Override. Auf einem
  gemeinsamen Schulungsserver lassen Sie stattdessen eine Person sie am Beamer ändern.
- **Browser und `.localhost`.** Viele aktuelle Browser schicken Namen, die auf `.localhost` enden, an den eigenen
  Rechner; tut der Browser einer Person das nicht, verwenden Sie nur `curl`.
- **Den INI-Cache vergessen.** Jede Änderung braucht `--clear-tag=ini` (und einen Neustart von Velocity, wenn
  Velocity die Übungsumgebung ausliefert).

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Lektion 2.1. Ersetzen Sie `http://127.0.0.1:8087` durch die Adresse
Ihrer Übungsumgebung.

1. Lesen Sie die aktuellen Prüfungen: `php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder`.
2. Fügen Sie die Host-Prüfung nach der URI-Prüfung hinzu, wobei die URI-Prüfung zuerst bleibt:

   ```bash
   php bin/php/console exp:ini set site.ini/SiteAccessSettings/MatchOrder 'uri;host' global --dry-run
   php bin/php/console exp:ini set site.ini/SiteAccessSettings/MatchOrder 'uri;host' global
   ```

3. Ordnen Sie der zweiten Website einen Host zu:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostMatchMapItems[]' 'site-b.localhost;site_b' global
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Testen Sie ohne DNS:

   ```bash
   curl -s -H 'Host: site-b.localhost' http://127.0.0.1:8087/ | grep -i '<title>'
   curl -s http://127.0.0.1:8087/ | grep -i '<title>'
   ```

5. Probieren Sie einen Testhost: `curl -s -H 'Host: site-b.localhost.training' http://127.0.0.1:8087/ | grep -i '<title>'`.
   Notieren Sie, welche Website antwortet.
6. Lassen Sie den Eintrag nach dem Anfang des Hosts zuordnen:

   ```bash
   php bin/php/console exp:ini rem 'site.ini/SiteAccessSettings/HostMatchMapItems[]' 'site-b.localhost;site_b' global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostMatchMapItems[]' 'site-b.localhost;site_b;start' global
   php bin/php/ezcache.php --clear-tag=ini
   ```

7. Wiederholen Sie Schritt 5.
8. Wiederholen Sie Schritt 4 mit dem Header `Host: SITE-B.localhost` und mit `Host: site-b.localhost:8087`.
9. Wenn Ihr Browser `http://site-b.localhost:8087/` öffnet, öffnen Sie es und vergleichen Sie mit der Hauptwebsite.
10. Schreiben Sie die drei Dinge auf, die ein echter Host vor dem Livegang außerdem bräuchte.

## Erwartetes Ergebnis

- Schritt 4: Der erste Befehl gibt den Titel von *Site B* aus, der zweite den Titel der Hauptwebsite.
- Schritt 5 antwortet mit dem Standard-Siteaccess; nach Schritt 6 antwortet Schritt 7 mit *Site B*.
- Schritt 8: Beide Varianten erreichen *Site B*, weil Groß- und Kleinschreibung und der Port der Anfrage ignoriert
  werden.
- Schritt 10: ein DNS-Eintrag, die Konfiguration des Webservers oder von Velocity und das Zertifikat (dazu
  `SiteURL`).
- Der Trainer prüft das, indem er Schritt 7 in der Übungsumgebung einer Person am Beamer ausführt.

## Quiz

1. Welche Einstellung ordnet `www.example.org` dem Siteaccess `site_org` zu?
2. Ein Eintrag lautet `www.example.com;site;start`. Welchen Host trifft er?
   A. `preview.www.example.com` B. `www.example.com.test.local` C. `example.com` D. `www.example.org`
3. Warum ist `part` auf einem Server riskant, der für jeden Host antwortet?
4. Ein Browser fragt `WWW.Example.com.` an (Großbuchstaben und ein abschließender Punkt). Passt ein `strict`-Eintrag
   für `www.example.com`?
5. Nennen Sie zwei Dinge außer der Host-Zuordnung, die ein neuer Hostname braucht.

<details>
<summary>Antworten</summary>

1. `HostMatchMapItems[]=www.example.org;site_org` in `[SiteAccessSettings]`, mit `HostMatchType=map` und `host` in
   `MatchOrder`.
2. **B.** `start` trifft einen Host, der mit dem Eintrag beginnt
   ([Siteaccess nach Host-Präfix](../../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md#the-host-map-by-the-start-of-a-host)).
3. Er nimmt auch Hosts, die Sie nicht gemeint haben (`a.www.example.com.b`); verwenden Sie ihn nur dort, wo der
   Webserver für Ihre eigenen Hosts antwortet.
4. Ja: Hosts werden ohne Rücksicht auf Groß- und Kleinschreibung und den abschließenden Punkt verglichen.
5. Zwei beliebige aus: `SiteURL` des Siteaccess, ein DNS-Eintrag, die Konfiguration des Webservers oder von Velocity,
   das Zertifikat, `TrustedProxies[]` hinter einem Proxy.

</details>

## Weiterführende Informationen

- [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md).
- [Multisite auf einer Installation](../../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md): Host-Zuordnung
  für Hoster.
- [Live gehen: Hosts, HTTPS und Siteaccesses](../../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md).
- [8. Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md): Konfiguration von Webserver und Velocity.

## Verwandte Seiten

- [Modul 2: Anfragen Siteaccesses zuordnen](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
