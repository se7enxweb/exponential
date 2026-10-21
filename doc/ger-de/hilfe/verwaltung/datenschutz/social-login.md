---
title: Social Login und Datenschutz
audience: government, project-manager, admin
category: public-sector
order: 250
summary: Für öffentliche Stellen, die Schaltflächen „Anmelden mit“ erwägen - die Social-Login-Handler der Erweiterung sevenx_authentication_2fa (Gerüste für Google, Facebook, Twitter/X, Instagram, Meta und ID.me), welche Daten zum und vom Anbieter fließen, und die Fragen, die vor dem Angebot zu klären sind.
keywords: Social Login, OAuth, Anmelden mit Google, Identitätsanbieter, sevenx_authentication_2fa, user2fa/oauth, Datenschutz, Drittanbieter, verifizierte Adressen, staatliche Identität, eID, öffentlicher Sektor, social login, sign in with Google, identity provider, privacy, third party, government identity
type: help
---
# Social Login und Datenschutz

Diese Seite richtet sich an Projektleitungen, Administratoren und Datenschutzbeauftragte. Die Erweiterung
`sevenx_authentication_2fa` enthält Social-Login-Handler, beschrieben als „Gerüste, bereit zum Anpassen“, für Google,
Facebook, Twitter/X, Instagram, Meta und ID.me, eine Basisklasse und einen Kommandozeilen-Generator für weitere Anbieter
([sevenx_authentication_2fa](../../../funktionen/6.0/erweiterungen/sevenx_authentication_2fa.md)). Schaltflächen wie
„Continue with Google“ erscheinen auf der Anmeldeseite für jeden in `sevenxauthentication2fa.ini` aktivierten Anbieter
([Zweistufige Anmeldung: Social Login](../../../anleitungen/zwei-faktor-anmeldung.md#social-login)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was geschieht](#was-geschieht)
2. [Zu klärende Fragen](#zu-klärende-fragen)
3. [Verwandte Seiten](#verwandte-seiten)

## Was geschieht

- Der Besucher wählt einen Anbieter und meldet sich dort an; der Anbieter sendet eine Identität zurück, die die Website
  einem Konto zuordnet.
- Die Erweiterung prüft einen an den Anbieter gebundenen Einmal-Zustand und lehnt Adressen ab, die der Anbieter nicht
  verifiziert hat.
- Ein auf diese Weise gefundener Benutzer muss weiterhin seinen eigenen zweiten Schritt bestehen, wenn die zweistufige
  Anmeldung eingeschaltet ist.
- Der Anbieter erfährt, dass die Person Ihre Website nutzt; die Website erhält, was der Anbieter weitergibt.

Die Datenschutzaussage des Projekts führt Social Login unter den Diensten von Drittanbietern: „Der Benutzer wählt ihn;
in der Datenschutzerklärung erwähnen“
([Datenschutzstandards](../../../standards/datenschutz.md#drittanbieterdienste-in-mitgelieferten-extensions)).

## Zu klärende Fragen

1. **Wer braucht es?** Beschäftigte sollten sich mit Konten der Organisation und zweistufiger Anmeldung anmelden, nicht
   mit Konten sozialer Netzwerke. Für Bürgerkonten bevorzugt eine öffentliche Stelle womöglich den staatlichen
   Identitätsdienst ihres Landes, wo es einen gibt; die Basisklasse der Handler und der Generator sind zum Hinzufügen
   von Anbietern da, was Entwicklungsarbeit ist.
2. **Welche Daten fordern Sie an?** So wenige, wie die Scopes es zulassen (Identität und verifizierte E-Mail).
3. **Gibt es eine Alternative?** Menschen ohne Konto beim Anbieter müssen den Dienst nutzen können.
4. **Datenschutzerklärung**: Nennen Sie jeden Anbieter, die ausgetauschten Daten und den Zweck.
5. **Konten**: Was mit dem lokalen Konto geschieht, wenn das Konto beim Anbieter wegfällt.

## Verwandte Seiten

- [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)
- [Erweiterungen von Drittanbietern und Datenschutz](erweiterungen-von-drittanbietern.md)
- [Zweistufige Anmeldung für Beschäftigte im öffentlichen Dienst](../sicherheit/zweistufige-anmeldung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
