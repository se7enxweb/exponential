---
title: Zugriffskontrolle im Code
audience: app-creator, developer
category: app-creation
order: 590
summary: Die Rechte von Exponential in jeder Schicht einer Anwendung anwenden - der aktuelle Benutzer, Richtlinienprüfungen mit Einschränkungen, Prüfungen auf Inhaltsebene, Hintergrundarbeit als der richtige Benutzer, Integrationsbenutzer und das Festhalten von Zugriffsentscheidungen im Audit-Trail.
keywords: Zugriffskontrolle, Rechte, Berechtigungen, Autorisierung, Einschränkungen, Hintergrundjobs, Integrationsbenutzer, Audit, Anwendungsentwickler, access control, permissions, authorization, eZUser, currentUser, hasAccessTo, limitations, canRead, canEdit, background jobs, integration user, audit, app creator
type: help
---
# Zugriffskontrolle im Code

Diese Seite ist für Anwendungsentwickler, die dafür sorgen, dass jeder Benutzer nur sieht und tut, was seine Rollen
erlauben. Der Kernel prüft die Modulfunktion, bevor eine Ansicht läuft; alles Feinere ist Aufgabe Ihres Codes
([Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)).

## Inhalt

1. [Wer ist der Benutzer?](#wer-ist-der-benutzer)
2. [Drei Ebenen von Prüfungen](#drei-ebenen-von-prüfungen)
3. [Hintergrundarbeit](#hintergrundarbeit)
4. [Integrationsbenutzer](#integrationsbenutzer)
5. [Entscheidungen festhalten](#entscheidungen-festhalten)

## Wer ist der Benutzer?

```php
$user = eZUser::currentUser();
if ( !$user->isRegistered() ) { /* anonymous */ }
$userID = eZUser::currentUserID();
```

In Web-Anfragen kommt der Benutzer aus der Sitzung oder aus einem API-Zugangsnachweis; in Skripten, Cronjobs und
Workern ist er anonym, solange der Code keinen Benutzer anmeldet.

## Drei Ebenen von Prüfungen

| Ebene | Prüfung | Beispiel |
|---|---|---|
| Funktion | die `functions` der Ansicht in `module.php` oder `hasAccessTo( 'expdemo', 'manage' )` | darf dieser Benutzer überhaupt verwalten? |
| Einschränkung | `$access['policies']` auswerten, wenn `accessWord` den Wert `limited` hat | nur in diesen Sektionen |
| Objekt | `$node->canRead()`, `$object->canEdit()`, `$object->checkAccess( 'remove' )`; eigene Besitzerprüfungen für Ihre Tabellen | dieses Element |

Prüfen Sie auf jeder zutreffenden Ebene, an jedem Einstiegspunkt. Eine Liste von Elementen muss dieselben Prüfungen
anwenden wie ein einzelnes Element: Filtern Sie die Abfrage, statt Zeilen im Template auszublenden.

## Hintergrundarbeit

- Ein Job in der Warteschlange läuft als der Benutzer, der ihn eingereiht hat (`user_id` von expqueue); prüfen Sie
  die Rechte als dieser Benutzer, so wie es eine Web-Anfrage täte ([Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)).
- Ein Cronjob, der Inhalte schreibt, meldet einen eigenen Benutzer an, dessen Rolle genau dafür angelegt ist.
- Lassen Sie Hintergrundarbeit nie "sicherheitshalber" als Administrator laufen: Das verdeckt Rechtefehler und macht
  das Audit wertlos.

## Integrationsbenutzer

Legen Sie für eine Integration (einen Import, einen API-Client) einen Benutzer an, dessen Rolle nur das enthält, was
die Integration braucht, eingeschränkt auf ihren Teilbaum oder ihre Sektion. Geben Sie ihm einen eigenen API-Schlüssel
oder OAuth-Client, damit er sich einzeln widerrufen lässt ([OAuth und API-Schlüssel](oauth-und-api-schluessel.md)).

## Entscheidungen festhalten

Schreiben Sie sicherheitsrelevante Aktionen und Ablehnungen in den Audit-Trail, mit IDs und niemals mit Geheimnissen
oder personenbezogenen Inhalten, damit Administratoren sie prüfen können
([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md), [Audit](../../../bc/6.0/audit.md)).

## Verwandte Seiten

- [Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)
- [Sicherheit für Anwendungen](sicherheit-fuer-anwendungen.md)
- [Rechteprüfungen bei vielen Zuweisungen](../../../../eng-us/features/6.0/permission-checks-for-many-assignments.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
