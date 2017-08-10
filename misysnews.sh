#this file is prepared for the unix system

if [ "$#" -lt 1 ]; then
  echo "-s :means setup"
  echo "-a :means setup and export all"
  echo "-e plugin :means export plugin"
  echo "-e theme :means export themes"
  echo "-e :means export plugin and themes"
  echo "please try again"
fi


if [ "$1" = "-s" ]; then
	npm install -g grunt-cli
	npm install -g bower
	npm install archiver
	npm install

	bower install
	grunt buildwp
	ng build
else
  if [ "$1" = "-a" ]; then
  	# npm install -g grunt-cli
  	# npm install -g bower
  	# npm install archiver
  	# npm install

  	bower install
  	grunt buildwp
    ng build
  	node misysexport.js -a
  fi

  if [ "$1" = "-e" ]; then
  	if [ "$2" = "" ]; then
      ng build
  		node misysexport.js -a
  	else
  		if [ "$2" = "plugin" ]; then
        ng build
  			node misysexport.js -p
  		elif [ "$2" = "theme" ]; then
        ng build
  			node misysexport.js -t
  		fi
  	fi
  fi
fi
