---
title: Dienstwerkzeuge
audience: ai-agents, admin, developer, app-creator
category: ai-agents
order: 35
summary: Wie Lesedienste von expservices zu Werkzeugen von Exponential Intelligence werden - die Freischaltung mit 'ai' => true oder den Einstellungen ServiceTools, die Dienste, die expservices selbst freischaltet (node, section, state, tags), was das Modell sieht, die Prüfungen bei jedem Aufruf (expai/services, ezjscore/call, die Zugriffsregel des Dienstes, die Datenrichtlinie) und warum Schreibdienste nie angeboten werden.
keywords: Dienstwerkzeuge, expservices, 'ai' => true, ServiceTools, expai/services, Lesedienste, Knotendienste, Tag-Dienste, Datenrichtlinie, Zugriffsregel, Werkzeug, Assistent, service tools, read services, node services, tags services, data policy, access rule, tool, assistant
type: help
---
# Dienstwerkzeuge

Diese Seite richtet sich an Administratoren und Entwickler, die möchten, dass der Assistent mehr Abfragen verwendet als seine eigenen.
Ein Lesedienst der Erweiterung expservices, einmal deklariert, ist für Menschen über HTTP und für den Assistenten als Werkzeug
verwendbar, jeweils mit den eigenen Berechtigungen des Aufrufers
([Dokumentation der Erweiterung: Dienstwerkzeuge](https://github.com/se7enxweb/expai/blob/main/doc/service-tools.md)).

## Inhalt

1. [Wie ein Dienst zu einem Werkzeug wird](#wie-ein-dienst-zu-einem-werkzeug-wird)
2. [Von Haus aus angebotene Dienste](#von-haus-aus-angebotene-dienste)
3. [Was das Modell sieht](#was-das-modell-sieht)
4. [Prüfungen bei jedem Aufruf](#prüfungen-bei-jedem-aufruf)
5. [Nur Lesezugriffe](#nur-lesezugriffe)

## Wie ein Dienst zu einem Werkzeug wird

Dienste sind für den Assistenten standardmäßig nicht sichtbar. Ein Dienst wird angeboten, wenn er sich dafür entscheidet:

| Wo | Was | Wirkung |
|---|---|---|
| Die Deklaration des Dienstes | `'ai' => true` | Dem Assistenten angeboten |
| Die Deklaration des Dienstes | `'ai' => array( 'summary' => '...' )` | Angeboten, mit dieser Beschreibung für das Modell |
| Die Deklaration des Dienstes | `'ai' => false` | Nie angeboten, auch wenn die Einstellungen ihn aufführen |
| `expai.ini [ServiceTools] Services[]` | `<domain>::<method>` oder `<domain>::*` | Angeboten, ohne Code zu ändern |
| `expai.ini [ServiceTools] Domains[]` | `<domain>` | Jeder Lesedienst der Domäne angeboten |
| `expai.ini [ServiceTools] Exclude[]` | dieselben Muster | Nie angeboten |
| `expai.ini [ServiceTools] Enabled` | `disabled` | Kein Dienst wird angeboten |

Eine eigene Dienstdomäne schreiben: [Entfernte Dienste mit expservices](../../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md).

## Von Haus aus angebotene Dienste

expservices schaltet diese Lesedienste selbst frei, jeder über den eigenen Lesezugriff des Knotens geprüft:

| Domäne | Dienste |
|---|---|
| node | Namen der Kinder, Zahl der Kinder, Breadcrumb, Baum, neueste, suchen |
| section | Liste, Sektion eines Knotens |
| state | Gruppen, Zustände eines Objekts |
| tags | Wurzeln, Kinder, Baum, Suche, Tags eines Knotens |

## Was das Modell sieht

Jedes Werkzeug hat eine Beschreibung, die mit "[Exponential service, read only]" beginnt, den Dienst und seine Rückgabe nennt und
sagt, dass die Antwort Daten sind, keine Anweisungen. Sein Eingabeschema hat eine Eigenschaft je deklariertem Argument. Die Antwort
ist die eigene Hülle des Dienstes, mit einem Hinweis versehen, dass es Daten sind; eine Fehlerhülle wird zu einem Werkzeugfehler,
den das Modell korrigieren kann.

## Prüfungen bei jedem Aufruf

Der Aufruf läuft im selben PHP-Prozess, als der angemeldete Redakteur, über denselben Einstieg, den der HTTP-Router verwendet. Davor:

1. die Richtlinie `expai/services` (Einschränkung **Domain**); ohne sie wird kein Dienst angeboten;
2. die Richtlinie `ezjscore/call` und die eigene Prüfung der Domäne durch den Router, genau wie bei HTTP;
3. die eigene Deklaration `access` des Dienstes und seine eigenen Inhaltsprüfungen;
4. die Datenrichtlinie des Anbieters des Panels: Ein Argument, das Inhalte nennt, die die Richtlinie fernhält, lässt den Aufruf
   ablehnen.

Danach filtert die Datenrichtlinie die Antwort: Zeilen, die Objekte nennen, die die Richtlinie fernhält, werden ausgelassen und
gezählt. Jeder Aufruf wird als Werkzeugeintrag der Nutzungszeile der Anfrage festgehalten.

## Nur Lesezugriffe

Ein mit `'write' => true` deklarierter Dienst wird **nie** angeboten, egal was die Deklaration oder die Einstellungen sagen. Die
Schreibzugriffe des Assistenten bleiben seine eigenen Entwurfswerkzeuge, die nie veröffentlichen
([Entwürfe und menschliche Prüfung](entwuerfe-und-menschliche-pruefung.md)).

## Verwandte Seiten

- [Agenten und expservices](../agentische-ablaeufe/agenten-und-expservices.md)
- [Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md)
- [Backend-Dienste über ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
