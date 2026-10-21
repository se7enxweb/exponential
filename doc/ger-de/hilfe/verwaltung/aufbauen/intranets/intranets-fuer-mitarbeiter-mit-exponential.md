---
title: Intranets für Mitarbeiter mit Exponential
audience: government, project-manager, admin
category: public-sector
order: 20
summary: Was eine Behörde für ein Intranet für Mitarbeiter braucht und wie Exponential es bereitstellt - ein Siteaccess, der eine Anmeldung verlangt, Sektionen und Rollen für interne Inhalte, Anmeldeoptionen (lokale Konten, LDAP-Handler, zweistufige Anmeldung), Sitzungen und die einzuplanenden Grenzen.
keywords: Intranet, Mitarbeiter-Site, interne Site, Sektionen, Rollen, LDAP, zweistufige Anmeldung, Sitzungen, interne Kommunikation, intranet, staff site, internal site, RequireUserLogin, sections, roles, two-step sign-in, sessions, internal communication
type: help
---
# Intranets für Mitarbeiter mit Exponential

Diese Seite richtet sich an Führungskräfte und Administratoren, die ein Intranet für Mitarbeiter planen. Ein Intranet
in Exponential ist meist ein Siteaccess, dessen Besucher sich anmelden müssen und der Inhalte in Sektionen zeigt, die
nur Mitarbeiter lesen dürfen
([Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)).

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Dieselbe Installation wie die öffentliche Site oder eine eigene](#dieselbe-installation-wie-die-öffentliche-site-oder-eine-eigene)
3. [Einzuplanende Grenzen](#einzuplanende-grenzen)

## Die Bausteine

| Bedarf | Exponential | Seite |
|---|---|---|
| Nur Mitarbeiter können es öffnen | Siteaccess mit `RequireUserLogin=true` | „Ein Siteaccess nur für Mitarbeiter“ |
| Interne Inhalte bleiben der öffentlichen Site fern | Sektionen, die die Rolle Anonymous nicht lesen darf | „Sektionen und Rollen für Intranet-Inhalte“ |
| Mitarbeiter melden sich mit ihrem Verzeichniskonto an | der LDAP-Login-Handler des Kernels | „Anmeldung über LDAP“ |
| Stärkere Anmeldung | zweistufige Anmeldung (`sevenx_authentication_2fa`) | „Zweistufige Anmeldung für Mitarbeiter“ |
| Sitzungen, die enden | Zeitlimits in `site.ini [Session]` | „Sitzungen, Abmeldung und gemeinsam genutzte Computer“ |
| Wer was getan hat | das Audit-Protokoll | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |

## Dieselbe Installation wie die öffentliche Site oder eine eigene

| Dieselbe Installation | Eigene Installation |
|---|---|
| Inhalte können in einem Schritt mit der öffentlichen Site geteilt werden | interne Daten nie in der Datenbank der öffentlichen Site |
| ein Satz Updates | das Intranet kann ausschließlich im internen Netz stehen |
| ein Fehler in einer Rolle kann interne Inhalte offenlegen | mehr zu betreiben |

Behörden mit sensiblen internen Inhalten wählen oft eine eigene Installation im internen Netz; andere verwenden eine
Installation mit strikten Sektionen. Entscheiden Sie mit Ihrem Sicherheitsbeauftragten.

## Einzuplanende Grenzen

- Kein eingebautes Single Sign-on mit einem Identitätsanbieter über die Login-Handler hinaus, die der Kernel und
  installierte Erweiterungen bieten (standard, LDAP, Textdatei und die Handler der 2FA-Erweiterung); prüfen Sie Ihre
  Anforderung dagegen.
- Kein eingebautes Mitarbeiterverzeichnis, kein Chat und kein gemeinsames Bearbeiten von Dokumenten; ein mit
  Exponential gebautes Intranet veröffentlicht Inhalte.
- Der LDAP-Handler hat keine Anleitung für 6.x; testen Sie ihn mit Ihrem Verzeichnis, bevor Sie sich darauf verlassen.

## Verwandte Seiten

- [Ein Siteaccess nur für Mitarbeiter](ein-siteaccess-nur-fuer-mitarbeiter.md)
- [Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
