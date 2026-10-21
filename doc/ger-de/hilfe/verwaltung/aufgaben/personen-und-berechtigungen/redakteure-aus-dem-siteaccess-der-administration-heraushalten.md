---
title: Redakteure aus dem Siteaccess der Administration heraushalten
audience: government, admin
category: public-sector
order: 250
summary: Schritt für Schritt - die Richtlinie user/login der Redakteursrollen mit der Einschränkung SiteAccess auf den Siteaccess der Redakteure oder den öffentlichen Siteaccess begrenzen, sodass sich nur das Web-Team und Administratoren in der Administration anmelden.
keywords: Siteaccess, user/login, Einschränkung SiteAccess, Administration, Siteaccess der Redakteure, Zugang zur Administration, Anmeldung, Trennung, minimale Rechte, siteaccess, SiteAccess limitation, administration, editor siteaccess, admin access, sign-in, separation, least privilege
type: help
---
# Redakteure aus dem Siteaccess der Administration heraushalten

Diese Aufgabe ist für Administratoren. Am Ende melden sich Redakteure der Abteilungen nur auf dem Siteaccess der
Redakteure (oder dem öffentlichen) an, und der Siteaccess der Administration lehnt sie ab. Die Richtlinie `user/login`
nimmt eine Einschränkung `SiteAccess` (`kernel/user/module.php`); die Seite der Rolle schreibt sie zum Beispiel als
*Darf sich anmelden, auf den Siteaccesses site und bold*
([Anleitung zu Rollen, Abschnitt 2](../../../../anleitungen/rollen-und-richtlinien.md#2-eine-rolle-lesen)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Finden, woher Redakteure user/login erhalten](#finden-woher-redakteure-userlogin-erhalten)
3. [Es einschränken](#es-einschränken)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ihre Installation hat einen eigenen Siteaccess zum Bearbeiten, oder Redakteure arbeiten auf dem öffentlichen
  Siteaccess; siehe [Siteaccesses der Administration und der Öffentlichkeit trennen](../../../../../eng-us/help/government/operations/hosting/separating-admin-and-public-siteaccesses.md).
  Die Namen stehen unter **Setup > Systeminformationen**, Teil *Siteaccesses*.
- Ein Konto mit dem Modul `role`.

## Finden, woher Redakteure user/login erhalten

1. **Benutzer > Rollen und Richtlinien**; öffnen Sie jede Rolle, die die Redakteure haben.
2. Suchen Sie den Satz *Darf sich anmelden* (Modul `user`, Funktion `login`) und seine Siteaccesses. Eine Richtlinie
   *Darf alles* im Modul user (`user/*`) schließt die Anmeldung überall ebenfalls ein.

## Es einschränken

1. In der Rolle **Bearbeiten**, dann **Einschränkungen** neben der Richtlinie `user/login`.
2. Wählen Sie unter **SiteAccess** nur die Siteaccesses der Redakteure und der Öffentlichkeit (Strg oder Cmd für mehr
   als einen). **OK**.
3. Wenn die Rolle `user/*` hat, ersetzen Sie es durch die einzelnen Funktionen, die die Redakteure brauchen (`login`
   eingeschränkt, `password`, `preferences`).
4. **Speichern**.

Die Rollen des Web-Teams und der Freigebenden behalten ein `user/login`, das den Siteaccess der Administration
einschließt.

## Prüfen, ob es funktioniert hat

- Melden Sie sich als Redakteur unter der Adresse der Administration an: Die Anmeldung wird abgelehnt.
- Melden Sie sich auf dem Siteaccess der Redakteure an: Es funktioniert.
- Die Seite der Rolle zeigt *Darf sich anmelden, auf den Siteaccesses ...* ohne die Administration.

## Fallstricke

- Ein Benutzer hat jede Richtlinie jeder Rolle: Wenn irgendeine Rolle (auch eine einer übergeordneten Gruppe)
  `user/login` in der Administration erlaubt, kommt der Redakteur trotzdem hinein. Prüfen Sie alle.
- Netzwerkbeschränkungen auf dem Hostnamen der Administration sind eine zweite, stärkere Schicht
  ([Die Administration beschränken](../../../hosting/sicherheit/die-administration-einschraenken.md)).

## Verwandte Seiten
- [Grundsätze der minimalen Rechte](../../../../../eng-us/help/government/operations/people-and-permissions/least-privilege-principles.md)
- [Prüfen, was eine Rolle erlaubt](pruefen-was-eine-rolle-erlaubt.md)
- [Setup-Assistent und Siteaccess der Redakteure](../../../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Sicherheitshärtung, Kapitel 13.5](../../../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
