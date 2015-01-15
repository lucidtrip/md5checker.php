md5checker.php
======
**md5checker** is a command line interface tool to check md5.


## Core Requirements
* PHP with phpcurl

## usage
```bash
php md5checker.php user:md5
```

## examples
adddir
```bash
php md5checker.php test:098f6bcd4621d373cade4e832627b4f6
```

for multicheck use xargs
```bash
xargs -a md5_list.txt -i -n 1 -P 25 php md5checker.php "{}"
```

for more configs and options look the first lines of this script!


## Contact
* email: nibiru[at]safe-mail[dot]net
