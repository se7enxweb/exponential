---
title: Muster in kernel/classes/user
audience: developer
category: standards
order: 521
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/user mit den Entwurfsmustern.
keywords: kernel/classes/user, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/user

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/user` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (14)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expPasswordChangeRequired` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/exppasswordchangerequired.php:35` | „Passwort bei der nächsten Anmeldung ändern“: Ein Administrator markiert ein Konto, und nach dessen nächster Anmeldung wird der Benutzer zum Formular für die Passwortänderung (user/p… |
| `expRegistrationAdminAction` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expregistrationadminaction.php:23` | Die signierten Links der Administrator-Mail „Neuer Benutzer registriert“ (design:user/registrationfeedback.tpl) und die Aktionen dahinter (user/registrat… |
| `expSignInSecondFactor` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expsigninsecondfactor.php:37` | Der zweite Schritt der Anmeldung auf den Wegen, die nicht über das Web-Anmeldeformular laufen: die REST-Schnittstelle mit Passwort (ezpRestBasicAuthStyle), Web… |
| `expSignInSecondFactorHandler` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/user/expsigninsecondfactorhandler.php:18` | Was eine Erweiterung für die zweistufige Anmeldung dem Kernel über Anmeldungen außerhalb des Web-Anmeldeformulars mitteilt (REST mit Passwort, WebDAV, SOAP, die expservices-Anmeld… |
| `expUnactivatedUsers` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expunactivatedusers.php:22` | Die Benutzer, die sich registriert und ihr Konto nie aktiviert haben, für die Seite user/unactivated: wie viele es sind, eine Seite davon, gesucht nach Login, E-Ma… |
| `expUserAccountOverview` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/user/expuseraccountoverview.php:22` | Die Kontoseite eines Benutzers (user/edit, user/edit/), für einen Menschen beschrieben: wer das Konto ist (Name, Login, E-Mail, Gruppen, Rollen), sein Zustand (aktiv… |
| `expUserAccountStatus` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expuseraccountstatus.php:21` | Der Kontostatus, den die Administrationsoberfläche an Benutzerobjekten markiert: aktiviert, deaktiviert (Setup › Users, ezuser_setting.is_enabled = 0) oder gesperrt (mehr fehlgeschlagene… |
| `expUserActivation` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expuseractivation.php:25` | Was auf die Aktivierung eines Kontos folgt, für user/activate (der Link, den der Benutzer anklickt) und die Schaltfläche Activate von user/unactivated. |
| `expUserAgent` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expuseragent.php:30` | Was eine Person über eine angemeldete Sitzung liest (user/sessions): den Browser, das Betriebssystem und die Geräteart, die ein User-Agent-Header nennt, und… |
| `expUserApproval` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expuserapproval.php:30` | Freigabe neuer Konten in ausgewählten Benutzergruppen (doc/eng-us/bc/6.0/user-account-approval.md): das Workflow-Ereignis „Approve user accounts“ (expUserAppro… |
| `expUserDeactivation` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expuserdeactivation.php:36` | „Deaktivieren und verschieben“ und „Reaktivieren und zurückverschieben“: statt ein Benutzerkonto zu entfernen (Benutzer kommen nie in den Papierkorb, daher lässt sich das Entfernen nicht rückgä… |
| `expUserHygiene` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/user/expuserhygiene.php:31` | Die Konten, die die Seite user/hygiene ("Check user accounts") auflistet: jedes Benutzerkonto oder die unterhalb einer Benutzergruppe, eingegrenzt durch einen Filter (nie angem… |
| `expUserSession` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/user/expusersession.php:22` | Eine Anmeldung eines Benutzers (Tabelle expuser_session): wann sie begann und zuletzt verwendet wurde, von wo (die Adresse, wie die Anfrage sie lieferte), womit (der Use… |
| `expUserSessionTracker` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expusersessiontracker.php:44` | Die angemeldeten Sitzungen eines Benutzers („Ihre Geräte“, user/sessions): Jede Anmeldung erhält eine Zeile in expuser_session (expUserSession), der Benutzer sieht, wo… |
