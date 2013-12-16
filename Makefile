all:
	@echo "Clear build directory."
	rm -rf build/
	@echo "Create directorys."
	mkdir -p build
	mkdir -p build/templates_c
	mkdir -p build/upload
	@echo "Update packages and autoloader."
	composer update --no-dev
	@echo "Copy files."
	cp -r app build/
	cp -r vendor build/
	cp -r assets build/
	cp -r lib build/
	cp index.php build/index.php
	cp payment.php build/payment.php
	cp sample.config.php build/config.php
	@echo "Remove unneccessary files."
	rm -rf `find build/ -type d -name .svn`
	rm -rf `find build/ -type d -name documentation`
	rm -rf `find build/ -type d -name test`
	rm -rf `find build/ -type d -name tests`
	rm -rf `find build/ -type d -name demo`
	rm -rf `find build/ -type d -name tutorial`
	rm -rf `find build/ -type d -name .git`
	rm -rf `find build/ -type d -name examples`
	