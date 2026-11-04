# Proprietäre DXPs und SaaS

Diese Seite von [Exponential im Wettbewerb](README.md) behandelt die Produkte, deren Code man nicht frei ausführen,
untersuchen, ändern und weitergeben darf: Enterprise-Plattformen für digitale Erlebnisse (DXPs), Headless Content
Management als Dienst sowie gehostete Website-Baukästen und Shops. Für jedes Produkt nennt sie, was es ist, seine
Lizenz und sein Geschäftsmodell, was es gut kann, wo es eine Nutzerin ohne Budget oder mit langem Zeithorizont im Stich
lässt und was Exponential stattdessen tut. Die Fakten stammen von den eigenen Seiten der Anbieter, Stand November 2026
(siehe [Quellen](#quellen)); Pläne und Preise ändern sich, prüfen Sie sie vor einer Entscheidung.

## Inhalt

- [1. Enterprise DXPs](#1-enterprise-dxps)
- [2. Headless CMS als Dienst](#2-headless-cms-als-dienst)
- [3. Website-Baukästen und gehostete Shops](#3-website-baukästen-und-gehostete-shops)
- [4. Was die ganze Gruppe gemeinsam hat](#4-was-die-ganze-gruppe-gemeinsam-hat)
- [Quellen](#quellen)

## 1. Enterprise DXPs

### Adobe Experience Manager (AEM)

- **Was es ist:** Adobes Plattform für Inhalte und digitale Medien, Teil der Adobe Experience Cloud, angeboten als AEM
  as a Cloud Service und in früheren Versionen zum eigenen Hosting.
- **Lizenz und Modell:** proprietär; lizenziert über Adobe, Preise auf Anfrage.
- **Kann gut:** Inhalte und Medienverwaltung im großen Maßstab und die Anbindung an Adobes Marketingprodukte.
- **Wo es Nutzer ohne Budget oder mit langem Horizont im Stich lässt:** es gibt keine kostenlose Ausgabe für den
  Produktivbetrieb; der Betrieb braucht einen Adobe-Vertrag und Fachleute; der Cloud-Dienst wird von Adobe betrieben,
  Hosting und Zeitpunkt der Updates bestimmt der Anbieter.
- **Exponential stattdessen:** freie Software mit allen Funktionen, auf jedem PHP-Hosting nach Wahl des Eigentümers,
  der selbst entscheidet, wann er aktualisiert.

### Adobe ColdFusion

- **Was es ist:** Adobes Anwendungsserver für die Sprache CFML, oft für eigene Websites und Intranets eingesetzt.
- **Lizenz und Modell:** proprietär; verkauft pro Ausgabe (Standard, Enterprise), mit einer kostenlosen
  Entwicklerausgabe, die nicht für den Produktivbetrieb gedacht ist.
- **Kann gut:** schnelle Entwicklung datenbankgestützter Anwendungen in CFML.
- **Wo es versagt:** der Produktivbetrieb braucht eine bezahlte Lizenz; die Sprachgemeinschaft ist klein, Fachleute und
  Erweiterungen sind rar; eine quelloffene CFML-Engine (Lucee) gibt es, siehe [Andere Sprachen](andere-sprachen.md).
- **Exponential stattdessen:** PHP, eine der am weitesten verbreiteten Websprachen, mit einem vollständigen CMS darauf
  und ohne Lizenzgebühr.

### Sitecore

- **Was es ist:** eine DXP auf Microsoft .NET, verkauft als Experience Manager (XM), Experience Platform (XP) und als
  Cloud-Angebote, mit Commerce- und Content-Hub-Produkten.
- **Lizenz und Modell:** proprietär; Preise auf Anfrage.
- **Kann gut:** Personalisierung und Marketingfunktionen für große Organisationen auf dem Microsoft-Stack.
- **Wo es versagt:** es gibt keine kostenlose Ausgabe für den Produktivbetrieb, ohne Budget ist es also unerreichbar;
  es braucht .NET-Fachwissen und einen Sitecore-Partner oder Lizenznehmer für den Betrieb.
- **Exponential stattdessen:** keine Lizenz, läuft auf Linux- oder Windows-Hosting und hält seine Sites innerhalb der
  Linie aktualisierbar ([Exponential 6.0 aktualisieren](../UPDATE.md)).

### Optimizely

- **Was es ist:** das frühere Episerver, umbenannt, nachdem es 2020 Optimizely übernommen hatte; eine DXP mit Content
  Management (.NET), Experimenten und Commerce, auch als SaaS-CMS.
- **Lizenz und Modell:** proprietär; Preise auf Anfrage.
- **Kann gut:** Inhalte mit A/B-Tests und Experimenten verbinden.
- **Wo es versagt:** kein kostenloser Produktivbetrieb; Produktnamen und Pakete änderten sich mit der Umbenennung, und
  langfristige Pläne hängen von der Roadmap des Anbieters ab.
- **Exponential stattdessen:** eine stabile, dokumentierte Produktlinie unter der Kontrolle der Gemeinschaft
  ([Geschichte von Exponential](../HISTORY.md)).

### Acquia

- **Was es ist:** ein Unternehmen, das Hosting, Werkzeuge und eine DXP rund um Drupal verkauft.
- **Lizenz und Modell:** die Dienste sind proprietäre Abonnements; Drupal selbst ist freie Software
  (siehe [PHP-CMS und -Frameworks](php-cms-und-frameworks.md)).
- **Kann gut:** verwaltetes Drupal-Hosting und Werkzeuge für große Drupal-Landschaften.
- **Wo es versagt:** der Komfort ist ein Abonnement; die zusätzlichen Plattformfunktionen bleiben beim Anbieter, wenn
  das Abonnement endet.
- **Exponential stattdessen:** das ganze Produkt ist freie Software; Hosting ist eine Wahl, kein Teil des Produkts.

### Bloomreach Content

- **Was es ist:** das Content-Produkt von Bloomreach, einem Commerce- und Marketingunternehmen. Sein Vorgänger, Hippo
  CMS, war quelloffen.
- **Lizenz und Modell:** proprietäre SaaS; Preise auf Anfrage.
- **Kann gut:** Inhalte, verbunden mit Bloomreachs Commerce-Suche und Personalisierung.
- **Wo es versagt:** kein kostenloser Produktivbetrieb; das Produkt hängt an der Plattform des Anbieters.
- **Exponential stattdessen:** Suche, Inhalte und Anbindungen, die der Eigentümer betreibt und behält.

### Ibexa DXP

- **Was es ist:** die kommerzielle Plattform für digitale Erlebnisse der Ibexa AS (früher eZ Systems AS), auf Symfony
  gebaut und Nachfolgelinie der eZ-Platform-Produkte ([Geschichte von Exponential](../HISTORY.md)).
- **Lizenz und Modell:** die quelloffene Basis, Ibexa OSS, steht unter der GNU GPL Version 2; die kommerziellen
  Ausgaben und Funktionen sind unter der Ibexa Business Use License mit Abonnement lizenziert.
- **Kann gut:** eine moderne, auf Symfony gebaute Plattform mit kommerziellem Support und Ausgaben für Headless,
  Experience und Commerce.
- **Wo es Nutzer ohne Budget im Stich lässt:** die Funktionen der kommerziellen Ausgaben brauchen ein Abonnement; eine
  Organisation ohne Budget bleibt auf die quelloffene Basis beschränkt.
- **Exponential stattdessen:** alle Funktionen in einem freien Produkt und Distributionen, die die quelloffene
  Plattform mit dem Legacy-Kernel verbinden ([Distributionen von Exponential](../DISTRIBUTIONS.md)).

### Salesforce Experience Cloud

- **Was es ist:** Salesforce' Produkt für Portale, Communities und Sites auf Salesforce-Daten.
- **Lizenz und Modell:** proprietäre SaaS, lizenziert pro Benutzer oder Anmeldung innerhalb von Salesforce-Verträgen.
- **Kann gut:** Sites, die direkt auf Salesforce-Kundendaten arbeiten.
- **Wo es versagt:** gemessen nach Benutzern und Anmeldungen; setzt ein Salesforce-Abonnement voraus; die Site läuft
  nicht außerhalb von Salesforce.
- **Exponential stattdessen:** unbegrenzt viele Benutzer und Besucher ohne Lizenzkosten; Anbindung an CRMs über seine
  APIs ([Mit Exponential 6.0 integrieren](../INTEGRATING.md)).

### Microsoft SharePoint

- **Was es ist:** Microsofts Plattform für Zusammenarbeit und Intranets, vor allem als SharePoint in Microsoft 365.
- **Lizenz und Modell:** proprietär; Teil von Microsoft-365-Abonnements pro Benutzer.
- **Kann gut:** Zusammenarbeit an Dokumenten in Organisationen, die Microsoft 365 schon nutzen.
- **Wo es versagt:** pro Benutzer lizenziert; öffentliche Websites sind nicht sein Zweck; Anpassungen folgen den Regeln
  der Microsoft-Plattform.
- **Exponential stattdessen:** ein CMS für öffentliche Websites und Intranets mit Rollen, Richtlinien und Bereichen,
  lizenziert für alle.

## 2. Headless CMS als Dienst

Diese Produkte speichern Inhalte in der Cloud des Anbieters und liefern sie über APIs an ein Frontend, das der Kunde
baut.

### Contentful

- **Was es ist:** ein gehostetes Headless-CMS.
- **Lizenz und Modell:** proprietäre SaaS; ein kostenloser Plan mit Nutzungsgrenzen, dann bezahlte und
  Enterprise-Pläne.
- **Kann gut:** eine saubere Content-API und Inhaltsmodellierung für Entwicklerteams.
- **Wo es versagt:** Grenzen bei Datensätzen, Benutzern, Umgebungen und API-Nutzung bestimmen, wann ein Projekt zahlen
  muss; die Inhalte liegen in der Cloud des Anbieters; ein eigenes Frontend muss gebaut und betrieben werden.
- **Exponential stattdessen:** Inhaltsspeicher, Redaktionsoberfläche und Website in einem Produkt, mit GraphQL und REST
  für den Headless-Einsatz, wenn gewünscht ([GraphQL](../funktionen/6.0/graphql.md)).

### Contentstack

- **Was es ist:** ein gehostetes Headless-CMS für Unternehmen.
- **Lizenz und Modell:** proprietäre SaaS, im Abonnement verkauft.
- **Kann gut:** Workflows und Anbindungen für Inhalte über viele Kanäle.
- **Wo es versagt:** kein eigenes Hosting; die Kosten wachsen mit dem Plan; das Inhaltsmodell ist an die Plattform des
  Anbieters gebunden.
- **Exponential stattdessen:** dieselbe Inhaltsmodellierung (Inhaltsklassen und Attribute) auf dem Server des
  Eigentümers.

### Sanity

- **Was es ist:** eine gehostete Content-Plattform („Content Lake") mit einer Redaktionsanwendung, Sanity Studio.
- **Lizenz und Modell:** Sanity Studio ist quelloffen (MIT); der Content Lake, der die Inhalte speichert, ist ein
  gehosteter Dienst mit kostenlosem Plan (zum Beispiel bis zu 20 Benutzerplätze und zwei öffentliche Datasets) und
  bezahlten Plänen.
- **Kann gut:** eine stark anpassbare Redaktionsanwendung und Zusammenarbeit in Echtzeit.
- **Wo es versagt:** der Inhaltsspeicher ist nicht quelloffen und läuft nur in Sanitys Cloud; Wachstum führt in
  bezahlte Pläne.
- **Exponential stattdessen:** Redaktionsoberfläche und Inhaltsspeicher sind freie Software auf dem Server des
  Eigentümers.

### Storyblok

- **Was es ist:** ein gehostetes Headless-CMS mit visuellem Editor.
- **Lizenz und Modell:** proprietäre SaaS; ein kostenloser Plan, dann bezahlte Pläne nach Funktionsumfang.
- **Kann gut:** visuelles Bearbeiten komponentenbasierter Seiten.
- **Wo es versagt:** Inhalte und Editor liegen in der Cloud des Anbieters; Funktionen werden je nach Plan
  freigeschaltet.
- **Exponential stattdessen:** visueller Seitenaufbau mit Exponential Layouts und Bearbeitung direkt auf der Site
  ([Distributionen von Exponential](../DISTRIBUTIONS.md)).

### Kontent.ai

- **Was es ist:** ein gehostetes Headless-CMS, früher Kentico Kontent.
- **Lizenz und Modell:** proprietäre SaaS; eine befristete kostenlose Testphase, dann bezahlte Pläne.
- **Kann gut:** Steuerung und Workflows für Redaktionsteams.
- **Wo es versagt:** kein dauerhaft kostenloser Produktivbetrieb; kein eigenes Hosting.
- **Exponential stattdessen:** Workflows, Zustände und Freigaben im Produkt, kostenlos.

### Hygraph

- **Was es ist:** eine gehostete GraphQL-Content-Plattform, früher GraphCMS.
- **Lizenz und Modell:** proprietäre SaaS; ein kostenloser Plan, bezahlte Pläne mit Grenzen bei API-Aufrufen und
  anderer Nutzung.
- **Kann gut:** Zusammenführen von Inhalten aus mehreren Quellen per GraphQL.
- **Wo es versagt:** gemessene Nutzung und Hosting beim Anbieter.
- **Exponential stattdessen:** eine GraphQL-API auf der eigenen Installation ([GraphQL](../funktionen/6.0/graphql.md)).

### Prismic

- **Was es ist:** ein gehostetes Headless-CMS mit „Slices" für den Seitenaufbau.
- **Lizenz und Modell:** proprietäre SaaS; ein kostenloser Plan für persönliche Sites und Machbarkeitsstudien, bezahlte
  Pläne pro Repository.
- **Kann gut:** einfaches Bearbeiten von Marketingseiten.
- **Wo es versagt:** Inhalte in der Cloud des Anbieters; Team- und Nutzungsbedarf führt in bezahlte Pläne.
- **Exponential stattdessen:** unbegrenzt viele Redakteure und Repositories ohne Lizenzkosten.

## 3. Website-Baukästen und gehostete Shops

### Squarespace

- **Was es ist:** ein gehosteter Website-Baukasten.
- **Lizenz und Modell:** proprietäre SaaS, Abonnements.
- **Kann gut:** ausgefeilte Vorlagen und ein leichter Einstieg.
- **Wo es versagt:** Squarespaces eigene Hilfe sagt, dass sich nur bestimmte Inhalte exportieren lassen (in eine
  WordPress-XML-Datei) und dass viele Funktionen an der Plattform hängen, sodass manche Inhalte nicht exportiert werden.
- **Exponential stattdessen:** die ganze Site, Inhalte, Struktur und Dateien, gehört dem Eigentümer, auf seinem Server.

### Wix

- **Was es ist:** ein gehosteter Website-Baukasten.
- **Lizenz und Modell:** proprietäre SaaS, kostenloser Plan mit Wix-Werbung und bezahlte Pläne.
- **Kann gut:** Seitengestaltung per Drag-and-drop für Einsteiger.
- **Wo es versagt:** Wix' eigene Hilfe sagt, dass eine Wix-Site auf den Servern von Wix gehostet und betrieben werden
  muss, um zu funktionieren; nur die Domain lässt sich mitnehmen.
- **Exponential stattdessen:** Hosting überall und auf Wunsch eine statische Kopie für jeden statischen Host
  ([Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md)).

### Webflow

- **Was es ist:** ein gehostetes Werkzeug für visuelles Webdesign mit CMS.
- **Lizenz und Modell:** proprietäre SaaS; ein kostenloser Einstiegsplan und bezahlte Site- und Workspace-Pläne.
- **Kann gut:** präzise visuelle Gestaltung für Designerinnen und Designer.
- **Wo es versagt:** das CMS läuft nur auf Webflows Hosting; mehr Einträge, Seiten oder Besucher führen Sites in höhere
  Pläne.
- **Exponential stattdessen:** ein CMS ohne Grenzen bei Einträgen oder Besuchern auf dem Hosting des Eigentümers.

### Shopify

- **Was es ist:** eine gehostete E-Commerce-Plattform.
- **Lizenz und Modell:** proprietäre SaaS; monatliche Pläne, und laut Shopifys Preisseite können
  Transaktionsgebühren Dritter anfallen, wenn ein anderer Zahlungsanbieter als Shopify Payments genutzt wird.
- **Kann gut:** ein schneller, zuverlässiger Start für Onlineshops.
- **Wo es versagt:** wiederkehrende Plangebühren, App-Abonnements und Transaktionsgebühren wachsen mit dem Geschäft;
  der Shop läuft nur auf Shopify.
- **Exponential stattdessen:** ein Shop im Produkt (Produkte, Warenkorb, Kasse und Bestellungen) ohne Gebühr pro
  Transaktion durch Exponential ([E-Commerce-FAQ](../hilfe/e-commerce/faq-zu-e-commerce.md)).

### HubSpot CMS

- **Was es ist:** HubSpots Website-Produkt (Content Hub), verbunden mit seinem CRM und seinen Marketingwerkzeugen.
- **Lizenz und Modell:** proprietäre SaaS; kostenlose Werkzeuge, dann gestaffelte bezahlte Pläne.
- **Kann gut:** Websites, die direkt mit CRM und Marketingautomatisierung verbunden sind.
- **Wo es versagt:** wichtige Funktionen liegen in höheren Stufen; die Site läuft nur auf HubSpot.
- **Exponential stattdessen:** ein CMS, das sich über offene APIs mit einem CRM verbindet, ohne Stufensystem.

### Duda

- **Was es ist:** ein gehosteter Website-Baukasten, vor allem für Agenturen und SaaS-Unternehmen.
- **Lizenz und Modell:** proprietäre SaaS; Abonnements nach einer kostenlosen Testphase.
- **Kann gut:** viele Kundensites aus einem Agenturkonto verwalten.
- **Wo es versagt:** die Sites bleiben auf Dudas Plattform; die Kosten wachsen mit der Zahl der Sites.
- **Exponential stattdessen:** viele Sites aus einer Installation ohne zusätzliche Lizenzkosten
  ([Website-spezifische Einstellungen in Extensions](../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md)).

## 4. Was die ganze Gruppe gemeinsam hat

- **Der Eigentümer mietet, statt zu besitzen.** Der Code ist keine freie Software, und bei SaaS liegen die Inhalte auf
  den Servern des Anbieters.
- **Der Preis folgt der Nutzung und den Entscheidungen des Anbieters.** Benutzerplätze, Datensätze, API-Aufrufe, Sites
  oder Transaktionen bestimmen die Rechnung, und Pläne ändern sich.
- **Der Ausstieg kostet.** Der Export ist unvollständig oder verlangt einen Neubau des Frontends.
- **Exponentials Antwort** ist in jedem Punkt das Gegenteil: freie Software unter der GPL, keine Messung, die eigenen
  Server des Eigentümers und ein dokumentierter Ausstieg ([Exponential verlassen](../installation/migration/exponential-verlassen.md)).
  Den Weg hinein beschreibt [Zu Exponential wechseln](../hilfe/unternehmen/auswahl/zu-exponential-wechseln.md).

## Quellen

Geprüft im November 2026.

- Adobe, [AEM as a Cloud Service: Einführung](https://experienceleague.adobe.com/en/docs/experience-manager-cloud-service/content/overview/introduction);
  [Adobe Experience Manager](https://business.adobe.com/products/experience-manager/adobe-experience-manager.html);
  [ColdFusion-Produktfamilie](https://www.adobe.com/products/coldfusion-family.html).
- Sitecore, [Produkte](https://www.sitecore.com/products).
- Optimizely, [Content Management](https://www.optimizely.com/products/content-management/).
- Acquia, [Produkte](https://www.acquia.com/products).
- Bloomreach, [Bloomreach Content](https://www.bloomreach.com/en/products/content).
- Ibexa, [Produkte](https://www.ibexa.co/products); die Lizenz von
  [ibexa/oss](https://github.com/ibexa/oss/blob/main/LICENSE) (Ibexa Business Use License und GNU GPL Version 2).
- Salesforce, [Experience Cloud](https://www.salesforce.com/products/experience-cloud/overview/).
- Microsoft, [SharePoint](https://www.microsoft.com/en-us/microsoft-365/sharepoint/collaboration).
- Contentful, [Preise](https://www.contentful.com/pricing/); Contentstack, [Preise](https://www.contentstack.com/pricing);
  Sanity, [Preise](https://www.sanity.io/pricing) und [die Lizenz von Sanity Studio (MIT)](https://github.com/sanity-io/sanity);
  Storyblok, [Preise](https://www.storyblok.com/pricing); Kontent.ai, [Preise](https://kontent.ai/pricing/);
  Hygraph, [Preise](https://hygraph.com/pricing); Prismic, [Preise](https://prismic.io/pricing).
- Squarespace, [Preise](https://www.squarespace.com/pricing) und
  [Inhalte exportieren](https://support.squarespace.com/hc/en-us/articles/206566687-Exporting-your-site).
- Wix, [Pläne](https://www.wix.com/upgrade/website) und
  [Wix-Site anderswo exportieren oder einbetten](https://support.wix.com/en/article/exporting-or-embedding-your-wix-site-elsewhere).
- Webflow, [Preise](https://webflow.com/pricing); Shopify, [Preise](https://www.shopify.com/pricing);
  HubSpot, [CMS-Preise](https://www.hubspot.com/pricing/cms); Duda, [Preise](https://www.duda.co/pricing).
