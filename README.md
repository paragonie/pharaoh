# Pharaoh - PHAR diff utility

[![Build Status](https://travis-ci.org/paragonie/pharaoh.svg?branch=master)](https://travis-ci.org/paragonie/pharaoh)
[![Latest Stable Version](https://poser.pugx.org/paragonie/pharaoh/v/stable)](https://packagist.org/packages/paragonie/pharaoh)
[![Latest Unstable Version](https://poser.pugx.org/paragonie/pharaoh/v/unstable)](https://packagist.org/packages/paragonie/pharaoh)
[![License](https://poser.pugx.org/paragonie/pharaoh/license)](https://packagist.org/packages/paragonie/pharaoh)

Display the differences between all of the files in two PHP Archives.

In all likelihood, two PHP Archives will not be byte-for-byte compatible,
so `sha256sum` will not yield the same result, even if they were both built
from the same source code and contain the same contents. This is because
Composer randomizes e.g. their autoloader class names. 

Copyright (c) 2015 - 2018 [Paragon Initiative Enterprises](https://paragonie.com). 
Check out our other [open source projects](https://paragonie.com/projects) too.

Pharaoh is used by [Box](https://github.com/humbug/box) to provide diffs.

## Example

To verify two PHP Archives were built from the same source code, first download
the official distribution and then build a Phar from source.

### Basic Usage

To see what differs between the two files, run this command:

```sh
/path/to/pharaoh \
    /path/to/distributed.phar \
    /path/to/built-from-source-code.phar
```

Sample output:

```diff
$ pharaoh dist/sodium-compat-php5.phar dist/sodium-compat-php7.phar
diff --git a/tmp/phr_GPrhh5/vendor/autoload.php b/tmp/phr_SBYnr7/vendor/autoload.php
index c20d4db..5c849e0 100644
--- a/tmp/phr_GPrhh5/vendor/autoload.php
+++ b/tmp/phr_SBYnr7/vendor/autoload.php
@@ -4,4 +4,4 @@
 
 require_once __DIR__ . '/composer/autoload_real.php';
 
-return ComposerAutoloaderInitf6d95af9246e0e0e98e255e3bc14c82b::getLoader();
+return ComposerAutoloaderInitd4c7400998bd39c407a1d41a47cd86c6::getLoader();
diff --git a/tmp/phr_GPrhh5/vendor/composer/autoload_real.php b/tmp/phr_SBYnr7/vendor/composer/autoload_real.php
index a23d814..432c698 100644
--- a/tmp/phr_GPrhh5/vendor/composer/autoload_real.php
+++ b/tmp/phr_SBYnr7/vendor/composer/autoload_real.php
@@ -2,7 +2,7 @@
 
 
 
-class ComposerAutoloaderInitf6d95af9246e0e0e98e255e3bc14c82b
+class ComposerAutoloaderInitd4c7400998bd39c407a1d41a47cd86c6
 {
 private static $loader;
 
@@ -19,15 +19,15 @@ if (null !== self::$loader) {
 return self::$loader;
 }
 
-spl_autoload_register(array('ComposerAutoloaderInitf6d95af9246e0e0e98e255e3bc14c82b', 'loadClassLoader'), true, true);
+spl_autoload_register(array('ComposerAutoloaderInitd4c7400998bd39c407a1d41a47cd86c6', 'loadClassLoader'), true, true);
 self::$loader = $loader = new \Composer\Autoload\ClassLoader();
-spl_autoload_unregister(array('ComposerAutoloaderInitf6d95af9246e0e0e98e255e3bc14c82b', 'loadClassLoader'));
+spl_autoload_unregister(array('ComposerAutoloaderInitd4c7400998bd39c407a1d41a47cd86c6', 'loadClassLoader'));
 
 $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
 if ($useStaticLoader) {
 require_once __DIR__ . '/autoload_static.php';
 
-call_user_func(\Composer\Autoload\ComposerStaticInitf6d95af9246e0e0e98e255e3bc14c82b::getInitializer($loader));
+call_user_func(\Composer\Autoload\ComposerStaticInitd4c7400998bd39c407a1d41a47cd86c6::getInitializer($loader));
 } else {
 $map = require __DIR__ . '/autoload_namespaces.php';
 foreach ($map as $namespace => $path) {
@@ -48,19 +48,19 @@ $loader->addClassMap($classMap);
 $loader->register(true);
 
 if ($useStaticLoader) {
-$includeFiles = Composer\Autoload\ComposerStaticInitf6d95af9246e0e0e98e255e3bc14c82b::$files;
+$includeFiles = Composer\Autoload\ComposerStaticInitd4c7400998bd39c407a1d41a47cd86c6::$files;
 } else {
 $includeFiles = require __DIR__ . '/autoload_files.php';
 }
 foreach ($includeFiles as $fileIdentifier => $file) {
-composerRequiref6d95af9246e0e0e98e255e3bc14c82b($fileIdentifier, $file);
+composerRequired4c7400998bd39c407a1d41a47cd86c6($fileIdentifier, $file);
 }
 
 return $loader;
 }
 }
 
-function composerRequiref6d95af9246e0e0e98e255e3bc14c82b($fileIdentifier, $file)
+function composerRequired4c7400998bd39c407a1d41a47cd86c6($fileIdentifier, $file)
 {
 if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
 require $file;
diff --git a/tmp/phr_GPrhh5/vendor/composer/autoload_static.php b/tmp/phr_SBYnr7/vendor/composer/autoload_static.php
index d10ce9b..55ba104 100644
--- a/tmp/phr_GPrhh5/vendor/composer/autoload_static.php
+++ b/tmp/phr_SBYnr7/vendor/composer/autoload_static.php
@@ -4,7 +4,7 @@
 
 namespace Composer\Autoload;
 
-class ComposerStaticInitf6d95af9246e0e0e98e255e3bc14c82b
+class ComposerStaticInitd4c7400998bd39c407a1d41a47cd86c6
 {
 public static $files = array (
 '5255c38a0faeba867671b61dfda6d864' => __DIR__ . '/..' . '/paragonie/random_compat/lib/random.php',

```

### GNU diffs (`-d`)

By default, Pharaoh will use `git` to generate a diff of the code. If you'd prefer
a GNU diff, pass the `-d` flag, like so:

```sh
/path/to/pharaoh -d \
    /path/to/distributed.phar \
    /path/to/built-from-source-code.phar
```

Sample output:

```diff
$ pharaoh -d dist/sodium-compat-php5.phar dist/sodium-compat-php7.phar
Common subdirectories: /tmp/phr_EsTl2p/lib and /tmp/phr_UV3iJt/lib
Common subdirectories: /tmp/phr_EsTl2p/src and /tmp/phr_UV3iJt/src
Common subdirectories: /tmp/phr_EsTl2p/vendor and /tmp/phr_UV3iJt/vendor
```

### File hashes (`-c`, `--check`)

If you're more interested in verifying the authenticity of a Pharaoh's contents from
a quick scan, you can use the `-c algo` or `--check=algo` arguments to specify the hash
function to use.

```sh
/path/to/pharaoh --check=sha256 \
    /path/to/distributed.phar \
    /path/to/built-from-source-code.phar

# This is equivalent to the above command:
/path/to/pharaoh -csha256 \
    /path/to/distributed.phar \
    /path/to/built-from-source-code.phar
```

Sample output:

```terminal
$ pharaoh --check=blake2b dist/sodium-compat-php5.phar dist/sodium-compat-php7.phar
	/vendor/autoload.php
		83d3edc0cc50bbe1d4a05ec1c269359b1eddeb0d7d706f81c7bfb52e7a2dd86c	3310968acbf487a14e38e55077cf792bcd649f48e001717a35506f12031c97a9
	/vendor/composer/autoload_static.php
		a3e53155cbc5faccc2f8bb1d28dfe202ac033504d0e72268847bf33429bb47df	da215e6479739f87f04248880d0bfee78bd5d0828e61c8fb38a85902d42e844c
	/vendor/composer/autoload_real.php
		70e1113f73dc73a61594ee5cb724c44019f8f1a79321e51532a3cf0ef582b50c	1fd1127fb6a245128b5901f5d1087a1fbe0477e525d477192bd91a7623cf152a
```

All hash functions supported by PHP are accepted here. Additionally, if you specify `blake2b`,
Pharaoh will use [sodium_compat](https://github.com/paragonie/sodium_compat) to generate a BLAKE2b
hash of each file.
