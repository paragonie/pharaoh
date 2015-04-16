# Pharaoh - PHAR diff utility

Display the differences between all of the files in two PHP Archives.

Copyright 2015 [Paragon Initiative Enterprises](https://paragonie.com)

## Set up

```sh
ln -s ./pharaoh /usr/bin/pharaoh
```

## Example

```sh
pharaoh composer.phar composer-from-source.phar
```

## Why?

As part of our [ASGard](https://getasgard.com) project, we must verify the
reproducibility of software deliverables. This means building a .phar from
source and comparing it to the one provided by the vendor.

There wasn't already a simple command line utility for examining the differences
between two .phar files, so our CDO wrote this as a proof of concept.
