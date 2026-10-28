# Breton (bre-FR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Breton (brezhoneg) in the unified spelling (peurunvan), with the computing terms of the Ofis Publik ar Brezhoneg term
bank (TermBret) and of the Breton software localisations (An Drouizig, Mozilla, LibreOffice), in a neutral, polite
register. No other locale falls back to `bre-FR`.

- Write real Breton, never leave the French or English word where Breton has a term: "restr", "ger-tremen",
  "teuliad", "ere", "implijer", "dilemel", "enrollañ".
- **Mutations** follow the grammar (ar restr, ar c'hlasad, he restr, da implijer, ma ger-tremen). A placeholder, a
  product name or code never mutates; place it where no mutation is needed (after a colon, after a noun: "an
  implijer %name", "Anv: %name").
- Short, plain sentences; the verb-noun forms of the standard (enrollañ, dilemel, kemmañ).

## 2. Form of address

- The user is addressed with **c'hwi** (polite), as Breton software does: "Ha sur oc'h e fell deoc'h dilemel an
  objed-mañ?", "N'hoc'h eus ket an aotre da ...".
- **Buttons, menu items and links**: the verb-noun: Enrollañ, Nullañ, Dilemel, Lemel kuit, Kemmañ, Ouzhpennañ,
  Krouiñ, Klask, Diuzañ, Kas, Gwelout, Embann, Pellgas, Pellgargañ, Arloañ, Assevel.
- **Titles, column headings and tabs**: nouns: Arventennoù, Troidigezhioù, Lec'hiadur pennañ, Anv.
- **Instructions**: the imperative of c'hwi: "Diuzit un objed ha klikit war \"Mat eo\".".
- **Confirmations and questions**: "Ha sur oc'h e fell deoc'h ...?".
- **What the user can or may do**: "Gallout a rit ...", "Ne c'hallit ket ..."; "You do not have permission" is
  "N'hoc'h eus ket an aotre da ...".
- **Messages about a result**: the impersonal past or the participle: "Enrollet eo bet ar brouilhed.", "Pellgaset
  eo bet ar restr.".

## 3. Script and typography

- **Latin** alphabet with the Breton letters and digraphs (c'h, ch, ñ, ù, ê, ü), as composed characters. The
  apostrophe of c'h is the straight '.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder and a placeholder is never
  mutated. The same for code, file names, INI settings and product names.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source (no space before : ? ! is added).
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Breton | Note |
|---|---|---|
| content | endalc'had | content object: objed endalc'had |
| object | objed | plural: objedoù |
| node | skoulm | plural: skoulmoù; main node: skoulm pennañ; parent node: skoulm kar |
| location | lec'hiadur | main location: lec'hiadur pennañ |
| item (list entry) | elfenn | |
| tree | gwezenn | content structure: frammadur an endalc'had |
| class | klasad | plural: klasadoù; class group: strollad klasadoù |
| attribute | doareenn | plural: doareennoù |
| datatype | rizh roadennoù | |
| version | handelv | plural: handelvoù; version history: istor an handelvoù |
| draft | brouilhed | store draft: Enrollañ ar brouilhed |
| publish | embann | published: embannet |
| send for publishing | kas da embann | |
| discard | dilezel | |
| siteaccess | haeziñ al lec'hienn | siteaccess as a setting name stays in code |
| site, website | lec'hienn | |
| section | rann | an INI section: rann INI |
| role | roll | plural: rolloù |
| policy | reolenn | plural: reolennoù |
| limitation | bevenn | plural: bevennoù |
| permission | aotre | access denied: haeziñ nac'het |
| module | mollad | |
| function | arc'hwel | |
| user / user group | implijer / strollad implijerien | plural: implijerien |
| group | strollad | |
| anonymous | dizanv | |
| administrator | merour | administration interface: etrefas merañ |
| username | anv implijer | |
| password | ger-tremen | |
| log in / log out | kevreañ / digevreañ | |
| e-mail | postel | e-mail address: chomlec'h postel |
| cache | krubuilh | clear cache: Skarzhañ ar grubuilh |
| template | patrom | override template: patrom erlec'hiañ |
| override | erlec'hiañ | |
| fallback | ehpenn | fallback language: yezh ehpenn |
| design | neuz | |
| workflow | red-labour | |
| trigger | loc'her | |
| event | darvoud | |
| trash | boubellenn | empty trash: Goullonderiñ ar voubellenn |
| bookmark | sined | |
| settings | arventennoù | configuration: kefluniadur |
| setup | kefluniañ | the Setup tab: Kefluniañ |
| layout | aozadur | Exponential Layouts stays |
| block / zone | bloc'h / takad | |
| extension | astenn | |
| package | pakad | |
| search | klask | search index: meneger klask |
| log | marilh | log file: restr varilh |
| queue | lostennad | |
| job / task | trevell | cronjob: labour cron |
| handler | ardoer | |
| gateway (payment) | porzh-paeañ | |
| custom tag | balizenn personelaet | |
| time zone | takad-eur | |
| set (button) | termenañ | |
| kernel, core | kalon | |
| server | servijer | |
| database | stlennvon | |
| folder / directory | teuliad / renkell | |
| file | restr | |
| image | skeudenn | thumbnail: skeudennig |
| preview | alberz | |
| upload / download | pellgas / pellgargañ | |
| export / import | ezporzhiañ / enporzhiañ | |
| remove | lemel kuit | take out of a list or place |
| delete | dilemel | destroy |
| edit | kemmañ | |
| editor | aozer | person or software |
| save / store | enrollañ | the verb "Store" of the forms |
| Store (shop tab) | Stal | the shop, as "Shop" |
| cancel | nullañ | |
| OK | Mat eo | |
| apply | arloañ | |
| back | distreiñ | |
| next / previous | war-lerc'h / a-raok | |
| enable / disable | gweredekaat / diweredekaat | |
| active / inactive | oberiant / dioberiant | |
| run | lañsañ | |
| start / stop | loc'hañ / paouez | |
| session | dalc'h | |
| hidden / visible | kuzh / gwelus | hide / reveal: kuzhat / diskouez |
| archive | diell | |
| order (sort) | urzh | sort order: urzh rummañ |
| order (shop) | urzhiad | |
| shop | stal | |
| basket | paner | |
| product | produ | |
| customer | pratik | |
| discount | distaol | |
| VAT | TGO | Tailh war ar Gwerzh Ouzhpennet |
| price / currency | priz / moneiz | |
| tag | tikedenn | |
| translation | troidigezh | |
| language | yezh | main language: yezh pennañ |
| locale | locale | as a setting; in prose: arventennoù lec'hel |
| state | stad | the object state |
| status | statud | |
| view (button) | gwelout | a module view (noun): gwel |
| sort | rummañ | |
| filter | sil | to filter: silañ |
| select | diuzañ | selected: diuzet |
| copy / move | eilañ / dilec'hiañ | |
| add / create / new | ouzhpennañ / krouiñ / nevez | |
| restore | assevel | |
| install / uninstall | staliañ / distaliañ | |
| related objects | objedoù liammet | relation: liamm |
| dashboard | taolenn-stur | |
| identifier / ID | anaouder / ID | |
| priority | kentelezh | |
| owner | perc'henn | |
| modified / created | kemmet / krouet | |
| date / time | deiziad / eur | |
| error / warning | fazi / diwall | |
| help | skoazell | |
| notification | kemennadenn | |
| account | kont | |
| default | dre ziouer | |
| link | ere | |
| none / all | hini ebet / an holl | |
| yes / no | ya / ket | |
| name / title / description | anv / titl / deskrivadur | |
| value / type / list | talvoud / rizh / listenn | |
| page / path | pajenn / hent | |
| required / optional | ret / diret | |
| options | dibarzhioù | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Stal, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Enrollañ.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Kevreañ).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
