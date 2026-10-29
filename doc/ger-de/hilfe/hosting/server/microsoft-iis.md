---
title: Microsoft IIS
audience: hosting
category: hosting
order: 70
summary: Was die Quellen von Exponential über Windows-Hosting mit Microsoft IIS und FastCGI-PHP sagen - der Status, was der Kernel erkennt, was Windows fehlt, das in web.config nachzubildende Routing und die Tests, bevor Sie es anbieten.
keywords: IIS, Microsoft IIS, Windows, FastCGI, cgi-fcgi, URL Rewrite, web.config, IIS_WasUrlRewritten, ForceVirtualHost, nicht threadsicheres PHP, pcntl, WSL2, Stufe 3, non-thread-safe PHP, tier 3
type: help
---
# Microsoft IIS

Diese Seite ist für Hosting-Unternehmen, die Windows-Hosting anbieten und wissen wollen, ob Exponential Teil davon sein kann. Sie
beantwortet: was die Quellen über IIS sagen, was unter Windows anders funktioniert, welches Routing eine IIS-Website nachbilden muss und
was Sie testen. Die eigenen Verwaltungswerkzeuge von IIS werden hier nicht beschrieben; sehen Sie dafür in der Dokumentation von Microsoft
nach.

## Inhalt

1. [Status](#status)
2. [Was Windows ändert](#was-windows-ändert)
3. [Was der Kernel über IIS weiß](#was-der-kernel-über-iis-weiß)
4. [Nachzubildendes Routing](#nachzubildendes-routing)
5. [Tests, bevor Sie es anbieten](#tests-bevor-sie-es-anbieten)
6. [Alternativen auf Windows-Maschinen](#alternativen-auf-windows-maschinen)
7. [Checkliste](#checkliste)

## Status

| Quelle | Aussage |
|---|---|
| `README.md`, Abschnitt „The traditional options“ | Microsoft IIS schreibt mit dem URL Rewrite Module (`web.config`) um, für Windows-Hosting |
| [Unterstützung von Betriebssystemen, Webserver](../../betriebssystemunterstuetzung.md#webserver) | Microsoft IIS mit FastCGI: **Stufe 3**; das Modul URL Rewrite (`web.config`); die Anfragebehandlung des Kernels hat Tests für Servervariablen von IIS 7 |
| [Unterstützung von Betriebssystemen, PHP-SAPIs](../../betriebssystemunterstuetzung.md#php-sapis) | CGI / FastCGI (`cgi-fcgi`) ist die SAPI für IIS unter Windows |
| [Unterstützung von Betriebssystemen, Windows](../../betriebssystemunterstuetzung.md#windows) | nutzen Sie die offiziellen PHP-8-Builds für Windows (x64, nicht threadsicher für FastCGI) mit Apache oder IIS |

Mit Exponential wird keine `web.config` ausgeliefert. Stufe 3 bedeutet: sollte funktionieren, hier nicht getestet
([Die vier Stufen](../../betriebssystemunterstuetzung.md#die-vier-stufen)).

## Was Windows ändert

Aus [Unterstützung von Betriebssystemen, Windows](../../betriebssystemunterstuetzung.md#windows):

- `pcntl` und `posix` gibt es unter Windows nicht. Asynchrones Veröffentlichen (`bin/php/ezasynchronouspublisher.php`) ist nicht
  verfügbar.
- Velocity führt seine Worker unter Windows als Unterprozesse `php-cgi` aus, ohne gemeinsame Nutzung von Speicher durch Copy-on-Write.
  Seine Windows-Binärdateien in einer Datei werden **ohne `intl`** gebaut, das die Einrichtung von Exponential verlangt; betreiben Sie
  Velocity unter Windows über das Phar mit dem offiziellen PHP statt über seine Binärdatei (sollte funktionieren, ist aber laut den
  eigenen Plattformhinweisen der Engine ungetestet).
- Die Dokumentation und die Shell-Befehle in dieser Hilfe sind für Linux geschrieben; übertragen Sie Pfade und Berechtigungen.
- Der Setup-Assistent sucht ImageMagick auch unter Windows-Namen (`convertim.exe`, `convert.exe`, in `settings/setup.ini`).

> **Hinweis**
> Für einen Hoster ist die praktische Folge, dass sich ein Windows-Tarif wie ein klassischer FastCGI-Tarif verhält: kein Worker-Pool von
> Velocity mit gemeinsamem Speicher, kein asynchrones Veröffentlichen. Nennen Sie das in der Tarifbeschreibung.

## Was der Kernel über IIS weiß

- `eZSys` (in `lib/ezutils/classes/ezsys.php`) erkennt umgeschriebene Anfragen von IIS an der Servervariablen `IIS_WasUrlRewritten` und
  arbeitet dann im Modus mit virtuellem Host, wie im `.htaccess`-Modus von Apache. Eine Website kann diesen Modus auch mit
  `site.ini [SiteAccessSettings] ForceVirtualHost=true` erzwingen.
- Die Testdaten der Servervariablen in den Tests des Kernels enthalten IIS 7 und IIS 7.5 mit FastCGI (`tests/tests/lib/ezutils/server/`),
  im Modus mit und ohne virtuellen Host.

Diese Testdaten wurden mit alten PHP-Versionen aufgezeichnet; sie decken ab, wie IIS seine Variablen meldet, nicht ein aktuelles IIS mit
PHP 8. Testen Sie Ihren eigenen Stack.

## Nachzubildendes Routing

IIS liest `.htaccess` nie, `exp:setup:htaccess` schützt eine IIS-Website also nicht
([.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)).
Welchen Regelsatz Sie für IIS auch schreiben, er muss tun, was `.htaccess_root` tut
([Kapitel 8, Wie eine Anfrage ausgeliefert wird](../../../installation/08-die-website-ausliefern.md#82-wie-eine-anfrage-beantwortet-wird)):

| Anfrage | Muss gehen an |
|---|---|
| eine Datei oder ein Verzeichnis mit Punkt (`.git`, `.env`, `.htaccess`), außer `.well-known` | 404 |
| eine vorhandene `.php`, `.phtml` oder `.phar` unterhalb der Wurzel | 404 |
| `/api/...` | `index_rest.php` |
| `/content/treemenu...`, `/<siteaccess>/content/treemenu...` | `index_treemenu.php` |
| ein aufgeführter Asset-Pfad (Stylesheets, Bilder, Skripte und Schriften von Designs und Erweiterungen; `var/*/storage/images`; Originalbilder; gespeicherte PDF-Exporte; öffentliche Caches; `share/icons`; die Icon-Themes der Erweiterungen, `extension/<name>/icons/`, Bilder mit genau einer Dateiendung; Paketvorschauen; `favicon.ico`, `robots.txt`, `index.js`, `sw.js`, `w3c/p3p.xml`) | als Datei gesendet |
| alles andere | `index.php` |

Die regulären Ausdrücke jeder Zeile stehen in `.htaccess_root` und, für die Asset-Liste, in `expVelocity::STATIC_PATHS`
(`kernel/classes/expvelocity.php`). Der abgeleitete nginx-Block auf [nginx mit PHP-FPM](nginx-mit-php-fpm.md) zeigt dieselbe Liste
bereits einmal übersetzt.

> **Warnung**
> Behalten Sie die Reihenfolge bei: die Regeln für Dateien mit Punkt und für Skripte zuerst, die Auffangregel zuletzt. Eine Auffangregel
> an erster Stelle oder eine Asset-Regel, die `.php` trifft, kann Dateien ausführen oder preisgeben.

## Tests, bevor Sie es anbieten

Führen Sie die vier Prüfungen des Routings aus [Webserver und Engines](README.md#was-jede-einrichtung-gemeinsam-hat) aus, dann:

- Die Startseite, eine Inhaltsseite, `/user/login` und die Administration antworten.
- Ein hochgeladenes Bild wird angezeigt, und eine hochgeladene Datei lässt sich über `content/download` herunterladen.
- Die geplante Aufgabe, die `runcronjobs.php` ausführt, funktioniert mit dem PHP der Kommandozeile ([Cronjobs](../betrieb/cronjobs.md)).
- PHP für Web und Kommandozeile hat dieselbe Version mit denselben Erweiterungen
  ([Kapitel 2, PHP für Kommandozeile und Web](../../../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)).
- 64-Bit-PHP: 32-Bit-PHP beschränkt eine Website auf 30 Sprachen
  ([Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md#prozessorarchitekturen)).

## Alternativen auf Windows-Maschinen

- **Apache mit FastCGI-PHP** unter Windows: Die mitgelieferte `.htaccess_root` gilt direkt (kopiert mit
  `php bin/php/console exp:setup:htaccess --apply`).
- **WSL2** führt die Linux-Stufen aus und ist der einfachere Weg für die Entwicklung unter Windows.
- **Eine virtuelle Linux-Maschine oder ein Container** auf dem Windows-Host, wenn sich der Tarif so bauen lässt
  ([Container, Docker und Podman](../modelle/container-docker-und-podman.md)).

## Checkliste

- [ ] Offizieller nicht threadsicherer PHP-8-Build für x64 mit FastCGI; `intl` und die anderen erforderlichen Erweiterungen vorhanden.
- [ ] Ein Regelsatz, der die Routing-Tabelle oben nachbildet, mit den Prüfungen des Routings getestet.
- [ ] Die Tarifbeschreibung nennt, dass der Worker-Pool von Velocity und asynchrones Veröffentlichen nicht dazugehören.
- [ ] Geplante Aufgaben für Cronjobs eingerichtet und getestet.
- [ ] Vor dem Verkauf auf Staging getestet (Stufe 3).

## Verwandte Seiten

- [Einen Webserver wählen](einen-webserver-waehlen.md)
- [Betriebssysteme für Hoster](../grundlagen/betriebssysteme-fuer-hoster.md)
- [Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md)
- [Kapitel 8: Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)
- [.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
