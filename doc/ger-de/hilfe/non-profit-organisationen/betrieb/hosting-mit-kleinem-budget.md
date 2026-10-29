---
title: Hosting mit kleinem Budget
audience: non-profit, businesses, hosting, admin
category: non-profits
order: 20
summary: Hosting für die Exponential-Website einer Non-Profit-Organisation mit kleinem Budget auswählen - was jeder Hoster bieten muss, Shared Hosting, ein kleiner virtueller Server und Managed Hosting im Vergleich nach Geld, Zeit und Fähigkeiten, alles zuerst mit einem einzigen Installationsbefehl auf einem Laptop ausprobieren, Fragen an einen Hoster und wo Sparen teuer wird - ohne erfundene Preise.
keywords: Hosting, kleines Budget, günstiges Hosting, Shared Hosting, VPS, Managed Hosting, Anforderungen, PHP, SQLite, Velocity, exp:install, Fragen an einen Hoster, falsche Sparsamkeit, Non-Profit-Organisation, hosting, low budget, cheap hosting, shared hosting, managed hosting, requirements, questions for a host, false economies, non-profit
type: help
---
# Hosting mit kleinem Budget

Diese Seite richtet sich an Geschäftsführung, Schatzmeister und technisch versierte Ehrenamtliche, die das Hosting
auswählen. Sie nennt keine Preise: Vergleichen Sie aktuelle Angebote selbst und tragen Sie das Ergebnis in das
[Arbeitsblatt Förderbudget](../vorlagen/arbeitsblatt-foerderbudget.md) ein.

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

1. [Was jeder Hoster bieten muss](#was-jeder-hoster-bieten-muss)
2. [Drei Hosting-Modelle](#drei-hosting-modelle)
3. [Zuerst kostenlos ausprobieren](#zuerst-kostenlos-ausprobieren)
4. [Fragen an einen Hoster](#fragen-an-einen-hoster)
5. [Falsche Sparsamkeit](#falsche-sparsamkeit)
6. [Checkliste](#checkliste)

## Was jeder Hoster bieten muss

Aus `README.md`, „Requirements“, und [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md):

| Bedarf | Hinweis |
|---|---|
| PHP | Eine aktuelle Version mit den erforderlichen Erweiterungen ([PHP-Versionen und -Erweiterungen](../../hosting/grundlagen/php-versionen-und-erweiterungen.md)) |
| Ein Webserver | Apache, Nginx, LiteSpeed und andere funktionieren, oder das mitgelieferte Velocity, das „needs nothing installed beyond PHP“, also außer PHP nichts Installiertes braucht |
| Eine Datenbank | Optional: SQLite braucht keinen Datenbankserver; unterstützt in dieser Reihenfolge: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Beschreibbare Verzeichnisse | Für Speicher und Caches ([Beschreibbare Verzeichnisse und Berechtigungen](../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)) |
| Cron | Für Newsletter, zeitgesteuertes Veröffentlichen, Aufräumarbeiten, das Audit-Protokoll ([Cronjobs](../../../anleitungen/cronjobs.md)) |
| HTTPS | Ein Zertifikat; beim Hoster oft kostenlos |
| E-Mail | Für Formulare und Newsletter |

## Drei Hosting-Modelle

| Modell | Geld | Ihre Zeit und Fähigkeiten | Passt zu | Lesen |
|---|---|---|---|---|
| Shared Hosting | Am niedrigsten | Gering; begrenzte Kontrolle | Sehr kleinen Websites; Apache oder LiteSpeed mit `.htaccess`, SQLite oder die Datenbank des Panels; Velocity meist nicht | [Shared Hosting](../../hosting/modelle/shared-hosting.md) |
| Ein kleiner virtueller Server | Niedrig | Jemand muss den Server patchen und absichern | Den meisten kleinen Non-Profit-Organisationen mit technisch versierten Ehrenamtlichen oder einem Partner | [VPS-Hosting](../../hosting/modelle/vps-hosting.md) |
| Managed Hosting | Höher | Gering: Der Hoster patcht und sichert | Organisationen ohne technisch versierte Ehrenamtliche | [Managed Hosting](../../hosting/modelle/managed-hosting.md) |

Ausführlicher Vergleich: [Ein Hosting-Modell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md) und
[Kostentreiber beim Hosting](../../unternehmen/kosten/kostentreiber-beim-hosting.md).

> **Tipp**
> Das günstigste Modell ist das, um das sich Ihre Organisation tatsächlich kümmern kann. Ein virtueller Server, den
> niemand patcht, ist am Ende teurer als Managed Hosting.

## Zuerst kostenlos ausprobieren

`./console exp:install --apply` „installs the multisite package on **SQLite**, in `eng-US`“, installiert also das
Multisite-Paket auf **SQLite** in `eng-US`, mit einer Website und einer Administrationsoberfläche
([Exponential mit einem Befehl installieren](../../../funktionen/6.0/installation-mit-einem-befehl.md)). Ehrenamtliche können
das ganze Produkt auf einem Laptop ausprobieren, bevor etwas gekauft wird, und es dem Vorstand zeigen.

## Fragen an einen Hoster

- [ ] Welche PHP-Versionen bieten Sie an, und wie werden sie aktualisiert?
- [ ] Kann ich Cronjobs alle paar Minuten ausführen?
- [ ] Ist HTTPS enthalten und wird es automatisch erneuert?
- [ ] Wie werden Sicherungen erstellt, wie lange aufbewahrt, und wie stelle ich eine wieder her? Kann ich eine
  Wiederherstellung testen?
- [ ] Wo stehen die Server (Land), und wer verarbeitet Daten für uns? Unterschreiben Sie einen Vertrag zur
  Auftragsverarbeitung?
- [ ] Kann die Website E-Mails versenden, und mit welchen Grenzen?
- [ ] Was geschieht, wenn wir nicht mehr zahlen oder Sie schließen: Wie bekommen wir unsere Daten?

Mehr: [Was Sie Ihren Hoster fragen sollten](../../kunden/gesund-halten/was-sie-ihren-hoster-fragen-sollten.md) und
[Einen Hoster auswählen](../../kunden/partner-und-hoster/einen-hoster-auswaehlen.md).

## Falsche Sparsamkeit

| Einsparung | Warum sie später mehr kostet |
|---|---|
| Keine Sicherungen oder ungetestete | Eine verlorene Website wird von Grund auf neu gebaut |
| Updates auslassen | Sicherheitsvorfälle und schwierigere Upgrades später |
| Eine Person hat alle Passwörter | Diese Person geht ([Wenn Ehrenamtliche gehen](wenn-ehrenamtliche-gehen.md)) |
| Die Domain ist privat auf eine Ehrenamtliche oder einen Ehrenamtlichen registriert | Sie können Ihre Adresse verlieren |
| Kein Cron | Newsletter, zeitgesteuerte Seiten und Aufräumarbeiten hören unbemerkt auf |

## Checkliste

- [ ] Anforderungen mit dem Hoster geklärt.
- [ ] Modell nach den vorhandenen Menschen gewählt, nicht nur nach dem Preis.
- [ ] Domain und Hosting-Vertrag auf den Namen der Organisation, zwei Personen mit Zugang.
- [ ] Sicherungen und Wiederherstellung vor dem Start getestet.
- [ ] Cron, HTTPS und E-Mail funktionieren.
- [ ] Vertrag zur Auftragsverarbeitung unterschrieben, wo Ihr Recht es verlangt.

## Verwandte Seiten

- [Betrieb für Non-Profit-Organisationen](README.md)
- [SQLite und Velocity für kleine Teams](sqlite-und-velocity-fuer-kleine-teams.md)
- [Hosting für eine Website](../../ersteller/website-ersteller/hosting-fuer-eine-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
