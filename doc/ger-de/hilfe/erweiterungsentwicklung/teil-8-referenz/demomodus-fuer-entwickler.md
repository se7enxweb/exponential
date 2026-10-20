---
title: Demomodus für Entwickler
audience: developer
category: extension-development
order: 92
summary: Die Demomodus-API für Autoren von Erweiterungen und Installern - expDemoMode::enabled(), protectsPassword(), refusalText(), refused(), installing() und der Speicherschutz in eZUser::store(), der Template-Operator exp_demo_mode() und wie eigener Code EXP_DEMO_MODE=1 in persistenten Workern beachtet.
keywords: Demomodus, Passwortänderung, Speicherschutz, Erweiterung, Installer, persistenter Worker, demo mode, EXP_DEMO_MODE, expDemoMode, protectsPassword, refusalText, installing, guardStore, eZUser::store, exp_demo_mode, template operator, password change, extension, installer, Velocity, persistent worker
type: help
---
# Demomodus für Entwickler

Diese Seite ist für Entwickler von Erweiterungen, Installern und Integrationen, die Benutzerpasswörter setzen oder
ändern. Auf einer öffentlichen Demo schaltet die Umgebungsvariable `EXP_DEMO_MODE=1` den **Demomodus** ein: Das
Passwort eines Administrators lässt sich auf keinem Weg ändern oder zurücksetzen, und die Installer akzeptieren das
veröffentlichte Demo-Passwort. Der Kernel setzt das an der letzten Stelle durch, in `eZUser::store()`, sodass Ihr Code
eine Demo nicht versehentlich beschädigen kann; diese Seite zeigt, wie Sie dem Benutzer den Grund nennen, statt
stillschweigend zu scheitern.

## Inhalt

1. [Die API](#die-api)
2. [Eine eigene Passwortänderung verweigern](#eine-eigene-passwortänderung-verweigern)
3. [Der Speicherschutz](#der-speicherschutz)
4. [Installer, die das Administratorpasswort setzen](#installer-die-das-administratorpasswort-setzen)
5. [Templates](#templates)
6. [Persistente Worker](#persistente-worker)
7. [Testen](#testen)
8. [Verwandte Seiten](#verwandte-seiten)

## Die API

`expDemoMode` (`kernel/classes/expdemomode.php`), alles statisch:

| Methode | Gibt zurück oder tut |
|---|---|
| `enabled()` | `true`, solange `EXP_DEMO_MODE` genau `1` ist (`getenv()`, dann `$_SERVER`); wird bei jedem Aufruf gelesen |
| `isAdministrator( $user )` | das Konto (`eZUser` oder Inhaltsobjekt-ID) hat unbeschränkten Zugriff auf jedes Modul und jede Funktion; `true`, wenn seine Rollen nicht gelesen werden können |
| `protectsPassword( $user )` | `enabled()` und `isAdministrator()`: Das Passwort des Kontos darf sich nicht ändern |
| `refusalText()` | die übersetzte Meldung, auf Deutsch: „Diese Installation ist eine öffentliche Demo: Die Passwörter von Administratoren können nicht geändert oder zurückgesetzt werden.“ |
| `refused( $user, $path, $event )` | protokolliert eine Verweigerung: das Audit-Ereignis (standardmäßig `access.user.password.change.failed` oder `access.user.password.reset.failed`) mit dem Grund `demo_mode` und `$path` sowie eine Debug-Warnung |
| `installing( $callable )` | führt `$callable` bei geöffnetem Speicherschutz aus (siehe unten); gibt dessen Rückgabewert zurück |
| `guardStore( eZUser $user )` | der Speicherschutz, aufgerufen von `eZUser::store()` |

Es gibt keine Einstellung zum Lesen und keine zum Schreiben: Der Modus gehört zur Umgebung des Servers, nicht zur
Installation.

## Eine eigene Passwortänderung verweigern

Fragen Sie nach, bevor Sie ein Passwort ändern, und zeigen Sie den Grund:

```php
if ( class_exists( 'expDemoMode' ) && expDemoMode::protectsPassword( $user ) )
{
    expDemoMode::refused( $user, 'myextension/password' );
    $errors[] = expDemoMode::refusalText();
    return;
}
eZUserOperationCollection::password( $user->id(), $newPassword );
```

Ein Webdienst antwortet mit HTTP 403 und `expDemoMode::refusalText()`, wie es die Kernel-Dienste `expaccount` und
`expuser` tun. Ein Formular, das eine Mail zum Zurücksetzen verschickt, verschickt für einen Administrator keine und
gibt dieselbe Antwort wie für jede andere Adresse, damit das Formular nicht verrät, welche Adresse einem Administrator
gehört.

Die Kernel-Operationen `user/password` und `user/forgotpassword` verweigern bereits: Sie geben
`array( 'status' => false, 'reason' => 'demo_mode' )` zurück.

## Der Speicherschutz

`eZUser::store()` ruft `expDemoMode::guardStore()` auf, solange der Demomodus eingeschaltet ist. Wenn die
gespeicherte Zeile eines bestehenden Administrators einen anderen `password_hash` oder `password_hash_type` hat als das
zu speichernde Objekt, setzt der Schutz die gespeicherten Werte zurück, der Rest der Zeile wird gespeichert, und die
Verweigerung wird protokolliert. Ein neues Konto, ein unverändertes Passwort und jedes Konto, das kein Administrator
ist, passieren.

Ein CSV-Import, ein Paket oder eine Erweiterung, die `password_hash` setzt, kann das Passwort eines Administrators auf
einer Demo also nicht ändern. Code, der `ezuser.password_hash` mit eigenem SQL schreibt, umgeht den Schutz: Speichern
Sie Benutzer immer über `eZUser`. (Das eigene SQL-Schreiben des Hashes durch den Kernel, die Hash-Aktualisierung bei
der Anmeldung, speichert dasselbe Passwort mit einem neueren Verfahren und ist keine Änderung.)

## Installer, die das Administratorpasswort setzen

Die Installationsschritte des Kernels setzen das Administratorpasswort innerhalb von `expDemoMode::installing()`. Ein
eigener Installer, der es nach ihnen setzt (die Nachinstallation eines Seitenpakets, ein Multisite-Installer), muss
dasselbe tun, sonst wird seine Änderung im Demomodus verweigert:

```php
$set = function () use ( $admin, $password ) {
    $admin->setInformation( $admin->id(), $admin->attribute( 'login' ), $admin->attribute( 'email' ), $password, $password );
    $admin->store();
};
class_exists( 'expDemoMode' ) ? expDemoMode::installing( $set ) : $set();
```

Der Geltungsbereich endet, wenn die Funktion zurückkehrt oder eine Ausnahme wirft, sodass er nie in den nächsten
Request eines persistenten Workers hineinreicht. Rufen Sie `installing()` nie aus Code auf, den ein Besucher erreichen
kann.

Um das Demo-Passwort zu akzeptieren, fragen Sie wie die Installer des Kernels `eZStepSiteAdmin::isWeakDefaultPassword()`,
`isTooShortAdminPassword()` und `kickstartPasswordProblem()`: Im Demomodus akzeptieren sie ein allgemein bekanntes
Passwort beliebiger Länge (nie ein leeres). Um ein Passwort zu speichern, das kürzer als `MinPasswordLength` ist,
setzen Sie es innerhalb von `expDemoMode::installing()` und zugleich innerhalb von
`expDemoMode::settingInstallationAdministratorPassword()`, wie es `eZStepCreateSites` tut: Nur dort akzeptiert
`eZUser::validatePassword()` es, und nur solange der Demomodus eingeschaltet ist.

## Templates

- `exp_demo_mode()` (`expDemoModeOperator`) gibt zurück, ob der Demomodus eingeschaltet ist, und ermittelt das bei
  jeder Ausführung des Templates neu.
- `{include uri='design:parts/exp_demo_mode_banner.tpl'}` zeichnet das Banner der Administration (admin, admin4 und
  admin4l) mit der gemeinsamen Komponente `.a4-notice.is-warn`. Binden Sie es außerhalb jedes `{cache-block}` ein,
  sonst zeigt die gecachte Seite den Modus des Requests, der den Cache gefüllt hat.
- `user/password.tpl` erhält `demo_mode_locked` und `demo_mode_text`, `user/forgotpassword.tpl` erhält
  `demo_mode_text` und `demo_mode_refused`, `setup/init/site_admin.tpl` erhält `demo_mode`. Alle sind optional:
  Verwenden Sie `first_set()`.
- Ein Design, das `user/forgotpassword.tpl` oder `user/password.tpl` überschreibt, muss den Hinweis in seinem eigenen
  Markup anzeigen, ohne Inline-CSS, zum Beispiel
  `{if first_set( $demo_mode_text, '' )|ne( '' )}<div class="warning"><p>{$demo_mode_text|wash}</p></div>{/if}`.
  `tests/tests/design/DemoModeUserTemplatesTest.php` prüft jeden solchen Override im Baum.

## Persistente Worker

Exponential Velocity und FrankenPHP behalten PHP-Prozesse für viele Requests. `expDemoMode` speichert nichts
zwischen, antwortet also immer für die aktuelle Umgebung des Prozesses. In Code, der in einem Worker laufen kann, der
gestartet wurde, bevor die Klasse existierte, prüfen Sie zuerst `class_exists( 'expDemoMode' )`, wie der Kernel es
tut. Speichern Sie die Antwort nicht in einer statischen Variablen oder einem Objekt, das länger lebt als der Request.

## Testen

Setzen und entfernen Sie die Variable im Test und stellen Sie sie danach wieder her:

```php
putenv( 'EXP_DEMO_MODE=1' );   // on
putenv( 'EXP_DEMO_MODE' );     // unset
```

`tests/tests/kernel/classes/user/expDemoModeTest.php` zeigt die Tests des Kernels ohne Datenbank: Ein `eZUser` mit
einem festen Zugriffs-Array steht für einen Administrator, und `expDemoMode::storeAllowed()` ist die Entscheidung des
Speicherschutzes als reine Funktion.

## Verwandte Seiten

- [Demomodus: der Upgrade-Hinweis](../../../bc/6.0/demomodus.md)
- [Demomodus](../../demomodus.md) (Administratoren), [Demomodus für Hoster](../../hosting/sicherheit/demomodus-fuer-hoster.md),
  [Demomodus-FAQ](../../demomodus-faq.md), [Probleme mit dem Demomodus](../../hosting/runbooks/probleme-mit-dem-demomodus.md)
- [eZUser und Zugriffsprüfungen](ezuser-und-zugriff.md)
- [Sicherheit in Erweiterungen](../teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
