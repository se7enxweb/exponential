---
title: Deaktivierung und Entfernung
audience: government, admin, project-manager
category: public-sector
order: 70
summary: Die drei Wege, ein Konto in Exponential zu beenden - deaktivieren, deaktivieren und verschieben, endgültig entfernen - was jeder behält und löscht, das Löschen der E-Mail-Einstellungen und die Pseudonymisierung im Audit beim Entfernen, und wie Sie auf einen Antrag auf Löschung eines Kontos antworten.
keywords: deaktivieren, Konto deaktivieren, Deaktivieren und verschieben, Benutzer entfernen, Entfernen von Konten, Löschung, Recht auf Löschung, E-Mail-Einstellungen, Einwilligungsprotokoll, Pseudonymisierung im Audit, deactivate, disable account, Deactivate and move, DeactivateAndMove, remove user, account removal, erasure, right to erasure, mail preferences, consent log, audit pseudonymization, user/deactivate
type: help
---
# Deaktivierung und Entfernung

Diese Seite richtet sich an Administratoren und Projektleiter, die Konten beenden: Mitarbeiter, die ausscheiden,
öffentliche Benutzer, die die Löschung verlangen, Konten, die niemand nutzt. Exponential bietet drei Schritte mit
zunehmender Endgültigkeit. Quellen:
[Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md#deaktivieren-und-verschieben) und
[Standards zum Datenschutz](../../../../standards/datenschutz.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Drei Wege, ein Konto zu beenden](#drei-wege-ein-konto-zu-beenden)
2. [Deaktivieren und verschieben](#deaktivieren-und-verschieben)
3. [Ein Konto entfernen](#ein-konto-entfernen)
4. [Auf einen Antrag auf Löschung antworten](#auf-einen-antrag-auf-löschung-antworten)
5. [Mitarbeiter, die ausscheiden](#mitarbeiter-die-ausscheiden)

## Drei Wege, ein Konto zu beenden

| Weg | Wo | Umkehrbar | Behält |
|---|---|---|---|
| **Deaktivieren** | **Benutzer > Benutzerkonten prüfen**, oder das Konto | ja (**Aktivieren**) | alles; das Konto kann sich nicht anmelden |
| **Deaktivieren und verschieben** (optional) | **Benutzer > Benutzerkonten prüfen**, das Menü des Benutzers | ja (**Reaktivieren und zurückverschieben**) | alles; das Konto wird deaktiviert und in eine versteckte Gruppe ohne Rollen verschoben |
| **Entfernen** | das eigene **Entfernen** des Benutzerobjekts | nein: Benutzerobjekte kommen nie in den Papierkorb | Inhalte, die der Benutzer angelegt hat, bleiben |

## Deaktivieren und verschieben

Voreingestellt aus. Schalten Sie es mit `site.ini [UserSettings] DeactivateAndMove=enabled` ein (in
`settings/override/` oder im Siteaccess der Administration). Die erste Verwendung legt unter dem Knoten Benutzer eine
versteckte Benutzergruppe *Deaktivierte Benutzer* an, sofern `DeactivatedUserGroupNodeID` keine eigene Gruppe nennt.
Das Konto behält seine Inhalte, Versionen, Beziehungen und die dem Konto selbst zugewiesenen Rollen; es verliert die
Rollen der Gruppen, in denen es war, weil es nicht mehr in ihnen ist. Der deaktivierten Gruppe wird nie eine Rolle
zugewiesen: Die Rollenzuweisung lässt sie aus und sagt das. Die Funktion ist `user/deactivate`.

Verwenden Sie es für ausscheidende Mitarbeiter und für öffentliche Konten während einer Wartefrist vor dem Entfernen.

## Ein Konto entfernen

| Was beim Entfernen geschieht | Quelle |
|---|---|
| Das Benutzerobjekt und das Konto werden endgültig gelöscht | [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md) |
| E-Mail-Einstellungen und ausstehende Bestätigungen werden entfernt; das Einwilligungsprotokoll wird anonymisiert (`expMailPreferences::eraseForRemovedAccount()`) | [Standards zum Datenschutz](../../../../standards/datenschutz.md) |
| Audit-Einträge über den Benutzer werden für ihre Aufbewahrungsfrist behalten und pseudonymisiert | [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md) |
| Vom Benutzer gesendete Formulareinsendungen bleiben unter **Setup > Gesammelte Informationen** (sie tragen eine Benutzer-ID) | [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md) |
| Vom Benutzer angelegte Inhalte bleiben | das Inhaltsmodell |

E-Mail-Einstellungen können auf Antrag auch mit `./console exp:mail:preferences erase --yes` gelöscht werden
([E-Mail-Einstellungen für Administratoren](../../../../anleitungen/mail-einstellungen-administrator.md)).

## Auf einen Antrag auf Löschung antworten

1. Bestätigen Sie, dass der Antrag vom Kontoinhaber kommt (Anmeldung, oder eine Prüfung über einen Weg, dem Sie
   vertrauen).
2. Exportieren Sie, was Sie herausgeben müssen, wenn die Person auch ihre Daten verlangt hat: E-Mail-Einstellungen und
   Einwilligungsverlauf (`mailpreferences/export` durch die Person selbst, oder `./console exp:mail:preferences export`),
   Audit-Einträge über sie (`./console exp:audit export --subject-user=<id>`)
   ([Standards zum Datenschutz](../../../../standards/datenschutz.md)).
3. Prüfen Sie, was ihre Daten sonst noch enthält: Formulareinsendungen, Kommentare oder andere von ihr geschriebene
   Inhalte, Newsletter-Abonnements in cjw_newsletter.
4. Entfernen Sie das Konto (das **Entfernen** des Benutzerobjekts); entfernen oder anonymisieren Sie die anderen
   Elemente, wie es Ihre Richtlinie vorsieht.
5. Halten Sie fest, dass der Antrag bearbeitet wurde, ohne die Daten in den Vermerk zu kopieren.

Exponential hat keine einzelne Funktion „alle meine Daten exportieren“; die Schritte oben decken die Orte ab, an denen
es personenbezogene Daten speichert. Nach der [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) in der EU hat das
Recht auf Löschung Ausnahmen, zum Beispiel für Unterlagen, die Sie aufbewahren müssen; Ihr Datenschutzbeauftragter
entscheidet.

## Mitarbeiter, die ausscheiden

| Wann | Aktion |
|---|---|
| Am letzten Tag | **Deaktivieren** oder **Deaktivieren und verschieben**; beenden Sie ihre Sitzungen |
| Ihre Inhalte | ändern Sie den Eigentümer auf das Team ([Eigentümer ändern](../../../../anleitungen/eigentuemer-aendern.md)) |
| Ihre Freigaben | prüfen Sie **Setup > Workflow Prozesse** und die Freigabeeinstellungen auf ihren Namen |
| Später, gemäß Richtlinie | entfernen Sie das Konto |

## Verwandte Seiten

- [Bürgerkonten](README.md)
- [Kontenhygiene](kontenhygiene.md)
- [E-Mail-Einstellungen für Administratoren](../../../../anleitungen/mail-einstellungen-administrator.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
