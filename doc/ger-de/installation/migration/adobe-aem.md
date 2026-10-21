---
title: Adobe Experience Manager und Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 47
summary: Inhalte zwischen Adobe Experience Manager und Exponential 6 übertragen – Content-Pakete, das JSON des Sling-GET-Servlets, die Assets HTTP API, Content Fragments über GraphQL, die Abbildung von Seiten, Komponenten, Assets und Fragmenten, Vanity-URLs und Weiterleitungen sowie das, was neu aufgebaut werden muss.
keywords: Adobe Experience Manager, AEM, Content-Paket, Package Manager, Sling, .json, infinity, Assets HTTP API, Content Fragments, GraphQL, JCR, Komponenten, DAM, Migration, content package, components, migration
type: install
---
# Adobe Experience Manager und Exponential

AEM speichert Inhalte in einem JCR-Repository: Seiten sind Bäume aus Knoten mit Komponenten, Assets liegen im DAM, und
strukturierte Inhalte können Content Fragments sein. All das lässt sich auslesen, aber die Komponentenstruktur der
Seiten ist für jedes AEM-Projekt spezifisch und braucht eine eigene Zuordnung.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Zuordnung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## 1. Was AEM exportieren kann

Laut der Dokumentation von Adobe und Apache Sling:

| Quelle | Was Sie erhalten | Hinweise |
|---|---|---|
| **Content-Pakete des Package Manager** | ein ZIP mit Repository-Inhalten in der „Vault-Serialisierung“, festgelegt durch Filter | das richtige Werkzeug für eine vollständige, eingefrorene Kopie; laut Adobes Richtlinie sollte ein Paket in etwa 60 Sekunden übertragen sein, teilen Sie große Bäume also auf |
| **Sling-Standard-GET-Servlet** | jeden Knoten als JSON (`<path>.json`, mit einem Tiefen-Selektor oder `.infinity`) | Ergebnisse begrenzt durch `json.maximumresults` (in AEM standardmäßig 1000); auf Publish-Instanzen oft gesperrt |
| **Assets HTTP API** (`/api/assets`) | Ordner, Asset-Metadaten, Renditions | liefert nur einen Teil der Metadaten; die Aufrufe zum Hochladen von Binärdaten sind im Cloud Service veraltet |
| **Content Fragments** | die AEM-GraphQL-API (Persisted Queries empfohlen); für Fragmente empfiehlt Adobe die Content Fragment Management OpenAPI statt der Assets API | |

Die Authentifizierungsdetails der Assets API wurden für diese Seite nicht geprüft. Templates, Designs und
Komponentencode sind keine Inhalte: Sie werden neu aufgebaut.

## 2. Abbildung auf Exponential

| AEM | Exponential 6 |
|---|---|
| Seite (`cq:Page` mit `jcr:content`) | Objekt im Baum; der Seitenpfad ist der Baum |
| Seiteneigenschaften (Titel, Beschreibung, Tags, Vanity-URL) | `title`, `xrowmetadata`, `eztags`, eigener URL-Alias |
| Textkomponenten (Rich-Text-HTML) | `ezxmltext` (umgewandelt) |
| Bildkomponenten (DAM-Referenzen) | Einbettungen oder Beziehungen zu Bildobjekten |
| Andere Komponenten (Teaser, Listen, Karussells) | Blöcke von Exponential Layouts oder Attribute und untergeordnete Objekte |
| Experience Fragments | gemeinsame Layouts oder eingebundene Objekte |
| Content-Fragment-Modelle und Fragmente | Klassen und Objekte |
| DAM-Assets und Metadaten | Bild-, Datei- oder Medienobjekte mit ihren Metadaten |
| Sprachkopien (`/content/site/en`, `/de`) | Übersetzungen desselben Objekts (ordnen Sie die Kopien über einen Schlüssel zu) |
| Tags (`cq:tags`) | `eztags` |

## 3. Schritt für Schritt

1. Erfassen Sie die verwendeten Templates und Komponenten pro Bereich der Website (Ressourcentypen der Komponenten und ihre Anzahl).
2. Exportieren Sie eine eingefrorene Kopie (Content-Pakete pro Bereich) oder lesen Sie über das JSON-Servlet auf einer Kopie der Autoreninstanz.
3. Schreiben Sie einen Extraktor pro Komponententyp; Bilder werden zu Referenzen auf importierte Assets.
4. Importieren Sie die Assets mit Metadaten, dann die Seiten pro Sprache, mit Remote-IDs aus dem JCR-Pfad oder aus `jcr:uuid`.
5. Bauen Sie die Seitenstrukturen als Exponential Layouts oder Templates neu auf; für Komponenten ohne Gegenstück ist eine Entscheidung nötig.
6. Weiterleitungen von Seitenpfaden, `.html`-Adressen und Vanity-URLs ([Checkliste, 4](checkliste.md#4-urls-und-weiterleitungen)).

## 4. Von Exponential zu AEM

Exportieren Sie mit xrowextract ([Migration weg von Exponential](exponential-verlassen.md)); legen Sie Seiten und
Fragmente über die eigenen APIs von AEM oder über ein aus dem Export erstelltes Content-Paket an, und Assets über den
Upload des DAM. Die Behandlung von Weiterleitungen auf der AEM-Seite (Vanity-URLs, Dispatcher-Regeln) wurde für diese
Seite nicht geprüft.

## 5. Checkliste

- [ ] Komponenten erfasst; ein Extraktor pro Komponententyp.
- [ ] Assets mit Metadaten und Renditions kopiert; Seiten pro Sprache extrahiert.
- [ ] Mit Remote-IDs importiert; Sprachkopien als Übersetzungen angehängt.
- [ ] Seitenstrukturen neu aufgebaut; Vanity-URLs und `.html`-Pfade weitergeleitet.

## Quellen

- Assets HTTP API: <https://experienceleague.adobe.com/en/docs/experience-manager-cloud-service/content/assets/admin/mac-api-assets>
- Package Manager: <https://experienceleague.adobe.com/en/docs/experience-manager-cloud-service/content/implementing/developer-tools/package-manager>
- Sling-Standard-GET-Servlets: <https://sling.apache.org/documentation/bundles/rendering-content-default-get-servlets.html>

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../README-LIZENZ.md)).
