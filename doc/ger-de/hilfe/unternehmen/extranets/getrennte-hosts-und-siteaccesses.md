---
title: Getrennte Hosts und Siteaccesses
audience: businesses, admin, hosting
category: business-solutions
order: 50
summary: Wie ein Partnerportal in einer Exponential-Installation eine eigene Adresse neben der öffentlichen Website bekommt oder in einer eigenen Installation läuft - Siteaccesses, Host-Zuordnung, Anmeldung nur im Portal, eigenes Design, HTTPS und die Abwägungen jeder Form.
keywords: Siteaccess, eigener Host, Hostname für Partner, Host-Zuordnung, HostMatchMapItems, HostMatchMethod, RequireUserLogin, user/login Einschränkung SiteAccess, RootNode, SiteDesign, Multisite, eigene Installation, Adresse des Extranets, HTTPS, siteaccess, separate host, partners host name, host matching, separate installation, extranet address
type: help
---
# Getrennte Hosts und Siteaccesses

Diese Seite ist für Führungskräfte und Administratoren, die entscheiden, wo das Partnerportal liegt: unter einer
eigenen Adresse wie `partners.example.com`, neben der öffentlichen Website in derselben Installation oder in einer
eigenen Installation. Sie erklärt das Konzept von Exponential, das das möglich macht, den Siteaccess, und die
Abwägungen.

## Inhalt

1. [Der Siteaccess in einem Absatz](#der-siteaccess-in-einem-absatz)
2. [Drei Formen](#drei-formen)
3. [Ein Portal neben der öffentlichen Website](#ein-portal-neben-der-öffentlichen-website)
4. [Anmeldung nur im Portal](#anmeldung-nur-im-portal)
5. [Ein eigenes Aussehen](#ein-eigenes-aussehen)
6. [Ein eigener Hostname und HTTPS](#ein-eigener-hostname-und-https)
7. [Test- und Staging-Hosts](#test--und-staging-hosts)
8. [Abwägungen](#abwägungen)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Der Siteaccess in einem Absatz

Ein **Siteaccess** ist ein Zugang zu einer Installation: ein Name, seine Einstellungen in
`settings/siteaccess/<name>/` und eine Regel, auf welche Anfragen er antwortet, zum Beispiel nach Hostname
([10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
Die öffentliche Website, die Administration und ein Partnerportal können drei Siteaccesses einer Installation sein,
jeder mit eigener Adresse, eigenem Design, eigener Startseite und eigenen Anmelderegeln.

## Drei Formen

| Form | Was sie bedeutet | Passt, wenn |
|---|---|---|
| Portal-Siteaccess in derselben Installation | eine Installation, ein Siteaccess `partners` unter eigenem Host | Inhalte teilweise mit der öffentlichen Website geteilt werden; ein Team betreibt beides |
| Portalordner der öffentlichen Website | ein Mitgliederbereich unter der Adresse der öffentlichen Website | kleine Portale, wenig Trennung nötig ([Registrierung von Mitgliedern](../mitglieder/registrierung-von-mitgliedern.md)) |
| Eigene Installation | eigener Code, eigene Datenbank und eigene Benutzer | Verträge eine strenge Trennung verlangen oder ein anderer Aktualisierungsrhythmus gilt |

## Ein Portal neben der öffentlichen Website

Die Schritte für eine zweite Website stehen unter
[Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md#step-by-step-a-second-site).
Für ein Portal laufen sie darauf hinaus:

1. Ein Ordner *Partnerportal* im Inhaltsbaum; sein Knoten wird zur Wurzel des Portals (`content.ini [NodeSettings]
   RootNode`) und zur Startseite (`IndexPage`).
2. Ein Siteaccess `partners` mit `SiteURL=partners.example.com`.
3. Host-Zuordnung in `settings/override/site.ini.append.php`: `MatchOrder=host;uri`, `HostMatchType=map`,
   `HostMatchMapItems[]=partners.example.com;partners`.
4. Der Hostname im DNS, im Webserver und im Zertifikat.

Gemeinsame Einträge wie Produktneuigkeiten können auf beiden Websites erscheinen: Ein Objekt kann Orte in beiden Bäumen
haben oder von einer Seite im Portal aus verknüpft sein
([Relationen und Wiederverwendung](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)).

## Anmeldung nur im Portal

Zwei Einstellungen machen das Portal privat, während die öffentliche Website offen bleibt:

- Im Siteaccess des Portals `[SiteAccessSettings] RequireUserLogin=true`: Jeder Besucher, der nicht angemeldet ist,
  kommt auf die Anmeldeseite; `AnonymousAccessList[]` hält Registrierung, Aktivierung und Zurücksetzen des Passworts
  erreichbar ([Was Besucher sehen dürfen, Siteaccess settings](../../../../eng-us/help/creators/website-creators/visitor-permissions.md#siteaccess-settings)).
  Der öffentliche Siteaccess setzt `false`.
- Die Richtlinie `user/login` lässt sich nach **SiteAccess** einschränken (`kernel/user/module.php`). Geben Sie
  Partnerrollen `user/login` für `partners`, Mitarbeiterrollen für die Administration und Mitgliedern der öffentlichen
  Website nur den öffentlichen Siteaccess. Eine Rollenseite liest sich dann zum Beispiel *May log in, on the
  siteaccesses partners* ([Rollen und Richtlinien, 2](../../../anleitungen/rollen-und-richtlinien.md#2-eine-rolle-lesen)).

> **Warnung**
> Inhalte des Portals müssen in Sektionen liegen, die die öffentliche Rolle Anonymous nicht liest. Die Standardrolle
> Anonymous liest nur die Sektion Standard ([Sektionen, 2](../../../anleitungen/sektionen.md#2-die-sektionen-einer-neuen-installation));
> lassen Sie das so und testen Sie abgemeldet auf dem öffentlichen Host.

## Ein eigenes Aussehen

Jeder Siteaccess hat sein eigenes `SiteDesign` und kann auf andere Designs zurückgreifen
([Designs und die Fallback-Kette](../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md)). Das Portal kann
wie die Marke aussehen, aber schlichter, mit einem Menü der Partnerbereiche und einem Link zum Abmelden. Templates
können sich auch nach Sektion unterscheiden, zum Beispiel ein anderes Aussehen für die Gold-Stufe
([Sektionen, 11.1](../../../anleitungen/sektionen.md#111-ein-template-für-eine-sektion-überschreiben)).

## Ein eigener Hostname und HTTPS

Der Hostname des Portals kommt in den DNS, in den Webserver oder Velocity und in das Zertifikat; `SiteURL` muss die
Adresse sein, die Menschen verwenden, denn Links in Mails werden daraus gebaut
([Live gehen: Hosts, HTTPS und Siteaccesses](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)).
Registrierungsmails zeigen in die Administration; wenn deren Adresse aus der Mail nicht erreichbar ist, setzen Sie
`RegistrationAdminBaseURL`
([Registrierung und Kontofreigabe](registrierung-und-kontofreigabe.md#die-mail-an-den-administrator-mit-signierten-links)).

Die Administration selbst kann einen eigenen Hostnamen und Netzwerkbeschränkungen haben
([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md#ein-eigener-hostname)).

## Test- und Staging-Hosts

`site.ini [SiteAccessSettings] HostMatchMethod` bestimmt, wie die Host-Zuordnung Hosts vergleicht: `strict`
(Standard), `start`, `end` oder `part`. Mit `start` erreicht `partners.example.com.test.local` den Siteaccess des
Portals ohne eigene Einträge. `start` und `part` akzeptieren auch Hosts, die Sie nicht gemeint haben; verwenden Sie sie
also nur dort, wo der Webserver für Ihre eigenen Hosts antwortet
([Siteaccess über das Host-Präfix](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md#the-host-map-by-the-start-of-a-host)).

## Abwägungen

| Dieselbe Installation | Eigene Installation |
|---|---|
| ein Kernel und ein Satz Erweiterungen zu aktualisieren und abzusichern | zwei zu aktualisieren und abzusichern |
| gemeinsame Inhalte, Medien und Suche | Inhalte kopiert oder synchronisiert |
| ein Benutzerstamm: Mitarbeiterkonten funktionieren in beiden | getrennte Benutzer: nichts dringt hinüber |
| ein Backup stellt beide wieder her | unabhängige Wiederherstellungen |
| ein Fehler in den Rollen kann Inhalte offenlegen | Trennung durch die Bauweise |

Dieselbe Tabelle für Marken steht unter
[Mehrere Websites einrichten, Trade-offs](../../../../eng-us/help/creators/website-creators/multisite-setup.md#trade-offs).
Logs und Caches können pro Website geführt werden
([Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)).

## Checkliste

- [ ] Form gewählt und in den Plan geschrieben.
- [ ] Siteaccess des Portals mit `RequireUserLogin=true`; öffentlicher Siteaccess mit `false`.
- [ ] `user/login` in jeder Rolle nach Siteaccess eingeschränkt.
- [ ] Sektionen des Portals nicht in der Rolle Anonymous; abgemeldet auf dem öffentlichen Host getestet.
- [ ] Hostname im DNS, im Webserver und im Zertifikat; `SiteURL` richtig.
- [ ] Links in den Registrierungsmails getestet.

## Verwandte Seiten

- [Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md)
- [Siteaccess über das Host-Präfix und die Weiterleitung zur Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)
- [Eine Installation, viele Websites](../../../../eng-us/help/businesses/multisite/one-installation-many-sites.md)
- [Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
