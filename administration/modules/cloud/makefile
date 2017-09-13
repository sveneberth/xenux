TOOL         = lessc

CLOUDLESS    = $(TOOL) cloud.less
EXPLORERLESS = $(TOOL) explorer.less

default:
	$(CLOUDLESS) cloud.css
	$(CLOUDLESS) cloud.min.css --clean-css
	$(EXPLORERLESS) explorer.css
	$(EXPLORERLESS) explorer.min.css --clean-css
