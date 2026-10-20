---
title: site.ini [UserSettings]
audience: developer, admin
category: examples
summary: "The settings of [UserSettings] in site.ini: MaxNumberOfFailedLogin, TrustedIPList, ShowMessageIfExceeded, MinPasswordLength, WeakPasswordCheck, WeakPasswordFile, ForgotPasswordKeyLifetime, GeneratePasswordIfEmpty ..."
keywords: site.ini, UserSettings, INI, setting, MaxNumberOfFailedLogin, TrustedIPList, ShowMessageIfExceeded, MinPasswordLength, WeakPasswordCheck, WeakPasswordFile, ForgotPasswordKeyLifetime, GeneratePasswordIfEmpty, GeneratePasswordLength, AnonymousUserID, DefaultUserPlacement, DefaultSectionID, RegistrationFeedback, RegistrationEmail, VerifyUserType, PasswordInRegistrationEmail, EmailRegistrationInfo, ActivationByAdministratorSendsApprovalMail, RegistrationAdminLinks, RegistrationAdminBaseURL, RegistrationAdminSiteAccess, RegistrationActionTTL, RegisterAutoLogin, UserClassID, UserGroupClassID, UserClassGroupID, UserCreatorID, DeactivateAndMove, DeactivatedUserGroupNodeID, HashType
type: generated
generated: exp:help:examples
---
# site.ini [UserSettings]

The block `[UserSettings]` of `settings/site.ini`, with 44 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## MaxNumberOfFailedLogin

> The possibility to deny login after MaxNumberOfFailedLogin failed login attempts.  
> Max number of failed login attempts.  
> If the max number is '0' the possibility will be disabled  

Default: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
MaxNumberOfFailedLogin=0
*/ ?>
```

## TrustedIPList

> An array with IP addresses or networks which will be always allowed to login after a few failed login attempts.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# an empty "TrustedIPList[]" first replaces the shipped list; without it the items are added
TrustedIPList[]
*/ ?>
```

## ShowMessageIfExceeded

> TrustedIPList\[\]=192.0.0.42  
> TrustedIPList\[\]=192.0.0.0/27  
> If true and failed login attempts of current user execeed MaxNumberOfFailedLogin the user will get a message.  

Default: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
ShowMessageIfExceeded=false
*/ ?>
```

## MinPasswordLength

> Minimum password length  

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
MinPasswordLength=10
*/ ?>
```

## WeakPasswordCheck

> Refuse a well-known or common password (expWeakPasswordList: "admin", "password",  
> "qwerty", "123456", the software's own names and the like, with their simple  
> variants: case, digits and symbols at the ends, letter swaps such as "p@ssw0rd",  
> common endings, a word typed twice; also keyboard runs and repeated characters).  
> Checked for every new password: user/password, registration, the user edit form,  
> exp:resetuserpassword and the web services. Signing in with an existing password  
> is never refused. The installers (setup wizard, kickstarter, exp:install) always  
> check, whatever this says. disabled: only the length and \[PasswordSettings\] count.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
WeakPasswordCheck=enabled
*/ ?>
```

## WeakPasswordFile

> More words to refuse, one per line, in their base form (variants are refused too)  
> WeakPasswordList\[\]  
> WeakPasswordList\[\]=ourcompanyname  
> A file with more words, one per line (# starts a comment), relative to the  
> installation root or absolute; read once per process while it is unchanged  
> WeakPasswordFile=settings/weak-passwords.txt  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
WeakPasswordFile=
*/ ?>
```

## ForgotPasswordKeyLifetime

> Seconds a "Forgot your password?" link works after it was sent (3600: one hour).  
> An expired link is refused with its own message; expired links are removed on  
> the next visit of user/forgotpassword. 0: a link works until it is used (the  
> behavior before 6.0.15).  

Default: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
ForgotPasswordKeyLifetime=3600
*/ ?>
```

## GeneratePasswordIfEmpty

> If true it will automatically generate a password if it is empty  
> and email it to the user.  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
GeneratePasswordIfEmpty=true
*/ ?>
```

## GeneratePasswordLength

Default: `16`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
GeneratePasswordLength=16
*/ ?>
```

## AnonymousUserID

> The ID of the anonymous user, this user will  
> be used for everyone who is not logged in.  

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
AnonymousUserID=10
*/ ?>
```

## DefaultUserPlacement

> Where to store newly self registered users  

Default: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DefaultUserPlacement=12
*/ ?>
```

## DefaultSectionID

> Which section to place newly self registered users  
> (Using 0 means that the user will get the section ID  
>  from its new location)  

Default: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DefaultSectionID=0
*/ ?>
```

## RegistrationFeedback

> What kind of feedback is sent back to the site for newly created  
> users. Use a semi-colon separated list of feedback types.  
> Only email is supported nativly, set to blank to disable.  
> Example on setting up custom feedback type:  
> 'registrationFeedback' function will be used on the class, look in  
> kernel/user/register.php for how the email code is implemented!  
> \[RegistrationFeedback\_<custom\_type>\]  
> Class=<custom\_type\_class\_name>  
> #File is optional, autoload will be used if not set  
> File=<custom\_type\_file\_path>  

Default: `email`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationFeedback=email
*/ ?>
```

## RegistrationEmail

> Where to send email of newly registered users if no  
> email\_receiver is returned by user/registrationfeedback.tpl  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationEmail=
*/ ?>
```

## VerifyUserType

> Type of verification for user registration, email is only  
> type supported nativly, set to blank to disable.  
> Example on how to set up custom verification:  
> 'verifyUser' function will be used on the class, look in  
> kernel/user/register.php for how the email code is implemented!  
> return value of function decides if user mail should be sent or not.  
> \[VerifyUserType\_<custom\_type>\]  
> Class=<custom\_type\_class\_name>  
> #File is optional, autoload will be used if not set  
> File=<custom\_type\_file\_path>  

Default: `email`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
VerifyUserType=email
*/ ?>
```

## PasswordInRegistrationEmail

> If set to enabled, user password would be send in the registration email  
> This password WON'T be stored in plain text  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
PasswordInRegistrationEmail=disabled
*/ ?>
```

## EmailRegistrationInfo

> If set to disabled, the registration confirmation mail won't be sent to the user  
> that created the account  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
EmailRegistrationInfo=enabled
*/ ?>
```

## ActivationByAdministratorSendsApprovalMail

> Whether Activate on Users > Unactivated users (user/unactivated) sends the  
> activated user the "registration approved" mail (user/registrationapproved.tpl),  
> as the user's own activation link does. disabled activates silently.  
> EmailRegistrationInfo=disabled switches that mail off everywhere.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
ActivationByAdministratorSendsApprovalMail=enabled
*/ ?>
```

## RegistrationAdminLinks

> The administrator's "New user registered" mail (RegistrationFeedback=email,  
> user/registrationfeedback.tpl) carries signed links to the administration:  
> view the account, approve (activate) it, reject (remove) it, and the approval  
> item when a workflow holds it. Each opens user/registration, which asks the  
> signed-in administrator to confirm. Guide: doc/eng-us/features/6.0/registration-admin-links.md  
> enabled or disabled (the mail then has no links)  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationAdminLinks=enabled
*/ ?>
```

## RegistrationAdminBaseURL

> The address of the administration the links point to, e.g.  
> https://admin.example.com or https://www.example.com/admin. Empty: https:// and  
> the SiteURL of RegistrationAdminSiteAccess, else that siteaccess's address.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationAdminBaseURL=
*/ ?>
```

## RegistrationAdminSiteAccess

> The administration siteaccess. Empty: the siteaccess whose \[SiteSettings\]  
> FrontSiteAccess is this one, else admin.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationAdminSiteAccess=
*/ ?>
```

## RegistrationActionTTL

> How long a link works, in seconds (604800 = 7 days). Approve and reject links  
> work once.  

Default: `604800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationActionTTL=604800
*/ ?>
```

## RegisterAutoLogin

> If set to disabled, the user will not be automatically logged in while registering  
> even if the user is automatically activated  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegisterAutoLogin=enabled
*/ ?>
```

## UserClassID

> Which content class to use for user creation  

Default: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UserClassID=4
*/ ?>
```

## UserGroupClassID

> Which content class to use for user group creation  

Default: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UserGroupClassID=3
*/ ?>
```

## UserClassGroupID

> Which class group that has user classes, controls which classes  
> is shown on the user navigation part. If empty all classes are used.  

Default: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UserClassGroupID=2
*/ ?>
```

## UserCreatorID

> Which user is considered the creator  

Default: `14`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UserCreatorID=14
*/ ?>
```

## DeactivateAndMove

> "Deactivate and move" (doc/eng-us/features/6.0/user-hygiene.md): enabled offers, on a user's page, in the context menu  
> and on Users > Check user accounts, to disable accounts and bring all their locations together in one hidden  
> user group without roles, remembering where they were, and to move them back on reactivation; a way to retire  
> accounts without removing them (users never go to the trash). disabled (the default) shows nothing and creates  
> nothing. Needs the policy user/deactivate.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DeactivateAndMove=disabled
*/ ?>
```

## DeactivatedUserGroupNodeID

> The node of that group. Empty: the user group with the remote id exp\_deactivated\_users\_group, created below  
> content.ini \[NodeSettings\] UserRootNode on first use (or beforehand by bin/php/ezcreatedeactivatedusergroup.php).  
> A node id set here that does not exist stops the action; it is never replaced by a new group.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DeactivatedUserGroupNodeID=
*/ ?>
```

## HashType

> Use either md5\_password, md5\_user, md5\_site or plaintext  
> md5\_password generates password hash from password only.  
> md5\_user generates password hash from user and password.  
> md5\_site generates password hash from site, user and password  
> plaintext does not generate a hash but has the password as it is, this is not  
> recommended since it is a huge security risk.  
> bcrypt generates hash from password only  
> php\_default uses whichever method PHP deems appropriate (currently bcrypt, as of PHP 5.5)  
> note: password hashes generated with md5\_site will not work after  
>       changing the site name.  

Default: `php_default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
HashType=php_default
*/ ?>
```

## SiteName

> What SiteName should be used when hashing the user\_password  
> with the 'md5\_site' HashType  

Default: `ez.no`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
SiteName=ez.no
*/ ?>
```

## UpdateHash

> true means update hash to HashType if it differs when logging in  
> This is useful if you're upgrading from an older Exponential site  
> or another system that uses mysql as password type, it will then  
> update the hash to whather is set.  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UpdateHash=true
*/ ?>
```

## AuthenticateMatch

> Authenticate match, a list of authenticate fields to use  
> Available are login and email  

Default: `login;email`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
AuthenticateMatch=login;email
*/ ?>
```

## RequireUniqueEmail

> Controls whether a unique email is required for all users,  
> if set to true and email is set in AuthenticateMatch then only  
> one email address instance is allowed on the site.  
> Set to false to disable it  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RequireUniqueEmail=true
*/ ?>
```

## RequireConfirmEmail

> since 4.5, Controls whether users are required to confirm their emails  
> If set to true, users must retype their emails when registering  
> or editing their profiles  

Default: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RequireConfirmEmail=false
*/ ?>
```

## UseSpecialCharacters

> Controls whether special characters are allowed in passwords in additional to  
> characters in the range a-z, A-Z and 0-9.  

Default: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UseSpecialCharacters=false
*/ ?>
```

## UserNameValidationRegex

> Custom validation of user login name  
> Will only affect new users, and existing users when they edit their credentials  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# an empty "UserNameValidationRegex[]" first replaces the shipped list; without it the items are added
UserNameValidationRegex[]
*/ ?>
```

## UserNameValidationErrorText

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# an empty "UserNameValidationErrorText[]" first replaces the shipped list; without it the items are added
UserNameValidationErrorText[]
*/ ?>
```

## DefaultUserNameValidationErrorText

Default: `User login name did not validate!`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DefaultUserNameValidationErrorText=User login name did not validate!
*/ ?>
```

## LogoutRedirect

> Enable these two lines if your MySQL database is not using the utf8mb4 character set.  
> UserNameValidationRegex\[utf8mb4\]=%(?:\\xF0\[\\x90-\\xBF\]\[\\x80-\\xBF\]{2}|\[\\xF1-\\xF3\]\[\\x80-\\xBF\]{3}|\\xF4\[\\x80-\\x8F\]\[\\x80-\\xBF\]{2})%xs  
> UserNameValidationErrorText\[utf8mb4\]=The username cannot contain 4-byte characters.  
> Examples of validation  
> UserNameValidationRegex\[sw\]=/^\\s/  
> UserNameValidationErrorText\[sw\]=The username cannot start with a whitespace.  
> UserNameValidationRegex\[ew\]=/\\s$/  
> UserNameValidationErrorText\[ew\]=The username cannot end with a whitespace.  
> UserNameValidationRegex\[rw\]=/\\s+/  
> UserNameValidationErrorText\[rw\]=The username cannot contain repeated whitespace.  
> UserNameValidationRegex\[lb\]=/\\n|\\r/  
> UserNameValidationErrorText\[lb\]=The username cannot contain line breaks.  
> UserNameValidationRegex\[t\]=/\\t/  
> UserNameValidationErrorText\[t\]=The username cannot contain tabs.  
> Alternative example only allowing 'word' charthers  
> UserNameValidationRegex\[w\]=/\\W/  
> UserNameValidationErrorText\[w\]=The username can only contain a-z, digits or underline.  
> Or  
> UserNameValidationRegex\[wh\]=/\[^A-Za-z0-9\_-\]/  
> UserNameValidationErrorText\[wh\]=The username can only contain a-z, digits, underline or hyphen.  
> While testing add this at the end, just remember to remove it when your done..  
> UserNameValidationRegex\[test\]=/\\w/  
> UserNameValidationErrorText\[test\]=The username seems to be ok!  
> Which url to redirect after a logout  

Default: `/user/login`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
LogoutRedirect=/user/login
*/ ?>
```

## RedirectOnLogoutWithLastAccessURI

> Use the LastAccessURI to decide where to redirect the user on logout  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RedirectOnLogoutWithLastAccessURI=disabled
*/ ?>
```

## LoginHandler

> Text attribute of class 'User' that contains URI where to redirect  
> the user to after login.  
> LoginRedirectionUriAttribute\[user\]=front\_page\_uri  
> Text attribute of class 'User group' that contains URI where  
> to redirect the user belonging to this group to after login.  
> LoginRedirectionUriAttribute\[group\]=front\_page\_uri  

Default: a list of 1: `standard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# an empty "LoginHandler[]" first replaces the shipped list; without it the items are added
LoginHandler[]
LoginHandler[]=standard
*/ ?>
```

## SingleSignOnHandlerArray

> LoginHandler\[\]=LDAP  
> LoginHandler\[\]=textfile  
> Defines the available single sign on handlers.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# an empty "SingleSignOnHandlerArray[]" first replaces the shipped list; without it the items are added
SingleSignOnHandlerArray[]
*/ ?>
```

## ExtensionDirectory

> Extension directories for login handlers. Login handler must be placed in  
> extension/<extension\_directory>/login\_handler/<login\_handler|lowercase>user.php,  
> and class should be named eZ<login\_handler>User  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# an empty "ExtensionDirectory[]" first replaces the shipped list; without it the items are added
ExtensionDirectory[]
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
