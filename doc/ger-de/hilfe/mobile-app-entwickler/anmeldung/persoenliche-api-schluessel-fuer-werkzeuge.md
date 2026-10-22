---
title: Persönliche API-Schlüssel für Werkzeuge
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 240
summary: Persönliche API-Schlüssel (expk_) für Entwicklungs-Builds, Tests, Kioske, Backends und interne Werkzeuge rund um eine App verwenden - einen erhalten, Geltungsbereiche, Laufzeiten, das Ratenlimit, Austausch und Widerruf, die Ereignisse im Prüfprotokoll - und warum ein Schlüssel nie in eine veröffentlichte App gehört.
keywords: API-Schlüssel, persönlicher API-Schlüssel, expk, Bearer, Geltungsbereiche, read, publish, edit, remove, apikey/create, Ratenlimit, RateLimitPerMinute, 429, Austausch, widerrufen, Prüfprotokoll, access.apikey, Entwicklung, CI, Mobile App, API key, personal API key, scopes, rate limit, rotation, revoke, audit, development, mobile app
type: help
---
# Persönliche API-Schlüssel für Werkzeuge

Diese Seite richtet sich an App-Entwickler, die Zugangsdaten für ihre eigene Arbeit rund um eine App brauchen: einen
Entwicklungs-Build, einen automatisierten Test, ein Backend, einen Inhaltsimport, einen Kiosk. Ein persönlicher
API-Schlüssel ist dafür die einfachste Form von Zugangsdaten, die Exponential bietet. Die vollständige Anleitung ist
[Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md).

## Inhalt

1. [Was ein Schlüssel ist](#was-ein-schlüssel-ist)
2. [Einen Schlüssel erhalten](#einen-schlüssel-erhalten)
3. [Geltungsbereiche](#geltungsbereiche)
4. [Grenzen](#grenzen)
5. [Austauschen und widerrufen](#austauschen-und-widerrufen)
6. [Wo Schlüssel hingehören](#wo-schlüssel-hingehören)

## Was ein Schlüssel ist

Ein Schlüssel ist eine Zeichenkette `expk_<12 characters>_<40 characters>`, die ein Benutzer für ein eigenes Programm
erzeugt. Eine Anfrage mit `Authorization: Bearer expk_...` läuft als Inhaber des Schlüssels, beschränkt auf die
Geltungsbereiche des Schlüssels und nie über die eigenen Richtlinien des Inhabers hinaus. Gespeichert wird nur ein
gesalzener HMAC des Geheimnisses, sodass niemand, nicht einmal ein Administrator, einen Schlüssel erneut anzeigen kann
([Kurz gesagt](../../../anleitungen/api-schluessel.md#kurz-gesagt)). Schlüssel werden von der REST-API angenommen, nicht von den
Remote-Diensten, die eigene Tokens haben ([expservices-Tokens in Apps](expservices-tokens-in-apps.md)).

## Einen Schlüssel erhalten

1. Sie brauchen ein Konto mit der Richtlinie `apikey/create`; in einer neuen Installation hat keine Rolle sie, also
   vergibt sie ein Administrator
   ([Schlüssel für Ihre Benutzer einschalten](../../../anleitungen/api-schluessel.md#5-schlüssel-für-ihre-benutzer-einschalten)).
2. Melden Sie sich an der Website an und öffnen Sie **Mein Konto > API-Zugang** (`/apikey/list`).
3. Benennen Sie den Schlüssel nach dem Ort, an dem er verwendet wird („Android-CI-Tests“), kreuzen Sie die
   Geltungsbereiche an und wählen Sie eine Laufzeit (standardmäßig 7, 30, 90 oder 365 Tage).
4. Kopieren Sie den Schlüssel von der nächsten Seite. Er wird einmal angezeigt, mit `Cache-Control: no-store`.

## Geltungsbereiche

| Geltungsbereich | Steht für | Erlaubt dem Schlüssel den Aufruf von |
|---|---|---|
| `read` | `content/read` | jeder `GET`- und `HEAD`-Route |
| `publish` | `content/create` | `POST /api/ezp/v2/content/node/create` |
| `edit` | `content/edit` | `POST /api/ezp/v2/content/node/<id>` (die heute mit `501` antwortet) |
| `remove` | `content/remove` | `DELETE /api/ezp/v2/content/node/delete/<id>` |

Die Zuordnung steht in `rest.ini [ApiKeySettings] Scopes[]` und `RouteScopes[]`. Ein Test, der nur liest, bekommt nur
`read`.

## Grenzen

| Einstellung | Standard | Wirkung für Ihr Werkzeug |
|---|---|---|
| `RateLimitPerMinute` | 120 | mehr Anfragen je Schlüssel und Minute antworten mit `429 rate_limited` und `Retry-After`; gezählt in APCu je PHP-FPM-Pool oder Velocity-Server |
| `MaxExpiryDays` | 365 | die längste Laufzeit |
| `MaxKeysPerUser` | 10 | noch gültige Schlüssel je Benutzer |
| `LastUsedInterval` | 60 | wie oft „zuletzt benutzt“ geschrieben wird, in Sekunden |

Ein Lasttest oder ein Massenimport muss unter dem Ratenlimit bleiben oder mit Zustimmung des Administrators die
Schlüssel mehrerer Benutzer verwenden ([Prüfprotokoll und Grenzen](../../../anleitungen/api-schluessel.md#8-audit-und-grenzen)).

## Austauschen und widerrufen

Erzeugen Sie zuerst den neuen Schlüssel, stellen Sie das Werkzeug darauf um, prüfen Sie, dass sein Feld
**Zuletzt benutzt** gefüllt wird, und widerrufen Sie dann den alten. Widerrufen Sie sofort, wenn jemand anderes einen
Schlüssel gesehen haben könnte, wenn das Werkzeug stillgelegt wird oder wenn die Maschine, auf der er lag, verloren
geht. Der Widerruf wirkt bei der nächsten Anfrage auf jedem Server. Administratoren sehen jeden Schlüssel unter
**Setup > oAuth-Verwaltung > API-Schlüssel** (`/oauthadmin/keys`), und jedes Erzeugen, Widerrufen, jede erste Nutzung
und jede Ablehnung wird im Prüfprotokoll als `access.apikey.*` festgehalten.

## Wo Schlüssel hingehören

| Ort | Schlüssel? |
|---|---|
| Die Shell eines Entwicklers, als Umgebungsvariable | ja |
| Ein Geheimnisspeicher der CI | ja |
| Der Geheimnisspeicher eines Backends | ja |
| Ein verwaltetes Kiosk-Gerät mit einem eigenen Schlüssel nur zum Lesen | ja, nach Entscheidung des Website-Inhabers |
| Eine veröffentlichte App, in Code, Ressourcen oder Konfiguration | **nein**: Jeder kann ihn herauslösen, und er handelt als eine Person |
| Eine URL | **nein**: Ein Schlüssel im Query-String wird abgelehnt, selbst wenn er gültig ist |

## Verwandte Seiten

- [Zugangsdaten auswählen](zugangsdaten-auswaehlen.md)
- [Geheimnisse auf Geräten speichern](geheimnisse-auf-geraeten-speichern.md)
- [Persönliche API-Schlüssel: Fehlerbehebung](../../../anleitungen/api-schluessel.md#12-fehlerbehebung)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
