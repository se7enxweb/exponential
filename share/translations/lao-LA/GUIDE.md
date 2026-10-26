# Lao (lao-LA): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Lao (ພາສາລາວ) of Vientiane, in the modern spelling of the Lao PDR (the 1975 reform: no etymological
consonants, ຣ only in foreign names and loanwords where current usage keeps it), in the polite, neutral register of
current Lao software (the Lao localisations of LibreOffice, Firefox and of government web services). The locale is
`lao-LA`. Thai (`tha-TH`) is a related language with its own script and its own catalogue: a Thai word is never
taken over in Thai spelling, and no locale falls back to `lao-LA`.

- **No sentence-final particles.** ເດີ, ເນາະ, ດອກ, ເຈົ້າ (as a polite ending) and the like are never written: the interface speaks
  neutrally. Politeness comes from the wording (ກະລຸນາ), not from particles.
- **Established loanwords** of computing are written as current Lao software gives them: ເຊີບເວີ, ໄຟລ໌,
  ໂຟນເດີ, ແຄຊ, ສະຄຣິບ, ໂມດູນ, ຟັງຊັນ, ອອບເຈັກ, ເວີຊັນ. Where a well-known Lao word exists it is preferred:
  ຖານຂໍ້ມູນ, ຜູ້ໃຊ້, ລະຫັດຜ່ານ, ການຕັ້ງຄ່າ, ແມ່ແບບ.

## 2. Form of address

The interface avoids pronouns where it can. When the user must be addressed, it is the polite **ທ່ານ** (you),
**ຂອງທ່ານ** (your).

- **Buttons, menu items and links**: a bare verb or verb phrase, without ທ່ານ and without ກະລຸນາ: ບັນທຶກ, ຍົກເລີກ,
  ລຶບ, ແກ້ໄຂ, ສົ່ງເພື່ອເຜີຍແຜ່, ເອົາລາຍການທີ່ເລືອກອອກ, ຍ້າຍໄປຖັງຂີ້ເຫຍື້ອ.
- **Titles, column headings and tabs**: nouns or noun phrases, often with ການ or ຄວາມ: ການຕັ້ງຄ່າ, ການແປ,
  ຕຳແໜ່ງຫຼັກ, ຄວາມສຳພັນ.
- **Instructions**: ກະລຸນາ + verb: "ກະລຸນາເລືອກລາຍການ ແລ້ວຄລິກ "ຕົກລົງ"", "ກະລຸນາລໍຖ້າ". A reference ("see
  ...") is "ເບິ່ງ ...".
- **Confirmations and questions**: "ທ່ານຕ້ອງການ ... ບໍ່?": "ທ່ານຕ້ອງການເອົາຄລາສນີ້ອອກບໍ່?".
- **What the user can or may do**: "ທ່ານສາມາດ ... ໄດ້", "ທ່ານບໍ່ສາມາດ ... ໄດ້", "ບໍ່ສາມາດ ... ໄດ້". "You do
  not have permission" is "ທ່ານບໍ່ມີສິດ ...".
- **Error messages**: neutral statements: "ບໍ່ພົບໄຟລ໌", "ບໍ່ສາມາດບັນທຶກການປ່ຽນແປງໄດ້".
- Not allowed: ເຈົ້າ or ມຶງ as the user's pronoun, particles, slash forms.

## 3. Script and typography

- **Lao script**, left to right. Words are **not separated by spaces**. A space stands only where Lao writing puts
  one: between phrases and sentences, before and after a Latin word, a number, a placeholder, code or a product
  name, and around a quotation in Latin letters. The browser breaks Lao lines by its own dictionary.
- **Spelling**: ຫ before a sonorant for the high tone class is written as the ligatures the keyboard gives (ໜ, ໝ)
  and as ຫຼ for hl: ໜ້າ, ໃໝ່, ຫຼັກ, ດາວໂຫຼດ. The cancellation mark ໌ only in loanwords where current usage has it
  (ໄຟລ໌).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never a Lao letter glued to a placeholder:
  "%count ອອບເຈັກ", not "%countອອບເຈັກ". The same for code, file names, INI settings and product names.
- **Digits**: Arabic digits 0-9, as in software. The Lao digits (໐-໙) are not used.
- **Repetition mark** ໆ where a word is repeated (ຕ່າງໆ); the abbreviation mark ຯ is not used.
- **Quotation marks**: the straight double quotes "..." where the source has its own quotation marks in text; where
  the source has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: the final punctuation follows the source (rule 6 of TRANSLATING.md): a full stop, colon,
  question or exclamation mark at the end stays. Inside a text, a sentence ends with its punctuation of the source
  and is followed by a space.
- **Numbers and plurals**: Lao has no plural forms; a classifier is not needed in labels: "%count ອອບເຈັກ".

## 4. Glossary

Binding for the kernel and every extension.

| English | Lao | Note |
|---|---|---|
| content | ເນື້ອຫາ | |
| content object | ອອບເຈັກເນື້ອຫາ | |
| object | ອອບເຈັກ | |
| node | ໂນດ | main node: ໂນດຫຼັກ; parent node: ໂນດແມ່ |
| location | ຕຳແໜ່ງ | main location: ຕຳແໜ່ງຫຼັກ |
| item (list entry) | ລາຍການ | selected items: ລາຍການທີ່ເລືອກ |
| class | ຄລາສ | the content class; class group: ກຸ່ມຄລາສ; a PHP class: ຄລາສ PHP |
| content class | ຄລາສເນື້ອຫາ | |
| attribute | ຄຸນລັກສະນະ | |
| datatype | ປະເພດຂໍ້ມູນ | |
| version | ເວີຊັນ | version history: ປະຫວັດເວີຊັນ |
| draft | ສະບັບຮ່າງ | store draft: ບັນທຶກສະບັບຮ່າງ |
| publish | ເຜີຍແຜ່ | published: ເຜີຍແຜ່ແລ້ວ; publishing: ການເຜີຍແຜ່ |
| send for publishing | ສົ່ງເພື່ອເຜີຍແຜ່ | |
| discard | ປະຖິ້ມ | discard draft: ປະຖິ້ມສະບັບຮ່າງ |
| archived | ເກັບຖາວອນແລ້ວ | archive (a file archive): ໄຟລ໌ບີບອັດ |
| pending | ລໍຖ້າດຳເນີນການ | |
| rejected | ຖືກປະຕິເສດ | |
| siteaccess | ການເຂົ້າເຖິງໄຊ | |
| site, website | ໄຊ, ເວັບໄຊ | |
| section | ພາກສ່ວນ | the Exponential section; an INI section: ພາກສ່ວນ INI |
| role | ບົດບາດ | |
| policy | ນະໂຍບາຍ | one rule of a role |
| limitation | ຂໍ້ຈຳກັດ | |
| permission | ສິດ | access denied: ປະຕິເສດການເຂົ້າເຖິງ |
| access | ການເຂົ້າເຖິງ | |
| module | ໂມດູນ | |
| function | ຟັງຊັນ | |
| view (module view) | ມຸມມອງ | the button "View": ເບິ່ງ |
| user | ຜູ້ໃຊ້ | |
| user group | ກຸ່ມຜູ້ໃຊ້ | |
| group | ກຸ່ມ | |
| member | ສະມາຊິກ | |
| anonymous | ຜູ້ໃຊ້ບໍ່ລະບຸຊື່ | as an adjective: ບໍ່ລະບຸຊື່ |
| administrator | ຜູ້ເບິ່ງແຍງລະບົບ | |
| administration (interface) | ສ່ວນຜູ້ເບິ່ງແຍງລະບົບ | |
| account | ບັນຊີ | |
| profile | ໂປຣໄຟລ໌ | |
| username | ຊື່ຜູ້ໃຊ້ | |
| password | ລະຫັດຜ່ານ | |
| log in / sign in | ເຂົ້າສູ່ລະບົບ | |
| log out / sign out | ອອກຈາກລະບົບ | |
| register (an account) | ລົງທະບຽນ | |
| two-factor authentication | ການຢືນຢັນຕົວຕົນສອງຂັ້ນຕອນ | |
| e-mail | ອີເມວ | e-mail address: ທີ່ຢູ່ອີເມວ |
| cache | ແຄຊ | clear cache: ລ້າງແຄຊ |
| template | ແມ່ແບບ | |
| override | ການແທນທີ່ | override template: ແມ່ແບບແທນທີ່ |
| fallback | ສຳຮອງ | fallback language: ພາສາສຳຮອງ |
| design | ດີໄຊ | the Exponential design; designing: ການອອກແບບ |
| workflow | ຂັ້ນຕອນການເຮັດວຽກ | |
| trigger | ຕົວກະຕຸ້ນ | |
| event | ເຫດການ | |
| trash | ຖັງຂີ້ເຫຍື້ອ | empty trash: ລ້າງຖັງຂີ້ເຫຍື້ອ; move to trash: ຍ້າຍໄປຖັງຂີ້ເຫຍື້ອ |
| bookmark | ບຸກມາກ | |
| settings | ການຕັ້ງຄ່າ | configuration |
| setup | ຕັ້ງຄ່າລະບົບ | the Setup tab |
| layout | ໂຄງຮ່າງ | Exponential Layouts |
| block | ບລັອກ | |
| zone | ໂຊນ | |
| extension | ສ່ວນຂະຫຍາຍ | |
| package | ແພັກເກດ | |
| search | ຄົ້ນຫາ | search (noun): ການຄົ້ນຫາ; search index: ດັດຊະນີການຄົ້ນຫາ |
| index | ດັດຊະນີ | indexing: ການສ້າງດັດຊະນີ |
| log | ບັນທຶກເຫດການ | a log file: ໄຟລ໌ບັນທຶກເຫດການ; never "log in" |
| queue | ຄິວ | |
| job / task | ວຽກ | |
| cron | cron | a cron job: ວຽກ cron |
| script | ສະຄຣິບ | |
| worker | ໂປຣເຊສເຮັດວຽກ | |
| kernel, core | ເຄີເນລ | |
| server | ເຊີບເວີ | |
| database | ຖານຂໍ້ມູນ | |
| table (database) | ຕາຕະລາງ | |
| folder | ໂຟນເດີ | |
| file | ໄຟລ໌ | |
| directory | ໄດເຣັກທໍຣີ | |
| path | ເສັ້ນທາງ | |
| image | ຮູບພາບ | thumbnail: ຮູບຫຍໍ້ |
| media | ສື່ | |
| preview | ສະແດງຕົວຢ່າງ | the noun: ຕົວຢ່າງ |
| upload | ອັບໂຫຼດ | |
| download | ດາວໂຫຼດ | |
| export / import | ສົ່ງອອກ / ນຳເຂົ້າ | |
| remove | ເອົາອອກ | take out of a list or place |
| delete | ລຶບ | destroy |
| edit | ແກ້ໄຂ | |
| editor | ຜູ້ແກ້ໄຂ | a person; the editing software: ຕົວແກ້ໄຂ |
| save / store | ບັນທຶກ | the verb "Store" of the forms |
| Store (shop tab) | ຮ້ານຄ້າ | the shop |
| cancel | ຍົກເລີກ | |
| OK | ຕົກລົງ | |
| apply | ນຳໃຊ້ | |
| back | ກັບຄືນ | |
| next / previous | ຖັດໄປ / ກ່ອນໜ້າ | |
| first / last | ທຳອິດ / ສຸດທ້າຍ | |
| enable / disable | ເປີດໃຊ້ງານ / ປິດໃຊ້ງານ | |
| enabled / disabled | ເປີດໃຊ້ງານແລ້ວ / ປິດໃຊ້ງານແລ້ວ | |
| active / inactive | ກຳລັງໃຊ້ງານ / ບໍ່ໄດ້ໃຊ້ງານ | |
| run | ດຳເນີນການ | a script, a job |
| create | ສ້າງ | |
| new | ໃໝ່ | new object: ອອບເຈັກໃໝ່ |
| add | ເພີ່ມ | |
| update | ອັບເດດ | |
| upgrade | ອັບເກຣດ | |
| install | ຕິດຕັ້ງ | installer: ຕົວຕິດຕັ້ງ |
| select | ເລືອກ | |
| browse | ທ່ອງເບິ່ງ | |
| move | ຍ້າຍ | |
| copy | ສຳເນົາ | |
| swap | ສະຫຼັບ | |
| hide / reveal | ເຊື່ອງ / ສະແດງ | hidden: ຖືກເຊື່ອງ; visible: ເບິ່ງເຫັນໄດ້; visibility: ການເບິ່ງເຫັນ |
| sort | ຮຽງລຳດັບ | sort order: ລຳດັບການຮຽງ |
| priority | ລຳດັບຄວາມສຳຄັນ | |
| filter | ຕົວກັ່ນຕອງ | the verb: ກັ່ນຕອງ |
| name | ຊື່ | |
| title | ຫົວຂໍ້ | |
| description | ຄຳອະທິບາຍ | |
| identifier | ຕົວລະບຸ | |
| ID | ID | |
| owner | ເຈົ້າຂອງ | |
| creator, author | ຜູ້ສ້າງ, ຜູ້ຂຽນ | |
| created / modified | ສ້າງເມື່ອ / ແກ້ໄຂເມື່ອ | |
| date / time | ວັນທີ / ເວລາ | |
| status | ສະຖານະ | |
| state (object state) | ສະພາວະ | state group: ກຸ່ມສະພາວະ |
| error | ຂໍ້ຜິດພາດ | |
| warning | ຄຳເຕືອນ | |
| notice | ຂໍ້ສັງເກດ | |
| required | ຈຳເປັນ | |
| information | ຂໍ້ມູນ | |
| statistics | ສະຖິຕິ | |
| session | ເຊສຊັນ | |
| language | ພາສາ | main language: ພາສາຫຼັກ |
| translation | ການແປ | translate: ແປ; a translated text: ຄຳແປ |
| locale | ໂລແຄລ | |
| country | ປະເທດ | |
| currency | ສະກຸນເງິນ | |
| shop | ຮ້ານຄ້າ | |
| order (shop) | ຄຳສັ່ງຊື້ | |
| order (sorting) | ລຳດັບ | |
| product | ສິນຄ້າ | |
| price | ລາຄາ | |
| VAT | ອາກອນມູນຄ່າເພີ່ມ | VAT type: ປະເພດອາກອນມູນຄ່າເພີ່ມ |
| discount | ສ່ວນຫຼຸດ | |
| basket | ກະຕ່າສິນຄ້າ | |
| wish list | ລາຍການທີ່ຢາກໄດ້ | |
| checkout | ຊຳລະເງິນ | |
| customer | ລູກຄ້າ | |
| notification | ການແຈ້ງເຕືອນ | |
| subscription | ການສະໝັກຮັບຂໍ້ມູນ | subscribe: ສະໝັກຮັບ; unsubscribe: ຍົກເລີກການສະໝັກຮັບ |
| newsletter | ຈົດໝາຍຂ່າວ | |
| relation | ຄວາມສຳພັນ | related objects: ອອບເຈັກທີ່ກ່ຽວຂ້ອງ |
| keyword | ຄຳສຳຄັນ | |
| tag (eztags) | ແທັກ | an HTML/XML tag: ແທັກ HTML |
| URL alias | ນາມແຝງ URL | |
| URL wildcard | ໄວລດ໌ຄາດ URL | |
| link | ລິ້ງ | |
| dashboard | ແດຊບອດ | |
| page | ໜ້າ | |
| menu | ເມນູ | |
| tab | ແຖບ | |
| toolbar | ແຖບເຄື່ອງມື | |
| preferences | ການກຳນົດຄ່າສ່ວນຕົວ | |
| help | ຊ່ວຍເຫຼືອ | |
| confirm | ຢືນຢັນ | |
| reset | ຣີເຊັດ | |
| field | ຊ່ອງຂໍ້ມູນ | |
| value | ຄ່າ | |
| default | ຄ່າເລີ່ມຕົ້ນ | as an adjective: ເລີ່ມຕົ້ນ |
| option | ຕົວເລືອກ | |
| parameter | ພາຣາມິເຕີ | |
| variable | ຕົວແປ | |
| operator (template) | ຕົວດຳເນີນການ | |
| query | ຄຳສັ່ງສອບຖາມ | |
| format | ຮູບແບບ | text formatting: ການຈັດຮູບແບບ |
| approve / approval | ອະນຸມັດ / ການອະນຸມັດ | |
| collaboration | ການເຮັດວຽກຮ່ວມກັນ | |
| token | ໂທເຄັນ | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ຮ້ານຄ້າ, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is ບັນທຶກ.
- "Log" is ບັນທຶກເຫດການ, never ບັນທຶກ alone, so that it never collides with ບັນທຶກ (save).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
