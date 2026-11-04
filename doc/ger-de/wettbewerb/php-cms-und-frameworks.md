---
title: PHP-Content-Management-Systeme und Frameworks
audience: admin, developer, businesses, partner
summary: Jedes verbreitete PHP-Content-Management-System, jede Shop-Plattform und jedes Framework, nach Lizenz geordnet, mit der Stelle, an der es einen Website-Betreiber ohne Budget im Stich lässt, der trotzdem über Jahre eine moderne, zuverlässige Website braucht, und was Exponential stattdessen tut.
keywords: Wettbewerb, Vergleich, WordPress, Drupal, Joomla, TYPO3, Contao, Neos, Craft CMS, Statamic, Kirby, October CMS, Winter CMS, Grav, Pimcore, Sulu, Magento, PrestaShop, WooCommerce, Laravel, Symfony, Lizenz, freie Software, Gesamtkosten
---

# PHP-Content-Management-Systeme und Frameworks

PHP betreibt die meisten Content-Management-Systeme des Webs, und Exponential ist eines davon. Diese Seite vergleicht
Exponential mit jedem anderen verbreiteten PHP-Content-Management-System, jeder Shop-Plattform und jedem Framework,
aus einem einzigen Blickwinkel: **ein Website-Betreiber ohne Budget für Lizenzen, Abonnements oder einen Neubau alle
paar Jahre, der trotzdem eine moderne Website braucht, die weiter funktioniert und sich ohne Mehrkosten zu fast allem
erweitern lässt.** Sie gehört zum Buch [Exponential im Wettbewerb](README.md).

Die Produkte sind nach ihren Lizenzbedingungen geordnet, denn die Lizenz entscheidet, was Sie mit der Software tun
dürfen, sobald Sie von ihr abhängen. Jeder Eintrag sagt, was das Produkt ist, welche Lizenz und welches
Geschäftsmodell es hat, was es gut kann, wo es den Nutzer ohne Budget oder mit langem Horizont im Stich lässt und was
Exponential stattdessen tut. Jede Angabe über ein anderes Produkt stammt aus seinem eigenen Repository, seiner
Lizenzdatei, seiner Website oder einer öffentlichen Quelle, alle unter [Quellen](#9-quellen) aufgeführt; Lizenzen,
Versionen und Aktivität wurden am 4. November 2026 aus den Repositorys der Projekte gelesen. Preise ändern sich: Die
genannten Beträge sind die an diesem Tag veröffentlichten Listenpreise der Hersteller.

## Inhalt

1. [Der Maßstab](#1-der-maßstab)
2. [Was Exponential Ihnen gibt, geprüft](#2-was-exponential-ihnen-gibt-geprüft)
3. [GPL: starkes Copyleft](#3-gpl-starkes-copyleft)
4. [LGPL, MPL und OSL: schwaches Copyleft](#4-lgpl-mpl-und-osl-schwaches-copyleft)
5. [MIT, BSD und Apache: freizügig](#5-mit-bsd-und-apache-freizügig)
6. [Quelloffen, aber proprietär](#6-quelloffen-aber-proprietär)
7. [Die Frameworks: was Sie selbst bauen](#7-die-frameworks-was-sie-selbst-bauen)
8. [Auf einen Blick](#8-auf-einen-blick)
9. [Quellen](#9-quellen)

## 1. Der Maßstab

Ein Produkt besteht für den Nutzer, an den diese Seite denkt, wenn es:

- **nichts kostet**, im Produktivbetrieb, auf beliebig vielen Websites, ohne Lizenzschlüssel, Gebühr pro Website oder
  bezahlten Update-Kanal;
- **seine Funktionen im freien Produkt behält**, statt die Teile, die ein Unternehmen braucht, in eine bezahlte
  Edition, einen Marktplatz oder einen gehosteten Dienst zu verlegen;
- **moderne Dinge von Haus aus kann**: strukturierte Inhalte, viele Sprachen, Rollen und Rechte, Workflows,
  Schnittstellen, Suche, Layouts, Anmeldung mit zweitem Faktor, ein Audit-Protokoll;
- **für diese Grundlagen nicht von einer Handvoll Plugins Dritter abhängt**, deren Eigentümer Bedingungen ändern, die
  Pflege einstellen oder verschwinden können;
- **eine Website von einer Hauptversion zur nächsten mitnimmt**, ohne Neubau;
- **auf gewöhnlichem Hosting läuft**, mit einer Auswahl an Datenbanken und auf aktuellen PHP-Versionen.

## 2. Was Exponential Ihnen gibt, geprüft

Jede Aussage dieser Seite über Exponential ist eine der folgenden, und jede verweist auf die Seite, die sie
dokumentiert:

- **E1, freie Software, vollständig.** Der Kernel steht unter GPL-2.0-or-later (`composer.json`, `LICENSE.md`); die
  Extensions der Distributionen sind auf GitHub und Packagist veröffentlicht. Der Code hat keinen Lizenzschlüssel,
  keine Editionsprüfung und keine Gebühr pro Website. Siehe [Distributionen von Exponential](../DISTRIBUTIONS.md).
- **E2, aktuelles PHP.** Exponential läuft auf PHP 7.4 und wird auf PHP 8.0 bis 8.5 unterstützt; die kontinuierliche
  Integration testet jede dieser Versionen ([Exponential 6.0 installieren](../INSTALL.md)).
- **E3, fünf Datenbanken.** MySQL und MariaDB, PostgreSQL, SQLite, MongoDB und Oracle
  ([SQLite](../funktionen/6.0/sqlite-datenbank.md), [MongoDB](../funktionen/6.0/mongodb-datenbankunterstuetzung.md)).
- **E4, Kontinuität seit 2002.** Das Inhaltsmodell, die Template-Sprache und der Aufbau der Extensions gehen auf
  eZ publish 3 zurück, das 2002 erschien; Websites und Pakete dieser Linien werden weitergeführt, nicht neu gebaut
  ([Geschichte von Exponential](../HISTORY.md)).
- **E5, die Geschäftsfunktionen stecken im Kernel**, nicht in Plugins: Inhaltsklassen für strukturierte Inhalte,
  Versionen und Entwürfe, mehr als 150 Oberflächensprachen und mehrsprachige Inhalte ([Sprachen](../sprachen.md)),
  Rollen und Richtlinien, Workflows, Anmeldung mit zweitem Faktor
  ([zweiter Faktor](../funktionen/6.0/zweiter-schritt-auf-anderen-anmeldewegen.md)), ein
  [Audit-Protokoll](../funktionen/6.0/pruefprotokoll.md), eine [GraphQL-Schnittstelle](../funktionen/6.0/graphql.md) neben
  REST, [Migrationen](../funktionen/6.0/migrationen.md), ein eingebautes Hilfecenter
  ([Hilfesystem](../funktionen/6.0/hilfesystem.md)), der [statische Cache](../funktionen/6.0/generator-fuer-den-statischen-cache.md) und
  das [statische Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md).
- **E6, ein eigener Anwendungsserver.** Exponential Velocity liefert die Website mit dauerhaft laufenden Workern aus,
  neben dem üblichen Apache mit PHP-FPM
  ([Server mit dauerhaften Workern](../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)).
- **E7, Layouts und Bearbeiten im Frontend** mit Exponential Layouts und der Website-Werkzeugleiste, mit den
  Distributionen ausgeliefert ([Distributionen von Exponential](../DISTRIBUTIONS.md)).

Auch Exponential hat Grenzen: Es ist eine vollständige Plattform, deshalb ist eine einseitige Visitenkarten-Website
anderswo einfacher, und seine Community ist kleiner als die von WordPress oder Drupal. Die Seite
[Community](../COMMUNITY.md) sagt, wo Sie Hilfe finden.

## 3. GPL: starkes Copyleft

Die GPL hält die Software für jeden frei, der sie erhält. Das schützt den Nutzer; die folgenden Risiken entstehen aus
den Geschäftsmodellen und den Plugin-Ökonomien rund um diese Produkte, nicht aus der Lizenz.

### WordPress

- **Was es ist:** das meistgenutzte PHP-Publishing-System, betrieben vom Projekt WordPress.org; WordPress.com ist ein
  eigener gehosteter Dienst von Automattic.
- **Lizenz und Modell:** GPL-2.0 oder jede spätere Version (`license.txt` im Repository). WordPress.com verkauft
  gehostete Tarife; was eine Website dort installieren darf, hängt vom Tarif ab.
- **Stärke:** ein sehr großes Ökosystem aus Themes, Plugins, Hostern und Menschen, die es kennen.
- **Wo es Sie im Stich lässt:** strukturierte Inhalte, eigene Felder, mehrsprachige Websites, Workflows, das Bearbeiten
  von Rollen, Anmeldung mit zweitem Faktor und ein Audit-Protokoll gehören nicht zum Kern; sie kommen aus Plugins,
  viele davon mit bezahlten Stufen. Diese Abhängigkeit ist ein reales Risiko: 2024 sperrte WordPress.org im Streit
  zwischen Automattic und WP Engine WP Engine von seinen Servern aus und übernahm den Verzeichniseintrag von WP Engines
  Plugin Advanced Custom Fields als „Secure Custom Fields“; WP Engine erwirkte am 10. Dezember 2024 eine einstweilige
  Verfügung. Eine Website, die sich auf ein einziges Plugin verließ, stand zwischen zwei Unternehmen.
- **Exponential stattdessen:** Inhaltsklassen, Übersetzungen, Workflows, Rollen und Richtlinien, der zweite Faktor
  und das Audit-Protokoll gehören zum Kernel (E5), unter einer freien Lizenz (E1), ohne Plugin-Anbieter zwischen Ihnen
  und ihnen.

### ClassicPress

- **Was es ist:** ein Fork von WordPress ohne den Block-Editor (Gutenberg).
- **Lizenz und Modell:** GPL-2.0 (Repository); Community-Projekt.
- **Stärke:** bewahrt das klassische Bearbeiten von WordPress für Websites, die keine Blöcke wollen.
- **Wo es Sie im Stich lässt:** es erbt die Abhängigkeit von WordPress von Plugins für strukturierte Inhalte und
  Geschäftsfunktionen, mit einem viel kleineren Projekt dahinter (863 Sterne auf GitHub gegenüber 21.517 bei
  WordPress).
- **Exponential stattdessen:** dieselben Funktionen im Kernel (E5) und eine Code-Linie, die seit 2002 weitergeführt
  wird (E4).

### Drupal

- **Was es ist:** ein leistungsfähiges Community-CMS, stark bei strukturierten Inhalten und Zugriffsrechten.
- **Lizenz und Modell:** GPL-2.0-or-later (`composer.json`); Community-Projekt mit einem großen Agentur-Ökosystem.
- **Stärke:** Inhaltstypen, Felder, Views und Rechte im Kern; eine große Bibliothek beigesteuerter Module.
- **Wo es Sie im Stich lässt:** Drupal 8 (2015) wurde auf Symfony-Komponenten neu geschrieben, deshalb zogen Websites
  von Drupal 7 per Migration um, in der Praxis ein Neubau, statt per Aktualisierung an Ort und Stelle. Das Ende von
  Drupal 7 wurde zweimal verschoben und auf den 5. Januar 2025 gelegt. Websites, die die Migration nicht bezahlen
  konnten, blieben auf einer Version ohne Unterstützung.
- **Exponential stattdessen:** dieselben Stärken (strukturierte Inhalte, Rollen und Richtlinien) und ein
  Aktualisierungsweg, der seit 2002 nicht abgebrochen ist (E4, E5).

### Backdrop CMS

- **Was es ist:** ein Community-Fork von Drupal 7 mit eingebautem Aktualisierungsweg von Drupal 7.
- **Lizenz und Modell:** GPL-2.0 (Repository); Community-Projekt.
- **Stärke:** der beste Weg für eine Drupal-7-Website, die sich keine Migration zum modernen Drupal leisten kann.
- **Wo es Sie im Stich lässt:** ein viel kleineres Projekt (1.051 Sterne), und viele beigesteuerte Module von Drupal 7
  gibt es nur, wenn jemand sie portiert.
- **Exponential stattdessen:** eine gepflegte Plattform, deren eigene Geschichte dieselbe Art von Kontinuität ist, mit
  den Geschäftsfunktionen im Kernel (E4, E5).

### Joomla

- **Was es ist:** ein langjähriges Community-CMS; sein neuestes Release ist 6.2.0 (13. Oktober 2026).
- **Lizenz und Modell:** GPL-2.0 (Repository); Community-Projekt.
- **Stärke:** mehrsprachige Websites, Benutzergruppen und Zugriffsebenen im Kern, ein breites Extension-Verzeichnis.
- **Wo es Sie im Stich lässt:** Geschäftsfunktionen darüber hinaus kommen aus Extensions Dritter, also hängt eine
  Website davon ab, dass ihre Extension-Anbieter jede Hauptversion mitgehen; Joomla brauchte dafür schon früher
  Übergangshilfen (einen „Legacy-Modus“ für ältere Extensions in Joomla 1.5). Seine Geschichte vermerkt auch, dass das
  Projekt Joomla 4 die Erwartungen seiner Entwickler zunächst nicht erfüllte.
- **Exponential stattdessen:** Templates und Extensions, die über Releases hinweg weiter funktionieren (E4), und die
  Geschäftsfunktionen im Kernel statt in Extensions Dritter (E5).

### TYPO3

- **Was es ist:** ein Enterprise-CMS, stark im deutschsprachigen Raum.
- **Lizenz und Modell:** GPL-2.0-or-later (`composer.json`). Jede Version mit Langzeitunterstützung (LTS) wird eine
  feste Zeit gepflegt; danach kommen Sicherheitskorrekturen über die Extended Long Term Support (ELTS), einen bezahlten
  Dienst der TYPO3 GmbH.
- **Stärke:** Multi-Site, mehrsprachige Websites und eine ausgereifte Redaktionsoberfläche.
- **Wo es Sie im Stich lässt:** eine Website, die nicht jeden LTS-Zyklus mitgehen kann, muss für ELTS bezahlen oder
  ohne Korrekturen laufen; jeder LTS-Schritt kann Änderungen an Extensions und Templates erfordern.
- **Exponential stattdessen:** kein bezahlter Kanal für Sicherheitskorrekturen: Sie erscheinen in den freien
  Releases (E1), und der Aktualisierungsweg hält Websites lauffähig (E4).

### Neos

- **Was es ist:** eine Plattform für Inhaltsanwendungen mit Bearbeitung direkt auf der Seite.
- **Lizenz und Modell:** GPL-3.0 (Repository); Community-Projekt mit Partneragenturen. Neuestes Release 9.2.0.
- **Stärke:** echtes Bearbeiten auf der Seite und Inhaltsdimensionen (Sprache, Markt).
- **Wo es Sie im Stich lässt:** ein kleines Projekt (265 Sterne auf seinem Entwicklungs-Repository), daher kennen es
  wenige Hoster, Agenturen und Extensions.
- **Exponential stattdessen:** Bearbeiten im Frontend mit der Website-Werkzeugleiste und Layouts (E7), auf einer
  längeren, stabilen Linie (E4).

### MODX

- **Was es ist:** ein CMS und Framework, das das Markup ganz dem Entwickler überlässt.
- **Lizenz und Modell:** GPL-2.0 (Repository); Community-Projekt.
- **Stärke:** volle Kontrolle über das HTML; Templates und Chunks.
- **Wo es Sie im Stich lässt:** Geschäftsfunktionen (Mehrsprachigkeit, Workflows, strukturierte Inhalte über
  Template-Variablen hinaus) kommen aus Add-ons; eine Website ist nur so gut gepflegt wie ihre Add-ons.
- **Exponential stattdessen:** diese Funktionen im Kernel (E5).

### Textpattern

- **Was es ist:** ein kleines, langlebiges Publishing-System; neuestes Release 4.9.2 (1. November 2026).
- **Lizenz und Modell:** GPL-2.0 (Repository); Community-Projekt.
- **Stärke:** sauberes, schlankes Publizieren für Schreibende.
- **Wo es Sie im Stich lässt:** gebaut für Blogs und kleine Websites, nicht für Rollen, Workflows oder mehrsprachige
  strukturierte Inhalte.
- **Exponential stattdessen:** wächst auf derselben Plattform von einer kleinen zu einer großen Website (E5).

### WooCommerce und OpenCart

- **Was sie sind:** WooCommerce ist ein Shop-Plugin für WordPress; OpenCart ist ein eigenständiger Shop.
- **Lizenz und Modell:** WooCommerce GPL-2.0, OpenCart GPL-3.0-or-later (Repositorys). Beide haben Marktplätze für
  Extensions; auf woocommerce.com sind viele Extensions bezahlte Abonnements („Paid plans start at $10/month“ ist ein
  typischer Eintrag).
- **Stärke:** ein schneller Start für einen kleinen Shop.
- **Wo sie Sie im Stich lassen:** Zahlung, Versand, Steuern und Abonnements, die ein echter Shop braucht, sind oft
  bezahlte Extensions mit jährlicher Verlängerung, also wachsen die laufenden Kosten eines „kostenlosen“ Shops mit dem
  Shop.
- **Exponential stattdessen:** ein eingebauter Shop (Bestellungen, das Shop-Dashboard) im freien Kernel (E1, E5);
  siehe die Shop-Seiten dieses Hilfecenters.

## 4. LGPL, MPL und OSL: schwaches Copyleft

### Contao

- **Was es ist:** ein bei deutschen Agenturen beliebtes CMS auf Symfony.
- **Lizenz und Modell:** LGPL-3.0 (Repository); Community-Projekt mit einem veröffentlichten Releaseplan der
  unterstützten Versionen.
- **Stärke:** Barrierefreiheit und ein klares Redaktions-Backend.
- **Wo es Sie im Stich lässt:** jede Version wird nur für ihren geplanten Zeitraum unterstützt; sicher bleibt, wer dem
  Releaseplan folgt.
- **Exponential stattdessen:** Korrekturen für die Linie, die Sie betreiben, in freien Releases, mit einem
  Aktualisierungsweg ohne Neubau (E1, E4).

### ProcessWire

- **Was es ist:** ein CMS und Framework rund um einen Seitenbaum und eigene Felder.
- **Lizenz und Modell:** der Kern steht unter MPL-2.0, die Site-Profile unter MIT (`LICENSE.TXT`); kostenlos.
- **Stärke:** eine saubere API und flexible Felder.
- **Wo es Sie im Stich lässt:** rund 96 % der Commits in seinem Repository stammen von einem einzigen Entwickler
  (Beitragsstatistik von GitHub), ein Kontinuitätsrisiko für eine Website, die viele Jahre halten soll.
- **Exponential stattdessen:** eine Plattform, die ein Unternehmen und eine Stiftung pflegen, mit dokumentierter
  Kontinuität (E4; [Geschichte von Exponential](../HISTORY.md)).

### PrestaShop

- **Was es ist:** ein quelloffenes Shop-System.
- **Lizenz und Modell:** der Kern steht unter OSL-3.0, die Module unter AFL-3.0 (`LICENSE.md`). Der Marktplatz
  PrestaShop Addons verkauft Module und Themes.
- **Stärke:** ein vollständiger Shop-Kern, stark in Europa.
- **Wo es Sie im Stich lässt:** viele Funktionen, die ein Shop braucht, kommen aus dem Marktplatz, auf dem Module und
  Themes verkauft werden, also wachsen die laufenden Kosten mit dem Shop.
- **Exponential stattdessen:** der Shop gehört zum freien Kernel (E1, E5).

### Magento Open Source und Adobe Commerce

- **Was es ist:** eine große Shop-Plattform; Magento Open Source ist kostenlos, Adobe Commerce ist die lizenzierte
  Edition.
- **Lizenz und Modell:** Magento Open Source steht unter OSL-3.0 (Repository); Adobe Commerce verkauft Adobe, das eine
  Lebenszyklus-Richtlinie mit festen Unterstützungszeiträumen pro Release veröffentlicht.
- **Stärke:** sehr große Kataloge und komplexer Handel.
- **Wo es Sie im Stich lässt:** Magento 1 erreichte am 30. Juni 2020 sein Lebensende, und der Umstieg auf Magento 2
  bedeutete einen Neubau. Es braucht mehr als PHP und eine Datenbank (einen Suchdienst wie OpenSearch), und viele
  seiner Geschäftsfunktionen stecken in der bezahlten Adobe-Edition.
- **Exponential stattdessen:** Handel und Inhalte auf gewöhnlichem Hosting, mit SQLite als Möglichkeit für kleine
  Websites (E3, E5), und ohne bezahlte Edition (E1).

### Akeneo PIM Community Edition

- **Was es ist:** ein System für Produktinformationen (PIM).
- **Lizenz und Modell:** OSL-3.0 (`LICENCE.txt`); Akeneo verkauft Enterprise-Editionen und einen Cloud-Dienst.
- **Stärke:** Produktdaten für viele Kanäle.
- **Wo es Sie im Stich lässt:** es ist ein Werkzeug für Produktdaten, keine Website-Plattform; eine Website braucht
  daneben weiterhin ein CMS.
- **Exponential stattdessen:** strukturierte Inhaltsklassen, die Produktdaten halten und auf derselben Plattform
  veröffentlichen (E5).

## 5. MIT, BSD und Apache: freizügig

Freizügige Lizenzen erlauben jedem, auch dem Hersteller, den Code später zu schließen. Zwei Produkte dieser Seite haben
das getan: October CMS und Pimcore (Abschnitt 6).

### Concrete CMS

- **Was es ist:** ein CMS mit Bearbeitung direkt auf der Seite.
- **Lizenz und Modell:** MIT (Repository); der Hersteller verkauft Hosting, auch zertifiziertes Hosting für
  Compliance-Anforderungen.
- **Stärke:** Bearbeiten auf der Seite, das auch Redakteure ohne Technikwissen verstehen.
- **Wo es Sie im Stich lässt:** das Geschäft des Herstellers sind Hosting und Dienstleistungen, und sein Marktplatz
  verkauft die Add-ons für Anforderungen über den Kern hinaus.
- **Exponential stattdessen:** Bearbeiten im Frontend (E7) auf jedem Hosting Ihrer Wahl, mit fünf Datenbanken (E3).

### Grav

- **Was es ist:** ein Flat-File-CMS: keine Datenbank, Inhalte in Markdown-Dateien.
- **Lizenz und Modell:** MIT (Repository); ein Shop verkauft Premium-Plugins und -Themes.
- **Stärke:** schnelle, einfache Websites ohne Datenbank.
- **Wo es Sie im Stich lässt:** Dateien skalieren nicht auf viele Redakteure, Rollen und Workflows, und strukturierte,
  verknüpfte Inhalte sind begrenzt.
- **Exponential stattdessen:** SQLite bietet dieselbe Einfachheit ohne Datenbankserver, mit dem vollen Inhaltsmodell,
  Rollen und Workflows (E3, E5).

### Winter CMS

- **Was es ist:** der Community-Fork von October CMS, im März 2021 entstanden „due to a difference in open source
  management philosophies“ (aus seiner README), auf Laravel.
- **Lizenz und Modell:** MIT (Repository); Community-Projekt.
- **Stärke:** hält die Entwicklererfahrung von October frei.
- **Wo es Sie im Stich lässt:** ein kleines Projekt (1.523 Sterne), das vom Release-Zyklus des darunterliegenden
  Laravel abhängt.
- **Exponential stattdessen:** eine freie Lizenz, die das Projekt seit mehr als zwei Jahrzehnten behält (E1, E4).

### Bolt und Sulu

- **Was sie sind:** zwei CMS auf Symfony.
- **Lizenz und Modell:** beide MIT (Repositorys). Sulu 3 braucht PHP 8.2 oder neuer und Symfony 6.4 bis 7.4 (aus
  seiner README).
- **Stärke:** moderne Symfony-Codebasen; Sulu ist stark bei mehrsprachigen Websites mit mehreren Portalen.
- **Wo sie Sie im Stich lassen:** sie folgen den Hauptversionen von Symfony, also aktualisiert eine Website im Takt von
  Symfony; Sulu 3 braucht bereits PHP 8.2.
- **Exponential stattdessen:** läuft auf PHP 7.4 bis 8.5 (E2), und seine Symfony-basierten Distributionen sind eine
  Wahl, kein erzwungener Umzug ([Distributionen von Exponential](../DISTRIBUTIONS.md)).

### Silverstripe CMS

- **Was es ist:** ein CMS und Framework, beliebt in Neuseeland und Australien.
- **Lizenz und Modell:** BSD-3-Clause (Repository); neuestes Release 6.3.1.
- **Stärke:** ein sauberes Framework für eigene Datenmodelle.
- **Wo es Sie im Stich lässt:** ein kleineres Ökosystem (516 Sterne), und ein großer Teil einer Geschäftswebsite wird
  von Entwicklern als eigene Datenmodelle gebaut.
- **Exponential stattdessen:** Inhaltsklassen, die Redakteure und Administratoren im Browser anlegen (E5), auf einem
  Aktualisierungsweg, der Code lauffähig hält (E4).

### Bagisto, Twill und Filament

- **Was sie sind:** Produkte auf Laravel: Bagisto ist ein Shop, Twill ein CMS-Baukasten (von AREA 17), Filament ein
  Baukasten für Administrationsoberflächen.
- **Lizenz und Modell:** Bagisto MIT, Twill Apache-2.0, Filament MIT (Repositorys).
- **Stärke:** schnelle Entwicklung für Laravel-Teams.
- **Wo sie Sie im Stich lassen:** es sind Baukästen: Jemand muss die Website darauf bauen und pflegen, und der
  Release-Zyklus von Laravel bestimmt den Takt der Aktualisierungen.
- **Exponential stattdessen:** eine vollständige Plattform, mit der Redakteure vom ersten Tag an arbeiten (E5, E7).

### ExpressionEngine

- **Was es ist:** ein langjähriges kommerzielles CMS, das kostenlos wurde.
- **Lizenz und Modell:** Apache-2.0 (Repository) und ein kostenloser Download.
- **Stärke:** flexible Channels und Felder.
- **Wo es Sie im Stich lässt:** heute ein kleines Projekt (506 Sterne), und eine freizügige Lizenz, die, wie October
  CMS und Pimcore zeigten, einem Eigentümer erlaubt, spätere Versionen zu schließen.
- **Exponential stattdessen:** eine Copyleft-Lizenz, die es frei hält (E1).

### Kleine Systeme und Flat-File-Systeme

| Projekt | Lizenz | Stand am 4. November 2026 | Wo es Sie im Stich lässt |
|---|---|---|---|
| Typemill | MIT | aktiv, 2.28.0 | Dokumentations-Websites in Markdown, kein allgemeines CMS |
| Bludit | MIT | aktiv, 3.22.0 | Inhalte in JSON-Dateien, keine Datenbank; nur kleine Websites |
| WonderCMS | MIT | aktiv, 3.7.2 | eine PHP-Datei und eine JSON-Datei; nur kleine Websites |
| Pico | MIT | letztes Release 2.1.4 im August 2020 | seit sechs Jahren kein Release |
| Croogo | MIT | letztes Release 4.0.7 im Oktober 2020, letzte Änderung Juli 2023 | inaktiv |
| Pagekit | MIT | auf GitHub archiviert; letztes Release 1.0.18 im Januar 2020 | eingestellt |
| CMS Made Simple | siehe seine Website | nicht auf GitHub | kleine Community |

**Exponential stattdessen:** SQLite für eine Website ohne Datenbankserver (E3) und eine gepflegte Plattform, die mit
der Website wächst (E5).

## 6. Quelloffen, aber proprietär

Diese Produkte veröffentlichen ihren Quellcode, aber ihre Lizenzen geben nicht die vier Freiheiten freier Software:
Sie brauchen eine bezahlte Lizenz für den Produktivbetrieb, für mehr als eine Website oder für Updates.

### Craft CMS

- **Was es ist:** ein ausgefeiltes CMS von Pixel & Tonic.
- **Lizenz und Modell:** die „Craft License“, proprietär (`composer.json`: `proprietary`). Craft Solo ist für einen
  Benutzer kostenlos; Team kostet 279 $ und Pro 399 $ pro Projekt, jeweils mit einem Jahr Updates und danach 99 $ im
  Jahr für Updates. Die Lizenz ist unbefristet; wer nicht verlängert, verliert den Zugang zu Updates.
- **Stärke:** ein gut durchdachtes Inhaltsmodell und ein guter Editor.
- **Wo es Sie im Stich lässt:** jedes Projekt braucht seine eigene Lizenz, mehr als ein Redakteur braucht eine bezahlte
  Edition, und aktuell zu bleiben kostet eine Jahresgebühr pro Website.
- **Exponential stattdessen:** beliebig viele Benutzer und Websites, alle Updates kostenlos (E1).

### Statamic

- **Was es ist:** ein CMS auf Laravel, mit Dateien oder Datenbank.
- **Lizenz und Modell:** proprietär (`LICENSE.md`): Statamic Core ist kostenlos; Pro kostet 349 $ pro Website mit einem
  Jahr Updates und danach 99 $ im Jahr. Die Lizenz verbietet, eine Lizenz für mehr als ein Projekt zu nutzen und die
  Lizenzfunktionen zu verändern.
- **Stärke:** ein angenehmer Editor und eine starke Laravel-Integration.
- **Wo es Sie im Stich lässt:** die Funktionen, die ein Team braucht, stecken in Pro, bezahlt pro Website und pro
  Jahr.
- **Exponential stattdessen:** keine Editionen (E1).

### Kirby

- **Was es ist:** ein dateibasiertes CMS.
- **Lizenz und Modell:** proprietär. Seine Lizenz sagt: „While Kirby's source code is publicly available, Kirby is
  **not free**. To use Kirby in production, you need to purchase a license“, und jedes Projekt, bestimmt durch seine
  URL, braucht eine eigene Lizenz.
- **Stärke:** ein flexibles Panel und saubere Templates.
- **Wo es Sie im Stich lässt:** eine bezahlte Lizenz pro Website, nach seinen eigenen Bedingungen.
- **Exponential stattdessen:** kostenlos für jede Website (E1).

### October CMS

- **Was es ist:** ein CMS auf Laravel, das als freie Software begann und zu einer bezahlten Lizenz wechselte.
- **Lizenz und Modell:** proprietäre Endbenutzer-Lizenz (`composer.json`: `proprietary`); eine kostenlose Lizenz mit
  einem Jahr System-Updates und bezahlte Lizenzen für weitere Updates. Winter CMS wurde 2021 davon abgespalten
  (Abschnitt 5).
- **Stärke:** eine saubere Entwicklererfahrung.
- **Wo es Sie im Stich lässt:** ein Projekt, das seine Lizenz änderte, nachdem andere darauf gebaut hatten, ist das
  deutlichste Beispiel für das Risiko, das freizügige oder herstellereigene Lizenzen tragen.
- **Exponential stattdessen:** eine Copyleft-Lizenz, die es frei hält (E1).

### Pimcore

- **Was es ist:** eine Plattform für Daten und Erlebnisse (PIM, MDM, DAM, CMS, Handel).
- **Lizenz und Modell:** mit Version 12 wechselte Pimcore von Open Source zur Pimcore Open Core License (POCL), einer
  proprietären Lizenz; seine Editionen sind Community (quelloffen, kostenlos, unter POCL), Professional und
  Enterprise (kommerziell).
- **Stärke:** sehr breite Funktionen für die Datenverwaltung.
- **Wo es Sie im Stich lässt:** eine Plattform, die Open Source war, steht jetzt unter einer Lizenz, die der Hersteller
  bestimmt, mit den professionellen Funktionen in bezahlten Editionen.
- **Exponential stattdessen:** freie Software, die frei bleibt (E1).

### Laravel Nova und Automad

- **Laravel Nova** ist eine bezahlte Administrationsoberfläche für Laravel: eine Einzellizenz kostet 99 $ und verlängert
  sich für 79 $ im Jahr, eine unbegrenzte Lizenz 299 $, Verlängerung 249 $ im Jahr.
- **Automad** ist ein Flat-File-CMS unter der Lizenz „O'Saasy“: freizügig, außer dass niemand es als gehosteten Dienst
  anbieten darf, der mit seinem Autor konkurriert. Diese Einschränkung macht es zu quelloffener, aber nicht freier
  Software.
- **Exponential stattdessen:** kostenlos für jede Nutzung, auch um es für andere zu hosten (E1).

## 7. Die Frameworks: was Sie selbst bauen

Laravel (MIT), Symfony (MIT), Laminas und Mezzio (BSD-3-Clause), CakePHP (MIT), CodeIgniter (MIT), Yii (BSD-3-Clause)
und Slim (MIT) sind ausgezeichnete, freie Frameworks, alle aktiv gepflegt (Repositorys). Sie sind keine
Content-Management-Systeme. Mit einem Framework bezahlt der Website-Betreiber dafür, alles zu bauen und zu pflegen, was
ein Redakteur braucht: das Inhaltsmodell, die Bearbeitungsoberflächen, Versionen, Übersetzungen, Rollen und Rechte,
Workflows, Suche, Medien, eine Schnittstelle, Anmeldung mit zweitem Faktor und ein Audit-Protokoll. Jede Hauptversion
eines Frameworks legt außerdem einen Takt von Aktualisierungen fest, dem der eigene Code folgen muss.

**Exponential stattdessen:** all das gibt es und es wird gepflegt (E5); Entwickler erweitern es mit denselben
Werkzeugen, die ein Framework bietet ([Exponential 6.0 erweitern](../EXTENDING.md)), und die Symfony-basierten
Distributionen von Exponential bringen Symfony selbst mit, wenn ein Projekt es will
([Distributionen von Exponential](../DISTRIBUTIONS.md)).

## 8. Auf einen Blick

| Produkt | Lizenz | Kostenlos für jede produktive Website | Wo die bezahlten Teile sind |
|---|---|---|---|
| **Exponential** | GPL-2.0-or-later | ja | keine: keine Editionen, Schlüssel oder Update-Gebühren |
| WordPress | GPL-2.0 oder später | ja | Plugins mit bezahlten Stufen; Tarife von WordPress.com |
| Drupal | GPL-2.0-or-later | ja | die Migration von 7 auf 8 und später |
| Joomla | GPL-2.0 | ja | kommerzielle Extensions |
| TYPO3 | GPL-2.0-or-later | ja | Sicherheitskorrekturen nach LTS (ELTS) |
| Contao | LGPL-3.0 | ja | keine im Kern |
| ProcessWire | MPL-2.0 | ja | keine im Kern |
| Magento Open Source | OSL-3.0 | ja | Adobe Commerce |
| PrestaShop | OSL-3.0 | ja | Marktplatz Addons |
| WooCommerce | GPL-2.0 | ja | Extension-Abonnements |
| Grav | MIT | ja | Premium-Plugins und -Themes |
| Winter CMS | MIT | ja | keine im Kern |
| Sulu, Bolt | MIT | ja | keine im Kern |
| Craft CMS | proprietär | nur Solo (ein Benutzer) | Team, Pro und jährliche Updates |
| Statamic | proprietär | nur Core | Pro pro Website und jährliche Updates |
| Kirby | proprietär | nein | eine Lizenz pro Website |
| October CMS | proprietär | kostenlose Lizenz, ein Jahr Updates | bezahlte Lizenzen |
| Pimcore | POCL (proprietär) | Community-Edition | Professional und Enterprise |
| Laravel Nova | proprietär | nein | jede Lizenz |
| Frameworks | MIT, BSD-3-Clause | ja | die Website, die Sie darauf bauen |

## 9. Quellen

Lizenzen, neueste Releases, Sterne und letzte Aktivität: das GitHub-Repository jedes Projekts (Repository-Daten und
Lizenzdatei), gelesen am 4. November 2026:
[WordPress](https://github.com/WordPress/WordPress), [ClassicPress](https://github.com/ClassicPress/ClassicPress),
[Drupal](https://github.com/drupal/drupal), [Backdrop](https://github.com/backdrop/backdrop),
[Joomla](https://github.com/joomla/joomla-cms), [TYPO3](https://github.com/TYPO3/typo3),
[Contao](https://github.com/contao/contao), [Concrete CMS](https://github.com/concretecms/concretecms),
[Neos](https://github.com/neos/neos-development-collection), [ProcessWire](https://github.com/processwire/processwire),
[MODX](https://github.com/modxcms/revolution), [Bolt](https://github.com/bolt/core),
[PrestaShop](https://github.com/PrestaShop/PrestaShop), [Magento](https://github.com/magento/magento2),
[WooCommerce](https://github.com/woocommerce/woocommerce), [OpenCart](https://github.com/opencart/opencart),
[Grav](https://github.com/getgrav/grav), [October CMS](https://github.com/octobercms/october),
[Winter CMS](https://github.com/wintercms/winter), [Pagekit](https://github.com/pagekit/pagekit),
[Statamic](https://github.com/statamic/cms), [Kirby](https://github.com/getkirby/kirby),
[Craft CMS](https://github.com/craftcms/cms), [Filament](https://github.com/filamentphp/filament),
[Twill](https://github.com/area17/twill), [Sulu](https://github.com/sulu/sulu), [Pimcore](https://github.com/pimcore/pimcore),
[Bagisto](https://github.com/bagisto/bagisto), [Akeneo PIM](https://github.com/akeneo/pim-community-dev),
[Typemill](https://github.com/typemill/typemill), [Bludit](https://github.com/bludit/bludit),
[WonderCMS](https://github.com/WonderCMS/wondercms), [Pico](https://github.com/picocms/Pico),
[Automad](https://github.com/marcantondahmen/automad), [Textpattern](https://github.com/textpattern/textpattern),
[Silverstripe CMS](https://github.com/silverstripe/silverstripe-cms), [Croogo](https://github.com/croogo/croogo),
[ExpressionEngine](https://github.com/ExpressionEngine/ExpressionEngine), [Laravel](https://github.com/laravel/framework),
[Symfony](https://github.com/symfony/symfony), [Laminas MVC](https://github.com/laminas/laminas-mvc),
[Mezzio](https://github.com/mezzio/mezzio), [CakePHP](https://github.com/cakephp/cakephp),
[CodeIgniter](https://github.com/codeigniter4/CodeIgniter4), [Yii](https://github.com/yiisoft/yii2),
[Slim](https://github.com/slimphp/Slim).

Geschäftsmodelle, Preise und Geschichte:

- Tarife von WordPress.com: <https://wordpress.com/pricing/>
- Der Streit um WP Engine, die Übernahme als „Secure Custom Fields“ und die einstweilige Verfügung vom
  10. Dezember 2024: <https://en.wikipedia.org/wiki/WP_Engine>
- ClassicPress, „Forked from WordPress without the block editor“: <https://www.classicpress.net/>
- Lebensende von Drupal 7, festgelegt auf den 5. Januar 2025: <https://www.drupal.org/about/drupal-7/d7eol> und
  <https://en.wikipedia.org/wiki/Drupal>
- Der Aktualisierungsweg von Backdrop ab Drupal 7: <https://backdropcms.org/>
- Die Geschichte von Joomla: <https://en.wikipedia.org/wiki/Joomla>
- TYPO3-Roadmap und Extended Long Term Support: <https://typo3.org/cms/roadmap>,
  <https://typo3.com/services/extended-support-elts>
- Releaseplan von Contao: <https://contao.org/en/release-plan>
- Lizenz von ProcessWire: <https://processwire.com/about/license/>
- PrestaShop und sein Marktplatz Addons: <https://www.prestashop.com/en>
- Lebensende von Magento 1 am 30. Juni 2020: <https://en.wikipedia.org/wiki/Magento>; Lebenszyklus-Richtlinie von
  Adobe Commerce: <https://experienceleague.adobe.com/en/docs/commerce-operations/release/planning/lifecycle-policy>
- Extensions und Tarife von WooCommerce: <https://woocommerce.com/products/>
- Hosting von Concrete CMS: <https://www.concretecms.com/>
- Grav und sein Premium-Shop: <https://getgrav.org/>
- Preise von Craft CMS: <https://craftcms.com/pricing>
- Preise von Statamic: <https://statamic.com/pricing>
- Lizenzen von Kirby: <https://getkirby.com/buy>
- Preise und kostenlose Lizenz von October CMS: <https://octobercms.com/pricing>
- Lizenzwechsel und Editionen von Pimcore: <https://en.wikipedia.org/wiki/Pimcore>, <https://pimcore.com/en>
- Preise von Laravel Nova: <https://nova.laravel.com/>
- ExpressionEngine: <https://expressionengine.com/>
- Bludit, WonderCMS, Typemill: <https://www.bludit.com/>, <https://www.wondercms.com/>, <https://typemill.net/>
- CMS Made Simple: <https://www.cmsmadesimple.org/>
- Unterstützte PHP-Versionen: <https://www.php.net/supported-versions.php>
