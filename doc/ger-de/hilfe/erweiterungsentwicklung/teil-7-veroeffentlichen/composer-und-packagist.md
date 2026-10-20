---
title: Composer und Packagist
audience: developer, designer, app-creator
category: extension-development
order: 50
summary: Registrieren Sie eine Erweiterung auf Packagist, damit Websites sie mit composer require installieren können - die composer.json, die sie braucht, das Einreichen des Repositorys, wie Versionen aus Tags entstehen und wie Benutzer sie installieren und aktualisieren.
keywords: Composer, Packagist, Paket einreichen, Tags, Versionen, Installer, aktualisieren, composer require, submit package, composer.json, versions, ezpublish-legacy-extension, installer, update
type: help
---
# Composer und Packagist

Teil 7, Kapitel 5. Packagist ist der öffentliche Index, in dem Composer sucht. Sobald Ihre Erweiterung dort
registriert ist, installiert eine Website sie mit einem Befehl und erhält Ihre Updates mit einem anderen. Dieses
Kapitel registriert eine Erweiterung und zeigt, was Benutzer danach tun.

## Bevor Sie einreichen

- Das Repository ist öffentlich auf GitHub (oder einem anderen Host, den Packagist lesen kann).
- `composer.json` ist gültig und vollständig
  ([composer.json für eine Erweiterung](../teil-3-erweiterungsentwickler/composer-json-fuer-erweiterungen.md)):
  `name`, `description`, `type` `ezpublish-legacy-extension`, `license`, `require` mit `php` und einem Installer
  sowie `extra.ezpublish-legacy-extension-name`.
- `composer validate` läuft ohne Fehler durch.
- Es gibt mindestens einen Tag ([Die nächste Versionsnummer wählen](die-naechste-version-waehlen.md)).

```bash
composer validate
```

## Einreichen

1. Melden Sie sich auf [packagist.org](https://packagist.org) an (am einfachsten mit Ihrem GitHub-Konto).
2. Klicken Sie auf **Submit**.
3. Fügen Sie die Adresse des Repositorys ein, zum Beispiel `https://github.com/your-account/myext`.
4. Packagist liest `composer.json`, zeigt den Paketnamen an und legt die Paketseite
   `https://packagist.org/packages/your-account/myext` an.

Der Paketname stammt aus `composer.json`, nicht aus dem Namen des Repositorys. Er muss auf Packagist frei sein, und er
gehört dem, der ihn zuerst registriert hat; deshalb verwendet ein Fork seinen eigenen Vendor-Namen.

## Woher die Versionen kommen

Packagist legt eine Version je Tag an (aus `v1.2.4` wird `1.2.4`) und eine Entwicklungsversion je Branch (aus `main`
wird `dev-main`). Es liest sie, wenn ihm mitgeteilt wird, dass sich das Repository geändert hat: über die
GitHub-Integration, die Sie nach dem ersten Einreichen prüfen sollten ([Der Packagist-Webhook](der-packagist-webhook.md)).

Ein Feld `version` in `composer.json` ist nicht nötig und muss, wenn es vorhanden ist, genau zu jedem Tag passen;
deshalb lassen die meisten Erweiterungen es weg.

## Was Benutzer tun

```bash
composer require your-account/myext:^1.2
```

Composer findet das Paket, prüft, ob seine Anforderungen zur Website passen (PHP-Version, andere Pakete), lädt es
herunter, und das Installer-Plugin für Legacy-Erweiterungen legt es nach `extension/myext/` (der Name aus
`extra.ezpublish-legacy-extension-name`). Dann aktiviert der Benutzer es und leert die Caches, wie es in Ihrer README
steht.

Später:

```bash
composer update your-account/myext
```

nimmt die neueste Version, die die Versionsangabe des Benutzers erlaubt
([Regeln für Versionsnummern](regeln-fuer-versionsnummern.md)).

## Anforderungen, die Benutzer schützen

Was Sie in `require` angeben, entscheidet, wo Ihre Erweiterung installiert werden kann:

| Anforderung | Wirkung |
|---|---|
| `"php": "^8.0"` | Composer verweigert die Installation auf PHP 7 |
| `"se7enxweb/exponential": "^6.0.14"` | verweigert die Installation mit einem älteren Kernel (verwenden Sie das, wenn Sie APIs aufrufen, die mit diesem Release kamen) |
| `"ext-mbstring": "*"` | verweigert, wenn die PHP-Erweiterung fehlt, statt eines fatalen Fehlers später |
| eine andere Erweiterung mit einem Versionsbereich | installiert sie mit oder verweigert bei einem Konflikt |

Fordern Sie, was Sie wirklich brauchen, und nicht mehr: Jede Anforderung ist ein Grund, aus dem Composer verweigern
kann.

## Die Paketinformationen aktualisieren

Die Paketseite aktualisiert sich selbst, wenn der Webhook funktioniert. Wenn nicht, hat die Paketseite für Maintainer
eine Schaltfläche **Update**; verwenden Sie sie einmal und reparieren Sie dann den Webhook.

## Verwandte Seiten

- Zurück: [ezinfo.php und extension.xml anheben](ezinfo-und-extension-xml-anheben.md)
- Weiter: [Der Packagist-Webhook](der-packagist-webhook.md)
- [composer.json modernisieren](../../../../eng-us/help/extension-development/part-4-upgrading/modernizing-composer-json.md)
- [Packagist: about](https://packagist.org/about) (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
