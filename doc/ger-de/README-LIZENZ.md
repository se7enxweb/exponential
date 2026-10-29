---
title: Die Lizenz dieser Dokumentation
audience: editor, admin, developer, user
category: reference
summary: Die Dokumentation von Exponential ist freie Dokumentation unter der GNU Free Documentation License 1.3 oder später. Der Copyright-Hinweis, was er abdeckt, und die älteren Seiten, die ihre eigenen Bedingungen behalten.
keywords: Lizenz, Copyright, Urheberrecht, GFDL, GNU Free Documentation License, freie Dokumentation, Namensnennung, Kopieren, Erlaubnis, license, free documentation
type: reference
---
# Die Lizenz dieser Dokumentation

## Copyright- und Erlaubnishinweis

Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.

Es ist erlaubt, dieses Dokument unter den Bedingungen der GNU Free Documentation License, Version 1.3 oder einer
späteren, von der Free Software Foundation veröffentlichten Version, zu kopieren, zu verbreiten und/oder zu verändern;
ohne unveränderliche Abschnitte, ohne vordere Umschlagtexte und ohne hintere Umschlagtexte. Eine Kopie der Lizenz ist in
dem Abschnitt mit dem Titel „GNU Free Documentation License“ enthalten.

(Maßgeblich ist der englische Wortlaut des Hinweises: „Permission is granted to copy, distribute and/or modify this
document under the terms of the GNU Free Documentation License, Version 1.3 or any later version published by the Free
Software Foundation; with no Invariant Sections, no Front-Cover Texts, and no Back-Cover Texts.“)

## GNU Free Documentation License

Die Lizenz ist die Datei [LICENSE-GFDL-1.3.txt](../eng-us/LICENSE-GFDL-1.3.txt) in diesem Verzeichnis: der Text der GNU Free
Documentation License, Version 1.3, 3. November 2008, genau so, wie die Free Software Foundation ihn unter
<https://www.gnu.org/licenses/fdl-1.3.txt> veröffentlicht. Er steht auch im Web unter
<https://www.gnu.org/licenses/fdl-1.3.html>. Version 1.3 ist die neueste Version der Lizenz.

Die Hilfe in der Administrationsoberfläche (Hilfe, die Schaltfläche „?“) zeigt die Lizenz auf jeder Seite, die sie
öffnet, und verlinkt auf [Freie Dokumentation: Nutzen Sie sie, um sich und anderen zu helfen](hilfe/freie-dokumentation.md),
die in einfachen Worten erklärt, was die Lizenz Ihnen erlaubt.

## Was der Hinweis abdeckt

Der Hinweis deckt die Dokumentation ab, die das Projekt Exponential im Verzeichnis `doc/` des Repositorys geschrieben
hat und pflegt: die Hilfeseiten (`doc/eng-us/help/`), die Anleitungen, die Installationskapitel, die Funktions-,
Aktualisierungs- und Spezifikationsdokumente der Linie 6.x (`doc/eng-us/features/6.0/`, `doc/eng-us/bc/6.0/`,
`doc/eng-us/specifications/6.0/`), die Changelogs von 6.x und der Extensions, die Chronik und die übrigen Seiten oben in
`doc/`. Dazu gehört jede Seite von 6.x, die über Pull Requests beigetragen wurde; ihr Urheberrecht haben die Mitwirkenden an die
Exponential Foundation und 7x übertragen.

Ein eigenes Urheberrecht „der Mitwirkenden von Exponential“ gibt es nicht: Jede und jeder Mitwirkende überträgt das
Urheberrecht an einem Beitrag über das Contributor Licensing Agreement an die Exponential Foundation und 7x, darum
nennt der Hinweis nur diese beiden. Mitwirkende werden genannt (Changelogs, Autorenschaft der Commits, die Seiten zur
Anerkennung), und eine Nennung ist kein Anspruch auf Urheberrecht; siehe
[Urheberrecht und Copyleft](hilfe/urheberrecht-und-copyleft.md).

Die Software selbst ist keine Dokumentation: Ihr Quellcode bleibt unter der GNU General Public License, Version 2 oder
jede spätere Version ([LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE) und `COPYRIGHT.md` im
Repository).

## Ältere Seiten mit eigenen Bedingungen

Einige Dateien in `doc/` wurden aus der Dokumentation der ursprünglichen Software übernommen, die eZ Systems AS und
seine Mitwirkenden zwischen 1999 und 2014 geschrieben haben, und werden hier zum Nachschlagen aufbewahrt. Das Projekt
Exponential hält nicht ihr Copyright und lizenziert sie nicht neu. Sie werden weiterhin unter den Bedingungen
verbreitet, mit denen sie kamen, der Lizenz des Repositorys (der GNU General Public License, Version 2), und der
Copyright-Hinweis von eZ Systems AS gilt für sie. Die Hilfe zeigt auf diesen Seiten einen anderen Hinweis. Es sind:

- die Funktions-, Aktualisierungs-, Spezifikations- und Changelog-Dokumente der Versionen vor 6.0
  (`doc/eng-us/features/2.x` bis `doc/eng-us/features/5.x`, `doc/eng-us/bc/4.x` und `doc/eng-us/bc/5.x`,
  `doc/eng-us/specifications/3.x`, `doc/eng-us/specifications/4.x` und `doc/eng-us/specifications/trunk`,
  `doc/changelogs/2.9` bis `doc/changelogs/5.x` sowie `doc/changelogs/README.md`);
- `doc/eng-us/shop/`, `doc/eng-us/release-overview` und `doc/eng-us/standards/header.txt`.

Die Beispiel-Konfigurationsdateien `doc/eng-us/examples/htaccess` und `doc/eng-us/examples/ezpublish.conf` wurden für
Exponential 6 neu geschrieben und sind wie der Rest freie Dokumentation; die Rewrite-Regeln darin sind die von
`.htaccess_root`, Teil der Software und unter deren Lizenz.

`doc/eng-us/branch-overview.md` ist neue Dokumentation unter der GNU Free Documentation License, mit Ausnahme ihres
Abschnitts „Historical branches (original SVN, 3.x)“, der die Branch-Aufzeichnungen des ursprünglichen SVN-Repositorys
unter den Bedingungen behält, mit denen sie kamen, und das auf der Seite sagt.

Die Liste ist die Einstellung `[License] OtherLicensePathRules[]` in `settings/exphelp.ini`, die die Hilfe liest, um den
Hinweis einer Seite zu wählen.

## Zur Dokumentation beitragen

Eine Änderung an einer Seite unter dem obigen Hinweis wird unter derselben Lizenz angenommen, der GNU Free Documentation
License 1.3 oder jede spätere Version, damit alle mit der geänderten Seite dieselben Freiheiten behalten. Ein Beitrag,
der nicht unter diesen Bedingungen veröffentlicht werden kann (etwa Text, der aus einer Quelle mit anderer Lizenz
kopiert wurde), muss das in seinem Pull Request angeben.

## Urheberrecht und Copyleft

Seit dem 29. Oktober 2026 halten die Exponential Foundation und 7x das Copyright an Exponential gemeinsam, die
Foundation zuerst genannt: „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“, in Dateien
„Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ Wer es hält und warum, was die
GPL Ihnen erlaubt, welche Hinweise Dritter bleiben und wie der Hinweis in neue Dateien und Erweiterungen kommt:
[Urheberrecht und Copyleft](hilfe/urheberrecht-und-copyleft.md).
