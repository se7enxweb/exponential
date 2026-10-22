---
title: Headless und entkoppelte Frontends
audience: web-framework-developer, developer
category: web-frameworks
order: 350
summary: Exponential als Inhalts-Backend eines Frontends einsetzen, das in einem anderen Framework geschrieben ist - Next.js, Nuxt, eine Laravel- oder Django-Site, eine mobile App - was in Exponential bleibt, welche API Sie lesen, Rich Text und Bilder, Vorschau, Caching und Purging und warum viele Teams hybrid beginnen.
keywords: headless, entkoppelt, decoupled, API-first, Frontend, front end, Next.js, Nuxt, SPA, mobile App, mobile app, Static Site Generator, REST, Vorschau, preview, Bilder, images, Rich Text, rich text, renderedOutput, CDN, Purge, purge, hybrid, Web-Framework-Entwickler, web framework developer
type: help
---
# Headless und entkoppelte Frontends

Diese Seite richtet sich an Entwickler, die das Inhaltsmodell, die Bearbeitung und die Berechtigungen von Exponential
hinter einem Frontend nutzen möchten, das mit einem anderen Framework gebaut ist: einer JavaScript-Anwendung, einem
Static Site Generator, einer mobilen App oder einer Site, die in Laravel, Django oder Rails geschrieben ist. Exponential
ist nicht ausschließlich headless: Es hat eigene Templates, daher entscheiden Sie, wie viel vom Frontend Sie ersetzen
([Headless Exponential](../ersteller/anwendungsentwickler/headless-exponential.md)).

## Inhalt

1. [Was Sie behalten und was Sie ersetzen](#was-sie-behalten-und-was-sie-ersetzen)
2. [Inhalte lesen](#inhalte-lesen)
3. [Rich Text, Bilder und Links](#rich-text-bilder-und-links)
4. [Vorschau](#vorschau)
5. [Caching und Purging](#caching-und-purging)
6. [Zuerst hybrid](#zuerst-hybrid)

## Was Sie behalten und was Sie ersetzen

| Bleibt in Exponential | Wird durch Ihr Frontend ersetzt |
|---|---|
| Inhaltsklassen, Bearbeitung, Versionen, Übersetzungen, Workflows | Seiten-Templates, Menüs, das Pagelayout |
| Rollen und Richtlinien, bei jedem API-Aufruf geprüft | Routing, URL-Verarbeitung |
| die Administrationsoberfläche und der Layout-Editor für Redakteure | Rendern von Layouts |
| Suche, Medienspeicher, Bildaliase | clientseitiger Zustand und Caching |

## Inhalte lesen

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Metadata,Fields"
```

Die REST-API verlangt standardmäßig einen Benutzer (`rest.ini [Authentication] RequireAuthentication`). Verwenden Sie
für ein öffentliches Frontend einen Integrationsbenutzer mit reinem Lesezugriff und einer Rolle, die auf das beschränkt
ist, was die Site zeigt, und bewahren Sie seinen Schlüssel auf Ihrem Server auf, niemals im Browser-Code
([REST- und JSON-APIs](rest-und-json-apis.md)).

## Rich Text, Bilder und Links

- `OutputFormat=xhtml` liefert gerendertes XHTML von Rich-Text-Feldern, sodass das Frontend das interne XML nicht
  parsen muss.
- Bildfelder enthalten das Original und die Aliase; definieren Sie Aliase für die Größen Ihres Frontends in `image.ini`
  ([Bilder und Bildvarianten](../ersteller/website-ersteller/bilder-und-bildvarianten.md)).
- Links in Rich Text zeigen auf Adressen der Site; ordnen Sie sie über den URL-Alias oder die Knoten-ID den Routen Ihres
  Frontends zu.

## Vorschau

Redakteure sehen die Vorschau in der Administrationsoberfläche mit den Templates der Site. Ein Headless-Frontend
braucht eine eigene Vorschau: Lesen Sie die Entwurfsversion über einen authentifizierten Aufruf mit Redakteursrechten
und rendern Sie sie in einer geschützten Vorschau-Route. Planen Sie das frühzeitig.

## Caching und Purging

- Das REST-Framework hat einen Antwort-Cache, der in `rest.ini` konfiguriert wird.
- Cachen Sie in Ihrem Frontend oder CDN; leeren Sie beim Veröffentlichen mit einem Workflow-Ereignis oder einem
  `content/cache`-Listener, der den Purge- oder Revalidierungs-Hook Ihres Frontends aufruft
  ([Ereignisse und Listener](ereignisse-und-listener.md)).
- Holen Sie Listen seitenweise mit Begrenzungen.

## Zuerst hybrid

Viele Projekte belassen die öffentliche Site in den Templates von Exponential und ergänzen eine App oder ein
JavaScript-Widget auf Basis der API. So bleiben Vorschau, Layouts, SEO und URL-Aliase einfach, und die App erhält
dieselben Inhalte. Gehen Sie ganz auf headless, wenn das Frontend wirklich anderswo leben muss. Wenn das Ziel ein
Symfony-Frontend mit Twig auf denselben Inhalten ist, sehen Sie sich statt der REST-API die Legacy Bridge an
([Exponential und ein Framework kombinieren](exponential-und-ein-framework-kombinieren.md)).

## Verwandte Seiten

- [Headless Exponential](../ersteller/anwendungsentwickler/headless-exponential.md)
- [OAuth und API-Schlüssel](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)
- [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
