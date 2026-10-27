# Kinyarwanda (kin-RW): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Kinyarwanda (Ikinyarwanda) as used in Rwanda, in the Latin alphabet, in the neutral, polite register
of the Government of Rwanda portals (Irembo), Rwandan news (Kigali Today, Imvaho Nshya), the Kinyarwanda
localisations of Google and Mozilla and Kinyarwanda Wikipedia. Kirundi (run-BI) is a close relative with its own
catalogue; no locale falls back to `kin-RW`.

- Word order is subject - verb - object, with the noun class agreement of Kinyarwanda (ifayili, amafayili;
  umukoresha, abakoresha). Function words: na (and), cyangwa (or), ariko (but), niba (if), iki / iyi / uyu (this),
  byose / zose / bose (all), muri (in), kuri (on, to), kuva (from), kugira ngo (so that), nta (no, none), si (not).
- Technical words: a Kinyarwanda word where one is in use (ijambobanga, umukoresha, gushakisha, igenamiterere);
  an English or French loan in Kinyarwanda spelling where Rwandan IT writing uses it (ifayili, seriveri, porogaramu,
  moderi -> modire, imeyili). Never leave the English word unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **mwe** (polite plural "you"), with the verb forms in mu- / mw- (murashobora, murashaka, mwahisemo).
- **Buttons, menu items and links**: the infinitive in ku- / gu-, as Kinyarwanda interfaces do: Kubika (save),
  Kureka (cancel), Gusiba (delete), Gukuraho (remove), Guhindura (edit), Kongeraho (add), Kurema (create),
  Gushakisha (search), Guhitamo (select), Kohereza (send), Kureba (view), Gutangaza (publish), Kohereza ku rubuga /
  Gushyiraho (upload: Gushyiraho), Gukuramo (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Igenamiterere, Ubusobanuro, Ahantu h'ibanze, Izina.
- **Instructions**: the polite plural imperative in -e: "Muhitemo ikintu, hanyuma mukande \"OK\".". "Please" is
  "Mwihangane / Turabasaba" only where the source has it; usually "Nyamuneka".
- **Confirmations and questions**: "Mwizeye ko mushaka ...?": "Mwizeye ko mushaka gusiba iki gice?".
- **What the user can or may do**: "Murashobora ...", "Ntimushobora ..."; "You do not have permission" is "Nta
  burenganzira mufite bwo ...".
- **Messages about a result**: the passive past: "Umushinga wabitswe.", "Ifayili yashyizweho neza.".

## 3. Script and typography

- **Latin** alphabet; the apostrophe ' (U+0027) of elisions (h'ibanze, y'umukoresha, w'ibirimo), never ’.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A noun class prefix or an elision is never written onto a
  placeholder (n'%name or %namena): use a noun that carries the agreement ("ifayili %name", "ibintu %count").
  The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Kinyarwanda | Note |
|---|---|---|
| content | ibirimo | content object: ikintu cy'ibirimo |
| object | ikintu | plural: ibintu |
| node | ingingo | main node: ingingo nkuru; parent node: ingingo mubyeyi |
| location | ahantu | main location: ahantu h'ibanze |
| item (list entry) | ikintu | |
| class | ubwoko | the content class; class group: itsinda ry'ubwoko |
| attribute | ikiranga | plural: ibiranga |
| datatype | ubwoko bw'amakuru | |
| version | verisiyo | version history: amateka ya verisiyo |
| draft | umushinga | store draft: Kubika umushinga |
| publish | gutangaza | published: byatangajwe |
| send for publishing | kohereza ngo bitangazwe | |
| discard | kureka | discard draft: Kureka umushinga |
| archived / pending / rejected | byabitswe mu bubiko / bitegereje / byanzwe | |
| siteaccess | uburyo bwo kugera ku rubuga | siteaccess as a setting name stays in code |
| site, website | urubuga | |
| section | igice | the Exponential section; an INI section: igice cya INI |
| role | uruhare | |
| policy | politiki | |
| limitation | imbibi | |
| permission | uburenganzira | access denied: kwinjira byanzwe |
| module | modire | |
| function | imikorere | a PHP function: funsiyo |
| user / user group | umukoresha / itsinda ry'abakoresha | plural: abakoresha |
| group | itsinda | |
| anonymous | utazwi | anonymous user: umukoresha utazwi |
| administrator | umuyobozi | administration (interface): ubuyobozi |
| username | izina ry'ukoresha | login (the name): izina ryo kwinjira |
| password | ijambobanga | |
| log in / log out | kwinjira / gusohoka | |
| e-mail | imeyili | e-mail address: aderesi ya imeyili |
| cache | ububiko bw'agateganyo | clear cache: Gusiba ububiko bw'agateganyo |
| template | inyandikorugero | override template: inyandikorugero isimbura |
| override | gusimbura | |
| fallback | ingoboka | fallback language: ururimi rw'ingoboka |
| design | igishushanyo | |
| workflow | urujya n'uruza rw'akazi | |
| trigger | imbarutso | |
| event | igikorwa | |
| trash | igiseke cy'imyanda | empty trash: Gusiba ibiri mu giseke; move to trash: Kohereza mu giseke |
| bookmark | ikimenyetso | |
| settings | igenamiterere | configuration: imiterere |
| setup | gutunganya | the Setup tab |
| layout | imiterere y'urupapuro | Exponential Layouts stays |
| block / zone | igice / akarere | block: ibloki |
| extension | umugereka | |
| package | ipaki | |
| search | ishakisha | to search: gushakisha; search index: urutonde rw'ishakisha |
| log | igitabo cy'ibyakozwe | a log file: ifayili y'ibyakozwe; never "log in" |
| queue | umurongo | job queue: umurongo w'imirimo |
| job / task | umurimo | |
| cron | cron | |
| script | sikiripiti | |
| kernel, core | intangiriro | |
| server | seriveri | |
| database | ububiko bw'amakuru | |
| folder / directory | ububiko | |
| file | ifayili | plural: amafayili |
| image | ifoto | thumbnail: agafoto |
| preview | kubanza kureba | |
| upload / download | gushyiraho / gukuramo | |
| export / import | kohereza hanze / kuzana | |
| remove | gukuraho | take out of a list or place |
| delete | gusiba | destroy; deleted: byasibwe |
| edit | guhindura | |
| editor | umwanditsi / porogaramu yo guhindura | person / software |
| save / store | kubika | the verb "Store" of the forms |
| Store (shop tab) | iduka | the shop, as "Shop" |
| cancel | kureka | |
| OK | Yego | |
| apply | gushyira mu bikorwa | |
| back | gusubira inyuma | |
| next / previous | ibikurikira / ibibanza | |
| enable / disable | gufungura / gufunga | enabled / disabled: birakora / ntibikora |
| active / inactive | birakora / ntibikora | |
| run | gukoresha | |
| start / stop | gutangira / guhagarika | |
| session | isesiyo | plural amasesiyo |
| hidden / visible | bihishe / bigaragara | hide / reveal: guhisha / kwerekana |
| archive | ububiko | |
| order (sort) | uko bikurikirana | sort order: uburyo bwo gutondeka |
| order (shop) | itumiza | |
| shop | iduka | |
| basket | igiseke | |
| product | igicuruzwa | |
| customer | umukiriya | |
| discount | igabanuka ry'igiciro | |
| VAT | TVA | |
| price / currency | igiciro / ifaranga | |
| tag | ikirango | |
| translation | ubusobanuro | |
| language | ururimi | main language: ururimi rw'ibanze |
| locale | aho ururimi rukoreshwa | |
| state | imimerere | the object state |
| status | uko bihagaze | |
| view (button) | kureba | a module view (noun): igaragaza |
| format | imiterere | |
| sort | gutondeka | |
| filter | iyungurura | to filter: kuyungurura |
| select | guhitamo | select all: Guhitamo byose; selected: byatoranyijwe |
| copy / move | gukoporora / kwimura | |
| add / create / new | kongeraho / kurema / gishya | |
| restore | kugarura | |
| install / uninstall | gushyiramo / gukuramo | |
| related objects | ibintu bifitanye isano | relation: isano |
| dashboard | ikibaho | |
| identifier / ID | ikiranga / ID | |
| priority | icyihutirwa | |
| owner | nyirabyo | |
| modified / created | byahinduwe / byakozwe | |
| date / time | itariki / isaha | |
| error / warning | ikosa / umuburo | |
| help | ubufasha | |
| notification | imenyesha | |
| account | konti | |
| default | isanzwe | |
| link | ihuza | |
| none / all | nta na kimwe / byose | |
| yes / no | yego / oya | |
| name / title / description | izina / umutwe / ibisobanuro | |
| value / type / list | agaciro / ubwoko / urutonde | |
| page / path | urupapuro / inzira | |
| required / optional | birakenewe / bitari ngombwa | |
| options | amahitamo | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is iduka, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is kubika.
- "Login" in the user, session and audit lists is the user's login name (izina ryo kwinjira); the Login button of the
  error page is the act (Kwinjira).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
