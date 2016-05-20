[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cvweiss/project-base/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cvweiss/project-base/?branch=master)

### project-base

A framework that I'm creating to learn good practices and how to do them badly.

##### Installation
Clone this to your preferred directory.

```
git clone https://github.com/cvweiss/project-base.git .
```

Install composer. Follow the directions here: https://getcomposer.org/download/

Run composer with the following command:
```
./composer.phar -o update
```
`update` will update, as well as install, all dependencies. The `-o` flag instructs composer to optimize the mappings into a class map, which is very useful if you want to use the Cron functionality.

##### Cron

@TODO

#### License
MIT
