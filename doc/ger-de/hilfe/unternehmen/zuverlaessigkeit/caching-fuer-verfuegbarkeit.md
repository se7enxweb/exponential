---
title: Caching für Verfügbarkeit
audience: businesses, admin, website-creator, hosting
category: business-solutions
order: 110
summary: Warum Caches ein Werkzeug für Zuverlässigkeit sind und nicht nur für Geschwindigkeit, welche Cache-Schichten Exponential hat und welche standardmäßig eingeschaltet sind, wie bedingte Anfragen und stale-while-revalidate eine stark besuchte Website schützen, und die Regeln für sicheres Leeren von Caches.
keywords: Cache, Caching, Verfügbarkeit, View-Cache, HTTP-Cache, Antwortcache, statischer Cache, bedingte Anfragen, Cache leeren, Vorwärmen, cache, caching, availability, view cache, HTTP cache, response cache, static cache, conditional requests, 304, ETag, stale-while-revalidate, cache clear, warming
type: help
---
# Caching für Verfügbarkeit

Diese Seite richtet sich an Führungskräfte, die wollen, dass die Website erreichbar bleibt, wenn viele Kunden
gleichzeitig kommen, und an die Person im Betrieb, die die Caches konfiguriert. Sie erklärt, warum Caching eine
Maßnahme für Zuverlässigkeit ist, welche Schichten Exponential bietet, was ab Werk eingeschaltet ist und welche wenigen
Regeln verhindern, dass Caches selbst Probleme verursachen. Die vollständige technische Übersicht finden Sie unter
[Cache-Schichten](../../hosting/leistung/cache-schichten.md) und [Caches](../../../anleitungen/caches.md).

![Systeminformationen mit den Zustandsprüfungen](../../bilder/help/bu-sysinfo.webp)

## Inhalt

1. [Warum Caching eine Maßnahme für Zuverlässigkeit ist](#warum-caching-eine-maßnahme-für-zuverlässigkeit-ist)
2. [Die Schichten in einfachen Worten](#die-schichten-in-einfachen-worten)
3. [Was ab Werk eingeschaltet ist](#was-ab-werk-eingeschaltet-ist)
4. [Im Cache eingebauter Schutz](#im-cache-eingebauter-schutz)
5. [Angemeldete Kunden](#angemeldete-kunden)
6. [Caches sicher leeren](#caches-sicher-leeren)
7. [Vorwärmen nach dem Leeren](#vorwärmen-nach-dem-leeren)
8. [Checkliste](#checkliste)

## Warum Caching eine Maßnahme für Zuverlässigkeit ist

Jede Seite, die aus einem Cache beantwortet wird, braucht weder die Datenbank noch die Template-Engine noch einen
PHP-Worker. Das hat zwei Wirkungen, die einem Unternehmen wichtig sind:

- **Reserven unter Last.** Derselbe Server kann weit mehr Besucher bedienen, wenn die meisten Seiten aus einem Cache
  kommen, sodass eine Lastspitze seltener zu einem Ausfall wird.
- **Weniger bewegliche Teile pro Anfrage.** Die Datenbankdokumentation sagt es deutlich: „Die meisten Anfragen erreichen
  die Datenbank nie“, weil der Antwortcache, der View-Cache und der Template-Block-Cache zuerst antworten
  ([Datenbankunterstützung, Leistung und Skalierung](../../datenbankunterstuetzung.md#performance-und-skalierung)).

Eine Grenze sollten Sie im Kopf behalten: Eine gecachte Startseite kann grün bleiben, während die Datenbank ausgefallen
ist. Deshalb lautet die Empfehlung für das Monitoring, auch die Anmeldeseite der Administration zu prüfen
([Health-Checks und Monitoring](health-checks-und-monitoring.md#prüfungen-von-außen)).

## Die Schichten in einfachen Worten

Von außen nach innen ([Cache-Schichten, die Schichten auf einen Blick](../../hosting/leistung/cache-schichten.md#die-schichten-auf-einen-blick)):

| Schicht | In einfachen Worten |
|---|---|
| Browser-Cache | Der Browser des Besuchers behält Bilder, Stile und Skripte |
| CDN oder Caching-Proxy | Ein optionaler Dienst vor Ihrer Website; nicht Teil von Exponential |
| Antwortcache von Velocity | Velocity beantwortet wiederholte Anfragen nach öffentlichen Seiten selbst, noch vor jedem Worker |
| Rollenbewusster HTTP-Cache | Speichert ganze Seiten je Berechtigungskontext, für anonyme und angemeldete Besucher |
| Statischer Cache | Öffentliche Seiten, als einfache Dateien gespeichert, die der Webserver direkt ausliefert |
| Content-View-Cache | Der gerenderte Inhalt jeder Seite, je Sprache und Rolle |
| Template-Block-Cache | Teile des Seitenlayouts, etwa Menüs |
| SQL-Abfrage-Cache | Beantwortet wiederholte Lesezugriffe auf die Datenbank |
| OPcache und APCu | Die eigenen Caches von PHP für kompilierten Code und kleine Werte |

Die beiden Schichten, die antworten, ohne die Anwendung überhaupt auszuführen, der statische Cache und der Antwortcache
von Velocity, haben eine eigene Seite: [Statischer Cache und Antwortcache](statischer-cache-und-antwortcache.md).

## Was ab Werk eingeschaltet ist

Aus den mitgelieferten Einstellungen ([Cache-Schichten](../../hosting/leistung/cache-schichten.md#die-schichten-auf-einen-blick),
abgeglichen mit `settings/site.ini`, `settings/velocity.ini` und `settings/querycache.ini`):

| Schicht | Ab Werk |
|---|---|
| Content-View-Cache | Ein (`[ContentSettings] ViewCaching=enabled`) |
| Template-Block-Cache | Ein |
| Antwortcache von Velocity | Ein, wenn Velocity die Website ausliefert (`[CacheSettings] Enabled=enabled`) |
| Rollenbewusster HTTP-Cache | Aus, bis er eingeschaltet wird |
| Statischer Cache | Aus (`[ContentSettings] StaticCache=disabled`) |
| SQL-Abfrage-Cache | Aus (`Mode=off`) |

**Setup > Systeminformationen** warnt, wenn View-Caching, Template-Kompilierung, der Template-Cache oder der
Override-Cache ausgeschaltet ist, und rät, sie „in der Produktion einzuschalten“
([Systeminformationen, Zustandsprüfungen](../../../anleitungen/systeminformationen.md#3-zustandsprüfungen)).

## Im Cache eingebauter Schutz

Die README nennt Korrekturen in Exponential 6.0.15, die „überall gelten, wo Exponential läuft“, unter Apache und Nginx
ebenso wie unter Velocity ([README, „The part that matters even if you never run Velocity“](https://github.com/se7enxweb/exponential/blob/main/README.md#the-part-that-matters-even-if-you-never-run-velocity)):

- **Bedingte Anfragen**: „Das Neuladen eines wiederkehrenden Besuchers wird mit 202 Byte statt mit der ganzen Seite
  beantwortet, weil gecachte Seiten jetzt korrekt auf bedingte Anfragen antworten.“
- **Inhaltsbasierte Identität**: Der Neuaufbau eines Cache-Eintrags, der identische Ausgabe erzeugt hat, „zwingt nicht
  mehr jeden Besucher, ihn erneut herunterzuladen“.
- **Ein Neuaufbau statt eines Ansturms**: „Wenn eine gecachte Seite abläuft, baut eine Anfrage sie neu auf, während alle
  anderen die vorhandene Kopie erhalten, statt dass jeder anwesende Besucher sie gleichzeitig neu aufbaut.“ Das
  verhindert, dass eine beliebte Seite den Server in dem Moment überlastet, in dem ihr Cache abläuft.
- **Gemerktes „nicht gefunden“**: „Ein ‚nicht gefunden‘ wird gemerkt, statt jedes Mal von Grund auf neu aufgebaut zu
  werden.“

Für ein Unternehmen zählt der dritte Punkt am meisten: Der Moment, in dem auf einer stark besuchten Seite ein Cache
abläuft, ist eine klassische Ursache für Überlastung, und Exponential kümmert sich darum für Sie.

## Angemeldete Kunden

Angemeldete Kunden (Mitglieder, Shop-Kunden mit Konto, Redakteure) sehen Seiten, die davon abhängen, wer sie sind. Der
Antwortcache von Velocity bedient sie nie: „Angemeldete Besucher werden nie aus dem Cache bedient“
(`settings/velocity.ini`, `[CacheSettings]`). Der Content-View-Cache hilft ihnen trotzdem, weil er je Rolle und
Einschränkungen gehalten wird
([View-Cache und Cache-Blöcke](../../ersteller/website-ersteller/view-cache-und-cache-bloecke.md#der-view-cache-für-inhalte)),
und der rollenbewusste HTTP-Cache kann angemeldete Besucher je Berechtigungskontext bedienen, wenn er eingeschaltet ist.

Wenn der größte Teil Ihres Verkehrs angemeldet ist (ein Intranet, eine Mitgliederwebsite, ein Shop an der Kasse),
planen Sie die Kapazität für gerenderte Seiten, nicht für gecachte ([Lastspitzen planen](lastspitzen-planen.md)).

## Caches sicher leeren

Caches machen nur Ärger, wenn sie unbedacht geleert werden. Drei Regeln:

1. **Leeren Sie das Kleinste, das das Problem behebt.** Eine geänderte Einstellung braucht den INI-Cache, ein Template
   den Template-Cache. „Alles, wenn unsicher“ gibt es, kostet aber die nächsten Besucher je eine langsame Seite
   ([Betrieb einer Site, den richtigen Cache leeren](../../../anleitungen/betrieb-einer-website.md#2-den-richtigen-cache-leeren)).
2. **Erst hinsehen.** `--dry-run` zeigt, was ein Leeren bewirken würde, und ändert nichts.
3. **Seiten-Caches zuletzt leeren.** „Leeren Sie die HTTP- und Antwortcaches, nachdem PHP-FPM oder Velocity den neuen
   Code ausführt, sonst wird eine in der Zwischenzeit vom alten Code gerenderte Seite erneut gespeichert“ (dieselbe
   Seite). Der Deploy-Befehl erledigt das in der richtigen Reihenfolge
   ([Unterbrechungsfreie Deployments](unterbrechungsfreie-deployments.md)).

Große Caches werden zur Seite verschoben und danach gelöscht, „damit ein Leeren Anfragen nicht warten lässt“
([Cache-Leerungen, die Verzeichnisse zur Seite verschieben](../../../funktionen/6.0/cache-leeren-durch-umbenennen.md)).

## Vorwärmen nach dem Leeren

Nach einem Leeren oder einem Deployment „bezahlt der erste Besucher für das Rendern“. Wärmen Sie die Seiten selbst vor
([Betrieb einer Site, die Site vorwärmen](../../../anleitungen/betrieb-einer-website.md#5-die-site-vorwärmen-preload)):

```bash
php bin/php/preload.php --siteaccess=site --allow-root-user
```

Verwenden Sie den Namen Ihres eigenen öffentlichen Siteaccess. Dasselbe steht in der Administration als
**Seiten vorladen** zur Verfügung ([Caches vorwärmen](../../../anleitungen/caches-vorwaermen.md)).

## Checkliste

- [ ] View-Caching, Template-Kompilierung und der Template-Cache sind in der Produktion eingeschaltet (keine Warnung unter **Setup > Systeminformationen**).
- [ ] Das Team weiß, welcher Cache bei welcher Änderung zu leeren ist, und nutzt zuerst `--dry-run`.
- [ ] Seiten-Caches werden nach dem Neuladen des Codes geleert, nicht davor (der Deploy-Befehl erledigt das).
- [ ] Die Website wird nach einem vollständigen Leeren oder einem Deployment vorgewärmt.
- [ ] Die Kapazität ist für angemeldeten Verkehr geplant, der gerendert und nicht aus dem Antwortcache bedient wird.

## Verwandte Seiten

- [Statischer Cache und Antwortcache](statischer-cache-und-antwortcache.md)
- [Lastspitzen planen](lastspitzen-planen.md)
- [Leistung und Caching für Websites](../../ersteller/website-ersteller/leistung-und-caching.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
