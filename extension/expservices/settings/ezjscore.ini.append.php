<?php /* #?ini charset="utf-8"?

# expservices: one block per domain class. Every service checks itself (access, POST and form token
# for writes), see expServiceBase. Call: <root>/ezjscore/call/exp<domain>::<method>[::arg...]

[ezjscServer_expservices]
Class=expServicesCatalog

[ezjscServer_expsession]
Class=expSessionServices

[ezjscServer_expsystem]
Class=expSystemServices

[ezjscServer_expini]
Class=expIniServices

[ezjscServer_expcache]
Class=expCacheServices

[ezjscServer_expcronjob]
Class=expCronjobServices

[ezjscServer_expextension]
Class=expExtensionServices

[ezjscServer_exppackage]
Class=expPackageServices

[ezjscServer_expworkflow]
Class=expWorkflowServices

[ezjscServer_expvelocity]
Class=expVelocityServices

[ezjscServer_exprad]
Class=expRadServices

[ezjscServer_expdebug]
Class=expDebugServices

# users and access
[ezjscServer_expuser]
Class=expUserServices

[ezjscServer_expusergroup]
Class=expUserGroupServices

[ezjscServer_exprole]
Class=expRoleServices

[ezjscServer_exppolicy]
Class=expPolicyServices

[ezjscServer_expsessionadmin]
Class=expSessionAdminServices

[ezjscServer_exppreferences]
Class=expPreferencesServices

[ezjscServer_expnotification]
Class=expNotificationServices

[ezjscServer_expcollaboration]
Class=expCollaborationServices

[ezjscServer_expaccount]
Class=expAccountServices

*/ ?>
