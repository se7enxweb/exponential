---
title: Funktionshighlights, den Bedürfnissen der Kunden zugeordnet
audience: sales-marketing, project-manager
category: business
order: 40
summary: Die Funktionen von Exponential 6.0.x, zugeordnet zu den Bedürfnissen, die Kunden mitbringen: was Sie sagen, wo Sie es in der Administration zeigen, welche Dokumentation es belegt und welche Grenzen Sie ehrlich nennen.
keywords: Funktionen, Highlights, Kundenbedürfnisse, Vertrieb, Presales, Bedarfsanalyse, Multisite, Sprachen, Locales, von rechts nach links, Layouts, Exponential Intelligence, expai, Barrierefreiheit, Sicherheit, Content Security Policy, Audit-Protokoll, Velocity, Leistung, Hilfesystem, PDF, EPUB, E-Commerce, REST-API, Datenbanken, features, highlights, customer needs, sales, presales, discovery, multisite, languages, locales, right to left, layouts, accessibility, security, audit trail, performance, help system, e-commerce, REST API, databases
type: help
---
# Funktionshighlights, den Bedürfnissen der Kunden zugeordnet

Diese Seite ist für Mitarbeiter aus Vertrieb und Marketing und für Projektleiter im Presales, die einem Interessenten mit
einer Liste von Bedürfnissen begegnen und jedes davon mit einer echten Funktion von Exponential beantworten wollen. Zu
jedem Bedürfnis nennt sie die Funktion, die darauf antwortet, die Stelle in der Administration, an der Sie sie zeigen
können, die Seite dieser Hilfe, die sie belegt, und die Grenze, die Sie erwähnen sollten, bevor der Kunde sie selbst
findet. Nichts hier ist ein Versprechen, das über die verlinkten Seiten hinausgeht: Wenn ein Kunde mehr verlangt, als
eine Seite sagt, lautet die ehrliche Antwort „Die Dokumentation macht dazu keine Angabe; wir prüfen das“.

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
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Wie Sie diese Seite verwenden](#wie-sie-diese-seite-verwenden)
2. [Die Übersicht: Bedürfnis, Funktion, wo zeigen, Nachweis](#die-übersicht-bedürfnis-funktion-wo-zeigen-nachweis)
3. [Redakteure bauen Seiten selbst](#redakteure-bauen-seiten-selbst)
4. [Mehrere Sites aus einer Installation](#mehrere-sites-aus-einer-installation)
5. [Viele Sprachen, einschließlich Schreibrichtung von rechts nach links](#viele-sprachen-einschließlich-schreibrichtung-von-rechts-nach-links)
6. [Hilfe beim Schreiben, unter menschlicher Kontrolle](#hilfe-beim-schreiben-unter-menschlicher-kontrolle)
7. [Barrierefreiheit der Administration](#barrierefreiheit-der-administration)
8. [Fragen zu Sicherheit, Audit und Compliance](#fragen-zu-sicherheit-audit-und-compliance)
9. [Geschwindigkeit](#geschwindigkeit)
10. [Hilfe und Dokumentation im Produkt](#hilfe-und-dokumentation-im-produkt)
11. [PDF und E-Books](#pdf-und-e-books)
12. [Strukturierte Inhalte, Freigabe und Versionen](#strukturierte-inhalte-freigabe-und-versionen)
13. [Shop, API und Integration](#shop-api-und-integration)
14. [Freie Wahl von Hosting, Datenbank und Betriebssystem](#freie-wahl-von-hosting-datenbank-und-betriebssystem)
15. [Den Betrieb der Site im Alltag](#den-betrieb-der-site-im-alltag)
16. [Ein Arbeitsblatt zur Bedarfsanalyse](#ein-arbeitsblatt-zur-bedarfsanalyse)
17. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)

## Wie Sie diese Seite verwenden

1. **Gehen Sie von den Worten des Kunden aus**, nicht von der Funktionsliste. Schreiben Sie seine Bedürfnisse zuerst in
   seinen eigenen Worten auf (das [Arbeitsblatt zur Bedarfsanalyse](#ein-arbeitsblatt-zur-bedarfsanalyse) unten hilft dabei).
2. **Suchen Sie jedes Bedürfnis in der Übersicht.** Jede Zeile nennt eine Funktion, eine Stelle, an der Sie sie zeigen,
   und eine Seite, die Sie danach schicken.
3. **Zeigen statt erzählen.** Jedes „Wo zeigen“ ist eine echte Stelle in der Administration einer Installation, über
   die Sie selbst verfügen. Die [Demo-Drehbücher](demo-drehbuecher.md) machen aus den Zeilen Rundgänge von 5, 15 und 45
   Minuten.
4. **Nennen Sie die Grenze selbst.** Jeder Abschnitt endet mit „Grenzen, die Sie nennen sollten“. Ein Kunde, der eine
   Grenze von Ihnen hört, glaubt Ihnen auch den Rest; einer, der sie später selbst findet, nicht.
5. **Schicken Sie den Nachweis.** Schicken Sie nach dem Termin die Seiten, die in den Zeilen verlinkt sind, die dem
   Kunden wichtig waren. Jede Hilfeseite lässt sich drucken oder als PDF herunterladen (siehe [PDF und E-Books](#pdf-und-e-books)).

> **Hinweis**
> Die folgenden Funktionen sind die der 6.0.15-Linie, die sich in Entwicklung befindet; der neueste Tag ist 6.0.14
> ([Wer ist 7x?, Die 6.0.x-Releases](../wer-ist-7x.md#die-releases-60x)). Wenn ein Kunde eine veröffentlichte Version
> betreiben wird, prüfen Sie im [Changelog zu 6.0.15](../../../changelogs/6.0/6.0.15.md), was in welcher Version
> enthalten ist, bevor Sie einen Termin zusagen.

## Die Übersicht: Bedürfnis, Funktion, wo zeigen, Nachweis

| Der Kunde braucht | Funktion, die darauf antwortet | Wo zeigen | Nachweis zum Schicken |
|---|---|---|---|
| Redakteure, die Seiten ohne Entwickler anordnen | Exponential Layouts: Layouts, Zonen, Blöcke und Regeln | Tab **Layouts**, ein Layout **öffnen** | [Der Layout-Editor](../layout-editor.md) |
| Mehrere Sites, Marken oder Länder aus einer Installation | Siteaccesses (einer je Site, Sprache oder für die Administration), Einstellungen je Site, Links zwischen Sites | Die Globus-Schaltfläche in der Kopfzeile, **Website oder einen anderen Siteaccess öffnen** | [Siteaccess-Zuordnung nach Host und Sprache](../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md), [Einstellungen je Site](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md), [Links zwischen Sites](../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md) |
| Inhalte in vielen Sprachen | 368 Locale-Dateien (361 Sprachen und Länder) als Inhaltssprachen; Übersetzungen je Objekt | Die Sprachabzeichen eines Objekts und der Tab **Übersetzungen** | [Sprachen in Exponential](../../sprachen.md), [Sprachen](../sprachen.md) |
| Eine Administration in der eigenen Sprache der Redakteure | Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md)) | Rechte Spalte, Kasten **Oberfläche**, **Sprache der Oberfläche** | [Sprachen in Exponential, Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen) |
| Hebräisch, Arabisch, Persisch oder Urdu | Schreibrichtung von rechts nach links in Inhalten und Administration (`dir="rtl"`) | Eine Sprache der Oberfläche mit Schreibrichtung von rechts nach links wählen | [Von rechts nach links](../../funktionen/6.0/rechts-nach-links.md) |
| Schneller schreiben, mit einem Menschen am Steuer | Exponential Intelligence (`expai`): schreibt nur Entwürfe | Der Tab **Intelligence** eines Objekts; das Bearbeitungsformular | [Exponential Intelligence](../../funktionen/6.0/erweiterungen/expai.md) |
| Eine barrierefreie Administration | admin4: Sprunglink, Landmarks, Tastaturmuster, Tests für Kontrast und Zielgröße | Auf einer beliebigen Seite einmal `Tab` drücken; den Baum mit den Pfeiltasten bedienen | [Tastenkürzel](../tastenkuerzel.md), [Das Design admin4](../../funktionen/6.0/admin4-design.md) |
| Nachweis, wer was getan hat | Manipulationssicheres Audit-Protokoll | Tab **Audit** (`/audit/dashboard`) | [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md) |
| Schutz vor eingeschleusten Skripten | Content Security Policy, in der Administration standardmäßig nur im Berichtsmodus | `site.ini [ContentSecurityPolicy]` (die Dokumentationsseite zeigen) | [Content Security Policy](../../funktionen/6.0/content-security-policy.md) |
| Eine stärkere Anmeldung | Anmeldung in zwei Schritten mit einer Authenticator-App oder einem Code per E-Mail | `/user2fa/setup` | [Anmeldung in zwei Schritten](../../anleitungen/zwei-faktor-anmeldung.md) |
| Ein Härtungsplan für den Produktivbetrieb | Leitfaden und Checkliste zur Sicherheitshärtung | (ein Dokument, kein Bildschirm) | [Sicherheitshärtung](../sicherheitshaertung.md) |
| Eine schnelle Site | Exponential Velocity und die Cache-Korrekturen von 6.0.15 | `./console exp:velocity status`; `exp:benchmark` | [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md), [Benchmarks](../../anleitungen/benchmarking.md) |
| Hilfe für die Mitarbeiter im Werkzeug selbst | Das Hilfesystem: Dashboard, Suche, kontextbezogene Hilfe, Drucken und PDF | Die Schaltfläche **Hilfe** in der Kopfzeile | [Hilfesystem](../../funktionen/6.0/hilfesystem.md) |
| PDF-Ausgabe von Inhalten | PDF-Export mit eigener Fußzeile und TrueType-Schriften | **Setup > PDF-Export** | [PDF-Export](../../funktionen/6.0/pdf-export.md) |
| Handbücher als E-Books | EPUB 3 und PDF von Hilfehandbüchern, die als Inhalte gepflegt werden | Die Seite eines Handbuchs in der Hilfe, **EPUB herunterladen** | [Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md) |
| Eigene Inhaltstypen | Inhaltsklassen mit Attributen | **Setup > Klassen** | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Prüfung vor dem Veröffentlichen | Entwürfe, Versionen, Freigabe-Workflows | **Versionen verwalten** bei einem Objekt; **Meine Warteliste** | [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md), [Workflows](../../anleitungen/workflows.md) |
| Ein Onlineshop | Der eingebaute Shop mit Dashboard und Bestellstatus | Tab **Shop** | [Shop-Dashboard](../../funktionen/6.0/shop-dashboard.md) |
| Inhalte für Apps und andere Systeme | REST-API v1 und v2 | (eine Anfrage im Terminal zeigen) | [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) |
| Die bevorzugte Datenbank | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | **Setup > Systeminformationen**, Karte „Datenbank“ | [Unterstützte Datenbanken](../datenbankunterstuetzung.md) |
| Das bevorzugte Hosting | Fast jedes Betriebssystem mit PHP 8.0 oder später; viele Webserver | (ein Dokument) | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Weniger Überraschungen im Betrieb | Log-Viewer, Fehlerreferenzen, Inhaltsprüfungen, Upgrade-Prüfung | **Setup > Logdateien**, **Setup > Upgrade-Prüfung** | [Log-Viewer](../../funktionen/6.0/log-betrachter.md), [Inhaltsprüfungen](../../funktionen/6.0/inhaltspruefungen.md), [Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| Große Änderungen ohne Zeitüberschreitung | Inhaltsaufträge im Hintergrund | Einen großen Teilbaum entfernen oder verschieben | [Inhaltsaufträge](../../funktionen/6.0/inhaltsauftraege.md) |

## Redakteure bauen Seiten selbst

**Was Sie sagen.** Exponential Layouts ist „ein visueller Seitenbaukasten für Exponential. Er bringt das Konzept von
Netgen Layouts in den Kernel von Exponential: Ein Layout ist eine gespeicherte Anordnung von Blöcken in Zonen, und eine
Regel entscheidet, welche Seiten es erhalten“ (`README.md`, „Exponential Layouts“). Redakteure ziehen Blöcke in Zonen,
ändern sie in einem Tab **Content** und einem Tab **Design** und arbeiten an einem Entwurf, den die Site erst zeigt,
wenn sie veröffentlichen ([Der Layout-Editor](../layout-editor.md)).

**Was Sie zeigen.** Das [Layout-Beispiel des Rundgangs](../rundgang-durch-die-administration.md#in-der-praxis-der-layout-editor): ein Layout
öffnen, einen Block auswählen, einen Block **Title** hinzufügen, ihn verschieben, eine verknüpfte Zone zeigen und dann
**Discard**, damit sich nichts ändert.

**Warum es für den Kunden zählt.**

- Gemeinsam genutzte Layouts halten Kopf- und Fußzeile an einem Ort: Andere Layouts *verknüpfen* eine Zone damit, statt
  Blöcke zu kopieren.
- Zuordnungen (Regeln) entscheiden, welche Seiten ein Layout verwenden, nach Ort, Teilbaum, Inhaltstyp oder Siteaccess,
  und es gewinnt die erste aktivierte Zuordnung nach Priorität ([Welche Seiten ein Layout verwenden](../layout-editor.md#welche-seiten-ein-layout-verwenden)).

**Grenzen, die Sie nennen sollten.**

- Der Tab **Layouts** wird nur Rollen angezeigt, die mit Layouts arbeiten dürfen; den Zugriff legt ein Administrator
  fest.
- Wer Zuordnungen ändert, ändert ganze Teile der Site; die Hilfe selbst rät Redakteuren, sich dafür an einen
  Site-Entwickler zu wenden.
- Exponential Platform DXP liefert keinen Layout-Editor mit; Legacy und Nexus schon
  ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick)).

## Mehrere Sites aus einer Installation

**Was Sie sagen.** Multisite steht in der Liste der Hauptfunktionen in der README. Jede Site ist ein *Siteaccess* mit
eigenem Host, Pfad, eigener Sprache und eigenem Design ([Wer ist 7x?, Das Produkt](../wer-ist-7x.md#das-produkt-was-exponential-ist)).
Eine Site, die in einer eigenen Erweiterung liegt, kann Einstellungen mitbringen, die nur für ihre Siteaccesses gelten
([Einstellungen je Site in Erweiterungen](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)). Hostnamen lassen sich nach
ihrem Anfang zuordnen, sodass Test- und Live-Hosts dieselbe Site erreichen, und die Startseite kann Besucher zu ihrer
Sprache weiterleiten ([Siteaccess nach Host-Präfix](../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)). Links
zwischen Sites eines Inhaltsbaums führen mit der Erweiterung `ezurlmultisite` zur richtigen Site
([Links zwischen Sites](../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)).

**Was Sie zeigen.** Die Globus-Schaltfläche in der Kopfzeile (**Website oder einen anderen Siteaccess öffnen**) und, auf
einer mit `./console exp:install --apply` angelegten Installation, die Sites des Multisite-Pakets
([Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md)).

**Grenzen, die Sie nennen sollten.**

- Ein einzelner Velocity-Serverprozess, der mehrere Sites bedient, „ist eine Bequemlichkeit beim Routing, keine
  Sicherheitsgrenze“; für eine Trennung auf Ebene des Betriebssystems betreiben Sie eine Instanz je Site (`README.md`,
  „When a traditional server is still the right answer“).
- Eine Installation kann höchstens 62 Inhaltssprachen haben (30 unter 32-Bit-PHP)
  ([Abschnitt 11 von Sprachen](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen)).

## Viele Sprachen, einschließlich Schreibrichtung von rechts nach links

**Was Sie sagen.** „368 Locale-Dateien (361 Sprachen und Länder ...) können als Inhaltssprachen und als Locales von
Siteaccesses gewählt werden; es gibt Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite Sprachen)“ ([Sprachen in Exponential](../../sprachen.md)).
Jede Übersetzung eines Objekts wird für sich veröffentlicht ([Sprachen](../sprachen.md#ein-objekt-übersetzen)). Wenn die
Sprache der Oberfläche von rechts nach links geschrieben wird, „dreht sich die Administration um“, und Inhalte in einer
Sprache mit Schreibrichtung von rechts nach links werden überall, wo sie angezeigt und bearbeitet werden, ebenso
gekennzeichnet ([Von rechts nach links](../../funktionen/6.0/rechts-nach-links.md)).

**Was Sie zeigen.** Übersetzen Sie ein Objekt über sein gestricheltes Sprachabzeichen; stellen Sie die **Sprache der
Oberfläche** in der rechten Spalte auf Deutsch um, dann auf Hebräisch oder Arabisch und wieder zurück.

**Grenzen, die Sie nennen sollten.**

| Stufe | Was der Kunde erhält | Quelle |
|---|---|---|
| Vollständig | Administration und Inhalte in der Sprache: `eng-US`, `ger-DE`, `esl-ES`, `esl-MX`, `fre-FR`, `heb-IL`, `ara-SA`, `lat-VA`, `tlh-QO`, `ukr-UA` (acht davon maschinell unterstützt) | [Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen) |
| Teilweise Oberfläche | 21 ältere Übersetzungen decken 16 bis 39 % der heutigen Texte ab; der Rest erscheint auf Englisch | ebenda |
| Nur Inhalte | Jede andere Locale: Inhalte, Datumsangaben, Zahlen und Währung, mit einer englischen Administration | ebenda |

> **Tipp**
> Wenn die Sprache eines Kunden nur „Nur Inhalte“ erreicht, ist das ehrliche Angebot ein Übersetzungsprojekt. Wie Sie
> eine Übersetzung der Administration hinzufügen oder vervollständigen, ist in
> [Abschnitt 8 von Sprachen](../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen) dokumentiert.

## Hilfe beim Schreiben, unter menschlicher Kontrolle

**Was Sie sagen.** Exponential Intelligence „schreibt Entwürfe, als der angemeldete Redakteur und mit dessen eigenen
Berechtigungen“, veröffentlicht nie von sich aus und arbeitet mit gehosteten Anbietern in den USA oder der EU oder mit
einem lokalen Open-Weight-Modell ([Exponential Intelligence](../../funktionen/6.0/erweiterungen/expai.md); `README.md`).

| Anliegen des Kunden | Dokumentierte Antwort |
|---|---|
| „Wird es etwas Falsches veröffentlichen?“ | Nur Entwürfe; **Send for approval** führt den normalen Veröffentlichungsvorgang aus, sodass ein Freigabe-Workflow die Version zurückhält. |
| „Wohin gehen unsere Inhalte?“ | Eine Datenrichtlinie je Anbieter: Region `us`, `eu` oder `local`, Aufbewahrung, Regeln je Sektion und Klasse; jede Anfrage hält fest, wohin sie ging. |
| „Was wird es kosten?“ | Ein Nutzungsprotokoll mit den geschätzten Kosten jeder Anfrage, Limits je Benutzer, Tag, Monat, Site und Anbieter sowie Budgets. |
| „Können wir es abschalten?“ | `[AISettings] Enabled` schaltet es überall oder je Siteaccess ab. |
| „Kann unser Datenschutzbeauftragter es prüfen?“ | **EI: Summary** ist eine druckbare Seite für Datenschutzbeauftragte. |
| „Wird es protokolliert?“ | Jede Anfrage, jeder Entwurf und jede Zustandsänderung ist ein Ereignis des Audit-Kanals `ai`; Prompts, Antworten und Schlüssel werden nie aufgezeichnet. |

**Grenzen, die Sie nennen sollten.** Es braucht einen Anbieterschlüssel oder ein lokales Modell und „sendet nichts
irgendwohin, bis Sie einen Anbieterschlüssel hinzufügen“ (`README.md`). Die Preise der Anbieter sind Sache der Anbieter,
nicht des Projekts.

## Barrierefreiheit der Administration

**Was Sie sagen.** admin4 bietet Screenreadern und Tastaturen einen Sprunglink, benannte Landmarks, die WAI-ARIA-Muster
für Tabs und Bäume, benannte Formularfelder und Kalenderschaltflächen, und Tests in `tests/tests/design/` prüfen jedes
davon ([Das Design admin4, Landmarks und Namen](../../funktionen/6.0/admin4-design.md#landmarks-und-namen)). Die
Farb-Tokens bestehen einen Kontrasttest im hellen und im dunklen Modus, und Zielflächen sind mindestens 24 mal 24 Pixel
groß ([Wer ist die Exponential Foundation?, Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit)).
Die Hilfeseiten haben genau ein `h1`, benannte Landmarks und einen sichtbaren Fokusrahmen
([Hilfesystem](../../funktionen/6.0/hilfesystem.md)).

**Was Sie zeigen.** Drücken Sie einmal `Tab` (der Link **Zum Hauptinhalt springen**), drücken Sie `/`, um in die Suche
zu springen, öffnen Sie den Inhaltsbaum mit den Pfeiltasten und wechseln Sie den Farbmodus mit der Sonnen-Schaltfläche
([Tastenkürzel](../tastenkuerzel.md)).

**Grenzen, die Sie nennen sollten.** Das sind Eigenschaften der Administration. Die Barrierefreiheit der öffentlichen
Site hängt von den Templates ab, die das Projekt baut; die Quellen nennen keine Konformitätsstufe für öffentliche Sites.
Behandeln Sie sie als Projektanforderung und planen Sie die Tests dafür ein.

## Fragen zu Sicherheit, Audit und Compliance

| Frage des Kunden | Funktion | Nachweis |
|---|---|---|
| „Wer hat das geändert oder entfernt?“ | Das Audit-Protokoll zeichnet Anmeldungen, Änderungen an Inhalten, geschriebene Einstellungen, Cache-Leerungen, Deployments, Bestellungen und Änderungen der Mehrwertsteuer auf; standardmäßig eingeschaltet | [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md) |
| „Lässt sich das Protokoll manipulieren?“ | Jeder Eintrag trägt den Hash des vorherigen; `verify` zeigt, ob etwas geändert oder herausgeschnitten wurde | ebenda |
| „Wie schützen Sie vor eingeschleusten Skripten?“ | Eine Content Security Policy; standardmäßig in der Administration nur im Berichtsmodus, auf den Sites ausgeschaltet, mit dokumentierten Schritten zur Durchsetzung | [Content Security Policy](../../funktionen/6.0/content-security-policy.md) |
| „Können wir einen zweiten Faktor verlangen?“ | TOTP-Apps oder Codes per E-Mail | [Anmeldung in zwei Schritten](../../anleitungen/zwei-faktor-anmeldung.md) |
| „Sehen Sie Angriffe?“ | `security.log` klassifiziert Sondierungen und Angriffe; der Klassifizierer selbst blockiert nichts | [Anfrageprotokolle](../../funktionen/6.0/anfrageprotokolle.md) |
| „Welche Empfehlungen zur Härtung geben Sie?“ | Ein Leitfaden, „standardmäßig sicher im Sinne von OpenBSD“, mit einer Checkliste | [Sicherheitshärtung](../sicherheitshaertung.md), [Kapitel 13](../../installation/13-sicherheitshaertung.md) |
| „Wie gehen Sie mit Sicherheitslücken um?“ | Vertrauliche Meldung mit einer ersten Antwort „innerhalb eines Werktags“; Korrekturen auf `main` und im nächsten 6.0.x-Release | `SECURITY.md` |

**Grenzen, die Sie nennen sollten.** Die CSP wird standardmäßig nicht durchgesetzt; das Durchsetzen ist ein
Projektschritt. Die Quellen enthalten keine rechtlichen Aussagen oder Zertifizierungen (zum Beispiel zur DSGVO): Machen
Sie daraus Fragen an die eigenen Rechtsberater des Kunden.

## Geschwindigkeit

**Was Sie sagen.** Velocity ist „ein in PHP geschriebener Webserver, der mit Exponential ausgeliefert wird und die
Anwendung zwischen den Anfragen geladen hält“, und er ist die empfohlene Engine für jede Phase; FrankenPHP ist die
produktionsreife Alternative ([Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)). Ebenso wichtig: „Der
größte Teil der Geschwindigkeit kam von Korrekturen an Exponential und am Cache, nicht vom Prozessmodell des Servers“,
deshalb ist 6.0.15 „auch unter Apache oder Nginx unter Last schnell“ (`README.md`, „The part that matters even if you
never run Velocity“).

**Zahlen.** `README.md` dokumentiert 0,36 ms je gecachter Seite und dauerhaft 8.941 Anfragen pro Sekunde, gemessen auf
einer Maschine mit 12 Kernen, und sagt, dass die Zahlen mit Skripten reproduzierbar sind, die mit dem Projekt
ausgeliefert werden. Zitieren Sie sie nur mit diesem Kontext und bieten Sie an, mit `exp:benchmark` auf der eigenen
Hardware des Kunden zu messen ([Benchmarks](../../anleitungen/benchmarking.md)).

**Grenzen, die Sie nennen sollten.** Shared Hosting erlaubt in der Regel keinen lang laufenden Prozess; dort ist Apache
oder LiteSpeed mit `.htaccess` die Antwort. Nach der Änderung eines Templates, einer Einstellung oder einer Klasse muss
ein Velocity-Server neu gestartet werden (`README.md`).

## Hilfe und Dokumentation im Produkt

**Was Sie sagen.** Die Hilfe ist die eigene Dokumentation des Repositorys, angezeigt in der Administration: ein
Dashboard, eine Suche, Seiten nach Zielgruppen, eine Schaltfläche **Hilfe**, die die Seite zum aktuellen Bildschirm
öffnet, und eine Seite **Fehler melden**, die ein Issue vorbereitet ([Hilfesystem](../../funktionen/6.0/hilfesystem.md)).
Die Dokumentation ist freie Dokumentation unter der GNU FDL 1.3, sodass ein Kunde sie für seine eigenen Mitarbeiter
kopieren, übersetzen und anpassen darf ([Freie Dokumentation](../freie-dokumentation.md)).

**Was Sie zeigen.** Drücken Sie auf dem Dashboard **Hilfe**, suchen Sie nach „publish“, öffnen Sie eine Seite und
drücken Sie **Als PDF herunterladen**.

## PDF und E-Books

| Ausgabe | Was sie leistet | Quelle |
|---|---|---|
| PDF-Export von Inhalten | **Setup > PDF-Export**: Dateien, die sich öffnen lassen, Text, der korrekt lesbar ist, Bilder, die passen, UTF-8 mit einer TrueType-Schrift, Ihre eigene Fußzeile | [PDF-Export](../../funktionen/6.0/pdf-export.md) |
| Eine Hilfeseite als PDF | **Als PDF herunterladen** auf jeder Hilfeseite, erzeugt auf dem Server mit der eigenen PDF-Bibliothek des Kernels, ohne externen Dienst | [Hilfesystem, Drucken und PDF](../../funktionen/6.0/hilfesystem.md#drucken-und-pdf) |
| Ein Hilfehandbuch als EPUB 3 oder PDF | Handbücher, die als Inhalte gepflegt werden (Phase 1 in 6.0.15), **EPUB herunterladen** und **PDF herunterladen** auf der Seite des Handbuchs | [Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md) |

**Grenzen, die Sie nennen sollten.** EPUB ist für Hilfehandbücher dokumentiert, die als Inhalte gepflegt werden, nicht
als allgemeiner Export von Site-Inhalten. Arabische Buchstaben werden in PDFs der Hilfe in ihren Grundformen gezeichnet,
ohne Verbindung ([Hilfesystem](../../funktionen/6.0/hilfesystem.md#drucken-und-pdf)).

## Strukturierte Inhalte, Freigabe und Versionen

**Was Sie sagen.** „Als CMS ist sein bemerkenswertestes Merkmal das vollständig anpassbare und erweiterbare
Inhaltsmodell“ (`README.md`). Inhaltsklassen legen die Felder jedes Inhaltstyps fest; jede Bearbeitung ist ein Entwurf,
jede Veröffentlichung behält die vorherige Version, und ein Freigabe-Workflow kann eine Version zurückhalten, bis jemand
sie freigibt ([Inhaltsmodell](../../anleitungen/inhaltsmodell-und-bearbeitung.md), [Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md),
[Workflows](../../anleitungen/workflows.md)). Fein abgestufte Rollen und Richtlinien entscheiden, wer was tun darf
([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)).

**Was Sie zeigen.** Das [Ordner-Beispiel](../rundgang-durch-die-administration.md#in-der-praxis-einen-ordner-bearbeiten): bearbeiten, einen Entwurf
speichern, die Vorschau in Telefonbreite ansehen, veröffentlichen, Versionen vergleichen.

## Shop, API und Integration

- **Shop.** „Advanced e-Commerce Stores (Built in)“ steht in der Funktionsliste der README; der Tab **Shop** öffnet ein
  Dashboard mit Bestellstatus ([Shop-Dashboard](../../funktionen/6.0/shop-dashboard.md)); PayPal ist eine Erweiterung
  ([Wer ist 7x?, Erweiterungen](../wer-ist-7x.md#das-ökosystem-der-erweiterungen)).
- **API.** REST v1 und v2 kommen aus einer Erweiterung, mit OAuth2-Tokens und persönlichen API-Schlüsseln
  ([REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)).
- **Erweiterungen.** `composer.json` fordert 66 `se7enxweb/*`-Pakete an; viele sind inaktiv, bis sie eingeschaltet
  werden ([Die Standardauswahl an Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md)).

**Grenzen, die Sie nennen sollten.** Die Quellen führen außer der PayPal-Erweiterung keine zertifizierten
Zahlungsanbieter auf und nennen keine Integrationen von Drittanbietern. Behandeln Sie jede Integration als Frage zum
Projektumfang ([Aufwand schätzen und Umfang festlegen mit Erweiterungen](../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)).

## Freie Wahl von Hosting, Datenbank und Betriebssystem

**Was Sie sagen.** Sechs Datenbanken werden unterstützt, „in der Reihenfolge der Präferenz: 1. SQLite 3 (Standard) ·
2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL“ ([Unterstützte Datenbanken](../datenbankunterstuetzung.md)).
Das Betriebssystem kann „fast jedes sein, auf dem PHP 8.0 oder später läuft“, eingeteilt in vier ehrliche Stufen
([Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md)). Eine Site kann mit dokumentierten Werkzeugen in eine andere
Datenbank umziehen ([Eine Site in eine andere Datenbank verschieben](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)).

**Grenzen, die Sie nennen sollten.** SQLite bedient eine Site von einer Maschine aus; mehrere Webserver brauchen einen
Datenbankserver. MariaDB und Oracle laufen nicht in der CI des Projekts ([Testabdeckung](../datenbankunterstuetzung.md#testabdeckung-ci)).
Für Microsoft SQL Server gibt es in 6.0.x keinen Treiber.

## Den Betrieb der Site im Alltag

| Bedarf | Funktion | Nachweis |
|---|---|---|
| Den Fehler hinter einer Meldung finden | Eine Fehlerseite zeigt eine Referenz; der Log-Viewer findet die zugehörige Anfrage | [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md), [Log-Viewer](../../funktionen/6.0/log-betrachter.md) |
| Ungenutzte Dateien aufräumen | Nicht referenzierte Objekte, defekte Beziehungen, Speicherdateien | [Inhaltsprüfungen](../../funktionen/6.0/inhaltspruefungen.md) |
| Vor einem Upgrade wissen, ob Dateien abweichen | **Setup > Upgrade-Prüfung** | [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| Die Site für Arbeiten vom Netz nehmen | Wartungsmodus | [Wartungsmodus](../../funktionen/6.0/wartungsmodus.md) |
| Inhalte über eine ID oder einen Pfad finden | **Setup > Inhaltsfinder** | [Der Inhaltsfinder](../../funktionen/6.0/inhalts-finder.md) |

## Ein Arbeitsblatt zur Bedarfsanalyse

Kopieren Sie es in Ihre Gesprächsnotizen und füllen Sie es gemeinsam mit dem Kunden aus.

```text
Customer: <organization>            Date: <date>          Contact role: <role, not a name>

1. Sites: how many sites, brands or countries?        -> <answer>   -> multisite rows
2. Languages: which, and is any written right to left? -> <answer>   -> languages rows
3. Who edits pages, and do they need developers today? -> <answer>   -> layouts rows
4. Approval: who must approve before publishing?       -> <answer>   -> workflows rows
5. AI: wanted? which region may content go to?         -> <answer>   -> expai rows
6. Security and audit duties (internal or legal)?       -> <answer>   -> security rows
7. Accessibility duties for staff or the public?        -> <answer>   -> accessibility rows
8. Database and hosting rules of their IT?              -> <answer>   -> freedom rows
9. Shop, API, integrations?                             -> <answer>   -> shop and API rows
10. Performance expectations, and how they measure it?  -> <answer>   -> speed rows
Pages to send afterwards: <list of links from the map>
Open questions the documentation does not answer: <list>
```

## Was Sie nicht behaupten sollten

- Keine Preise, SLAs, Reaktionszeiten, Kundennamen, Benutzerzahlen oder Marktanteile: Nichts davon steht in den
  Quellen. Preise und Bedingungen der Dienstleistungen von 7x stehen auf der eigenen Website von 7x, nicht hier
  ([Wer ist 7x?, Dienstleistungen und Hosting](../wer-ist-7x.md#dienstleistungen-und-hosting)).
- Keine Funktion, die keine Seite hier beschreibt. „Auf der Roadmap“ stimmt nur, wenn eine Seite das sagt (zum Beispiel
  die späteren Phasen in [Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md)).
- Kein Vergleich mit einem namentlich genannten Wettbewerber; siehe [Exponential sachlich vergleichen](exponential-sachlich-vergleichen.md).
- Keine heutige Verwendung des alten Produktnamens: Exponential führt die Codebasis von eZ Publish fort, einem Produkt,
  das sein Hersteller nach fast 18 Jahren eingestellt hat ([Wer ist 7x?](../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021)).

## Verwandte Seiten

- [Vertrieb und Marketing: hier beginnen](vertrieb-und-marketing-hier-beginnen.md)
- [Positionierung und Nutzenversprechen](positionierung-und-nutzenversprechen.md)
- [Demo-Drehbücher](demo-drehbuecher.md)
- [Einwandbehandlung: Sicherheit, Langlebigkeit und Support](einwandbehandlung.md)
- [Exponential sachlich vergleichen](exponential-sachlich-vergleichen.md)
- [Was Exponential ist: die Produktfamilie](../fuehrung/was-exponential-ist.md)
- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
