---
title: Checklisten für die Community
audience: community, advocate, supporter, developer
category: community
order: 8
summary: Checklisten zum Kopieren für Mitglieder der Exponential-Community - vor einem Beitrag im Forum, vor einem Fehlerbericht, vor einem Pull Request, vor einer Antwort, vor dem Veröffentlichen einer Erweiterung und vor einer Veranstaltung.
keywords: Checkliste, Checklisten, vor dem Posten, Fehler, Pull Request, Antwort, Erweiterung, Veranstaltung, Community, checklist, before posting, bug, answer, extension, event
type: help
---
# Checklisten für die Community

Kopieren Sie die Liste, die Sie brauchen, in Ihre Notizen.

## Inhalt

1. [Bevor Sie eine Frage stellen](#bevor-sie-eine-frage-stellen)
2. [Bevor Sie einen Fehler melden](#bevor-sie-einen-fehler-melden)
3. [Bevor Sie einen Pull Request eröffnen](#bevor-sie-einen-pull-request-eröffnen)
4. [Bevor Sie jemandem antworten](#bevor-sie-jemandem-antworten)
5. [Bevor Sie eine Erweiterung oder ein Theme veröffentlichen](#bevor-sie-eine-erweiterung-oder-ein-theme-veröffentlichen)
6. [Bevor Sie eine Veranstaltung durchführen](#bevor-sie-eine-veranstaltung-durchführen)

## Bevor Sie eine Frage stellen

```text
[ ] I searched the help, the documentation, eZpedia and the forums
[ ] I describe the goal, not only the error
[ ] Steps, expected and actual result, exact message
[ ] Environment from exp:info --plain or Copy report as text
[ ] No passwords, keys, server addresses or personal data
[ ] It is not a security problem (those go to security@se7enx.com)
```

Seite: [Eine gute Frage stellen](beitreten/eine-gute-frage-stellen.md).

## Bevor Sie einen Fehler melden

```text
[ ] It is reproducible on a current release or main
[ ] I searched the tracker; no existing issue
[ ] Right tracker: software / Velocity / extension / community
[ ] Title names the problem
[ ] Steps, expected, actual, environment, cleaned log lines
[ ] Nothing private in text or screenshots
```

Seite: [Einen Fehler melden](mitwirken/einen-fehler-melden.md).

## Bevor Sie einen Pull Request eröffnen

```text
[ ] One change only
[ ] make hooks installed; hooks pass
[ ] Commit message starts with Added: / Updated: / Fixed: / Removed: / Renamed:
[ ] share/filelist.md5 updated (make manifest-fix)
[ ] Tests added that fail without the change
[ ] Coding standards and naming prefixes followed; no inline styles in admin templates
[ ] Documentation updated if behavior changed
[ ] AI-assisted parts checked and mentioned
[ ] Signed CLA on file (kernel); checkbox ticked
```

Seite: [Code beitragen](mitwirken/code-beitragen.md).

## Bevor Sie jemandem antworten

```text
[ ] I understand their goal
[ ] I know the answer, or I say that I am not sure
[ ] I link the source
[ ] I say which version I checked
[ ] No sales pitch
[ ] Security problem? Point to the private channel instead
```

Seite: [Fragen beantworten](anderen-helfen/fragen-beantworten.md).

## Bevor Sie eine Erweiterung oder ein Theme veröffentlichen

```text
[ ] No secrets, customer data or site-specific settings
[ ] LICENSE file (GPL 2 or later recommended)
[ ] ezinfo.php and extension.xml carry the same version
[ ] composer.json; Packagist package; webhook
[ ] README: what it does, supported versions, how to install
[ ] Tag chosen from what is published, sorted by version; never moved later
[ ] Listed on Projects; announced in the forums
```

Seite: [Erweiterungen und Themes teilen](mitwirken/erweiterungen-und-themes-teilen.md).

## Bevor Sie eine Veranstaltung durchführen

```text
[ ] Announced as community-run, not "official"
[ ] Code of conduct linked; contact person named
[ ] Accessibility checked
[ ] Demo on a test installation with invented data
[ ] Consent for photos, recordings and published names
[ ] Only the attendee data needed; deleted afterwards
```

Seite: [Sicherheit und Datenschutz in der Community](lokale-gruppen/sicherheit-und-datenschutz-in-der-community.md).

## Verwandte Seiten

- [Community: hier beginnen](community-hier-beginnen.md)
- [Checklisten für Fürsprecher](../fuersprecher/checklisten-fuer-fuersprecher.md)
- [Checklisten für Veranstaltungen](../fuersprecher/checklisten-fuer-veranstaltungen.md)
- [Die Release-Checkliste](../erweiterungsentwicklung/teil-7-veroeffentlichen/die-release-checkliste.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
