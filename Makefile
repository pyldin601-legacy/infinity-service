REVISION_FILE := ".revision"
REVISION := $(shell cat ${REVISION_FILE})
NAME := "pldin601/infinity-service"

build:
	@echo $(REVISION) | awk '{ print $$0 + 1 }' > ${REVISION_FILE}
	@docker build -t ${NAME} --tag ${REVISION} .

run:
	@docker run -it --rm -p 5050:80 $(NAME)

push:
	@docker push $(NAME)

attach:
	@docker run -it --rm $(NAME) sh

dev:
	@docker run -v $(CURDIR):/var/app --rm -p 5050:80 $(NAME)
