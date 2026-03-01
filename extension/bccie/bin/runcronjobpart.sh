#!/bin/bash

# php ./runcronjobs.php -dall exportcsv;
# php ./runcronjobs.php -dall exportcsv | tee var/log/cie.log
# php ./runcronjobs.php -dall exportsylk | tee var/log/cie.log

php ./runcronjobs.php -dall exportcsv | tee var/log/cie.log

