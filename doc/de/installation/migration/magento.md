---
title: Magento, Adobe Commerce und Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 43
summary: Inhalte zwischen Magento Open Source oder Adobe Commerce und Exponential 6 umziehen – der CSV-Export von Data Transfer, REST und GraphQL, was nicht im Export enthalten ist (CMS-Seiten, Blöcke, URL-Rewrites), die Abbildung von Katalog- und CMS-Inhalten, URL-Rewrites und Weiterleitungen und wann Sie den Shop behalten und nur die Inhalte umziehen.
keywords: Magento, Adobe Commerce, Data Transfer, Export, Import, CSV, REST, GraphQL, cmsPage, URL-Rewrites, Katalog, Produkte, Kategorien, CMS-Seiten, Shop, Migration, export, import, URL rewrites, catalog, products, categories, CMS pages, shop, migration
type: install
---
# Magento, Adobe Commerce und Exponential

Magento Open Source und Adobe Commerce sind in erster Linie Shops. Die übliche Migration ist **nicht** „der Shop nach Exponential“:
Sie besteht darin, die **Inhalte** (CMS-Seiten, Blog, Ratgeber, Landingpages) nach Exponential zu bringen und den Shop zu behalten, oder
einen kleinen Katalog in den Kernel-Shop von Exponential zu übernehmen. Entscheiden Sie das, bevor Sie beginnen.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Abbildung](inhaltsmodell-zuordnen.md) · [Werkzeuge](exponential-tools.md)

## Inhalt

- [1. Was Magento exportieren kann](#1-was-magento-exportieren-kann)
- [2. Abbildung auf Exponential](#2-abbildung-auf-exponential)
- [3. Schritt für Schritt](#3-schritt-für-schritt)
- [4. Adressen und Weiterleitungen](#4-adressen-und-weiterleitungen)
- [5. Von Exponential zu Magento](#5-von-exponential-zu-magento)
- [6. Risiken und Checkliste](#6-risiken-und-checkliste)
- [Quellen](#quellen)

## 1. Was Magento exportieren kann

Laut der Dokumentation von Adobe:

| Werkzeug | Was Sie erhalten | Hinweise |
|---|---|---|
| **System > Data Transfer > Export** | CSV der Entitäten Products, Advanced Pricing, Customers Main File, Customer Addresses, Customer Finances, Stock Sources | **CMS-Seiten, CMS-Blöcke und URL-Rewrites sind keine Export-Entitäten** |
| **REST-API** | z. B. `GET /rest/<store_code>/V1/...` mit `searchCriteria[pageSize]` und `searchCriteria[currentPage]` | Authentifizierung über eine Integration (OAuth), Admin- oder Kunden-Bearer-Tokens |
| **GraphQL** | `cmsPage(identifier: ...)` liefert eine Seite (mit den Feldern `url_key`, `title`, `content`, `meta_*`) | listet keine Seiten auf; Sie brauchen die Identifier |
| **Marketing > SEO & Search > URL Rewrites** | die Liste der Rewrites (Produkt, Kategorie, CMS-Seite, benutzerdefiniert; 301 oder 302) | ein CSV-Export der Rewrites im Kern wurde nicht geprüft |
| Die Datenbank | alles, einschließlich CMS-Seiten (`cms_page`), Blöcke (`cms_block`) und Rewrites (`url_rewrite`) | die praktische Quelle für CMS-Inhalte |

## 2. Abbildung auf Exponential

| Magento | Exponential 6 |
|---|---|
| CMS-Seite | `article`-/`folder`-Objekte; HTML aus `content` in XmlText umgewandelt; Markup des Page Builders braucht eine eigene Umwandlung |
| CMS-Block | ein Layout-Block oder ein Objekt, das von Templates eingebunden wird |
| Kategorie (mit Beschreibung) | Ordner mit Beschreibung |
| Produkt (Inhaltsansicht: Name, Beschreibung, Bilder, Attribute) | eine Produktklasse (`ezprice` oder `ezmultiprice` für den Kernel-Shop) oder eine Inhaltsseite, die auf den Shop verlinkt |
| Produktbilder (`pub/media/catalog/product`) | Bildobjekte |
| `meta_title`, `meta_description` | `xrowmetadata` |
| Kunden, Bestellungen | bleiben im Shop oder in dessen Archiv; Kundendaten sind personenbezogene Daten, ziehen Sie sie nur mit einem Grund und einer Rechtsgrundlage um |
| URL-Rewrites | weiterleitende URL-Aliase oder Webserver-Regeln |

## 3. Schritt für Schritt

1. **Legen Sie den Umfang fest**: nur Inhalte (der Shop bleibt) oder Inhalte und Katalog (der Kernel-Shop von Exponential oder ein externer,
   verlinkter Shop).
2. **Exportieren Sie** CMS-Seiten und -Blöcke aus der Datenbank (oder per GraphQL je Identifier), den Katalog über Data Transfer,
   die URL-Rewrites aus `url_rewrite`, die Medien aus `pub/media`.
3. **Wandeln Sie** HTML (einschließlich der Inhalte des Page Builders) in die vereinfachte Eingabe um; Widgets und Direktiven (`{{widget ...}}`,
   `{{media url=...}}`) werden zu Einbettungen oder Links oder werden gerendert.
4. **Importieren Sie** Medien, Kategorien, Produkte und Seiten mit Remote-IDs (`magento-cms-<id>`, `magento-product-<sku>`).
5. **Verbinden Sie** Shop und Inhalte, wenn beide bleiben: Produktlinks auf Inhaltsseiten zeigen auf die Adressen des Shops.
6. **Weiterleitungen** für jeden Rewrite, der Magento verlässt; testen; proben; umstellen.

## 4. Adressen und Weiterleitungen

Die Tabelle `url_rewrite` von Magento enthält jeden öffentlichen Pfad (`request_path`) und sein Ziel (`target_path`,
`entity_type`, `redirect_type`). Legen Sie für jede Zeile, die nach Exponential umzieht, einen weiterleitenden eigenen URL-Alias auf dem
neuen Knoten an; für Zeilen, die im Shop bleiben, ändert sich nichts. GET-Parameter werden von den Rewrites von Magento entfernt
(laut der Dokumentation von Adobe), sie brauchen also keine Regel.

## 5. Von Exponential zu Magento

Exportieren Sie die Inhalte mit xrowextract ([Exponential verlassen](exponential-verlassen.md)). Produkte gehen über **System > Data Transfer > Import** als CSV hinein (Verhalten Add/Update, Replace, Delete); CMS-Seiten und -Blöcke über die REST-API oder von Hand, da
sie keine Import-Entitäten sind. Weiterleitungen werden als URL-Rewrites eingetragen (benutzerdefinierter Typ, 301).

## 6. Risiken und Checkliste

| Risiko | Gegenmaßnahme |
|---|---|
| CMS-Inhalte nicht in Data Transfer | aus der Datenbank auslesen |
| Widgets und Direktiven im HTML | auflisten und jede Art umwandeln |
| Kundendaten ohne Notwendigkeit umgezogen | im Shop belassen |

- [ ] Umfang festgelegt: Inhalte oder Inhalte und Katalog.
- [ ] CMS-Seiten, Blöcke, Katalog, Medien und URL-Rewrites exportiert.
- [ ] Widgets und Direktiven umgewandelt; Remote-IDs gesetzt.
- [ ] Weiterleitungen für jeden umgezogenen Rewrite; Links zwischen Inhalten und Shop geprüft.

## Quellen

- Datenexport: <https://experienceleague.adobe.com/en/docs/commerce-admin/systems/data-transfer/data-export>
- Authentifizierung der Web API: <https://developer.adobe.com/commerce/webapi/get-started/authentication/>
- GraphQL `cmsPage`: <https://developer.adobe.com/commerce/webapi/graphql/schema/store/queries/cms-page>

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
