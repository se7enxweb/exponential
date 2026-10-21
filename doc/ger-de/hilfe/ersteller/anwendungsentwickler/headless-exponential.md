---
title: Headless Exponential
audience: app-creator, developer, project-manager
category: app-creation
order: 370
summary: Exponential als Inhalts-Backend für Apps und JavaScript-Frontends nutzen - die REST-API, expservices, gerendertes XHTML, Bilder, URLs, Vorschau, Caching, Rechte und was mit den eingebauten Templates einfacher bleibt.
keywords: headless, entkoppelt, API, REST, JSON, Frontend, mobile App, Single-Page-Anwendung, Bilder, Vorschau, Caching, Anwendungsentwickler, decoupled, front end, mobile app, single page application, expservices, images, preview, caching, CORS, app creator
type: help
---
# Headless Exponential

Diese Seite richtet sich an Anwendungsentwickler, die das Inhaltsmodell, die Bearbeitung und die Rechte von
Exponential ohne seine Templates nutzen möchten: für eine mobile App, eine Single-Page-Anwendung, einen Generator für
statische Websites oder ein anderes System, das die Inhalte anzeigt.

## Inhalt

1. [Was Sie behalten und was Sie ersetzen](#was-sie-behalten-und-was-sie-ersetzen)
2. [Die API wählen](#die-api-wählen)
3. [Inhalte lesen](#inhalte-lesen)
4. [Rich Text, Bilder und Links](#rich-text-bilder-und-links)
5. [Vorschau und Entwürfe](#vorschau-und-entwürfe)
6. [Caching und Leistung](#caching-und-leistung)
7. [Hybrid: Templates und API zusammen](#hybrid-templates-und-api-zusammen)

## Was Sie behalten und was Sie ersetzen

| Bleibt | Wird durch Ihr Frontend ersetzt |
|---|---|
| Inhaltsklassen, Bearbeitung, Versionen, Übersetzungen, Workflows | Seiten-Templates, Menüs, das Pagelayout |
| Rollen und Richtlinien (bei jedem API-Aufruf geprüft) | Routing, URL-Verarbeitung |
| die Administration und der Layouts-Editor für die Redaktion | Rendern der Layouts |
| Suche, Medienspeicher, Bildvarianten | Zustand und Caching im Client |

## Die API wählen

| API | Wofür |
|---|---|
| REST v1/v2 (`ezprestapi`) | Knoten, Kinder und Felder lesen; Inhalte anlegen und entfernen; OAuth für Apps ([Die REST-API](rest-api.md)) |
| expservices | die Funktionen der Administration als Dienste, mit Discovery und Tokens ([Entfernte Dienste mit expservices](entfernte-dienste-expservices.md)) |
| ezjscore-Serverfunktionen | kleine JSON-Endpunkte, die Sie selbst schreiben ([JSON-Endpunkte mit ezjscore](json-endpunkte-mit-ezjscore.md)) |

## Inhalte lesen

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Metadata,Fields"
```

Öffentliche Inhalte können anonymen API-Benutzern nur ausgeliefert werden, wenn
`rest.ini [Authentication] RequireAuthentication` das erlaubt; standardmäßig ist ein Benutzer erforderlich. Für
öffentliche Frontends verwenden Sie einen Integrationsbenutzer mit reinen Leserechten und behalten seinen Schlüssel auf
Ihrem Server.

## Rich Text, Bilder und Links

- Jedes REST-Feld hat `type`, `identifier`, `value`, `id` und `classattribute_id`. Der `value` eines Rich-Text-Felds
  ist reiner Text: seine gerenderte Ausgabe ohne Tags.
- `OutputFormat=xhtml` ergänzt `renderedOutput`: den ganzen Knoten, von Templates gerendert, im View-Modus `rest`,
  innerhalb des Templates aus `rest.ini [<Controller>_viewContent_OutputSettings] Template` (`rest_pagelayout.tpl`).
  Das mitgelieferte `rest_pagelayout.tpl` ist nur eine Platzhalterseite; überschreiben Sie es in Ihrem Design und
  ergänzen Sie ein `node/view/rest.tpl`, um brauchbares Markup zu erhalten.
- Bilder: Der REST-`value` eines Bildfelds ist nur der Pfad der Originaldatei, relativ zum Wurzelverzeichnis der
  Website (zum Beispiel `var/<site>/storage/images/.../name.jpg`); er enthält keine Bildvarianten, keine Größe und
  keinen Alternativtext. Adressen der Bildvarianten erhalten Sie heute so:
  - über die Bilddomäne von expservices, `ezjscore/call/expimage::url::<Knoten-ID>::<Attribut>::<Variante>` für eine
    Variante (die Datei wird erzeugt, wenn sie fehlt) oder `expimage::urls::<Knoten-ID>::<Attribut>` für alle
    konfigurierten Varianten, jeweils mit `url`, `absolute_url`, Breite, Höhe und MIME-Typ
    ([Entfernte Dienste mit expservices](entfernte-dienste-expservices.md)); oder
  - indem Sie sie in Ihr `rest`-View-Template schreiben (`$node.data_map.<Attribut>.content.<Variante>.url`) und
    `renderedOutput` lesen.

  Definieren Sie Bildvarianten für die Größen Ihres Frontends in `image.ini`
  ([Bilder und Bildvarianten](../website-ersteller/bilder-und-bildvarianten.md)).
- Links im Rich Text zeigen auf Adressen der Website; bilden Sie sie per URL-Alias oder Knoten-ID auf die Routen Ihres
  Frontends ab.

## Vorschau und Entwürfe

Die Redaktion sieht die Vorschau in der Administration mit den Templates der Website. Ein Headless-Frontend braucht eine
eigene Vorschau: Lesen Sie die Entwurfsversion über einen authentifizierten Aufruf mit Redakteursrechten und rendern
Sie sie in einer geschützten Vorschau-Route. Planen Sie das früh ein; diesen Teil vergessen Teams am häufigsten.

## Caching und Leistung

- Das REST-Framework hat einen Antwort-Cache (`rest.ini`); die `rest`-Caches werden mit dem Tag content geleert.
- Cachen Sie in Ihrem Frontend oder CDN; leeren Sie beim Veröffentlichen mit einem Workflow-Ereignis oder einem
  Listener, der Ihren Purge-Hook aufruft ([Workflows und Ereignisse](workflows-und-ereignisse.md)).
- Holen Sie Listen seitenweise mit Limits.

## Hybrid: Templates und API zusammen

Viele Projekte behalten die Website in Templates und ergänzen eine App über die API. So bleiben Vorschau, Layouts und
SEO für die Website einfach, und die App erhält dieselben Inhalte. Beginnen Sie hybrid, sofern nichts dagegen spricht.

## Verwandte Seiten

- [Die REST-API](rest-api.md)
- [OAuth und API-Schlüssel](oauth-und-api-schluessel.md)
- [Standard für APIs und Protokolle](../../../standards/apis-und-protokolle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
