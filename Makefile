# Makefile
#
# This file contains the commands most used in DEV, plus the ones used in CI and PRD environments.
#
# The commands are to be organized semantically and alphabetically, so that similar commands are nex to each other
# and we can compare them and update them easily.
#
# For example in a format like `subject-action-environment`, ie 'dep-install-prd'.
#

# Mute all `make` specific output. Comment this out to get some debug information.
.SILENT:

# .DEFAULT: If the command does not exist in this makefile
# default:  If no command was specified
.DEFAULT default:
	if [ -f ./Makefile.custom ]; then \
	    $(MAKE) -f Makefile.custom "$@"; \
	else \
	    if [ "$@" != "" ]; then echo "Command '$@' not found."; fi; \
	    $(MAKE) help; \
	    if [ "$@" != "" ]; then exit 2; fi; \
	fi

help:
	@echo "Usage:"
	@echo "     make [command]"
	@echo
	@echo "Available commands:"
	@grep '^[^#[:space:]].*:' Makefile | grep -v '^default' | grep -v '^\.' | grep -v '=' | grep -v '^_' | sed 's/://' | xargs -n 1 echo ' -'

########################################################################################################################

COVERAGE_REPORT_PATH="var/coverage.clover.xml"

cs-fix:
	php vendor/bin/php-cs-fixer fix --verbose

.dep_analyzer-install:
	if [ ! -f bin/deptrac ]; then \
      echo " // deptrac not found in bin/deptrac. Downloading it ..."; \
      curl -LS http://get.sensiolabs.de/deptrac.phar -o bin/deptrac; \
      chmod +x bin/deptrac; \
      echo; \
      echo "If you want to create nice dependency graphs, you need to install graphviz:"; \
      echo "    - For osx/brew: $ brew install graphviz"; \
      echo "    - For ubuntu: $ sudo apt-get install graphviz"; \
      echo "    - For windows: https://graphviz.gitlab.io/_pages/Download/Download_windows.html"; \
  fi;

dep-install:
	composer install

dep-install-prd:
	composer install --no-dev --optimize-autoloader --no-ansi --no-interaction --no-progress --no-scripts

dep-update:
	composer update

test:
	- $(MAKE) cs-fix
	bin/phpunit
	$(MAKE) test-dep

test-dep:
	$(MAKE) test-dep-components
	$(MAKE) test-dep-layers
	$(MAKE) test-dep-class

test-dep-graph:
	$(MAKE) test-dep-components-graph
	$(MAKE) test-dep-layers-graph
	$(MAKE) test-dep-class-graph

test-dep-components:
	$(MAKE) .dep_analyzer-install
	bin/deptrac analyze depfile.components.yaml --formatter-graphviz=0

test-dep-components-graph:
	$(MAKE) .dep_analyzer-install
	bin/deptrac analyze depfile.components.yaml --formatter-graphviz-dump-image=var/deptrac_components.png --formatter-graphviz-dump-dot=var/deptrac_components.dot

test-dep-layers:
	$(MAKE) .dep_analyzer-install
	bin/deptrac analyze depfile.layers.yaml --formatter-graphviz=0

test-dep-layers-graph:
	$(MAKE) .dep_analyzer-install
	bin/deptrac analyze depfile.layers.yaml --formatter-graphviz-dump-image=var/deptrac_layers.png --formatter-graphviz-dump-dot=var/deptrac_layers.dot

test-dep-class:
	$(MAKE) .dep_analyzer-install
	bin/deptrac analyze depfile.classes.yaml --formatter-graphviz=0

test-dep-class-graph:
	$(MAKE) .dep_analyzer-install
	bin/deptrac analyze depfile.classes.yaml --formatter-graphviz-dump-image=var/deptrac_class.png --formatter-graphviz-dump-dot=var/deptrac_class.dot

# We use phpdbg because is part of the core and so that we don't need to install xdebug just to get the coverage.
# Furthermore, phpdbg gives us more info in certain conditions, ie if the memory_limit has been reached.
test_coverage:
	phpdbg -qrr bin/phpunit --coverage-text --coverage-clover=${COVERAGE_REPORT_PATH}

