NAME := "pldin601/infinity-service"

build:
	docker build -t $(NAME) .

run:
	docker run -it --rm -p 5050:80 $(NAME)

push:
	docker push $(NAME)

attach:
	docker run -it --rm $(NAME) sh

dev:
	docker run -v $(CURDIR):/var/app --rm -p 5050:80 $(NAME)
