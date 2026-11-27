# Makefile fragment bridging the phpize build (phpize && ./configure && make)
# to the actual cargo build of the Rust extension.
#
# The Cargo project lives in the parent of this build path
# (php-ext.build-path = "pie" in composer.json):
#   $(top_srcdir)    -> <package>/pie
#   $(top_srcdir)/.. -> <package> (Cargo.toml, Cargo.lock, src/, .cargo/)

POLARS_RUST_DIR    = $(top_srcdir)/..
POLARS_PROFILE     = release
POLARS_DYLIB_SO    = $(POLARS_RUST_DIR)/target/$(POLARS_PROFILE)/libpolars_php.so
POLARS_DYLIB_DYLIB = $(POLARS_RUST_DIR)/target/$(POLARS_PROFILE)/libpolars_php.dylib
POLARS_MODULE      = $(top_builddir)/modules/polars.so

CARGO ?= cargo

# Without C sources the default `all` target of the phpize Makefile is a
# no-op, so make it depend on the cargo build below.
all: build-modules

build-modules:
	cd "$(POLARS_RUST_DIR)" && $(CARGO) build --$(POLARS_PROFILE)
	@mkdir -p $(top_builddir)/modules
	@if [ -f "$(POLARS_DYLIB_SO)" ]; then \
		cp -f "$(POLARS_DYLIB_SO)" "$(POLARS_MODULE)"; \
	elif [ -f "$(POLARS_DYLIB_DYLIB)" ]; then \
		cp -f "$(POLARS_DYLIB_DYLIB)" "$(POLARS_MODULE)"; \
	else \
		echo "ERROR: cargo build did not produce $(POLARS_DYLIB_SO) or $(POLARS_DYLIB_DYLIB)"; \
		exit 1; \
	fi
	@echo "Built $(POLARS_MODULE)"

# `make install` (run by PIE) uses the stock install-modules target, which
# installs modules/* into $(INSTALL_ROOT)$(EXTENSION_DIR) as polars.so.

.PHONY: build-modules
