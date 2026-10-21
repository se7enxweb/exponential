---
title: Die offene Registrierung schließen
audience: government, admin
category: public-sector
order: 280
summary: Schritt für Schritt - Besucher daran hindern, auf einer Site im öffentlichen Sektor, auf der sich niemand selbst registrieren soll, Konten anzulegen, auf einer neuen Installation (keine Richtlinie) und auf einer aktualisierten (PolicyOmitListExclude), und nicht aktivierte Registrierungen aufräumen.
keywords: Registrierung, user/register, Selbstregistrierung, Registrierung schließen, PolicyOmitList, PolicyOmitListExclude, Rolle Anonymous, nicht aktivierte Benutzer, Spam-Konten, registration, self-registration, close registration, Anonymous role, unactivated users, spam accounts
type: help
---
# Die offene Registrierung schließen

Diese Aufgabe ist für Administratoren öffentlicher Websites, auf denen nur Mitarbeiter Konten haben. Am Ende wird
`user/register` für Besucher abgelehnt, die Anmeldeseite zeigt keinen Link zur Registrierung, und übrig gebliebene
Registrierungen sind entfernt. Die Regeln stehen in der
[Anleitung zu Rollen, Abschnitt 8](../../../../anleitungen/rollen-und-richtlinien.md#8-festlegen-wer-sich-registrieren-und-wer-knotendaten-lesen-darf).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Neue Installation](#neue-installation)
3. [Aktualisierte Installation](#aktualisierte-installation)
4. [Alte Registrierungen aufräumen](#alte-registrierungen-aufräumen)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang (für eine aktualisierte Site) und ein Konto mit dem Modul `role`.
- Stellen Sie sicher, dass kein Dienst auf die Selbstregistrierung angewiesen ist (die Anmeldung zum Newsletter ist in
  den meisten Einrichtungen von Konten getrennt; prüfen Sie Ihre).

## Neue Installation

Auf einer neuen Installation braucht das Registrieren die Richtlinie `user/register`. Prüfen Sie, dass die Rolle
**Anonymous** nicht *User / Register* hat: **Benutzer > Rollen und Richtlinien**, Anonymous. Wenn sie sie hat,
**Bearbeiten**, die Richtlinie entfernen, **Speichern**.

## Aktualisierte Installation

Eine aktualisierte Site lässt die Registrierung offen, weil `user/register` in `site.ini [RoleSettings]
PolicyOmitList[]` bleibt. Um sie zu schließen, fügen Sie `settings/override/site.ini.append.php` hinzu:

```ini
[RoleSettings]
PolicyOmitListExclude[]=user/register
```

dann leeren Sie den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

`PolicyOmitListExclude[]` nimmt einzelne Einträge aus der Auslassungsliste, nachdem alle Einstellungsdateien gelesen
sind, sodass die Einträge, die Erweiterungen hinzufügen, bleiben. Stellen Sie dann sicher, dass die Rolle Anonymous
keine Richtlinie *User / Register* hat.

## Alte Registrierungen aufräumen

**Benutzer > Nicht aktivierte Benutzer** listet nie aktivierte Registrierungen; die älter als 30 Tage sind als **Alt**
markiert. Verwenden Sie **Alle unaktivierten Benutzer entfernen** (oder entfernen Sie ausgewählte). Der anonyme Benutzer,
`admin` und Sie selbst werden nie entfernt; das Entfernen wird als `access.user.remove` aufgezeichnet.

## Prüfen, ob es funktioniert hat

- Öffnen Sie in einem privaten Fenster die Anmeldeseite: kein Link *Neues Konto registrieren*.
- Öffnen Sie `/user/register` direkt: abgelehnt.

## Verwandte Seiten
- [Pflege der Benutzerkonten](../../../../../eng-us/help/government/operations/people-and-permissions/user-account-hygiene.md)
- [Spamschutz für Formulare](../../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)
- [Anleitung zu Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
