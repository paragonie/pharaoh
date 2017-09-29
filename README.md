# Pharaoh - PHAR diff utility

Display the differences between all of the files in two PHP Archives.

Copyright (c) 2015 [Paragon Initiative Enterprises](https://paragonie.com)

Check out our other [open source projects](https://paragonie.com/projects) too.

## Set up

1. Edit your php.ini file and set `phar.readonly` to `0` for PHP's CLI.
2. Optionally, symlink `/usr/bin/pharaoh` to the `pharaoh` file in the current
   directory.

## Example

```sh
pharaoh composer-from-source.phar composer-from-web.phar
```
