---
title: Den Zugang beenden
audience: government, admin
category: public-sector
order: 70
summary: Wie Sie den Zugang eines Auftragnehmers oder Partners in Exponential am Vertragsende beenden - Konten deaktivieren oder deaktivieren und verschieben (optional), Ablauf von Passwörtern mit ezmbpaex als Sicherheitsnetz, was mit ihren Inhalten geschieht, und warum das Entfernen von Benutzerkonten endgültig ist.
keywords: Offboarding, Zugang beenden, Konto deaktivieren, Deaktivieren und verschieben, Ablauf von Passwörtern, Benutzer entfernen, Auftragnehmer, Vertragsende, end access, disable account, Deactivate and move, DeactivateAndMove, ezmbpaex, password expiry, remove user, contractors, contract end
type: help
---
# Den Zugang beenden

Diese Seite richtet sich an Administratoren, die den Zugang am Ende eines Vertrags schließen. Exponential hat kein
Ablaufdatum für Konten; der Zugang endet, wenn ein Administrator handelt, also verbinden Sie das mit Ihrem
Vertragsregister.

## Inhalt

1. [Deaktivieren](#deaktivieren)
2. [Deaktivieren und verschieben](#deaktivieren-und-verschieben)
3. [Ablauf von Passwörtern als Sicherheitsnetz](#ablauf-von-passwörtern-als-sicherheitsnetz)
4. [Ihre Inhalte](#ihre-inhalte)
5. [Warum das Konto nicht entfernen](#warum-das-konto-nicht-entfernen)

## Deaktivieren

**Benutzer > Benutzerkonten prüfen**, markieren Sie die Konten, **Deaktivieren**, **Bestätigen**: Sie können sich
nicht anmelden, und ihre Sitzungen enden ([Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)). Tun Sie
es am letzten Tag des Vertrags.

## Deaktivieren und verschieben

Mit `site.ini [UserSettings] DeactivateAndMove=enabled` (voreingestellt aus) deaktiviert **Deaktivieren und
verschieben** Konten und führt ihre Orte in einer versteckten Benutzergruppe ohne Rollen zusammen, wobei vermerkt wird,
wo sie waren; **Reaktivieren und zurückverschieben** macht es rückgängig. Nichts wird entfernt. So bleiben die externen
Gruppen frei von ehemaligen Auftragnehmern.

## Ablauf von Passwörtern als Sicherheitsnetz

Die Erweiterung `ezmbpaex` legt fest, wie lange ein Passwort gültig ist
(`mbpaex.ini [mbpaexSettings] DefaultPasswordLifeTime`, in Tagen, auch je Gruppe oder Benutzer möglich), und kann vor
dem Ablauf per Mail warnen; nach dem Ablauf muss der Benutzer bei der nächsten Anmeldung ein neues Passwort setzen
([ezmbpaex](../../../../funktionen/6.0/erweiterungen/ezmbpaex.md)). Das beendet den Zugang nicht von selbst (die Person
kann ein neues Passwort setzen), begrenzt aber, wie lange ein durchgesickertes Passwort nützlich bleibt.

## Ihre Inhalte

Inhalte, die Auftragnehmer angelegt haben, bleiben, mit ihrem Namen als Ersteller in den Versionen. Muss ein Objekt
den Eigentümer wechseln, erledigt das die Erweiterung `ezownerchange` für ein Objekt oder einen ganzen Teilbaum
([Eigentümer ändern](../../../../anleitungen/eigentuemer-aendern.md)).

## Warum das Konto nicht entfernen

Ein Benutzerobjekt kommt in Exponential nie in den Papierkorb: Es zu entfernen, ist endgültig. Deaktivieren oder
Deaktivieren und verschieben bewahrt den Nachweis, wer was getan hat. Entfernen Sie ein Konto nur, wenn Ihre
Aufbewahrungsregeln es verlangen, Konto für Konto, nach Ablauf der Aufbewahrung seiner Unterlagen.

## Verwandte Seiten

- [Verwaiste Konten überprüfen](verwaiste-konten-ueberpruefen.md)
- [Aktivitäten von Auftragnehmern und das Audit-Protokoll](aktivitaeten-von-auftragnehmern-und-das-audit-protokoll.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
