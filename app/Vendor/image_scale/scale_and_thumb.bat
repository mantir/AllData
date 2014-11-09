::@ECHO OFF
::Usage - [Path] [Filename] [maxWidth/maxHeight] [offset]
::pfad
Set pfad=%1
::name
Set name=%2
Set f=%pfad%\%name%
ECHO %f%
ECHO Pfad: %pfad%
ECHO Datei: %name%
::maxwidth/height 1024
Set wh=%3
::offset dependant on size s1 (240)
Set offset=%4
::Set Sizes
Set s1=240
Set s2=80
Set s3=48
::Set filepath to the biggest cropped images where all other image sizes made from
Set f2=%pfad%\%s1%\%name%

for /F %%i in ('identify -format "%%w" %f%') do set width=%%i
for /F %%i in ('identify -format "%%h" %f%') do set height=%%i

ECHO Breite: %width%
ECHO Hoehe: %height%

if  %width% GTR %height%  (
	if  %width% GTR %wh%  (
		convert_image %f% -resize %wh% %f%
	)
	convert_image %f% -resize x%s1% -crop %s1%x%s1%+%offset%+0 %f2%
	
) else (
	if  %height% GTR %wh%  (
		convert_image %f% -resize x%wh% %f%
	)
	convert_image %f% -resize %s1%x -crop %s1%x%s1%+0+%offset% %f2%
	
)

convert_image %f2% -resize %s2% %pfad%\%s2%\%name%
convert_image %f2% -resize %s3% %pfad%\%s3%\%name%
echo %width%x%height%