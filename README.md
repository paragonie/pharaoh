# Pharaoh - PHAR diff utility

Display the differences between all of the files in two PHP Archives

## Set up

```sh
ln -s ./pharaoh /usr/bin/pharaoh
```

## Example

```sh
pharaoh composer-downloaded.phar composer-from-soruce.phar
```

## Why?

As part of our [ASGard](https://getasgard.com) project, we must verify the
reproducibility of software deliverables. This means building a .phar from
source and comparing it to the one provided by the vendor.
