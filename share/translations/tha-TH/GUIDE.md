# Thai (tha-TH): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Thai (ภาษาไทยกลาง) of Thailand, in the polite, neutral register of current Thai software (the Thai
localisations of operating systems, office suites and content management systems). The locale is `tha-TH`. Lao
(`lao-LA`) is a related language with its own script and its own catalogue; it does not fall back to Thai, and no
other locale falls back to `tha-TH`.

- **No sentence-final particles.** ครับ, ค่ะ, คะ, นะ, จ้ะ and the like are never written: the interface speaks
  neither as a man nor as a woman. Politeness comes from the wording (กรุณา, โปรด), not from particles.
- **Established loanwords** of computing are written as the Royal Institute's transcription rules and current
  software give them: เซิร์ฟเวอร์, ไฟล์, โฟลเดอร์, แคช, เทมเพลต, สคริปต์, โมดูล, ฟังก์ชัน, ออบเจ็กต์, เวอร์ชัน.
  Where a well-known Thai word exists it is preferred: ฐานข้อมูล, ผู้ใช้, รหัสผ่าน, การตั้งค่า.

## 2. Form of address

The interface avoids pronouns where it can. When the user must be addressed, it is **คุณ** (you), **ของคุณ**
(your).

- **Buttons, menu items and links**: a bare verb or verb phrase, without คุณ and without กรุณา: บันทึก, ยกเลิก,
  ลบ, แก้ไข, ส่งเพื่อเผยแพร่, นำรายการที่เลือกออก, ย้ายไปถังขยะ.
- **Titles, column headings and tabs**: nouns or noun phrases, often with การ or ความ: การตั้งค่า, คำแปล,
  ตำแหน่งหลัก, ความสัมพันธ์.
- **Instructions**: กรุณา (or โปรด) + verb: "กรุณาเลือกรายการแล้วคลิก "ตกลง"", "กรุณารอสักครู่". A reference
  ("see ...") is "ดู ...".
- **Confirmations and questions**: "คุณต้องการ ... หรือไม่?" or "ต้องการ ... หรือไม่?": "คุณต้องการนำคลาสนี้ออกหรือไม่?".
  Not "แน่ใจนะ".
- **What the user can or may do**: "คุณสามารถ ... ได้", "คุณไม่สามารถ ... ได้", "ไม่สามารถ ... ได้". "You do not
  have permission" is "คุณไม่มีสิทธิ์ ...".
- **Error messages**: neutral statements: "ไม่พบไฟล์", "ไม่สามารถบันทึกการเปลี่ยนแปลงได้".
- Not allowed: เธอ, แก, ท่าน as the user's pronoun, gendered particles, slash forms.

## 3. Script and typography

- **Thai script**, left to right. Words are **not separated by spaces**. A space stands only where Thai writing puts
  one: between phrases and sentences, before and after a Latin word, a number, a placeholder, code or a product
  name, and around a quotation in Latin letters. The browser breaks Thai lines by its own dictionary.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never a Thai letter glued to a placeholder:
  "%count ออบเจ็กต์", not "%countออบเจ็กต์". The same for code, file names, INI settings and product names.
- **Digits**: Arabic digits 0-9, as in software. The Thai digits (๐-๙) are not used.
- **Repetition and abbreviation marks**: ๆ is allowed where the word is repeated (ต่างๆ is
  written ต่าง ๆ with a space, as the Royal Institute recommends); ฯ is not used.
- **Quotation marks**: the straight double quotes "..." where the source has its own quotation marks in text; where
  the source has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: the final punctuation follows the source (rule 6 of TRANSLATING.md): a full stop, colon,
  question or exclamation mark at the end stays. Inside a text, a sentence ends with its punctuation of the source
  and is followed by a space. No comma is added where the source has none; a comma of the source in a list may
  become a space.
- **Numbers and plurals**: Thai has no plural forms; a classifier is not needed in labels: "%count ออบเจ็กต์".

## 4. Glossary

Binding for the kernel and every extension.

| English | Thai | Note |
|---|---|---|
| content | เนื้อหา | |
| content object | ออบเจ็กต์เนื้อหา | |
| object | ออบเจ็กต์ | |
| node | โหนด | main node: โหนดหลัก; parent node: โหนดแม่ |
| location | ตำแหน่ง | main location: ตำแหน่งหลัก |
| item (list entry) | รายการ | selected items: รายการที่เลือก |
| class | คลาส | the content class; class group: กลุ่มคลาส; a PHP class: คลาส PHP |
| content class | คลาสเนื้อหา | |
| attribute | แอตทริบิวต์ | |
| datatype | ชนิดข้อมูล | |
| version | เวอร์ชัน | version history: ประวัติเวอร์ชัน |
| draft | ฉบับร่าง | store draft: บันทึกฉบับร่าง |
| publish | เผยแพร่ | published: เผยแพร่แล้ว; publishing: การเผยแพร่ |
| send for publishing | ส่งเพื่อเผยแพร่ | |
| discard | ละทิ้ง | discard draft: ละทิ้งฉบับร่าง |
| archived | เก็บถาวรแล้ว | archive (a file archive): ไฟล์บีบอัด / ไฟล์เก็บถาวร |
| pending | รอดำเนินการ | |
| rejected | ถูกปฏิเสธ | |
| siteaccess | การเข้าถึงไซต์ | |
| site, website | ไซต์, เว็บไซต์ | |
| section | ส่วน | the Exponential section; an INI section: ส่วน INI |
| role | บทบาท | |
| policy | นโยบาย | one rule of a role |
| limitation | ข้อจำกัด | |
| permission | สิทธิ์ | access denied: ปฏิเสธการเข้าถึง |
| access | การเข้าถึง | |
| module | โมดูล | |
| function | ฟังก์ชัน | |
| view (module view) | มุมมอง | the button "View": ดู |
| user | ผู้ใช้ | |
| user group | กลุ่มผู้ใช้ | |
| group | กลุ่ม | |
| member | สมาชิก | |
| anonymous | ผู้ใช้นิรนาม | as an adjective: นิรนาม |
| administrator | ผู้ดูแลระบบ | |
| administration (interface) | ส่วนผู้ดูแลระบบ | |
| account | บัญชี | |
| profile | โปรไฟล์ | |
| username | ชื่อผู้ใช้ | |
| password | รหัสผ่าน | |
| log in / sign in | เข้าสู่ระบบ | |
| log out / sign out | ออกจากระบบ | |
| register (an account) | ลงทะเบียน | |
| two-factor authentication | การยืนยันตัวตนแบบสองขั้นตอน | |
| e-mail | อีเมล | e-mail address: ที่อยู่อีเมล |
| cache | แคช | clear cache: ล้างแคช |
| template | เทมเพลต | |
| override | การแทนที่ | override template: เทมเพลตแทนที่ |
| fallback | สำรอง | fallback language: ภาษาสำรอง |
| design | ดีไซน์ | the Exponential design; designing: การออกแบบ |
| workflow | เวิร์กโฟลว์ | |
| trigger | ทริกเกอร์ | |
| event | เหตุการณ์ | |
| trash | ถังขยะ | empty trash: ล้างถังขยะ; move to trash: ย้ายไปถังขยะ |
| bookmark | บุ๊กมาร์ก | |
| settings | การตั้งค่า | configuration |
| setup | ตั้งค่าระบบ | the Setup tab |
| layout | เลย์เอาต์ | Exponential Layouts |
| block | บล็อก | |
| zone | โซน | |
| extension | ส่วนขยาย | |
| package | แพ็กเกจ | |
| search | ค้นหา | search (noun): การค้นหา; search index: ดัชนีการค้นหา |
| index | ดัชนี | indexing: การทำดัชนี |
| log | ล็อก | a log file: ไฟล์ล็อก; never "log in", never บันทึก |
| queue | คิว | |
| job / task | งาน | |
| cron | cron | a cron job: งาน cron |
| script | สคริปต์ | |
| worker | เวิร์กเกอร์ | |
| kernel, core | เคอร์เนล | |
| server | เซิร์ฟเวอร์ | |
| database | ฐานข้อมูล | |
| table (database) | ตาราง | |
| folder | โฟลเดอร์ | |
| file | ไฟล์ | |
| directory | ไดเรกทอรี | |
| path | พาธ | |
| image | รูปภาพ | thumbnail: ภาพย่อ |
| media | สื่อ | |
| preview | แสดงตัวอย่าง | the noun: ตัวอย่าง |
| upload | อัปโหลด | |
| download | ดาวน์โหลด | |
| export / import | ส่งออก / นำเข้า | |
| remove | นำออก | take out of a list or place |
| delete | ลบ | destroy |
| edit | แก้ไข | |
| editor | ผู้แก้ไข | a person; the editing software: ตัวแก้ไข |
| save / store | บันทึก | the verb "Store" of the forms |
| Store (shop tab) | ร้านค้า | the shop |
| cancel | ยกเลิก | |
| OK | ตกลง | |
| apply | ใช้ | |
| back | ย้อนกลับ | |
| next / previous | ถัดไป / ก่อนหน้า | |
| first / last | แรก / สุดท้าย | |
| enable / disable | เปิดใช้งาน / ปิดใช้งาน | |
| enabled / disabled | เปิดใช้งานแล้ว / ปิดใช้งานแล้ว | |
| active / inactive | ใช้งานอยู่ / ไม่ได้ใช้งาน | |
| run | เรียกใช้ | a script, a job |
| create | สร้าง | |
| new | ใหม่ | new object: ออบเจ็กต์ใหม่ |
| add | เพิ่ม | |
| update | อัปเดต | |
| upgrade | อัปเกรด | |
| install | ติดตั้ง | installer: ตัวติดตั้ง |
| select | เลือก | |
| browse | เรียกดู | |
| move | ย้าย | |
| copy | คัดลอก | |
| swap | สลับ | |
| hide / reveal | ซ่อน / แสดง | hidden: ซ่อนอยู่; visible: มองเห็นได้; visibility: การมองเห็น |
| sort | เรียงลำดับ | sort order: ลำดับการเรียง |
| priority | ลำดับความสำคัญ | |
| filter | ตัวกรอง | the verb: กรอง |
| name | ชื่อ | |
| title | ชื่อเรื่อง | |
| description | คำอธิบาย | |
| identifier | ตัวระบุ | |
| ID | ID | |
| owner | เจ้าของ | |
| creator, author | ผู้สร้าง, ผู้เขียน | |
| created / modified | สร้างเมื่อ / แก้ไขเมื่อ | |
| date / time | วันที่ / เวลา | |
| status | สถานะ | |
| state (object state) | สภาวะ | state group: กลุ่มสภาวะ |
| error | ข้อผิดพลาด | |
| warning | คำเตือน | |
| notice | ข้อสังเกต | |
| required | จำเป็น | |
| information | ข้อมูล | |
| statistics | สถิติ | |
| session | เซสชัน | |
| language | ภาษา | main language: ภาษาหลัก |
| translation | คำแปล | translate: แปล; the act: การแปล |
| locale | โลแคล | |
| country | ประเทศ | |
| currency | สกุลเงิน | |
| shop | ร้านค้า | |
| order (shop) | คำสั่งซื้อ | |
| order (sorting) | ลำดับ | |
| product | สินค้า | |
| price | ราคา | |
| VAT | ภาษีมูลค่าเพิ่ม | VAT type: ประเภทภาษีมูลค่าเพิ่ม |
| discount | ส่วนลด | |
| basket | ตะกร้าสินค้า | |
| wish list | รายการที่อยากได้ | |
| checkout | ชำระเงิน | |
| customer | ลูกค้า | |
| notification | การแจ้งเตือน | |
| subscription | การสมัครรับข้อมูล | subscribe: สมัครรับ; unsubscribe: ยกเลิกการสมัครรับ |
| newsletter | จดหมายข่าว | |
| relation | ความสัมพันธ์ | related objects: ออบเจ็กต์ที่เกี่ยวข้อง |
| keyword | คำสำคัญ | |
| tag (eztags) | แท็ก | an HTML/XML tag: แท็ก HTML |
| URL alias | นามแฝง URL | |
| URL wildcard | ไวลด์การ์ด URL | |
| link | ลิงก์ | |
| dashboard | แดชบอร์ด | |
| page | หน้า | |
| menu | เมนู | |
| tab | แท็บ | |
| toolbar | แถบเครื่องมือ | |
| preferences | การกำหนดลักษณะ | |
| help | วิธีใช้ | |
| confirm | ยืนยัน | |
| reset | รีเซ็ต | |
| field | ฟิลด์ | |
| value | ค่า | |
| default | ค่าเริ่มต้น | as an adjective: เริ่มต้น |
| option | ตัวเลือก | |
| parameter | พารามิเตอร์ | |
| variable | ตัวแปร | |
| operator (template) | ตัวดำเนินการ | |
| query | คิวรี | |
| format | รูปแบบ | text formatting: การจัดรูปแบบ |
| approve / approval | อนุมัติ / การอนุมัติ | |
| collaboration | การทำงานร่วมกัน | |
| token | โทเค็น | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ร้านค้า, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is บันทึก.
- "Log" is ล็อก so that it never collides with บันทึก (save).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
