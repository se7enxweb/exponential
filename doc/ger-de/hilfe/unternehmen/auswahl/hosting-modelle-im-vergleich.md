---
title: Hosting-Modelle im Vergleich
audience: businesses, project-manager, hosting
category: business-solutions
order: 20
summary: Shared Hosting, VPS, dedizierte Server, Managed Hosting, Platform as a Service, Container und Kubernetes im Vergleich für ein Unternehmen, das Exponential betreibt - was jedes Modell erlaubt, wer die Arbeit macht, die Risiken und zu welcher Art von Organisation es passt - qualitativ, aus der Hosting-Dokumentation des Projekts.
keywords: Hosting-Modelle, Shared Hosting, VPS, dedizierter Server, Managed Hosting, Platform as a Service, PaaS, Container, Docker, Kubernetes, Vergleich, Velocity, SQLite, Isolation, Abwägungen, hosting models, shared hosting, dedicated server, managed hosting, containers, comparison, isolation, trade-offs
type: help
---
# Hosting-Modelle im Vergleich

Diese Seite ist für Inhaber und Führungskräfte, denen Angebote für sehr unterschiedliche Arten von Hosting vorliegen
und die sie vergleichen müssen. Sie übersetzt den technischen Vergleich des Projekts in die Sprache des Geschäfts:
was jedes Modell erlaubt, wer die Arbeit macht, was schiefgehen kann und zu welchen Organisationen es passt. Sie nennt
keine Preise; das Projekt veröffentlicht keine ([Ein Hosting-Modell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hosting/control-panels/plesk.md), [cPanel](../../hosting/control-panels/cpanel.md), [DirectAdmin](../../hosting/control-panels/directadmin.md), [ISPConfig](../../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Die Modelle in einer Tabelle](#die-modelle-in-einer-tabelle)
2. [Jedes Modell aus Sicht des Geschäfts](#jedes-modell-aus-sicht-des-geschäfts)
3. [Wer die Arbeit macht](#wer-die-arbeit-macht)
4. [Welches Modell zu welcher Organisation passt](#welches-modell-zu-welcher-organisation-passt)
5. [Das Modell später wechseln](#das-modell-später-wechseln)

## Die Modelle in einer Tabelle

Technische Fakten aus [Ein Hosting-Modell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md#die-vergleichstabelle):

| | Shared | VPS | Dediziert | Container | Kubernetes |
|---|---|---|---|---|---|
| Ihr Zugang | Panel, oft keine Shell | root oder ein sudo-Benutzer | root | das Image und seine Volumes | Manifeste, Images, Volumes |
| Velocity (langlaufender Prozess) | meist nicht erlaubt | ja | ja | ja | ja |
| Übliche Auslieferung | Apache oder LiteSpeed mit `.htaccess` | Velocity, oder Apache/nginx mit PHP-FPM | wie VPS | Velocity im Container | Velocity oder PHP-FPM |
| SQLite 3 | ja, eine Website pro Datei | ja | ja | nur ein schreibender Container | nur ein schreibendes Replikat |
| Mehrere Webknoten für eine Website | nein | nein | nein | braucht einen Datenbankserver und gemeinsamen Speicher | ebenso |

Die eigene Stufenskala des Projekts für diese Konfigurationen reicht von 1 (getestet) bis 3 (sollte funktionieren);
planen Sie eine Testinstallation, bevor Sie sich auf ein Modell der Stufe 3 verlassen
([Ein Hosting-Modell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)).

## Jedes Modell aus Sicht des Geschäfts

**Shared Hosting.** Viele Kunden auf einem Server, vom Hoster über ein Panel verwaltet. Einfach und meist die kleinste
Rechnung. Exponential funktioniert mit Apache oder LiteSpeed und dem Einrichtungsassistenten; Velocity ist meist nicht
möglich, weil langlaufende Prozesse nicht erlaubt sind ([Shared Hosting](../../hosting/modelle/shared-hosting.md)). Die
Trennung von anderen Kunden hängt davon ab, wie der Hoster Konten und PHP-Pools einrichtet.

**VPS (Virtual Private Server).** Ihre eigene virtuelle Maschine. Sie können Velocity betreiben und PHP-Versionen
wählen. Jemand muss sie betreiben: Sie, Ihr Partner oder der Hoster als verwalteter Dienst
([VPS-Hosting](../../hosting/modelle/vps-hosting.md)).

**Dedizierter Server.** Eine ganze physische Maschine. Mehr Kapazität und Kontrolle, dieselben Betriebspflichten wie
bei einem VPS ([Dedizierte Server](../../hosting/modelle/dedizierte-server.md)).

**Managed Hosting.** Keine eigene Technik, sondern ein Dienst auf einer der anderen: Der Hoster übernimmt Updates,
Backups, Monitoring und mehr und verwendet dafür die Werkzeuge, die Exponential bereitstellt (Updates mit `ezupdate`,
die Upgrade-Prüfung, den Wartungsmodus, den Log-Viewer, das Prüfprotokoll)
([Managed Hosting](../../hosting/modelle/managed-hosting.md)).

**Platform as a Service.** Eine Plattform führt Ihren Code aus; in den Quellen Stufe 3. Ein schreibgeschütztes oder
temporäres Dateisystem verlangt `var/` auf dauerhaftem Speicher und einen Datenbankserver
([Platform as a Service](../../hosting/modelle/platform-as-a-service.md)).

**Container (Docker, Podman).** Die Website in einem Container-Image, in den Quellen Stufe 2. Gut für wiederholbare
Deployments; Volumes müssen die Daten halten ([Container: Docker und Podman](../../hosting/modelle/container-docker-und-podman.md)).

**Kubernetes.** Viele orchestrierte Container; Stufe 3. Mehrere Replikate brauchen einen Datenbankserver,
gemeinsamen Dateispeicher, gemeinsame Sitzungen und Sorgfalt bei Cron ([Kubernetes](../../hosting/modelle/kubernetes.md)).
Lohnt sich, wenn Sie Kubernetes bereits betreiben, selten für eine einzelne kleine Website.

## Wer die Arbeit macht

| Aufgabe | Shared | VPS oder dediziert | Managed | Container oder Kubernetes |
|---|---|---|---|---|
| Server- und Betriebssystem-Updates | Hoster | Sie oder Ihr Partner | Hoster | Ihr Plattformteam |
| PHP-Updates | Hoster (Sie wählen eine Version) | Sie | Hoster | in Ihrem Image |
| Exponential-Updates | Sie | Sie | Hoster, wenn vereinbart | Sie, in Ihrem Image |
| Backups | Hoster, prüfen Sie, was enthalten ist | Sie | Hoster | Sie, von Volumes und Datenbank |
| Wiederherstellungstests | nachfragen | Sie | Hoster, wenn vereinbart | Sie |
| Monitoring | nachfragen | Sie | Hoster | Sie |

Halten Sie bei jedem Modell schriftlich fest, wer welche Aufgabe übernimmt
([Grundlagen zu Verträgen und SLAs](grundlagen-zu-vertraegen-und-slas.md)).

## Welches Modell zu welcher Organisation passt

| Organisation | Passt oft gut | Warum |
|---|---|---|
| Kleines Unternehmen, kleines Budget, keine technischen Mitarbeiter | Managed Hosting, oder Shared Hosting mit einem Partner | jemand anderes betreibt die Maschine |
| Kleiner Shop mit saisonalen Spitzen | VPS mit Velocity, verwaltet oder mit einem Partner | der Antwortcache von Velocity bedient Seiten aus dem Cache ohne Worker (`README.md`) |
| Gemeinnützige Organisation oder Verein mit Ehrenamtlichen | Managed oder Shared Hosting | wenige Pflichten bleiben bei den Ehrenamtlichen |
| Unternehmen mit eigenem IT-Team | VPS oder dediziert | volle Kontrolle, Velocity, eigene Abläufe |
| Mehrere Marken oder Websites | ein VPS oder dedizierter Server mit Multisite | eine Installation, viele Websites ([Multisite](../multisite/README.md)) |
| Organisation, die bereits Container betreibt | Container oder Kubernetes | passt zum bestehenden Betrieb |
| Websites, die eine strikte Trennung zwischen Kunden brauchen | eine Instanz pro Website, jede unter eigenem Benutzer | „ein einzelner Serverprozess, der mehrere Websites ausliefert, ist eine Bequemlichkeit beim Routing, keine Sicherheitsgrenze“ (`README.md`) |

Das sind qualitative Vorschläge, keine Regeln. Prüfen Sie jeden gegen Ihre eigenen Anforderungen und Angebote.

## Das Modell später wechseln

Der Umzug einer Website zwischen Hostern oder Modellen ist dokumentiert und ändert ihre Version nicht
([Eine Website zwischen Hostern umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)). Auch ein Wechsel der
Datenbank-Engine ist möglich ([Die Datenbank-Engine einer Kunden-Website wechseln](../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md)).
Beginnen Sie also mit dem einfachsten Modell, das die heutigen Anforderungen erfüllt, und wechseln Sie, wenn Messungen
es nahelegen.

## Verwandte Seiten

- [Hosting auswählen](hosting-auswaehlen.md)
- [Kostentreiber beim Hosting](../kosten/kostentreiber-beim-hosting.md)
- [Clustering und Hochverfügbarkeit](../zuverlaessigkeit/clustering-und-hochverfuegbarkeit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
