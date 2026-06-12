#!/bin/bash
# @description Shared functions library sourced by extension packaging scripts (not executable)
# @long-description Provides common shell functions for building and packaging Exponential extensions. Used by makeaddonpackages.sh, packext.sh, and related scripts. Source with . ./bin/shell/extensionscommon.sh

EXTENSIONS="http://svn.projects.ez.no/ezoe/stable/5.0/ezoe/"
EXTENSIONS="$EXTENSIONS http://svn.ez.no/svn/extensions/ezodf/stable/2.4/"
#EXTENSIONS="$EXTENSIONS http://svn.ez.no/svn/extensions/ezpaypal/trunk"

# These are disabled for now
#EXTENSIONS="$EXTENSIONS http://svn.ez.no/svn/commercial/projects/ezoracle/trunk"
#EXTENSIONS="$EXTENSIONS http://svn.ez.no/svn/commercial/projects/paymentgateways/ezpaynet/"
#EXTENSIONS="$EXTENSIONS http://svn.ez.no/svn/commercial/projects/survey/"
