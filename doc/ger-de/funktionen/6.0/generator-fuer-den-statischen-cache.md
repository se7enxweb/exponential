# Generator für den statischen Cache

Diese Seite ist für Administratoren von Websites mit überwiegend öffentlichen, selten geänderten Seiten, die möchten, dass
diese so schnell ausgeliefert werden, wie der Webserver eine Datei senden kann. Der statische Cache macht aus Seiten
einfache Dateien, die der Webserver senden kann, ohne Exponential überhaupt zu starten. In 6.0 durchläuft der Generator
Ihre Website so, wie es ein Besucher täte, zeigt während der Arbeit den Fortschritt, und die mitgelieferten Standards
erzeugen jetzt einen funktionierenden Cache, statt still nichts zu erzeugen.

## Was Sie bekommen

- **Setup > Cache > Statischer Inhalts-Cache**: Wählen Sie eine Website, setzen Sie ein Seitenlimit und eine Link-Tiefe,
  drücken Sie *Neu erstellen* und sehen Sie zu, wie jede Seite gespeichert wird. Eine Schaltfläche zum Anhalten beendet
  den Lauf vorzeitig.
- **`bin/php/makestaticcache.php`**: derselbe Durchlauf auf der Kommandozeile.
- **Webserver-Regeln**, die eine Anfrage aus der gespeicherten Datei beantworten, sodass eine zwischengespeicherte Seite
  einen Dateizugriff kostet.

## In der Administrationsoberfläche erzeugen

1. Öffnen Sie **Setup > Cache**.
2. Wählen Sie im Abschnitt *Statischer Inhalts-Cache* die zu erzeugende Website. Angeboten werden nur Siteaccesses, die
   jedem ausgeliefert werden können: Einer, der eine Anmeldung verlangt, wird ausgelassen, weil seine Seiten pro Benutzer
   sind.
3. Setzen Sie die Grenzen (unten) und drücken Sie *Neu erstellen*.
4. Lesen Sie die Live-Liste der gespeicherten Seiten. Drücken Sie *Anhalten*, um den Lauf zu beenden.

| Feld | Standard | Grenzen | Bedeutung |
|---|---|---|---|
| Seitenlimit | 2500 | 1 bis 20000 | Die meisten Seiten, die ein Lauf speichert. |
| Link-Tiefe | 12 | 0 bis 30 | Wie viele Links von der Wurzel der Website entfernt der Durchlauf folgt. |

Ein Lauf leert zuerst die gespeicherten Seiten der Website (der Parameter `purge` des Streams, standardmäßig an), sodass
gelöschte Seiten nicht liegen bleiben. Die Ansicht läuft unter der vorhandenen Richtlinie `managecache` von
`setup/cache`; niemand, der keine Caches leeren kann, kann einen erzeugen.

### Warum er die Website durchläuft

Ein älterer Ansatz erweiterte `CachedURLArray` gegen die Tabelle der URL-Aliase der ganzen Datenbank. Auf einer
Installation mit mehreren Websites bietet das jedem Siteaccess jeden Alias an, sodass eine Website nach den Seiten einer
anderen gefragt wird und ihre eigenen verfehlt. Der Durchlauf beginnt an der eigenen Wurzel der Website und folgt nur
Links, die die Website selbst schreibt, sodass er genau die Adressen besucht, die ein Besucher anfragen kann, in der Form,
in der der Besucher sie anfragt. Jede geholte Seite wird gespeichert, sodass keine Seite zweimal angefragt wird.

## Auf der Kommandozeile erzeugen

```bash
php bin/php/makestaticcache.php --help --allow-root-user
php bin/php/makestaticcache.php --site=user --allow-root-user
php bin/php/makestaticcache.php --site=user --site=intranet --max-pages=500 --max-depth=6 --allow-root-user
```

`--allow-root-user` ist nur nötig, wenn Sie als root arbeiten. `--site` nennt einen Siteaccess und kann wiederholt werden;
lassen Sie es weg, um jeden öffentlichen Siteaccess zu erzeugen. `--max-pages` (Standard 2500) und `--max-depth` (Standard
12) haben dieselben Grenzen wie das Formular der Administrationsoberfläche. `--keep` fügt zum Gespeicherten hinzu, statt
es zu ersetzen; `-f`/`--force` wird aus Kompatibilitätsgründen angenommen (ein Lauf ersetzt immer, was er erzeugt, sofern
nicht `--keep` angegeben ist). Die Ausgabe von `--help` oben wurde auf dieser Installation ausgeführt und führt genau diese
Optionen auf.

## Die Dateien ausliefern, ohne das CMS zu starten

Dateien zu erzeugen ist die halbe Arbeit. Die andere Hälfte ist, dem Webserver zu sagen, dass er sie verwenden soll.

- **Apache mit `.htaccess`**: Kopieren Sie `.htaccess_root_static` statt `.htaccess_root` nach `.htaccess`. Es ist
  `.htaccess_root` plus die Regeln zum Entlasten, sodass das Zurückwechseln das Kopieren der anderen Datei darüber ist.
  Prüfen Sie die eine Zeile `RewriteRule ^ - [E=EZ_VAR_SITE:site]`: Der Wert ist `site.ini` `[FileSettings] VarDir` ohne
  das führende `var/`; eine Standardinstallation (`var/site`) braucht keine Änderung.
- **Apache oder ein anderer virtueller Host**: Ein vhost liest keine `.htaccess`; verwenden Sie dieselben Regeln aus
  `doc/eng-us/examples/staticcache_offload.conf`.

Eine Anfrage wird nur dann aus einer Datei beantwortet, wenn sie ein einfaches `GET` oder `HEAD` ist, keine Abfrage hat
und keine Sitzung trägt. Formulare, Suchen und jeder Angemeldete erhalten immer die Live-Seite.

## Einstellungen (`settings/staticcache.ini`, Block `[CacheSettings]`)

| Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|
| `HostName` | leer | global | Veraltet. Lassen Sie ihn leer, damit jeder Siteaccess von seiner eigenen `site.ini [SiteSettings] SiteURL` geholt wird. Er wurde früher als `localhost` ausgeliefert, was jeden Abruf an das schickte, was für diesen Namen antwortet. |
| `SourceProtocol` | `http` | global | `http` oder `https`; alles andere bedeutet `http`. Der Abruf folgt Weiterleitungen, sodass `http` auf einer Website funktioniert, die auf https weiterleitet. Verwenden Sie `https`, wo einfaches http abgelehnt wird. |
| `StaticStorageDir` | `static` | global | Wohin Seiten geschrieben werden. Ein relativer Name wird unter dem var-Verzeichnis des Siteaccess aufgelöst, sodass `static` zu `var/<site>/static` wird. Ein absoluter Pfad oder einer, der bereits mit `var/` beginnt, wird so verwendet, wie er angegeben ist. |
| `MaxCacheDepth` | `12` | global | URLs mit mehr Segmenten als diesem werden weder zwischengespeichert noch beim Veröffentlichen erneuert. War `3`. |
| `CachedURLArray[]` | `/` und `/*` | global | Zwischenzuspeichernde URLs; `*` nimmt einen Teilbaum, ein bloßes `/*` die ganze Website. War `/`, `/news*`, `/weblog*`. |
| `CachedSiteAccesses[]` | leer | global | Zu erzeugende Siteaccesses. Leer bedeutet jeder Eintrag aus `site.ini [SiteAccessSettings] RelatedSiteAccessList` (oder `AvailableSiteAccessList`) außer denen mit `RequireUserLogin=true` oder einer `SiteURL`, die noch `example.com` ist. |

Drei weitere Schlüssel der mitgelieferten Datei sind älter und unverändert:

| Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|
| `AlwaysUpdateArray[]` | `/` | global | URLs, die bei jedem Veröffentlichen erneuert werden, gleich, was veröffentlicht wurde. |
| `CronjobCacheClear` | `disabled` | global | `enabled` lässt den Cronjob-Teil `staticcache_cleanup` das Leeren übernehmen statt des Veröffentlichens. |
| `AppendGeneratedTime` | `true` | global | Hängt jeder gespeicherten Seite einen Kommentar `<!-- Generated: ... -->` an. |

Überschreiben Sie in `settings/override/staticcache.ini.append.php` oder pro Siteaccess.

## Erneuern beim Veröffentlichen: jede einmal, parallel (01.10.2026)

Mit eingeschaltetem statischem Cache erneuert ein Veröffentlichen die Seiten, die es berührt. Ein Veröffentlichen eines
Produkts ohne Änderung verbrachte früher 4975 ms einer Anfrage von 6 s damit, 24 Seiten zu erneuern: Jede wurde zweimal
über HTTP geholt (eine Prüfung mit HEAD, die die Website genauso vollständig ausgibt, dann das GET), eine nach der anderen,
und zehn davon waren bei jedem Veröffentlichen 404. `eZStaticCache::fetchPages()` holt jetzt jede Seite einmal, mehrere
gleichzeitig, und nur eine Antwort 2xx zählt. Dasselbe Veröffentlichen erneuert 14 Seiten in etwa 0,5 s und speichert
dieselben 32 Dateien. Ohne curl wird das alte Prüfen und Holen verwendet.

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/staticcache.ini` | `CacheSettings` | `FetchConcurrency` | `8` im Code (nicht in der mitgelieferten Datei aufgeführt; der schnellste der gemessenen Werte 1 bis 24) | global |

`executeActions()` und der Cronjob-Teil `staticcache_cleanup` holen über dieselbe Methode. Ein Siteaccess mit einem
`PathPrefix` speichert nur seine Startseite, die Seiten unter dem Präfix und Seiten, die `PathPrefixExclude` nennt: Eine
Seite einer Website wird nicht mehr unter dem Präfix einer anderen Website angefragt.

## Aktualisieren

Die Standards haben sich geändert; siehe [bc: Standards des statischen Caches](../../../eng-us/bc/6.0/static-cache-defaults.md).

## Verwandte Seiten

- [Seiten vorladen](ansicht-websites-vorladen.md), [Befehl Vorlader für den Website-Cache](../../../eng-us/bc/6.0/preload.md)
- [Cache aus der Konsole](../../../eng-us/bc/6.0/cache-console.md) (Gruppe statischer Cache von `exp:cache`), [HTTP-Caching](../../../eng-us/bc/6.0/http-caching.md)
- [Velocity und der Opcode-Cache](velocity-opcode-cache-und-profil.md), [Leeren von Caches, das Verzeichnisse beiseiteschiebt](cache-leeren-durch-umbenennen.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [September 2026, erste Hälfte: Caches, die Sie sehen können](../../../eng-us/history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run), [Oktober 2026](../../../eng-us/history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
