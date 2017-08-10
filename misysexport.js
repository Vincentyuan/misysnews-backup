var arglength = process.argv.length;
var operateArray = ["-p","-a","-t"];
if (arglength == 3 && operateArray.indexOf(process.argv[2]) > -1) {
  if (process.argv[2] == '-p' || process.argv[2] == '-a') {
    zipDir("plugins/misysnews/", "misysnews.zip");
  }
  if(process.argv[2] == '-t' || process.argv[2] == '-a') {
    zipDir("themes/misysnews/", "misysnewstheme.zip");
  }
} else {
  console.log("-a means all -p means plugin -t means themes");
}



function zipDir(dir, destName) {

  var fs = require('fs');
  var archiver = require('archiver');

  var output = fs.createWriteStream("zip/"+destName);
  var archive = archiver('zip');

  output.on('close', function() {
    console.log(archive.pointer() + ' total bytes');
    console.log('"' + dir + '" has been exported to folder zip/ ');
  });

  archive.on('error', function(err) {
    throw err;
  });

  archive.pipe(output);

  archive.directory(dir, "");
  archive.finalize();
}
