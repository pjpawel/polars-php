dnl config.m4 for the polars PHP extension (Rust via ext-php-rs)
dnl
dnl PIE (PHP Installer for Extensions) and phpize in general build with:
dnl   phpize && ./configure && make && make install
dnl This extension has no C sources; the actual compilation is delegated to
dnl cargo in Makefile.frag, which copies the resulting cdylib to
dnl modules/polars.so where the build system (and PIE) expects the artifact.

PHP_ARG_ENABLE([polars],
  [whether to enable the polars extension (Rust via ext-php-rs)],
  [AS_HELP_STRING([--enable-polars], [Enable the polars extension, built with cargo])],
  [yes])

if test "$PHP_POLARS" != "no"; then
  AC_PATH_PROG([CARGO], [cargo], [])
  if test -z "$CARGO"; then
    AC_MSG_ERROR([cargo not found in PATH. Install the Rust toolchain (https://rustup.rs) to build the polars extension.])
  fi

  AC_PATH_PROG([RUSTC], [rustc], [])
  if test -z "$RUSTC"; then
    AC_MSG_ERROR([rustc not found in PATH. Install the Rust toolchain (https://rustup.rs) to build the polars extension.])
  fi

  dnl When invoked through rustup, prefer the real toolchain binaries:
  dnl Makefile.frag passes RUSTC to cargo so it never spawns the rustup
  dnl rustc shims, which can race each other on channel syncs when cargo
  dnl builds with -j.
  AC_PATH_PROG([RUSTUP], [rustup], [])
  if test -n "$RUSTUP"; then
    CARGO_REAL=`$RUSTUP which cargo 2>/dev/null`
    if test -n "$CARGO_REAL"; then
      CARGO="$CARGO_REAL"
    fi
    RUSTC_REAL=`$RUSTUP which rustc 2>/dev/null`
    if test -n "$RUSTC_REAL"; then
      RUSTC="$RUSTC_REAL"
    fi
  fi

  PHP_SUBST([CARGO])
  PHP_SUBST([RUSTC])

  PHP_ADD_MAKEFILE_FRAGMENT([$srcdir/Makefile.frag])
fi
