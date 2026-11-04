# Exponential im Wettbewerb

Dieses Buch vergleicht Exponential mit den anderen Wegen, heute eine Website zu betreiben: proprietären Digital
Experience Platforms (DXPs) und Software as a Service (SaaS), den PHP-Content-Management-Systemen und -Frameworks und
den Systemen in allen anderen Programmiersprachen. Es ist vor allem für eine Leserin und einen Leser geschrieben: eine
Organisation oder eine Person **ohne Budget für die Website**, die trotzdem moderne Dinge braucht, viele Jahre lang
funktionieren muss und sich zu **Grenzkosten von null** an fast jeden Zweck anpassen lassen soll.

Jede Seite nennt die Produkte ihrer Gruppe und sagt, was jedes ist, wie es lizenziert und bezahlt wird, was es gut
kann, wo es diese Leserschaft auf lange Sicht im Stich lässt und was Exponential stattdessen tut. Jede Aussage über ein
anderes Produkt stammt aus der Dokumentation, der Lizenz oder der Preisseite des Produkts zum Zeitpunkt des Schreibens
(November 2026), und jede Seite nennt ihre Quellen. Preise und Pläne ändern sich; prüfen Sie die Quelle, bevor Sie
entscheiden.

## Inhalt

- [1. Für wen dieses Buch ist](#1-für-wen-dieses-buch-ist)
- [2. Die Kriterien](#2-die-kriterien)
- [3. Die Werte freier Software](#3-die-werte-freier-software)
- [4. Die Übersicht](#4-die-übersicht)
- [5. Die Seiten dieses Buches](#5-die-seiten-dieses-buches)
- [6. Wie man dieses Buch fair liest](#6-wie-man-dieses-buch-fair-liest)
- [7. Was Exponential mitbringt](#7-was-exponential-mitbringt)
- [Quellen](#quellen)

## 1. Für wen dieses Buch ist

- **Kein Budget, echter Bedarf.** Ein Verein, eine Schule, eine gemeinnützige Organisation, ein kleines Unternehmen,
  eine Behörde oder eine einzelne Entwicklerin, die eine Website mit Inhaltstypen, mehreren Sprachen, Redaktion mit
  Rechten, Suche und Anbindungen braucht, aber keine Lizenz, kein Abonnement pro Benutzer und keine gemessene
  API-Rechnung bezahlen kann.
- **Langes Leben.** Eine Website, die in zehn oder zwanzig Jahren noch läuft, aktualisiert wird und sicher bleibt, ohne
  erzwungenen Plattformwechsel, weil ein Anbieter seine Pläne, Preise oder sein Produkt geändert hat.
- **Die Freiheit, alles zu ändern.** Das Recht, den Code zu lesen, zu ändern und weiterzugeben, ihn überall zu
  betreiben und beim Wechsel jedes Byte an Inhalt und Daten mitzunehmen.

Ein gut finanziertes Team mit anderen Prioritäten kann dieselben Fakten anders gewichten. Die folgenden Kriterien
machen den Vergleich in beiden Fällen überprüfbar.

## 2. Die Kriterien

| Kriterium | Die Frage an jedes Produkt |
|---|---|
| **Kosten ohne Budget** | Läuft es mit allen Funktionen im Produktivbetrieb, nur für die Kosten eines Servers? |
| **Lizenzfreiheit** | Darf jeder es nutzen, untersuchen, ändern und weitergeben (die vier Freiheiten)? |
| **Bindung und Ausstieg** | Lassen sich alle Inhalte, Strukturen und Daten exportieren und ohne den Anbieter betreiben? |
| **Messung** | Bestimmen Benutzerplätze, Datensätze, API-Aufrufe, Bandbreite, Seitenaufrufe oder „KI-Guthaben" den Preis? |
| **Hosting** | Läuft es auf gewöhnlichem PHP-Hosting, auf einem kleinen Server, auf SQLite oder nur in der Cloud des Anbieters? |
| **Stabilität bei Updates** | Wurde seine Linie 10 bis 20 Jahre lang aktualisierbar gehalten, oder brach sie ab? |
| **Erweiterbarkeit** | Lassen sich Inhaltstypen, Module, Ansichten, Workflows und Anbindungen ohne Erlaubnis und Gebühren hinzufügen? |
| **Mehrere Sites und Sprachen** | Gehören mehrere Sites und viele Sprachen zum Produkt oder sind sie kostenpflichtige Zusätze? |
| **Sicherheit** | Sind Korrekturen öffentlich, und kann der Eigentümer sie ohne Anbieter einspielen? |
| **Datenhoheit** | Wem gehören Datenbank, Dateien, Protokolle und Prüfprotokoll? |

## 3. Die Werte freier Software

Freie Software ist durch vier Freiheiten bestimmt: das Programm für jeden Zweck auszuführen, es zu untersuchen und zu
ändern, Kopien weiterzugeben und geänderte Fassungen zu verbreiten
([Was ist freie Software?](https://www.gnu.org/philosophy/free-sw.html),
[Free Software Foundation](https://www.fsf.org/about/what-is-free-software)). Exponential ist freie Software unter der
[GNU General Public License Version 2 oder jeder späteren Version](../../../LICENSE.md)
([Urheberrecht und Copyleft](../hilfe/urheberrecht-und-copyleft.md)).

- **Copyleft.** Die GPL bindet diese Freiheiten an den Code: Wer Exponential weitergibt, geändert oder nicht, muss
  dieselben Freiheiten weitergeben. Kein späterer Eigentümer kann es schließen.
- **Grenzkosten von null.** Eine weitere Site, eine weitere Redakteurin, eine weitere Sprache oder eine weitere Million
  Seitenaufrufe kostet keine Lizenzgebühr. Die Kosten sind der Server und die Menschen, beides wählt der Eigentümer.
- **Datenhoheit.** Datenbank und Dateien gehören dem Eigentümer, in offenen Formaten (SQL-Datenbanken, MongoDB,
  einfache Dateien), auf seinen eigenen Rechnern.
- **Ausstiegskosten.** Einem System, das man verlassen kann, kann man vertrauen. Exponential dokumentiert seinen eigenen
  Ausstieg ([Exponential verlassen](../installation/migration/exponential-verlassen.md)).
- **Die Ökonomie gemessener SaaS.** Ein gehostetes Produkt, das nach Benutzerplätzen, Datensätzen, API-Aufrufen,
  Bandbreite oder Nutzung bepreist wird, macht Wachstum zu Kosten und die Preisentscheidung des Anbieters jedes Jahr zum
  Teil des Website-Budgets.

## 4. Die Übersicht

Wie jede Gruppe die Kriterien im Allgemeinen beantwortet. Die Seiten nennen die Einzelheiten pro Produkt, mit Quellen.

| Gruppe | Kosten ohne Budget | Lizenzfreiheit | Ausstieg | Gemessen | Auf eigenem Server |
|---|---|---|---|---|---|
| [Proprietäre DXPs](proprietaere-dxp-und-saas.md#1-enterprise-dxps) | Nein: Lizenz oder Abonnement, meist auf Anfrage | Nein | Teilweise | Oft | Manche, mit Lizenz |
| [Headless CMS als Dienst](proprietaere-dxp-und-saas.md#2-headless-cms-als-dienst) | Kostenlose Stufen mit Grenzen | Nein (teils offene Editoren) | Teilweise, über APIs | Ja | Nein |
| [Website-Baukästen](proprietaere-dxp-und-saas.md#3-website-baukästen-und-gehostete-shops) | Kostenlose oder günstige Stufen mit Grenzen und Werbung | Nein | Eingeschränkt | Ja | Nein |
| [PHP-CMS und -Frameworks](php-cms-und-frameworks.md) | Meist ja | Meist freie Software | Ja | Nein (außer gehostete Ausgaben) | Ja |
| [Andere Sprachen](andere-sprachen.md) | Unterschiedlich | Unterschiedlich | Unterschiedlich | Unterschiedlich | Unterschiedlich |
| **Exponential** | **Ja** | **Ja, GPL 2 oder später** | **Ja, dokumentiert** | **Nein** | **Ja, von SQLite auf einem kleinen Server bis zum Cluster** |

## 5. Die Seiten dieses Buches

| Seite | Inhalt |
|---|---|
| [Proprietäre DXPs und SaaS](proprietaere-dxp-und-saas.md) | Adobe Experience Manager, Adobe ColdFusion, Sitecore, Optimizely, Acquia, Bloomreach, Ibexa DXP, Salesforce Experience Cloud, Microsoft SharePoint; Contentful, Contentstack, Sanity, Storyblok, Kontent.ai, Hygraph, Prismic; Squarespace, Wix, Webflow, Shopify, HubSpot CMS, Duda |
| [PHP-CMS und -Frameworks](php-cms-und-frameworks.md) | WordPress, Drupal, Joomla, TYPO3 und die anderen PHP-Systeme und -Frameworks, nach Lizenz gruppiert |
| [Andere Sprachen](andere-sprachen.md) | Python, JavaScript und TypeScript, Ruby, Java, .NET, Go, Rust, C und C++, Swift, Kotlin, Elixir, CFML und die Generatoren statischer Websites |

## 6. Wie man dieses Buch fair liest

- **Fakten, keine Urteile über Menschen.** Jede Aussage über ein Produkt betrifft seine Lizenz, sein Geschäftsmodell,
  seine dokumentierten Grenzen oder seine Versionsgeschichte und ist mit ihrer Quelle verlinkt.
- **Je eine ehrliche Zeile.** Jedes Produkt erhält eine Zeile darüber, was es gut kann. Viele sind hervorragend in dem,
  wofür sie gebaut wurden.
- **Datiert.** Pläne, Preise und Produkte ändern sich. Die Seiten wurden im November 2026 geprüft; die Quellen zeigen
  den aktuellen Stand.
- **Die Leserschaft entscheidet.** Die Kriterien sind die Prioritäten aus [Abschnitt 1](#1-für-wen-dieses-buch-ist).
  Andere Prioritäten können zu einer anderen Wahl führen.

## 7. Was Exponential mitbringt

Jeder Punkt verlinkt die Seite, die ihn dokumentiert.

- **Freie Software**, GPL 2 oder später, ohne kostenpflichtige Ausgabe und ohne zurückgehaltene Funktion
  ([LICENSE.md](../../../LICENSE.md)).
- **Läuft überall, wo PHP läuft:** PHP 8.0 bis 8.5 unterstützt, 7.4 nutzbar
  ([Distributionen von Exponential](../DISTRIBUTIONS.md),
  [Warum Exponential PHP 7.4 unterstützt](../hilfe/warum-exponential-php-7-4-unterstuetzt.md)).
- **Fünf Datenbanken:** SQLite, MySQL und MariaDB, PostgreSQL, MongoDB und Oracle
  ([SQLite](../funktionen/6.0/sqlite-datenbank.md), [MongoDB](../funktionen/6.0/mongodb-datenbankunterstuetzung.md)).
- **Ein eigener Anwendungsserver, Velocity**, mit dauerhaften Workern und einem Antwort-Cache
  ([Velocity](../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)).
- **Mehrere Sites und viele Sprachen:** mehrere Sites aus einer Installation und Übersetzungen der Administration in
  vielen Sprachen ([Sprachen in Exponential](../sprachen.md),
  [Website-spezifische Einstellungen in Extensions](../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)).
- **Inhalte hinein und hinaus:** Migrationen, Pakete, GraphQL und REST und statisches Veröffentlichen auf jedem
  statischen Host ([Migrationen](../funktionen/6.0/migrationen.md), [GraphQL](../funktionen/6.0/graphql.md),
  [Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md),
  [Mit Exponential 6.0 integrieren](../INTEGRATING.md)).
- **Nachvollziehbarkeit:** ein Prüfprotokoll, wer was getan hat ([Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md)).
- **Dokumentation im Produkt:** dieses Hilfezentrum, auf Englisch und Deutsch ([Hilfesystem](../funktionen/6.0/hilfesystem.md)).
- **Ein dokumentierter Weg hinein und hinaus:**
  [Zu Exponential wechseln](../hilfe/unternehmen/auswahl/zu-exponential-wechseln.md),
  [Von einem anderen System migrieren](../hilfe/anwender-umsteiger/umziehen/von-einem-anderen-system.md),
  [Exponential verlassen](../installation/migration/exponential-verlassen.md).

## Quellen

- Free Software Foundation, [Was ist freie Software?](https://www.gnu.org/philosophy/free-sw.html) und
  [What is free software (FSF)](https://www.fsf.org/about/what-is-free-software).
- GNU, [GNU General Public License, Version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).
- Die Quellen jedes Produkts stehen auf seiner Seite.
