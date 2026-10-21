---
title: Wie gebe ich einer externen Anwendung einen API-Schlüssel?
audience: admin, developer
category: manual
order: 560
summary: Für ein Skript, das als ein Benutzer handelt, erstellt dieser Benutzer unter Mein Konto > API-Schlüssel einen persönlichen API-Schlüssel; für eine Anwendung, die Benutzer anmeldet, registriert ein Administrator sie unter Setup > oAuth-Verwaltung > Neue Anwendung und gibt ihr die Client-Kennung und das Client-Geheimnis.
keywords: API-Schlüssel, REST API, externe Anwendung, oAuth, Client-Kennung, Client-Geheimnis, persönlicher API-Schlüssel, apikey/create, oauthadmin, API key, external application, client identifier, client secret, personal API key
context: oauthadmin/list
type: help
---
# Wie gebe ich einer externen Anwendung einen API-Schlüssel?

Es gibt zwei Arten von Zugriff. Wählen Sie danach, was die Anwendung tut:

- ein Skript oder Werkzeug, das **als ein Benutzer** veröffentlicht, bekommt einen **persönlichen API-Schlüssel**
  dieses Benutzers;
- eine Anwendung, bei der sich **viele Benutzer anmelden** (eine mobile App, ein Portal), wird als
  **REST-Anwendung** registriert und bekommt eine Client-Kennung und ein Client-Geheimnis.

## Wer das tun kann

Ein persönlicher Schlüssel: der Benutzer, mit der Richtlinie `apikey/create`. Eine REST-Anwendung: Benutzer mit Zugriff
auf die **oAuth-Verwaltung** (Administratoren).

## Schritte

1. **Persönlicher API-Schlüssel:** Melden Sie sich als der Benutzer an, für den das Skript handelt (ein eigenes Konto
   mit nur den Rechten, die es braucht). Öffnen Sie **Mein Konto** und klicken Sie auf **Meine API-Schlüssel
   verwalten**.
2. Geben Sie unter **Neuen Schlüssel erstellen** einen **Namen** ein, der sagt, wo der Schlüssel verwendet wird,
   markieren Sie die **Bereiche**, die er braucht, und wählen Sie, wie lange er **Gültig für** ist. Klicken Sie auf
   **Schlüssel erstellen**.
3. Kopieren Sie den Schlüssel mit **Schlüssel kopieren** und tragen Sie ihn in die Einstellungen der Anwendung ein. Er
   wird nur einmal angezeigt.
4. **REST-Anwendung:** Öffnen Sie **Setup** > **oAuth-Verwaltung** und klicken Sie auf **Neue Anwendung**.
5. Geben Sie den **Namen**, eine **Beschreibung**, die Benutzern angezeigt wird, und die **Endpunkt-URI** ein, zu der
   die Anwendung zurückkehrt. Speichern Sie.
6. Geben Sie den Entwicklern der Anwendung die **Client-Kennung** und das **Client-Geheimnis** (**Das Geheimnis
   zeigen**) über einen sicheren Kanal.

## Was schiefgehen kann

- **Der Schlüssel ist verloren gegangen.** Niemand kann ihn noch einmal anzeigen. Widerrufen Sie ihn (**Schlüssel
  widerrufen**) und erstellen Sie einen neuen.
- **Die Anwendung darf zu viel.** Ein Schlüssel geht nie über die Rechte seines Benutzers hinaus; verwenden Sie also ein
  Konto mit wenigen Rechten, und markieren Sie nur die benötigten Bereiche.
- **Ein Schlüssel ist nach außen gelangt.** Widerrufen Sie ihn sofort. Administratoren sehen alle Schlüssel unter
  **Setup** > **oAuth-Verwaltung** > **API-Schlüssel**, und jede Verwendung steht im Audit-Trail.

## Verwandte Seiten

- [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)
- [Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md)
- [REST API für Inhaber](../kunden/wachstum/rest-api-fuer-betreiber.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
