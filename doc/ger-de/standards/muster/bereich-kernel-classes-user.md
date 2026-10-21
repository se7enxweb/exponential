---
title: Muster in kernel/classes/user
audience: developer
category: standards
order: 509
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/user mit den Entwurfsmustern.
keywords: kernel/classes/user, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/user

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/user` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (7)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expRegistrationAdminAction` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expregistrationadminaction.php:23` | Die signierten Links der Administrator-Mail „Neuer Benutzer registriert“ (design:user/registrationfeedback.tpl) und die Aktionen dahinter (user/registrat… |
| `expUnactivatedUsers` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expunactivatedusers.php:22` | Die Benutzer, die sich registriert und ihr Konto nie aktiviert haben, für die Seite user/unactivated: wie viele es sind, eine Seite davon, gesucht nach Login, E-Ma… |
| `expUserAccountOverview` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/user/expuseraccountoverview.php:22` | Die Kontoseite eines Benutzers (user/edit, user/edit/), für einen Menschen beschrieben: wer das Konto ist (Name, Login, E-Mail, Gruppen, Rollen), sein Zustand (aktiv… |
| `expUserAccountStatus` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expuseraccountstatus.php:21` | Der Kontostatus, den die Administrationsoberfläche an Benutzerobjekten markiert: aktiviert, deaktiviert (Setup › Users, ezuser_setting.is_enabled = 0) oder gesperrt (mehr fehlgeschlagene… |
| `expUserActivation` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expuseractivation.php:25` | Was auf die Aktivierung eines Kontos folgt, für user/activate (der Link, den der Benutzer anklickt) und die Schaltfläche Activate von user/unactivated. |
| `expUserDeactivation` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/user/expuserdeactivation.php:36` | „Deaktivieren und verschieben“ und „Reaktivieren und zurückverschieben“: statt ein Benutzerkonto zu entfernen (Benutzer kommen nie in den Papierkorb, daher lässt sich das Entfernen nicht rückgä… |
| `expUserHygiene` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/user/expuserhygiene.php:31` | Die Konten, die die Seite user/hygiene ("Check user accounts") auflistet: jedes Benutzerkonto oder die unterhalb einer Benutzergruppe, eingegrenzt durch einen Filter (nie angem… |
