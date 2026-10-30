---
title: "Einwandbehandlung: Sicherheit, Langlebigkeit und Support"
audience: sales-marketing, project-manager
category: business
order: 70
summary: Die Einwände, die Käufer gegen Exponential vorbringen (Sicherheit, ob es gepflegt wird, Langlebigkeit, Support, PHP-Versionen, Bindung an einen Hoster, Fachkräfte, Upgrades, KI), jeweils mit einer Antwort, die nur auf Quellen beruht, der Seite, die sie belegt, und der ehrlichen Antwort, wo die Quellen Nein sagen.
keywords: Einwände, Einwandbehandlung, Vertrieb, Bedenken von Käufern, Sicherheit, gepflegt, Langlebigkeit, Support, Support-Zusage, PHP-Versionen, Herstellerbindung, Hosting, Fachkräfte, Entwickler, Schulung, Upgrades, Migration, KI, Datenschutz, Community, Risiko, FAQ, objections, objection handling, sales, buyer concerns, security, maintained, longevity, support commitment, PHP versions, lock-in, skills, developers, training, upgrades, migration, AI, data protection, risk
type: help
---
# Einwandbehandlung: Sicherheit, Langlebigkeit und Support

Diese Seite ist für Mitarbeiter aus Vertrieb und Marketing und für Projektleiter, die die Zweifel eines Käufers hören
und mit Fakten darauf antworten wollen. Zu jedem Einwand gibt es eine Antwort, die nur auf dokumentierten Quellen
beruht, die Seite oder Datei, die sie belegt, und dort, wo die Quellen „Nein“ oder gar nichts sagen, die ehrliche
Antwort. Ein Käufer, der Ihre Antwort prüft und sie zutreffend findet, vertraut auch der nächsten. Ein Käufer, der eine
Übertreibung entdeckt, zweifelt an allem; deshalb zieht diese Seite „Die Quellen sagen dazu nichts“ einer Vermutung vor.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hosting/control-panels/plesk.md), [cPanel](../hosting/control-panels/cpanel.md), [DirectAdmin](../hosting/control-panels/directadmin.md), [ISPConfig](../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Eine Methode für jeden Einwand](#eine-methode-für-jeden-einwand)
2. [Kurzübersicht](#kurzübersicht)
3. [Sicherheit](#sicherheit)
4. [„Wird es gepflegt?“](#wird-es-gepflegt)
5. [Langlebigkeit: „Gibt es das in zehn Jahren noch?“](#langlebigkeit-gibt-es-das-in-zehn-jahren-noch)
6. [Support](#support)
7. [PHP-Versionen und moderne Technik](#php-versionen-und-moderne-technik)
8. [Hosting und Herstellerbindung](#hosting-und-herstellerbindung)
9. [Fachkräfte: „Finden wir Leute, die es kennen?“](#fachkräfte-finden-wir-leute-die-es-kennen)
10. [Upgrades und Migration](#upgrades-und-migration)
11. [Exponential Intelligence und Datenschutz](#exponential-intelligence-und-datenschutz)
12. [Sprachen, Barrierefreiheit und Leistung](#sprachen-barrierefreiheit-und-leistung)
13. [Wenn die ehrliche Antwort Nein lautet](#wenn-die-ehrliche-antwort-nein-lautet)
14. [Neue Einwände festhalten](#neue-einwände-festhalten)

## Eine Methode für jeden Einwand

1. **Hören Sie bis zum Ende zu.** Schreiben Sie den Einwand in den Worten des Käufers auf.
2. **Erkennen Sie ihn an.** Das Anliegen ist berechtigt; bei den meisten geht es um Risiko.
3. **Fragen Sie, was dahintersteckt.** „Wenn Sie Support sagen, meinen Sie Sicherheitskorrekturen, jemanden, den Sie
   anrufen können, oder PHP-Versionen?“ Die Antwort sagt Ihnen, welche Zeile dieser Seite Sie brauchen.
4. **Antworten Sie mit einer Quelle.** Nennen Sie den Fakt und dann, wo er geschrieben steht. Bieten Sie an, den Link
   zu schicken.
5. **Bieten Sie eine Prüfung an.** Geben Sie dem Käufer, wo möglich, einen Weg, es selbst nachzuprüfen (einen Befehl,
   eine Seite, eine Testinstallation).
6. **Sagen Sie, was Sie nicht wissen.** „Die Dokumentation macht dazu keine Angabe. Ich kläre das und schicke Ihnen die
   Quelle.“

## Kurzübersicht

| Einwand | Kurze Antwort aus den Quellen | Nachweis |
|---|---|---|
| „Open Source ist weniger sicher.“ | Vertrauliche Meldung von Sicherheitslücken mit einer ersten Antwort innerhalb eines Werktags; Korrekturen im nächsten 6.0.x-Release; eine CSP, ein Audit-Protokoll, Anmeldung in zwei Schritten und ein Leitfaden zur Sicherheitshärtung | `SECURITY.md`, [Sicherheitshärtung](../sicherheitshaertung.md) |
| „Wird es noch gepflegt?“ | Fünfzehn 6.0.x-Releases vom 1. Januar 2024 bis zum 7. Juni 2026, 6.0.15 in Entwicklung; CI bei jedem Push | [Wer ist 7x?, Die 6.0.x-Releases](../wer-ist-7x.md#die-releases-60x), [CI](../../bc/6.0/ci.md) |
| „Wird es Bestand haben?“ | Eine seit 1999 gepflegte Codebasis, ein Unternehmen und eine gemeinnützige Organisation dahinter und eine GPL-Lizenz, die den Code für alle verfügbar hält | `COPYRIGHT.md`, [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |
| „Wer unterstützt uns?“ | Dokumentierte Zusagen zu PHP, Sicherheitskorrekturen, Releases und Dokumentation; Serviceverträge von 7x oder jedem anderen Anbieter; Orte der Community | [Die Support-Zusage](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung) |
| „Welche PHP-Versionen?“ | PHP 8.0 bis 8.5, einschließlich Velocity (ab seinem Release 0.0.4.54) | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| „Wir werden gebunden sein.“ | GPL-Code, sechs Datenbanken, fast jedes Betriebssystem, dokumentierte Umzüge zwischen Datenbanken | [Exponential sachlich vergleichen](exponential-sachlich-vergleichen.md#keine-herstellerbindung-punkt-für-punkt) |
| „Niemand kennt dieses System.“ | Die Quellen zählen keine Entwickler; sie dokumentieren den Technologie-Stack und viel freies Lernmaterial | [Fachkräfte](#fachkräfte-finden-wir-leute-die-es-kennen) |
| „Upgrades sind riskant.“ | Veröffentlichte Versionen ändern sich nie; Update-Dateien je Datenbank; eine Upgrade-Prüfung in der Administration | [Upgrade](../../installation/11-aktualisieren.md), [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| „KI veröffentlicht Unsinn oder lässt Daten abfließen.“ | Nur Entwürfe, eine Region je Anbieter, Regeln je Sektion und Klasse, ein einziger Schalter zum Abschalten | [Exponential Intelligence](../../funktionen/6.0/erweiterungen/expai.md) |
| „Ist das nicht das alte, eingestellte Produkt?“ | Exponential führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat; seit dem 1. Januar 2024 sind fünfzehn 6.0.x-Releases gefolgt | [Wer ist 7x?](../wer-ist-7x.md#verantwortung-von-einer-ruhenden-codebasis-zu-exponential-6) |

## Sicherheit

| Einwand | Antwort | Quelle |
|---|---|---|
| „Wie wird mit Sicherheitslücken umgegangen?“ | „Bitte melden Sie Sicherheitsprobleme vertraulich per E-Mail ... Sie können eine erste Antwort innerhalb eines Werktags erwarten.“ Ein anerkanntes Problem wird auf `main` behoben und mit der nächsten 6.0.x-Version veröffentlicht. | `SECURITY.md` |
| „Welche Versionen erhalten Korrekturen?“ | 6.0.x ist die gepflegte Linie. Veröffentlichte Tags werden „durch ein Upgrade auf die neueste 6.0.x behoben“; 5.x, 4.x, 3.x und älter werden nicht unterstützt. | `SECURITY.md` |
| „Kann eine Korrektur die Version ändern, die wir abgenommen haben?“ | Nein. „Ein veröffentlichter Tag wird nie geändert“: Eine Korrektur erreicht Sie als nächstes Release. | `SECURITY.md` |
| „Was ist mit Cross-Site-Scripting?“ | Eine Content Security Policy aus `site.ini [ContentSecurityPolicy]`; standardmäßig in der Administration nur im Berichtsmodus und auf den Sites ausgeschaltet, mit Schritten, um sie durchzusetzen. Neue Templates der Administration enthalten keine Inline-Styles oder -Skripte, damit sich eine strikte Richtlinie durchsetzen lässt. | [Content Security Policy](../../funktionen/6.0/content-security-policy.md), `CONTRIBUTING.md` |
| „Können wir sehen, wer was getan hat?“ | Ein manipulationssicheres Audit-Protokoll, standardmäßig eingeschaltet; jeder Eintrag trägt den Hash des vorherigen; `verify` meldet Änderungen oder Lücken. | [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md) |
| „Passwörter werden gestohlen.“ | Anmeldung in zwei Schritten mit einer Authenticator-App oder einem Code per E-Mail. | [Anmeldung in zwei Schritten](../../anleitungen/zwei-faktor-anmeldung.md) |
| „Bemerken Sie Angriffe?“ | Sondierungen und Angriffe werden klassifiziert und in `security.log` geschrieben, mit maskierten Adressen; der Klassifizierer selbst blockiert nichts, Listener können reagieren. | [Anfrageprotokolle](../../funktionen/6.0/anfrageprotokolle.md) |
| „Standardpasswörter sind ein Risiko.“ | Neue Installationen erzeugen ein zufälliges Administratorpasswort, wenn keines oder ein allgemein bekanntes angegeben wird. | [Das Standardpasswort des Administrators](../standard-administratorpasswort.md) |
| „Was sollen unsere Administratoren tun?“ | Ein Leitfaden zur Sicherheitshärtung, Bedrohung für Bedrohung, und eine Checkliste. | [Sicherheitshärtung](../sicherheitshaertung.md#checkliste), [Kapitel 13](../../installation/13-sicherheitshaertung.md) |
| „Kümmern Sie sich um Ihre Abhängigkeiten?“ | Die Profil-README von se7enxweb nennt 14 Sicherheits-Pull-Requests an die Zeta Components im April 2026. | [Der PHP-Stack, auf dem es steht](../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt) |

**Sagen Sie nicht** „Es ist sicher“ oder „Es wurde noch nie gehackt“: Keine Quelle sagt das eine oder das andere.
Sagen Sie, wie der Prozess aussieht und wie der Kunde ihn nachprüfen kann.

## „Wird es gepflegt?“

**Antwort.** Ja, und der Käufer kann das in wenigen Minuten prüfen:

- Releases: 6.0.0 am 1. Januar 2024 bis 6.0.14 am 7. Juni 2026, mit 6.0.15 in Entwicklung
  ([Wer ist 7x?, Die 6.0.x-Releases](../wer-ist-7x.md#die-releases-60x)).
- Änderungen: „6556 Änderungen im Verzeichnis in 53 Repositories seit Dezember 2023. 1037 davon stammen vom
  se7enxweb-Team“ ([Das Ökosystem der Exponential-Plattform](../../geschichte/oekosystem.md)); jeder Monat wird in
  [der Geschichte](../../geschichte/README.md) erzählt.
- Tests: Qualitätsprüfungen und PHPUnit laufen bei jedem Push und jedem Pull-Request, und montags läuft ein
  wöchentlicher Bericht zum Zustand der CI ([Continuous Integration](../../bc/6.0/ci.md)).
- Das Änderungsprotokoll der aktuellen Linie: [das Changelog zu 6.0.15](../../../changelogs/6.0/6.0.15.md).

**Lassen Sie den Käufer prüfen.**

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git tag -l 'v6.0.*' --sort=version:refname    # every release, in the right order
git log -5 --format='%ad %s'                   # the latest changes
```

> **Hinweis**
> Sortieren Sie Versionen mit `--sort=version:refname`: Eine einfache Sortierung nach Namen stellt `0.1.10` vor `0.1.9`
> ([Wer ist die Exponential Foundation?, Releases und Versionsnummern](../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern)).

## Langlebigkeit: „Gibt es das in zehn Jahren noch?“

Niemand kann die Zukunft versprechen, und Sie sollten es auch nicht. Was die Quellen zeigen, ist eine Bilanz und eine
Struktur:

| Fakt | Quelle |
|---|---|
| Die Codebasis trägt Urheberrechtsjahre ab 1999 (eZ Systems AS) und von 1998 bis 2026 (7x und die Exponential Foundation) | `COPYRIGHT.md` |
| „Exponential hat sein Lebensende im Jahr 2021 hinter sich gelassen und überlebt“ | `README.md`, „Exponential Project Status“ |
| 7x hat 2023 die Führung des Projekts übernommen; das Unternehmen arbeitet seit den ersten Versionen mit dieser Software | [Wer ist 7x?](../wer-ist-7x.md#von-brookins-consulting-zu-7x-in-drei-stufen) |
| Die Exponential Foundation, eine am 19. März 2026 gegründete gemeinnützige Organisation, betreibt das Projekt; zu ihrem Auftrag gehören „Finanzierung, Verbreitung, Support, Ausrichtung, Bildung, Entwicklung, Förderung und Dokumentation“ | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#was-die-foundation-ist) |
| Die vollständige Historie seit 2002 ist öffentlich; die GPL erlaubt jedem, den Code zu behalten, zu ändern und weiterzugeben | [Wer ist 7x?](../wer-ist-7x.md#grundsätze-die-sie-im-code-sehen-können), `LICENSE` |

**Sagen Sie:** „Die Software hat ihren ursprünglichen Hersteller schon einmal überlebt, weil ihr Code frei ist. Was
auch immer mit einem Unternehmen geschieht, Sie behalten den Code, die Historie und das Recht, ihn von jedem pflegen zu
lassen.“

**Ehrliche Grenzen.** Die Quellen sagen nicht, wer im Vorstand der Foundation sitzt, wie sie geführt wird oder wie hoch
ihr Budget ist ([Was die Quellen nicht sagen](../wer-ist-die-exponential-foundation.md#was-die-quellen-nicht-sagen)).
Füllen Sie diese Lücke nicht; empfehlen Sie dem Käufer, die Foundation über ihre Website zu fragen.

## Support

Das Projekt „veröffentlicht kein einzelnes Dokument mit einer ‚Support-Richtlinie‘“; seine Zusagen sind an mehreren
Stellen schriftlich festgehalten ([Die Support-Zusage](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)).
Stellen Sie sie als Tabelle dar:

| Art des Supports | Was es gibt | Quelle |
|---|---|---|
| Sicherheitskorrekturen | Auf `main`, veröffentlicht mit der nächsten 6.0.x | [Sicherheitskorrekturen](../wer-ist-die-exponential-foundation.md#sicherheitskorrekturen) |
| PHP-Kompatibilität | 8.0 bis 8.5, mit CI unter 8.1 bis 8.5 und einer Startprüfung unter 8.0 | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| Disziplin bei Releases | Veröffentlichte Versionen ändern sich nie; die letzte Stelle zählt weiter | [Releases und Versionsnummern](../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern) |
| Dokumentation | Im Repository, frei unter der GNU FDL, in der Administration angezeigt | [Hilfesystem](../../funktionen/6.0/hilfesystem.md) |
| Fehlermeldungen | Der Issue-Tracker von Exponential und die Seite **Fehler melden** der Hilfe (`/exphelp/report`) | [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md) |
| Fragen | Die Community-Foren auf [share.exponential.earth](https://share.exponential.earth/forums) und der in `README.md` genannte Telegram-Chat der Community | [Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community) |
| Kostenpflichtige Dienstleistungen | 7x führt Support- und Wartungsverträge, Entwicklung, Schulung, Migration und Managed Hosting auf; Preise und Bedingungen stehen auf der eigenen Website von 7x | [Wer ist 7x?, Dienstleistungen und Hosting](../wer-ist-7x.md#dienstleistungen-und-hosting) |

**Nennen Sie keine** Reaktionszeit, kein SLA und keine Verfügbarkeitszahl: Die einzige dokumentierte Zeit ist die erste
Antwort auf eine Sicherheitsmeldung, „innerhalb eines Werktags“ (`SECURITY.md`). Service-Level gehören in einen Vertrag
mit einem Anbieter.

## PHP-Versionen und moderne Technik

| Einwand | Antwort | Quelle |
|---|---|---|
| „Es braucht ein altes PHP.“ | PHP 8.0 bis 8.5 werden unterstützt; 7.4 ist ab 6.0.15 ohne Support nutzbar; `composer.json` erlaubt bis 8.8. | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| „Wir betreiben Red Hat Enterprise Linux 9 mit seinem mitgelieferten PHP.“ | PHP 8.0, das mitgelieferte PHP von RHEL 9 und seinen Nachbauten, betreibt es unter Apache, PHP-FPM oder Velocity (ab Velocity 0.0.4.54). | `README.md`, [Unterstützung für PHP 8.0](../../bc/6.0/php-8.0-unterstuetzung.md) |
| „Wir sind noch auf PHP 7.4.“ | 6.0.15 ist unter PHP 7.4 wieder nutzbar, ohne Support, als Brücke; 7x empfiehlt weiterhin den Wechsel auf PHP 8. | `README.md` |
| „Die Oberfläche wirkt veraltet.“ | admin4 mit hellem und dunklem Modus; jQuery 4 und TinyMCE 8 in der 6.0.15-Linie; AdminUI als alternatives Erscheinungsbild. | [Das Design admin4](../../funktionen/6.0/admin4-design.md), [Oktober 2026](../../geschichte/2026/2026-10.md), [AdminUI](../../funktionen/6.0/exp-adminui.md) |
| „Wir brauchen eine Headless-Auslieferung.“ | Eine REST-API in den Versionen 1 und 2. Für ein Symfony-Projekt mit GraphQL bietet die Familie Exponential Platform DXP. | [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md), [Welches Produkt zu Ihnen passt](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt) |

## Hosting und Herstellerbindung

| Einwand | Antwort | Quelle |
|---|---|---|
| „Wir müssen bei Ihnen hosten.“ | Nein. Jeder darf es hosten: unter Linux, BSD, macOS, Windows, in Containern oder auf Cloud-Maschinen, in dokumentierten Stufen. | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md#die-vier-stufen) |
| „Wir brauchen unsere eigene Datenbank.“ | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle oder MySQL. | [Unterstützte Datenbanken](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge) |
| „Wir brauchen einen Datenbankserver, den wir schon betreiben.“ | PostgreSQL ist der empfohlene Server; MySQL und MariaDB teilen sich einen Treiber; Oracle über `ezoracle`. | ebenda |
| „Wir bekommen unsere Daten nicht heraus.“ | Standard-Dumps je Datenbank; dokumentierte Werkzeuge für den Umzug zwischen Engines. | [Backup und Wiederherstellung](../datenbankunterstuetzung.md#backup-und-wiederherstellung), [Eine Site verschieben](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen) |
| „Wir haben nur Shared Hosting.“ | Apache oder LiteSpeed mit `.htaccess` liefern es aus; ohne Shell-Zugang installiert es der Einrichtungsassistent. Velocity kann dort meist nicht laufen. | [Container, Cloud und Hosting-Panels](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels), `README.md` |

## Fachkräfte: „Finden wir Leute, die es kennen?“

Auf diesen Einwand müssen Sie am sorgfältigsten antworten, denn die Quellen zählen weder Entwickler noch Agenturen noch
Anwender. Erfinden Sie nie eine Zahl.

**Was Sie sagen können:**

1. **Der Technologie-Stack ist schlicht.** PHP 8, Composer, eine Template-Sprache, INI-Einstellungen und die Datenbank
   nach Wahl des Kunden ([Wer ist 7x?, Der Anwendungs-Stack](../wer-ist-7x.md#der-anwendungs-stack)).
2. **Das Lernmaterial ist frei und umfangreich.** Diese Hilfe; das Installationsbuch ([Inhalt](../../installation/README.md));
   die Leitfäden; die offizielle Dokumentations-Website [doc.exponential.earth](https://doc.exponential.earth/); die
   Community-Enzyklopädie [eZpedia](https://ezpedia.exponential.earth/); der Spiegel von „drei originalen
   Entwickler-Schulungsbüchern (zusammen über 700 Seiten)“ und das Buch „Learning eZ Publish“ unter der GFDL
   ([Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community)).
3. **Es gibt einen Weg vom Anwender zum Entwickler.** [Mit Git entwickeln](../mit-git-entwickeln.md) zeigt Schritt für
   Schritt, wie man vom Anwender zum Entwickler wird.
4. **Schulungen lassen sich einkaufen.** 7x führt Schulungen unter seinen Dienstleistungen auf ([Dienstleistungen und Hosting](../wer-ist-7x.md#dienstleistungen-und-hosting)).
5. **Die Codebasis hatte viele Mitwirkende.** 194 verschiedene Commit-Autoren vor 2016 und Pull-Requests aus mehreren
   Organisationen seit Dezember 2023 ([Mitwirkende](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts)).

**Was Sie nicht sagen dürfen:** „Es gibt Tausende von Exponential-Entwicklern“ oder irgendetwas über die Größe des
Arbeitsmarkts. Machen Sie stattdessen einen Planungsschritt daraus: Nehmen Sie Einarbeitung und Schulung in den
Projektplan auf ([Ein Exponential-Projekt planen](../fuehrung/ein-exponential-projekt-planen.md)).

## Upgrades und Migration

| Einwand | Antwort | Quelle |
|---|---|---|
| „Upgrades machen Sites kaputt.“ | Das Kapitel zum Upgrade beginnt mit einer Regel: Sichern Sie, und führen Sie nie einen Installer über einer bestehenden Site aus. Update-Dateien gibt es je Datenbank-Engine. | [Upgrade](../../installation/11-aktualisieren.md) |
| „Woher wissen wir, dass das Upgrade vollständig ist?“ | **Setup > Upgrade-Prüfung** vergleicht die Dateien mit dem Manifest des Releases und die Datenbank mit dem Schema des Releases. | [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| „Wir müssen wissen, was sich ändert.“ | Changelogs je Version und Hinweise zur Abwärtskompatibilität für 6.0. | [Changelogs](../../../changelogs/README.md), [Changelog zu 6.0.15](../../../changelogs/6.0/6.0.15.md) |
| „Unsere alte Site stammt aus der Zeit von 4.x oder 5.x.“ | Dokumentierte Migrationen von 3.10 und 4.x, vom 5.x-Legacy-Stack sowie von eZ Platform und Ibexa. | [Kapitel 14](../../installation/14-migration-von-4x.md), [Kapitel 15](../../installation/15-migration-von-5x-legacy.md), [Kapitel 16](../../installation/16-migration-von-ez-platform-und-ibexa.md) |
| „Eine Migration ist teuer.“ | Der Aufwand hängt von der Site ab; die Kapitel führen auf, was zu erfassen ist. 7x bietet die Migration als Dienstleistung an. | [Kapitel 14, Bestandsaufnahme der alten Site](../../installation/14-migration-von-4x.md), [Dienstleistungen](../wer-ist-7x.md#dienstleistungen-und-hosting) |

## Exponential Intelligence und Datenschutz

| Einwand | Antwort | Quelle |
|---|---|---|
| „KI wird falsche Inhalte veröffentlichen.“ | Sie schreibt nur Entwürfe und veröffentlicht nie von sich aus; **Send for approval** führt den normalen Veröffentlichungsvorgang aus, sodass ein Freigabe-Workflow die Version zurückhält. | [Exponential Intelligence](../../funktionen/6.0/erweiterungen/expai.md) |
| „Unsere Inhalte dürfen die EU (oder das Gebäude) nicht verlassen.“ | Jeder Anbieter gibt seine Region an: `us`, `eu` oder `local`; Regeln je Sektion und Klasse entscheiden, welche Inhalte wohin gehen dürfen; ein lokales Open-Weight-Modell hält die Daten auf dem Server. | ebenda, `README.md` |
| „Wir können die Kosten nicht kontrollieren.“ | Jede Anfrage wird mit ihren geschätzten Kosten protokolliert; Limits je Benutzer, Tag, Monat, Site und Anbieter; Budgets für Arbeit in der Warteschlange. | ebenda |
| „Unser Datenschutzbeauftragter muss es freigeben.“ | **EI: Summary** ist eine druckbare Seite für Datenschutzbeauftragte. | ebenda |
| „Wir wollen es überhaupt nicht.“ | Es sendet nichts, solange kein Anbieterschlüssel hinterlegt ist, und `[AISettings] Enabled` schaltet es überall oder je Siteaccess ab. | `README.md` |

Rechtsfragen (zum Beispiel zur DSGVO) sind Sache der Rechtsberater des Kunden. Geben Sie ihnen die EI: Summary und die
Fakten zum Audit; leisten Sie keine Rechtsberatung.

## Sprachen, Barrierefreiheit und Leistung

| Einwand | Antwort | Quelle |
|---|---|---|
| „Unsere Sprache wird nicht unterstützt.“ | Es gibt 368 Locale-Dateien; prüfen Sie die Sprache des Kunden in der Liste. Jede Übersetzung der Administration ist vollständig (die aktuellen Gesamtzahlen stehen am Anfang der Seite Sprachen); eine Sprache ohne eigene zeigt Englisch. | [Sprachen in Exponential](../../sprachen.md) |
| „Wir brauchen Arabisch oder Hebräisch.“ | Schreibrichtung von rechts nach links in Inhalten und in der Administration; die hebräische und die arabische Übersetzung der Administration sind vollständig. | [Von rechts nach links](../../funktionen/6.0/rechts-nach-links.md), [Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen) |
| „Ist die Administration barrierefrei?“ | Sprunglink, Landmarks, Tastaturmuster, beschriftete Felder, Tests für Kontrast und Zielgröße in admin4. | [Das Design admin4](../../funktionen/6.0/admin4-design.md#landmarks-und-namen) |
| „PHP ist langsam.“ | `README.md` dokumentiert 0,36 ms je gecachter Seite und 8.941 Anfragen pro Sekunde auf einer Maschine mit 12 Kernen mit Velocity und hält fest, dass der größte Teil der Geschwindigkeit aus Korrekturen stammt, die auch unter Apache oder Nginx wirken. Bieten Sie eine Messung auf der Hardware des Kunden an. | `README.md`, [Benchmarks](../../anleitungen/benchmarking.md) |

## Wenn die ehrliche Antwort Nein lautet

Manche Einwände treffen zu. Sagen Sie das, und bieten Sie die Alternative an, die die Quellen nennen.

| Der Käufer braucht | Ehrliche Antwort | Alternative |
|---|---|---|
| Microsoft SQL Server | Kein Treiber in 6.0.x | „Mit zusätzlichem Aufwand möglich“, dokumentiert ([Unterstützte Datenbanken](../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)) |
| Einen reinen Symfony-Stack | Das ist Exponential 6 nicht | Exponential Platform DXP ([Welches Produkt zu Ihnen passt](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt)) |
| Ein veröffentlichtes SLA des Projekts | Es gibt keines | Ein Servicevertrag mit einem Anbieter |
| Mehr als 62 Inhaltssprachen in einer Installation | Nicht möglich | Auf mehrere Installationen aufteilen oder den tatsächlichen Bedarf prüfen ([Die Grenze von 62](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen)) |
| Velocity auf Shared Hosting | Meist nicht möglich | Apache oder LiteSpeed mit `.htaccess` (`README.md`) |
| PHP 7.4 mit neuen Funktionen | Ja, ab 6.0.15, ohne Support und ohne die Funktionen, die PHP 8 brauchen | PHP aktualisieren ([PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen)) |

## Neue Einwände festhalten

Jeder Einwand, den diese Seite nicht abdeckt, ist eine Lücke in der Dokumentation. Führen Sie ein Protokoll und bringen
Sie es in die regelmäßige Besprechung Ihres Teams mit ([Zusammenarbeiten](../fuehrung/zusammenarbeiten.md)):

```text
Date: <date>     Context: <meeting type, industry>     Role of the person asking: <role, no names>
Objection, in their words: <text>
What we answered: <text>
Source we used, or "none found": <link or file>
Follow-up promised: <what, by when>
Documentation gap to report: <yes/no; link to the issue when filed>
```

Wenn eine Lücke tatsächlich besteht, verbessern Sie die Hilfe: Jede Hilfeseite lässt sich auf GitHub bearbeiten, und
die Seiten sind freie Dokumentation ([Freie Dokumentation](../freie-dokumentation.md)). Melden Sie Fehler über die Seite
**Fehler melden** der Hilfe.

## Verwandte Seiten

- [Exponential sachlich vergleichen](exponential-sachlich-vergleichen.md)
- [Funktionshighlights, den Bedürfnissen der Kunden zugeordnet](funktionshighlights-fuer-kunden.md)
- [Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation](lizenzen-fuer-kaeufer.md)
- [Community und Ökosystem als Verkaufsargument](community-und-oekosystem.md)
- [Risikomanagement für Exponential-Projekte](../fuehrung/risikomanagement.md)
- [Release- und Upgrade-Planung](../fuehrung/release-und-upgrade-planung.md)
- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
