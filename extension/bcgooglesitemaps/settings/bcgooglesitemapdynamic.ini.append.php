<?php /* #?ini charset="utf-8"?

[Classes]
#set include to include objects of classes listed in Class_Filter_Array
#set exclude to exclude objects of classes listed in Class_Filter_Array
ClassFilterType=include

#setting array to include/exclude classes in sitemap
ClassFilterArray[]
ClassFilterArray[]=folder
ClassFilterArray[]=article
ClassFilterArray[]=frontpage


[NodeSettings]
#set true to include only main node of content object in sitemap
#set false to include all nodes of content object in sitemap
MainNodeOnly=false


[StandardSettings]
#set standard values for last fallback
StandardChangefreq=weekly
StandardPriority=0.5


[NodeChangeFreqSettings]
#<changefreq> based on absolute depth of node;
#NodeDepthChangefreq[<depth>]=<value>
NodeDepthChangefreq[]
#NodeDepthChangefreq[0]=always
#NodeDepthChangefreq[1]=always
#NodeDepthChangefreq[2]=hourly
#NodeDepthChangefreq[3]=daily
#NodeDepthChangefreq[4]=weekly
#NodeDepthChangefreq[5]=monthly
#NodeDepthChangefreq[6]=yearly
#NodeDepthChangefreq[7]=never

#<changefreq> for individual NodeID
#NodeIndividualChangefreq[<NodeID>]=<value>
NodeIndividualChangefreq[]
#NodeIndividualChangefreq[355]=daily


[NodePrioritySettings]
#<priority> based on absolute depth of node;
#NodeDepthPriority[<depth>]=<value>
NodeDepthPriority[]
#NodeDepthPriority[0]=1
#NodeDepthPriority[1]=1
#NodeDepthPriority[2]=0.9
#NodeDepthPriority[3]=0.8
#NodeDepthPriority[4]=0.7
#NodeDepthPriority[5]=0.6
#NodeDepthPriority[6]=0.5
#NodeDepthPriority[7]=0.4

#<priority> for individual NodeID
#NodeIndividualPriority[<NodeID>]=<value>
NodeIndividualPriority[]
#NodeIndividualPriority[98]=1


[NodeVisibilitySettings]
#visibility for individual NodeID
#NodeIndividualVisibility[<NodeID>]={hide; show}
#it is not neccessary to set 'show' as it is the default value
NodeIndividualVisibility[]
#NodeIndividualVisibility[60]=hide


[SubtreeChangeFreqSettings]
#<changefreq> for certain subtree
#SubtreeChangefreq[<NodeID>]=<value>
#<NodeID> and all its children get <value> for <changefreq>
SubtreeChangefreq[]
#SubtreeChangefreq[91]=weekly

#<NodeID> and all its children with depth <= <depth> get for <changefreq> the value listed in <matrix> depending on the last modified child
#<matrix> must be an array of settings explained beneath
#set <depth>=0 for unlimited depth
#SubtreeChangefreqModified[<NodeID>]=<matrix>;<depth>
SubtreeChangefreqModified[]
#SubtreeChangefreqModified[69]=NewsSetTree;0

#this matrix shows the relation between the time-interval of last modification and its changefreq-value
#<matrix>[]=<time-interval in seconds>;<changefreq-value>
#SubtreeChangefreqModified[69]=NewsSetTree;0 in connection with NewsSetTree[0]=3600;always means:
#Node 69 and all its children (independet of depth as <depth>=0) get "always" for <changefreq>,
#if minimum one node of the subtree 69 was modified in one hour (3600 seconds) or less
#NewsSetTree[]
#NewsSetTree[0]=3600;always
#NewsSetTree[1]=86400;hourly
#NewsSetTree[2]=172800;daily


[SubtreePrioritySettings]
#<priority> for certain subtree
#SubtreePriority[<NodeID>]=<value>
#<NodeID> and all its children get <value> for <priority>
SubtreePriority[]
#SubtreePriority[91]=0.9

#<NodeID> and all its children with depth <= <depth> get for <priority> the value listed in <matrix> depending on the last modified child
#<matrix> must be an array of settings explained beneath
#set <depth>=0 for unlimited depth
#SubtreePriorityModified[<NodeID>]=<matrix>;<depth>
SubtreePriorityModified[]
#SubtreePriorityModified[69]=NewsSetTree;0

#this matrix shows the relation between the time-interval of last modification and its priority-value
#<matrix>[]=<time-interval in seconds>;<priority-value>
#SubtreePriorityModified[69]=NewsSetTree;0 in connection with NewsSetTree[0]=3600;1 means:
#Node 69 and all its children (independet of depth as <depth>=0) get "1" for <priority>,
#if minimum one node of the subtree 69 was modified in one hour (3600 seconds) or less
#NewsSetTree[]
#NewsSetTree[0]=3600;1
#NewsSetTree[1]=86400;0.9
#NewsSetTree[2]=172800;0.8


[SubtreeVisibilitySettings]
#visibility for a subtree
#'values are "hide", "show" with "show" as default
SubtreeVisibility[]
#SubtreeVisibility[592]=hide


[FolderChangeFreqSettings]
#all children of <NodeID> get the value for <changefreq> as explained in <matrix>
#FolderChangefreqModified[<NodeID>]=<matrix>
FolderChangefreqModified[]
#FolderChangefreqModified[98]=NewsSet

#<matrix> which explains the relation between a <time-interval> (in seconds) and its <changefreq-value>
#<matrix>[]=<time-interval>;<changefreq-value>
#NewsSet[]
#NewsSet[0]=6000000;always
#NewsSet[1]=9000000;hourly
#NewsSet[2]=15000000;daily


[FolderPrioritySettings]
#all children of <NodeID> get the value for <priority> as explained in <matrix>
#FolderPriorityModified[<NodeID>]=<matrix>
FolderPriorityModified[]
#FolderPriorityModified[98]=NewsSet

#<matrix> which explains the relation between a <time-interval> (in seconds) and its <priority-value>
#<matrix>[]=<time-interval>;<priority-value>
#NewsSet[]
#NewsSet[0]=6000000;1
#NewsSet[1]=9000000;0.8
#NewsSet[2]=15000000;0.6
*/ ?>