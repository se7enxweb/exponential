---
title: Remote-Dienste (expservices)
audience: customer, businesses, partner
category: customer-success
order: 10
summary: Was die Remote-Dienste von Exponential (die Erweiterung expservices) für einen Website-Betreiber bedeuten - die Funktionen der Administration als benannte HTTP-Dienste für Skripte, Apps und Portale, dieselben Berechtigungen wie die Person, persönliche Tokens, der Nachweis im Prüfprotokoll, der Ausschalter und was mit denen zu vereinbaren ist, die sie nutzen.
keywords: Remote-Dienste, expservices, HTTP-API, Skripte, Portale, Tokens, Automatisierung, Berechtigungen, Prüfprotokoll, ezjscore, Website-Betreiber, remote services, HTTP API, scripts, portals, tokens, automation, permissions, audit, site owner
type: help
---
# Remote-Dienste (expservices)

Diese Seite ist für Betreiber, deren Partner oder eigenes Personal Arbeit auf der Website automatisieren möchte: ein
Skript, das jede Nacht Produkte importiert, ein Dashboard, das Zahlen liest, ein Portal-Frontend, eine Desktop- oder
Smartphone-App für Redakteure. Die Erweiterung `expservices` macht aus den Funktionen der Administration über tausend
benannte Dienste, die über HTTP aufgerufen werden und mit JSON antworten ([Remote-Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)).
Sie ist Teil der Standarddistribution.

## Inhalt

1. [Was es ist, aus Sicht des Betreibers](#was-es-ist-aus-sicht-des-betreibers)
2. [Was es sicher hält](#was-es-sicher-hält)
3. [Tokens](#tokens)
4. [Was zu vereinbaren ist, bevor jemand es nutzt](#was-zu-vereinbaren-ist-bevor-jemand-es-nutzt)
5. [Es abschalten](#es-abschalten)

## Was es ist, aus Sicht des Betreibers

Ein Dienst wird über seinen Namen aufgerufen, `exp<domain>::<method>`, unter `/ezjscore/call/`
([Remote-Dienste](../../../../eng-us/features/6.0/remote-services-expservices.md)). Die Dienste umfassen:

| Gruppe | Beispiele |
|---|---|
| Kern | Sitzung, System, Einstellungen (lesen), Caches, Cronjobs, Erweiterungen, Pakete, Workflows |
| Inhalte | Knoten, Objekte, Klassen, Versionen, Übersetzungen, Beziehungen, Papierkorb, Sektionen, Zustände, URL-Aliase, Suche, Inhaltsaufträge |
| Benutzer und Zugriff | Benutzer, Gruppen, Rollen, Richtlinien, Sitzungen, Einstellungen, Benachrichtigungen |
| Handel, Community, Feeds | Produkte, Warenkorb und Kasse, Bestellungen, Mehrwertsteuer, Währungen, Rabatte, gesammelte Formulare, RSS |
| Medien und Sonstiges | Bilder, Dateien, Tags, Layouts, Prüfprotokoll (nur lesen), Newsletter, Sitemaps, Statistik |

Beispiel-Frontends für Portale, die auf den Diensten aufbauen, werden mit der Erweiterung ausgeliefert
([Remote-Dienste](../../../../eng-us/features/6.0/remote-services-expservices.md)). Um Inhalte in eine separate App zu lesen, ist
die REST-API möglicherweise die einfachere Wahl ([Die REST-API für Betreiber](rest-api-fuer-betreiber.md)).

## Was es sicher hält

Aus [Remote-Dienste](../../../../eng-us/features/6.0/remote-services-expservices.md):

- Jeder Dienst ist `public`, braucht einen angemeldeten Benutzer oder braucht eine Richtlinie; ohne Anmeldung antwortet
  er mit 401, ohne die Richtlinie mit 403. Ein Skript kann nie mehr als das Konto, das es verwendet.
- **Schreibvorgänge** brauchen POST, das Formular-Token und die Richtlinie, und jeder erfolgreiche Schreibvorgang wird
  im Prüfprotokoll als `service.<domain>.<method>` festgehalten.
- Große Vorgänge werden an Inhaltsaufträge weitergeleitet, die im Hintergrund in Stapeln laufen.
- Kein Schreibvorgang ist ohne Richtlinie offen.

## Tokens

Ein angemeldeter Benutzer kann ein persönliches Token für ein Skript erzeugen: Es wird einmal angezeigt, gespeichert
wird nur sein Hash, es gilt 1 bis 365 Tage (standardmäßig 90), und Anfragen damit laufen mit denselben Richtlinien wie
der Benutzer. Abgelaufene, widerrufene Tokens oder Tokens deaktivierter Benutzer antworten mit 401, und ein Token kann
keine Tokens erzeugen. Erzeugen, Widerrufen und fehlgeschlagene Nutzung gehen ins Prüfprotokoll
([Remote-Dienste](../../../../eng-us/features/6.0/remote-services-expservices.md)).

> **Tipp**
> Wie bei API-Schlüsseln: Geben Sie jedem Skript ein **eigenes Konto** mit einer eng gefassten Rolle und ein Token mit
> Ablaufdatum. Ein Token auf dem Administratorkonto einer Person kann alles, was diese Person kann.

## Was zu vereinbaren ist, bevor jemand es nutzt

| Frage | Ihre Antwort |
|---|---|
| Welche Aufgaben werden automatisiert? | |
| Welches Konto und welche Rolle verwendet jedes Skript? | |
| Nur lesen, oder auch schreiben? | |
| Wann läuft jedes Token ab, und wer erneuert es? | |
| Wo werden Tokens aufbewahrt? | |
| Wer sieht sich die Schreibvorgänge des Skripts im Prüfprotokoll an, und wie oft? | |
| Was geschieht, wenn sich das Skript falsch verhält (wer schaltet es ab)? | |

Kopieren Sie die Tabelle in den Plan der Integration ([Eine Integration planen](eine-integration-planen.md)).

## Es abschalten

Der Schalter für die ganze Installation ist `[Services] Enabled` in `extension/expservices/settings/expservices.ini`;
steht er auf `disabled`, antwortet jeder Dienst mit 403 ([Remote-Dienste](../../../../eng-us/features/6.0/remote-services-expservices.md)).
Ihr Administrator kann auch ein einzelnes Token widerrufen oder das Konto deaktivieren, das ein Skript verwendet
([Ausscheiden von Mitarbeitenden](../alltag/ausscheiden-von-mitarbeitenden.md)). Wissen Sie, welche dieser Möglichkeiten Sie
nutzen würden, bevor Sie sie brauchen.

## Verwandte Seiten

- [Remote-Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)
- [Remote-Dienste für Unternehmen](../../unternehmen/mobil-und-headless/entfernte-dienste.md)
- [Eine Integration planen](eine-integration-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
