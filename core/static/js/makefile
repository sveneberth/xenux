TOOL     = uglifyjs
OPTIONS  = --compress --mangle --

FILENAME = xenux
EXT      = js

default:
	$(TOOL) $(OPTIONS) $(FILENAME).$(EXT) > $(FILENAME).min.$(EXT)

init:
	npm install uglify-js -g #uglify-js is the newer version
