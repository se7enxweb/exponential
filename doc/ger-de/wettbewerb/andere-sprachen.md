---
title: Content-Management in anderen Sprachen als PHP
audience: admin, developer, businesses, partner, decision-maker
summary: Die Content-Management-Systeme, Frameworks und Website-Baukästen in Python, JavaScript und TypeScript, Ruby, Java und Kotlin, .NET, Go, Rust, C++, Swift, Elixir und weiteren Sprachen, mit Lizenz und Geschäftsmodell, und wo jedes eine Website ohne Budget oder mit langer Lebensdauer im Stich lässt, verglichen mit Exponential.
keywords: Wettbewerb, Vergleich, Python, Django, Wagtail, Plone, JavaScript, Node.js, Strapi, Ghost, Payload, Directus, Next.js, Ruby, Rails, Jekyll, Java, Liferay, Magnolia, OpenCms, dotCMS, .NET, Umbraco, Orchard Core, Go, Hugo, Rust, Zola, Static-Site-Generator, Low-Code, Lizenz, Source-Available, Business Source License
---

# Content-Management in anderen Sprachen als PHP

Diese Seite gehört zu [Exponential im Wettbewerb](README.md). Sie behandelt die Systeme, die nicht in PHP geschrieben
sind: Content-Management-Systeme, Headless-Backends, die Web-Frameworks, mit denen Websites gebaut werden, die
Static-Site-Generatoren und die gehosteten Website-Baukästen. Die PHP-Systeme stehen auf
[PHP-CMS und Frameworks](php-cms-und-frameworks.md), die proprietären und gehosteten Plattformen auf
[Proprietäre DXP und SaaS](proprietaere-dxp-und-saas.md).

Jedes Produkt bekommt dieselben fünf Punkte: was es ist, Lizenz und Geschäftsmodell, was es gut kann, wo es jemanden im
Stich lässt, der kein Budget für die Website hat, sie aber über viele Jahre für zeitgemäße Aufgaben braucht, und was
Exponential stattdessen bietet. Die Angaben wurden im November 2026 am Repository oder an der Website des jeweiligen
Projekts geprüft; sie stehen unter [Quellen](#quellen). Produkte ändern sich, prüfen Sie also den aktuellen Stand, bevor
Sie entscheiden.

## Inhalt

1. [Was Exponential in jeden Vergleich mitbringt](#was-exponential-in-jeden-vergleich-mitbringt)
2. [Python](#python)
3. [JavaScript und TypeScript](#javascript-und-typescript)
4. [Ruby](#ruby)
5. [Java, Kotlin und die JVM](#java-kotlin-und-die-jvm)
6. [.NET und C#](#net-und-c)
7. [Go](#go)
8. [Rust](#rust)
9. [C und C++](#c-und-c)
10. [Swift](#swift)
11. [Elixir](#elixir)
12. [CFML](#cfml)
13. [Haskell, Clojure und Perl](#haskell-clojure-und-perl)
14. [Lua, Nim, Crystal, Dart und Zig](#lua-nim-crystal-dart-und-zig)
15. [Gehostete Baukästen ohne Code](#gehostete-baukästen-ohne-code)
16. [Static-Site-Generatoren als Gattung](#static-site-generatoren-als-gattung)
17. [Zusammenfassung](#zusammenfassung)
18. [Quellen](#quellen)

## Was Exponential in jeden Vergleich mitbringt

Damit jeder Eintrag unten kurz bleiben kann, steht hier, was Exponential in jedem Fall bietet. Jeder Punkt ist in
diesem Hilfecenter dokumentiert.

- **Eine Lizenz, für alle.** Der Kernel und seine mitgelieferten Extensions stehen unter der GNU General Public License,
  Version 2 oder später. Es gibt keinen Lizenzschlüssel, keine Umsatzgrenze, keine Edition, die Funktionen zurückhält,
  und keine Frist, nach der sich die Lizenz ändert.
- **Ein vollständiges Content-Management-System ab Werk.** Inhaltsklassen, Versionen und Entwürfe, Übersetzungen,
  Objektzustände, Rollen und Richtlinien mit Einschränkungen, Workflows, ein Prüfprotokoll
  ([Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md)), die Zurückhaltung zur Prüfung ([Zurückhaltung zur Prüfung](../funktionen/6.0/zurueckhaltung-zur-pruefung.md)),
  Anmeldung in zwei Schritten, eine Administrationsoberfläche in mehr als 150 Sprachen ([Sprachen](../sprachen.md))
  und ein Hilfecenter.
- **Die Datenbank Ihrer Wahl.** MySQL und MariaDB, PostgreSQL, SQLite ([SQLite](../funktionen/6.0/sqlite-datenbank.md)),
  MongoDB ([MongoDB](../funktionen/6.0/mongodb-datenbankunterstuetzung.md)) und Oracle; siehe
  [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md).
- **Gewöhnliches Hosting.** PHP von 8.0 bis 8.5 wird unterstützt, und 7.4 führt es weiterhin aus. Ein Shared Host mit
  PHP und SQLite genügt für den Anfang; der optionale Velocity-Server
  ([Velocity](../funktionen/6.0/velocity-webserver.md)) betreibt dieselbe Website als dauerhaft laufenden
  Anwendungsserver.
- **Eine statische Kopie, wenn Sie eine wollen.** [Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md)
  macht aus einer Website einfache Dateien und veröffentlicht sie auf jedem Git-Host, etwa auf den kostenlosen GitHub
  Pages ([Statische Websites auf GitHub Pages hosten](../anleitungen/statische-websites-auf-github-pages-hosten.md)), während die
  Redaktion weiter in Exponential arbeitet.
- **Schnittstellen.** REST, GraphQL ([GraphQL](../funktionen/6.0/graphql.md)), WebDAV und SOAP; siehe
  [Integrating With Exponential 6.0](../INTEGRATING.md).
- **Eine lange, ununterbrochene Linie.** Der Kernel führt die Codebasis fort, die 2002 als eZ publish 3 begann;
  Inhaltsmodelle von damals öffnen sich noch heute ([Geschichte von Exponential](../HISTORY.md)).

## Python

### Django (Framework)

- **Was es ist:** das meistgenutzte Python-Web-Framework, mit einer automatischen Verwaltung für Datenmodelle.
- **Lizenz und Modell:** BSD-3-Clause, getragen von der Django Software Foundation, einer gemeinnützigen Organisation.
- **Kann gut:** ein ausgereiftes, gut dokumentiertes Framework mit großem Ökosystem und Langzeitversionen.
- **Wo es Sie im Stich lässt:** Es ist ein Framework, kein Content-Management-System. Seitenbäume, Versionen,
  redaktionelle Workflows, Übersetzungen von Inhalten, Medienverwaltung und eine redaktionsfreundliche Oberfläche
  müssen gebaut oder aus Erweiterungen zusammengesetzt und dann über die ganze Lebenszeit der Website von einer
  Entwicklerin oder einem Entwickler gepflegt werden.
- **Exponential stattdessen:** All das ist ab Werk enthalten und wird eingestellt, nicht programmiert.

### django CMS

- **Was es ist:** ein seitenbasiertes CMS auf Django.
- **Lizenz und Modell:** BSD-3-Clause; gepflegt von der django CMS Association.
- **Kann gut:** Bearbeitung direkt auf der Seite und ein Plugin-Modell, das Django-Entwicklern vertraut ist.
- **Wo es Sie im Stich lässt:** Sie betreiben und aktualisieren ein eigenes Django-Projekt mit seinen
  Python-Abhängigkeiten, und seine Hauptversionen bringen Aktualisierungsarbeit an diesem Projekt mit sich. Einen
  eingebauten statischen Export gibt es nicht, und die Datenbankwahl ist das, was Django unterstützt.
- **Exponential stattdessen:** ein seit 2002 kompatibles Inhaltsmodell, fünf Datenbanksysteme und eingebautes
  statisches Veröffentlichen.

### Wagtail

- **Was es ist:** ein CMS auf Django mit strukturiertem Seitenmodell und dem StreamField-Editor.
- **Lizenz und Modell:** BSD-3-Clause; überwiegend von Torchbox und Beitragenden entwickelt.
- **Kann gut:** eine aufgeräumte Bearbeitungsoberfläche und ein gutes Inhaltsmodell für Entwickler.
- **Wo es Sie im Stich lässt:** Jede Wagtail-Website ist ein Django-Projekt, das eine Entwicklerin oder ein Entwickler
  schreibt und pflegt; Seitentypen sind Python-Code, also ist eine Änderung am Inhaltsmodell eine Codeänderung mit
  Deployment. Funktionsversionen erscheinen mehrmals im Jahr, jeweils mit Aktualisierungshinweisen.
- **Exponential stattdessen:** Inhaltsklassen werden in der Administrationsoberfläche bearbeitet, ohne Deployment.

### Plone

- **Was es ist:** eines der ältesten quelloffenen Enterprise-CMS, in Python geschrieben.
- **Lizenz und Modell:** GPL-2.0; im Besitz der gemeinnützigen Plone Foundation.
- **Kann gut:** starke Berechtigungen, Workflows und eine lange Sicherheitsbilanz.
- **Wo es Sie im Stich lässt:** Es braucht einen Python-Anwendungsserver und seine eigene Objektdatenbank (ZODB) oder
  eine relationale Speicherschicht, Spezialwissen, das wenige Hoster und Agenturen haben; mit Plone 6 kam ein eigenes
  React-Frontend (Volto) hinzu, das ebenfalls betrieben und aktualisiert werden muss.
- **Exponential stattdessen:** dieselbe Art von Berechtigungen und Workflows auf gewöhnlichem PHP-Hosting und mit einer
  Datenbank Ihrer Wahl.

### Mezzanine

- **Was es ist:** ein CMS und Blog auf Django.
- **Lizenz und Modell:** BSD-2-Clause, von der Community gepflegt.
- **Kann gut:** ein einfaches, klassisches Django-CMS.
- **Wo es Sie im Stich lässt:** Die Entwicklung hat sich verlangsamt: Die letzte Version, 6.1.1, erschien im Juni 2025.
  Eine Website, die jahrelang Aktualisierungen braucht, hängt an einer kleinen Gruppe Freiwilliger.
- **Exponential stattdessen:** ein aktiv veröffentlichter Kernel mit öffentlicher Versionsgeschichte.

### FeinCMS

- **Was es ist:** ein Baukasten, um ein CMS in einem Django-Projekt zu bauen.
- **Lizenz und Modell:** BSD-3-Clause.
- **Kann gut:** flexible Inhaltsbereiche für Entwickler.
- **Wo es Sie im Stich lässt:** Es veröffentlicht keine Releases auf GitHub und ist ein Baustein, kein fertiges System;
  den Rest baut und pflegt die Entwicklung.
- **Exponential stattdessen:** ein fertiges System.

### Odoo Website

- **Was es ist:** der Website-Baukasten der Odoo-Unternehmenssuite.
- **Lizenz und Modell:** Odoo Community steht unter LGPL-3.0; Odoo Enterprise ist eine kostenpflichtige, lizenzierte
  Edition mit zusätzlichen Apps.
- **Kann gut:** eine Website, die direkt mit Odoos ERP, Shop und CRM verbunden ist.
- **Wo es Sie im Stich lässt:** Die Website lebt in einem ganzen ERP, das gehostet und aktualisiert werden muss, und
  mehrere Funktionen gibt es nur in der kostenpflichtigen Edition. Ein Wechsel zwischen Odoo-Hauptversionen ist ein
  eigenes Migrationsprojekt.
- **Exponential stattdessen:** eine Content-Plattform unter einer Lizenz, mit Anbindungen über REST, GraphQL und
  Webhooks.

### Pelican, Nikola und MkDocs

- **Was sie sind:** Static-Site-Generatoren in Python (MkDocs ist auf Dokumentation ausgerichtet).
- **Lizenz und Modell:** Pelican AGPL-3.0, Nikola MIT, MkDocs BSD-2-Clause; alles ehrenamtliche Projekte.
- **Können gut:** schnelle, einfache Websites aus Markdown-Dateien.
- **Wo sie Sie im Stich lassen:** siehe [Static-Site-Generatoren als Gattung](#static-site-generatoren-als-gattung).
  Die letzte MkDocs-Version ist 1.6.1 vom August 2024.
- **Exponential stattdessen:** eine Oberfläche für die Redaktion und statische Ausgabe.

### Flask (Framework)

- **Was es ist:** ein minimales Python-Web-Framework.
- **Lizenz und Modell:** BSD-3-Clause, vom Pallets-Projekt.
- **Kann gut:** kleine, flexible Anwendungen.
- **Wo es Sie im Stich lässt:** Es hat überhaupt kein Content-Management; jede CMS-Funktion ist eigener Code.
- **Exponential stattdessen:** Die CMS-Funktionen gibt es bereits.

## JavaScript und TypeScript

### Strapi

- **Was es ist:** ein Headless-CMS für Node.js.
- **Lizenz und Modell:** Die Community Edition steht unter MIT; alles unter den Verzeichnissen `ee/` ist die Enterprise
  Edition unter einer kommerziellen Lizenz (laut LICENSE des Repositorys). Strapi Cloud ist ein kostenpflichtiger
  Hosting-Dienst.
- **Kann gut:** schnelle Content-APIs für ein separates Frontend.
- **Wo es Sie im Stich lässt:** nur headless, Sie bauen, hosten und pflegen also eine zweite Anwendung für die Website
  selbst. Hauptversionen (v4 auf v5) erforderten Migrationsarbeit, und manche Funktionen liegen in der
  kostenpflichtigen Edition.
- **Exponential stattdessen:** Website und Schnittstellen aus einer Installation und jede Funktion unter einer Lizenz.

### Ghost

- **Was es ist:** eine Publishing-Plattform für Blogs und Newsletter.
- **Lizenz und Modell:** MIT; getragen von der gemeinnützigen Ghost Foundation, die auch das Hosting Ghost(Pro)
  verkauft.
- **Kann gut:** ein ausgefeiltes Schreiberlebnis und eingebaute Mitgliedschaften.
- **Wo es Sie im Stich lässt:** Die Ghost-Dokumentation nennt MySQL 8 als einzige unterstützte Produktionsdatenbank, mit
  Node.js und nginx; es braucht also einen Server, den Sie selbst verwalten. Es ist für Publikationen gebaut, nicht für
  allgemeine strukturierte Inhalte.
- **Exponential stattdessen:** jede Inhaltsstruktur, fünf Datenbanksysteme und gewöhnliches PHP-Hosting.

### Payload

- **Was es ist:** ein Headless-CMS und Anwendungs-Framework in TypeScript, eingebaut in Next.js.
- **Lizenz und Modell:** MIT, entwickelt von Payload, Inc.
- **Kann gut:** Schemata im Code und starke Typisierung.
- **Wo es Sie im Stich lässt:** Das Inhaltsmodell ist Code, Änderungen brauchen also Entwicklung und Deployment; die
  Website ist eine Next.js-Anwendung, die gehostet und aktuell gehalten werden muss.
- **Exponential stattdessen:** Inhaltsklassen, verwaltet in der Administrationsoberfläche.

### KeystoneJS

- **Was es ist:** ein Headless-CMS und GraphQL-API-Framework für Node.js.
- **Lizenz und Modell:** MIT, entwickelt von Thinkmill.
- **Kann gut:** eine GraphQL-Schnittstelle aus einem Schema.
- **Wo es Sie im Stich lässt:** headless und code-first; Frontend und Hosting bauen und pflegen Sie selbst.
- **Exponential stattdessen:** eine vollständige Website plus GraphQL.

### Directus

- **Was es ist:** eine Datenplattform, die aus einer SQL-Datenbank eine Schnittstelle und eine Verwaltungs-App macht.
- **Lizenz und Modell:** veröffentlicht unter der Monospace Sustainable Core License 1.0 (MSCL-1.0-GPL). Ihre
  erlaubten Zwecke schließen eine „konkurrierende Nutzung" aus, und die Lizenz verbietet, die
  Lizenzschlüssel-Funktion zu umgehen (laut Lizenzdatei des Repositorys).
- **Kann gut:** eine gute Verwaltungsoberfläche über einer vorhandenen Datenbank.
- **Wo es Sie im Stich lässt:** Es ist Source-Available und keine freie Software im Sinne der Open Source Definition;
  was Sie damit tun dürfen, bestimmen die Bedingungen des Lizenzgebers, die sich schon geändert haben.
- **Exponential stattdessen:** die GPL, ohne Nutzungseinschränkungen und ohne Lizenzschlüssel.

### ApostropheCMS

- **Was es ist:** ein Node.js-CMS mit Bearbeitung im Kontext.
- **Lizenz und Modell:** MIT für den Kern; Apostrophe Technologies verkauft die Editionen Pro und Assembly.
- **Kann gut:** Bearbeitung auf der Seite.
- **Wo es Sie im Stich lässt:** Es braucht Node.js und MongoDB als Datenbank; die Preisseite führt die
  Multisite-Architektur und das Multitenant-Dashboard nur für die kostenpflichtige Assembly-Edition.
- **Exponential stattdessen:** Multisite im Kernel und eine Auswahl an Datenbanksystemen.

### TinaCMS und Decap CMS

- **Was sie sind:** Bearbeitungsoberflächen, die Markdown oder JSON für eine statische Website in ein Git-Repository
  schreiben.
- **Lizenz und Modell:** TinaCMS Apache-2.0, mit TinaCloud als kostenpflichtigem gehostetem Backend; Decap CMS MIT (bis
  Februar 2023 Netlify CMS).
- **Können gut:** Bearbeitung für statische Websites ohne serverseitiges CMS.
- **Wo sie Sie im Stich lassen:** Die Inhalte liegen als Dateien in Git, es gibt also keine echten Berechtigungen pro
  Inhalt, keine Workflows jenseits von Pull Requests und keine strukturierten Abfragen über Inhalte hinweg.
- **Exponential stattdessen:** ein echtes Inhalts-Repository mit Berechtigungen und Workflows und statische Ausgabe,
  wenn Sie sie wollen.

### Builder.io

- **Was es ist:** ein gehosteter visueller Editor und Headless-CMS.
- **Lizenz und Modell:** Die SDKs stehen unter MIT; der Dienst ist kommerziell.
- **Kann gut:** visueller Seitenbau für Marketing-Teams.
- **Wo es Sie im Stich lässt:** Inhalte und Editor leben im Dienst des Anbieters, zu dessen Tarifen und Bedingungen.
- **Exponential stattdessen:** Software, die Sie selbst betreiben.

### Medusa

- **Was es ist:** eine Commerce-Engine für Node.js.
- **Lizenz und Modell:** MIT, ausgenommen die Materialien der Enterprise Edition, die unter einer eigenen Lizenz und
  Vereinbarung stehen.
- **Kann gut:** ein modulares Commerce-Backend.
- **Wo es Sie im Stich lässt:** nur Commerce; Storefront und Content-Website sind eigene Anwendungen, die gebaut werden
  müssen.
- **Exponential stattdessen:** Inhalte und Shop in einem System.

### Next.js, Nuxt, SvelteKit, Remix, Astro und Gatsby

- **Was sie sind:** JavaScript-Frameworks für Websites und Webanwendungen.
- **Lizenz und Modell:** MIT; Next.js wird von Vercel entwickelt, Gatsby von Gatsby Inc., die Netlify 2023 übernommen
  hat. Die Funktionen von Remix wurden in React Router 7 übernommen.
- **Können gut:** schnelle Frontends, Server-Rendering und moderne Entwicklerwerkzeuge.
- **Wo sie Sie im Stich lassen:** Keines verwaltet Inhalte. Eine Website braucht ein separates CMS, und die Frameworks
  bewegen sich schnell: Mehrere hatten innerhalb weniger Jahre inkompatible Hauptversionen oder einen Kurswechsel (die
  Zusammenlegung von Remix, Gatsbys seltenere Versionen seit der Übernahme, mit 5.16.1 im Februar 2026). Jemand muss
  jede Website aktuell halten.
- **Exponential stattdessen:** ein Frontend, das das CMS selbst ausgibt, mit kompatibel gehaltenen Templates.

### Docusaurus und Eleventy

- **Was sie sind:** ein Generator für Dokumentations-Websites (Docusaurus, von Meta) und ein allgemeiner
  Static-Site-Generator (Eleventy).
- **Lizenz und Modell:** MIT.
- **Können gut:** Dokumentation und einfache statische Websites.
- **Wo sie Sie im Stich lassen:** siehe [Static-Site-Generatoren als Gattung](#static-site-generatoren-als-gattung).
- **Exponential stattdessen:** eine Bearbeitungsoberfläche und statisches Veröffentlichen.

## Ruby

### Ruby on Rails (Framework)

- **Was es ist:** das Ruby-Web-Framework.
- **Lizenz und Modell:** MIT.
- **Kann gut:** produktive Entwicklung von Webanwendungen.
- **Wo es Sie im Stich lässt:** kein Content-Management; jede CMS-Funktion ist eigener Code, und Rails-Hauptversionen
  sind Arbeit für Ruby-Entwickler.
- **Exponential stattdessen:** CMS-Funktionen, fertig zum Einstellen.

### Refinery CMS, Spina, LocomotiveCMS, Alchemy CMS und Camaleon CMS

- **Was sie sind:** CMS-Engines für Rails.
- **Lizenz und Modell:** Refinery MIT, Spina MIT, LocomotiveCMS MIT, Alchemy BSD-3-Clause, Camaleon MIT; alles Projekte
  von Communitys oder kleinen Firmen.
- **Können gut:** ein CMS in einer Rails-Anwendung.
- **Wo sie Sie im Stich lassen:** Jedes braucht eine Rails-Anwendung, die gehostet und aktualisiert werden muss. Die
  Aktivität schwankt: Refinerys v4.1.0 (Mai 2026) folgte auf eine lange Pause, und die Engine von LocomotiveCMS hat
  kein GitHub-Release, ihr letzter Commit war im August 2025.
- **Exponential stattdessen:** ein eigenständiges System mit aktiver Versionslinie.

### Spree und Solidus

- **Was sie sind:** Commerce-Plattformen auf Rails.
- **Lizenz und Modell:** BSD-3-Clause.
- **Können gut:** ein vollständiger Shop auf Rails.
- **Wo sie Sie im Stich lassen:** Commerce zuerst, Inhalte danach; eine Rails-Anwendung, die betrieben werden muss.
- **Exponential stattdessen:** Inhalte und Shop in einem.

### Jekyll, Middleman und Bridgetown

- **Was sie sind:** Static-Site-Generatoren in Ruby; Jekyll treibt die eingebauten Builds von GitHub Pages an.
- **Lizenz und Modell:** MIT.
- **Können gut:** einfache statische Websites.
- **Wo sie Sie im Stich lassen:** siehe [Static-Site-Generatoren als Gattung](#static-site-generatoren-als-gattung). Die
  letzte Jekyll-Version ist 4.4.1 vom Januar 2025.
- **Exponential stattdessen:** eine Oberfläche für die Redaktion und ein eigener Weg zu GitHub Pages.

## Java, Kotlin und die JVM

### Liferay

- **Was es ist:** ein Java-Portal und eine Digital-Experience-Plattform.
- **Lizenz und Modell:** Liferay Portal steht unter LGPL-2.1-or-later oder der Liferay DXP EULA (laut seinen
  Quelltext-Kopfzeilen); Liferay DXP ist ein kostenpflichtiges Abonnement. Das letzte allgemein verfügbare Release von
  Liferay Portal auf GitHub ist 7.4.3.132 vom Februar 2025.
- **Kann gut:** ein breiter Portal-Funktionsumfang für große Organisationen.
- **Wo es Sie im Stich lässt:** Es braucht einen Java-Anwendungsserver und viel Hardware und Fachwissen; die meisten
  Enterprise-Funktionen und der Support kommen mit dem Abonnement.
- **Exponential stattdessen:** Enterprise-Content-Funktionen auf gewöhnlichem PHP-Hosting, unter einer Lizenz.

### Magnolia

- **Was es ist:** eine Digital-Experience-Plattform in Java.
- **Lizenz und Modell:** ein kommerzielles Produkt, vertrieben von Magnolia International mit Cloud- und
  Support-Angeboten.
- **Kann gut:** Headless- und hybride Auslieferung für große Teams.
- **Wo es Sie im Stich lässt:** Ohne kommerziellen Vertrag gibt es kein Produkt zu betreiben, und es braucht
  Java-Hosting und Fachwissen.
- **Exponential stattdessen:** das ganze Produkt unter der GPL.

### Jahia

- **Was es ist:** eine Digital-Experience-Plattform in Java.
- **Lizenz und Modell:** Das Kern-Repository wird unter Apache-2.0 und der Jahia Sustainable Enterprise License
  ausgeliefert (beide Lizenzdateien liegen im Repository); die Enterprise-Distribution ist kommerziell.
- **Kann gut:** strukturierte Inhalte und Personalisierung für Unternehmen.
- **Wo es Sie im Stich lässt:** Java-Hosting und eine kommerzielle Beziehung für das Enterprise-Produkt.
- **Exponential stattdessen:** die GPL und gewöhnliches Hosting.

### OpenCms

- **Was es ist:** ein Java-CMS von Alkacon.
- **Lizenz und Modell:** LGPL-2.1; Alkacon verkauft Support.
- **Kann gut:** ein langlebiges quelloffenes Java-CMS.
- **Wo es Sie im Stich lässt:** ein Java-Servlet-Container, der betrieben und gepflegt werden muss.
- **Exponential stattdessen:** PHP-Hosting, das fast jeder Anbieter hat.

### Bloomreach Experience Manager (früher Hippo CMS)

- **Was es ist:** das Java-CMS, das Hippo CMS war, bevor Bloomreach Hippo übernahm.
- **Lizenz und Modell:** eine Community Edition unter Apache-2.0 auf GitHub; das vollständige Produkt ist kommerziell.
- **Kann gut:** strukturierte Inhalte für Unternehmen.
- **Wo es Sie im Stich lässt:** Der Anbieter konzentriert sich auf seine kommerziellen Produkte; die Community Edition
  braucht Java-Hosting.
- **Exponential stattdessen:** eine Plattform, deren freie Ausgabe das ganze Produkt ist.

### CrafterCMS

- **Was es ist:** ein Git-basiertes Java-CMS.
- **Lizenz und Modell:** GPL-3.0, mit einer kommerziellen Enterprise Edition.
- **Kann gut:** Inhalte in Git mit einer Headless-Auslieferungsschicht.
- **Wo es Sie im Stich lässt:** Java, Elasticsearch oder OpenSearch und mehrere Dienste, die betrieben werden müssen.
- **Exponential stattdessen:** eine einzige PHP-Anwendung.

### dotCMS

- **Was es ist:** ein Headless- und Hybrid-CMS in Java.
- **Lizenz und Modell:** Business Source License 1.1. Die Nutzung in Produktion ist nur kostenlos, solange die „Total
  Finances" (Umsatz, Budget oder Finanzierung) der Nutzerin oder des Nutzers 5 Millionen US-Dollar nicht übersteigen,
  wobei bei einer Agentur auch die Kunden zählen, für die sie baut; jede Version wechselt vier Jahre nach dem 1. August
  2025 zu GPL-3.0 (laut LICENSE des Repositorys).
- **Kann gut:** ein leistungsfähiges Headless-CMS.
- **Wo es Sie im Stich lässt:** Eine erfolgreiche Organisation wächst aus der kostenlosen Nutzungserlaubnis heraus, und
  die Lizenz, nicht die Software, entscheidet, was sie darf.
- **Exponential stattdessen:** die GPL vom ersten Tag an, für Organisationen jeder Größe.

### Alfresco

- **Was es ist:** ein Enterprise-Content- und Dokumentenmanagementsystem, heute im Besitz von Hyland.
- **Lizenz und Modell:** Die Community Edition steht unter LGPL-3.0; die Enterprise Edition ist kommerziell.
- **Kann gut:** Dokumentenmanagement.
- **Wo es Sie im Stich lässt:** Es ist ein Dokumenten-Repository, kein Website-CMS; Websites brauchen ein weiteres
  Produkt.
- **Exponential stattdessen:** Web-Content-Management mit Datei- und Dokumentenverwaltung.

### JBake, Spring Boot und Ktor

- **Was sie sind:** ein Static-Site-Generator in Java (JBake) und die Java- und Kotlin-Frameworks, mit denen Websites
  gebaut werden.
- **Lizenz und Modell:** JBake MIT, Spring Boot Apache-2.0, Ktor Apache-2.0 (JetBrains).
- **Können gut:** solide Grundlagen für Entwickler.
- **Wo sie Sie im Stich lassen:** kein Content-Management; siehe auch
  [Static-Site-Generatoren als Gattung](#static-site-generatoren-als-gattung).
- **Exponential stattdessen:** Das Content-Management gibt es bereits.

### Apache Lenya und andere eingestellte Java-CMS

- **Was sie sind:** frühere quelloffene Java-CMS wie Apache Lenya, das die Apache Software Foundation in ihren Attic
  verlegt hat.
- **Wo sie Sie im Stich lassen:** gar keine Aktualisierungen mehr; Websites darauf mussten migrieren.
- **Exponential stattdessen:** eine Codelinie, die seit 2002 weitergeführt statt aufgegeben wurde.

## .NET und C#

### Umbraco

- **Was es ist:** das bekannteste quelloffene .NET-CMS.
- **Lizenz und Modell:** MIT; Umbraco HQ verkauft Umbraco Cloud und Zusatzprodukte.
- **Kann gut:** ein freundliches Backoffice und eine große .NET-Community.
- **Wo es Sie im Stich lässt:** Umbraco wechselt zwischen Hauptversionen mit kurzem und mit langem Support: Umbraco 18
  (Juni 2026) ist ein Kurzzeit-Release, das im Juni 2027 sein Lebensende erreicht, und das Langzeit-Release Umbraco 13
  endet im Dezember 2026. Websites stehen alle ein bis zwei Jahre vor einem Aktualisierungsprojekt, auf .NET-Hosting.
- **Exponential stattdessen:** eine stabile Kernel-Linie und PHP-Hosting.

### Orchard Core, Piranha CMS, Oqtane und DNN

- **Was sie sind:** quelloffene .NET-CMS und Anwendungs-Frameworks; DNN hieß früher DotNetNuke.
- **Lizenz und Modell:** Orchard Core BSD-3-Clause; Piranha, Oqtane und DNN MIT.
- **Können gut:** CMS-Funktionen für .NET-Teams.
- **Wo sie Sie im Stich lassen:** Sie brauchen .NET-Entwickler und -Hosting; ein großer Teil der Arbeit an einer Website
  ist Code.
- **Exponential stattdessen:** Einstellungen in der Administrationsoberfläche.

### Kentico Xperience, Sitefinity, Sitecore und Optimizely

- **Was sie sind:** kommerzielle Digital-Experience-Plattformen auf .NET.
- **Lizenz und Modell:** proprietäre Lizenzen oder Abonnements (Kentico, Progress Sitefinity); Sitecore und Optimizely
  stehen auf [Proprietäre DXP und SaaS](proprietaere-dxp-und-saas.md).
- **Können gut:** Marketing-Funktionen für Teams mit Budget.
- **Wo sie Sie im Stich lassen:** Ohne Budget gibt es keine Lizenz, und die Website kann das Abonnement nicht
  überdauern.
- **Exponential stattdessen:** freie Software, die weiterläuft, was immer mit einem Anbieter geschieht.

## Go

### Hugo

- **Was es ist:** der wohl schnellste Static-Site-Generator.
- **Lizenz und Modell:** Apache-2.0, ehrenamtlich gepflegt.
- **Kann gut:** sehr schnelle Builds großer statischer Websites.
- **Wo es Sie im Stich lässt:** Es ist noch immer eine 0.x-Versionslinie (v0.169.0 im Oktober 2026), und seine
  Templates haben sich zwischen Versionen geändert; siehe auch
  [Static-Site-Generatoren als Gattung](#static-site-generatoren-als-gattung).
- **Exponential stattdessen:** stabile Templates und eine Bearbeitungsoberfläche.

### Ponzu

- **Was es ist:** ein Headless-CMS in Go.
- **Lizenz und Modell:** BSD-3-Clause.
- **Wo es Sie im Stich lässt:** Die letzte Version, v0.11.0, stammt vom September 2019.
- **Exponential stattdessen:** eine aktive Versionslinie.

### PocketBase

- **Was es ist:** ein quelloffenes Backend in einer einzigen Datei (Datenbank, Anmeldung, Dateien, Verwaltung).
- **Lizenz und Modell:** MIT. Sein GitHub-Repository ist archiviert und verweist auf ein neues Zuhause außerhalb von
  GitHub; die Versionen sind noch 0.x.
- **Kann gut:** ein schnelles Backend für kleine Anwendungen.
- **Wo es Sie im Stich lässt:** Es ist kein CMS; Seiten, Bearbeitung und Veröffentlichung müssen Sie selbst bauen.
- **Exponential stattdessen:** ein fertiges CMS.

### Gitea

- **Was es ist:** eine selbst gehostete Git-Plattform, die manchmal statische Websites hostet.
- **Lizenz und Modell:** MIT.
- **Wo es Sie im Stich lässt:** Es hostet Code, keine Inhalte; eine Website braucht weiterhin einen Generator oder ein
  CMS.
- **Exponential stattdessen:** ein CMS, dessen statische Ausgabe auf jeden Git-Host übertragen werden kann, auch auf
  Gitea.

## Rust

### Zola und Cobalt

- **Was sie sind:** Static-Site-Generatoren in Rust.
- **Lizenz und Modell:** Zola EUPL-1.2, Cobalt Apache-2.0.
- **Können gut:** eine einzige schnelle Programmdatei.
- **Wo sie Sie im Stich lassen:** Beide sind noch 0.x-Versionen; siehe
  [Static-Site-Generatoren als Gattung](#static-site-generatoren-als-gattung).
- **Exponential stattdessen:** statische Ausgabe aus einem vollständigen CMS.

### Loco, Axum und Actix Web

- **Was sie sind:** Rust-Web-Frameworks; Loco orientiert sich an Rails.
- **Lizenz und Modell:** Loco Apache-2.0, Axum MIT, Actix Web MIT oder Apache-2.0.
- **Wo sie Sie im Stich lassen:** kein Content-Management; jede Funktion ist Rust-Code, den wenige Web-Agenturen pflegen
  können.
- **Exponential stattdessen:** eine weithin bekannte Sprache und ein fertiges CMS.

## C und C++

### CppCMS und Wt

- **Was sie sind:** C++-Web-Frameworks; trotz seines Namens ist CppCMS ein Framework, kein CMS.
- **Lizenz und Modell:** CppCMS MIT; Wt GPL-2.0 mit einer kommerziellen Lizenz von Emweb.
- **Können gut:** sehr hohe Leistung.
- **Wo sie Sie im Stich lassen:** kein Content-Management, und C++-Webentwickler sind selten, eine Website hängt also an
  wenigen Menschen. Deshalb sind fast keine Content-Management-Systeme in C oder C++ geschrieben.
- **Exponential stattdessen:** ein CMS in einer der bekanntesten Web-Sprachen.

## Swift

### Publish, Ignite und Vapor

- **Was sie sind:** Static-Site-Generatoren in Swift (Publish, Ignite) und ein Swift-Web-Framework (Vapor).
- **Lizenz und Modell:** MIT.
- **Wo sie Sie im Stich lassen:** Die letzte Publish-Version, 0.9.0, ist vom April 2022; Ignite ist noch 0.x; Vapor hat
  kein Content-Management. Swift auf dem Server ist eine Nischenkompetenz.
- **Exponential stattdessen:** ein aktives, vollständiges CMS.

## Elixir

### Beacon, Phoenix und Tableau

- **Was sie sind:** ein CMS auf Phoenix (Beacon), das Phoenix-Framework und ein Static-Site-Generator in Elixir
  (Tableau).
- **Lizenz und Modell:** MIT.
- **Können gut:** die Echtzeitfunktionen von Phoenix.
- **Wo sie Sie im Stich lassen:** Beacon ist noch 0.x (v0.5.1, April 2025), und Elixir-Hosting und -Entwickler sind
  selten.
- **Exponential stattdessen:** ein ausgereiftes CMS auf verbreitetem Hosting.

## CFML

### Lucee

- **Was es ist:** die quelloffene CFML-Engine, eine Alternative zu Adobe ColdFusion (siehe
  [Proprietäre DXP und SaaS](proprietaere-dxp-und-saas.md)).
- **Lizenz und Modell:** LGPL-2.1, von der Lucee Association.
- **Kann gut:** führt CFML-Anwendungen ohne Adobe-Lizenz aus.
- **Wo es Sie im Stich lässt:** Es ist eine Sprach-Engine, kein CMS, und CFML-Entwickler sind wenige.
- **Exponential stattdessen:** ein vollständiges CMS in PHP.

## Haskell, Clojure und Perl

### Hakyll und Cryogen

- **Was sie sind:** Static-Site-Generatoren in Haskell (Hakyll) und Clojure (Cryogen).
- **Lizenz und Modell:** Hakyll BSD-3-Clause, Cryogen EPL-1.0.
- **Wo sie Sie im Stich lassen:** Die letzte Cryogen-Version, 0.4.0, ist vom Dezember 2020; beide brauchen Entwickler
  seltener Sprachen. Siehe [Static-Site-Generatoren als Gattung](#static-site-generatoren-als-gattung).

### Movable Type

- **Was es ist:** ein Blog- und Publishing-System in Perl von Six Apart.
- **Lizenz und Modell:** proprietär; die frühere quelloffene Ausgabe wird nicht mehr angeboten.
- **Wo es Sie im Stich lässt:** eine kostenpflichtige Lizenz und Perl-Hosting.
- **Exponential stattdessen:** freie Software.

### Bricolage

- **Was es ist:** ein Content-Management- und Publishing-System in Perl.
- **Wo es Sie im Stich lässt:** Der letzte Commit seines Repositorys ist vom Februar 2015; es wird nicht mehr
  entwickelt.

### Mojolicious

- **Was es ist:** ein Perl-Web-Framework.
- **Lizenz und Modell:** Artistic-2.0.
- **Wo es Sie im Stich lässt:** kein Content-Management.

## Lua, Nim, Crystal, Dart und Zig

- **Lapis** (Lua und MoonScript, MIT) und **Sailor** (Lua, MIT, letzter Commit im Oktober 2022): Web-Frameworks ohne
  Content-Management.
- **Jester** (Nim, MIT): ein Web-Framework; letzte Version v0.6.0 vom Juni 2023.
- **Amber** und **Lucky** (Crystal, MIT): Web-Frameworks ohne Content-Management.
- **Jaspr** (Dart, MIT): ein Web-Framework, noch 0.x.
- **Zine** (Zig, MIT): ein Static-Site-Generator, noch 0.x.

Alle sind Frameworks oder Generatoren für Entwickler kleiner Sprach-Communitys; keines bietet Content-Management,
Bearbeitung oder Berechtigungen für Menschen ohne Programmierkenntnisse.

## Gehostete Baukästen ohne Code

Das sind proprietäre gehostete Dienste. Das Für und Wider gehosteter Plattformen allgemein steht auf
[Proprietäre DXP und SaaS](proprietaere-dxp-und-saas.md); hier stehen die Baukästen, die dort nicht vorkommen.

- **Bubble:** ein Anwendungsbaukasten, dessen Anwendungen auf Bubbles eigener Plattform laufen.
- **Framer:** ein designorientierter Website-Baukasten, gehostet von Framer.
- **Notion Sites:** Seiten, die aus einem Notion-Arbeitsbereich veröffentlicht werden.
- **Google Sites:** ein kostenloser Website-Baukasten in einem Google-Konto.
- **Carrd:** einseitige Websites, mit kostenpflichtigen Tarifen für mehr Funktionen.

**Was sie gut können:** schnelle Ergebnisse ohne technische Kenntnisse.
**Wo sie Sie im Stich lassen:** Die Website existiert nur im Dienst des Anbieters, im Rahmen seiner Tarife, Preise und
Bedingungen. Sie lässt sich nicht als funktionierende Website auf einen eigenen Server umziehen, und sie endet, wenn das
Konto oder der Dienst endet.
**Exponential stattdessen:** Software, von der Sie eine eigene Kopie besitzen, die überall läuft, wo PHP läuft, mit
einer kostenlosen statischen Kopie auf GitHub Pages, wenn Sie gar keinen Server wollen.

## Static-Site-Generatoren als Gattung

Static-Site-Generatoren (Pelican, Nikola, MkDocs, Hugo, Jekyll, Eleventy, Docusaurus, Zola und die anderen oben) machen
aus Textdateien hervorragend schnelle Seiten. Für eine geschäftliche Website, die jahrelang funktionieren muss, lassen
sie dieselben Lücken:

- **Keine Bearbeitung für Nicht-Entwickler.** Inhalte sind Dateien in einem Repository; die Redaktion braucht Git oder
  eine Ergänzung wie TinaCMS oder Decap CMS.
- **Keine Berechtigungen oder Workflows.** Wer in das Repository schreiben darf, kann alles ändern; eine Prüfung ist ein
  Pull Request, kein redaktioneller Workflow mit Zuständen und Rollen.
- **Nichts Dynamisches.** Suche, Formulare, Kommentare, Anmeldung und Shops brauchen Drittdienste, jeder mit eigenem
  Konto, eigenen Bedingungen und Kosten.
- **Struktur steckt in Konventionen.** Front Matter und Ordnernamen ersetzen ein Inhaltsmodell; Abfragen über Inhalte
  hinweg sind auf das beschränkt, was der Generator kann.
- **Versionswechsel.** Mehrere der Generatoren sind noch 0.x und ändern Templates zwischen Versionen.

**Exponential stattdessen:** [Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md) liefert Ihnen dieselben
statischen Dateien auf einem kostenlosen Host, erzeugt aus einem echten CMS mit Redaktion, Berechtigungen, Workflows und
Versionen, und es meldet, was statisch nicht funktionieren wird
([Dynamische Funktionen in einer statischen Kopie](../anleitungen/statisches-veroeffentlichen-dynamische-funktionen.md)).

## Zusammenfassung

| Gruppe | Typische Lizenz | Typische Lücke für eine Website ohne Budget und mit langer Lebensdauer |
|---|---|---|
| Python-CMS | BSD, GPL | Ein Django- oder Python-Projekt, das gepflegt werden muss; Inhaltsmodell im Code |
| JavaScript-Headless-CMS | MIT mit kostenpflichtigen Editionen, Source-Available | Eine zweite Anwendung fürs Frontend; Funktionen in kostenpflichtigen Editionen |
| JavaScript-Frameworks | MIT | Kein Content-Management; schnell wechselnde Hauptversionen |
| Ruby | MIT, BSD | Eine Rails-Anwendung, die gepflegt werden muss; ungleichmäßige Aktivität |
| Java und .NET | LGPL, GPL, BSL, kommerziell | Aufwendiges Hosting und Fachwissen; Enterprise-Funktionen kostenpflichtig |
| Go, Rust, Swift, Elixir und andere | MIT, Apache, EUPL | Frameworks oder 0.x-Generatoren für kleine Entwickler-Communitys |
| Gehostete Baukästen | Proprietär | Die Website existiert nur im Dienst des Anbieters |
| Exponential | GPL-2.0-or-later | Ein vollständiges CMS auf gewöhnlichem Hosting, mit statischer Ausgabe und Schnittstellen, unter einer Lizenz |

## Quellen

Geprüft im November 2026. Lizenzangaben stammen aus der Lizenzdatei des jeweiligen Repositorys oder der Lizenzerkennung
von GitHub, Versions- und Aktivitätsangaben aus den Releases und der Commit-Geschichte des jeweiligen Repositorys.

- Python: <https://github.com/django/django>, <https://github.com/django-cms/django-cms>,
  <https://github.com/wagtail/wagtail>, <https://github.com/plone/Products.CMFPlone>, <https://plone.org>,
  <https://github.com/stephenmcd/mezzanine>, <https://github.com/feincms/feincms>, <https://github.com/odoo/odoo>,
  <https://www.odoo.com/page/editions>, <https://github.com/getpelican/pelican>, <https://github.com/getnikola/nikola>,
  <https://github.com/mkdocs/mkdocs>, <https://github.com/pallets/flask>
- JavaScript und TypeScript: <https://github.com/strapi/strapi>, <https://github.com/TryGhost/Ghost>,
  <https://ghost.org/docs/faq/supported-databases/>, <https://github.com/payloadcms/payload>,
  <https://github.com/keystonejs/keystone>, <https://github.com/directus/directus>,
  <https://github.com/apostrophecms/apostrophe>, <https://apostrophecms.com/pricing>,
  <https://github.com/tinacms/tinacms>, <https://github.com/decaporg/decap-cms>,
  <https://www.netlify.com/blog/netlify-cms-to-become-decap-cms/>, <https://github.com/BuilderIO/builder>,
  <https://github.com/medusajs/medusa>, <https://github.com/vercel/next.js>, <https://github.com/nuxt/nuxt>,
  <https://github.com/sveltejs/kit>, <https://github.com/remix-run/remix>,
  <https://remix.run/blog/merging-remix-and-react-router>, <https://github.com/withastro/astro>,
  <https://github.com/gatsbyjs/gatsby>,
  <https://www.netlify.com/press/netlify-acquires-gatsby-inc-to-accelerate-adoption-of-composable-web-architectures/>,
  <https://github.com/facebook/docusaurus>, <https://github.com/11ty/eleventy>
- Ruby: <https://github.com/rails/rails>, <https://github.com/refinery/refinerycms>, <https://github.com/SpinaCMS/Spina>,
  <https://github.com/locomotivecms/engine>, <https://github.com/AlchemyCMS/alchemy_cms>,
  <https://github.com/owen2345/camaleon-cms>, <https://github.com/spree/spree>, <https://github.com/solidusio/solidus>,
  <https://github.com/jekyll/jekyll>, <https://github.com/middleman/middleman>,
  <https://github.com/bridgetownrb/bridgetown>
- Java, Kotlin und die JVM: <https://github.com/liferay/liferay-portal>, <https://www.liferay.com>,
  <https://www.magnolia-cms.com/>, <https://github.com/Jahia/jahia>, <https://github.com/alkacon/opencms-core>,
  <https://github.com/bloomreach/brxm>, <https://github.com/craftercms/craftercms>, <https://github.com/dotCMS/core>,
  <https://github.com/Alfresco/alfresco-community-repo>, <https://github.com/jbake-org/jbake>,
  <https://github.com/spring-projects/spring-boot>, <https://github.com/ktorio/ktor>,
  <https://attic.apache.org/projects/lenya.html>
- .NET: <https://github.com/umbraco/Umbraco-CMS>,
  <https://umbraco.com/products/knowledge-center/long-term-support-and-end-of-life/>,
  <https://github.com/OrchardCMS/OrchardCore>, <https://github.com/PiranhaCMS/piranha.core>,
  <https://github.com/oqtane/oqtane.framework>, <https://github.com/dnnsoftware/Dnn.Platform>,
  <https://www.kentico.com/>, <https://www.progress.com/sitefinity-cms>
- Go: <https://github.com/gohugoio/hugo>, <https://github.com/ponzu-cms/ponzu>,
  <https://github.com/pocketbase/pocketbase>, <https://github.com/go-gitea/gitea>
- Rust: <https://github.com/getzola/zola>, <https://github.com/cobalt-org/cobalt.rs>, <https://github.com/loco-rs/loco>,
  <https://github.com/tokio-rs/axum>, <https://github.com/actix/actix-web>
- C++, Swift, Elixir, CFML: <https://github.com/artyom-beilis/cppcms>, <https://github.com/emweb/wt>,
  <https://github.com/JohnSundell/Publish>, <https://github.com/twostraws/Ignite>, <https://github.com/vapor/vapor>,
  <https://github.com/BeaconCMS/beacon>, <https://github.com/phoenixframework/phoenix>,
  <https://github.com/elixir-tools/tableau>, <https://github.com/lucee/Lucee>
- Haskell, Clojure, Perl und weitere: <https://github.com/jaspervdj/hakyll>,
  <https://github.com/cryogen-project/cryogen-core>, <https://github.com/movabletype/movabletype>,
  <https://www.movabletype.com>, <https://github.com/bricoleurs/bricolage>, <https://github.com/mojolicious/mojo>,
  <https://github.com/leafo/lapis>, <https://github.com/sailorproject/sailor>, <https://github.com/dom96/jester>,
  <https://github.com/amberframework/amber>, <https://github.com/luckyframework/lucky>,
  <https://github.com/schultek/jaspr>, <https://github.com/kristoff-it/zine>
- Gehostete Baukästen: <https://bubble.io>, <https://www.framer.com>, <https://www.notion.com/product/sites>,
  <https://sites.google.com>, <https://carrd.co>
