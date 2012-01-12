#!/bin/bash

cd ../../util/buildscripts

echo $PWD

./build.sh profile=../../bba/builder/bba.profile.js

cd ../../release

echo $PWD
echo "Cleaning up..."
echo "remove old bba folder"
rm -fr bba

echo "Copy new files accross..."

mkdir -v --parents bba/dojox/grid/resources/images/
mkdir -v --parents bba/dojox/widget/Wizard/

cp -vr bba-temp/dojox/widget/Wizard/Wizard.css bba/dojox/widget/Wizard/Wizard.css

cp -vr bba-temp/dojox/grid/resources/claroGrid.css bba/dojox/grid/resources/claroGrid.css
cp -vr bba-temp/dojox/grid/resources/Grid.css bba/dojox/grid/resources/Grid.css
cp -vr bba-temp/dojox/grid/resources/images/ bba/dojox/grid/resources

mkdir -v --parents bba/dijit/icons/
mkdir -v --parents bba/dijit/themes/claro/

cp -vr bba-temp/dijit/icons/ bba/dijit
cp -vr bba-temp/dijit/themes/claro/ bba/dijit/themes
cp -vr bba-temp/dijit/themes/dijit.css bba/dijit/themes/dijit.css
cp -vr bba-temp/dijit/themes/dijit_rtl.css bba/dijit/themes/dijit_rtl.css

mkdir -v --parents bba/dojo/cldr/nls/en/
mkdir -v --parents bba/dojo/cldr/nls/en-gb/
mkdir -v --parents bba/dojo/resources/

cp -vr bba-temp/dojo/cldr/nls/en/ bba/dojo/cldr/nls
cp -vr bba-temp/dojo/cldr/nls/en-gb/ bba/dojo/cldr/nls
cp -vr bba-temp/dojo/resources/ bba/dojo
rm -v bba/dojo/resources/_modules.js
cp -vr bba-temp/dojo/dojo.js bba/dojo/dojo.js

rm -fr bba-temp
