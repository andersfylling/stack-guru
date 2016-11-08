.PHONY: test
test:
	phpunit --bootstrap test-environment.php tests/
