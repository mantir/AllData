#!/bin/bash
#Usage - [Path] [Filename] [maxWidth/maxHeight] [offset]
p="$1"
#name
n="$2"
f=$p/$n
#maxwidth/height 1024
wh="$3"
#offset dependant on size s1 (240)
offset="$4"
#Set Sizes
s1=240
s2=80
s3=48
#Set filepath to the biggest cropped images where all other image sizes made from
f2=${p}/${s1}/${n}

#`which identify > test2.txt`
#width=`identify -format '%w' $f`
#height=`identify -format '%h' $f`

width=800
height=600

if [ $width -gt $height ]; then
  if [ $width -gt $wh  ]; then 
    convert $f -resize $wh $f
  fi
  convert $f -resize x$s1 -crop ${s1}x${s1}+${offset}+0 $f2
else
	if [ $height -gt $wh ]; then
		convert $f -resize x$wh $f
	fi
	convert $f -resize $s1x -crop ${s1}x${s1}+0+${offset} $f2
fi

convert $f2 -resize $s2 ${p}\${s2}\${n}
convert $f2 -resize $s3 ${p}\${s3}\${n}
print ${width}x${height}