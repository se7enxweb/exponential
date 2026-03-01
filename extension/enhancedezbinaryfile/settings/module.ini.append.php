<?php /* #?ini charset="iso-8859-1"?

# These are the allowed file type extensions.  Beware of allowing any
# extensions that can be executed.  Beware of a server setup that lets any
# unexpected filetypes be executed.

[AllowedFileTypes]
AllowedFileTypeList[]
AllowedFileTypeList[]=gif
AllowedFileTypeList[]=jpg
AllowedFileTypeList[]=jpeg
AllowedFileTypeList[]=jpe
AllowedFileTypeList[]=png
AllowedFileTypeList[]=txt
AllowedFileTypeList[]=doc
AllowedFileTypeList[]=pdf
AllowedFileTypeList[]=sxw

# MaxFiles is the Maximum number of files allowed in the original/collected
# folder.  0 means there is no limit to the number of files.  This is to keep
# a malicious user from continuosly uploading files until /var fills up
# potentially crashing the machine (depending on the server setup).
# Of course, this functionality could have undesirable consequences since
# it is removing files.

#If the intention is to have the ability for anonymous users to upload a file
#which will be sent as an attachment to the InformationCollection email - then
#this can be a relatively low number depending on traffic.  If the intention
#is to have files, uploaded by trusted users, which are accessible to other
#users - then use the binaryfile type and an edit form instead of the
#collected info functionality.

#Since the file is not ever written to the database, there is also no way to 
#check if the file has already been uploaded but some other part of the form
#will fail in the future.  Meaning that there will potentially be duplicate
#junk files.

[RemoveFiles]
MaxFiles=25

# The path to the download directory can be changed.
# This will be in the StorageDir.  Default is original/collected

DownloadPath=original/attachments

[Security]
#This defines whether the uploading user can see the full path of the file
#on the collected info feedback page after uploading a file.
#It is a (huge, gaping) security hole if anonymous users can upload files and
#then can access them - especially if, say, php is in the AllowedFileTypeList.
#If ShowFullPath is enabled, then, members of any of the FullPathGroups
#will get a line showing the url of the link on the content/collectedinfo
#webpage.

ShowFullPath=enabled
FullPathGroups[]
#Administrators
FullPathGroups[]=12
#Editors
FullPathGroups[]=13
#All Users
FullPathGroups[]=4

# However, It is perhaps desirable for the person who gets the collected info
# mail to always get the link.  Assuming the collect.ini SendEmail is enabled.
# But, only the group of the uploading user is known, so you can't define
# permissions by the receiver.  The FullPathMail switch allows the
# link to be sent to whomever the Information Collector is.

# This depends on content/collectedinfomail/form.tpl passing a mail=true()
# to the result/info/enhancedbinaryfile.tpl - which of course
# will have to be added to any custom override templates for this
# to work.

FullPathMail=enabled

*/ ?>
