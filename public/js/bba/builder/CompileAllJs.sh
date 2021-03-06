#!/bin/bash

cd ../../release

echo $PWD
echo "remove old bba folder"

rm -fr bba

echo $PWD

../util/buildscripts/build.sh profile=../bba/builder/bba.profile.js

#cd ../../release

echo $PWD
echo "Cleaning up..."

echo "Copy new files accross..."

#mkdir -v --parents bba/dojox/form/resources/images/
mkdir -v --parents bba/dojox/grid/resources/images/
mkdir -v --parents bba/dojox/widget/Wizard/
mkdir -v --parents bba/dojox/widget/Standby/images/
mkdir -v --parents bba/dojox/widget/nls/

cp -vr bba-temp/dojox/widget/Standby/images/ bba/dojox/widget/Standby
cp -vr bba-temp/dojox/widget/Wizard/Wizard.css bba/dojox/widget/Wizard/Wizard.css
cp -vr bba-temp/dojox/widget/nls/Wizard.js bba/dojox/widget/nls

cp -vr bba-temp/dojox/grid/resources/claroGrid.css bba/dojox/grid/resources/claroGrid.css
cp -vr bba-temp/dojox/grid/resources/Grid.css bba/dojox/grid/resources/Grid.css
cp -vr bba-temp/dojox/grid/resources/images/ bba/dojox/grid/resources

#cp -vr bba-temp/dojox/form/resources/UploaderFileList.css bba/dojox/form/resources/UploaderFileList.css

mkdir -v --parents bba/dijit/icons/
mkdir -v --parents bba/dijit/themes/claro/
mkdir -v --parents bba/dijit/nls/
mkdir -v --parents bba/dijit/form/nls/

cp -vr bba-temp/dijit/form/nls/validate.js bba/dijit/form/nls/validate.js
cp -vr bba-temp/dijit/nls/loading.js bba/dijit/nls/loading.js
cp -vr bba-temp/dijit/nls/common.js bba/dijit/nls/common.js
cp -vr bba-temp/dijit/icons/ bba/dijit
cp -vr bba-temp/dijit/themes/claro/ bba/dijit/themes
cp -vr bba-temp/dijit/themes/dijit.css bba/dijit/themes/dijit.css
cp -vr bba-temp/dijit/themes/dijit_rtl.css bba/dijit/themes/dijit_rtl.css

mkdir -v --parents bba/dojo/cldr/nls/en/
mkdir -v --parents bba/dojo/cldr/nls/en-gb/
mkdir -v --parents bba/dojo/resources/
mkdir -v --parents bba/dojo/nls/

cp -v bba-temp/dojo/nls/dojo_en-gb.js bba/dojo/nls/dojo_en-gb.js
cp -vr bba-temp/dojo/cldr/nls/en/ bba/dojo/cldr/nls
cp -vr bba-temp/dojo/cldr/nls/en-gb/ bba/dojo/cldr/nls
cp -vr bba-temp/dojo/cldr/nls/*.js bba/dojo/cldr/nls
cp -vr bba-temp/dojo/cldr/*.js bba/dojo/cldr
cp -vr bba-temp/dojo/resources/ bba/dojo
cp -vr bba-temp/dojo/dojo.js bba/dojo/dojo.js

rm -v bba/dojo/cldr/*.uncompressed.js
rm -v bba/dojo/cldr/nls/*.uncompressed.js
rm -v bba/dojo/cldr/nls/en/*.uncompressed.js
rm -v bba/dojo/cldr/nls/en-gb/*.uncompressed.js

cp -v bba-temp/build-report.txt bba/build-report.txt

rm -fr bba-temp
