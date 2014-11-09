identify -format "%%w" test.jpg
for /F %%i in ('identify -format "%w" test.jpg') do set T=%%i
echo %T%
echo hh