---
title: Exponential und ein Framework kombinieren
audience: web-framework-developer, developer
category: web-frameworks
order: 490
summary: Muster für Projekte, die Exponential zusammen mit einem anderen Framework einsetzen - Exponential als Inhalts-Backend einer Framework-Website, ein Framework-Dienst, der aus Exponential aufgerufen wird, Exponential innerhalb von Symfony über die Legacy Bridge, gemeinsame Benutzer und Fragen zu Single Sign-on, gemeinsames Hosting unter einer Domain sowie die Regeln, die beide Seiten konsistent halten.
keywords: Integration, hybrid, kombinieren, Microservices, Legacy Bridge, REST, Headless, Composer-Bibliotheken, gemeinsame Domain, Reverse Proxy, Single Sign-on, OAuth2, Web-Framework-Entwickler, mixing, integration, shared domain, reverse proxy, web framework developer
type: help
---
# Exponential und ein Framework kombinieren

Diese Seite richtet sich an Teams, die Exponential für Inhalte und ein Framework für etwas anderes einsetzen möchten.
Vier Muster decken die meisten Projekte ab. Jedes folgt einer Regel: **Inhalte werden über die Inhalts-API von
Exponential geschrieben**, damit Versionen, Caches, Berechtigungen und Suche konsistent bleiben.

## Inhalt

1. [Muster 1: Exponential als Inhalts-Backend](#muster-1-exponential-als-inhalts-backend)
2. [Muster 2: ein Framework-Dienst, der aus Exponential aufgerufen wird](#muster-2-ein-framework-dienst-der-aus-exponential-aufgerufen-wird)
3. [Muster 3: Exponential innerhalb von Symfony](#muster-3-exponential-innerhalb-von-symfony)
4. [Muster 4: Bibliotheken innerhalb einer Erweiterung](#muster-4-bibliotheken-innerhalb-einer-erweiterung)
5. [Benutzer auf beiden Seiten](#benutzer-auf-beiden-seiten)
6. [Eine Domain, zwei Anwendungen](#eine-domain-zwei-anwendungen)
7. [Regeln](#regeln)

## Muster 1: Exponential als Inhalts-Backend

Die Framework-Anwendung (Laravel, Django, Rails, ein Node.js-Frontend) rendert die Seiten und liest Inhalte über die
REST-API von Exponential, mit dem API-Schlüssel eines Integrationsbenutzers oder einem OAuth2-Client; Redakteure
arbeiten in der Administrationsoberfläche von Exponential
([Headless und entkoppelte Frontends](headless-und-entkoppelte-frontends.md)). Leeren Sie den Cache des Frameworks beim
Veröffentlichen mit einem `content/cache`-Listener oder einem Workflow-Ereignis, das das Framework aufruft.

## Muster 2: ein Framework-Dienst, der aus Exponential aufgerufen wird

Ein Dienst, der in einem anderen Framework geschrieben ist (Abrechnung, eine Empfehlungs-Engine, ein Suchindex), wird
aus einer Exponential-Erweiterung aufgerufen: Eine Klasse kapselt die HTTP-Aufrufe, ein `expqueue`-Job führt sie im
Hintergrund aus, und ein Workflow-Ereignis oder ein Listener stellt den Job nach dem Veröffentlichen in die
Warteschlange ([Warteschlangen](warteschlangen-und-hintergrundjobs.md)). Der Dienst ruft über die REST-API oder expservices
zurück, wenn er Inhalte ändern muss.

## Muster 3: Exponential innerhalb von Symfony

Die Legacy Bridge lädt den Kernel von Exponential 6 innerhalb einer Installation der Symfony-Plattform:
Symfony-Controller und Twig für die öffentliche Website, die Legacy-Administrationsoberfläche für Redakteure, eine
Datenbank ([Die Symfony Legacy Bridge](symfony-und-die-legacy-bridge.md)). Dies ist das einzige Muster mit einem
Prozess für beide Seiten, und dasjenige mit den meisten beweglichen Teilen beim Aktualisieren.

## Muster 4: Bibliotheken innerhalb einer Erweiterung

Viele Framework-Komponenten sind gewöhnliche Composer-Bibliotheken (HTTP-Clients, Validatoren, Markdown-Parser,
Mailer). Binden Sie sie in der `composer.json` Ihrer Erweiterung ein; `autoload.php` lädt den Autoloader von Composer,
sodass Ihre Klassen sie verwenden können. Halten Sie Framework-spezifische Laufzeitbestandteile (einen Container,
einen Router, eine ORM-Verbindung) aus dem Request-Pfad des Kernels heraus.

## Benutzer auf beiden Seiten

Exponential verwaltet eigene Benutzer (Inhaltsobjekte) und Sitzungen
([Authentifizierung](authentifizierung-und-sitzungen.md)). Möglichkeiten, wenn beide Seiten dieselben Personen benötigen:

- Die Framework-Seite tritt über die REST-API als ein einzelner Integrationsbenutzer mit eigener Rolle auf.
- Die Framework-Seite lässt sich von den Benutzern über den OAuth2-Server von Exponential autorisieren
  ([OAuth und API-Schlüssel](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)).
- Exponential prüft Anmeldedaten über einen eigenen Login-Handler gegen ein anderes System
  ([Login-Handler für Benutzer](../../bc/6.0/rad-erweiterungspunkte.md#login-handler-für-benutzer)).

Planen Sie dies frühzeitig; es gibt kein fertiges Single Sign-on zwischen Exponential und einer Framework-Anwendung.

## Eine Domain, zwei Anwendungen

Ein Reverse Proxy kann Pfade an zwei Anwendungen weiterleiten (`/shop/` an ein Framework, alles andere an
Exponential). Dann gilt: Tragen Sie den Proxy in `TrustedProxies[]` ein, halten Sie die Cookie-Namen getrennt
(`[Session] SessionNamePrefix`), und stellen Sie sicher, dass die URL-Aliase von Exponential niemals die Pfade der
anderen Anwendung belegen ([Hinter einem Reverse Proxy](../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).

## Regeln

- Inhalte werden über die Inhalts-API geschrieben (`eZContentFunctions`, REST, expservices), niemals per SQL in die Inhaltstabellen.
- Jede Seite besitzt ihre eigenen Tabellen; teilen Sie Daten über APIs, nicht über Fremdschlüssel.
- Verweisen Sie von der anderen Seite aus über Remote-IDs auf Inhalte; numerische IDs unterscheiden sich zwischen Installationen.
- Testen Sie die Integration bei jedem Release einer der beiden Seiten.

## Verwandte Seiten

- [Wann Sie Exponential wählen sollten](wann-sie-exponential-waehlen-sollten.md)
- [Entfernte Dienste mit expservices](../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md)
- [Die Plattformprodukte](die-plattformprodukte.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
