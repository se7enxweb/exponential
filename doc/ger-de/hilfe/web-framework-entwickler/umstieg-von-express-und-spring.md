---
title: Umstieg von Express oder Spring
audience: web-framework-developer, developer
category: web-frameworks
order: 470
summary: Ein Leitfaden für Entwickler aus Node.js (Express, NestJS) und Java (Spring, Spring Boot) - das Request-Modell von PHP im Vergleich mit einer Event-Loop und einem Servlet-Container, Routing, Middleware und Interceptors, Dependency Injection, Datenzugriff, Konfigurationsprofile, Sicherheit, Zeitplanung und Messaging sowie die Frage, wie Sie Exponential hinter ein JavaScript-Frontend stellen.
keywords: Umstieg, Express, NestJS, Node.js, Spring, Spring Boot, Java, Event-Loop, Profile, Zeitplanung, coming from, event loop, servlet, middleware, interceptors, dependency injection, profiles, Spring Security, @Scheduled, JPA, Hibernate, Web-Framework-Entwickler, web framework developer
type: help
---
# Umstieg von Express oder Spring

Diese Seite richtet sich an Entwickler, die Webanwendungen mit Express oder NestJS auf Node.js oder mit Spring und
Spring Boot auf der JVM bauen. Die größte Umstellung ist nicht die Sprache, sondern das Prozessmodell; danach haben die
meisten Konzepte eine Entsprechung, einige, auf die sich Spring-Entwickler verlassen (ein DI-Container, Annotationen),
jedoch nicht.

## Inhalt

1. [Das Prozessmodell](#das-prozessmodell)
2. [Gegenüberstellung](#gegenüberstellung)
3. [Ihr Frontend behalten](#ihr-frontend-behalten)
4. [Was Ihnen fehlen wird](#was-ihnen-fehlen-wird)

## Das Prozessmodell

| | Express, NestJS | Spring | Exponential 6 |
|---|---|---|---|
| Prozess | ein langlebiger Prozess, Event-Loop | langlebige JVM, ein Thread pro Request | PHP-FPM: ein frischer Durchlauf pro Request; Velocity: persistente Worker, jeder mit jeweils einem Request |
| Zustand zwischen Requests | im Speicher, gemeinsam | Singletons, gemeinsam | keiner unter PHP-FPM; pro Worker unter Velocity, worauf Sie sich nicht verlassen dürfen |
| Nebenläufigkeit innerhalb eines Requests | asynchrone I/O | Threads, reaktive Stacks | synchron; langsame Arbeit geht in eine Warteschlange |
| Echtzeit | WebSockets | WebSocket-Unterstützung | WebSockets und Server-Sent Events von Velocity ([Velocity-WebSockets](../../funktionen/6.0/velocity-websockets-und-ereignisse.md)) |

Unter Velocity ähneln die Regeln denen eines langlebigen Prozesses: Setzen Sie den Zustand pro Request zurück, und
registrieren Sie Listener nur einmal ([Server und Deployment](server-und-deployment.md)).

## Gegenüberstellung

| Express / NestJS | Spring | Exponential 6 | Seite |
|---|---|---|---|
| `app.get()`, Controller mit Decorators | `@RequestMapping` | Views in `module.php` | [Routing](routing-mit-modulen-und-views.md) |
| Middleware, Guards, Interceptors | Filter, `HandlerInterceptor` | Request-Regeln, Listener, Runnable-Ereignisse | [Middleware](middleware-und-request-regeln.md) |
| NestJS-Provider | Beans, `@Autowired` | kein Container; Handler-Klassen aus INI | [Dependency Injection](dependency-injection-und-dienste.md) |
| ein ORM (TypeORM, Prisma, Sequelize) | JPA, Hibernate, Spring Data | Inhalts-API; `eZPersistentObject` | [ORM](orm-und-ezpersistentobject.md) |
| `.env`, Konfigurationsmodule | `application.yml`, Profile | INI-Dateien, `EXP_ENV` | [Konfiguration](konfiguration-ini-dateien.md) |
| Passport.js, Guards | Spring Security | Login-Handler, Rollen und Richtlinien | [Authentifizierung](authentifizierung-und-sitzungen.md), [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| Template-Engines (EJS, Pug, Handlebars) | Thymeleaf | die Template-Sprache | [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md) |
| Job-Warteschlangen (BullMQ) | `@Async`, JMS, Spring Batch | `expqueue`, Inhalts-Jobs | [Warteschlangen](warteschlangen-und-hintergrundjobs.md) |
| node-cron | `@Scheduled` | Cronjob-Teile | [Geplante Aufgaben](geplante-aufgaben-und-cronjobs.md) |
| EventEmitter | `ApplicationEventPublisher` | `ezpEvent` | [Ereignisse](ereignisse-und-listener.md) |
| npm-Skripte, CLIs | `CommandLineRunner` | `bin/php/console` | [Kommandozeile](cli-und-konsolenbefehle.md) |
| Jest, Supertest | JUnit, MockMvc | PHPUnit, HTTP-Prüfungen | [Testen](testen.md) |

## Ihr Frontend behalten

Viele Teams aus diesen Ökosystemen behalten ein JavaScript- oder Java-Frontend und nutzen Exponential als
Inhalts-Backend: die REST-API für Inhalte, OAuth2 oder API-Schlüssel als Zugangsdaten, Redakteure in der
Administrationsoberfläche
([Headless und entkoppelte Frontends](headless-und-entkoppelte-frontends.md), [REST- und JSON-APIs](rest-und-json-apis.md)).

## Was Ihnen fehlen wird

- Annotationen oder Decorators für Routen, Sicherheit und Transaktionen.
- Ein DI-Container und typisierte Konfigurationseigenschaften.
- Asynchrone I/O im selben Prozess; verwenden Sie Warteschlangen für langsame Aufrufe.
- Generierte API-Dokumentation (OpenAPI) für die REST-API.

## Verwandte Seiten

- [Begriffswörterbuch](begriffswoerterbuch.md)
- [Wann Sie Exponential wählen sollten](wann-sie-exponential-waehlen-sollten.md)
- [PHP-Entwickler: hier beginnen](../php-entwickler/php-entwickler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
