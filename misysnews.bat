::this file is used for windows cmd line to setup project and export plugins and themes
@echo off


if "%1"=="" (
	echo -s :means setup
	echo -a :means setup and export all
	echo -e plugin :means export plugin
	echo -e theme :means export themes
	echo -e :means export plugin and themes
	echo please try again
) else (

if "%1"=="-s" (
	npm install -g grunt-cli
	npm install -g bower
	npm install archiver
	npm install

	bower install
	grunt buildwp
	ng build
)
if "%1"=="-a" (
	bower install
	grunt buildwp
	ng build
	node misysexport.js -a
)

if "%1"=="-e" (
	if "%2"=="" (
		ng build
		node misysexport.js -a
	) else (
		if "%2"=="plugin" (
			ng build
			node misysexport.js -p
		)
		if "%2"=="theme" (
			ng build
			node misysexport.js -t
		)
	)
)
)
