# Symfony boilerplate

A Symfony boilerplate with cumulative and isolated git commits, so it's easy to change or remove what we don't want to use.

## Container

All commands in the Makefile are setup to run inside the container, except for the commands used by the CI as it has 
its own process to run them inside a container.

The container is NOT production ready. 

You should tweak it to what you need in production, and have another container (based on the production one) 
for dev and ci environments. 

Furthermore, while the dev container should work with shared volumes for the code to be shared between guest and host, 
the production container should contain the actual code, and only the code needed for production, so no tests and no 
dev and test dependencies.

One of the things you should change, for example, is removing xdebug from your production container.
(The less packages in the container, the less chance of bugs that allow a hacker to break in)

The container used is created using the files:

```bash
  build
    dockerfile
    php.ini
    xdebug.ini
```

### Build a new container

If you need changes in the container, you can edit the dockerfile and run:

```bash
  make box-build
```

### Upload the new container to docker hub

When you have a new container, you can push it to docker hub by running:

```bash
  make box-push
```

### php.ini

The only change to the default php.ini is the `memory_limit`, which is set to `1024M` 
so that xdebug runs with less chances of reaching the memory limit.

Feel free to change this to whatever you find reasonable for your production environment.

### Xdebug

By default, xdebug is disabled. 

If you want to enable it, you just need to uncomment the first line in the xdebug.ini file.

You might also need to change the
  - `xdebug.remote_host` to whatever IP your host machine has;
  - `xdebug.idekey` to whatever is the key used by your xdebug client.
