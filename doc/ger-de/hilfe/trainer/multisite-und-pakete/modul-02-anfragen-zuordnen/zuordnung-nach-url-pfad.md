---
title: "Lektion 2.1: Zuordnung nach URL-Pfad"
audience: trainer, admin, developer
category: training
order: 21
summary: Die Teilnehmenden erklären, wie MatchOrder und die URI-Prüfung einen Siteaccess aus dem ersten Teil des Pfads wählen, was DefaultAccess tut, wenn nichts passt, und wie PathPrefix die Adressen einer Website verkürzt. 50 Minuten.
keywords: multisite und seitenpakete, uri-zuordnung, pfad, standard-siteaccess, pfadpräfix, multisite and site packages, MatchOrder, uri matching, URIMatchType, element, map, URIMatchMapItems, DefaultAccess, RemoveSiteAccessIfDefaultAccess, PathPrefix, PathPrefixExclude
type: help
---
# Lektion 2.1: Zuordnung nach URL-Pfad

Jede Anfrage wird einem Siteaccess zugeordnet, bevor irgendetwas anderes geschieht. Die einfachste Regel, und die, die
eine Standardinstallation verwendet, nimmt den ersten Teil des Pfads: `/site_b/...` gehört zu `site_b`. Diese Lektion
erklärt diese Regel, den Fallback, wenn nichts passt, und `PathPrefix`, mit dem eine Website ihren eigenen Ordner aus
ihren Adressen ausblenden kann.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 2: Anfragen Siteaccesses zuordnen](README.md) |
| Dauer | 50 Minuten (20 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Modul 1](../modul-01-siteaccesses-fuer-mehrere-websites/README.md): Die Siteaccesses `site_b` und `site_b_ger` funktionieren |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `MatchOrder` als Liste von Prüfungen erklären, die der Reihe nach versucht werden, wobei der erste Treffer gewinnt;
- die URI-Zuordnungsarten `element`, `map`, `text` und `regexp` beschreiben;
- vorhersagen, welcher Siteaccess eine Adresse beantwortet, auch eine, auf die keine Prüfung passt (`DefaultAccess`);
- `PathPrefix` so setzen, dass die Seiten einer Website ohne den Ordnernamen der Website adressiert werden.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Kommentare von `settings/site.ini [SiteAccessSettings]` und
[10.2 Siteaccesses und Website-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site).

1. **Prüfungen der Reihe nach** (5 Minuten). `MatchOrder` listet die Prüfungen: `uri`, `host`, `host_uri`, `port` und
   `servervar`. Der mitgelieferte Wert ist `uri;host;port`; der Installer schreibt einen eigenen. Die erste Prüfung,
   die einen verfügbaren Siteaccess findet, gewinnt; eine Prüfung, die einen Namen findet, der nicht in
   `AvailableSiteAccessList[]` steht, zählt nicht, und die nächste Prüfung wird versucht.
2. **Die URI-Prüfung** (6 Minuten). `URIMatchType` wählt das Wie:

   | Wert | Bedeutung |
   |---|---|
   | `element` (mitgeliefert) | das Pfadelement `URIMatchElement` (1, das erste) ist der Name des Siteaccess |
   | `map` | Einträge `URIMatchMapItems[]=uri;siteaccess` ordnen ein Pfadelement einem Siteaccess zu |
   | `text` | ein Teilstring am Anfang oder Ende des Pfads |
   | `regexp` | ein regulärer Ausdruck und der Teil, den er herauszieht |
   | `disabled` | keine URI-Zuordnung |

   Bei `element` wird das zugeordnete Element aus dem Pfad entfernt, den der Kernel sieht, sodass `/site_b/news` die
   Seite `news` von `site_b` ist.
3. **Wenn nichts passt** (3 Minuten). `[SiteSettings] DefaultAccess` antwortet. `RemoveSiteAccessIfDefaultAccess=enabled`
   lässt den Namen des Standard-Siteaccess in den Links weg, die er erzeugt; der Kommentar in `settings/site.ini`
   sagt, dass eine Änderung daran das Leeren des Caches erfordert. Lektion 2.4 zeigt ein besseres Werkzeug für die
   Startseite: die Weiterleitung zur Sprache.
4. **`PathPrefix`** (6 Minuten). Eine Website, deren Wurzel ein Ordner `Site B` ist, hätte sonst Adressen wie
   `/site_b/Site-B/an-article`. `PathPrefix=Site-B` in diesem Siteaccess blendet das Präfix aus: `/site_b/an-article`.
   `PathPrefixExclude[]` listet Pfade (zum Beispiel `media`, `users`), die ihre vollständige Adresse behalten. Der
   Kommentar in `settings/site.ini` ergänzt, dass ein `PathPrefix` auch die Einstellung `PathPrefix` in `logfile.ini`
   braucht.

### Häufige Fragen

**Ist der Name in der Adresse der Name des Siteaccess?** Bei `element` ja. Bei `map` können der Adressteil und der
Name des Siteaccess abweichen, zum Beispiel `URIMatchMapItems[]=de;site_b_ger`.

**Kann die Hauptwebsite ganz ohne Präfix auskommen?** Ja: Machen Sie sie zum `DefaultAccess`, sodass `/` und jede
Adresse, auf die keine Prüfung passt, zu ihr gehen, und ziehen Sie die Weiterleitung aus Lektion 2.4 in Betracht.

**Ist die URL-Zuordnung für den Produktivbetrieb geeignet?** Sie funktioniert hinter jedem Webserver mit einem
Hostnamen. Die Host-Zuordnung (Lektion 2.2) ist die übliche Wahl für den Produktivbetrieb mit mehreren Marken.

**Warum funktioniert `/admin` noch, nachdem ich `URIMatchType` geändert habe?** Die Administration wird oft über eine
andere Prüfung oder einen anderen Eintrag erreicht; lesen Sie `MatchOrder` und die Zuordnung mit `exp:ini where`.

### Stolperfallen

- **Globale Änderungen in einer gemeinsamen Übungsumgebung.** `MatchOrder` und `URIMatchType` stehen im globalen
  Override. Auf einem gemeinsamen Schulungsserver macht die Änderung einer Person die Adressen aller kaputt. Die
  Änderungen dieser Lektion gelten pro Siteaccess.
- **Der falsche Alias für `PathPrefix`.** Der Wert ist der URL-Alias des Ordners, wie der Browser ihn zeigt
  (einschließlich Groß- und Kleinschreibung und Bindestrichen), nicht sein Name.
- **Veraltete Seiten.** Leeren Sie nach einer Änderung von `PathPrefix` den INI- und den Inhalts-Cache, sonst bleiben
  alte Links in zwischengespeicherten Seiten.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Modul 1 (URL-Zuordnung).

1. Lesen Sie die Prüfungen und die wirksame URI-Regel:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/URIMatchType
   php bin/php/console exp:ini get site.ini/SiteSettings/DefaultAccess
   ```

2. Sagen Sie voraus und prüfen Sie dann mit `curl -sI` und im Browser, welcher Siteaccess jede Adresse beantwortet
   (verwenden Sie die Adresse Ihrer Übungsumgebung): `/site_b`, `/site_b_ger`, `/admin`, `/no_such_site`, `/`.
3. Erklären Sie für `/no_such_site`, was geantwortet hat: Die Prüfung fand einen Namen, der nicht verfügbar ist,
   deshalb übernahm der Standard-Siteaccess die Anfrage und behandelte `no_such_site` als Seitenadresse.
4. Legen Sie in der Administration unterhalb des Ordners *Site B* einen **Folder** namens `News` an und
   veröffentlichen Sie ihn.
5. Öffnen Sie `/site_b` im Browser und folgen Sie dem Link zu *News*, oder öffnen Sie den Ordner *Site B* auf der
   Hauptwebsite. Notieren Sie den URL-Alias von *Site B* in der Adresse (zum Beispiel `Site-B`).
6. Setzen Sie das Präfix für `site_b` und leeren Sie die Caches:

   ```bash
   php bin/php/console exp:ini set site.ini/SiteAccessSettings/PathPrefix Site-B siteaccess:site_b
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=content
   ```

7. Öffnen Sie `/site_b/News`. Öffnen Sie dann `/site_b/Site-B/News` und vergleichen Sie.
8. Sehen Sie sich den Link zu *News* auf der Startseite von `/site_b` an: Enthält er noch `Site-B`?
9. Machen Sie die Änderung rückgängig, wenn Ihr Trainer Sie darum bittet:
   `php bin/php/console exp:ini rem site.ini/SiteAccessSettings/PathPrefix siteaccess:site_b`, und leeren Sie dann die
   Caches erneut.

## Erwartetes Ergebnis

- Schritt 2: `/site_b` und `/site_b_ger` antworten mit ihren eigenen Website-Namen, `/admin` zeigt die Anmeldeseite,
  `/no_such_site` antwortet aus dem Standard-Siteaccess (dort meist eine Seite "nicht gefunden"), und `/` zeigt die
  Startseite des Standard-Siteaccess.
- Schritt 7: `/site_b/News` zeigt den Ordner *News*; die Links auf den Seiten von `site_b` enthalten kein `Site-B` mehr.
- Der Trainer prüft das, indem er jede Person bittet, vor dem Drücken der Eingabetaste zu sagen, welcher Siteaccess
  `/site_b_ger/News` beantworten wird, und es dann im Browser bestätigt.

## Quiz

1. Was bedeutet `MatchOrder=uri;host;port`?
2. Welcher Siteaccess beantwortet bei `URIMatchType=element` die Adresse `/site_b/news`?
   A. `site` B. `site_b` C. `news` D. der `DefaultAccess`
3. Eine Prüfung findet den Namen `shop`, der nicht in `AvailableSiteAccessList[]` steht. Was passiert als Nächstes?
4. Was ändert `PathPrefix`?
5. Welche Einstellung listet Pfade, die trotz eines `PathPrefix` ihre vollständige Adresse behalten?

<details>
<summary>Antworten</summary>

1. Der Kernel versucht zuerst die URI-Prüfung, dann die Host-Prüfung, dann die Port-Prüfung; die erste, die einen
   verfügbaren Siteaccess findet, gewinnt (`settings/site.ini`).
2. **B.** Das erste Pfadelement nennt den Siteaccess, und der Kernel liefert darin dann `news` aus.
3. Die Prüfung zählt nicht; die nächste Prüfung wird versucht, und wenn keine passt, antwortet `DefaultAccess`.
4. Es blendet diesen Teil am Anfang der URL-Aliase für den Siteaccess aus, sodass der eigene Ordner der Website nicht
   in ihren Adressen erscheint (Kommentar in `settings/site.ini`).
5. `PathPrefixExclude[]`.

</details>

## Weiterführende Informationen

- [10.2 Siteaccesses und Website-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site).
- [URL-Aliase und sprechende URLs](../../../../../eng-us/help/creators/website-creators/url-aliases-and-nice-urls.md): wie Aliase
  entstehen.
- [URLs und Aliase](../../../../anleitungen/urls-und-aliase.md): die Referenz für Administratoren.
- [Live gehen: Hosts, HTTPS und Siteaccesses](../../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md).

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
